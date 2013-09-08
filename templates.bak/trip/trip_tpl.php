<? global $_GTC; ?>

<? if (!isset($_GTC->ajax)){?>
<script>

	tripSyncF = 0;

	placeSaveInterval = 0;

	currUpdObject = null;



	$(document).ready(function(){

	 //tripSyncF = setInterval('tripSync();', 15000);
     placeListener();


	});



</script>

<!-- trip stat -->
<ul class="secondary_menu" id="trip_stat">
<?
				$t = clone($_GTC);
				echo $_GTC->__this->phptpl_include($t,$_GTC->__this->_tplDir().'tripstat_tpl.php');
?>
</ul>
<div class="content_wrapper">
	<!--h3>Ссылка на трип: http://estmesto.net/<?=$_GTC->Key?></h3-->
	<!--p><?=$_GTC->Description?></p-->
	<input type="button" value="Добавить транспорт" onclick="$('#transportAddDiv').css('display','block');">
	<div id="debug"></div>
	<div id="transportAddDiv" style="display:none">
	Тип транспорта:<br><select id="TransportType" onchange="transportAdd(<?=$_GTC->ID?>, this.value);$('#transportAddDiv').css('display','none');">
	<option value="">---</option>
	<? foreach($_GTC->transportTypes as $tr) {?>
	<option value="<?=$tr->TypeID?>"><?=$tr->Title?> (<?=$tr->PlaceCountDef?>-местный)</option>
	<? } ?>
	</select>
	</div>
	
	<div id="trip_<?=$_GTC->ID?>" style="margin:0 auto">
	<? } ?>
	
	<input type="hidden" id="syncID" value="<?=$_GTC->syncID?>">
	<input type="hidden" id="tripID" value="<?=$_GTC->ID?>">
	<input type="hidden" id="tripKey" value="<?=$_GTC->Key?>">
	
	<div>
	<? if ($_GTC->transport)
		{
			foreach($_GTC->transport as $idx => &$t)
			{
				$t->Key = $_GTC->Key;
				echo $_GTC->__this->phptpl_include($t,$_GTC->__this->_tplDir().'transport_tpl.php');
	?>
	<? 		} 
		}
	?>
	</div>
	
	<? if (!isset($_GTC->ajax)){?>
	</div>
	<? } ?>
</div>	