<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once '../includeAll.php';
define('PRINT', true);


Mock::generate('BD');

class testEmpreendimento extends UnitTestCase{
	private $bd;
	
	function setUp() {
		$this->bd = new MockBD();
		includeModule('sgo');
		$_POST = array('nome' => 'Obra de Teste Ampliação do laboratório de engenharia genética', 
						'justificativa' => 'Com o crescimento do DEG, se fez necessária a construção de um novo prédio',
						'local' => 'Q23 próximo ao LLL','descricao' => 'Laboratório de Ensaios Genéticos e Pesquisa de Engenharia Genética',
						'solicNome' => 'Valéria Borges',
						'solicDepto' => 'Dep. Pesq. Genética',
						'solicEmail' => 'valeria@dpg.ib.unicamp.br',
						'solicRamal' => '45560',
						'unOrg' => '01.02.03.04.05.06');
	}
	
	function testEmpreendSettersGetters(){
		separador('<b>Testando classe Empreendimento</b>');
		if(constant('PRINT')) separador('Teste 1: Getters and Setters');
		
		$empr = new Empreendimento($this->bd);
		//ID
		$this->assertEqual($empr->set('id', 1), false);
		$this->assertEqual($empr->get('id'), 0);
		//NOME
		$this->assertEqual($empr->set('nome',  'teste nome'), true);
		$this->assertEqual($empr->get('nome'), 'teste nome');
		//JUST
		$this->assertEqual($empr->set('justificativa',  'Justificativa conforme lei n&deg; 333/09'), true);
		$this->assertEqual($empr->get('justificativa'), 'Justificativa conforme lei n&deg; 333/09');
		//LOCAL
		$this->assertEqual($empr->set('local',  'Quadra 21 pr&oacute;ximo ao terreno vago Num 23'), true);
		$this->assertEqual($empr->get('local'), 'Quadra 21 pr&oacute;ximo ao terreno vago Num 23');
		//DESCR
		$this->assertEqual($empr->set('descricao',  'Predio de concreto pr&eacute;-moldado 23cm, 2 andares e auditorio'), true);
		$this->assertEqual($empr->get('descricao'), 'Predio de concreto pr&eacute;-moldado 23cm, 2 andares e auditorio');
		//UNORG
		$this->assertEqual($empr->set('unOrg',  '01.02.03.04.05.06'), true);
		$this->assertEqual($empr->get('unOrg'), '01.02.03.04.05.06');
		//SOLIC
		$this->assertEqual($empr->set('solicitante',  array('nome' => 'Fulano de Tal', 'depto' => 'DES', 'email' => 'fulano@tal.de', 'ramal' => '445688')), true);
		$this->assertEqual($empr->get('solicitante'), array('nome' => 'Fulano de Tal', 'depto' => 'DES', 'email' => 'fulano@tal.de', 'ramal' => '445688'));
	}
	
	function testEmpreendGetVars(){
		if(constant('PRINT')) separador('Teste 2: GetVars');
		
		$empr = new Empreendimento($this->bd);
		
		$empr->getVars();
		$this->assertEqual($empr->get('nome'), 'Obra de Teste Amplia&ccedil;&atilde;o do laborat&oacute;rio de engenharia gen&eacute;tica');
		$this->assertEqual($empr->get('justificativa'), 'Com o crescimento do DEG, se fez necess&aacute;ria a constru&ccedil;&atilde;o de um novo pr&eacute;dio');
		$this->assertEqual($empr->get('local'), 'Q23 pr&oacute;ximo ao LLL');
		$this->assertEqual($empr->get('descricao'), 'Laborat&oacute;rio de Ensaios Gen&eacute;ticos e Pesquisa de Engenharia Gen&eacute;tica');
		$this->assertEqual($empr->get('solicitante'), array('nome' => 'Val&eacute;ria Borges', 'depto' => 'Dep. Pesq. Gen&eacute;tica', 'email' => 'valeria@dpg.ib.unicamp.br', 'ramal' => '45560'));
	}
	
	function testEmpreendNew(){
		if(constant('PRINT')) separador('teste 3: Salvar nova obra');
		
		$this->bd->returnsAt(0,'query', true);
		$this->bd->returnsAt(1,'query', array(array('id' => '8047')));
		
		
		$empr = new Empreendimento($this->bd);
		$empr->getVars();
		$this->assertEqual($empr->saveNew(), true);
		$this->assertEqual($empr->get('id'), 8047);
	}
	
