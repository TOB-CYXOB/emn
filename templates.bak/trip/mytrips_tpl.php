<?
global $_GTC;
?>

<?
	if (is_array($_GTC->trips))
	{
?>
		<ul>
<?
		foreach($_GTC->trips as $idx => $trip)
		{
?>
		<li><a href="/<?=$trip->Key?>"><?=$trip->Title?></a> (старт: <?=$trip->Start?>)</li>	

<?
		}
?>
		</ul>
<?
	}
	else
	{
?>
		<h3>Нет трипов с вашим участием.</h3>
<?
	}
?>