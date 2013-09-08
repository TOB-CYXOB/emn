<?php


  class Control  {
    var $template; // текущий шаблон
    var $name; // название страницы
    var $module ; // управляющий модуль
	var $wrapper ; // текущий враппер
	var $NESTEDSETS  = true;

	function Control()
	{
		global $sql;
		global $config;
		global $wrappers;

		$this->sql = $sql;



	//структура урла
	//  / 0  / 1   /  2
	//  /key/action/params
	// /trip/create/a-10_b20_c30

	$uri = $_SERVER['REQUEST_URI'];
	if ($uri[0] == '/')
		$uri[0] = ' ';

	$parts = explode('/', $uri);

	$this->key = mysql_escape_string( strip_tags( trim($parts[0])));
	$this->action = mysql_escape_string( strip_tags( trim($parts[1])));
	$this->params = array();
	if (strlen(trim($parts[2])))
	{
		$lines = explode('_', $parts[2]);
		foreach($lines as &$line)
		{
			if (!strlen(trim($line))) continue;
			$buf = explode('-', $line);
			$this->params[mysql_escape_string( strip_tags( trim($buf[0])))] = mysql_escape_string( strip_tags( trim($buf[1])));
		}
	}

	if (isset($_REQUEST['params']) && is_array($_REQUEST['params']))
	{
		foreach($_REQUEST['params'] as $pname => &$pval)
		{
			if (!strlen(trim($pname))) continue;
			if (!is_array($pval) && !is_object($pval))
			//$this->params[mysql_escape_string( strip_tags( trim($pname)))] = mysql_escape_string( strip_tags( trim($pval)));
			$this->params[mysql_escape_string( strip_tags( trim($pname)))] = trim($pval);
		}
	}

	if (isset($config['URLS'][$this->key]))
		$str = $config['URLS'][$this->key];
	else
		$str = $config['URLS']['trip'];

		$this->template = $str['template'];
		$this->name = $str['name'];

	}


    function Init()
    {
		global $sql;
		global $config;
		global $wrappers;

        //формируем массив врапперов, подключая модули
        $this->sitemodules = array();
        $dir = $config['DOCUMENT_ROOT'].'inc/modules/';
        if ($handle = opendir($dir)) 
        { 
            while (false !== ($file = readdir($handle))) 
            { 
                if ($file != "." && $file != ".." && strpos($file,'_wraps.php') === false && strpos($file, '.php') !== false) 
                { 
                    if ($file == "_default_.php") continue;

                    $mname = str_replace('.php','',$file);
                    $this->sitemodules[] = $mname;
                    //грузим для него враппер
                    if (is_file($dir.'wrappers/'.$mname."_wraps.php"))                    
                        include_once($dir.'wrappers/'.$mname."_wraps.php");
                    else 
                    {
                    //враппер-файл не найден, пробуем сормировать его автоматом
                        include_once($dir.$file);
                        
                        unset($tmpobj);
                        $tmpobj = new $mname ();
                        if (method_exists($tmpobj,'_saveWrappers'))
                            $tmpobj->_saveWrappers();

                        include_once($dir.'wrappers/'.$mname."_wraps.php");
                    }
                } 
            } 
        closedir($handle);
       } 


		$this->wrappers = $wrappers;



		$this->wrapper = $this->wrappers[$this->template]['html'];
		$this->module = $this->wrappers[$this->template]['module'];

		if (trim($this->module) == '')
		{
			$this->error(404);
		}

    }

	function tick($text)
	{
		global $dtimer;
		if (!is_object($dtimer)) return;
		$dtimer->tick($text);
	}

	//вывод ошибки
	function error($num=404)		
	{
		global $config;

		$headers[404] = "Status: 404 Not Found"; 
		$headers[403] = "Status: 403 Not Access"; 
		$headers[500] = "Status: 500 Internal Server Error"; 
		header($headers[$num]); 

		$purl = $config['server_url'].$num.'.php';
		include ($num.'.php');
		die();
	}


	function Make()
	{
		global $sql;
		global $config;
		global $wrappers;
		global $POST_VARS;
		global $ar_mon;

        //каталог с модулями
        $mdir = $config['DOCUMENT_ROOT'].'inc/modules/';

        //каталог с шаблонами
        $tdir = $config['DOCUMENT_ROOT'].'templates/';            
		if(is_file($mdir.$this->module.".php"))
		{
			ob_start();
			include_once ($mdir.$this->module.".php");			
			ob_end_clean();

			$this->tick($this->module.' include');
		}
		else
		{
			$this->error(404); 
		}
		
		$this->html = file_get_contents($tdir.$this->wrapper);
		$this->html = $this->html ? $this->html : 'Отсутствует файл '.$tdir.$this->wrapper;

		preg_match_all('/<!--#control::(.*?)#(.*?)-->/Ui', $this->html, $main_modules);
		preg_match_all('/<!--#(.*?)#(.*?)-->/Ui', $this->html, $arr_modules);
		
        unset($mdl);
        unset($return_data);
        
        $mdl = new $this->module();
        //для текущего модуля в любом случае отрабатываем метод content
        if (method_exists($mdl,'content'))
        {
		    $return_data = $mdl->content();            
			$this->tick($this->module.'::content()');
		}

        $this->html = str_replace("<!--#control::content#-->", $return_data, $this->html);        

		if (count($main_modules) > 0)
		{
			foreach ($main_modules[1] as $idx => $method)
			{
                    if ($method == 'content') continue; //content уже отрабатывался, его не трогаем

					//формируем строку параметров
					unset($mparams);
					if (strlen($main_modules[2][$idx]))
					{
						$parr = explode(' ', $main_modules[2][$idx]);
						if (count($parr))
						{
							foreach($parr as $one)
							{
								if (trim($one) == "") continue;
								$parr2 = explode('=', $one);
								$mparams[$parr2[0]] = $parr2[1];
							}
						}
					}

                    if ($method != 'content') 
                    {
                        unset($return_data);
                        $return_data = $mdl->$method($mparams);    
                        $this->html = preg_replace('/<!--#control::'.$method.'#(.*?)-->/Ui', $return_data, $this->html, 1);
						$this->tick($this->module.'::'.$method.'()');
                    }
            }
        }        

        unset($mdl);

		//миски (включаемые области)
		if (count($arr_modules) > 0)
		{
			foreach ($arr_modules[1] as $idx => $one_arr)
			{
				if (strstr($one_arr, 'control::')) continue;
                if (strpos($one_arr,'::') === false) continue;

                    //название состоит из имени модуля и имени метода
                    $name_arr = explode("::", $one_arr);
                    $mname = $name_arr[0];
                    //ограничение на длину названия модуля - 128 символов
                    if (!strlen($mname) || strlen($mname) > 128) continue;
                    $method = $name_arr[1];
				
					if ( !is_file($mdir.$mname.".php") )	
					{
						$tmp_file_body = file_get_contents($mdir."_default_.php");
						$tmp_file_body = str_replace('<!--name//-->', $mname, $tmp_file_body);
						$tmp_file_body = str_replace('<!--year//-->', date('Y'), $tmp_file_body);
    
						if ($fp = @fopen($mdir.$mname.".php", 'w+'))	{
							fputs ($fp, $tmp_file_body);
							fclose($fp);
						}
					}

					//формируем строку параметров
					unset($mparams);
					if (strlen($arr_modules[2][$idx]))
					{
						$parr = explode(' ', $arr_modules[2][$idx]);
						if (count($parr))
						{
							foreach($parr as $one)
							{
								if (trim($one) == "") continue;
								$parr2 = explode('=', $one);
								$mparams[$parr2[0]] = $parr2[1];
							}
						}
					}

                    ob_start();
					include_once ($mdir.$mname.".php");
                    ob_end_clean();

                    unset($misk);
                    unset($miskhtml);
                    $misk = new $mname ();
					$miskhtml = $misk->$method($mparams);//передаем параметры миску
				
				    $this->html = preg_replace('/<!--#'.$one_arr.'#(.*?)-->/Ui', $miskhtml, $this->html, 1);
					$this->tick($mname.'::'.$method.'()');

			}
		}

		//$mail_admin = @mysql_result($sql->query("SELECT admin_email FROM prname_sadmin WHERE admin_id=4"), 0, 0);   
		$this->html = str_replace(array('{base_url}','<!--base_url//-->'), $config['server_url'], $this->html);

		echo $this->html;

		// глобальное кеширование
		if (check_cache_enable())
		{
        	$uri = str_replace('/','#',$_SERVER['REQUEST_URI']);
	        set_cache($uri, $this->html);
		}
	}
	
   
  } 
?>