<? global $_GTC;?>
	<li <? if (!strlen($_GTC->pagemode)) {?>class="selected"<? } ?>
			><? if (strlen($_GTC->pagemode)) {?><a href="/<?=$_GTC->Key?>"><? } ?>Транспорт<? if (strlen($_GTC->pagemode)) {?></a><? } ?>
		<?if ($_GTC->transport && count($_GTC->transport)) {?><span class="secondary_menu-counter"><?=count($_GTC->transport)?></span><? } ?>
	</li>

	<? if ($_GTC->pagemode == 'members') {?>
	<li class="selected">Участники<?if ($_GTC->users && count($_GTC->users)) {?><span class="secondary_menu-counter"><?=$_GTC->memberCount?></span><? } ?></li>
	<? } else { ?>
	<li><a href="/<?=$_GTC->Key?>/members">Участники</a><?if ($_GTC->users && count($_GTC->users)) {?><span class="secondary_menu-counter"><?=$_GTC->memberCount?></span><? } ?></li>
	<? } ?>

	<? if ($_GTC->pagemode == 'route') {?>
	<li class="selected">Маршрут</li>
	<? } else { ?>
	<li><a href="/<?=$_GTC->Key?>/route">Маршрут</a></li>
	<? } ?>

	<li class="trip_start">Отъезд <?=$_GTC->Start_str?></li>

