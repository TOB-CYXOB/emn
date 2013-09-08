<?global $_GTC;?>
<div class="page-header">
	<h1><?=$_GTC->PageName?></h1>
</div>
<?if (is_array($_GTC->trips)){?>
		<?foreach($_GTC->trips as $idx => $trip) {?>
		<div class="row">
			<div class="span8">
				<p class="lead">
					<a href="/<?=$trip->Key?>"><?=$trip->Title?></a><br />
					<span class="muted"><small><?=date("d.m.Y, H:i",strtotime($trip->Start))?></small></span>
				</p>
			</div>
		</div>
		<?}?>
<?}else{?>
	<h3>Нет трипов с вашим участием.</h3>
<?}?>