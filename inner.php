<?


//редирект с www на простой домен
if (strpos($_SERVER['SERVER_NAME'],'www.')!== false)
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://estmesto.net".$_SERVER['REQUEST_URI']);
	exit();
}

//if ( get_magic_quotes_gpc() ) die('magic_quotes_gpc включен');
//if ( get_magic_quotes_runtime() ) die('magic_quotes_runtime включен');
//if ( ini_get('register_globals') == 1) die('register_globals включен');

//экранируем от HTML GET-запросы
if ($_GET)
{
    foreach($_GET as $f => $v)
        if (!is_array($v))
        {
            unset($_GET[$f]);
            $_GET[strip_tags($f)] = strip_tags($v);
        }
}



//обрабатываем sync-запросы без обращений к базе
if (strpos($_SERVER['REQUEST_URI'], '/sync/tripID-') !== false)
{
//	/sync/tripID-44_syncID-443d1c88db8a51ddc5c966912a993dec
	$uri = $_SERVER['REQUEST_URI'];
	if ($uri[0] == '/')
		$uri[0] = ' ';

	$parts = explode('/', $uri);

	$key = mysql_escape_string( strip_tags( trim($parts[0])));
	$action = mysql_escape_string( strip_tags( trim($parts[1])));
	$params = array();
	if (strlen(trim($parts[2])))
	{
		$lines = explode('_', $parts[2]);
		foreach($lines as &$line)
		{
			if (!strlen(trim($line))) continue;
			$buf = explode('-', $line);
			$params[mysql_escape_string( strip_tags( trim($buf[0])))] = mysql_escape_string( strip_tags( trim($buf[1])));
		}
	}

	$syncIDfile = sprintf("%s_cache/trip_%d_clean_syncid_%s", 
						$_SERVER['DOCUMENT_ROOT'], intval($params['tripID']),
						$params['syncID']);
	if (is_file($syncIDfile))
	{
		echo 'nochange';
		die();
	}

}


//запросы к чату
if (strpos($_SERVER['REQUEST_URI'], '/chat/chatsync/TripID-') !== false)
{
	$uri = $_SERVER['REQUEST_URI'];


	if ($uri[0] == '/')
		$uri[0] = ' ';

	$parts = explode('/', $uri);

	$key = mysql_escape_string( strip_tags( trim($parts[0])));
	$action = mysql_escape_string( strip_tags( trim($parts[1])));
	$params = array();
	if (strlen(trim($parts[2])))
	{
		$lines = explode('_', $parts[2]);
		foreach($lines as &$line)
		{
			if (!strlen(trim($line))) continue;
			$buf = explode('-', $line);
			$params[mysql_escape_string( strip_tags( trim($buf[0])))] = mysql_escape_string( strip_tags( trim($buf[1])));
		}
	}

	$syncIDfile = sprintf("%s_cache/chat_%d_syncid_%s", 
						$_SERVER['DOCUMENT_ROOT'], intval($params['TripID']),
						$params['syncID']);

	if (is_file($syncIDfile))
	{
		echo 'nochange';
		die();
	}

}


//функции кеширования
include "inc/var.php";
include "inc/libs/caching.php";
include "inc/libs/dtimer.php";

//всегда вешаем тестовую куку
if (!isset($_COOKIE['emn_cookie_on']))
	setcookie("emn_cookie_on", "1", time() + 3600);

//header('Content-Type: text/html; charset=utf-8');

$dtimer = new dtimer();

//глобальное кеширование
if (check_cache_enable())
{
	$uri = str_replace('/','#',$_SERVER['REQUEST_URI']);
    $html = get_cache($uri,1800);
    if (!is_null($html))
    {   
            echo $html;
            
            //$time_end = getmicrotime();
            //$time = sprintf('%.16f',$time_end - $time_start);
            //echo "<br><center><font color='gray' size='1'>[".substr($time, 0, 6)."]</font></center>";
            die();
    }
}

//псевдослучайная очистка кеша
/* суть - при долгом отсутствии администрирования файлы с кешем накапливаются в большом количестве 
   полезно с определенной периодичностью их грохать	
*/
if (rand(1,5000) == 2500)
{
	clear_cache(0);
}


include "includes.php";
include_once "inc/modules/user.php";
$sql = new Sql();
$sql->connect();
$control = new Control();
$control->Init();
$control->user = user::logon();
$control->Make();
$sql->close();

$dtimer->tick("Done.");
//echo $dtimer->show();

?>