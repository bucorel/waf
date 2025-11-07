<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Struct;

class Node extends Struct{
	
	function __construct(){
		$this->setField( 'node_id', self::TYPE_STRING );		//uuid
		$this->setField( 'node_type', self::TYPE_STRING );		//node type - depends on implimentation
		$this->setField( 'c_tstamp', self::TYPE_INTEGER );		//creation timestamp
		$this->setField( 'lu_tstamp', self::TYPE_INTEGER );		//last update timestamp
		$this->setField( 'active', self::TYPE_STRING );			//node status - depends on implimentation
	}
}
?>
