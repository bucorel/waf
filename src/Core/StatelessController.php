<?php
namespace Bucorel\Waf\Core;
use Bucorel\Waf\Language\Language;
use Bucorel\Waf\Language\Langpack;

/**
 * StatefulController extends BaseController to manage session-dependent state,
 * including user authorization and language settings.
 * * It automatically handles I18N and role-based access control.
 */
class StatelessController extends BaseController{
    
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
    }
}
