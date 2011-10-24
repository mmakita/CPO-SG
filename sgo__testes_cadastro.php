<?php
	include 'includeAll.php';
	include 'classes/Teste.php';
	includeModule('sgo');
	
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	
	$testCases = array();
	
	$test = new Teste(count($testCases));
	$test->nome = 'Teste Cadastrar Obra 1 - Teste bem comportado';
	$test->versao = '1.1.0.0';
	$test->data = '12/08/2011';
	$test->casoUso = 'Cadastrar Obra';
	$test->preCond = '1. O usuario deve estar logado no sistema. <br /> 2. O sistema ja validou o formulario.';
	$test->posCond = '1. Os dados devem estar todos gravados no sistema.';
	
	$test->POSTvars = array(
						array("nome" => 'unOrgSolic', "val" => ''),
						array("nome" => 'nomeSolic', "val"  => ''),
						array("nome" => 'deptoSolic', "val" => ''),
						array("nome" => 'emailSolic', "val" => ''),
						array("nome" => 'ramalSolic', "val" => ''),
						array("nome" => 'ofir', "val"       => '297'),
						array("nome" => 'nome', "val"       => 'Climatização de Refeitório reservado a funcionários e docentes Inst. Matemática. Estatística e Comutação Científica - Depto de Matemática Discreta'),
						array("nome" => 'tipo', "val"       => 'ref'),
						array("nome" => 'dimensao', "val"   => '35'),
						array("nome" => 'dimensaoUn', "val" => 'm'),
						array("nome" => 'amianto', "val"    => '0'),
						array("nome" => 'ocupacao', "val"   => 'Sala de Convivência'),
						array("nome" => 'residuos', "val"   => 'Papéis/Pláticos'),
						array("nome" => 'pavimentos', "val" => '2'),
						array("nome" => 'elevador', "val"   => '0'),
						array("nome" => 'recursos', "val"   => '1'),
						array("nome" => 'montanteRec', "val"=> '570120'),
						array("nome" => 'origemRec', "val"  => 'Funcamp'),
						array("nome" => 'prazoRec', "val"   => '1/12/2011'),
						array("nome" => 'latObra', "val"    => '-41.6698441'),
						array("nome" => 'lngObra', "val"    => '-56.5666547'),
						array("nome" => 'saa', "val"        => '342')
					);
	$test->GETvars = array();
	$test->vars = array();
	
	$test->resEsperado = array("success" => true, "errorNo" => 0, "errorFeedback" => "");
	
	
	$test->prepara();
	$test->roda();
	/*ROTINA A SER TESTADA - INICIO*/
	$obra = new Obra();
	$res = $obra->saveNew();
	
	/*ROTINA A SER TESTADA - FIM*/
	$test->verificaResultado($res);
	
	$testCases[] = $test;
	
	//---------------------------------------------------
	
	$test = new Teste(count($testCases));
	$test->nome = 'Teste Cadastrar Obra 1 - Repeticao';
	$test->versao = '1.1.0.0';
	$test->data = '12/08/2011';
	$test->casoUso = 'Cadastrar Obra';
	$test->preCond = '1. O usuario deve estar logado no sistema. <br /> 2. O sistema ja validou o formulario.';
	$test->posCond = '1. Os dados devem estar todos gravados no sistema.';
	
	$test->POSTvars = array(
						array("nome" => 'unOrgSolic', "val" => ''),
						array("nome" => 'nomeSolic', "val"  => ''),
						array("nome" => 'deptoSolic', "val" => ''),
						array("nome" => 'emailSolic', "val" => ''),
						array("nome" => 'ramalSolic', "val" => ''),
						array("nome" => 'ofir', "val"       => '297'),
						array("nome" => 'nome', "val"       => 'Climatização de Refeitório reservado a funcionários e docentes Inst. Matemática. Estatística e Comutação Científica - Depto de Matemática Discreta'),
						array("nome" => 'tipo', "val"       => 'ref'),
						array("nome" => 'dimensao', "val"   => '35'),
						array("nome" => 'dimensaoUn', "val" => 'm'),
						array("nome" => 'amianto', "val"    => '0'),
						array("nome" => 'ocupacao', "val"   => 'Sala de Convivência'),
						array("nome" => 'residuos', "val"   => 'Papéis/Pláticos'),
						array("nome" => 'pavimentos', "val" => '2'),
						array("nome" => 'elevador', "val"   => '0'),
						array("nome" => 'recursos', "val"   => '1'),
						array("nome" => 'montanteRec', "val"=> '570120'),
						array("nome" => 'origemRec', "val"  => 'Funcamp'),
						array("nome" => 'prazoRec', "val"   => '1/12/2011'),
						array("nome" => 'latObra', "val"    => '-41.6698441'),
						array("nome" => 'lngObra', "val"    => '-56.5666547'),
						array("nome" => 'saa', "val"        => '342')
					);
	$test->GETvars = array();
	$test->vars = array();
	
	$test->resEsperado = array("success" => false, "errorNo" => 5, "errorFeedback" => "Erro ao inserir registro. Esta obra ja foi inserida anteriormente.");
	
	
	$test->prepara();
	$test->roda();
	/*ROTINA A SER RODADA- INICIO*/
	$obra = new Obra();
	$res = $obra->saveNew();
	
	/*ROTINA A SER TESTADA- FIM*/
	$test->verificaResultado($res);
	
	$testCases[] = $test;
	
	//-----------------------------------------------
	
	$test = new Teste(count($testCases));
	$test->nome = 'Teste Cadastrar Obra 2 - alguns campos em branco';
	$test->versao = '1.1.0.0';
	$test->data = '12/08/2011';
	$test->casoUso = 'Cadastrar Obra';
	$test->preCond = '1. O usuario deve estar logado no sistema. <br /> 2. O sistema ja validou o formulario.';
	$test->posCond = '1. Os dados devem estar todos gravados no sistema.';
	
	$test->POSTvars = array(
						array("nome" => 'unOrgSolic', "val" => ''),
						array("nome" => 'nomeSolic', "val"  => ''),
						array("nome" => 'deptoSolic', "val" => ''),
						array("nome" => 'emailSolic', "val" => ''),
						array("nome" => 'ramalSolic', "val" => ''),
						array("nome" => 'ofir', "val"        => '297'),
						array("nome" => 'nome', "val"        => 'Revitalização de estacionamento da BC'),
						array("nome" => 'tipo', "val"        => 'ref'),
						array("nome" => 'dimensao', "val"    => '35'),
						array("nome" => 'dimensaoUn', "val"  => 'm'),
						array("nome" => 'amianto', "val"     => '0'),
						array("nome" => 'ocupacao', "val"    => ''),
						array("nome" => 'residuos', "val"    => ''),
						array("nome" => 'pavimentos', "val"  => ''),
						array("nome" => 'elevador', "val"    => '0'),
						array("nome" => 'recursos', "val"    => '1'),
						array("nome" => 'montanteRec', "val" => '570120'),
						array("nome" => 'origemRec', "val"   => 'Funcamp'),
						array("nome" => 'prazoRec', "val"    => '1/12/2011'),
						array("nome" => 'latObra', "val"     => '-41.6698441'),
						array("nome" => 'lngObra', "val"     => '-56.5666547'),
						array("nome" => 'saa', "val"         => '342')
					);
	$test->GETvars = array();
	$test->vars = array();
	
	$test->resEsperado = array("success" => true, "errorNo" => 0, "errorFeedback" => "");;
	
	
	$test->prepara();
	$test->roda();
	/*ROTINA A SER RODADA- INICIO*/
	$obra = new Obra();
	$res = $obra->saveNew();
	
	/*ROTINA A SER TESTADA- FIM*/
	$test->verificaResultado($res);
	
	$testCases[] = $test;
	
	//-----------------------------------------------
	
	$test = new Teste(count($testCases));
	$test->nome = 'Teste Cadastrar Obra 3 - com recurso sem prazo nem origem definidos';
	$test->versao = '1.1.0.0';
	$test->data = '12/08/2011';
	$test->casoUso = 'Cadastrar Obra';
	$test->preCond = '1. O usuario deve estar logado no sistema. <br /> 2. O sistema ja validou o formulario.';
	$test->posCond = '1. Os dados devem estar todos gravados no sistema.';
	
	$test->POSTvars = array(
						array("nome" => 'unOrgSolic', "val" => ''),
						array("nome" => 'nomeSolic', "val"  => ''),
						array("nome" => 'deptoSolic', "val" => ''),
						array("nome" => 'emailSolic', "val" => ''),
						array("nome" => 'ramalSolic', "val" => ''),
						array("nome" => 'ofir', "val"       => '297'),
						array("nome" => 'nome', "val"       => 'Revitalização de estacionamento BC'),
						array("nome" => 'tipo', "val"       => 'ref'),
						array("nome" => 'dimensao', "val"   => '35'),
						array("nome" => 'dimensaoUn', "val" => 'm'),
						array("nome" => 'amianto', "val"    => '0'),
						array("nome" => 'ocupacao', "val"   => ''),
						array("nome" => 'residuos', "val"   => ''),
						array("nome" => 'pavimentos', "val" => ''),
						array("nome" => 'elevador', "val"   => '0'),
						array("nome" => 'recursos', "val"   => '1'),
						array("nome" => 'montanteRec', "val"=> '570120'),
						array("nome" => 'origemRec', "val"  => ''),
						array("nome" => 'prazoRec', "val"   => ''),
						array("nome" => 'latObra', "val"    => '-41.6698441'),
						array("nome" => 'lngObra', "val"    => '-56.5666547'),
						array("nome" => 'saa', "val"        => '')
					);
	$test->GETvars = array();
	$test->vars = array();
	
	$test->resEsperado = array("success" => true, "errorNo" => 0, "errorFeedback" => "");;
	
	
	$test->prepara();
	$test->roda();
	/*ROTINA A SER RODADA- INICIO*/
	$obra = new Obra();
	$res = $obra->saveNew();
	
	/*ROTINA A SER TESTADA- FIM*/
	$test->verificaResultado($res);
	
	$testCases[] = $test;
	
	//-----------------------------------------------
	
	$test = new Teste(count($testCases));
	$test->nome = 'Teste Cadastrar Obra 2 - todos os campos nao verificados pelo JS em branco';
	$test->versao = '1.1.0.0';
	$test->data = '12/08/2011';
	$test->casoUso = 'Cadastrar Obra';
	$test->preCond = '1. O usuario deve estar logado no sistema. <br /> 2. O sistema ja validou o formulario.';
	$test->posCond = '1. Os dados devem estar todos gravados no sistema.';
	
	$test->POSTvars = array(
						array("nome" => 'unOrgSolic', "val"  => ''),
						array("nome" => 'nomeSolic', "val"   => ''),
						array("nome" => 'deptoSolic', "val"  => ''),
						array("nome" => 'emailSolic', "val"  => ''),
						array("nome" => 'ramalSolic', "val"  => ''),
						array("nome" => 'ofir', "val"        => ''),
						array("nome" => 'nome', "val"        => 'Construção do IC 4'),
						array("nome" => 'tipo', "val"        => 'continuidade'),
						array("nome" => 'dimensao', "val"    => '35'),
						array("nome" => 'dimensaoUn', "val"  => ''),
						array("nome" => 'amianto', "val"     => ''),
						array("nome" => 'ocupacao', "val"    => ''),
						array("nome" => 'residuos', "val"    => ''),
						array("nome" => 'pavimentos', "val"  => ''),
						array("nome" => 'elevador', "val"    => ''),
						array("nome" => 'recursos', "val"    => ''),
						//array("nome" => 'montanteRec', "val" => ''),
						//array("nome" => 'origemRec', "val"   => ''),
						//array("nome" => 'prazoRec', "val"    => ''),
						array("nome" => 'latObra', "val"     => ''),
						array("nome" => 'lngObra', "val"     => ''),
						array("nome" => 'saa', "val"         => '')
					);
	$test->GETvars = array();
	$test->vars = array();
	
	$test->resEsperado = array("success" => true, "errorNo" => 0, "errorFeedback" => "");;
	
	
	$test->prepara();
	$test->roda();
	/*ROTINA A SER RODADA- INICIO*/
	$obra = new Obra();
	$res = $obra->saveNew();
	
	/*ROTINA A SER TESTADA- FIM*/
	$test->verificaResultado($res);
	
	$testCases[] = $test;
	
	//-----------------------------------------------
	
	$test = new Teste(count($testCases));
	$test->nome = 'Teste Cadastrar Obra 2 - campos com aspas e aspas simples';
	$test->versao = '1.1.0.0';
	$test->data = '12/08/2011';
	$test->casoUso = 'Cadastrar Obra';
	$test->preCond = '1. O usuario deve estar logado no sistema. <br /> 2. O sistema ja validou o formulario.';
	$test->posCond = '1. Os dados devem estar todos gravados no sistema.';
	
	$test->POSTvars = array(
						array("nome" => 'unOrgSolic', "val" => ''),
						array("nome" => 'nomeSolic', "val"  => ''),
						array("nome" => 'deptoSolic', "val" => ''),
						array("nome" => 'emailSolic', "val" => ''),
						array("nome" => 'ramalSolic', "val" => ''),
						array("nome" => 'ofir', "val"        => '125'),
						array("nome" => 'nome', "val"        => 'Construção da sala "Gleb Wataghin" do \'IFGW\' '),
						array("nome" => 'tipo', "val"        => 'continuidade'),
						array("nome" => 'dimensao', "val"    => '35'),
						array("nome" => 'dimensaoUn', "val"  => 'km'),
						array("nome" => 'amianto', "val"     => '0'),
						array("nome" => 'ocupacao', "val"    => ''),
						array("nome" => 'residuos', "val"    => ''),
						array("nome" => 'pavimentos', "val"  => ''),
						array("nome" => 'elevador', "val"    => ''),
						array("nome" => 'recursos', "val"    => '0'),
						//array("nome" => 'montanteRec', "val" => ''),
						//array("nome" => 'origemRec', "val"   => ''),
						//array("nome" => 'prazoRec', "val"    => ''),
						array("nome" => 'latObra', "val"     => '-26.889'),
						array("nome" => 'lngObra', "val"     => '-48.698'),
						array("nome" => 'saa', "val"         => '129')
					);
	$test->GETvars = array();
	$test->vars = array();
	
	$test->resEsperado = array("success" => true, "errorNo" => 0, "errorFeedback" => "");;
	
	
	$test->prepara();
	$test->roda();
	/*ROTINA A SER RODADA- INICIO*/
	$obra = new Obra();
	$res = $obra->saveNew();
	
	/*ROTINA A SER TESTADA- FIM*/
	$test->verificaResultado($res);
	
	$testCases[] = $test;
?>