	function testEmpreendLoad(){
		if(constant('PRINT')) separador('Teste 4: Carregando Obra');
		
		$this->bd->returnsAt(0,'query', array(
					array('nome' => 'Obra de Teste Leitura Bem Sucedida N&uacute;mero 1', 
						'justificativa' => 'Se voc&ecirc; est&aacute; vendo esse texto, a leitura foi bem sucedida',
						'local' => 'Verificar sintaxe da consulta SQL',
						'descricao' => 'Mock do banco de dados',
						'solicNome' => 'MOCK BD',
						'solicDepto' => 'DTST',
						'solicEmail' => 'testes@cpo.unicamp.br',
						'solicRamal' => '001122',
						'unOrg' => '00.01.02.03.04.05')));
		$this->bd->returnsAt(1,'query', array(
					array('id' => '00.01.02.03.04.05',
						'sigla' => 'TEST',
						'nome' => 'Unidade Ficticia para testes')));
		
		$empr = new Empreendimento($this->bd);
		$this->assertEqual($empr->load(4501), true);
		
		$this->assertEqual($empr->get('id'), 4501);
		$this->assertEqual($empr->get('nome'), 'Obra de Teste Leitura Bem Sucedida N&uacute;mero 1');
		$this->assertEqual($empr->get('justificativa'), 'Se voc&ecirc; est&aacute; vendo esse texto, a leitura foi bem sucedida');
		$this->assertEqual($empr->get('local'), 'Verificar sintaxe da consulta SQL');
		$this->assertEqual($empr->get('descricao'), 'Mock do banco de dados');
		$this->assertEqual($empr->get('solicitante'), array('nome' => 'MOCK BD' ,'depto' => 'DTST' ,'email' => 'testes@cpo.unicamp.br' ,'ramal' => '001122'));
		$this->assertEqual($empr->get('unOrg'), array('id' => '00.01.02.03.04.05', 'sigla' => 'TEST', 'nome' => 'Unidade Ficticia para testes', 'compl' => '00.01.02.03.04.05 - Unidade Ficticia para testes (TEST)'));
		//$this->assertEqual($empr->get(''), );
	}
	
	function testEmpreendSave(){
		if(constant('PRINT')) separador('Teste 5: Carrega, modifica e salva obra');
		
		$this->bd->returnsAt(0,'query', array(
					array('nome' => 'Obra de Teste Leitura Bem Sucedida N&uacute;mero 1', 
						'justificativa' => 'Se voc&ecirc; est&aacute; vendo esse texto, a leitura foi bem sucedida',
						'local' => 'Verificar sintaxe da consulta SQL',
						'descricao' => 'Mock do banco de dados',
						'solicNome' => 'MOCK BD',
						'solicDepto' => 'DTST',
						'solicEmail' => 'testes@cpo.unicamp.br',
						'solicRamal' => '001122',
						'unOrg' => '00.01.02.03.04.05')));
		$this->bd->returnsAt(1,'query', array(
					array('id' => '00.01.02.03.04.05',
						'sigla' => 'TEST',
						'nome' => 'Unidade Ficticia para testes')));		
		$this->bd->returnsAt(2,'query', true);			
		$this->bd->returnsAt(3,'query', array(
					array('nome' => 'Obra de Teste UPDATE bem sucedido', 
						'justificativa' => 'Se voc&ecirc; est&aacute; vendo esse texto, a leitura foi bem sucedida',
						'local' => 'Verificar sintaxe da consulta SQL',
						'descricao' => 'Mock do banco de dados',
						'solicNome' => 'MOCK BD',
						'solicDepto' => 'DTST',
						'solicEmail' => 'testes@cpo.unicamp.br',
						'solicRamal' => '001122',
						'unOrg' => '00.01.02.03.04.05')));
		$this->bd->returnsAt(4,'query', array(
					array('id' => '00.01.02.03.04.05',
						'sigla' => 'TEST',
						'nome' => 'Unidade Ficticia para testes')));
		
		$empr = new Empreendimento($this->bd);
		$this->assertEqual($empr->load(4580), true);
		
		$this->assertEqual($empr->set('nome', 'Obra de Teste UPDATE bem sucedido'), true);
		$this->assertEqual($empr->get('nome'), 'Obra de Teste UPDATE bem sucedido');
		$this->assertEqual($empr->save(), true);
		
		$this->assertEqual($empr->load(4580), true);
		$this->assertEqual($empr->get('nome'), 'Obra de Teste UPDATE bem sucedido');
	}
	
