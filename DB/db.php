<?php 
namespace Emagid\Db;

/**
* base class for DB access and modeling .
*/
abstract class Db{


	/**
	* @var holds loaded data, to support defered/  lazy loading 
	*/
	private $data = array();

	/**
	* @var object - private db object, should be created once per instance of the encasing object.
	*/
	private $db ;

	/**
	* @var string id field for table
	*/	
	public $fld_id = "id";

	/**
	* @var string table name 
	*/
	protected $table_name; 


	/** 
	* @var Array of fields . used for insert / update 
	*/ 
	public $fields = array(); 



	/** 
	* @var Array of error messages from the PDO object
	*/ 
	public $errors = [] ;




	/**
	* @var int id of the current record 
	*/
	public $id = 0 ;

	/**
	* @var Array relationships with other tables
	*/
	public $relationships = []  ;


	function __construct($params){
		if(is_int($params)){ // safe to assume that the user wanted to load a single record 
			$this->getItem($params);
		}
	}


	/**
	* Get or creates the db object
	* 
	* @return Object - the db object.
	*/
	protected function getConnection (){
		global $emagid ;

		$this->db = new \PDO("mysql:host={$emagid->connection_string->host};dbname={$emagid->connection_string->db_name};charset=utf8", $emagid->connection_string->username, $emagid->connection_string->password);

		return $this->db ;
	}


	


	/**
	* count rows in table.
	* @param $params Array - conditions 
	*		 $params = array(
	*						"where" => array(field_name => handle, field_name => handle) //where condition for "=" and "AND"  only, NO "OR", "LIKE" or anyothers 
	*						);
	* @return int number of records in the table 
	*/
	function getCount($params = array()){

		$db = $this->getConnection(); 

		$sql = "SELECT count(*) FROM $this->table_name";


	
		// apply where conditions
		if(isset($params['where'])){ // apply where conditions
			$sql.=" WHERE ". $this->buildWhere($params['where']);
		}

		return $this->getVar($sql);
	}
	



	/**
	* get list from a table
	* @param $params Array - conditions 
	*		 $params = array(
	*						"sql" => sql statement //If this param is set, all other params would be DISABLED
	*						"where" => array(field_name => handle, field_name => handle) //where condition for "=" and "AND"  only, NO "OR", "LIKE" or anyothers 
	*						"orderBy" => field_name 
	*						"sort" => ASC or DESC 
	*						"limit" => 10 //number
	*						"offset" => 10 //number
	*						);
	* @return Array array of objects from the db table.
	*/
	function getList($params = array()){

		$db = $this->getConnection(); 

		
	if(isset($params['sql'])){
			//if sql is set, just execute it without apply any other params
			$sql = $params['sql'];
	
	}else{
			$sql = "SELECT * FROM $this->table_name";
	
			// apply where conditions
			if(isset($params['where'])){ // apply where conditions
				$sql.=" WHERE ". $this->buildWhere($params['where']);
			}
			
			// apply order and sort

			isset($params['orderBy'])? $orderBy = $params['orderBy'] : $orderBy = $this->fld_id." DESC";
			
			$sql.= " ORDER BY {$orderBy}";


			// apply pagination
			if(isset($params['limit'])){
				$sql.= " LIMIT ".$params['limit'];
			}
			
			if(isset($params['offset'])){
				$sql.= " OFFSET ".$params['offset'];
			}


	}//close construct sql




		$sth = $this->db->prepare($sql);
		$sth->execute();
		$dbList = $sth->fetchAll();

	
		  

			$list = [] ;

		if($dbList && count($dbList)){

			$cls = get_class($this); 

			foreach( $dbList  as $item){

				if (is_array($item))
					$item = array_to_object($item); 

				$obj = new $cls ; 

				clone_into($item, $obj);

				$obj->id=$item->{$this->fld_id};

				array_push($list, $obj);

			}

		}

		return $list;
	}



	function buildWhere($where){

		if(is_array($where)){

			$arr = [] ; 

			foreach($where as $key=>$val ){
				array_push($arr,sprintf("(%s='%s')", $key,$val));
			}

			return implode(" AND ", $arr);
		} else {
			return $where;
		}
	}



