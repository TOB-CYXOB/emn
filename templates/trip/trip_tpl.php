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
<?
				$t = clone($_GTC);
				echo $_GTC->__this->phptpl_include($t,$_GTC->__this->_tplDir().'tripstat_tpl.php');
?>

	<!--p><?=$_GTC->Description?></p-->

      <div class="row-fluid">
		  <div class="span6">

		<? if (strlen($_GTC->user->userKey)) { //юзер авторизован ?>
				<a class="btn btn-success pull-left" onclick="$('#transportAddDiv').css('display','block');"><i class="icon-plus icon-white"></i> Добавить транспорт</a>
		<? } else { ?>
				<a class="btn btn-success pull-left loginza" href="http://loginza.ru/api/widget?token_url=<?=$_GTC->siteurl?>/<?=$_GTC->Key?>" ><i class="icon-plus icon-white"></i>Добавить транспорт</a>
		<? } ?>

				 <p class="pull-left" style="margin: 6px 0 0 20px">Пригласить друзей:</p>
<script type="text/javascript">(function() {
 if (window.pluso && typeof window.pluso.start == "function") return;
 var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
 s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
 s.src = d.location.protocol  + '//share.pluso.ru/pluso-like.js';
 var h=d[g]('head')[0] || d[g]('body')[0];
 h.appendChild(s);
})();</script>
<div style="margin:-2px 0 0 0" class="pluso" data-options="medium,square,line,horizontal,nocounter,theme=06" data-services="vkontakte,odnoklassniki,facebook,twitter,email" data-background="transparent"></div>

		  </div>
		  <div class="span6">
				<!--Ссылка на трип: http://estmesto.net/<?=$_GTC->Key?>-->
		  </div>
      </div>


<div class="content_wrapper">


	<div id="transportAddDiv" style="display:none; margin: 10px 0 10px 0;">
		<div class="well">
			Выберите тип транспорта:<br><select id="TransportType" onchange="transportAdd(<?=$_GTC->ID?>, this.value);$('#transportAddDiv').css('display','none');">
			<option value="">не выбран</option>
			<? foreach($_GTC->transportTypes as $tr) {?>
			<option value="<?=$tr->TypeID?>"><?=$tr->Title?> (<?=$tr->PlaceCountDef?>-местный)</option>
			<? } ?>
			</select>
		</div>
	</div>
		
		<div id="trip_<?=$_GTC->ID?>" style="margin:0 auto">
		<? } ?>
	
	<input type="hidden" id="syncID" value="<?=$_GTC->syncID?>">
	<input type="hidden" id="tripID" value="<?=$_GTC->ID?>">
	<input type="hidden" id="tripKey" value="<?=$_GTC->Key?>">
	
	<div class="trip-items">
	<? if ($_GTC->transport)
		{
			foreach($_GTC->transport as $idx => &$t)
			{
				$t->Key = $_GTC->Key;
				echo $_GTC->__this->phptpl_include($t,$_GTC->__this->_tplDir().'transport_tpl.php');
	 		} 
		
		} else {
	?>
			<p class="lead">Для начала добавьте транспорт</p>
	<?	
		}
	?>
	</div>
	
	<? if (!isset($_GTC->ajax)){?>
	</div>
	<? } ?>
</div>	