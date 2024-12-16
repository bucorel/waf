<?php
	/**
	 * User Model
	 */
	
	namespace Bucorel\Waf\Testmod;
	use \Bucorel\Waf\Dal\Struct;
	use \Bucorel\Waf\Dal\StructException;
	
	class UserAccount extends Struct{
		
		const STATUS_BLOCKED = 0;
		const STATUS_ACTIVE = 1;
		const STATUS_DELETED = -1;
		
		function __construct(){
			$this->setField( 'ua_id', self::TYPE_INTEGER );
			$this->setField( 'first_name', self::TYPE_STRING );
			$this->setField( 'last_name', self::TYPE_STRING );
			$this->setField( 'roles', self::TYPE_STRING, self::BOOLEAN_TRUE );
			$this->setField( 'password', self::TYPE_STRING );
			$this->setField( 'status', self::TYPE_INTEGER );
			$this->setField( 'country', self::TYPE_STRING );
			
			$this->setField( 'source', self::TYPE_STRING );		//google, facebook etc
			$this->setField( 'c_time', self::TYPE_INTEGER );	//creation timestamp
			$this->setField( 'u_time', self::TYPE_INTEGER );	//last update timestamp
		}
	}
?>
