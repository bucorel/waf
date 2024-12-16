<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Network;
	
	trait AppResponse{
	
		function showPanel( 
					string $panelId, 
					string $panelType, 
					string $html, 
					int $closeButton=1, 
					string $onCloseUrl="", 
					string $fallBackUrl="" ) : void {
			$o = array(
				'_typ'=>'panel',
				'_pty'=>$panelType,
				'_tar'=>$panelId,
				'_dat'=>$html,
				'_cbt'=>$closeButton,
				'_ocu'=>$onCloseUrl,
				'_fbu'=>$fallBackUrl
			);
			
			$this->appendData( $o );
		}
		
		function removePanel( string $panelId ) : void {
			$o = array(
				'_typ'=>'rpanel',
				'_tar'=>$panelId
			);
			
			$this->appendData( $o );
		}
		
		function fill( 
				string $targetId, 
				string $html ) : void{
			$o = array(
				'_typ'=>'fill',
				'_tar'=>$targetId,
				'_dat'=>$html
			);

			$this->appendData( $o );
		}
		
		function redirect( 
				string $url, 
				int $showProgressBar = 1, 
				string $method='GET' ) : void {
			$o = array(
				'_typ'=>'next',
				'_met'=>$method,
				'_tar'=>$url,
				'_pba'=>$showProgressBar
			);

			$this->appendData( $o );
		}
		
		function clearWorkspace( string $url ) : void {
			$o = array(
				'_typ'=>'clear',
				'_tar'=>$url
			);

			$this->appendData( $o );
		}
		
		function renderTemplate( 
				string $module, 
				string $templateFile,
				array $data=array() ) : string {
			$path = TEMPLATE_PATH.$_SESSION['language'].'/'.$module.'/';
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
