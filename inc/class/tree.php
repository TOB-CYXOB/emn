<?php

  class Tree  {
	function BeginTree()		{
		global $sql;

		$q = "SELECT count(id) FROM prname_tree WHERE writeend = 1 ";
		if (!sql::one_record($q))	return true;

	}
	function MakeTree()		{
		global $sql;

		if (!$this->BeginTree()) return;

		$q = "TRUNCATE TABLE prname_tree ";
		sql::query($q);

		$q = "INSERT INTO prname_tree (id, parent, name, level, left_key, right_key, `key`, template, visible) VALUES (1, 0, 'Главная страница', 0, 1, 2, 'main', 'index', 1) ";
		sql::query($q);

		$this->GetTree(1);
		$this->WriteUrlTree();
		$this->EndTree();


	}
	
    function tree_url($parent='')
    {
    	global $control;
    	global $config;
        $q = sql::query("select p2.* from prname_tree p1, prname_tree p2 where p2.left_key<=p1.left_key and p2.right_key>=p1.right_key and p1.id='".($parent?$parent:$control->cid)."' ORDER BY p2.left_key");
        while ($arr = mysql_fetch_assoc($q))
        {
     	    $array[$arr[level]]->id = $arr[id];
     	    $array[$arr[level]]->name = $arr[name];
     	    $array[$arr[level]]->url = $arr[url]=='/'?$config['server_url']:$config['server_url'].$arr[url];
        }
     if(isset($array))return $array;
    }
    
	function EndTree()		{
		global $sql;

		$q = "UPDATE prname_tree SET writeend = 1 ";
		sql::query($q);

	}
	function WriteUrlTree()		{
		global $sql;

		$q = "SELECT id, `key`, left_key, right_key FROM prname_tree ORDER BY id";
		$res = sql::query($q);
		while ($str = sql::fetch_array($res))	{
			$id = $str['id'];
			$key = $str['key'];
			$left_key = $str['left_key'];
			$right_key = $str['right_key'];

			$url = '';

			$q = "SELECT id, `key`, level FROM prname_tree WHERE left_key <= '$left_key' AND right_key >= '$right_key' ORDER BY left_key ";
			$res2 = sql::query($q);
			$i = 0;
			while ($str2 = sql::fetch_array($res2))		{
				$tmp_id = $str2['id'];
				$tmp_key = $str2['key'];
				if ($i > 1)
					$url .= '/';	
				if ($tmp_key <> '')		{
					$url .= $tmp_key;
				}	else	{
					$url .= $tmp_id;
				}
			$i++;
			}
			$url .= '/';	
			$url = substr($url, 4);
			$q = "UPDATE prname_tree SET url = '$url' WHERE id = '$id' ";
			sql::query($q);
		}

	}
	function GetTree($parent, $old_parent=0)
	{
		global $sql;

		$q = "SELECT * FROM prname_categories WHERE parent = '$parent' ORDER BY sort ";
		$res = sql::query($q);
		if (sql::num_rows($res) > 0)	{
			while ($str = sql::fetch_array($res) )	{
				$id = $str['id'];
				$name = $str['name'];
				$sort = $str['sort'];
				$key = $str['key'];
				$template = $str['template'];
				$visible = $str['visible'];
//				echo " $id $name <br>";

				$q = "SELECT level, left_key, right_key FROM prname_tree WHERE id = '$parent' ";
				$str1 = sql::fetch_array(sql::query($q));
				$level = $str1['level'];
				$left_key = $str1['left_key'];
				$right_key = $str1['right_key'];		

				$q  = "UPDATE prname_tree SET left_key = left_key + 2, right_key = right_key + 2 WHERE left_key > $right_key ";
				sql::query($q);

				$q = " UPDATE prname_tree SET right_key = right_key + 2 WHERE right_key >= '$right_key' AND left_key < '$right_key' ";
				sql::query($q);

				$q = "INSERT INTO prname_tree SET left_key = $right_key, right_key = $right_key + 1, level = $level + 1, id = '$id', name = '".sql::escape_string($name)."', parent = '$parent', sort = '$sort', `key` = '$key', template = '$template', visible = '$visible' ";
				sql::query($q);


				$this->GetTree($id, $parent);
			}
		}

	}
	
    function tree_all ($id='1',$level='')
  {
  	global $control;
  	global $config;
     $q = sql::query("SELECT p1.*,p2.level as parent_level FROM prname_tree p1,prname_tree p2 where p1.left_key > p2.left_key and p1.right_key < p2.right_key and p2.id='$id' and p1.visible=1".($level!==''?" and p1.level<=$level":"")." ORDER BY p1.left_key");
     $i=0;
     while($b =  mysql_fetch_assoc($q))
         { 
           if(
           $b[level]==$b[parent_level]+1)
           {
           	 if($control->cid==$b[id])$a->item[$b['id']]->link = 'nolink';else $a->item[$b['id']]->link ='link';
           	 $a->item[$b['id']]->name =  $b[name];
             $a->item[$b['id']]->id = $b[id];
             $a->item[$b['id']]->parent = $b['parent'];
             $a->item[$b['id']]->level = $b['level'];
             $a->item[$b['id']]->url = $config['server_url'].$b['url'];
             $a->item[$b['id']]->template = $b['template'];
             $a->item[$b['id']]->key = $b['key'];
             $a->item[$b['id']]->i = $i+1;
             $a->item[$b['id']]->visible = $b['visible'];
             $a->item[$b['id']]->class = '';//$i==0?"Первый раздел всего меню сайта":"Первый раздел ветки";
             $level = $b[level];
             $last_id = $b[id];
             $c[$b[level]] =& $a->item[$b['id']];
             $allid[$b['id']] = $b['id'];
           }else{
           	 if($control->cid==$b[id])
           	 {
             for ($l =0;$l<$b['level'];$l++)if($allid[$control->parents[$b[level]-$l]])$c[$b[level]-$l]->link = 'stronglink';
           	 $c[$b[level]-1]->item[$b['id']]->link = 'nolink';
           	 }else $c[$b[level]-1]->item[$b['id']]->link = 'link';
             $c[$b[level]-1]->item[$b['id']]->name =  $b[name];
             $c[$b[level]-1]->item[$b['id']]->id = $b[id];
             $c[$b[level]-1]->item[$b['id']]->parent = $b['parent'];
             $c[$b[level]-1]->item[$b['id']]->level = $b['level'];
             $c[$b[level]-1]->item[$b['id']]->url = $config['server_url'].$b['url'];
             $c[$b[level]-1]->item[$b['id']]->template = $b['template'];
             $c[$b[level]-1]->item[$b['id']]->key = $b['key'];
             $c[$b[level]-1]->item[$b['id']]->i = $i+1;
             $c[$b[level]-1]->item[$b['id']]->visible = $b['visible'];
             $c[$b[level]-1]->item[$b['id']]->class = "";//"Последний в своей ветке";
             if($level>$b[level])$c[$b[level]]->class = "class='open'";
             if($level==$b[level])$c[$level]->class = '';//Раздел не имеет вложений он не первый но и не последний
             if($level<$b[level])$c[$level]->class = "class='open'";//'"Этот раздел имеет вложение '.$c[$level]->class;
             $level = $b[level];
             $last_id = $b[id];
             $allid[$b['id']] = $b['id'];
             $c[$b[level]] =& $c[$b[level]-1]->item[$b['id']];
           }
           $i++;
         }
   return $a;
}
	function GetUrl($id)		{
		global $sql;

		$q = "SELECT url FROM prname_tree WHERE id = '$id'";
		$url = sql::one_record($q);
		return substr($url, 0, strlen($url)-1);

	}
	function GetParents($id)	{
		global $sql;

		$parents = array();

		$q = "SELECT left_key, right_key FROM prname_tree WHERE id = '$id' ";
		$str = sql::fetch_array(sql::query($q));
		$left_key = $str['left_key'];
		$right_key = $str['right_key'];

		$q = "SELECT id, `key`, level FROM prname_tree WHERE left_key <= '$left_key' AND right_key >= '$right_key' ORDER BY left_key ";
		$res2 = sql::query($q);
		$i = 0;
		while ($str2 = sql::fetch_array($res2))		{
			$parents[$i] = $str2['id'];
		$i++;
		}

		return $parents;
	}
	function GetNode($id, $depth=1000000, $type='full')	{
		global $sql;
		global $control;

        $rootID = $id;

		$q = "SELECT left_key, right_key, level FROM prname_tree WHERE id = '$id' ";
		$str = sql::fetch_array(sql::query($q));
		$left_key = $str['left_key'];
		$right_key = $str['right_key'];
		$level = $str['level'];


		$q = "  SELECT pr1.id as id, pr1.name as title, pr1.url as url, pr1.level as level, pr2.id as parent, pr2.name, pr2.level as parentlevel, pr1.template as template FROM prname_tree pr1, prname_tree pr2 WHERE pr1.right_key > $left_key AND pr1.left_key < $right_key AND pr1.level < '".($level + $depth + 1) ."' AND pr2.left_key <= pr1.left_key AND pr2.right_key >= pr1.right_key   AND pr1.visible = 1 ORDER BY pr1.left_key, pr1.sort, pr2.level  ";

        if ($type == 'formoder')
    		$q = "  SELECT pr1.id as id, pr1.name as title, pr1.url as url, pr1.level as level, pr2.id as parent, pr2.name, pr2.level as parentlevel, pr1.template as template FROM prname_tree pr1, prname_tree pr2 WHERE pr1.right_key > $left_key AND pr1.left_key < $right_key AND pr1.level < '".($level + $depth + 1) ."' AND pr2.left_key <= pr1.left_key AND pr2.right_key >= pr1.right_key ORDER BY pr1.left_key, pr1.sort, pr2.level  ";

		$res = sql::query($q);
		$arr = array();
		while ($str = sql::fetch_array($res))		{
			$id = $str['id'];
			$title = $str['title'];
			$parent = $str['parent'];
			$level = $str['level'];
			$template = $str['template'];
			$url = $str['url'];
			$parentlevel = $str['parentlevel'];
			if (!isset($arr[$id]['parents']))	{
				$arr[$id]['parents'] = array();
			}
			$arr[$id]['id'] = $id;
			$arr[$id]['level'] = $level;
			$arr[$id]['title'] = $title;
			$arr[$id]['template'] = $template;
			$arr[$id]['url'] = substr($url, 0, strlen($url)-1);
			array_push($arr[$id]['parents'], array('level'=>$parentlevel, 'parent'=>$parent));
		}

//		print_r($arr);


		if (count($arr) > 0)		{

			$contr_parents = $control->parents;
			unset($contr_parents[count($contr_parents)-1]);

			foreach ($arr as $one_arr)	{
				$level = $one_arr['level'];
				$link = 'link';
				if ($one_arr['id'] == $control->cid)	{
					$link = 'nolink';
					if ($control->bid > 0)		{
							$link = 'stronglink';
					}
				}

				if (count($contr_parents) > 0)	{
					foreach ($contr_parents as $one_parent)	{
						if ($one_parent == $one_arr['id'])	{
							$link = 'stronglink';
						}
					}
				}

				if (count($one_arr['parents']) > 0)	{

					$strok = '';
					foreach ($one_arr['parents'] as $parent)	{
						if ($type =='full')	{
							$strok .= 'item'.$parent[level].'['.$parent[parent].']->';
						}
						if (in_array($type, array('formap','formoder')))	{
							$strok .= 'item['.$parent[parent].']->';
						}

					}

					eval("\$".$strok."title = '".$one_arr['title'] . "'; ");
					eval("\$".$strok."url = '<!--base_url//-->".$one_arr['url'] . "'; ");
					eval("\$".$strok."link = '".$link . "'; ");
					eval("\$".$strok."level = '".$level . "'; ");
					eval("\$".$strok."id = '".$one_arr['id']. "'; ");
					eval("\$".$strok."template = '".$one_arr['template']. "'; ");

				}
			}

			if ($type == 'full')	{
				$page->items = $item0;
			}	

			if ($type == 'formap')	{
				$page->items = $item;
			}	

            if ($type == 'formoder')
            {
                return $item[$rootID];
            }            

			return $page->items[1];
		}

	}
	function GetList($id)	{
		global $sql;
		global $control;

		$q = "  SELECT id as id, name as title, url as url, level as level FROM prname_tree WHERE parent = '$id' AND visible = 1 ORDER BY sort";

		$res = sql::query($q);
		$arr = array();
		while ($str = sql::fetch_array($res))		{
			$id = $str['id'];
			$title = $str['title'];
			$level = $str['level'];
			$url = $str['url'];

			$arr[$id]['id'] = $id;
			$arr[$id]['level'] = $level;
			$arr[$id]['title'] = $title;
			$arr[$id]['url'] = substr($url, 0, strlen($url)-1);
		}



		if (count($arr) > 0)		{

			$strok = '';
			foreach ($arr as $one_arr)	{
				$level = $one_arr['level'];

				$strok = 'item['.$one_arr['id'].']->';


				eval("\$".$strok."title = '".$one_arr['title'] . "'; ");
				eval("\$".$strok."url = '<!--base_url//-->".$one_arr['url'] . "'; ");
//				eval("\$".$strok."link = '".$link . "'; ");
				eval("\$".$strok."level = '".$level . "'; ");

			}

			$page->items = $item;

			return $page->items;
		}

	}
// Новый нестед.
    function getparents_new($left_key,$right_key)
	{
		$res2 = sql::query("SELECT id, `key`, level FROM prname_tree WHERE left_key <= '$left_key' AND right_key >= '$right_key' ORDER BY left_key ");
		$i = 0;
		while ($str2 = sql::fetch_array($res2))
		{
			$parents[$i] = $str2['id'];
		    $i++;
		}
		return $parents;
	}


  }

?>