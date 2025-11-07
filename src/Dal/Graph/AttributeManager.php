<?php
namespace Bucorel\Waf\Dal\Graph;
use Bucorel\Waf\Dal\Uuid;
use Bucorel\Waf\Dal\Struct;
use Bucorel\Waf\Dal\Graph\NodeAttributes;

class AttributeManager extends LabelManager{
	
	//function setAttributes( string $nodeId, array $keyVals ){
	function setAttributes( NodeAttributes $attribs ){
		$sql = "INSERT INTO node_attributes (node_id, attributes)
					VALUES ($1, $2)
				ON CONFLICT (node_id) DO UPDATE
				SET
    			attributes = EXCLUDED.attributes";
    			
    	return $this->db->query( $sql, array( $attribs->get('node_id'), json_encode( $attribs->get('attributes') ) ) );
	}
	
	function getAttributes( string $nodeId ):array{
		$sql = "select attributes from node_attributes where node_id=$1";
		$r = $this->db->query( $sql, array( $nodeId ) );
		if( pg_num_rows( $r ) == 0 ){
			return [];
		}
		
		//nod_id is a unique primary key so it will only return one row
		while( $row = pg_fetch_assoc( $r ) ){
			if( !$attribs = @json_decode( $row['attributes'], true ) ){
				throw new \Exception( 'ERROR_BAD_JSON' );
			}
			
			return $attribs;
		}
	}
}
