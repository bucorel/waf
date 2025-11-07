<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Graph\Node;
use Bucorel\Waf\Dal\Graph\NodeAttributes;

class GraphManager extends EdgeManager{
	
	/**
	 * @param $node Node object
	 * @param $labels array containing NodeLabel objects, multiple labels at once can be inserted
	 * @param $attrib NodeAttribute object
	 */
	function addNodeWithLabelsAndAttributes( Node $node, array $labels, NodeAttributes $attrib, array $edges ){
		$this->db->begin();
		
		if( $nodeId = $this->insertNode( $node ) ){
		
			foreach( $labels as $label ){
				$label->set('node_id', $nodeId );
				$this->insertLabel( $label );
			}
		
			$attrib->set( 'node_id', $nodeId );
			$this->setAttributes( $attrib );
			
			foreach( $edges as $edge ){
				$this->addEdge( $edge );
			}
		}
		
		$this->db->commit();
		if( isset( $nodeId ) && $nodeId !== false ){
			return $nodeId;
		}else{
			return false;
		}
	}
	
	function fetchNode( string $nodeId, string $language ){
		$sql = "SELECT 
			n.node_id,
			n.node_type,
			nl.label AS official_label,
			nl.language,
			na.attributes
		FROM nodes n
		LEFT JOIN node_labels nl 
			ON nl.node_id = n.node_id
		   AND nl.language = $1
		   AND nl.is_official = true
		LEFT JOIN node_attributes na 
			ON na.node_id = n.node_id
		WHERE
			n.node_id = $2";
		
		$r = $this->db->query( $sql, array( $language, $nodeId ) );
		
		while( $row = pg_fetch_assoc( $r ) ){
			$row['attributes'] = json_decode($row['attributes'],true);
			return $row;
		}

		return false;
	}
	
	function getAncestors( string $nodeId, string $language='en' ){
		$list = array();
		
		$sql = "WITH RECURSIVE ancestors AS (
					SELECT 
					e.node_from AS ancestor_id,
					e.node_to AS child_id,
					1 AS depth
					FROM node_edges e
					WHERE e.node_to = $1	--node id
					AND e.edge_type = $2	--edge type
					AND e.active = true

					UNION ALL

					SELECT 
					e.node_from AS ancestor_id,
					a.child_id,
					a.depth + 1
					FROM node_edges e
					JOIN ancestors a ON e.node_to = a.ancestor_id
					WHERE e.edge_type = $2
					AND e.active = true
					)
					SELECT 
					n.node_id, nl.label, nl.language, nl.is_official, a.depth, na.attributes->>'iso2' as iso2,na.attributes->>'iso3' as iso3
					FROM ancestors a
					JOIN nodes n ON n.node_id = a.ancestor_id
					JOIN node_labels nl ON nl.node_id = n.node_id
					JOIN node_attributes na ON na.node_id=n.node_id
					WHERE nl.language = $3 AND nl.is_official = true
					ORDER BY a.depth DESC";
		$r = $this->db->query( $sql, array( $nodeId, 'parl', $language ) );
		while( $row = pg_fetch_assoc( $r ) ){
			array_push( $list, $row );
		}
		
		return $list;
	}
	
	function searchNodesByLabel( string $label ){
		$list = array();
		
		$sql = "select * from node_labels where label ilike $1";
		$r = $this->db->query( $sql, array( $label.'%') );
		while( $row = pg_fetch_assoc( $r ) ){
			array_push( $list, $row );
		}
		
		return $list;
	}
	
	function fetchFullNode( string $nodeId ){
		if( $node = $this->fetchNode( $nodeId, 'en' ) ){
			//if( $full ){
			$node['labels'] = $this->fetchLabels( $nodeId );
			$node['attributes'] = $this->getAttributes( $nodeId );
			//}
			return $node;
		}
		
		return false;
	}
}
?>
