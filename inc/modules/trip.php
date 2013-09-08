<?                                                     
class trip extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
        'trip',
);
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
        'trip' => 'inner.html',
);
    }

    function __destruct()
    {
    }



	//=== служебные методы модуля

	function _rand_key($len)
	{
        $arr="1234567890abcdefghjklmnopqrstuvwxyz";
        for($i=0; $i < $len; $i++)
		{
            $idx=rand(0,35);
            $rkey .= $arr[$idx];
        }
        return $rkey;
    }

    //создание трипа
	//возвращает информацию по трипу
	function _createTrip($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!isset($params['user'])) return NULL; //требуется передавать юзера

		//создавать трип может только авторизованный юзер
		if (!strlen($params['user']->userKey)) return NULL;
		//генерим ключ для трипа
		$len = 5;
		$params['Key'] = trip::_rand_key($len);
		$q = sprintf("SELECT ID FROM prname_trip WHERE `Key` = '%s';", $params['Key']);
		$tID = $sql->one_record($q);
		while ($tID)
		{
			$len++;
			//проверка пограничного условия
			if ($len > 10)
			{
				return NULL;
			}

			$params['Key'] = trip::_rand_key($len);
			$q = sprintf("SELECT ID FROM prname_trip WHERE `Key` = '%s';", $params['Key']);
			$tID = $sql->one_record($q);
		}


		//заносим в базу
		$q = sprintf("INSERT INTO prname_trip SET 
			`Key` = '%s',
			`Start` = '%s',
			`Status` = %d,
			`Creator` = %d,
			`CreateDate` = NOW(),
			`Title` = '%s',
			`Description` = '%s',
			`Coords` = '%s';
		", 
			$params['Key'],
			sql::escape_string($params['Start']),
			intval($params['Status']),
			intval($params['user']->id),
			sql::escape_string(stripslashes($params['Title'])),
			sql::escape_string(stripslashes($params['Description'])),
			sql::escape_string(stripslashes($params['Coords']))
		 );

		$sql->query($q);

		$ID = $sql->insert_id();

		//сброс кеша списка моих трипов
		clear_cache(0, 'mytrips_'.intval($params['user']->id).'_');

		//возвращаем результат
		return trip::_getTrip(array("ID" => $ID));
	}





	function _changeTrip($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!isset($params['user'])) return NULL; //требуется передавать юзера

		//создавать трип может только авторизованный юзер
		if (!strlen($params['user']->userKey)) return NULL;

		//заносим в базу
		$q = sprintf("UPDATE prname_trip SET 
			`Start` = '%s',
			`Title` = '%s',
			`Description` = '%s',
			`Coords` = '%s'
			WHERE 
			ID = %d
;
		", 
			sql::escape_string($params['Start']),
			sql::escape_string(stripslashes($params['Title'])),
			sql::escape_string(stripslashes($params['Description'])),
			sql::escape_string(stripslashes($params['Coords'])),
			intval($params['ID'])
		 );

		$sql->query($q);

		//сброс кеша списка моих трипов
		clear_cache(0, 'mytrips_'.intval($params['user']->id).'_');

		//сброс кэша по трипу
		$_cn = sprintf("%s_%s_",'trip', intval($params['ID']));
		clear_cache(0, $_cn); 

		return true;
	}

	function _deleteTrip($params = array())
	{
      global $control;
      global $config;
      global $sql;
	}

	//возвращает объект с набором полной информации по трипу
	function _getTrip($params = array())
	{
		global $control;
      	global $config;
      	global $sql;
		global $dtimer;

		if (!intval($params['ID'])) return NULL;

		//попытка загрузить из кэша


		$_cn = sprintf("%s_%s_%s",'trip', intval($params['ID']), md5(serialize($params))); 
		$_cn2 = sprintf("%s_%s_clean",'trip', intval($params['ID'])); 
		$sobj = get_cache($_cn);


		if (!is_null($sobj))
		{
			$obj = unserialize($sobj);

			return $obj;
		}


		$where[] = sprintf("`ID` = %d", intval($params['ID']));

		$fields = "`ID`, `Key`, `Start`, `Status`, `Creator`, `CreateDate`, `Title`";

		if (intval($params['ext_data']))
			$fields .= ", `Description`, `Coords`";

		$q = sprintf("SELECT %s FROM prname_trip WHERE %s ;", $fields, implode(' AND ', $where));
		$trip = $sql->fetch_object($sql->query($q));

		if ($trip === false || is_null($trip)) return NULL; //не нашли такого

		/*
		$MyTransport = array();

		if (strlen($params['user']->userKey)) //юзер авторизован - вытаскиваем список его транспортов
			$MyTransport = trip::_getMyTransportIDs(array(
									"user" => $params["user"],
									"TripID" => $trip->ID
						));
		*/


		//пробуем запросить clean-трип (без персонализации прав)
		$sobj = get_cache($_cn2);
		$trip_cache = 0;
		if (!is_null($sobj) && strlen($sobj))
		{
			$trip = unserialize($sobj);
			$trip_cache = 1;
		}

		$trip->haveplace = 0;
		$trip->memberCount = 0;
		$UIDs = array();


		$UIDs[$trip->Creator] = $trip->Creator;


		//логика следующая - извлекаем все по трипу, формируем структуру, создаем кэш и уже после распределяем права

		//извлекаем транспорт
		$trip->transport = trip::_getTransport(array(
				"TripID" => $trip->ID
		));

		//извлекаем места этого трипа и распределяем их по транспортам
		$places = trip::_getPlaces(array(
				"TripID" => $trip->ID
			));


		if (is_array($trip->transport))
		{
			foreach($trip->transport as &$t)
			{
				//запоминаем ID владельца
				$UIDs[$t->UserID] = $t->UserID;
				$t->haveplace = 0;
			}
		}


		$trip->myPlacesCount = 0;

		if (is_array($places))
		foreach($places as &$pl) //расставляем места по транспортам
		{
			if (!isset($trip->transport[$pl->TransportID])) continue; //странная ситуация, игнорируем
				
			//устанавливаем место
			$trip->transport[$pl->TransportID]->places[$pl->ID] = clone($pl);

			//запоминаем ID пассажира
			$UIDs[$pl->PassengerID] = $pl->PassengerID;

			if ($pl->PassengerID == 0) //счетчики свободных мест
			{
				$trip->haveplace++;
				$trip->transport[$pl->TransportID]->haveplace++;
			}
			else
			{
				$trip->memberCount++;
			}
	
		}

		if ($trip->haveplace > 0)
		{
			$trip->haveplace_str = All::declOfNum($trip->haveplace, $config['LANG']['TRANSPORT_PLACES_COUNT_ARR'], true);	
		}

		//извлекаем детализацию по всем участникам данного трипа
		if (count($UIDs))
		{
			$q = sprintf("SELECT id, nickname, avatar,email, webLink FROM prname_users WHERE id IN (%s)", implode(',', $UIDs));
			$users = $sql->fetch_object_arr($sql->query($q), 'id');

			//проводим персонафикацию всех данных
			$trip->User_nickname = $users[$trip->Creator]->nickname;
			$trip->User_avatar = $users[$trip->Creator]->avatar;
			$trip->User_email = $users[$trip->Creator]->email;
			$trip->User_webLink = $users[$trip->Creator]->webLink;

			foreach($trip->transport as &$t)
			{
				$t->User_nickname = $users[$t->UserID]->nickname;
				$t->User_avatar = $users[$t->UserID]->avatar;
				$t->User_email = $users[$t->UserID]->email;
				$t->User_webLink = $users[$t->UserID]->webLink;

				if (isset($t->places))
				foreach($t->places as &$pl)
				{
					if ($pl->PassengerID > 0 )
					{	
						$trip->users[$pl->PassengerID] = clone($users[$pl->PassengerID]);
						$trip->users[$pl->PassengerID]->UserID = $pl->PassengerID;

						$pl->User_nickname = $users[$pl->PassengerID]->nickname;
						$pl->User_avatar = $users[$pl->PassengerID]->avatar;
						$pl->User_email = $users[$pl->PassengerID]->email;
						$pl->User_webLink = $users[$pl->PassengerID]->webLink;
					}
				}

				if ($t->haveplace > 0)
				{
					$t->haveplace_str = All::declOfNum($t->haveplace, $config['LANG']['TRANSPORT_PLACES_COUNT_ARR'], true);	
				}
				
			}
		 }


		//теперь объект можно сохранить в кэш
		if (!$trip_cache)
		{
			$strip = serialize($trip);
			$trip->syncID = md5($strip);
			unset($strip);

			set_cache($_cn2, serialize($trip));

			//генерируем syncID-файл
			$_cn_sync = $_cn2."_syncid_".$trip->syncID;
			set_cache($_cn_sync, $_cn_sync." ".date("Y-m-d H:i:s"));
		}

		//назначаем права
		if ($trip->Creator == $params['user']->id)
		{
			$trip->access->close = 1;
			$trip->access->change = 1;
		}
		
		if (is_array($trip->transport) && count($trip->transport))
		{
			//выставляем права
			foreach($trip->transport as &$t)
			{
				if ($t->UserID == $params["user"]->id)
				{
					$t->access->delete = 1; //удалять транспорт
					$t->access->free = 1; //освобождать места
					//$t->access->change = 1; //менять подпись для места
					$t->access->moderate = 1; //модерировать транспорт(описание) 
					$t->access->view = 1; //видеть
				}
				else if ($params["user"]->id == $trip->Creator) //если я - создатиель трипа, могу удалять транспорт
				{
					$t->access->delete = 1;   //удалить транспорт
					//$t->access->free = 1;
					$t->access->view = 1;
				}
				else
					$t->access->view = 1;

				if (is_array($t->places))
				foreach($t->places as &$pl)
				{
					//права на место наследуются от прав на транспорт
					if (isset($trip->transport[$pl->TransportID]->access))
						$trip->transport[$pl->TransportID]->places[$pl->ID]->access = 
									clone($trip->transport[$pl->TransportID]->access);

					//если пассажир - я, то имею право менять описание(текст) и освободить место
					if ($pl->PassengerID == $params['user']->id)
					{
						$trip->transport[$pl->TransportID]->places[$pl->ID]->access->change = 1;
						$trip->transport[$pl->TransportID]->places[$pl->ID]->access->free = 1;

						//тут же увеличиваем счетчик занятых мною мест в трипе
						$trip->myPlacesCount++;
					}
				}
			}

			//если я - неавторизованный, более 1 места я занять не могу!
			if (!strlen($params['user']->userKey) && $trip->myPlacesCount > 0)
			{
				$trip->access->nokeep = 1;
				foreach($trip->transport as &$t)
				{
					$t->access->nokeep = 1;
				}
			}
		}

		//форматируем строку с датой отъезда
		$trip->Start_str = date('d.m.Y, H:i', strtotime($trip->Start));

		set_cache($_cn, serialize($trip));
		

		return $trip;
	}

	// --- работа с транспортом
	function _createTransport($params = array())
	{
		global $control;
		global $config;
		global $sql;

		//создавать транспорт может только авторизованный юзер
		if (!strlen($params['user']->userKey)) return NULL;

		if (!intval($params['TypeID'])) return NULL;//обязательно должен быть указан тип
		if (!intval($params['TripID'])) return NULL;//обязательно должен быть указан трип


		$q = sprintf("SELECT * FROM prname_transport WHERE TypeID=%d;", intval($params['TypeID']));
		$tr = $sql->fetch_object($sql->query($q));

		$params['PlaceCount'] =	intval($params['PlaceCount']) ? intval($params['PlaceCount']) : $tr->PlaceCountDef;

		//создаем транспорт
		$q = sprintf("INSERT INTO prname_utransport SET 
				`TypeID` = %d,
				`TripID` = %d, 
				`UserID` = %d,
				`ModelName` = '%s',
				`RegNum` = '%s',
				`PlaceCount` = %d
		",
			intval($params['TypeID']),
			intval($params['TripID']),
			intval($params['user']->id),
			strlen($params['ModelName']) ? sql::escape_string(stripslashes($params['ModelName'])) : $tr->Title,
			sql::escape_string(stripslashes($params['RegNum'])),
			intval($params['PlaceCount'])
		);
		$sql->query($q);

		$ID = $sql->insert_id();

		//создаем места
		for ($i=0; $i < intval($params['PlaceCount']); $i++)
		{
			if ($i > 100) break;
			trip::_createPlace(array(
								"TripID" => $params['TripID'],
								"TransportID" => $ID,
								"PlaceText" => ""
			));
		}


		//сброс кэша по трипу
		$_cn = sprintf("%s_%s_",'trip', intval($params['TripID']));
		clear_cache(0, $_cn); 

		return trip::_getTransport(array("ID"=> $ID));                                                                  
	}

	//изменить настройки транспорта
	function _changeTransport($params = array())
	{
      global $control;
      global $config;
      global $sql;

		//проверка прав
		if (!strlen($params['user']->userKey)) return NULL;		
		if (!intval($params['ID'])) return NULL;		

		$trn = trip::_getTransport(array("ID" => intval($params['ID'])));

		if (!isset($trn[intval($params['ID'])])) return NULL; //такой транспорт не нашелся

		if ($params['user']->id != $trn[intval($params['ID'])]->UserID) return NULL; //не мой транспорт

		$tID = intval($params['ID']);

		//если число мест изменилось в меньшую сторону - удаляем лишнее, иначе добавляем места
		if (intval($params['PlaceCount']))
		{
			if (intval($params['PlaceCount']) > $trn[$tID]->PlaceCount)
			for($n = intval($params['PlaceCount']); $n <= $trn[$tID]->PlaceCount; $n++)
			{
				trip::_createPlace(array(
					"TripID" => $trn[$tID]->TripID,
					"TransportID" => $trn[$tID]->ID	
				));
			}
			else //удаление лишних мест
			if (intval($params['PlaceCount']) < $trn[$tID]->PlaceCount)

				trip::_delPlace(array(
					"TransportID" => $trn[$tID]->ID,
					"limit" => sprintf("LIMIT %d, 999", intval($params['PlaceCount'])) //удаляем все,что свыше нового кол-ва мест
				));
		}

		$setter = array();

		if (isset($params['TypeID']))
			$setter[] = sprintf("`TypeID` = %d", intval($params['TypeID']));

		if (isset($params['ModelName']))
			$setter[] = sprintf("`ModelName` = '%s'", sql::escape_string($params['ModelName']));

		if (isset($params['RegNum']))
			$setter[] = sprintf("`RegNum` = '%s'", sql::escape_string($params['RegNum']));

		if (intval($params['PlaceCount']))
			$setter[] = sprintf("`PlaceCount` = %d", intval($params['PlaceCount']));

		if (count($setter))
		{
			$q = sprintf("UPDATE prname_utransport SET %s WHERE `ID` = %d;", implode(',', $setter), $trn[$tID]->ID);
			$sql->query($q);
		}

		//сброс кэша по трипу
		$_cn = sprintf("%s_%s_",'trip', $trn[$tID]->TripID);
		clear_cache(0, $_cn); 


		return trip::_getTransport(array(
				"ID" => $tID
			));
	}

	function _delTransport($params = array())
	{
		global $control;
		global $config;
		global $sql;

		//проверка прав
		if (!strlen($params['user']->userKey)) return NULL;		
		if (!intval($params['ID'])) return NULL;		

		$trn = trip::_getTransport(array("ID" => intval($params['ID'])));

		if (!isset($trn[intval($params['ID'])])) return NULL; //такой транспорт не нашелся

		if ($params['user']->id != $trn[intval($params['ID'])]->UserID)
		{
			//возможно запрос на удаление от админа
			$trip = trip::_getTrip(array('ID' => $trn[intval($params['ID'])]->TripID));
			if ($trip->Creator != $params['user']->id)
			 return NULL; //не мой транспорт
		}

		$places = $this->_getPlaces(array("TransportID" => intval($params['ID'])));

		//сбрасываем кеш списка трипов для пассажиров
        //TODO по идее позже лучше сюда обработчик воткнуть для той же рассылки уведомлений
		if (is_array($places))
		foreach($places as $pl)
		{
			clear_cache(0, "mytrips_".$pl->PassengerID."_");
		}

		//удаляем места		
		trip::_delPlace(array("TransportID" => intval($params['ID'])));

		//удаляем транспорт
		$q = sprintf("DELETE FROM prname_utransport WHERE ID = %d;", intval($params['ID']));
		$sql->query($q);

		//сброс кэша по трипу
		$_cn = sprintf("%s_%s_",'trip', $trn[intval($params['ID'])]->TripID);
		clear_cache(0, $_cn); 

		return $trn[intval($params['ID'])]->TripID;
	}

	//получить структуру с полной информацией о транспорте
	function _getTransport($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (intval($params['TripID']))
			$where[] = sprintf("ut.`TripID` = %d", intval($params['TripID']));

		if (is_array($params['ID']))
			$where[] = sprintf("ut.`ID` IN  (%s)", implode(',', $params['ID']));
		else
		if (intval($params['ID']))
			$where[] = sprintf("ut.`ID` = %d", intval($params['ID']));

		$orderby = "ORDER BY ut.`ID` asc";



		if (intval($params['ext'])) //расширенный режим запроса
		{
			$q = sprintf("SELECT ut.*, t.TypeName, t.Title, t.PlaceCountDef FROM prname_utransport ut 
	LEFT JOIN prname_transport t ON ut.TypeID = t.TypeID WHERE %s ;", implode(' AND ', $where));
			$result = $sql->fetch_object_arr($sql->query($q), 'ID');
		}
		else  //простой режим
		{
			$q = sprintf("SELECT ut.*, t.TypeName FROM prname_utransport ut 
			LEFT JOIN prname_transport t ON ut.TypeID = t.TypeID WHERE %s %s;", implode(' AND ', $where), $orderby);
			$result = $sql->fetch_object_arr($sql->query($q), 'ID');
		}

		//режим запроса транспорта вместе с местами (только в режиме запроса конкретного транспорта)
		if (intval($params['places']) && intval($params['ID']) && is_object($result[intval($params['ID'])]))
		{
			$result[intval($params['ID'])]->places = $this->_getPlaces(array('TransportID' => intval($params['ID'])));
			
		}

		
		return $result;
	}


	//получить список трипов с моим участием
	function _getMyTrips($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!isset($params['user'])) return NULL;

		//попытка загрузить из кэша
		$_cn = sprintf("%s_%s_%s",'mytrips', intval($params['user']->id), md5(serialize($params))); 
		$sobj = get_cache($_cn);
		if (!is_null($sobj))
		{
			return unserialize($sobj);
		}

		$where[] = sprintf("`Creator` = %d", intval($params['user']->id));

		$tripIDS = array();

		//где я - участник		
		$q = sprintf("SELECT DISTINCT `TripID` FROM prname_places WHERE `PassengerID` = %d;",intval($params['user']->id));
		$res = $sql->query($q);
		while ($row = $sql->fetch_assoc($res))
		{
			$tripIDS[$row['TripID']] = $row['TripID'];
		}

		$sql->free_result($res);

		if (count($tripIDS))
		{
			$where[] = sprintf(" OR `ID` IN(%s)", implode(',',$tripIDS));
		}

		//где я - создатель + то,что нарыли
		$q = sprintf("SELECT * FROM prname_trip WHERE %s ORDER BY `CreateDate` DESC;", 
			implode(' ',$where) );

		$result = $sql->fetch_object_arr($sql->query($q), 'ID');

		$sobj = serialize($result);
		set_cache($_cn, $sobj);		

		return $result;
	}



	//получить список последних трипов (только админам)
	function _getLastTrips($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!isset($params['user'])) return NULL;
		if (!intval($params['user']->admin)) return NULL;

		//попытка загрузить из кэша
		$_cn = sprintf("%s_%s_%s",'lasttrips', intval($params['user']->id), md5(serialize($params))); 
		$sobj = get_cache($_cn);
		if (!is_null($sobj))
		{
			return unserialize($sobj);
		}

		//где я - создатель + то,что нарыли
		$q = sprintf("SELECT * FROM prname_trip ORDER BY ID DESC LIMIT 100;");
		$result = $sql->fetch_object_arr($sql->query($q), 'ID');

		$sobj = serialize($result);
		set_cache($_cn, $sobj);		

		return $result;
	}



	//получить массив транспортных средств юзера 
	function _getMyTransportIDs($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!isset($params['user'])) return array();


		$where[] = sprintf("`UserID` = %d", intval($params['user']->id));

		if (intval($params['TripID']))
			$where[] = sprintf("`TripID` = %d", intval($params['TripID']));

		$OrderBy ="";
		$Limit = "";

		if (isset($params['orderby']))
			$OrderBy = sprintf(" ORDER BY %s", sql::escape_string($params['orderby']));

		if (isset($params['limit']))
			$Limit = sprintf(" LIMIT %s", sql::escape_string($params['limit']));

		$q = sprintf("SELECT `ID` FROM prname_utransport WHERE %s %s %s", implode(' AND ', $where), $OrderBy, $Limit);
		
		$res = $sql->fetch_assoc_arr($sql->query($q), 'ID');
		if (is_array($res))
			foreach($res as $id)
				$result[$id['ID']] = $id['ID'];


		return $result;
	}

	//работа с местами

	//создать место	
	function _createPlace($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!intval($params['TripID'])) return NULL;
		if (!intval($params['TransportID'])) return NULL;

		$q = sprintf("INSERT INTO prname_places SET 
		`TripID`=%d, 
		`TransportID`=%d,
		`PassengerID`=0,
		`PlaceText` = '%s';", 
			intval($params['TripID']),
			intval($params['TransportID']),
			$sql->escape_string($params['PlaceText'])
		);		
		$sql->query($q);

		return $sql->insert_id();		
	}

	//изменить что-то для места
	function _changePlace($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (!intval($params['ID'])) return NULL;		

		$set = array();

		if (isset($params['PassengerID']))
			$set[] = sprintf("`PassengerID` = %d", intval($params['PassengerID']));

		if (isset($params['PlaceText']))
			$set[] = sprintf("`PlaceText` = '%s'", $sql->escape_string(stripslashes($params['PlaceText'])));

		$set[] = sprintf("ChangeTime=NOW()");

		$q = sprintf("UPDATE prname_places SET %s
		WHERE `ID` = %d;", 
			implode(',', $set),
			intval($params['ID'])
		);
		$sql->query($q);

	}

	//удалить место
	function _delPlace($params = array())
	{
		global $control;
		global $config;
		global $sql;

		if (isset($params['TransportID']))
		{
			$where[] = sprintf("`TransportID` = %d", intval($params['TransportID']));
		}		
	
		if (isset($params['ID']))
			$where[] = sprintf("`ID` = %d", intval($params['ID']));

		$q = sprintf("DELETE FROM prname_places WHERE %s %s;", implode(' AND ', $where), $params['limit']);
		$sql->query($q);

	}

	//структура с полной информацией о месте
	function _getPlaces($params = array())
	{
		global $control;
		global $config;
		global $sql;


		if (intval($params['TripID']))
			$where = sprintf("`TripID` = %d", intval($params['TripID']));

		if (is_array($params['TransportID']) && count($params['TransportID']))
			$where = sprintf("place.`TransportID` IN (%s)", implode(',', $params['TransportID']));
		else
		if (intval($params['TransportID']))
			$where = sprintf("place.`TransportID` = %d", intval($params['TransportID']));

		if (is_array($params['ID']) && count($params['ID']))
			$where = sprintf("place.`ID` IN (%s)", implode(',', $params['ID']));
		else
		if (intval($params['ID']))
			$where = sprintf("place.`ID` = %d", intval($params['ID']));

		

		if ($params['ext']) //расширенный режим информирования
			$q = sprintf("SELECT place.*, usr.nickname, usr.avatar FROM prname_places place 
LEFT JOIN prname_users usr ON place.PassengerID = usr.id 
WHERE %s ORDER BY place.`ID`;", $where);
		else
			$q = sprintf("SELECT place.* FROM prname_places place 
WHERE %s ORDER BY place.`ID`;", $where);

		$places = $sql->fetch_object_arr($sql->query($q), 'ID');

		return $places;
	}

	//============================================================================
	//=== конец служебных методов
	//============================================================================


	function showMain()
	{
      global $control;
      global $config;
      global $sql;

		
	  $html = $this->phptpl($page, $this->_tplDir().'main_tpl.php');
	  return $html;	
	}


	function createTrip()
	{
		global $control;
		global $config;
		global $sql;


		//создаватьтрип может только авторизованный юзверь
		if (!strlen($control->user->userKey))
		{
			header("Location: /");
			die();
		}

		if (isset($control->params['Title'])) //пришел запрос на создание трипа
		{

			$params = $control->params;
			$params['user'] = $control->user;

			//время в формате dd.mm.yyyy hh::mm - переводим
			$buf = explode(" ", $control->params['Start']);
			$dt = explode(".", $buf[0]);
			$params['Start'] = sprintf("%s.%s.%s %s", $dt[2], $dt[1],$dt[0], $buf[1]);

			$trip = $this->_createTrip($params);
			
			header("Location: /".$trip->Key);
			die();
		}
		else //страница создания трипа
		{
			$control->PageName = $config['LANG']['TRIP_CREATE_TITLE'];
		  	$html = $this->phptpl($page, $this->_tplDir().'tripCreate_tpl.php');
		}
		  return $html;	
	}

    //изменение информационных данных по трипу
	function editTrip()
	{
		global $control;
		global $config;
		global $sql;

		//менять трип может только авторизованный юзверь
		if (!strlen($control->user->userKey))
		{
			header("Location: /");
			die();
		}

		//требуется ИД трипа
		if (!strlen($control->params['tripKey']))
		{
			header("Location: /");
			die();
		}

		$q = sprintf("SELECT `ID` FROM prname_trip WHERE `Key`='%s';", $sql->escape_string($control->params['tripKey']));
		$tID = $sql->one_record($q);

		$trip = $this->_getTrip(array("user" => $control->user, "ID" => $tID));

		if (is_null($trip)) $control->error(404);  //такого трипа нет
		if (!intval($trip->access->change)) $control->error(403); //нет прав на изменение трипа

		if (isset($control->params['Title'])) //пришел запрос на создание трипа
		{

			$params = $control->params;
			$params['user'] = $control->user;
			$params['ID'] = $tID;

			//время в формате dd.mm.yyyy hh::mm - переводим
			$buf = explode(" ", $control->params['Start']);
			$dt = explode(".", $buf[0]);
			$params['Start'] = sprintf("%s.%s.%s %s", $dt[2], $dt[1],$dt[0], $buf[1]);

			$trip = $this->_changeTrip($params);
			header("Location: /".$sql->escape_string($control->params['tripKey']).'/route');
			die();
		}
	}




	function showTrip($tripKey)
	{
		global $control;
		global $config;
		global $sql;

		if ($control->action == "sync" && strlen($control->params['syncID']) && intval($control->params['tripID']))
		{
			//это запрос на наличие изменений в указанном трипе, смотрим в кеш-файл и если он не менялся возвращаем NULL
			$syncIDfile = sprintf("%s_cache/trip_%d_clean_syncid_%s", 
						$config['DOCUMENT_ROOT'], intval($control->params['tripID']),
						sql::escape_string($control->params['syncID']));
			if (is_file($syncIDfile))
			{
				echo 'nochange';
				die();
			}
		}

		$q = sprintf("SELECT `ID` FROM prname_trip WHERE `Key`='%s';", $sql->escape_string($tripKey));
		$tID = $sql->one_record($q);

		$trip = $this->_getTrip(array("user" => $control->user, "ID" => $tID));

		if (is_null($trip)) $control->error(404);

		if ($control->action == "sync") //sync-запрос
		{
			$trip->ajax = 1;
		}

		$control->PageName = $trip->Title;
		if ($trip->haveplace)
			$control->SiteName = $config['LANG']['SITENAME_HAVEPLACE'];
		else
			$control->SiteName = $config['LANG']['SITENAME_NOPLACE'];

		$control->CurrentTripUrl = sprintf("%s%s", $config['server_url'], $trip->Key);


		//если это не аякс-запрос - выводим перечень типов транпорта
		if (!intval($trip->ajax))
		{
			$q = sprintf("SELECT * FROM prname_transport;");
			$trip->transportTypes = $sql->fetch_object_arr($sql->query($q));
		} 

		$trip->user = $control->user;
		$trip->siteurl = sprintf("http://%s",$_SERVER['SERVER_NAME']);


		$html = $this->phptpl($trip, $this->_tplDir().'trip_tpl.php');

		if ($control->action == "sync") //sync-запрос, должны эхнуть и умереть
		{
			echo $html;
			die();
		}

		return $html;	
	}
	                                                                                  
	//пришла команда занять место
	function placeKeep()
	{
		global $control;
		global $config;
		global $sql;

		$transportID = intval($control->params['transportID']);
		$placeID = intval($control->params['placeID']);

		if (!intval($control->user->id)) return NULL;		

		$trn = trip::_getTransport(array("ID" => $transportID, "places" => 1));

		if (!isset($trn[$transportID])) return NULL; //такой транспорт не нашелся

		//выгребаем информацию о трипе
		$trip = trip::_getTrip(array(
				"ID" => $trn[$transportID]->TripID,
				"user" => $control->user
			));

		$keepFlag = 1; //флаг возможности занять место

		//если юзер неавторизован, он может занять только 1 место в трипе
		if (!strlen($control->user->userKey))
		{
			if ($trip->access->nokeep)
				$keepFlag = 0; 
		}	

		//если я пробую занять/отредактировать свое же место
		if ($trn[$transportID]->places[$placeID]->PassengerID == $control->user->id)
		{
			$keepFlag = 1; 
		}

	
		//если вписали "Я" - земняем на никнейм
		if (strlen(trim($control->user->nickname)) && in_array(trim($control->params['PlaceText']), array('я','Я')))
		{
			$control->params['PlaceText'] = $control->user->nickname;
		}


		if ($keepFlag && $trn[$transportID]->places[$placeID]->PassengerID == 0) //место пустое - можно занимать
		{
			$params['ID'] = $placeID;

			if ($control->params['PlaceText'] == '') //если текста нет - освобождаем место
				$params['PassengerID'] = 0;
			else
				$params['PassengerID'] = $control->user->id;

			$params['PlaceText'] = $control->params['PlaceText'];
		    trip::_changePlace($params);

			//очистка кеша
			clear_cache(0, 'trip_'.$trn[$transportID]->TripID.'_');

		}
		else if ($keepFlag && $trn[$transportID]->places[$placeID]->PassengerID == $control->user->id) //место занято мною - редактируем
		{
			$params['ID'] = $placeID;

			if ($control->params['PlaceText'] == '') //если текста нет - освобождаем место
				$params['PassengerID'] = 0;
			else
				$params['PassengerID'] = $control->user->id;

			$params['PlaceText'] = $control->params['PlaceText'];
		    trip::_changePlace($params);

			//очистка кеша
			clear_cache(0, 'trip_'.$trn[$transportID]->TripID.'_');
		}

		if (!intval($control->params['SAVE_ONLY']))
		{	
			//извлекаем все места этого транспорта
			$trip = trip::_getTrip(array(
				"ID" => $trn[$transportID]->TripID,
				"user" => $control->user
			));

			$html = $this->phptpl($trip->transport[$transportID], $this->_tplDir().'transport_tpl.php');
			echo $html;
		}

		die();
	}


	//пришла команда освободить место
	function placeUnKeep()
	{
		global $control;
		global $config;
		global $sql;

		$transportID = intval($control->params['transportID']);
		$placeID = intval($control->params['placeID']);

		if (!intval($control->user->id)) return NULL;		
		$trn = trip::_getTransport(array("ID" => $transportID, "places" => "1"));

		if (!isset($trn[$transportID])) die(); //такой транспорт не нашелся


		if ($trn[$transportID]->places[$placeID]->PassengerID == $control->user->id ||
			$trn[$transportID]->UserID == $control->user->id) //проверка прав
		{
			$params['ID'] = $placeID;
			$params['PassengerID'] = 0;
			$params['PlaceText'] = '';
		    trip::_changePlace($params);

			//очистка кеша
			clear_cache(0, 'trip_'.$trn[$transportID]->TripID.'_');
		}

		//извлекаем все места этого транспорта
		$trip = trip::_getTrip(array(
				"ID" => $trn[$transportID]->TripID,
				"user" => $control->user
			));



		$html = $this->phptpl($trip->transport[$transportID], $this->_tplDir().'transport_tpl.php');
		echo $html;	
		die();
	}


	//запрос статистики по трипу
	function showTripStat()
	{
		global $control;
		global $config;
		global $sql;

		if (strlen($control->params['syncID']) && intval($control->params['tripID']))
		{
			//это запрос на наличие изменений в указанном трипе, смотрим в кеш-файл и если он не менялся возвращаем NULL
			$syncIDfile = sprintf("%s_cache/trip_%d_clean_syncid_%s", 
						$config['DOCUMENT_ROOT'], intval($control->params['tripID']),
						sql::escape_string($control->params['syncID']));
			if (is_file($syncIDfile))
			{
				echo 'nochange';
				die();
			}
		}

		//извлекаем все места этого транспорта
		$trip = trip::_getTrip(array(
				"ID" => intval($control->params['tripID']),
				"user" => $control->user
			));


		$trip->pagemode = $control->params['pagemode'];

		$html = $this->phptpl($trip, $this->_tplDir().'tripstat_tpl.php');
		echo $html;	
		die();
	}

	//пришла команда установить текст
	function placeSetText()
	{
		global $control;
		global $config;
		global $sql;

		$transportID = intval($control->params['transportID']);
		$placeID = intval($control->params['placeID']);

		if (!intval($control->user->id)) return NULL;		
		$trn = trip::_getTransport(array("ID" => $transportID, "places" => 1));

		if (!isset($trn[$transportID])) die(); //такой транспорт не нашелся


		if ($trn[$transportID]->places[$placeID]->PassengerID == $control->user->id ||
			$trn[$transportID]->UserID == $control->user->id) //проверка прав
		{
			$params['ID'] = $placeID;
			$params['PlaceText'] = $control->params['PlaceText'];
		    trip::_changePlace($params);

			//очистка кеша
			clear_cache(0, 'trip_'.$trn[$transportID]->TripID.'_');
		}

		//извлекаем все места этого транспорта
		$trip = trip::_getTrip(array(
				"ID" => $trn[$transportID]->TripID,
				"user" => $control->user
			));



		$html = $this->phptpl($trip->transport[$transportID], $this->_tplDir().'transport_tpl.php');
		echo $html;	
		die();
	}

	//добавить транспорт
	function transportAdd()
	{
		global $control;
		global $config;
		global $sql;

		$TypeID = intval($control->params['typeID']);
		if (!$TypeID)
			$TypeID = 1;

		$this->_createTransport(array("user" => $control->user, 
				"TypeID" => $TypeID,
				"TripID" => intval($control->params['tripID']),
				"RegNum" => $control->user->nickname
			));

		$trip = trip::_getTrip(array(
				"ID" => intval($control->params['tripID']),
				"user" => $control->user
			));

		$trip->ajax = true;
	
		$html = $this->phptpl($trip, $this->_tplDir().'trip_tpl.php');
		echo $html;	
		die();
	}

	//удалить транспорт
	function transportDel()
	{
		global $control;
		global $config;
		global $sql;

		$tripID = $this->_delTransport(array("user" => $control->user, 
				"ID" => intval($control->params['ID'])
			));

		if (!is_null($tripID))
			$trip = trip::_getTrip(array(
				"ID" => intval($tripID),
				"user" => $control->user
			));

		$trip->ajax = true;
	
		$html = $this->phptpl($trip, $this->_tplDir().'trip_tpl.php');
		echo $html;	
		die();
	}


	//изменить опции транспорта
	function transportSetProperty()
	{
		global $control;
		global $config;
		global $sql;

		$ID = intval($control->params['ID']);
		$params[$_POST['property']] = $_POST['value'];

		$params["user"] = $control->user;
		$params["ID"] = $ID;


		$trn = $this->_changeTransport($params);

		$trip = trip::_getTrip(array(
				"ID" => $trn[$ID]->TripID,
				"user" => $control->user
			));


		$html = $this->phptpl($trip->transport[$ID], $this->_tplDir().'transport_tpl.php');
		echo $html;	
		die();

	}

	function getMyTrips()
	{
		global $control;
		global $config;
		global $sql;

		$params['user'] = $control->user;
		$page->trips = $this->_getMyTrips($params);

        $control->PageName = $config['LANG']['TRIP_MYTRIPS_TITLE'];
		$page->PageName = $control->PageName;

		$html = $this->phptpl($page, $this->_tplDir().'mytrips_tpl.php');
		return $html;
	}

	function getLastTrips()
	{
		global $control;
		global $config;
		global $sql;

		$params['user'] = $control->user;
		$page->trips = $this->_getLastTrips($params);

        $control->PageName = $config['LANG']['TRIP_LASTTRIPS_TITLE'];

		$page->PageName = $control->PageName;

		$html = $this->phptpl($page, $this->_tplDir().'mytrips_tpl.php');
		return $html;
	}
	

	function getXML($tripKey)
	{
		global $control;
		global $config;
		global $sql;

	    header("Content-Type:text/xml");
		include_once($config['DOCUMENT_ROOT'].'libs/obj2xml.php');

		$q = sprintf("SELECT `ID` FROM prname_trip WHERE `Key`='%s';", $sql->escape_string($tripKey));

		$trip = $this->_getTrip(array("user" => $control->user, "ID" => $sql->one_record($q)));

		if (!is_null($trip) && intval($control->params['TransportID'])) //запрос транспорта
		{
			$trn = $trip->transport[intval($control->params['TransportID'])];
			if (is_null($trn)) $trn->error->code=404;

			$converter=new Obj2xml("transport");
	    	echo $converter->toXml($trn);
			die();
			
		}

		if (is_null($trip)) $trip->error->code=404;

		$converter=new Obj2xml("trip");
	    echo $converter->toXml($trip);
		die();
	}



	function showTripMembers($tripKey)
	{
		global $control;
		global $config;
		global $sql;

		if (strlen($control->params['syncID']) && intval($control->params['tripID']) && intval($control->params['sync']))
		{
			//это запрос на наличие изменений в указанном трипе, смотрим в кеш-файл и если он не менялся возвращаем NULL
			$syncIDfile = sprintf("%s_cache/trip_%d_clean_syncid_%s", 
						$config['DOCUMENT_ROOT'], intval($control->params['tripID']),
						sql::escape_string($control->params['syncID']));
			if (is_file($syncIDfile))
			{
				echo 'nochange';
				die();
			}
		}

		$q = sprintf("SELECT `ID` FROM prname_trip WHERE `Key`='%s';", $sql->escape_string($tripKey));
		$tID = $sql->one_record($q);

		$trip = $this->_getTrip(array("user" => $control->user, "ID" => $tID));

		if (is_null($trip)) $control->error(404);

		if (intval($control->params['sync'])) //sync-запрос
		{
			$trip->ajax = 1;
		}

		$control->PageName = $trip->Title;
		if ($trip->haveplace)
			$control->SiteName = $config['LANG']['SITENAME_HAVEPLACE'];
		else
			$control->SiteName = $config['LANG']['SITENAME_NOPLACE'];

		$control->CurrentTripUrl = sprintf("%s%s", $config['server_url'], $trip->Key);


		$trip->pagemode = "members";


		$html = $this->phptpl($trip, $this->_tplDir().'members_tpl.php');

		 //sync-запрос
		if (intval($control->params['sync'])) //sync-запрос, должны эхнуть и умереть
		{
			echo $html;
			die();
		}

		return $html;	
	}


	function showTripDescription($tripKey)
	{
		global $control;
		global $config;
		global $sql;

		$q = sprintf("SELECT `ID` FROM prname_trip WHERE `Key`='%s';", $sql->escape_string($tripKey));
		$tID = $sql->one_record($q);

		$trip = $this->_getTrip(array("user" => $control->user, "ID" => $tID, "ext_data" => 1));

		if (is_null($trip)) $control->error(404);


		$control->PageName = $trip->Title;
		$control->CurrentTripUrl = sprintf("%s%s", $config['server_url'], $trip->Key);

		$trip->pagemode = "route";

		$html = $this->phptpl($trip, $this->_tplDir().'route_tpl.php');

		return $html;	
	}




    //базовый метод сайт-модуля
    function content($arParams=array())
    {
		global $control;
		global $config;
		global $sql;

		if ($control->action == "create")
		{
			return $this->createTrip();
		}

		if ($control->action == "edit")
		{
			return $this->editTrip();
		}


		if ($control->action == "placeKeep")
		{
			return $this->placeKeep();
		}

		if ($control->action == "placeUnKeep")
		{
			return $this->placeUnKeep();
		}

		if ($control->action == "placeSetText")
		{
			return $this->placeSetText();
		}

		if ($control->action == "transportAdd")
		{
			return $this->transportAdd();
		}

		if ($control->action == "transportDel")
		{
			return $this->transportDel();
		}

		if ($control->action == "transportSetProperty")
		{
			return $this->transportSetProperty();
		}

		if ($control->action == "my")
		{
			return $this->getMyTrips();
		}

		if ($control->action == "last")
		{
			return $this->getLastTrips();
		}


		if ($control->action == "xml")
		{
			return $this->getXML($control->key);
		}

		if ($control->action == "stat")
		{
			return $this->showTripStat();
		}

		if ($control->action == "members")
		{
			return $this->showTripMembers($control->key);
		}

		if ($control->action == "route")
		{
			return $this->showTripDescription($control->key);
		}


		if ($control->key != "trip")
		{
			return $this->showTrip($control->key);
		}

		return $this->showMain();
    }   
	
}

?>