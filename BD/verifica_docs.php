<?php
include '../includeAll.php';

$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);

$res = $bd->query("SELECT id FROM doc ORDER BY id ASC");

foreach ($res as $r) {
	print 'Testando documento '.$r['id'].'<br>';
	$doc = new Documento($r['id']);
	$doc->loadCampos();
}
?>