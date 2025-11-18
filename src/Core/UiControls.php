<?php
namespace Bucorel\Waf\Core;

/**
 * Trait UiControls
 * Provides methods for StatefulControllers to manipulate the client-side UI
 * by appending specialized commands to the JSON response data array.
 * * It assumes the client-side JavaScript framework interprets these commands
 * * (e.g., fill, panel, next, etc.).
 */
trait UiControls{
    
    /**
     * Updates an element's content or value on the client side.
     * If the target is a form element (input/textarea), its value is updated.
     * Otherwise, the element's inner HTML is replaced with the content.
     * * @param string $targetElementId The HTML ID of the element to update.
     * @param mixed $content The new content (string) or value (string/numeric).
     * @return void
     */
    function fill( string $targetElementId, mixed $content ): void{
        $r = [
            '_typ'=>'fill', // Command type
            '_tar'=>$targetElementId, // Target element ID
            '_dat'=>$content // New content/value
        ];

        // appendData is assumed to be a method available on JsonResponse/BaseController
        $this->appendData( $r );
    }

    /**
     * Shows a modal panel (dialog/popup) on the client side.
     * * @param string $panelType The type/style of the panel (e.g., 'modal', 'drawer').
     * @param string $panelId The unique ID for the panel instance.
     * @param string $content The HTML content to display inside the panel.
     * @param string $title The title of the panel header (default: "").
     * @param int $closeButton Whether to display a close button (1 for yes, 0 for no).
     * @return void
     */
    function showPanel( string $panelType, string $panelId, string $content, string $title="", int $closeButton=1 ): void{
        $r = [
            '_typ'=>'panel',
            '_pty'=>$panelType,
            '_tar'=>$panelId,
            '_tit'=>$title,
            '_cbtn'=>$closeButton,
            '_dat'=>$content
        ];

        $this->appendData( $r );
    }

    /**
     * Closes a previously opened panel on the client side using its ID.
     * * @param string $panelId The unique ID of the panel to close.
     * @return void
     */
    function closePanel( string $panelId ): void{
        $r = array(
            '_typ'=>'rpanel', // Command type: remove panel
            '_tar'=>$panelId
        );
        
        $this->appendData( $r );
    }

    /**
     * Instructs the client to execute a new request (redirect/API call).
     * This is useful for chaining operations after a successful submission.
     * * @param string $url The URL to navigate to or request.
     * @param array $keyValueData Associative array of key-value data to be appended to the URL as query string parameters.
     * @param int $progressBar Whether to display a loading/progress bar during the next request (1 for yes, 0 for no).
     * @return void
     */
    function executeNext( string $url, array $keyValueData=array(), int $progressBar=1 ): void{
        $data = [];
        foreach($keyValueData as $k=>$v){
            array_push( $data, $k.'='.$v);
        }
        
        $dataStr = implode('&', $data);
        $a = array(
            '_typ'=>'next', // Command type
            '_tar'=>$url,
            '_dat'=>$dataStr,
            '_pbar'=>$progressBar
        );
        
        $this->appendData( $a );
    }
    
    /**
     * Activates a specific content pane within a multi-pane container (e.g., a tab content area).
     * * @param string $paneId The ID of the pane element to select/show.
     * @return void
     */
    function selectPane( string $paneId ): void{
        $a = array(
            '_typ'=>'selpane',
            '_tar'=>$paneId
        );
        
        $this->appendData( $a );
    }
    
    /**
     * Activates a specific tab button/element, potentially triggering selection of the corresponding pane.
     * * @param string $tabId The ID of the tab element to select/activate.
     * @return void
     */
    function selectTab( string $tabId ): void{
        $a = array(
            '_typ'=>'seltab',
            '_tar'=>$tabId
        );
        
        $this->appendData( $a );
    }
    
    
    /**
     * Activates a specific drawer, potentially triggering selection of the corresponding drawer.
     * * @param string $drawerId The ID of the drawer element to select/activate.
     * @return void
     */
    function selectDrawer( string $drawerId ): void{
        $a = array(
            '_typ'=>'seldra',
            '_tar'=>$drawerId
        );
        
        $this->appendData( $a );
    }
    /**
     * Instructs the client to initialize or update a map view.
     * * @param float $lat The latitude for the map center (default: 23.316633).
     * @param float $lng The longitude for the map center (default: 79.074485).
     * @param int $zoom The zoom level for the map (default: 1).
     * @return void
     */
    function showMap( float $lat=23.316633, float $lng=79.074485, int $zoom=1 ): void{
        $a = array(
            '_typ'=>'map',
            '_lat'=> $lat, 
            '_lng'=>$lng,
            '_zoo'=>$zoom
        );
        
        $this->appendData( $a );
    }
    
    /**
     * Resets a CAPTCHA element, typically to display a new challenge.
     * * @param string $captchaId The ID of the CAPTCHA element to reset.
     * @return void
     */
    function resetCaptcha( string $captchaId ): void{
        $a = array(
            '_typ'=>'rescap',
            '_tar'=>$captchaId
        );
        
        $this->appendData( $a );
    }
    
    /**
     * Displays a list of category suggestions, typically in a dedicated UI element.
     * * @param mixed $data The data structure containing the category suggestions.
     * @return void
     */
    function showCategorySuggestions( $data ): void{
        $r = array(
            '_typ'=>'catsug',
            '_dat'=>$data
        );
        
        $this->appendData( $r );    
    }

    /**
     * Changes the source URL of an HTML image element.
     * * @param string $imageElementId The ID of the <img> element.
     * @param string $sourceUrl The new URL for the image source (src attribute).
     * @return void
     */
    function changeImage( string $imageElementId, string $sourceUrl ): void{
        $r = array(
            '_typ'=>'cimg',
            '_tar'=>$imageElementId,
            '_dat'=>$sourceUrl
        );
        
        $this->appendData( $r );
    }

    /**
     * Changes the background image URL of a specified HTML element.
     * * @param string $imageElementId The ID of the target element.
     * @param string $sourceUrl The new URL for the background image (CSS background-image property).
     * @return void
     */
    function changeBackgroundImage( string $imageElementId, string $sourceUrl ): void{
        $r = array(
            '_typ'=>'cbimg',
            '_tar'=>$imageElementId,
            '_dat'=>$sourceUrl
        );
        
        $this->appendData( $r );
    }

    /**
     * Displays a field-specific error message and highlights the corresponding field.
     * Sets the response status to 400 Bad Request and terminates the request.
     * * @param string $message The human-readable error message.
     * @param string $fieldId The ID of the form field to highlight.
     * @param string $paneId Optional ID of a pane to select before finishing (for error visibility).
     * @param string $tabId Optional ID of a tab to select before finishing (for error visibility).
     * @return void
     */
    function showFieldError( string $message, string $fieldId, string $paneId="", string $tabId = "" ): void{
        $this->setStatus( self::STATUS_BAD_REQUEST );
        $this->setMessage( $message );
        $this->addMeta( '_fie', $fieldId ); // Adds field ID to meta data for client processing

        if( $paneId != '' ){
            $this->selectPane( $paneId );
        }

        if( $tabId != '' ){
            $this->selectTab( $tabId );
        }

        $this->finish();
    }
}
