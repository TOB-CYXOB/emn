<?
global $_GTC;
?>
<? if (!isset($_GTC->ajax)){?>

<!-- trip stat -->
<ul class="secondary_menu" id="trip_stat">
<?
				$t = clone($_GTC);
				echo $_GTC->__this->phptpl_include($t,$_GTC->__this->_tplDir().'tripstat_tpl.php');
?>
</ul>

<div class="content_wrapper" id="members_block">
<?}?>

	<input type="hidden" id="syncID" value="<?=$_GTC->syncID?>">
	<input type="hidden" id="tripID" value="<?=$_GTC->ID?>">
	<input type="hidden" id="tripKey" value="<?=$_GTC->Key?>">

<h1><?=$_GTC->Title?></h1>

<!-- блок описания инициатора трипа-->
<table>
<tr>
<? if (strlen($_GTC->User_avatar)) {?> <!-- avatar -->
<td><a href="<?=$_GTC->User_webLink?>" title="<?=$_GTC->User_nickname?>" target="_blank"><img src="<?=$_GTC->User_avatar?>" height="50"></a></td>
<? } ?>
<td>
	Организатор<br /> 
	<?if (strlen($_GTC->User_webLink)){?><a href="<?=$_GTC->User_webLink?>" target="_blank"><?}?><?=$_GTC->User_nickname?><?if (strlen($_GTC->User_webLink)){?></a><?}?>
	<?if (strlen($_GTC->User_email)){?><br /><font style="small"><?=$_GTC->User_email?></font><?}?>
</td>
</tr>
</table>
<!--  конец блока описания организатора -->

<!-- блок статистики -->
<? if ($_GTC->transport) { ?>
<h2>Статистика по трипу</h2>
<ul>                                                                                    
<li>Транспортных средств - <b><?=count($_GTC->transport)?></b></li>
<li>Участников - <b><?=$_GTC->memberCount?></b></li>
<li>Свободных мест - <b><?=$_GTC->haveplace?></b></li>
</ul>
<? } ?>
<!-- конец блока статистики -->

	<div>
	<? if ($_GTC->transport)
		{
			echo('<h2>Транспорт</h2>');

			foreach($_GTC->transport as $idx => &$t)
			{
	?>
	                    <div class="transport-info">
						<table>
						<tr>
						<td colspan="2">
						<h3><?=$t->ModelName?></h3>
						</td>
						</tr>
						<tr>
						<td><!-- инфа владельца машины -->

						<? if (strlen($t->User_avatar)) {?> <!-- avatar -->
						<a href="<?=$t->User_webLink?>" title="<?=$t->User_nickname?>" target="_blank"><img src="<?=$t->User_avatar?>" height="30"></a><br />
						<? } ?>
						<?if (strlen($t->User_webLink)){?><a href="<?=$t->User_webLink?>" target="_blank"><?}?><?=$t->User_nickname?><?if (strlen($t->User_webLink)){?></a><?}?>
						<?if (strlen($t->User_email)){?><br /><font style="small"><?=$t->User_email?></font><?}?>
						</td>
						<td>
						<ul>
						<? foreach($t->places as $idx => &$pl) {?>
							<? if ($pl->PassengerID !=0){?>

								<!-- если это сам владелец авто занял-->
								<? if ($pl->PassengerID == $t->UserID) {?>
	                    				<li><?=$pl->PlaceText?></li>
								<? } else { ?>

									<?if (strlen($pl->User_webLink) && $pl->User_nickname == $pl->PlaceText) {?>
										<li><a href="<?=$pl->User_webLink?>" target="_blank"><?=$pl->User_nickname?></a></li>
									<? } else if (strlen($pl->User_webLink)) {?>
										<li><?=$pl->PlaceText?> (<a href="<?=$pl->User_webLink?>" target="_blank"><?=$pl->User_nickname?></a>)</li>
									<? } else if (strlen($pl->User_email)) {?>
										<li><a href="mailto:<?=$pl->User_email?>" ><?=$pl->PlaceText?></a></li>
									<? } else {?>
										<li><?=$pl->PlaceText?></li>
									<? } ?>

								<? } ?>
							<? } ?>
						<? } ?>
						</ul>
						</td>
						</tr>
						</table>
						</div>
	<? 		} 
		}
	?>
	</div>


<? if (!isset($_GTC->ajax)){?>
</div>
<? } ?>