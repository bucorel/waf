<?php
    /**
     * @copyright Business Computing Research Laboratory <www.bucorel.com>
     * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
     */

    namespace Bucorel\Waf;

    class WebAppController extends RequestHandler{

        protected $lang = null;
        protected $allowedRoles = array( -1 );

        function init(){
			$this->initSession();
			$this->initLanguage();
			$this->checkAuthentication();
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

			$this->lang = new \Bucorel\Waf\Langpack\Langpack( LANGPACK_PATH, $_SESSION['language'] );
        }

        function checkAuthentication(){
            //signin not required
            if( count( $this->allowedRoles ) == 0 || $this->allowedRoles[0] == -1 ){
                return;
            }

            //signin required, every role is allowed
            if( $this->allowedRoles[0] == 0 && isset( $_SESSION['user'] ) ){
                return;
            }

            //specific role only
            if( count( array_intersect( $_SESSION['user']['roles'], $this->allowedRoles) ) > 0 ){
                return;
            }

            $this->showError( self::HTTP_STATUS_UNAUTHORIZED, 'Unauthorized' );
        }

        /** in a WebAppController, GET method always loads theme */
        function handleGetRequest(){
        	echo $this->renderTheme( DEFAULT_THEME );
        }

        /** Template Controls */
        function renderTheme( string $themeName ) : string {
			$path = SYSTEM_PATH.'assets/templates/themes/'.$themeName.'/';
			$template ='index.html';
            return $this->renderTemplate( $path, $template );
        }

        function renderUi( $module, $uiTemplate, array $data=array() ){
            $path = SYSTEM_PATH.'assets/templates/ui/'.$_SESSION['language'].'/'.$module.'/';
            return $this->renderTemplate( $path, $uiTemplate, $data );
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

		/** UI controls */
		function fill( $targetElementId, $data ){
			$a = array(
				'_typ'=>'fill',
				'_tar'=>$targetElementId,
				'_dat'=>$data
			);

			$this->appendData( $a );
		}

		function showPanel( $type, $id, $content, $closeBtn=1, $fallBackUrl="" ){
			$a = array(
				'_typ'=>'panel',
				'_pty'=>$type,
				'_tar'=>$id,
				'_cbt'=>$closeBtn,
				'_dat'=>$content,
				'_fbu'=>$fallBackUrl
			);

			$this->appendData( $a );
		}

		function removePanel( $targetElementId ){
			$a = array(
				'_typ'=>'rpanel',
				'_tar'=>$targetElementId
			);

			$this->appendData( $a );
		}

		function next( $url, $progressBar = 1 ){
			$a = array(
				'_typ'=>'next',
				'_tar'=>$url,
				'_pba'=>$progressBar
			);

			$this->appendData( $a );
		}

		function selectTab( $targetElementId ){
			$a = array(
				'_typ'=>'seltab',
				'_tar'=>$targetElementId
			);

			$this->appendData( $a );
		}

		function selectDrawer( $targetElementId ){
			$a = array(
				'_typ'=>'seldra',
				'_tar'=>$targetElementId
			);

			$this->appendData( $a );
		}

    }
?>