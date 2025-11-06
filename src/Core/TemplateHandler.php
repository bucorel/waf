<?php
namespace Bucorel\Waf\Core;

class TemplateHandler{

	protected $templatePath = '';
	protected $language = 'en';
	protected $twig = null;

	/**
     * @param string $templatePath The base path where all templates are stored.
     * @param string $language The default language for UI templates.
     */
	function __construct( string $templatePath, string $language = 'en' ){
		$this->templatePath = $templatePath;
		$this->language = $language;

		$loader = new \Twig\Loader\FilesystemLoader( $templatePath );
		$options = array(
			'strict_variables' => false,
			'debug' => false,
			'cache'=> false
		);

		$this->twig = new \Twig\Environment($loader, $options);
	}
	
	/**
     * Renders a template from the structured UI path.
     * @return string The rendered HTML.
     */
	function renderUi( string $module, string $template, array $data=array() ):string{
		$template = 'ui/'.$this->language.'/'.$module.'/'.$template;
		return $this->twig->render( $template, $data );
	}

	function renderTheme( string $themeName, array $data=array() ):string{
		$template = 'themes/'.$themeName.'/frame.html';
		return $this->twig->render( $template, $data );
	}

	function renderPage( string $pageTemplate, array $data ):string{
		$template = 'pages/'.$this->language.'/'.$pageTemplate;
		return $this->twig->render( $template, $data );
	}
}
?>