	function testEmpreendERROload(){
		if(constant('PRINT')) separador('Teste 6: Erro de BD ao carregar obra');
		
		$this->bd->returns('query', false);
		
		$empr = new Empreendimento($this->bd);
		$this->assertEqual($empr->load(0), false);
		$this->assertEqual($empr->get('nome'), '');
		$this->assertEqual($empr->get('unOrg'), array('compl' => '', 'id' => '', 'nome' => '', 'sigla' => ''));
		
		$this->assertEqual($empr->load(1), false);
		$this->assertEqual($empr->get('nome'), '');
		$this->assertEqual($empr->get('unOrg'), array('compl' => '', 'id' => '', 'nome' => '', 'sigla' => ''));
		
	}
	
	function testEmpreendERROsavenew(){
		if(constant('PRINT')) separador('Teste 7: Erro de BD ao inserir obra');
		
		$this->bd->returnsAt(0,'query', false);
		$this->bd->returnsAt(1,'query', false);
		$this->bd->returnsAt(2,'query', true);
		$this->bd->returnsAt(3,'query', false);
		
		$empr = new Empreendimento($this->bd);
		$empr->getVars();
		$this->assertEqual($empr->saveNew(), false);
		$this->assertEqual($empr->get('id'), 0);
		
		$this->assertEqual($empr->saveNew(), false);
		$this->assertEqual($empr->get('id'), 0);
		$this->assertEqual($empr->get('nome'), htmlentities('Obra de Teste Ampliação do laboratório de engenharia genética'));
	}
	
	function testEmpreendERROsave(){
		if(constant('PRINT')) separador('Teste 8: Erro de BD ao salvar obra');
		
		$this->bd->returnsAt(0,'query', array(
					array('nome' => 'Obra de Teste Leitura Bem Sucedida N&uacute;mero 1', 
						'justificativa' => 'Se voc&ecirc; est&aacute; vendo esse texto, a leitura foi bem sucedida',
						'local' => 'Verificar sintaxe da consulta SQL',
						'descricao' => 'Mock do banco de dados',
						'solicNome' => 'MOCK BD',
						'solicDepto' => 'DTST',
						'solicEmail' => 'testes@cpo.unicamp.br',
						'solicRamal' => '001122',
						'unOrg' => '00.01.02.03.04.05')));
		$this->bd->returnsAt(1,'query', array(
					array('id' => '00.01.02.03.04.05',
						'sigla' => 'TEST',
						'nome' => 'Unidade Ficticia para testes')));		
		$this->bd->returnsAt(2,'query', false);
		
		$empr = new Empreendimento($this->bd);
		$this->assertEqual($empr->load(4580), true);
		
		$this->assertEqual($empr->set('nome', 'Obra de Teste UPDATE bem sucedido'), true);
		$this->assertEqual($empr->get('nome'), 'Obra de Teste UPDATE bem sucedido');
		$this->assertEqual($empr->save(), false);
	}
	
	
	function testEmpreendGetRec() {
		if(constant('PRINT')) separador('Teste 9: Leitura de Recursos');
		
		$this->bd->returnsAt(0,'query', array(
					array('nome' => 'Obra de Teste Leitura Bem Sucedida N&uacute;mero 1', 
						'justificativa' => 'Se voc&ecirc; est&aacute; vendo esse texto, a leitura foi bem sucedida',
						'local' => 'Verificar sintaxe da consulta SQL',
						'descricao' => 'Mock do banco de dados',
						'solicNome' => 'MOCK BD',
						'solicDepto' => 'DTST',
						'solicEmail' => 'testes@cpo.unicamp.br',
						'solicRamal' => '001122',
						'unOrg' => '00.01.02.03.04.05')));
		$this->bd->returnsAt(1,'query', array(
					array('id' => '00.01.02.03.04.05',
						'sigla' => 'TEST',
						'nome' => 'Unidade Ficticia para testes')));
		$this->bd->returnsAt(2,'query', Array());
		$this->bd->returnsAt(3,'query', array(
					array('id' => '12')));
		$this->bd->returnsAt(4,'query', array(
					array('obraID' => 450,
					'empreendID' => 54,
					'montante' => 778500.50, 
					'origem' => 'Unicamp/Reitoria',
					'prazo' => '',
					'tipo' => 'c')));
		
		$empr = new Empreendimento($this->bd);
		$this->assertEqual($empr->getRecursos(), false);//falha ao consultar recursos de empreend 0
		$this->assertEqual($empr->load(9), true);
		$this->assertEqual($empr->get('id'), 9);
		$this->assertEqual($empr->getRecursos(), false);//falha de BD ou Array vazio
		$this->assertEqual($empr->getRecursos(), true);//
		$this->assertEqual(getVal($empr->get('recursos'), 0)->montante, 778500.50);
	}
	
