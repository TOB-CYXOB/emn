<? global $_GTC ?>
<? if (isset($_GTC->Key)) {?>

	<div class="transport-item transport-type-<?=$_GTC->TypeName?>" id="transport_<?=$_GTC->ID?>"><?}?>
		<div class="transport-type-t-<?=$_GTC->TypeName?>">
			<div class="transport-type-b-<?=$_GTC->TypeName?>">
				<div class="transport-item-header">
					<div class="transport-item-del">
						<? if ($_GTC->access->delete == 1) {?> <a title="Удалить" href="#" onclick="transportDelete(<?=$_GTC->TripID?>, <?=$_GTC->ID?>);return false;">удалить</a> <? } ?>
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
					<div class="transport-item-nicname">
						<?=$_GTC->User_nickname?>
					</div>
					<div class="transport-item-place-counter">
						<? if($_GTC->haveplace) {?>
							Есть <?=$_GTC->haveplace_str?>
						<? } else {?>
							Мест нет
						<? } ?>
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

		<? if ($pl->PassengerID == 0 && !$pl->access->nokeep) {?>
			<div class="transport-place-add" name="<?=$_GTC->User_nickname?>" plID="<?=$pl->ID?>">Занять место</div>
		<? } ?>

		<? if ($pl->PassengerID != 0 && ($pl->access->free || $pl->access->moderate)) {?>
			<div class="transport-place-del" onclick="placeUnKeep(<?=$_GTC->ID?>,<?=$pl->ID?>);"></div>
		<? } ?>
		
	</div>

	<!--
	<table>
	<tr><th>

	<tr><td>
		<? if (!isset($pl->access)) {?>
		<input type="text" value="<?=$pl->PlaceText?>" readonly="true" disabled="true">
		<? } else {?>
	
			<? if ($pl->PassengerID == 0 ) {?>
				<input type="text" id="place_<?=$pl->ID?>" placeholder="<?=$pl->PlaceText?>" />
			<? } else {?>
				<input type="text" id="place_<?=$pl->ID?>" value="<?=$pl->PlaceText?>" 
			<? if (!$pl->access->change && !$pl->access->moderate) {?>readonly="true" disabled="true" <? } ?>/>
			<? } ?>
	
			<? if ($pl->PassengerID == 0 && is_null($_GTC->access->nokeep)) {?>
				<input type="button" value="<=" onclick="placeKeep(<?=$_GTC->ID?>,<?=$pl->ID?>);" />
			<? } ?>
	
			<? if ($pl->PassengerID != 0 && ($pl->access->change || $pl->access->moderate)) {?>
				<input type="button" value="<-" onclick="placeSetText(<?=$_GTC->ID?>,<?=$pl->ID?>, $('#place_<?=$pl->ID?>').val());" />
			<? } ?>
	
			<? if ($pl->PassengerID != 0 && ($pl->access->free || $pl->access->moderate)) {?>
				<input type="button" value="x" onclick="placeUnKeep(<?=$_GTC->ID?>,<?=$pl->ID?>);" />
			<? } ?>
	
		<? } ?>
	</td></tr>
	</table>
	-->
<? } ?>
<? if (isset($_GTC->Key)) {?>
			</div>
		</div>
	</div>
<? } ?>