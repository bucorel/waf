<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Dal;
	
	class PgDb{
		protected $conString = "";
		protected $dbCon = null;
		
		function setConnectionParams( $host, $port, $dbName, $user, $password, $connectTimeout=10 ){
			$this->conString = "host=$host 
								port=$port 
								dbname=$dbName 
								user=$user 
								password=$password
								connect_timeout=$connectTimeout";
		}
		
		function connect(){
			if( !$this->dbCon ){
				$this->dbCon = pg_connect( $this->conString );
			}
		}
		
		function query( $sql, array $params=array() ){
			$this->connect();
			return pg_query_params( $this->dbCon, $sql, $params );
		}
		
		final function begin(){
			return $this->query( "begin transaction" );
		}
		
		final function commit(){
			return $this->query( "commit transaction" );
		}
	}
?>
