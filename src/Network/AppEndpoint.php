<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Network;
	use \Bucorel\Waf\Langpack\Langpack;
	
	class AppEndpoint extends RequestHandler{
	
		protected $allowedRoles = array( -1 );
		protected $lang = null;
		
		
		use \Bucorel\Waf\Network\AppResponse;
		
		function init() : void {
			$this->initSession();
			$this->initLanguage();
			$this->initLocationAndTimezone();
			$this->checkRole();
		}
		
		function initSession() : void {
			if( defined( 'SESSION_NAME' ) ){
				session_name( SESSION_NAME );
			}
			
			session_start();
		}
		
		function initLanguage() : void {
			if( !isset( $_SESSION['language'] ) ){
				$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				$_SESSION['language'] = in_array( $lang, AVAILABLE_LANGUAGES ) ? $lang : DEFAULT_LANGUAGE;
			}
			
			$this->lang = new Langpack( LANGPACK_PATH, $_SESSION['language'] );
		}
		
		function initLocationAndTimezone() : void {
		}
		
		function checkRole() : bool {
			//roles not defined
			if( count( $this->allowedRoles ) < 1 ){
				$this->showError( self::STATUS_INTERNAL_ERROR, 'UNDEFINED_AUTH' );
			}
			
			//controller is accessible to everyone
			if( $this->allowedRoles[0] == -1 ){
				return true;
			}
			
			//controller is accessible to only signedin users
			if( $this->allowedRoles[0] == 0 ){
				if( isset( $_SESSION['user'] ) ){
					return true;
				}
				
				$this->showError( self::STATUS_UNAUTHORIZED, 'UNAUTHORIZED' );
			}
			
			if( count( array_intersect( $_SESSION['user']['roles'], $this->allowedRoles ) ) > 0 ){
				return true;
			}
			
			$this->showError( self::STATUS_UNAUTHORIZED, 'UNAUTHORIZED' );
		}
	}
?>
