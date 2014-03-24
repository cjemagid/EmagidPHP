<?php 

namespace Emagid\Html ;


/** 
* A helper class for all membership related
*/
class Membership {

	/**
	* 	Sets the session when a user has signed up .
	*
	*	@param  int/string  $id    the member's Id 
	*	@param  array       $roles array of roles the user is subscribed to .
	*	@param  object      $obj   any extra object 
	*/
	public static function setAuthenticationSession($id, $roles = [] , $obj = null ){
		$session = new \stdClass ; 

		$session->model = $obj ;
		$session->roles = $roles ; 
		$session->id = $id ; 

		$_SESSION['em_authentication_id'] = $id ;
		$_SESSION['em_authentication_model'] = $session ; 


	}

	/**
	* 	Gets the session when a user has signed up .
	*
	*	@return Object   The object that was stored when the user logged in . 
	*					- model  : the extra data
	*					- roles  : an array of roles the user belongs to. 
	*					- id 	 : the user's id .
	*/
	public static function getAuthenticationSession(){

		return $_SESSION['em_authentication_model'];
	}

	/**
	*	Delete the authentication session
	*/
	public static function destroyAuthenticationSession(){
		unset($_SESSION['em_authentication_id'] );
		unset($_SESSION['em_authentication_model'] );
	}


	/**
	* 	@return mixed the user id from the session 
	*/
	public static function userId(){
		
		return $_SESSION['em_authentication_id'] ;
		
		
	}


	/**
	* 	Checks if user is logged in .
	*/
	public static function isAuthenticated(){
		
		return isset($_SESSION['em_authentication_id'] );
		
		
	}

	/**
	* 	Checks if user is assigned to a role, or roles
	*
	*	@param   Array   $params   List of roles to check 
	*	@return  bool              True / False if the user is in the roles in question 
	*/
	public static function isInRoles(){
		$params = func_get_args();


		if(!self::isAuthenticated())
			return false; 

		if(!count($params))
			return true;


		$model = self::getAuthenticationSession();


		if(!$model->roles || !count($model->roles)){
			return false;
		}

		foreach ($params as $role) {
			if(in_array($role, $model->roles))
				return true;
		}

		return false; 
	}


	/**
	* Imported from Radon
	*  @author Norman Ovenseri <novenseri@gmail.com>
 	*  @copyright eMagid 2014
   * Generates a password with a salt (uses default salt generation function if no salt provided)
   * using the chosen algo (sha256 default)
   * @param string $password
   * @param string $salt
   * @param string $algo
   * @return array Newly generated password with salt
   */
  public static function hash($password, $salt = null, $algo = null)
  {
    $algo = ($algo != null) ? $algo : 'sha256';
    $salt = ($salt != null) ? $salt : self::_generateSalt();
    
    return ['password' => hash_hmac($algo, $password, $salt), 'salt' => $salt];
  }
  
  /**
  * Imported from Radon
	*  @author Norman Ovenseri <novenseri@gmail.com>
 	*  @copyright eMagid 2014
   * Generates a salt for passwords generation
   * @return string salt for password
   */
  protected static function _generateSalt()
  {
    return md5(time());
  }


}

?>