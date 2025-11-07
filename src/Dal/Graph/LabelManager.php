<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Uuid;
use Bucorel\Waf\Dal\Struct;

class LabelManager extends NodeManager{
	
	function fetchLabel( string $labelId, bool $asArray = false ){
		$sql = "select * from node_labels where label_id=$1";
		$r = $this->db->query( $sql, array( $labelId ) );
		while( $row = pg_fetch_assoc( $r ) ){
			if( $asArray ){
				return $row;
			}else{
				$label = new NodeLabel();
				$label->load( $row );
				return $label;
			}
		}
		
		return false;
	}
	
	function fetchLabels( string $nodeId ){
		$list = [];
		$sql = "select * from node_labels where node_id=$1";
		$r = $this->db->query( $sql, array( $nodeId ) );
		while( $row = pg_fetch_assoc( $r ) ){
			array_push( $list, $row );
		}
		
		return $list;
	}
	
	function insertLabel( NodeLabel $label ){
	
		if( $label->get( 'label_id' ) == '' ){
			$label->set( 'label_id', Uuid::v7() );
		}
		
		if( $label->get( 'node_id' ) == '' ){
			throw new \Exception( 'LABELMAN_NO_NODE_ID' );
		}
		
		if( $label->get( 'is_official' ) != Struct::BOOLEAN_TRUE ){
			$label->set( 'is_official', Struct::BOOLEAN_FALSE );
		}else{
			$this->unsetExistingOfficialLabel( $label->get( 'node_id' ), $label->get( 'language' ) );
		}
		
		$label->set( 'active', Struct::BOOLEAN_TRUE );
		
		$now = time();
		
		$sql = "insert into node_labels (
			label_id,
			node_id,
			language,
			label,
			is_official,
			active,
			c_tstamp,
			lu_tstamp
		) values (
			$1,
			$2,
			$3,
			$4,
			$5,
			$6,
			$7,
			$8
		)";
		
		return $this->db->query( $sql, array(
			$label->get( 'label_id' ),
			$label->get( 'node_id' ),
			$label->get( 'language' ),
			$label->get( 'label' ),
			$label->get( 'is_official' ),
			$label->get( 'active' ),
			$now,
			$now
		) );
	}
	
	function updateLabel( NodeLabel $label ){
	
		if( $label->get( 'label_id' ) == '' ){
			throw new \Exception( 'LABELMAN_NO_LABEL_ID' );
		}
		
		if( $label->get( 'node_id' ) == '' ){
			throw new \Exception( 'LABELMAN_NO_NODE_ID' );
		}
		
		if( $label->get( 'is_official' ) != Struct::BOOLEAN_TRUE ){
			$label->set( 'is_official', Struct::BOOLEAN_FALSE );
		}else{
			$this->unsetExistingOfficialLabel( $label->get( 'node_id' ), $label->get( 'language' ) );
		}
		
		$now = time();
		
		$sql = "update node_labels set
			label=$1,
			is_official=$2,
			active=$3,
			lu_tstamp=$4
			where
			label_id=$5";
		
		return $this->db->query( $sql, array(
			$label->get( 'language' ),
			$label->get( 'label' ),
			$label->get( 'is_official' ),
			$label->get( 'active' ),
			$now,
			$label->get('label_id')
		) );
	}

	function deleteLabel( string $labelId, bool $softDelete = true ){
		if( $label = $this->fetchLabel( $labelId ) ){
			if( $label->get('is_official') == 't' ){
				throw new \Exception( 'DELETE_ISSUE_OFFICAL_LABEL' );
			}
		}
		
		if( $softDelete ){
			$label->set('active', Struct::BOOLEAN_FALSE );
			return $this->updateLabel( $label );
		}
		
		
		$sql = "delete from node_labels where label_id=$1";
		$r = $this->db->query( $sql, array( $labelId ) );
		return pg_affected_rows( $r );
	}
	
	function unsetExistingOfficialLabel( string $nodeId, string $lang ){
		$sql = "update node_labels set is_official=FALSE where node_id=$1 and language=$2 and is_official=TRUE";
		return $this->db->query( $sql, array( $nodeId, $lang ) );
	}
}
?>
