<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Network;
	
	abstract class RequestHandler extends JsonResponse{
		abstract function init();
		
		function handleRequest() : void {
			switch( $_SERVER[ 'REQUEST_METHOD' ] ){
				case 'GET':
					$this->handleGetRequest();
					break;
				case 'POST':
					$this->handlePostRequest();
					break;
				case 'PUT':
					$this->handlePutRequest();
					break;
				case 'DELETE':
					$this->handleDeleteRequest();
					break;
				default:
					$this->showMethodNotAllowedError();
			}
		}
		
		function showMethodNotAllowedError(){
			$this->showError( self::STATUS_METHOD_NOT_ALLOWED, 'METHOD_NOT_ALLOWED' );
		}
		
		function handleGetRequest(){
			$this->showMethodNotAllowedError();
		}
		
		function handlePostRequest(){
			$this->showMethodNotAllowedError();
		}
		
		function handlePutRequest(){
			$this->showMethodNotAllowedError();
		}
		
		function handleDeleteRequest(){
			$this->showMethodNotAllowedError();
		}
	}
?>
