<?php

//This class provides a SINGLETON database connection -- there is and can only be one.

	class DBConnection {

		const HOST = "localhost";
		const USER = "";
		const PASSWORD = "";
		const DATABASE = "";
		//these can be accessed with:
		//self::PASSWORD

		private function __construct () {}

		private static $mysql; //the variable for the mysql connection

		public static function getConnection() { //check if there's a connection and build one if there's none
			if (empty(self::$mysql)) {
				self::$mysql = new MySQLi(self::HOST, self::USER, self::PASSWORD, self::DATABASE);
				self::$mysql->set_charset('utf8');
			}

			return self::$mysql;
		}


		/*
		//OBJECT CONTEXT
		$this->vegetable =4;
		//PRONOUN $this
		//ACCESSOR ->


		//CLASS CONTEXT (or 'static context' or 'static reference')
		self::$mysql = new MySQLi();
		//PRONOUN self
		//PRONOUN FOR PARENT parent
		//ACCESSOR ::
		*/
	}

?>