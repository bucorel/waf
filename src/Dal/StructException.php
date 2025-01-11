<?php
	namespace Bucorel\Waf\Dal;
	
	class StructException extends \Exception{
		
		function getErrorLabel(){
			$m = explode( "\r\n", $this->getMessage() );
			return $m[0];
		}
		
		function getErrorFieldName(){
			$m = explode( "\r\n", $this->getMessage() );
			if( isset( $m[1] ) ){
				return $m[1];
			}else{
				return "";
			}
		}
	}
?>
