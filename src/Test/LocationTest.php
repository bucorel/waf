<?php
	namespace Bucorel\Waf\Test;

	class LocationTest extends \Bucorel\Waf\WebAppController{

		function handleGetRequest(){
			echo '<pre>';
			print_r( $_SESSION['location'] );
			echo '</pre>';

			echo \Bucorel\Waf\Location\IpLocation::getIp();
		}
	}
?>