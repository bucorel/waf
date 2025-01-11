<?php
    /**
     * @copyright Business Computing Research Laboratory <www.bucorel.com>
     * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
     */

    namespace Bucorel\Waf;

    abstract class RequestHandler extends JsonResponse{

        abstract function init();

        function handleRequest(){
            switch( $_SERVER[ 'REQUEST_METHOD' ] ){
                case 'GET':
                    $this->handleGetRequest();
                    break;
                case 'POST':
                    $this->handlePostRequest();
                    break;
                case 'DELETE':
					$this->handleDeleteRequest();
                    break;
                case 'PUT':
					$this->handlePutRequest();
                    break;
                default:
                    $this->showError( self::HTTP_STATUS_METHOD_NOT_ALLOWED, 'METHOD_NOT_ALLOWED' );
            }
        }

        function handleGetRequest(){
            $this->showError( self::HTTP_STATUS_METHOD_NOT_ALLOWED, 'METHOD_NOT_ALLOWED' );
        }

        function handlePostRequest(){
            $this->showError( self::HTTP_STATUS_METHOD_NOT_ALLOWED, 'METHOD_NOT_ALLOWED' );
        }

        function handleDeleteRequest(){
            $this->showError( self::HTTP_STATUS_METHOD_NOT_ALLOWED, 'METHOD_NOT_ALLOWED' );
        }

        function handlePutRequest(){
            $this->showError( self::HTTP_STATUS_METHOD_NOT_ALLOWED, 'METHOD_NOT_ALLOWED' );
        }
    }
?>