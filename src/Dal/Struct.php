<?php
	/** 
	 * Author - Pushpendra Singh Thakur <thakur@bucorel.com>
	 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
	 */
	namespace Bucorel\Waf\Dal;
	
	class Struct{
		
		protected $fields = array();
		protected $data = array();
		
		const TYPE_INTEGER = 1;
		const TYPE_FLOAT = 2;
		const TYPE_BOOLEAN = 3;
		const TYPE_STRING = 4;
		const TYPE_STRUCT = 5;
		
		const BOOLEAN_TRUE = 't';
		const BOOLEAN_FALSE = 'f';
		const BOOLEAN_UNDEFINED = 'u';
		
		function setField( $name, $dataType, $multiValue = self::BOOLEAN_FALSE ){
			$this->fields[ $name ] = array( $dataType, $multiValue );
			if( $multiValue == self::BOOLEAN_TRUE ){
				$this->data[ $name ] = array();
			}else{
				$this->data[ $name ] = $this->getDefaultValue( $dataType );
			}
		}
		
		function getDefaultValue( $dataType ){
			switch( $dataType ){
				case self::TYPE_INTEGER:
				case self::TYPE_FLOAT:
					return 0;
				case self::TYPE_BOOLEAN:
					return self::BOOLEAN_UNDEFINED;
				case self::TYPE_STRUCT:
					return array();
				default:
					return "";
			}
		}
		
		function set( $fieldName, $value ){
			if( !array_key_exists( $fieldName, $this->fields ) ){
				throw new StructException( "UNKNOWN_FIELD\r\n".$fieldName );
			}
			
			if( $this->fields[ $fieldName ][1] == self::BOOLEAN_TRUE ){
				$this->setMultiValue( $fieldName, $value );
			}else{
				$this->setSingleValue( $fieldName, $value );
			}
		}
		
		private function setMultiValue( $fieldName, $value ){
			if( !is_array( $value ) ){
				throw new StructException( "ARRAY_EXPECTED\r\n".$fieldName );
			}
			
			try{
				foreach( $value as $v ){
					$this->validate( $this->fields[ $fieldName ][0], $v );
				}
			}catch( \Exception $e ){
				throw new StructException( $e->getMessage()."\r\n".$fieldName );
			}
			
			$this->data[ $fieldName ] = $value;
		}
		
		private function setSingleValue( $fieldName, $value ){
			try{
				$this->validate( $this->fields[ $fieldName ][0], $value );
			}catch( \Exception $e ){
				throw new StructException( $e->getMessage()."\r\n".$fieldName );
			}
			$this->data[ $fieldName ] = $value;
		}
		
		function get( $fieldName ){
			if( !array_key_exists( $fieldName, $this->fields ) ){
				throw new StructException( "UNKNOWN_FIELD\r\n".$fieldName );
			}
			
			return $this->data[ $fieldName ];
		}
		
		function load( array $data ){
			foreach( $this->fields as $k=>$v ){
				if( array_key_exists( $k, $data ) ){
					$this->set( $k, $data[ $k ] );
				}
			}
		}
		
		function dump(){
			return $this->data;
		}
		
		function getJson(){
			return json_encode( $this->data );
		}
		
		function validate( $dataType, $value ){
			switch( $dataType ){
				case self::TYPE_INTEGER:
					return self::validateInt( $value );
				case self::TYPE_FLOAT:
					return self::validateFloat( $value );
				case self::TYPE_BOOLEAN:
					return self::validateBoolean( $value );
				case self::TYPE_STRUCT:
					return self::validateStruct( $value );
				default:
					return true;
			}
		}
		
		public static function validateInt( $value ){
			if( filter_var( $value, FILTER_VALIDATE_INT ) === false ){
				throw new \Exception( "INVALID_INT" );
			}
			
			return true;
		}
		
		public static function validateFloat( $value ){
			if( filter_var( $value, FILTER_VALIDATE_FLOAT ) === false ){
				throw new \Exception( "INVALID_FLOAT" );
			}
			
			return true;
		}
		
		public static function validateBoolean( $value ){
			$avlOptions = array('t','f','u');
			if( !in_array( $value, $avlOptions ) ){
				throw new \Exception( "INVALID_BOOL" );
			}
			
			return true;
		}
		
		public static function validateStruct( $value ){
			if( !is_array( $value ) ){
				throw new \Exception( "INVALID_STRUCT" );
			}
			
			if( $value == array() ){
				return true;
			}
			
			$ks = array_keys( $value );

			foreach( $ks as $k ){
				if( !is_numeric( $k ) ){
					return true;
				}
			}
			
			throw new \Exception( "INVALID_STRUCT" );
		}
		
		public static function escape( $string ){
			return htmlentities( $string, ENT_QUOTES, 'UTF-8' );
		}
	}
?>
