<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once '../includeAll.php';

class TestObra extends UnitTestCase {
	function setUp() {
		includeModule('sgo');
	}
	
    function testNewObra() {
    	$obra = new Obra();
    	$obra->load(0);
    	$this->assertEqual($obra, new Obra());
    }
}
?>