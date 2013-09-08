<? global $_GTC;?>

<!--/div>
	<div class="navbar navbar-static-top secondary-menu">
	  <div class="navbar-inner">
		<div class="container-fluid">
			<p class="navbar-text pull-left">
				<strong><?=$_GTC->Title?></strong>
			</p>
		  <ul class="nav">
			<li <? if ($_GTC->pagemode == '') echo 'class="active"';?>>
				<a href="/<?=$_GTC->Key?>">Транспорт 
					<?if ($_GTC->transport && count($_GTC->transport)) {?>
						<span class="badge"><?=count($_GTC->transport)?></span>
					<?}?>	
				</a>
			</li>
			<li <? if ($_GTC->pagemode == 'members') echo 'class="active"';?>>
				<a href="/<?=$_GTC->Key?>/members">Участники
					<?if ($_GTC->users && count($_GTC->users)) {?>
						<span class="badge"><?=$_GTC->memberCount?></span>
					<?}?>
				</a>
			</li>
			<li <? if ($_GTC->pagemode == 'route') echo 'class="active"';?>>
				<a href="/<?=$_GTC->Key?>/route">Маршрут</a>
			</li>
		  </ul>
            <p class="navbar-text pull-right">
					Отъезд <?=$_GTC->Start_str?>
			</p>		  
		</div>
	  </div>
	</div>
<div class="container-fluid container-trip" -->

<div class="content_wrapper">

	<h1 style="margin-bottom: 25px;"><?=$_GTC->Title?></h1>
	<ul class="nav nav-tabs">
		<li <? if ($_GTC->pagemode == '') echo 'class="active"';?>>
			<a href="/<?=$_GTC->Key?>">Транспорт 
				<?if ($_GTC->transport && count($_GTC->transport)) {?>
					<span class="badge <? if ($_GTC->pagemode != '') echo 'badge-info';?>"><?=count($_GTC->transport)?></span>
				<?}?>	
			</a>
		</li>
	<?if(false):?>
		<li <? if ($_GTC->pagemode == 'members') echo 'class="active"';?>>
			<a href="/<?=$_GTC->Key?>/members">Участники
				<?if ($_GTC->users && count($_GTC->users)) {?>
					<span class="badge"><?=$_GTC->memberCount?></span>
				<?}?>
			</a>
		</li>
	<?endif;?>
		<li <? if ($_GTC->pagemode == 'route') echo 'class="active"';?>>
			<a href="/<?=$_GTC->Key?>/route">Маршрут</a>
		</li>
		<li class="pull-right" style="margin-top: 9px;">		
			Отъезд <?=date("d.m.Y \в\ H:i",strtotime($_GTC->Start))?>			
		</li>
	</ul>
</div>

