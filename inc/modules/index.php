<?
class index extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
        'index',
);
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
        'index' => 'index.html',
);
    }

    function __destruct()
    {
    }

	function showMain()
	{
      global $control;
      global $config;
      global $sql;

      //попытка загрузить из кэша
//      $_cn = sprintf("%s_%s_%s",get_class($this), 'index',  cache_key()); 
//      $html = get_cache($_cn);
//      if (!is_null($html)) return $html;
	
	  $_GTC->percent = $config['DONE_PERCENT'];	
	  $_GTC->user = $control->user;

	  if (strlen($control->user->userKey))
		$_GTC->createButton = 1;

		$_GTC->siteurl = sprintf("http://%s",$_SERVER['SERVER_NAME']);


	  $html = $this->phptpl($_GTC, $this->_tplDir().'main_tpl.php');
      //сохраняем кэш
//      set_cache($_cn, $html);
	  return $html;	
	}

    //базовый метод сайт-модуля
    function content($arParams=array())
    {
      global $control;
      global $config;
      global $sql;

	  return $this->showMain();
    }   
	
}

?>