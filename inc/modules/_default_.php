<?
/*
###############################################################################
#  (c)SOFTMAJOR 2011-<!--year//-->
###############################################################################
#  ЗДЕСЬ РАЗМЕСТИТЬ КОНТАКТНЫЕ ДАННЫЕ АВТОРА МОДУЛЯ
################################################################################
   ЗДЕСЬ РАЗМЕСТИТЬ КРАТКОЕ ОПИСАНИЕ ЕГО НАЗНАЧЕНИЯ И Т.П.
*/

class <!--name//--> extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
/* раскомментируй это при необходимости
        '<!--name//-->',
*/
);
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
/* раскомментируй это при необходимости
        '<!--name//-->' => 'inner.html',
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

        return "Контент модуля <b><!--name//--></b>";

        //раскомментировать при доработке
        //return $this->sprintt($page, $this->_tplDir().'content.html');
    }

// Сюда будут заноситься автодополняемые методы
// <#AUTOMETHODS#>

}
?>