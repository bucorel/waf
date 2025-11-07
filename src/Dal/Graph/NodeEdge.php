<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Struct;

class NodeEdge extends Struct{
	
	function __construct(){
		$this->setField( 'edge_id', self::TYPE_STRING );		//uuid
		$this->setField( 'node_from', self::TYPE_STRING );		//uuid of node
		$this->setField( 'node_to', self::TYPE_STRING );		//uuid of node
		$this->setField( 'edge_type', self::TYPE_STRING );		//edge type - depends on implimentation
		$this->setField( 'c_tstamp', self::TYPE_INTEGER );		//creation timestamp
		$this->setField( 'lu_tstamp', self::TYPE_INTEGER );		//last update timestamp
		$this->setField( 'active', self::TYPE_STRING );			//edge importance/trust score
	}
}
?>
