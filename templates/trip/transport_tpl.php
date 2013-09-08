<? global $_GTC ?>
<? if (isset($_GTC->Key)) {?>

<div class="transport-item-wrapper">
	<div class="transport-item-out-header">
		<? if ($_GTC->access->delete == 1) {?>
			<div class="transport-item-del">
				<a title="Удалить" href="#" onclick="transportDelete(<?=$_GTC->TripID?>, <?=$_GTC->ID?>);return false;"><i class="icon-remove"></i>Удалить</a>
			</div>
		<? } ?>
		<div class="transport-item-nicname">
		<? if (strlen(trim($_GTC->User_webLink))) {?>
			<a href="<?=$_GTC->User_webLink?>" target="_blank"><?=$_GTC->User_nickname?></a>
		<? } else if (strlen(trim($_GTC->User_email))) {?>
			<a href="mailto:<?=$_GTC->User_email?>"><?=$_GTC->User_nickname?></a>
		<? } else {?>
			<?=$_GTC->User_nickname?>
		<? } ?>
		</div>
	</div>
	<div class="transport-item transport-type-<?=$_GTC->TypeName?>" id="transport_<?=$_GTC->ID?>"><?}?>
		<div class="transport-type-t-<?=$_GTC->TypeName?>">
			<div class="transport-type-b-<?=$_GTC->TypeName?>">
				<div class="transport-item-header">
					<div class="transport-item-place-counter">
						<? if($_GTC->haveplace) {?>
							<span class="label label-success">
								Есть <?=$_GTC->haveplace_str?>
							</span>
						<? } else {?>
							<span class="label label-important">
								Мест нет
							</span>
						<? } ?>
					</div>
					<div class="transport-item-name">
						<textarea class="
						transport-input-text
						<? if ($_GTC->access->moderate == 1) {?> transport-input-text-editable <? } ?>
						" 
						trID="<?=$_GTC->ID?>" id="ModelName_<?=$_GTC->ID?>"  
							<? if ($_GTC->access->moderate != 1) {?> disabled="true" <? } ?>
						><?=$_GTC->ModelName?></textarea>
					</div>
				</div>

<? foreach($_GTC->places as $idx => &$pl) {?>
	
	<div class="
		transport-place
		<? if ($pl->PassengerID == 0 && !$pl->access->nokeep) {?> place-free <? } ?>
		<? if ($pl->access->nokeep) {?> place-nokeep <? } ?>
	" 
		plID="<?=$pl->ID?>">
		<input class="
					transport-place-text
				<? if ($pl->access->change || ($pl->PassengerID == 0 && !$pl->access->nokeep)) {?>
					transport-place-text-editable
				<? } ?>
		
		" type="text" id="place_<?=$pl->ID?>" plID="<?=$pl->ID?>" trID="<?=$_GTC->ID?>"  value="<?=$pl->PlaceText?>" 
	<? if ( ($pl->PassengerID == 0 && $pl->access->nokeep) || ($pl->PassengerID != 0 && !$pl->access->change)) {?>
		readonly="true" disabled="true" 
	<? } ?> 
	
		placeholder="Есть место" />


		<? if ($pl->PassengerID != 0 && ($pl->access->free || $pl->access->moderate)) {?>
			<div class="transport-place-del" onclick="placeUnKeep(<?=$_GTC->ID?>,<?=$pl->ID?>);"></div>
		<? } ?>
		
	</div>

<? } ?>

<? if (isset($_GTC->Key)) {?>
			</div>
		</div>
	</div>
</div>
<? } ?>