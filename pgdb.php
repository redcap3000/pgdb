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
	
	
	function pg_2d_array_explode($column){
	/* pass this a STRING, with arrays that hold TEXT values, this is specifically designed with table versioning in mind
	   takes a two dimensional array inside a postgres column and converts into an array with assocititave values provided
	   supported format specified below
	*/
	
	// cleaning up output for explosion
	
	// remove values that may have ben stored as null
	$column = str_replace(array(',NULL,',',NULL','NULL,'),',',$column);
	// takes care of leftover commas if any
	$column = str_replace(',,',',',$column);
	// remove the curly braces except the one we explode on, }, to properly additional arrays are appended to
	// the two dimensional array , also removing NULL if it exists (pesky... )
	$column = str_replace(array('NULL','{{','}}','{'),'',$column);
	// ditch quotes
	$column = str_replace('""',' ',$column);
	$column = str_replace('"','',$column);
	$column = explode('}',$column);
	// final cleanup of commas that may exist at the beggining of a row
	
	foreach($column as $key=>$item){
		$column[$key] = ltrim($item,',');
		$column[$key] = explode(',',$column[$key]);
		foreach($column[$key] as $key_2=>$item2){
			// this step assumes you have a => as a seperator for your key=> value pairs
			$result_2 = explode(' => ',$item2);
			// assign the array value
			$column[$key][$result_2[0]] = $result_2[1];
			// unset the old value
			unset($column[$key][$key_2]);
		
			}
		}
		
	return $column;
	}	
		

	function __destruct()
	{
		pg_close($this->db);
	}

}