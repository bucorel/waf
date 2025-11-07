<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Struct;

class NodeLabel extends Struct{
	
	function __construct(){
		$this->setField( 'label_id', self::TYPE_STRING );		//uuid
		$this->setField( 'node_id', self::TYPE_STRING );		//node type - depends on implimentation
		$this->setField( 'language', self::TYPE_STRING );		//node attributes - depends on implimentation
		$this->setField( 'label', self::TYPE_STRING );
		$this->setField( 'is_official', self::TYPE_BOOLEAN );	//is official label for specified node and language
		$this->setField( 'c_tstamp', self::TYPE_INTEGER );		//creation timestamp
		$this->setField( 'lu_tstamp', self::TYPE_INTEGER );		//last update timestamp
		$this->setField( 'active', self::TYPE_STRING );
	}
}
?>
