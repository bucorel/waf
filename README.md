# WAF
Web Application Framework is a lightweight PHP framework to create simple MVC web applications and REST APIs.

### Sample configuration for a WebApp
```php
<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	define( 'SYSTEM_PATH', '/path/to/project/source/' );
	define( 'BASE_URL', '/' );
	
	//REDIS SESSION
	define( 'SESSION_SAVE_PATH', 'tcp://127.0.0.1:6379' );
	define( 'SESSION_SAVE_HANDLER', 'redis' );
	
	//LANGUAGES
	define( 'LANGPACK_PATH', SYSTEM_PATH.'assets/dynamic/langpacks/' );
	define( 'AVAILABLE_LANGUAGES', array('en') );
	define( 'DEFAULT_LANGUAGE', 'en' );
	
	//ROUTES
	//if no route is specified in the URL this will be used as default
	define( 'DEFAULT_ROUTE', 'start' );
	
	//THEMES
	define( 'THEME_PATH', SYSTEM_PATH.'assets/templates/themes/' );
	define( 'DEFAULT_THEME', 'z5' );
?>
```
### Sample index.php 

```php
<?php
	require_once( 'config.php' );
	require_once( SYSTEM_PATH.'error_handler.php' );
	require_once( SYSTEM_PATH.'routes.php' );
	require_once( SYSTEM_PATH.'vendor/autoload.php' );
	
	$app = new Bucorel\Waf\WebApp();
	$app->start();
?>
```
### Sample .htaccess for Apache
```
RewriteEngine On

RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^.*$ index.php?_route=$0 [QSA,L]
```

