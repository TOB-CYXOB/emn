<?
class user extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
        'user',
);
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
        'user' => 'inner.html',
);
    }

    function __destruct()
    {
    }


	function _setOneTimeKey($params=array())
	{
		global $control;
		global $config;
		global $sql;

		if (trim($params['email']) == '') return NULL;
		
		//у юзера должна быть активна кука
		if (!strlen($control->user->cookieID)) return NULL; 
	
		$params['email'] = sql::escape_string($params['email']);

		$q = sprintf("SELECT id FROM prname_users WHERE `email` LIKE '%s';", sql::escape_string($params['email']));
		$uID = $sql->one_record($q);

		$oKey = md5(uniqid('oKey-', true).$config['md5']);

		if ($uID) //такой есть - устанавливаем ключ
		{
			$q = sprintf("UPDATE prname_users SET `onetimeKey` = '%s' WHERE id = %d;", $oKey, $uID);
			$sql->query($q);

			return $oKey;	
		}
		else
		{
			//создаем E-mail пользователя
			$params['onetimeKey'] = $oKey;
			$params['admin'] = 0;

			$params['webLink'] = 'mailto:'.$params['email'];
			$params['webUID'] = $params['email'];
			$params['webProvider'] = 'email';
			$params['userKey'] = md5($params['webLink'] . $params['webProvider'] . $params['webUID'] . $config['md5']);
			user::_createUser($params);
			return $oKey;
		}
	}

	function logon()
	{
		global $control;
		global $config;
		global $sql;

		//если в рамках сесси уже проводилась авторизация
		if (!is_null($control->user)) return $control->user;

		$cookieID = trim(sql::escape_string($_COOKIE['cookieID']));

		//проверка на Loginza-авторизацию
		if (!empty($_POST['token'])) 
		{
			require_once 'libs/LoginzaAPI.class.php';
			require_once 'libs/LoginzaUserProfile.class.php';
			// объект работы с Loginza API
			$LoginzaAPI = new LoginzaAPI();


			// получаем профиль авторизованного пользователя
			$UserProfile = $LoginzaAPI->getAuthInfo($_POST['token']);
	
			// проверка на ошибки
			if (!empty($UserProfile->error_type)) 
			{
				// есть ошибки, выводим их
				// в рабочем примере данные ошибки не следует выводить пользователю, так как они несут информационный характер только для разработчика
				//echo $UserProfile->error_type.": ".$UserProfile->error_message;
			} 
			elseif (empty($UserProfile)) 
			{
				// прочие ошибки
				//echo 'Temporary error.';
			} 
			else 
			{
				// ошибок нет 

				//ищем этого пользователя, может он уже есть в базе
				$userKey = md5($UserProfile->identity . $UserProfile->provider . $UserProfile->uid . $config['md5']);

				$q = sprintf("SELECT * FROM prname_users WHERE userKey ='%s';", $userKey);
				$res = $sql->query($q);
				$obj = $sql->fetch_object($res);
				if ($obj) //да, пользователь найден
				{
					$cookieID = $obj->cookieID;

					setcookie("cookieID", $cookieID, time() + (3600*24*356*10),'/');
					$_COOKIE['cookieID'] = $cookieID;

					clear_cache('logon-'.$cookieID);

					$set['lastlogin'] = date('Y.m.d H:i:s');
					if (strtotime($obj->lastchange) < time() - 86400)
					{
						//если не заходил более суток - обновляем инфу по аккаунту
						//TODO мог измениться никнейм и аватар. Периодически нужно синхронизировать.

						$LoginzaProfile = new LoginzaUserProfile($UserProfile);
						if (!strlen($obj->email))
                    		$set['email'] = $LoginzaProfile->getEmail();

						$set['nickname'] = $LoginzaProfile->genDisplayName();
						$set['avatar'] = $UserProfile->photo;
						$set['webLink'] = $UserProfile->identity;
						$set['webUID'] = $UserProfile->uid;
						$set['webProvider'] = $UserProfile->provider;
						$set['lastchange'] = date('Y.m.d H:i:s');
					}

					$qset = array();
					foreach($set as $f=> $v)
						$qset[] = sprintf("`%s`='%s'", $f, mysql_real_escape_string(htmlspecialchars($v)));

					$q = sprintf("UPDATE prname_users SET %s WHERE cookieID='%s';", implode(',',$qset), $cookieID);
					$sql->query($q);

				}
				else //такого пользователя нет,создаем
				{
					$LoginzaProfile = new LoginzaUserProfile($UserProfile);

					$uparams['userKey'] = $userKey;
					$uparams['email'] = $LoginzaProfile->getEmail();
					$uparams['nickname'] = $LoginzaProfile->genDisplayName();
					$uparams['avatar'] = $UserProfile->photo;
					$uparams['admin'] = 0;

					$uparams['webLink'] = $UserProfile->identity;
					$uparams['webUID'] = $UserProfile->uid;
					$uparams['webProvider'] = $UserProfile->provider;

					$cookieID = user::_createUser($uparams);

					setcookie("cookieID", $cookieID, time() + (3600*24*356*10),'/');
					$_COOKIE['cookieID'] = $cookieID;

					clear_cache(0,'logon-'.$cookieID);
				}

				//переадресуем, дабы избежать POST-хвоста
				header('Location: '.$_SERVER['REQUEST_URI']);
				die();				
			}
		}

	
		$cn = sprintf('logon-%s', $cookieID);
		$susr = get_cache($cn, 300);
		if (!is_null($susr))
		{
			$usr = unserialize($susr);
			$control->user = $usr;
			return $usr;
		}

		if ($cookieID == '' && $_COOKIE['emn_cookie_on'] == 1)
		{
			//юзер не авторизован, куки активны
			$cookieID = user::_createUser();
			//сразу вешаем вечные куки
			setcookie("cookieID", $cookieID, time() + (3600*24*356*10),'/');
			$_COOKIE['cookieID'] = $cookieID;
		}

		if ($cookieID == '' && $_COOKIE['emn_cookie_on'] != 1)
		{
			$usr->nickname = $config['LANG']["USER_DEFAULT_NICKNAME"];
			return $usr;
		}


		$q = sprintf("SELECT * FROM prname_users WHERE cookieID='%s' LIMIT 1;", $cookieID);
		$res = $sql->query($q);

		$usr = $sql->fetch_object($res);
		 
		$susr = serialize($usr);
		set_cache($cn, $susr);

		$control->user = $usr;
		return $usr;
	}


	function logout()
	{
		global $control;
		global $config;
		global $sql;

		if (!function_exists ('clear_cache'))
		{
			require_once($config['DOCUMENT_ROOT'].'inc/libs/caching.php');
		}

		clear_cache('logon-'.$_COOKIE['cookieID']);
		
		$q = sprintf("UPDATE prname_users SET lastlogin = NOW() WHERE cookieID='%s';", $_COOKIE['cookieID']);
		$sql->query($q);

		setcookie("cookieID", '', time() - 86400,'/');
		unset($_COOKIE['cookieID']);
		unset($control->user);
		return;
	}


	function _createUser($params=array())
	{
		global $control;
		global $config;
		global $sql;

		$params['cookieID'] = uniqid('emn-', true);
		$params['lastlogin'] = date('Y-m-d H:i:s');
		if (!isset($params['nickname']))
			$params['nickname'] = $config['LANG']['USER_DEFAULT_NICKNAME'];
		
		foreach($params as $f => $v)
			$arr[] = sprintf("%s='%s'",$f, sql::escape_string($v));

		$q = sprintf("INSERT INTO prname_users SET %s;", implode(',', $arr));
		$sql->query($q);

		return	$params['cookieID'];			
	}


	function regUserByEmail()
	{
		global $control;
		global $config;
		global $sql;
		
		if (strlen($control->params['email']) && strlen($control->params['nickname']))
		{
			$oKey = $this->_setOneTimeKey(array(
					'email' => $control->params['email'],
					'nickname' => $control->params['nickname']
			));

			if (strlen($oKey))
			{
				$page->oKey = $oKey;
				$page->nickname = $control->params['nickname'];
				$msubj = $config['LANG']['USER_REGEMAIL_SUBJECT'];
				$mbody = $this->phptpl($page, $this->_tplDir().'RegByEmail_mail_tpl.php');
				All::send_mail($control->params['email'], $msubj, $mbody);
				echo sprintf('<div class="alert alert-info">%s</div>', $config['LANG']['USER_REGEMAIL_DONE']);
				die();
			}
			else
			{
				echo sprintf('<div class="alert alert-info">%s</div>', $config['LANG']['UNKNOWN_ERROR']);
				die();
			}
		}

		return $this->phptpl($page, $this->_tplDir().'RegByEmail_page_tpl.php');
	}

	function setOnceKey()
	{
		global $control;
		global $config;
		global $sql;

		if (strlen($control->params['email'])) //установка одноразового ключа
		{
		}
		else
		if (strlen(trim($control->action))) //вход по одноразовому ключу
		{
			$oKey = sql::escape_string($control->action);
			$q = sprintf("SELECT cookieID FROM prname_users WHERE `onetimeKey` = '%s';", $oKey);
			$cookieID = $sql->one_record($q);
			if (strlen($cookieID)) //ключ найден, выполняем вход
			{

				$q = sprintf("UPDATE prname_users SET `onetimeKey` = '' WHERE `cookieID` = '%s';", $cookieID);
				$sql->query($q);

				setcookie("cookieID", $cookieID, time() + (3600*24*356*10),'/');
				$_COOKIE['cookieID'] = $cookieID;

				clear_cache(0,'logon-'.$cookieID);

				//переадресуем, дабы избежать POST-хвоста
				header('Location: /');
				die();				
			}

			//сюда попали - значит повторное использование ключа
			return $this->phptpl($page, $this->_tplDir().'onceKey_page_tpl.php');
		}

	}


    //базовый метод сайт-модуля
    function content($arParams=array())
    {
		global $control;
		global $config;
		global $sql;

		if ($control->key == 'regme')
		{
			return $this->regUserByEmail();			
		}

		if ($control->key == 'once')
		{
			return $this->setOnceKey();			
		}

    }   
	
}

?>