<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Langpack;
	
	class Langpack{
		
		protected $data = array();
		protected $path = '';
		protected $language = 'en';
		
		function __construct( string $dataPath, string $language = 'en' ){
			$this->language = $language;
			$this->path = $dataPath.$language.'/';
		}
		
		function load( string $module, string $file, string $useAs ) : void{
			$file = $this->path.$module.'/'.$file.'.php';
			if( file_exists( $file ) ){
				include $file;
				if( isset( $labels ) && is_array( $labels )  ){
					$this->data[ $useAs ] = $labels;
				}
			}
		}
		
		function get( $module, $key ){
			if( isset( $this->data[ $module ][ $key ] ) ){
				return $this->data[ $module ][ $key ];
			}else{
				return strtoupper($this->language.'-'.$module.'-'.$key);
			}
		}
		
		function set( $module, $ey, $value ){
			$this->data[ $module ][ $key ] = $value;
		}
	}
?>
