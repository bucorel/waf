<?php
	/** 
	 * Author - Pushpendra Singh Thakur <thakur@bucorel.com>
	 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
	 */
	function wafErrorHandler($errno, $errstr, $errfile, $errline) {
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

	// Set user-defined error handler function
	set_error_handler("wafErrorHandler");
?>
