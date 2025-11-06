<?php
namespace Bucorel\Waf\Core;
use Bucorel\Waf\Language\Language;
use Bucorel\Waf\Language\Langpack;

/**
 * StatefulController extends BaseController to manage session-dependent state,
 * including user authorization and language settings.
 * * It automatically handles I18N and role-based access control.
 */
class StatefulController extends BaseController{

    /**
     * @var array $allowedRoles Specifies roles authorized to access this controller.
     * Valid values include 'all', 'auth', or specific role names (e.g., 'admin', 'editor').
     */
    protected $allowedRoles = array( 'all' );
    
    /**
     * @var Langpack|null $lang Language pack instance for retrieving localized strings.
     */
    protected $lang = null;

    /**
     * Includes the UiControls trait to add helper methods for controlling the client-side UI.
     */
    use UiControls;
    
    /**
     * Controller Initialization.
     * Sets the application configuration, initializes language settings, and checks user authorization.
     * This method is called by the Router before processing the request.
     * * @param array $config The application configuration array passed from the Router.
     * @return void
     */
    function init( array $config ): void{
        // $this->config is expected to be a property on BaseController or inherited class.
        $this->config = $config;
        $this->initLanguage();
        $this->checkRole();
    }

    /**
     * Determines and initializes the application language based on session, configuration, or browser settings.
     * 1. Checks $_SESSION['language'].
     * 2. Falls back to $this->config['DEFAULT_LANGUAGE'].
     * 3. Attempts to use the browser's language if supported and available.
     * The resulting language is stored in $_SESSION and loaded into the $this->lang property.
     * * @return void
     */
    function initLanguage(): void{
        // If language is not set in session, determine it.
        if( !isset( $_SESSION['language'] ) ){
            $lang = $this->config['DEFAULT_LANGUAGE'] ?? 'en';
            $availableLanguages = $this->config['AVAILABLE_LANGUAGES'] ?? [];
            
            // Attempt to determine language from the browser request headers
            $browserLanguage = Language::getBrowserLanguage();

            // Check if the browser language is supported by the system and configured for the app
            if( Language::isSupported( $browserLanguage ) && in_array( $browserLanguage, $availableLanguages ) ){
                $lang = $browserLanguage;
            }

            $_SESSION['language'] = $lang;
        }
        
        // Initialize the Langpack using the system path and determined language
        $this->lang = new Langpack( $this->config['SYSTEM_PATH'], $_SESSION['language'] );
    }

    /**
     * Enforces role-based access control (RBAC) for the controller.
     * Compares the user's session roles against the $allowedRoles property.
     * Terminates execution with an Unauthorized response (401) if access is denied.
     * * @throws \ErrorException If $allowedRoles is not an array.
     * @return void
     */
    function checkRole(): void{
        if( !is_array( $this->allowedRoles ) ){
            // Use static method if JsonResponse provides it
            self::showFatalError( 'ERROR_INVALID_ROLE_SPECIFICATION' );
        }
        
        // Rule 1: Allow access for everyone
        if( in_array( 'all', $this->allowedRoles ) ){
            return;
        }

        // Rule 2: Allow access for any authorized (signed-in) user
        if( in_array( 'auth', $this->allowedRoles ) && isset( $_SESSION['user']['roles'] ) ){
            return; // Use 'return' instead of 'return true' as the function signature is void
        }

        // Rule 3: Check for specific role matches
        // Check if the user is authenticated and if any of their roles intersect with the allowed roles
        if( isset( $_SESSION['user']['roles'] ) && count( array_intersect( $this->allowedRoles, $_SESSION['user']['roles'] ) ) > 0 ){
            return;
        }

        // If no authorization condition matched, deny access and terminate
        $this->setStatus( self::STATUS_UNAUTHORIZED );
        $this->setMessage( 'Unauthorized' );
        $this->finish();
    }
}
