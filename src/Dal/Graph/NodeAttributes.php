<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Struct;

class NodeAttributes extends Struct{
	
	function __construct(){
		$this->setField( 'node_id', self::TYPE_STRING );	//uuid of the node
		$this->setField( 'attributes', self::TYPE_KEYVAL );
	}
}
?>
