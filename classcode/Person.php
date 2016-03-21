<?php

class Person extends BirdClubObject {

	protected $table = "member";

	//method that gets called when you try putting this object in a string (e.g. when trying to echo it)
	public function __toString () {
		$string = "Name: {$this->first} {$this->last} (id: {$this->id})\n";
		$string .= "E-mail: {$this->email}\n";
		$string .= "Phone: {$this->phone}\n";
		return $string;
	}
	
	public function saveToDatabase() {
		//Tasks:
		//1. clean up data
		//2. build a query
		//3. run the query
		// 		3a. return an error, if appropriate


		$vars_for_query = array();
		foreach ($this->properties as $key => $value) {
			if ($key!='id') {
				$vars_for_query[$key] = $this->cleanUp($value);
			}
		}
		print_r($vars_for_query);
		
		if ($this->id < 1) {
			//create an INSERT query
			
			//foreach ($vars_for_query as $var => $value) {
			
			$query = "INSERT INTO {$this->table} (first, last, email, phone) VALUES ({$vars_for_query['first']}, {$vars_for_query['last']}, {$vars_for_query['email']}, {$vars_for_query['phone']})";
			echo $query . "\n";
		} else {
			//create an UPDATE query
			$query = "UPDATE {$this->table} SET first={$vars_for_query['first']}, last={$vars_for_query['last']}, email={$vars_for_query['email']}, phone={$vars_for_query['phone']} WHERE id={$this->id}";
			echo $query . "\n";
		}
		
		//run the query
		$this->mysql->query($query);
		
		if ($this->mysql->affected_rows != 1) {
			echo "Something went wrong!\n";
		}
		
		
	}
	
	
	public function displayAsHTMLForm () {
		echo "<form method=\"post\" action=\"\">\n";
			echo "<p>First: <input type=\"text\" name=\"first\" id=\"first\" value=\"{$this->first}\"></p>\n";
			echo "<p>Last: <input type=\"text\" name=\"last\" id=\"last\" value=\"{$this->last}\"></p>\n";
			echo "<p>Email: <input type=\"text\" name=\"email\" id=\"email\" value=\"{$this->email}\"></p>\n";
			echo "<p>Phone: <input type=\"text\" name=\"phone\" id=\"phone\" value=\"{$this->phone}\"></p>\n";
			echo "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"{$this->id}\">\n";
			echo "<br>\n";
			echo "<p><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Send to DB!\"></p>\n";
		echo "</form>\n";
	}
	


}


?>