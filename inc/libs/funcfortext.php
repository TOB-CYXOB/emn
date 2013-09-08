<?php

  $c30 = chr(30);
  $c31 = chr(31);

    function text_view($text)  {
      global $config;

		$text = stylizeimages(parse_table(parse_begin($text)));

		$text = str_replace("../../images/", $config['upload_url'], $text);
		$text = str_replace("../../documents/", $config['upload_doc'], $text);
		$text = substr($text, 6);
		//замена блоквотов на что то более красивое

		$tmp_block_start = '
				<blockquote>

		';
									
		$tmp_block_end = '
			</blockquote>

		';

		$text = eregi_replace("<blockquote>", $tmp_block_start, $text); // оформление
		$text = eregi_replace("</blockquote>", $tmp_block_end, $text); // оформление


      return parse_href($text);
    }


  function htmltoxml($data) {

		global $c30;
		global $c31;
		//$tm = time() + microtime();
		while (($data = ereg_replace('<([a-zA-Z0-9]+[^>]* [a-zA-Z0-9]+)=([^" >]*)( |>)',
							 '<\1="\2"\3', 
							 $olddata = $data)) != $olddata); // суем аттрибуты в кавычки
		$data = eregi_replace('<[ ]*((img|br|hr)[^>]*)>', '<\1/>', $data); // одиночные тэги переводим в xml-стиль
		$data = eregi_replace('(<[^>]* )nowrap( |>)', '\1nowrap="nowrap"\2', $data); // аттрибуты без значений переводим в xml-стиль
//		$data = str_replace('&nbsp;', '&#160;', $data); // заменяем пробелы
//		$data = str_replace('&shy;', '', $data); // убираем переносы
		$data = str_replace("</P", '</p', $data); // переводим P в нижний регистр
		$data = str_replace("<P", '<p', $data); // переводим P в нижний регистр
		$data = str_replace("</LI", '</li', $data); // переводим LI в нижний регистр
		$data = str_replace("<LI", '<li', $data); // переводим LI в нижний регист//
		$data = str_replace("</UL", '</ul', $data); // переводим UL в нижний регистр
		$data = str_replace("<UL", '<ul', $data); // переводим UL в нижний регист//
		$data = str_replace("</SPAN", '</span', $data); // переводим LI в нижний регистр
		$data = str_replace("<SPAN", '<span', $data); // переводим LI в нижний регист//
		$data = str_replace("</DIV", '</div', $data); // переводим LI в нижний регистр
		$data = str_replace("<DIV", '<div', $data); // переводим LI в нижний регист//
		$data = str_replace("</H1", '</h1', $data); // переводим LI в нижний регистр
		$data = str_replace("<h1", '<h1', $data); // переводим LI в нижний регист//
		$data = str_replace("</H2", '</h2', $data); // переводим LI в нижний регистр
		$data = str_replace("<h2", '<h2', $data); // переводим LI в нижний регист//
		$data = str_replace("</H3", '</h3', $data); // переводим LI в нижний регистр
		$data = str_replace("<h3", '<h3', $data); // переводим LI в нижний регист//
		$data = str_replace("</H4", '</h4', $data); // переводим LI в нижний регистр
		$data = str_replace("<h4", '<h4', $data); // переводим LI в нижний регист//
		$data = str_replace("</H5", '</h5', $data); // переводим LI в нижний регистр
		$data = str_replace("<h5", '<h5', $data); // переводим LI в нижний регист//
		$data = str_replace("</H6", '</h6', $data); // переводим LI в нижний регистр
		$data = str_replace("<h6", '<h6', $data); // переводим LI в нижний регист//

		$data = str_replace("<TABLE", '<table', $data); //

		$data = str_replace("</TABLE", '</table', $data); //
		$data = str_replace("<TH", '<th', $data); //
		$data = str_replace("</TH", '</th', $data); //
		$data = str_replace("<TD", '<td', $data); //
		$data = str_replace("</TD", '</td', $data); //
		$data = str_replace("<TR", '<tr', $data); //
		$data = str_replace("</TR", '</tr', $data); //
		$data = closetag($data);
 		$data = eregi_replace('&(^#)', '&amp;\1', $data); // заменяем &
//		$data = eregi_replace("<p[^>]*>[ \n\r]*</p>", '', $data); // убираем пустые <p>
		//echo time() + microtime() - $tm;
		//exit;

		$data = eregi_replace("<pre[^>]*>", '<blockquote>', $data); // оформление
		$data = eregi_replace("</pre[^>]*>", '</blockquote>', $data); // оформление
		$data = eregi_replace("<blockquote[^>]*>", '<blockquote>', $data); // оформление
		$data = eregi_replace("</blockquote[^>]*>", '</blockquote>', $data); // оформление


		$data = eregi_replace("<address[^>]*>", '<cite>', $data); // оформление
		$data = eregi_replace("</address[^>]*>", '</cite>', $data); // оформление



		//$data = tags_text('div', $data);
		//$data = tags_text('span', $data);
		//$data = tags_text('p', $data);
		//$data = tags_text('h1', $data);
		//$data = tags_text('h2', $data);
		//$data = tags_text('h3', $data);
		//$data = tags_text('h4', $data);




		return $data;
  };

  function closetag($data) {
		$i = 0; $intag = 0; $tags = array();
		$rd = '';
		$n = strlen($data);
		while (($i = strpos($data, '<', $wasi = $i)) !== false) {
			$rd .= substr($data, $wasi, $i - $wasi);
			//$n1 = strpbrk($data, " >\r\n/");
			if (($n1 = strpos($data,  ' ', $i + 2)) === false) $n1 = $n;
			if (($n2 = strpos($data,  '>', $i + 2)) === false) $n2 = $n;
			if (($n3 = strpos($data, "\r", $i + 2)) === false) $n3 = $n;
			if (($n4 = strpos($data, "\n", $i + 2)) === false) $n4 = $n;
			if (($n5 = strpos($data,  '/', $i + 2)) === false) $n5 = $n;
			$t = strtolower(substr($data, $i + 1, min($n1, $n2, $n3, $n4, $n5) - $i - 1));
			//echo /*($i + 1) . "_" . min($n1, $n2, $n3, $n4) . "_" . */htmlspecialchars($t) . "<br>";
			if ($t == 'p') {
				if ($tags[$intag] == 'p') $rd .= '</p>'; else $tags[++$intag] = $t;
			} elseif ($t == 'li') {
				if ($tags[$intag] == 'li') $rd .= '</li>'; else $tags[++$intag] = $t;
			} elseif ($t[0] == '/') {
				if (($tags[$intag] ==  'p') && ($t !=  '/p')) {$rd .=  '</p>'; $intag--;};
				if (($tags[$intag] == 'li') && ($t != '/li')) {$rd .= '</li>'; $intag--;};
				$intag--;
			} elseif (($t != 'br') && ($t != 'img') && ($t != 'hr')) $tags[++$intag] = $t;
			$rd .= $data[$i++];
		};
		return $rd . substr($data, $wasi);
  };


	function tags_text($tags, $s)		{
//функция парсит теги оставляя только выравнивание
		$data = $s;

		$n = -1;
		while (($n = strpos(strtolower($s), '<'.$tags, $n + 1)) !== false) {
			$n1 = strpos($s, ">", $n);
			$tableopen = substr($s, $n, $n1 - $n + 1);
		
			$regs = array();
			eregi('(align="?([^"^ ]*)"?)', $tableopen, $regs); // выделяем значение alt у тэга <img>
			$alignparam = $regs[1]; $border = $regs[2]; unset($regs); // очищаем переменные

			$newtags = '<'.$tags;
			if (trim($alignparam) != '')	{
				$newtags .= ' '.$alignparam;
			}
			$newtags .= '>';

			$data = str_replace($tableopen, $newtags, $data);
		}	
//		echo $data;


		return $data;
	}


  function parse_table($s)  {



	$n = -1;
	while (($n = strpos(strtolower($s), '<table', $n + 1)) !== false) {
		$n1 = strpos($s, ">", $n);
		$tableopen = substr($s, $n, $n1 - $n + 1);
		
		$regs = array();
		eregi('(border="?([^"^ ]*)"?)', $tableopen, $regs); // выделяем значение alt у тэга <img>
		$borderparam = $regs[1]; $border = $regs[2]; unset($regs); // очищаем переменные
		
		$regs = array();
		//eregi('(width(=|:)"? *([0-9]+)%"?)', $tableopen, $regs); // выделяем значение alt у тэга <img>
		eregi('(width="?([^"^ ]*)"?)', $tableopen, $regs); // выделяем значение alt у тэга <img>

		$widthparam = $regs[1]; $width = $regs[2]; unset($regs); // очищаем переменные


		

		
		/*$snew = substr($s, 0, $n) . "<table" .
			($width ? " width=\"$width\"" : "") .
			($border ? ' class="table"' : ' ') .
			" cellpadding=\"0\" cellspacing=\"0\">";
		$s = substr($s, $n1 + 1);*/
		
		if (($n2 = strpos(strtolower($s), "</table")) === false) $n2 = strlen($s);
		$s = eregi_replace('colspan', chr(3), $s);
		$s = eregi_replace('rowspan', chr(4), $s);
		$s = eregi_replace('<(/?)td[^' . chr(3) . '^' . chr(4) . '^>]*([' . chr(3) . chr(4) . ']=[^>^ ]+)?[^>]*>', '<\1td \2>', substr($s, 0, $n2)) . substr($s, $n2);
		$s = str_replace(chr(4), 'rowspan', $s);
		$s = str_replace(chr(3), 'colspan', $s);
		$s = eregi_replace('<(/?)tr[^>]*>', '<\1tr>', substr($s, 0, $n2)) . substr($s, $n2);
		if ($border) {
			if (($n3 = strpos(strtolower(substr($s, 0, $n2)), "</tr")) === false) $n3 = $n2;
			$s = str_replace( '<td',  '<th', substr($s, 0, $n3)) . substr($s, $n3);
			$s = str_replace('</td', '</th', substr($s, 0, $n3)) . substr($s, $n3);

/*
			if (($n3 = strrpos(strtolower(substr($s, 0, $n2)), "<tr")) === false) $n3 = $n2;
			$s = substr($s, 0, $n3) . str_replace( '<tr',  '<tr class="bb"', substr($s, $n3));
*/

//TH
			$sfile = '';
//			echo htmlspecialchars($line);
			$line = substr($s, 0, $n3);
			$line_count = substr_count($line, '<th');
			$strok = $line;
			$yes = 0;
			$i = 0;
			while (strstr($strok, "<th"))		{
				$curs = strpos ($strok, "<th");
				$text1 = substr($strok, 0, $curs); 
				$strok = substr ($strok, $curs, strlen($strok));    
				$curs = strpos ($strok,">");
				$text2 = substr($strok, 0, $curs+1);

                //colspan
                $args = array();
		        eregi('(colspan="?([^"^ ]*)"?)', $text2, $args);
		        if ($args)
		          $colspan = $args[1];
		        else
		          $colspan = "";

                //colspan
                unset($args);
                $args = array();
		        eregi('(rowspan="?([^"^ ]*)"?)', $text2, $args);
		        if ($args)
		          $rowspan = $args[1];
		        else
		          $rowspan = "";

		        
                $strok = substr ($strok, $curs+1);
				if ($i == $line_count-1)  {
					$sfile.= $text1.'<th class="last tr" '.$colspan.' '.$rowspan.'>';
//					$sfile.= $text1.'<th class="last rgh">';
//					$sfile.= $text1.'<th>';
				} else  {
					if ($i == 0)  {
						$sfile.= $text1.'<th  class="first tl" '.$colspan.' '.$rowspan.'>';
//						$sfile.= $text1.'<th class="first lft">';
//						$sfile.= $text1.'<th>';
					}  else  {
						$sfile.= $text1.'<th '.$colspan.' '.$rowspan.'>';
					}
				}
				$yes = 1;
				if (!strstr($strok, "<th"))	{
        			$sfile.= $strok;
				}
			$i++;
		    }
			//echo $i;
			if ($yes == 1)	{
				$strok = $sfile;
		    }
            
			$s = str_replace($line, $strok, $s);

//td
			$sfile = '';
			$strok = $s;
			$yes = 0;
			$i = 0;
			while (strstr($strok, "<tr"))		{
				$curs = strpos ($strok, "<tr");
				$text1 = substr($strok, 0, $curs); 
				$strok = substr ($strok, $curs, strlen($strok));    
				$curs = strpos ($strok,"/tr>");
				$text2 = substr($strok, 0, $curs+4);
				$strok = substr ($strok, $curs+4);


				if (($i/2) == floor($i/2))		{
					$text_new2 = $text2;
//					черезполосица
//					$text_new2 = str_replace('<tr', '<tr class="bg"', $text2);
				}	else	{
					$text_new2 = $text2;
				}
                if($i==0)$text_new2 = str_replace('<tr', '<tr class="thead"', $text2);else $text_new2 = str_replace('<tr', '<tr class="tbody"', $text2); // Первая строка
/*
				$text_pos2 = strrpos($text_new2, '<td');
				if ($text_pos2 > 0)		{
					$text_pos2 = strlen($text_new2) - $text_pos2;
					$text_new2 = substr($text_new2, 0, $text_pos2) . '<td class="last"' . substr($text_new2, $text_pos2+3);
				}

				echo "<P>".htmlspecialchars($text_new2)."</P>";
*/

				$s = str_replace($text2, $text_new2, $s);
/*
				if ($i == $line_count-1)  {
					$sfile.= $text1.'<td class="last">';
//					$sfile.= $text1.'<th class="last rgh">';
//					$sfile.= $text1.'<th>';
				} else  {
					if ($i == 0)  {
						$sfile.= $text1.'<td class="first">';
//						$sfile.= $text1.'<th class="first lft">';
//						$sfile.= $text1.'<th>';
					}  else  {
						$sfile.= $text1.'<td>';
					}
				}
*/
				$yes = 1;
				if (!strstr($strok, "<tr"))	{
        			$sfile.= $strok;
				}
			$i++;
		    }
/*
			if ($yes == 1)	{
				$strok = $sfile;
		    }
*/
//			echo htmlspecialchars($sfile);
//			$s = str_replace($line, $strok, $s);




			$s = str_replace('</table>', '</table>', $s);


		};

		$s = $snew . $s;
	};


	return $s;
  }


