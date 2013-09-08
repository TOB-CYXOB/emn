<?
class Obj2xml {

    var $xmlResult;
	var $attrs = array('ID', 'USERID', 'KEY', 'TRIPID', 'TRANSPORTID', 'CREATOR', 'CREATEDATE', 'PASSENGERID', 'PLACECOUNT',
		'TYPEID', 'CHANGETIME', 'STATUS', 'START');
   
    function __construct($rootNode){
        $this->xmlResult = new SimpleXMLElement("<$rootNode></$rootNode>");
    }
   
    private function iteratechildren($object,$xml){
        foreach ($object as $name=>$value) {
            if (is_string($value))
			{
				if (in_array(strtoupper($name), $this->attrs))
                	$xml->addAttribute($name, $value);
				else
                	$xml->$name=$value;
            }
			else if (is_numeric($value))
			{
                $xml->addAttribute($name, $value);
			}
			else if (is_array($value))
			foreach($value as $idx => $arrobj)
			{
                $this->iteratechildren($arrobj,$xml->addChild($name));
			} 
			else {
                $xml->$name=null;
                $this->iteratechildren($value,$xml->$name);
            }
        }
    }
   
    function toXml($object) {
        $this->iteratechildren($object,$this->xmlResult);
        return $this->xmlResult->asXML();
    }
}

?>