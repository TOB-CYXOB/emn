<? global $_GTC;?>
<!-- trip stat -->
<ul class="secondary_menu" id="trip_stat">
<?
				$t = clone($_GTC);
				echo $_GTC->__this->phptpl_include($t,$_GTC->__this->_tplDir().'tripstat_tpl.php');
?>
</ul>

<div class="content_wrapper" id="route_block">

	<input type="hidden" id="syncID" value="<?=$_GTC->syncID?>">
	<input type="hidden" id="tripID" value="<?=$_GTC->ID?>">
	<input type="hidden" id="tripKey" value="<?=$_GTC->Key?>">

<h1><?=$_GTC->Title?></h1>

<?=$_GTC->Description?>

</div>