	function testEmpreendGetObra() {
		if(constant('PRINT')) separador('Teste 10: Leitura de Obras');
		
			$this->bd->returnsAt(0,'query', array(
					array('nome' => 'Obra de Teste Leitura Bem Sucedida N&uacute;mero 1', 
						'justificativa' => 'Se voc&ecirc; est&aacute; vendo esse texto, a leitura foi bem sucedida',
						'local' => 'Verificar sintaxe da consulta SQL',
						'descricao' => 'Mock do banco de dados',
						'solicNome' => 'MOCK BD',
						'solicDepto' => 'DTST',
						'solicEmail' => 'testes@cpo.unicamp.br',
						'solicRamal' => '001122',
						'unOrg' => '00.10.02.03.04.05')));
		$this->bd->returnsAt(1,'query', array(
					array('id' => '00.10.02.03.04.05',
						'sigla' => 'TEST',
						'nome' => 'Unidade Ficticia para testes')));
		$this->bd->returnsAt(2,'query', Array());
		$this->bd->returnsAt(3,'query', array(
					array('id' => '10010')));
		$this->bd->returnsAt(4,'query', array(
					array('id' => 10010,
						'nome' => 'Obra 1 do Empreendimento 10',
						'cod' => 'XXYYZZ-AA-BB',
						'lat' => '-25.2568',
						'lng' => '-55.9999',
						'dimensao' => 588,
						'dimensaoUn' => 'm3',
						'pavimentos' => '',
						'caract' => 'ref',
						'tipo' => 'pred',
						'amianto' => '',
						'ocupacao' => '',
						'residuos' => '',
						'elevador' => 0,
						'desc_img' => '',
						'estadoID' => 1,
						'campus' => 'cps',
						'responsavelProjID' => 15,
						'responsavelObraID' => 12,
						'visivel' => '1')));
		$this->bd->returnsAt(4,'query', array(
					array('id' => 10010,
						'nome' => 'Obra 1 do Empreendimento 10',
						'cod' => 'XXYYZZ-AA-BB',
						'lat' => '-25.2568',
						'lng' => '-55.9999',
						'dimensao' => 588,
						'dimensaoUn' => 'm3',
						'pavimentos' => '',
						'caract' => 'ref',
						'tipo' => 'pred',
						'amianto' => '',
						'ocupacao' => '',
						'residuos' => '',
						'elevador' => 0,
						'desc_img' => '',
						'estadoID' => 1,
						'campus' => 'cps',
						'responsavelProjID' => 15,
						'responsavelObraID' => 12,
						'visivel' => '1')));
		$this->bd->returnsAt(5,'query', array(
					array('nome' => 'Reforma')));
		$this->bd->returnsAt(6,'query', array(
					array('nome' => 'Predial')));
		$this->bd->returnsAt(7,'query', array(
					array('nome' => 'Em Obras')));
		$this->bd->returnsAt(8,'query', array(
					array()));
		$this->bd->returnsAt(9,'query', array(
					array()));			
					
		$empr = new Empreendimento($this->bd);
		$this->assertEqual($empr->getObras(), false);//falha ao consultar recursos de empreend 0
		$this->assertEqual($empr->load(10,true), true);
		$this->assertEqual($empr->getObras(), false);//falha de BD ou Array vazio
		$this->assertEqual($empr->getObras(), true);
		$this->assertEqual(getVal($empr->get('obras'), 0)->nome, 'Obra 1 do Empreendimento 10');
		$this->assertEqual(getVal(getVal($empr->get('obras'), 0)->caract, 'label'), 'Reforma');
		
		
	}
	
}

?>