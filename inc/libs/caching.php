<?

/*
    ¬ чем фишка использовани€ технологии memcache:
    помимо хранени€ на диске кеш сохран€етс€ в оперативной пам€ти
    и при достаточно интенсивных запросах это даст большую экономию обращений к диску.
    “.е. теперь цепочка кешировани€ выгл€дит так:
    запрос - кеш из пам€ти - кеш с диска - отработка "вживую"
*/


	//функции дополнительного кешировани€
	global $config;
	global $CDIR;

	if (!isset($config["DOCUMENT_ROOT"]))
		$config["DOCUMENT_ROOT"] = $_SERVER['DOCUMENT_ROOT'];

	//каталог кешировани€
	$CDIR = $config["DOCUMENT_ROOT"].'_cache/';


	//исключени€ дл€ кэша (вс€кие €ндекс-директы и т.п.)
	$CACHE_ARR_EXCLUDE = array (
	'PHPSESSID',
	'__utma',
	'__utmz',
	'__utmc',
	'__utmb'	
	);

    
    //необходимые услови€ дл€ использовани€ глобального кеша
    function check_cache_enable()
    {
        global $config;
        
		if ($config["cache"] != 2) return false;
        if (count($_POST) > 0) return false;
        
        return true;  
    }

	function get_cache($filename, $timediff=1800, $ignore_config=0)
	{
		global $config;
		global $CDIR;

		if ($config['cache'] == 0 && $ignore_config == 0) return NULL;

        /* сперва попытка реализации через MEMCACHE*/
        //логика така€ - часто используемые данные будут хранитьс€ в memcache
        //c фиксированным временем жизни в 120 секунд.
        if ($config['cache_memcache'] == 1 && extension_loaded('memcache'))
        {
            global $memcache_obj;
            if (!$memcache_obj)
            {
                $memcache_obj = new Memcache;
                $memcache_obj->connect('localhost', 11211);
            }
            $content = $memcache_obj->get($filename);             
            if ($content !== FALSE) return $content;
        }


		$now = time();
		$ftime = @filemtime ($CDIR.$filename);
		if (!$ftime) return NULL;
		if ($now - $ftime > $timediff) return NULL;
		$file = fopen($CDIR.$filename, "r");
		$content = @fread($file, filesize($CDIR.$filename));
		fclose($file);

        //если memcache используетс€ - запихнуть в него результат
        if ($config['cache_memcache'] == 1 && extension_loaded('memcache'))
        {
            $memcache_obj->set($filename, $content, 0, 120);
        }


		return $content;
	}

	function set_cache($filename, $content, $ignore_config=0)
	{
		global $config;
		global $CDIR;

		if ($config['cache'] == 0 && $ignore_config == 0) return;
		if (!strlen($filename)) return;
		
		$out = fopen($CDIR.$filename, "w");
		fwrite($out, $content);
		fclose($out);

        //если memcache используетс€ - запихнуть в него результат
        if ($config['cache_memcache'] == 1 && extension_loaded('memcache'))
        {
            global $memcache_obj;
            if (!$memcache_obj)
            {
                $memcache_obj = new Memcache;
                $memcache_obj->connect('localhost', 11211);
            }

            $memcache_obj->set($filename, $content, 0, 120);
        }
	}


	function my_rm($dir, $prefix='')
	{
	   if ($handle = opendir($dir)) 
       { 
            while (false !== ($file = readdir($handle))) 
            { 
                if ($file != "." && $file != "..") 
                { 
					if ($prefix != '' && strpos($file,$prefix) === false) continue; 

                    @unlink($dir.$file); 
                } 
            } 
        closedir($handle);
       } 
    }
    
    function clear_cache($cachetime=600, $prefix='')
	{
	   global $config;
	   global $CDIR;

		//очистка кеша
		$now = time();
		$ftime = @filemtime ($CDIR.".cachetime");
		if (!$ftime || $now - $ftime > $cachetime )
		{				
            //универсальный вариант
            my_rm($CDIR, $prefix);
			$out = fopen($CDIR.".cachetime", "w");
			fwrite($out, date('Y-m-d H:i:s'));
			fclose($out);
		}	

        //если memcache используетс€ - сбрасываем там все
        if ($config['cache_memcache'] == 1 && extension_loaded('memcache'))
        {
            global $memcache_obj;
            if (!$memcache_obj)
            {
                $memcache_obj = new Memcache;
                $memcache_obj->connect('localhost', 11211);
            }

            $memcache_obj->flush();
        }
	}

	function cache_grep_array(&$arr)
	{
		global $CACHE_ARR_EXCLUDE;

		$res = '';
		if (is_array($arr))
		{
			foreach($arr as $f => $v)
			{
				//исключение
				if (in_array($f, $CACHE_ARR_EXCLUDE) && $f != "0") continue;
                if (is_object($v))
    				$res .= sprintf("%s=%s;", get_class($arr[$f]), serialize($arr[$f]));
                else
    				$res .= sprintf("%s=%s;", $f, cache_grep_array($arr[$f]));
			}
			return $res;
		}
		else
			return $arr;
	}

	//сгенерировать ключ по всем данным GET, SESSION и COOKIE
	function cache_key($use_session = false)
	{
        if ($use_session)
    		session_start();
		$raw = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$raw .= cache_grep_array($_GET);		
		$raw .= cache_grep_array($_POST);		

	    if ($use_session)
        {
        	$raw .= cache_grep_array($_SESSION);		
		/*
		if ($_SERVER['REMOTE_ADDR'] == "85.12.198.87")
		var_dump(cache_grep_array($_SESSION));
		*/

		  $raw .= cache_grep_array($_COOKIE);
        }

		return md5($raw);
	}

?>