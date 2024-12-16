<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Testmod;
	
	class Start extends \Bucorel\Waf\Network\ApiEndpoint{
		
		function handleGetRequest(){
			try{
				/*
				$db = new \Bucorel\Waf\Dal\PgDb();
				$db->setConnectionParams(
					'localhost',
					5432,
					'postgres',
					'postgres',
					'highrisk'
				);
				
				$sql = "select version()";
				$r = $db->query( $sql );
				while( $row = pg_fetch_assoc( $r ) ){
					$this->showData( $row );
				}
				*/
				
				$ua = new UserAccount();
				$ua->set( 'first_name', 'Pushpendra' );
				$ua->set( 'last_name', 'Thakur' );
				$ua->set( 'roles', array('subs') );
				
				$this->showData( $ua->dump() );
			}catch( \Exception $e ){
				$this->showError(
					self::STATUS_INTERNAL_ERROR,
					$e->getMessage()
				);
			}
		}
	}
?>
