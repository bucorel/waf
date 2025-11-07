<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Struct;
use Bucorel\Waf\Dal\Uuid;

class EdgeManager extends AttributeManager{
	
	function fetchEdge( string $edgeId, $asArray = false ){
		$sql = "select * from node_edge where edge_id=$1";
		$r = $this->db->query( $sql, array( $edgeId ) );
		while( $row = pg_fetch_assoc( $r ) ){
			if( $asArray ){
				$edge = new NodeEdge();
				$edge->load( $row );
				return $edge;
			}else{
				return $row;
			}
		}
	}
	
	function addEdge( NodeEdge $edge ){
		$now = time();
		$edge->set( 'edge_id', Uuid::v7() );
		$edge->set( 'active', Struct::BOOLEAN_TRUE );
		//$edge->set( 'c_tstamp', $now );
		//$edge->set( 'lu_tstamp', $now );
		
		$sql = "insert into node_edges (
					edge_id,
					node_from,
					node_to,
					edge_type,
					c_tstamp,
					lu_tstamp,
					active
				) values (
					$1,
					$2,
					$3,
					$4,
					$5,
					$6,
					$7
				)";
		$r = $this->db->query( $sql, array(
			$edge->get('edge_id'),
			$edge->get('node_from'),
			$edge->get('node_to'),
			$edge->get('edge_type'),
			$now,
			$now,
			$edge->get('active')
		) );
		
		return pg_affected_rows( $r );
	}
	
	function updateEdge( NodeEdge $edge ){
		$now = time();
		//$edge->set( 'lu_tstamp', $now );
		$sql = "update 
					node_edges 
				set
					node_from=$1,
					node_to=$2,
					edge_type=$3,
					lu_tstamp=$4,
					active=$5
				where 
					edge_id=$6";
		$r = $this->db->query( $sql, array(
			$edge->get('node_from'),
			$edge->get('node_to'),
			$edge->get('edge_type'),
			$now,
			$edge->get('active'),
			$edge->get('edge_id')
		) );
		
		return pg_affected_rows( $r );
	}
	
	function deleteEdge( string $edgeId, $softDelete=true ){
		if( $edge = $this->fetchEdge( $edgeId ) ){
			if( $softDelete ){
				$edge->set( 'active', Struct::BOOLEAN_FALSE );
				return $this->updateEdge( $edge );
			}else{
				$sql = "delete from node_edges where edge_id=$1";
				$r = $this->db->query( $sql, array( $edgeId ) );
				return pg_affected_rows( $r );
			}
		}
		
		return 0 ;
	}
}
?>
