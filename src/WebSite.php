<?php
	namespace Bucorel\Waf;
	
	class WebSite extends Router{
		
		function start(){
			$this->initSession();
			$this->initLanguage();
			$this->showPage();
		}
		
		function initSession(){
			if( defined('SESSION_SAVE_HANDLER') && defined('SESSION_SAVE_PATH') ){
				ini_set('session.save_handler', SESSION_SAVE_HANDLER );
				ini_set('session.save_path', SESSION_SAVE_PATH );
			}
			
			if( defined( 'SESSION_NAME' ) ){
				session_name( SESSION_NAME );
			}
			
			session_start();
		}

        function initLanguage(){
            //use browser specified language if language is not set
			if( !isset( $_SESSION['language'] ) ){
				$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				$_SESSION['language'] = in_array( $lang, AVAILABLE_LANGUAGES ) ? $lang : DEFAULT_LANGUAGE;
			}
        }
		
		function showPage(){
			$route = $this->getRoute();
			$fullPath = SYSTEM_PATH.'assets/templates/pages/'.$_SESSION['language'].'/'.$route.'.html';
			if( !file_exists( $fullPath ) ){
				header( 'HTTP/1.1 404 Not Found' );
				echo 'Not Found'.$fullPath;
				exit;
			}
			
			echo $this->renderPage( $route.'.html' );
		}
		
        function renderPage( $pageTemplate, array $data=array() ){
            $path = SYSTEM_PATH.'assets/templates/pages/'.$_SESSION['language'].'/';
            return $this->renderTemplate( $path, $pageTemplate, $data );
        }

        function renderTemplate( string $path, string $templateFile, array $data=array() ){

			$full = $path.$templateFile;
			
			if( !file_exists( $full ) ){
				return 'not found ('.$templateFile.')';
			}
			
			$data['base_url'] = BASE_URL;
			
			$loader = new \Twig\Loader\FilesystemLoader( $path );
			$options = array(
				'strict_variables' => false,
				'debug' => false,
				'cache'=> false
			);

			$twig = new \Twig\Environment($loader, $options);

			return $twig->render( $templateFile, $data );
        }
	}
?>
