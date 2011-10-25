<?php
/**
 * @version 0.1 11/2/2011 
 * @package geral
 * @author Mario Akita
 * @desc contem as variaveis de configuracao do sistema
 */

/**
 * caminho para o arquivo de template principal do sistema, nova janela (mini) e login
 * @var string
 */

 $conf["template"] = "templates/template.php";
 $conf["template_mini"] = "templates/template_mini.php";
 $conf["template_login"] = "templates/template_login.php";
 $conf["template_menu"] = "templates/template_menu.php";
 
 /**
  * tamanho das novas janelas (pop-ups) a serem abertas
  * @var int
  */
 $conf["newWindowHeight"] = 800;
 $conf["newWindowWidth"] = 600;
 
 /**
  * paginas de login e logout
  * @var string
  */
 $conf["login_page"] = "login.php";
 $conf["logout_page"] = "logout.php";
 
 /**
  * texto (HTML) padrao do rodape
  * @var string
  */
 $conf["title"] = "SiGPOD - CPO/Unicamp - ";
 $conf["footer"] = "2011. CPO/Inform&aacute;tica (v. 1.0.12 - 29/7/2011)";
 $conf["head"] = '';

 /**
  * variaveis de configuração do BD
  * @var string
  */
 
 $conf["DBLogin"] = "sgcpo_testes";
 $conf["DBPassword"] = "";
 $conf["DBhost"] = array('master' => 'engenheiro.cpo.unicamp.br', 'slave' => 'arquiteto.cpo.unicamp.br');
 $conf["DBport"] = 3306;
 $conf["DBTable"] = "sg_testes";
 
 $conf['debugMode'] = false;
 
 /**
  * @var Int
  */
 $conf['timezone'] = "Etc/GMT+3";
  
 ?>