	/**
	* get list from a table
	* @param $id int get     a single record by id.
	* @param $options Array  added instructions : 
	* @return Object  object representing a single result line from the DB .
	*/
	function getItem($id, Array $options = []){

		$db = $this->getConnection(); 



		//$sql = "SELECT * FROM $this->table_name WHERE $this->fld_id=".$id;
		//'sql'=>$sql
		$list = $this->getList(['where' => [$this->fld_id => $id ]]);

		if (count($list))
			return $list[0]; 

		return null; 
	}

	/**
	* Delete the current record 
	*/
	function delete($id){

		$db = $this->getConnection();

		$sql = "DELETE FROM $this->table_name WHERE $this->fld_id=".$id;

		$sth = $db->prepare($sql);

		$sth->execute();
	}


	/**
	* Returns the list of field names, in case the array is complex 
	*/
	function getFieldsList () {
		$arr = array();

		foreach ($this->fields as $key=>$val) {

			if($key)
				$arr[]  = $key; 
			else 
				$arr[] = $val ; 

		}

		return $arr ;
	}

	/**
	* Insert / Update the current record 
	*/
	function save(){

		global $emagid ;

	  $db = $this->getConnection();
		
		
		$vals = object_to_array($this);
		
		// array used for update 
		$update = array(); 
		
		// arrays used for insert
		$insert_names = array(); 
		$insert_vals = array(); 
		
		
		
		// build both insert and update arrays
		foreach($this->fields as $key => $value ){
			$null_if_empty = false; 


			if(is_numeric($key))
			{
				$fld = $value; 
			}else{
				$fld = $key;

				
				$null_if_empty = isset($this->fields[$key]['null_if_empty'])  && $this->fields[$key]['null_if_empty'];
				
			}
			
			if(isset($fld)   && $fld){


				if(!isset($vals[$fld]))
					continue;


				if((!isset($vals[$fld]) || empty($vals[$fld])) && $null_if_empty){
					$val = "NULL "; 
				}
				else {
					//$val = $db->escape($vals[$fld]);
					$val = $vals[$fld];
					$val = is_numeric($val)?$val:sprintf("'%s'", $val);
				}
			}else{

			}



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


		
		

		if($this->execute($sql)){

				if($this->id == 0 )
				{

					$this->id = $this->getVar("SELECT LAST_INSERT_ID() as id ");
				}

				return true;
		}else{

			if ($emagid->debug){
				dd($this->errors);
			}
			
			return false;
		}
		
	}

	function execute($sql){
		$sth = $this->db->prepare($sql);
		$res = $sth->execute();

		$this->errors = $sth->errorInfo();

		return $res;
	}

	function getVar($sql){
		$sth = $this->db->prepare($sql);
		$sth->execute();

		$result = $sth->fetchColumn();

		return $result;
	}

	function query($sql){
	  $db = $this->getConnection();

	  $db->query($sql);
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


	public function __get($name){

		// check if data was already created 
		if(isset($this->data[$name]))
			return $this->data[$name];



		foreach($this->relationships as $relationship){


			if($relationship['name'] == $name){


				if(isset($relationship['class_name'])){ // creating a strong named object 

					$class = $relationship['class_name'];

					$obj = new $class;

					$local_val = $this->{$relationship['local']};

					if(!$local_val)
					{
						return null;
					}


					if($relationship['relationship_type']=='many'){
						$key = $relationship['remote']; 
						
						$obj = $obj->getList([
								'where' => [
									 $key => $local_val
								] 
							]);
					}else{

						$obj->getItem($local_val);
					}
					


					$this->data[$name] = $obj;
					$this->{$name} = $obj;


					return $obj; 

				}else { // creating a generic type 

					$this->{$name} = $this->loadChildren($relationship);
					$this->data[$name] = $this->loadChildren($relationship);

				}
				
			}
		}

	}




	function loadChildren($params){
		$db = $this->getConnection();

		$table = $params['table_name'];
		$local = $params['local'];
		$local_val = $this->{$local}; 
		$remote = $params['remote'];
		$relationship_type = $params['relationship_type'];

		if($relationship_type=='many'){
			return $db->get_results("SELECT * FROM $table WHERE $remote='$local_val'");
		}else {
			return $db->get_row("SELECT * FROM $table WHERE $remote='$local_val'");
			
		}

	}


}

?>
