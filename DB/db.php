<?php 
namespace Emagid\Db;

/**
* base class for DB access and modeling .
*/
abstract class Db{

	/**
	* @var object - private db object, should be created once per instance of the encasing object.
	*/
	private $db ;

	/**
	* @var string id field for table
	*/	
	protected $fld_id = "id";

	/**
	* @var string table name 
	*/
	protected $table_name; 


	/** 
	* @var Array of fields . used for insert / update 
	*/ 
	protected $fields = array(); 


	/**
	* @var int id of the current record 
	*/
	public $id = 0 ;


	/**
	* Get or creates the db object
	* 
	* @return Object - the db object.
	*/
	private function getConnection (){

		global $emagid ;

		if($this->db ==null ){

			require_once("ez_sql_core.php");
			require_once("ez_sql_mysql.php");

			$this->db = new \ezSQL_mysql(
				$emagid->connection_string->username, 
				$emagid->connection_string->password,  // pwd
				$emagid->connection_string->db_name,  // dbname 
				$emagid->connection_string->host);
		}

		return $this->db;
	}


	/** 
	* convert an array to an object 
	*
	* @param Array
	* @return strClass 
	*/
	private function array_to_object($array) {
	  $obj = new stdClass;

	  foreach($array as $k => $v) {
	     if(is_array($v)) {
	        $obj->{$k} = $this->array_to_object($v); //RECURSION
	     } else {
	        $obj->{$k} = $v;
	     }
	  }

	  return $obj;
	} 

	/** 
	* convert object to an array
	*
	* @param $data Object 
	* @return Array named array 
	*/
	private function object_to_array($data)
	{
	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] = $this->object_to_array($value);
	        }
	        return $result;
	    }
	    return $data;
	}



	/**
	* get list from a table
	* @param $params Array - conditions 
	* @return Array array of objects from the db table.
	*/
	function getList($params = array()){

		$db = $this->getConnection(); 

		

		$sql = "SELECT * FROM $this->table_name";

		return $db->get_results($sql);
	}



	/**
	* get list from a table
	* @param $id int get a single record by id.
	* @return Object  object representing a single result line from the DB .
	*/
	function getItem($id){

		$db = $this->getConnection(); 



		$sql = "SELECT * FROM $this->table_name WHERE $this->fld_id=".$id;

		$row = $db->get_row($sql);;


		if(count($row)>0){
			// assign the values to the current object. 
			// required for the update/insert functionality .
			$arr = $this->object_to_array($row);
			
			foreach($arr as $key => $val){
				$this->{$key}=$val;
			}
		}

		return $this;
	}

	/**
	* Delete the current record 
	*/
	function delete($id){

		$db = $this->getConnection();
		$sql = "DELETE FROM $this->table_name WHERE $this->fld_id=".$id;
		$db->query($sql);
	}

	/**
	* Insert / Update the current record 
	*/
	function save(){
	  $db=$this->db; ; 
		
		
		$vals = $this->object_to_array($this);
		
		// array used for update 
		$update = array(); 
		
		// arrays used for insert
		$insert_names = array(); 
		$insert_vals = array(); 
		
		
		
		// build both insert and update arrays
		foreach($this->fields as $fld){
			$val = $db->escape($vals[$fld]);
			$val = is_numeric($val)?$val:sprintf("'%s'", $val);
			
			
			array_push($update , sprintf("%s=%s", $fld, $val));
			array_push($insert_names, $fld);
			array_push($insert_vals, sprintf("%s", $val));	
		}
		
		
		// decide whether we need an INSERT or an UPDATE, and build the SQL query.
		if($this->id == 0){
			$vals1 = implode(',', $insert_names );
			$vals2 = implode(',', $insert_vals );
			
			$sql = "INSERT INTO $this->table_name ($vals1) VALUES($vals2)";
		}else {
			$vals = implode(',', $update );
			$sql = "UPDATE $this->table_name SET $vals";
			$sql .= " WHERE id={$this->id}";
		}
		
		
		
		if($db->query($sql)){
				return true;
			}else{
				die($sql . "<br/>" . mysql_error());
				return false;
			}
		
	}



	/**
	* load the form fields into the object after submit using POST.
	*/
	function loadFromPost(){
		foreach($_POST as $key=>$val){
			$this->{$key} = $val;
		}
	}
	

	/**
	* load the form fields into the object after submit using GET.
	*/
	function loadFromGet(){
		foreach($_GET as $key=>$val){
			$this->{$key} = $val;
		}
	}


}

?>
