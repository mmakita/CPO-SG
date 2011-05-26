<?php
include_once('../includeAll.php');
include_once('../classes/adLDAP/adLDAP.php');

$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);

$adldap = new adLDAP();
$userdata = $adldap->user_info('*',array('displayname','samAccountName','sn','GivenName','userPrincipalName','telephoneNumber','mail','title','department','description','initials','AccountDisabled','enabled'));

foreach ($userdata as $user) {
	if(isset($user['initials'][0])
	&& isset($user['samaccountname'][0])
	&& isset($user['givenname'][0]) 
	&& isset($user['sn'][0]) 
	&& isset($user['title'][0]) 
	&& isset($user['department'][0])
	&& isset($user['telephonenumber'][0])
	&& isset($user['description'][0])
	&& isset($user['mail'][0])){
		if (!count($bd->query("SELECT id FROM usuarios WHERE matr = ".$user['initials'][0]))){
			$sql = "INSERT INTO usuarios (matr,username,gid,nome,sobrenome,nomeCompl,cargo,area,ramal,email,descr) VALUES ("
				."'".$user['initials'][0]."',"
				."'".htmlentities($user['samaccountname'][0],ENT_QUOTES,'UTF-8')."',"
				."'0',"
				."'".htmlentities($user['givenname'][0],ENT_QUOTES,'UTF-8')."',"
				."'".htmlentities($user['sn'][0],ENT_QUOTES,'UTF-8')."',"
				."'".htmlentities($user['displayname'][0],ENT_QUOTES,'UTF-8')."',"
				."'".htmlentities($user['title'][0],ENT_QUOTES,'UTF-8')."',"
				."'".$area."',"
				."'".htmlentities($user['telephonenumber'][0],ENT_QUOTES,'UTF-8')."',"
				."'".$user['mail'][0]."',"
				."'".htmlentities($user['description'][0],ENT_QUOTES,'UTF-8')."'"
				.")";
			$bd->query($sql);
			print($sql.'<br />');
		}
	}
}
?>