<?php
	namespace Bucorel\Waf\Language;
	
	class Langpack{
		
		protected $data = array();
		protected $language = 'en';
		protected $systemPath = '';
		
		function __construct( string $systemPath, string $language = 'en' ){
			$this->systemPath = $systemPath;
			$this->language = $language;
		}
		
		function load( string $module, string $file, string $useAs ) : void{
			$file = $this->systemPath.'assets/dynamic/langpacks/'.$this->language.'/'.$module.'/'.$file.'.php';
			if( file_exists( $file ) ){
				include $file;
				if( isset( $labels ) && is_array( $labels )  ){
					$this->data[ $useAs ] = $labels;
				}
			}
		}
		
		function get( string $module, string $key ):mixed{
			if( isset( $this->data[ $module ][ $key ] ) ){
				return $this->data[ $module ][ $key ];
			}else{
				return strtoupper($this->language.'-'.$module.'-'.$key);
			}
		}
		
		function set( string $module, string $key, mixed $value ):void{
			$this->data[ $module ][ $key ] = $value;
		}
		
		function dump( string $module ):array{
			if( isset( $this->data[ $module ] ) ){
				return $this->data[ $module ];
			}else{
				return array();
			}
		}

		function getSelectedLanguage():string{
			return $this->language;
		}
	}
?>
