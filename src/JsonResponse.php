<?php
    /**
     * @copyright Business Computing Research Laboratory <www.bucorel.com>
     * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
     */

    namespace Bucorel\Waf;

    class JsonResponse{

        const HTTP_STATUS_OK = 200;
        const HTTP_STATUS_CREATED = 201;
        const HTTP_STATUS_ACCEPTED = 202;

        const HTTP_STATUS_BAD_REQUEST = 400;
        const HTTP_STATUS_UNAUTHORIZED = 401;   //user/password required
        const HTTP_STATUS_FORBIDDEN = 403;      //signed in but not enough privileges
        const HTTP_STATUS_NOT_FOUND = 404;
        const HTTP_STATUS_METHOD_NOT_ALLOWED = 405;
        const HTTP_STATUS_CONFLICT = 409;
        const HTTP_STATUS_GONE = 410;
        const HTTP_STATUS_TOO_MANY_REQUESTS = 429;

        const HTTP_STATUS_INTERNAL_ERROR = 500;

        protected $data = array();  //response

        function setMessage( string $message ) : void {
            $this->data['_mes'] = $message;
        }

        function setField( string $fieldName ) : void {
            $this->data['_fie'] = $fieldName;
        }

        function showOk( string $message, $httpStatusCode = self::HTTP_STATUS_OK ) : void {
            $this->setMessage( $message );
            $this->finish( $httpStatusCode );
        }

        function showError( int $httpStatusCode, string $message, string $field = "" ) : void {
            $this->setMessage( $message );
            $this->setField( $field );
            $this->finish( $httpStatusCode );
        }

        function appendData( array $row ) : void {
            if( !isset( $this->data[ '_dat' ] ) ){
                $this->data[ '_dat' ] = array();
            }

            array_push( $this->data[ '_dat' ], $row );
        }

		function showData( array $data ){
			$this->data = $data;
			$this->finish();
		}
		
        /** Tts - time taken by the server */
        function getTts() : string {
            return number_format( (microtime( true ) - $_SERVER[ 'REQUEST_TIME_FLOAT' ]), 3 );
        }

        function finish( int $httpStatusCode = self::HTTP_STATUS_OK ) : void {
            //set response status
            if( in_array( $httpStatusCode, array( 200, 201, 202 ) ) ){
                $this->data['_sta'] = 1;
            }else{
                $this->data['_sta'] = 0;
            }

            //set response headers
            http_response_code( $httpStatusCode );
            header( 'Content-Type: application/json' );

            //set TTS
            $this->data['_tts'] = $this->getTts();

            //json output
            echo json_encode( $this->data );
            exit;
        }
    }
?>