///////////////////
  function parse_begin($text)   {
	  global $config;

	  $text = htmltoxml($text);

      $text = str_replace('<TBODY>', '', $text);
      $text = str_replace('</TBODY>', '', $text);

      $strok = $text;
      $yes = 0;

    $text = $strok;

    $text = str_replace("../../images/", $config['server_url']."images/", $text);
    $text = str_replace("../../documents/", $config['server_url']."documents/", $text);

    $text = '&nbsp;'.$text.'&nbsp;';



    return $text;
  }

  function parse_href($text)
  {
	return $text;
  	global $config;
  	$ico['doc'] = '/img/icons/ico-doc.gif';
  	$ico['xls'] = '/img/icons/ico-xls.gif';
  	$ico['pdf'] = '/img/icons/ico-pdf.gif';
  	$ico['rar'] = '/img/icons/ico-rar.gif';
  	$ico['zip'] = '/img/icons/ico-zip.gif';
  	$tb = '<span class="ico-link"><a href="{href}"><img src="{tmpimg}" /></a>&nbsp;';
  	$ta = '&nbsp;(.{type}, {size}Кб)</span>';
  	
  	preg_match_all('/<a(.*)href="(.*)"(.*)>(.*)<\/a>/Uis',$text, $out);
  	for ($i=0;$i<count($out[1]);$i++)
  	if($ico[$type = strtolower(substr($out[2][$i],-3))])$text = str_replace($out[0][$i],str_replace('{tmpimg}',$ico[$type],str_replace('{href}',$out[2][$i],$tb)).$out[0][$i].str_replace('{type}',$type,str_replace('{size}',(round(filesize(str_replace($config['server_url'],'',$out[2][$i]))/1024)),$ta)),$text);
  	
  	return $text;
  }



  function stylizeimages($s) {
		global $config;
		global $code_imgclass;
		global $code_open;
		global $code_close;
		$n = -1;
		
		while (($n = strpos(strtolower($s), '<img', $n + 1)) !== false) {
			$n1 = strpos(strtolower($s), '</a>', $n + 1); // ищем закрывающий тэг A для определения - в ссылке или нет
			$n2 = strpos(strtolower($s), '<a ', $n + 1); // ищем открывающий тэг A
			$s2 = '';
			if (($n1 !== false) && (($n1 < $n2) || ($n2 == false))) { // если внутри ссылки
				$ina = true;
				// проверяем, только ли картинка внутри тэга ссылки
				$n2 = $n - strpos(strtolower(strrev(substr($s, 0, $n - 1))), 'a<') - 3;
				$s2 = substr($s, $n2, $n1 + 4 - $n2); // выделили конструкцию <a> .. <img> .. </a>
				$onlya = eregi('^<a[^>]*><img[^>]*></a>$', $s2) !== false;

				$n3 = strpos($s2, '>');
				if ($n3 !== false) $aopen = substr($s2, 0, $n3 + 1); // выделили открывающий A
				$aclose = substr($s2, -4); // выделили закрывающий A
				$aopen2 = substr($s2, $n3 + 1, $n - $n2 - $n3 - 1); // выделили то что между открывающим A и IMG
				$n3 = strpos($s, '>', $n + 1);
				$aclose2 = substr($s2, $n3 + 1 - $n2, -4); // выделили то что между IMG и закрывающим A
				$img = substr($s, $n, $n3 - $n + 1); // выделили сам IMG
				
				$regs = array();
				if (!eregi('href="([^"]*)"', $aopen, $regs))
				eregi('href=([^ ^\>]*)( |\>)', $aopen, $regs); // выделяем значение href у тэга <a>
				if (!$regs[1]) $regs[1] = ''; // если не найден href - делаем его пустым
				$href = $regs[1]; unset($regs); // очищаем переменные
				
				$regs = array();
				if (!eregi('target="([^"]*)"', $aopen, $regs))
				eregi('target=([^ ^\>]*)( |\>)', $aopen, $regs); // выделяем значение href у тэга <a>
				if (!$regs[1]) $regs[1] = ''; // если не найден href - делаем его пустым
				$target = $regs[1]; unset($regs); // очищаем переменные
			} else {
				$ina = false;
				$n1 = strpos($s, ">", $n);
				if ($n1 !== false) $s2 = substr($s, $n, $n1 - $n + 1); // выделили конструкцию <img>
				$img = $s2;
			};
			
			$regs = array();
			eregi('(align="?([^"^ ]*)"?)', $img, $regs); // выделяем значение align у тэга <img>
			if (!in_array(strtolower($regs[2]), array('left', 'right', ''))) continue; // если align не left и не right - сваливаем
			$alignparam = $regs[1]; $align = $regs[2]; unset($regs); // очищаем переменные
			
			$regs = array();
			eregi('(alt="?([^"]*)"?)', $img, $regs); // выделяем значение alt у тэга <img>
			$altparam = $regs[1]; $alt = $regs[2]; unset($regs); // очищаем переменные

			//высота и ширина
			$regs = array();
			eregi('(width="?([^"]*)"?)', $img, $regs); 
			$wparam = $regs[1]; $width = $regs[2]; unset($regs); // очищаем переменные

			$regs = array();
			eregi('(width: ?([0-9]*)px?)', $img, $regs); //width: 266px
			if (intval($regs[2]))
				$wparam = 'width="'.$regs[2].'"'; unset($regs); // очищаем переменные

			$regs = array();
			eregi('(height="?([^"]*)"?)', $img, $regs); 
			$hparam = $regs[1]; $heigth = $regs[2]; unset($regs); // очищаем переменные

			$regs = array();
			eregi('(height: ?([0-9]*)px?)', $img, $regs); //height: 266px
			if (intval($regs[2]))
				$hparam = 'height="'.$regs[2].'"'; unset($regs); // очищаем переменные
			
			$regs = array();
			eregi('(title="?([^"^ ]*)"?)', $img, $regs); // выделяем значение title у тэга <img>
			$titleparam = $regs[1]; $title = $regs[2]; unset($regs); // очищаем переменные
			
			$regs = array();
			eregi('(src="?([^"^ ]*)"?)', $img, $regs); // выделяем значение title у тэга <img>
			$srcparam = $regs[1]; $src = $regs[2]; unset($regs); // очищаем переменные


			if ($ina) {
				$onclick = '';
				if ((substr($href, -4) == '.jpg') ||
					(substr($href, -4) == '.gif') ||
					(substr($href, -5) == '.jpeg') ||
					(substr($href, -4) == '.png') ||
					(substr($href, -4) == '.bmp')) {
					$target = "_blank";
					if (substr($href, 0, strlen($config['server_url'])) == $config['server_url']) {
						$onclick = ' onclick="javascript:window.open(\'' . $config['server_url'] . 'picture/' . substr($href, strlen($config['server_url'])) . '\', \'_blank\', \'height=100,width=100,status=0,toolbar=0,menubar=0,resizable=1,scrollbars=0,titlebar=0\');return false;"';
					};
				};

				$class = 'class="'.$code_imgclass[$align].'"';
				//if(!$code_imgclass[$align]) $class = 'class="img-dft"';

				$newimg = '<a href="' . $href . '" target="' . $target . '"' . $onclick . ' ><img src="' . $src . '" title="Нажмите, чтобы увеличить изображение" alt="' . $alt . '" '.$class.'></a>';
				$newimg .= '';
//				$newimg = '<img src="' . $src . '" title="' . $alt . '" alt="' . $alt . '">';
//				$newaopen = '<a href="' . $href . '" target="' . $target . '"' . $onclick . ' >';
//				$newaclose = '</a>';

//				$newaopen = '</div><div class="zoom"><a href="' . $href . '" target="' . $target . '"' . $onclick . ' >+ увеличить +';


				global $code_plus;
				$newaopen = $code_plus;
				$newaopen = str_replace('{href}', $href, $newaopen);
				$newaopen = str_replace('{target}', $target, $newaopen);
				$newaopen = str_replace('{onclick}', $onclick, $newaopen);
				

//				$newaopen = '';

				$newaclose = '';

				//echo $newimg; die();
				$newblock = $code_open[$align] . $newimg . $newaopen . $newaclose . $code_close[$align] . '';
				if ($onlya) {
					$snew = $newblock;
					$s = substr($s, 0, $n2) . $snew . substr($s, $n1 + 4);
					$n = $n2 + strlen($snew);
				} else {
					$snew = '';
					if ($aopen2) $snew .= $aopen . $aopen2 . $aclose;
					$snew .= $newblock;
					if ($aclose2) $snew .= $aopen . $aclose2 . $aclose;
					$s = substr($s, 0, $n2) . $snew . substr($s, $n1 + 4);
					$n = $n2 + strlen($snew);
				};
			} else {
				

				$class = 'class="'.$code_imgclass[$align].'"';
				//if(!$code_imgclass[$align]) $class = 'class="img-dft"';
	
				$newimg = sprintf('<img src="%s" title="%s" alt="%s" %s %s %s>', $src, $alt, $alt, $wparam, $hparam, $class);
				$newblock = $code_open[$align] . $newimg . $code_close[$align];
				$snew = $newblock;
				$s = substr($s, 0, $n) . $snew . substr($s, $n1 + 1);
				$n = $n + strlen($snew);
			};

		};

		/*
		echo htmlspecialchars($s2) . "<br>";
		echo "ina - " . ($ina ? 1 : 0) . "<br>";
		echo "onlya - " . ($onlya ? 1 : 0) . "<br>";
		echo "aopen - " . htmlspecialchars($aopen) . "<br>";
		echo "aclose - " . htmlspecialchars($aclose) . "<br>";
		echo "aopen2 - " . htmlspecialchars($aopen2) . "<br>";
		echo "aclose2 - " . htmlspecialchars($aclose2) . "<br>";
		echo "href - " . htmlspecialchars($href) . "<br>";
		echo "img - " . htmlspecialchars($img) . "<br>";
		echo "align - " . htmlspecialchars($align) . "<br>";
		echo "alt - " . htmlspecialchars($alt) . "<br>";
		echo "title - " . htmlspecialchars($title) . "<br>";
		echo "snew - " . nl2br(htmlspecialchars($snew)) . "<br>";
		exit;
		*/

		return $s;
  };



?>