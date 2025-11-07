<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\PgDb;
use Bucorel\Waf\Dal\Uuid;
use Bucorel\Waf\Dal\Struct;

class NodeManager{
	
	protected $db = null;
	
	function __construct( PgDb $db ){
		$this->db = $db;
	}
	
	function insertNode( Node $node ):string|false{
		if( $node->get('node_id') == '' ){
			$node->set( 'node_id', Uuid::v7() );
		}
		
		$now = time();
		
		$node->set( 'c_tstamp', $now );
		$node->set( 'lu_tstamp', $now );
		$node->set( 'active', Struct::BOOLEAN_TRUE );
		
		$sql = "insert into nodes ( node_id, node_type, c_tstamp, lu_tstamp, active ) values ( $1, $2, $3, $4, $5 )";
		$r = $this->db->query( $sql, array(
			$node->get('node_id'),
			$node->get('node_type'),
			$node->get('c_tstamp'),
			$node->get('lu_tstamp'),
			$node->get('active')
		) );
		
		if( pg_affected_rows( $r ) > 0 ){
			return $node->get('node_id');
		}else{
			return false;
		}
	}
	
	function updateNode( Node $node ){
		if( $node->get('node_id') == '' ){
			throw new \Exception( 'NODEMAN_NO_NODE_ID' );
		}
		
		$now = time();
		
		$node->set( 'lu_tstamp', $now );
		
		$sql = "update nodes set node_type=$1, lu_tstamp=$2, active=$3 where node_id=$4";
		return $this->db->query( $sql, array(
			$node->get('node_type'),
			$node->get('lu_tstamp'),
			$node->get('active'),
			$node->get('node_id')
		) );
	}
	
	function activateNode( string $nodeUuid ){
		$now = time();
		$sql = "update nodes set active=$1, lu_tstamp=$2 where node_id=$3";
		return $this->db->query( $sql, array('t',$now, $nodeUuid ) );
	}
	
	function deactivateNode( string $nodeUuid ){
		$now = time();
		$sql = "update nodes set active=$1, lu_tstamp=$2 where node_id=$3";
		return $this->db->query( $sql, array('f',$now, $nodeUuid ) );
	}
	
	function deleteNode( string $nodeUuid ){
		$sql = "delete from nodes where node_id=$1";
		return $this->db->query( $sql, array( $nodeUuid ) );
	}
	
	function fetchNodes( string $parentNodeUuid, string $nodeType, string $lang='en', string $searchPhrase='' ){
		if( $parentNodeUuid == "" ){
			$parentNodeUuid = null;
		}
		
		$list = array();
		
		if( $parentNodeUuid != null ){
			/*
			$sql = "SELECT 
				n.node_id,
				n.node_type,
				nl.label AS official_label,
				nl.language,
				e.edge_type
			FROM nodes n
			LEFT JOIN node_labels nl 
				ON nl.node_id = n.node_id
			   AND nl.language = $1
			   AND nl.is_official = true
			LEFT JOIN node_edges e 
				ON e.node_to = n.node_id
			   AND e.active = true
			   AND e.edge_type = $2
			WHERE n.active = true
			  AND
				  e.node_to IS NOT NULL AND e.node_from = $3  -- direct children
			  AND
			  	  nl.label ilike $4
			ORDER BY nl.label";
			*/
			
			$sql = "SELECT 
				n.node_id,
				n.node_type,
				nl.label AS official_label,
				nl.language,
				a.attributes
			FROM nodes n
			LEFT JOIN node_labels nl 
				ON nl.node_id = n.node_id
			   AND nl.language = $1
			   AND nl.is_official = true
			LEFT JOIN node_edges e 
				ON e.node_to = n.node_id
			   AND e.active = true
			   AND e.edge_type = $2
			LEFT JOIN node_attributes a
				ON a.node_id = n.node_id
			WHERE n.active = true
			  AND
				  e.node_to IS NOT NULL AND e.node_from = $3  -- direct children
			  AND
			  	  nl.label ilike $4
			ORDER BY nl.label";
			$r = $this->db->query( $sql, array( $lang, $nodeType, $parentNodeUuid, $searchPhrase.'%' ) );
		}else{
			/*
			$sql = "SELECT 
				n.node_id,
				n.node_type,
				nl.label AS official_label,
				nl.language,
				e.edge_type
			FROM nodes n
			LEFT JOIN node_labels nl 
				ON nl.node_id = n.node_id
			   AND nl.language = $1
			   AND nl.is_official = true
			LEFT JOIN node_edges e 
				ON e.node_to = n.node_id
			   AND e.active = true
			   AND e.edge_type = $2
			WHERE n.active = true
			  AND
				  e.node_from IS NULL AND e.node_to IS NULL  -- direct children
			  AND
			      nl.label ilike $3
			ORDER BY nl.label";
			*/
			
			$sql = "SELECT 
				n.node_id,
				n.node_type,
				nl.label AS official_label,
				nl.language,
				a.attributes
			FROM nodes n
			LEFT JOIN node_labels nl 
				ON nl.node_id = n.node_id
			   AND nl.language = $1
			   AND nl.is_official = true
			LEFT JOIN node_edges e 
				ON e.node_to = n.node_id
			   AND e.active = true
			   AND e.edge_type = $2
			LEFT JOIN node_attributes a
				ON a.node_id = n.node_id
			WHERE n.active = true
			  AND
				  e.node_from IS NULL AND e.node_to IS NULL  -- direct children
			  AND
			      nl.label ilike $3
			ORDER BY nl.label";
			$r = $this->db->query( $sql, array( $lang, $nodeType, $searchPhrase.'%' ) );
		}
		
		//$r = $this->db->query( $sql, array( $lang, $nodeType, $parentNodeUuid ) );
		while( $row = pg_fetch_assoc( $r ) ){
			$row['attributes'] = json_decode( $row['attributes'], true );
			array_push( $list, $row );
		}
		
		return $list;
	}
	
	/*
	function fetchNode( string $nodeId, bool $full=false ){
		//get node
		$sql = "select * from nodes where node_id=$1";
		$r = $this->query( $sql, array( $nodeId ) );
		
		if( pg_num_rows( $r ) == 0 ){
			return false;
		}
		
		while( $row = pg_fetch_assoc( $r ) ){
			return $row;
		}
	}
	*/
}
?>
