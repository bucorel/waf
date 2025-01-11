<?php
	namespace Bucorel\Waf;
	
	class ApiClient{
		
		public $output = "";
		public $lastError = "";
		public $lastHttpStatus = "";
		protected $user = "";
		protected $password = "";
		protected $baseUrl = '';
		
		function __construct( $baseUrl, $user="", $password="" ){
			$this->baseUrl = $baseUrl;
			$this->user = $user;
			$this->password = $password;
		}
		
		function execPost( $url, array $postFields = array() ){
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $this->baseUrl.$url );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			
			if( $this->user != "" && $this->password != "" ){
				curl_setopt( $ch, CURLOPT_USERPWD, $this->user . ":" . $this->password );
			}
			
			curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
			$this->output = curl_exec( $ch );
			$this->lastHttpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			if( curl_errno( $ch ) ){
				$this->lastError = curl_error( $ch );
				return false;
			}
			
			return true;
		}
		
		function execGet( $url ){
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $this->baseUrl.$url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			if( $this->user != "" && $this->password != "" ){
				curl_setopt( $ch, CURLOPT_USERPWD, $this->user . ":" . $this->password );
			}
			
			curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
			$this->output = curl_exec( $ch );
			$this->lastHttpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			if( curl_errno( $ch ) ){
				$this->lastError = curl_error( $ch );
				return false;
			}
			
			return true;
		}
		
		function execDelete( $url ){
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $this->baseUrl.$url );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			if( $this->user != "" && $this->password != "" ){
				curl_setopt( $ch, CURLOPT_USERPWD, $this->user . ":" . $this->password );
			}
			
			curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
			$this->output = curl_exec( $ch );
			$this->lastHttpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			if( curl_errno( $ch ) ){
				$this->lastError = curl_error( $ch );
				return false;
			}
			
			return true;
		}
	}
?>
