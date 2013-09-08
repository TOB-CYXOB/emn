<?
class chat extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
        'chat',
		);
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
        'chat' => 'inner.html',
		);
    }

    function __destruct()
    {
    }

    function _cleanText(&$msg)
	{
		//ограничение на длину одного текстового блока - 2кб
		$limit = 2048;
		return trim(substr(stripslashes(strip_tags($msg,'<a><b><p><strong><span><ul><li><s>')),0, $limit));	
	}


	//подготовка текста к выводу
	function _prepareText(&$msg)
	{

		//автолинки
$preg_autolinks = array(
    'pattern' => array(
        "'[\w\+]+://[A-z0-9\.\?\+\-/_=&%#:;]+[\w/=]+'si",
        "'([^/])(www\.[A-z0-9\.\?\+\-/_=&%#:;]+[\w/=]+)'si",
    ),
    'replacement' => array(
        '<a href="$0" target="_blank" rel="nofollow">$0</a>',
        '$1<a href="http://$2" target="_blank" rel="nofollow">$2</a>',
    ));
$search = $preg_autolinks['pattern'];
$replace = $preg_autolinks['replacement'];
$msg = preg_replace($search, $replace, $msg);
#$text=preg_replace($search, $replace, $text);


		return nl2br($msg);
	}


	//добавить сообщение
	function _addMessage($params=array())
	{
		global $control;
		global $config;
		global $sql;

		if (!strlen($params['user']->userKey)) //только авторизованные юзеры могут писать в чат
			return NULL;

		if (!intval($params['TripID'])) //трип нужно указывать
			return NULL;

		//обрабатываем (чистим) текст
		$msg = chat::_cleanText($params['Message']);

		if (!strlen($msg)) //нельзя постить пустые сообщения
			return NULL;

		$q = sprintf("INSERT INTO prname_chat SET `TripID` = %d, `UserID` = %d, `Message` = '%s';",
				intval($params['TripID']), intval($params['user']->id), sql::escape_string($msg));
		$sql->query($q);

		//сбрасываем кеши чатов
		clear_cache(0, 'chat_'.intval($params['TripID']).'_');

		return $sql->insert_id();
	}

	//удаление сообщения
	function _delMessage($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!strlen($params['user']->userKey)) //неавторизованному тут вообще нечего делать
			return;

		if (!intval($params['TripID'])) //трип нужно указывать
			return;

		//владелец трипа - супермодератор
		$q = sprintf("SELECT Creator FROM prname_trip WHERE ID = %d;", intval($params['TripID']));
		$creator = intval($sql->one_record($q));

		$q = sprintf("SELECT UserID FROM prname_chat WHERE id = %d;", intval($params['id']));
		$owner = intval($sql->one_record($q));

		if (intval($params['user']->id) == $creator ||
			intval($params['user']->id) == $owner)
		{
			$q = sprintf("DELETE FROM prname_chat WHERE `id`=%d;",
				intval($params['id']));
			$sql->query($q);
		}

		//сбрасываем кеши чатов
		clear_cache(0, 'chat_'.intval($params['TripID']).'_');
		return ;
	}


	//вытащить порцию сообщений
	function _getMessagePage($params = array())
	{
		global $control;
		global $config;
		global $sql;


		$_cn = sprintf("chat_%s_%s", intval($params['TripID']), md5(serialize($params)));
		$sobj = get_cache($_cn);
		if (!is_null($sobj))
		{
			return unserialize($sobj);
		}		

		if (!strlen($params['limit']))
			$params['limit'] = 20;

		if ($params['limit'] > 100)
			$params['limit'] = 100;

		if (!intval($params['page']))
			$params['page'] = 0;

		if (!intval($params['TripID']))
			return NULL;

		$offset = $params['page'] * $params['limit'];

		$LIMIT = sprintf(" LIMIT %d, %d", $offset, $params['limit']);

		if ($params['limit'] == 0)
			$LIMIT = "";
		
		$q = sprintf("SELECT * FROM prname_chat WHERE `TripID` = %d ORDER BY id DESC %s",
					intval($params['TripID']), $LIMIT);

		$page->messages = $sql->fetch_object_arr($sql->query($q),'id');
		


		$UIDs = array();

		if (is_array($page->messages) && count($page->messages))
		{
			$page->messages = array_reverse($page->messages, true);

			foreach($page->messages as &$msg)
			{
				$UIDs[$msg->UserID] = $msg->UserID;

				$msg->Message = chat::_prepareText($msg->Message);
			}
		}

		//извлекаем инфу о пользователях - постерах
		if (count($UIDs))
		{
			$q = sprintf("SELECT id, nickname FROM prname_users WHERE id IN(%s);", implode(',', $UIDs));
			$page->users = $sql->fetch_object_arr($sql->query($q), 'id');
		}

		//о владельце трипа
		$q = sprintf("SELECT `Creator` FROM prname_trip WHERE ID=%d;", intval($params['TripID']));
		$page->TripCreator = $sql->one_record($q);

		//всего сообщений в трипе
		$q = sprintf("SELECT count(id) FROM prname_chat WHERE `TripID` = %d;", intval($params['TripID']));
		$page->allCount = $sql->one_record($q);
		$page->limit = $params['limit'];

		$page->beforeCount = $params['page'] * $params['limit'];
		$page->afterCount = $page->allCount - ($offset + count($page->messages));

		//ключ синхронизации

		if ($page->limit == 0)
		{
			$page->syncID = md5(serialize($page));
			$_cn_sync = sprintf("chat_%s_syncid_%s_all", intval($params['TripID']), $page->syncID);
			set_cache($_cn_sync, $page->syncID);
		}
		else
		{
			$page->syncID = md5(serialize($page));
			$_cn_sync = sprintf("chat_%s_syncid_%s", intval($params['TripID']), $page->syncID);
			set_cache($_cn_sync, $page->syncID);
		}

		set_cache($_cn, serialize($page));

		return $page;	
			
	}



	function showChat()
	{
		global $control;
		global $config;
		global $sql;

		$TripID = intval($control->params['TripID']);
		$Page = intval($control->params['Page']);
		$limit = 20;

		if ($control->params['all'] == 1)
		{
			$Page = 0;
			$limit = 0;
		}

		$chatPage = chat::_getMessagePage(array(
					"TripID" => $TripID,
					"page" => $Page,
					"limit" => $limit
					));


		if (is_array($chatPage->messages))
		{
			foreach($chatPage->messages as &$msg)
			{
				//форматируем дату
				$msg->MsgTimeFormat = date('d.m.Y H:i', strtotime($msg->MsgTime));

				if (isset($chatPage->users[$msg->UserID]))
					$msg->UserNickname = htmlspecialchars($chatPage->users[$msg->UserID]->nickname);
				else
					$msg->UserNickname = '--unknown--';

				//права на эту запись
				if ($msg->UserID == $control->user->id) //если это моя запись,могу удалять и менять
				{
					$msg->access->change = 1;
					$msg->access->delete = 1;
				}	
				else
				if ($chatPage->TripCreator == $control->user->id)
				{									 //если я создатель трипа - могу удалять
					$msg->access->delete = 1;
				}
			}
		}

		if (strlen($control->user->userKey))
			$chatPage->user = $control->user;

		//выводим результат
		$html = $this->phptpl($chatPage, $this->_tplDir().'chat_page_tpl.php');
		echo $html;

		die();
	}

	//отправить сообщение в чат
	function addMessage()
	{
		global $control;
		global $config;
		global $sql;

		$TripID = intval($control->params['TripID']);
		$Page = intval($control->params['Page']);

		chat::_addMessage(array(
			"TripID" => $TripID,
			"Message" => $control->params['Message'],
			"user"=> $control->user
		));

		$control->params['Page'] = 0;
		chat::showChat();
	}

	//удалить сообщение из чата
	function delMessage()
	{
		global $control;
		global $config;
		global $sql;

		$TripID = intval($control->params['TripID']);
		$id = intval($control->params['id']);

		chat::_delMessage(array(
			"TripID" => $TripID,
			"id" => $id,
			"user"=> $control->user
		));

		$control->params['Page'] = 0;
		chat::showChat();
	}


    //базовый метод сайт-модуля
    function content($arParams=array())
    {
		global $control;
		global $config;
		global $sql;

		if ($control->action == 'get')
		{
			return $this->showChat();			
		}

		if ($control->action == 'chatsync')
		{
			return $this->showChat();			
		}

		if ($control->action == 'say')
		{
			return $this->addMessage();			
		}

		if ($control->action == 'del')
		{
			return $this->delMessage();			
		}

    }   

// Сюда будут заноситься автодополняемые методы

    function workArea($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;

		//блок строится только в основном окне трипа
		if ($control->module != 'trip' || !in_array($control->action , array('','members','route'))) return;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s",get_class($this), 'workArea', $control->user->userKey); 
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

		$page->user = $control->user;

        $html = $this->phptpl($page, $this->_tplDir()."workArea_tpl.php");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;        
    }



// <#AUTOMETHODS#>



}
?>