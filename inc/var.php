<?php

	$admin_ar_m = array(
			'1'=>'январь', '2'=>'февраль', '3'=>'март', '4'=>'апрель', '5'=>'май', '6'=>'июнь', '7'=>'июль', '8'=>'август', '9'=>'сентябрь', '10'=>'октябрь', '11'=>'ноябрь', '12'=>'декабрь'
	);

	$ar_mon = array('1' => 'января', '2'=>'февраля', '3'=>'марта', '4'=>'апреля', '5'=>'мая', '6'=>'июня', '7'=>'июля', '8'=>'августа', '9'=>'сентября', '10'=>'октября', '11'=>'ноября', '12'=>'декабря');
	$ar_mon_count = array('1' => 31, '2' => 28, '3' => 31, '4' => 30,'5' => 31, '6' => 30, '7' => 31, '8' => 31, '9' => 30, '10' => 31, '11' => 30, '12' => 31);

	$substep=5;	

	$prname = "emn";

	$config = array();

	$config['DONE_PERCENT'] = '3';

	$config["DOCUMENT_ROOT"] = $_SERVER['DOCUMENT_ROOT'];
//	$config["DOCUMENT_ROOT"] = $_SERVER['DOCUMENT_ROOT'].'addsite/estmesto.smhost.ru/';
	$config['server_url'] = 'http://'.$_SERVER['SERVER_NAME'].'/';


	ob_start();
	require($config["DOCUMENT_ROOT"].'inc/lang/ru.php');
	ob_end_clean();
	$config['LANG'] = $LANG;

	$config['dbhost'] = 'localhost';
	$config['dbname'] = 'u21360_estmesto';
	$config['dbuser'] = 'u21360';
	$config['dbpass'] = 'bukor8bevov';
	//настройка типа кодировки соединения с MYSQL
	$config['mysql_set_names'] = 'utf8';
	

	//MD5-Salt
	$config['md5'] = 'fgY408vbnQw';	
	
    $config['site_name'] = 'Есть место';
    $array_admin_add = array 
    (
		'0'=>'4',
	);


	$config['VK_APP_ID'] = '2830724';

// сопоставление url-веток и шаблонов папки (вместо таблицы БД)
	$config['URLS']['/']['template'] = 'index';
	$config['URLS']['/']['name'] = '';

	$config['URLS']['']['template'] = 'index';
	$config['URLS']['']['name'] = '';

	$config['URLS']['trip']['template'] = 'trip';
	$config['URLS']['trip']['name'] = 'Трип';

	$config['URLS']['auth']['template'] = 'user';
	$config['URLS']['auth']['name'] = 'Авторизация';

	$config['URLS']['user']['template'] = 'user';
	$config['URLS']['user']['name'] = 'Авторизация';

	$config['URLS']['info']['template'] = 'text';
	$config['URLS']['info']['name'] = 'Информация';

	$config['URLS']['regme']['template'] = 'user';
	$config['URLS']['regme']['name'] = 'Вход по e-mail';

	$config['URLS']['once']['template'] = 'user';
	$config['URLS']['once']['name'] = 'Экспресс-вход';
//================ ЧАТ ============================================

	$config['URLS']['chat']['template'] = 'chat';
	$config['URLS']['chat']['name'] = 'Чат';

	$config['URLS']['say']['template'] = 'chat';
	$config['URLS']['say']['name'] = 'Чат - сказать';

//=================================================================	

	$wrappers = array();


	$texts['sql_connection_error'] = 'Невозможно подключиться к серверу баз данных.';
	$texts['sql_db_selection_error'] = 'Невозможно выбрать базу данных.';

	/*
	кеширование
		0 - отключено
		1 - локальное
		2 - глобальное (страница целиком)
	*/
	$config['cache'] = 0;

    //включение режима "автодополнения" для модулей
    //при включении будут самостоятельно создаваться необходимые методы у модулей
    $config['metamodule_autocreate'] = 1;
    
  
?>