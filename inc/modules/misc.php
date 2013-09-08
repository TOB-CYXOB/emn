<?
/*
###############################################################################
#  (c)SOFTMAJOR 2011-2011
###############################################################################
#  ЗДЕСЬ РАЗМЕСТИТЬ КОНТАКТНЫЕ ДАННЫЕ АВТОРА МОДУЛЯ
################################################################################
   ЗДЕСЬ РАЗМЕСТИТЬ КРАТКОЕ ОПИСАНИЕ ЕГО НАЗНАЧЕНИЯ И Т.П.
*/

class misc extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
/* раскомментируй это при необходимости
        'misc',
*/
);
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
/* раскомментируй это при необходимости
        'misc' => 'inner.html',
*/
);
    }

    function __destruct()
    {
    }

    //базовый метод сайт-модуля
    function content($arParams=array())
    {
        global $control;
        global $config;
        global $sql;

        return "Контент модуля <b>misc</b>";

        //раскомментировать при доработке
        //return $this->sprintt($page, $this->_tplDir().'content.html');
    }





    function head($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s",get_class($this), 'head',  cache_key()); 
        $html = get_cache($_cn, 120);
        if (!is_null($html)) return $html;

		if (strlen($control->PageName) && strlen($control->SiteName))
			$page->title = trim($control->PageName." - ".$control->SiteName);
		else
		if (strlen($control->PageName))
			$page->title = trim($control->PageName." - ".$config['site_name']);
		else
		if (strlen($control->name))
			$page->title = trim($control->name." - ".$config['site_name']);
		else
			$page->title = $config['site_name'];

		$page->keywords="сервис организации поездок,автомобили, бронировать, свободное место";
		$page->description="Организация совместных поездок на транспортных средствах";

        $html = $this->phptpl($page, $this->_tplDir()."head_tpl.php");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;        
    }



    function authBlock($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
//        $_cn = sprintf("%s_%s_%s",get_class($this), 'authBlock',  cache_key(true)); 
//        $html = get_cache($_cn, 300);
//        if (!is_null($html)) return $html;

		$page->user = $control->user;
		$page->siteurl = sprintf("http://%s%s",$_SERVER['SERVER_NAME'],$_SERVER['REQUEST_URI']);

		if (isset($control->SiteName))
			$page->SiteName = $control->SiteName;
		else
			$page->SiteName = $config['LANG']['SITENAME_HAVEPLACE'];

		if (isset($control->PageName))
			$page->PageName = $control->PageName;
		else
			$page->PageName = $control->name;

		$page->CurrentTripUrl = $control->CurrentTripUrl;

        $html = $this->phptpl($page, $this->_tplDir()."authBlock_tpl.php");

        //сохраняем кэш
//        set_cache($_cn, $html);

        return $html;        
    }


    function UserEcho($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s",get_class($this), 'UserEcho',  cache_key()); 
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

		//тут пусто


        $html = $this->phptpl($page, $this->_tplDir()."UserEcho_tpl.php");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;        
    }

// <#AUTOMETHODS#>



















































}
?>