<?php
/*
pgdb
Ronaldo Barbachano
http://myparse.net
July, 2010

My super simple postgres class that interfaces with  postgres php functions

*/
class pgdb
{

	function __construct($dbname, $user, $password, $host='localhost')
	{
		if (!$this->db)
			$this->db = pg_connect("host=$host dbname=$dbname user=$user password=$password") or die('Could not connect: ' . pg_last_error());
	}


	private function do_query($query, $classname=NULL, $params=NULL)
	{
		if (func_num_args() > 1) {
			if (is_string($classname) && !is_array($params))
				$result = pg_query($query, $classname);
			elseif (!is_string($classname) && is_array($params))
				$result = pg_query($query, NULL, $params);
			elseif (is_string($classname) && is_array($params))
				$result = pg_query($query, $classname, $params);
		}
		if (!$result) $result = pg_query($query);
		return $result;

	}
	function put_array($table_name, $array, $db=NULL)
	{
		// experimental ??uses pg_insert(to put an array into a table )
		//... you'll want to make sure the fields match perfectly !!

		if ($db == NULL) $db = $this->db;
		pg_insert($db, $table_name, $array);

	}
	
	function p_select($table,$array,$options=NULL){
	// Experimental
	// pg_select to select from a table the values in array that match ?	
		$result = ($options == NULL ? pg_select($this->db, $table, $array) : pg_select($this->db,$table,$array,$options));
		
		return $result? $result:false;
	}

	function get_objects($query, $classname=NULL, $params=NULL)
	{
		// outputs normal array with std objects (unless classname is defined)
		$result = $this->do_query($query, $classname, $params);
		while ($row = pg_fetch_object($result))
			$r []= $row;
		pg_free_result($result);
		return $r;
	}

	function get_all_array($query)
	{
		return pg_fetch_all($this->do_query($query));

	}
	function get_table_copy($table, $seperator=NULL, $null_value=NULL)
	{
		// uses pg_copy_to to return an array of an entire table
		// good for use of 'export table out'
		switch (func_num_args()) {
		case 1:
			return pg_copy_to($this->db, $table);
			break;
		case 2:
			return pg_copy_to($this->db, $table, $seperator);
			break;
		case 3:
			return ($seperator != NULL ?pg_copy_to($this->db, $table, $seperator, $null_value) : pg_copy_to($this->db, $table, NULL, $null_value));
			break;
		}

	}

	function get_meta_data($table)
	{
		$meta = pg_meta_data($this->db, $table);
		return is_array($meta)?$meta:NULL;
	}

	function get_assoc($query, $params=NULL)
	{
		// outputs array with associtative arrays
		$result = $this->do_query($query, NULL, $params);
		while ($row = pg_fetch_assoc($result))
			$r []= $row;
		pg_free_result($result);
		return $r;
	}

	function get_rows($query, $params=NULL)
	{
		// outputs all rows into a single string
		$result = $this->do_query($query, NULL, $params);
		while ($row = pg_fetch_row($result)) {
			foreach ($row as $key=>$field) {
				$r .= $field . ' ';
			}

		}
		pg_free_result($result);
		return $r;
	}

	function __destruct()
	{
		pg_close($this->db);
	}

}