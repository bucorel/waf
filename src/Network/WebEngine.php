<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur
	 */

	namespace Bucorel\Waf\Network;
	
	class WebEngine{
		
		function start() : void {
			$route = $this->getRoute();
			
			$templatePath = PAGE_PATH.'/en/';
			$file = $route.'.html';
			if( file_exists( $templatePath. $file ) ){
				$this->renderTemplate( $templatePath, $file );
				exit;
			}
			
			header( 'HTTP/1.1 404 Not Found' );
			$file = ERROR_404_PAGE.'.html';
			if( file_exists( $templatePath. $file ) ){
				$this->renderTemplate( $templatePath, $file );
				exit;
			}else{
				echo 'page not found ('.$route.')';
			}
		}
		
		function getRoute() : string {
			if( !isset( $_REQUEST[ '_route' ] ) || $_REQUEST[ '_route' ] == '' ){
				return DEFAULT_ROUTE;
			}else{
				return $_REQUEST[ '_route' ];
			}
		}
		
		function renderTemplate( $path, $file ) : void {
			$data['base_url'] = BASE_URL;
			
			$loader = new \Twig\Loader\FilesystemLoader( $path );
			$options = array(
				'strict_variables' => false,
				'debug' => false,
				'cache'=> false
			);
			$twig = new \Twig\Environment($loader, $options);

			echo  $twig->render( $file, $data );
		}
	}
?>
