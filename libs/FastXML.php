<?
/*
  ����� ��� �������� ������� ��������� XML-����������.

  * FastXML( &$rh, $xml="" ) -- �����������. ����� �������� SetXML().

  * SetXML($xml) -- ����������� � ������� xml-��������.

  * GetArray($tag) -- ���������� ��� ��������� ��������� ������� ���� ��� ������.

  * GetOne($tag) -- ���������� ������ ��������� ��������� ������� ���� ��� �����.

================================================================== v.1 (zharik@jetstyle)
*/
  
class FastXML {
  
  var $xml; //������ c xml-���������� ��� �������
  
  function _GetNodeData($tag){
    $re = "/<".$tag.".*?>(.*?)<\/".$tag.".*?>/is";
    preg_match_all( $re, $this->xml, $matches );
    return $matches[1];
  }
  
  function GetArray($tag){
    return $this->_GetNodeData($tag);
  }
  // ================================================================================
function get_tag($tag,$tag_content)
{
	$re = "/<".$tag.".*?>(.*?)<\/".$tag.".*?>/is";
    preg_match_all( $re, $tag_content, $matches );
    return $matches[1][0];
}
  function GetOne($tag){
    $A = $this->GetArray($tag);
    return $A[0];
  }
  
  function GetAttrs($tag,$all = false){
    //���� ������ � ��������<h2></h2>���
    $re = "/<".$tag."(.*?)\/{0,1}>/is";
    preg_match_all( $re, $this->xml, $matches );
    //������������ �� � �����
    $R = array();
    foreach($matches[1] as $str){
//      echo $str."<br>";
      //������ ��������� ��������
      $re = "/\s.*?=\".*?\"/i";
      preg_match_all( $re, $str, $matches );
      $A = $matches[0];
      $B = array();
      foreach($A as $s1){
        $C = explode("=",$s1);
        $B[ trim($C[0]) ] = trim($C[1],"\"");
      }
      //����� ������ ����?
      if(!$all) return $B;
      //�������� ���
      $R[] = $B;
    }
    return $R;
  }
}

?>