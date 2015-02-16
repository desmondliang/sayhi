<?php

class User extends CI_Model {

	public $user_id;
  	public $email;
  	public $password;
  	public $errors;
  	
  	private $organization;

  function __construct($uid = null){

  	// Call the Model constructor
  	parent::__construct();

	if($uid){
	  $this->user_id = $uid;
	}

  }
  
  /**
  *	function: login
  * login to admin dashboard
  * @return boolean
  * 	true	login successful
  * 	false	login unsuccessful
  */
  function login() {
  	$this->load->library(array('aauth'));
  	
  	$this->errors = array();

	//$this->aauth->update_user(2, 'me@desmondliang.com', '810324', 'Desmond');  	
  	if($this->aauth->login($this->email, $this->password)){
  	//email and password combo found. Load user information.
  		//$this->aauth->set_user_var('org_id','1');
  		return true;
  		
  	}else {
  	//email and password combo not found.
  		$this->errors = $this->aauth->get_errors_array();
  		return false;
  	}
  }

  /**
  * Get a list of campaigns this user can manage.
  * This is a wrapper of organization->get_my_campaign_array(). It will provide more flexiblity in future if a user is allowed to 
  * manage campaigns across organizations.  
  * @return array	an array of campaign objects
  */
  
  function get_my_campaigns_array(){
	$this->load->library(array('aauth'));
	$this->load->model('organization');

	$this->organization = new organization($this->aauth->get_user_var('org_id'));
  	return $this->organization->get_my_campaigns_array();
  }

}

?>