<?
	include "../inc/var.php";
	include "../inc/libs/sql.php";
	include "../inc/class/metamodule.php";
	include "../inc/modules/user.php";

	$sql = new Sql();
	$sql->connect();

	$usr = new user();
	$usr->logout();

	$sql->close();

	die();
?>