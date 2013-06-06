<?php


$conn = mysqli_connect(
		$this->connection_string->host, 
		$this->connection_string->username, 
		$this->connection_string->password,
		$this->connection_string->db_name
                               ) or 
die('Database Connection Error: '.mysqli_connect_error());  


$result = mysqli_query($conn,"SHOW TABLES");

$tables= []; 

$fields = [] ;


while($cRow = mysqli_fetch_array($result))
{
	$table = $cRow[0];
	$tables[] = $table;

	$sql = "SELECT * FROM ".$table. " LIMIT 0";
	$r = mysqli_query($conn, $sql);


	$tinfo = mysqli_fetch_fields($r);

	$fields[$table] = $tinfo;


	




}

mysqli_close($conn);

?>
<html>
<head>
	<link href="http://shjs.sourceforge.net/sh_style.css" rel="stylesheet" type="text/css"/>
</head>
<body style="padding:30px; ">
	
	<h1>Scaffold</h1>
	
	<table>
		<tr>
			<td>Table : </td>
			<td>
				<select name="table">
					<option value="">Select Table</option>
					<?php foreach ($tables as $table) { 
						printf("<option value=\"%s\">%s</option>" , $table, $table);
					}
					?>
				</selecT>
			</td>
		</tr>
		<tr>
			<td>Namespace :</td>
			<td><input type="text" name="namespace"/>

		</tr>

		<tr>
			<td>Class Name :</td>
			<td><input type="text" name="class"/>

		</tr>
		<tr>
			<td></td>
			<td><input id="btnScaffold" type="button" value="Scaffold"/></td>
		</tr>
	</table>
	

	<pre style="width:80%; height:600px; border:1px solid #eef; padding:10px 20px; margin:auto;" class="sh_php">

	</pre>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmplPlus.min.js"></script>
<script src="http://shjs.sourceforge.net/sh_main.min.js"></script>
<script src="http://shjs.sourceforge.net/lang/sh_php.min.js"></script>

	<script id="tmplDb" type="text/x-jquery-tmpl">
&lt;?php 
/**
* Handles user functions - authentication, profile management, etc.. 
*/
class User extends \Emagid\DB\Db{

	/**
	* Constructor 
	*/
	function __construct($params = [] ){
		$this->table_name="users";
		$this->fld_id = "id";

		$this->fields = [
			'email' => [
				'type'=>'email', 
				'required'=>true,
				'unique' => true
			], 
			'username' => [
				'required'=>true,
				'unique' => true
			], 
			'password' => [
				'type'=>'password', 
				'required'=>true
			]
		]; 


		parent:: __construct($params);
	}


	/**
	*   Check the username / pwd in the DB, and if it's valid it will create the authentication session for the user.
	*
	*   @return boolean   True / False wether the login is valid or not. 
	*/
	function login($username, $password){
		

		$list = $this->getList([
			'where' =>[
					'username' => $username, 
					'password' => $password
				] 
			]); 

		if(count($list)==1){



			\Emagid\Html\Membership::setAuthenticationSession($list[0]->id ,[] , $list[0]);


			return true;

		}

		return false;
		
	}


}

?&gt;
	</script>
	<script type="text/javascript">
		var fields = [];

		<?php foreach ($fields as $key => $value) {
		printf("fields[\"%s\"] = %s;",$key, json_encode($value)) ;
		}?>


		$(function(){

			$('#btnScaffold').bind('click',function(){
				var data ={
					'test':'test'
				};

				var txt = $('#tmplDb').text() ;


				$('pre').html('');

				$('pre').html(txt);
				//$('#tmplDb').tmpl(data).appendTo('textarea');
				//$('#tmplDb').tmpl(data).appendTo('pre');

				sh_highlightDocument();

			});

		});
		console.log(fields);

	</script>

</body>
</html>
<?php
exit;
?>