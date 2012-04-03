<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once '../includeAll.php';

Mock::generate('BD');

class TestObra extends UnitTestCase {
	function setUp() {
		includeModule('sgo');
	}
	
    function testNewObraSuccess() {
    	separador('<b>Testando classe Obra</b>');
    	separador('Teste 1: Getters e Setters');
    	//configurando Mock para o BD
    	$bd = new MockBD();
    	$bd->returns('query',array('0' => array('id' => '632','cod' => '010755-55-01','nome' => 'Reforma do pavilhão de acesso ao hospital','unOrg' => '02.00.00.00.00.00','nomeSolic' => '','deptoSolic' => '','emailSolic' => '','ramalSolic' => '','descricao' => '','caract' => '','tipo' => '','lat' => '','lng' => '','campus' => '','dimensao' => '855','dimensaoUn' => 'm3','responsavelProjID' =>'36', 'responsavelObraID' => '44','estadoID' => '7','ocupacao' => '','amianto' => '','residuos' => '','pavimentos' => '','elevador' => '','custo' => '','desc' => '','desc_img' => '','visivel' => '','sigla' => 'OUTRO')));
       	$obra = new Obra($bd);
       	   	
       	$obra->load(632, true);
    	
       	$this->assertEqual($obra->get('id'), 632);
       	$this->assertEqual($obra->get('codigo'), '010755-55-01');
    }
}
?>