<?php

class Member extends CI_Model {

	public  $member_id;

  	public $member_id_hash; //md5($member_id)
	public $firstname;
  	public $lastname;
  	public $email;

  function __construct(){

  	// Call the Model constructor
  	parent::__construct();

  }

  /**
   * Load the member's information from the database
   * @param $member_id
   *
   * @return bool	True if execution is successful
   * 				False if execution is not successful
   */
  public function getInfoByID($member_id){
	$this->load->database();

	$query = $this->db->get_where('member', array('member_id' => $member_id), 1);
	$row = $query->result();

	if($query->num_rows() > 0 ){
	  $this->member_id = $row[0]->member_id;//for later to add user back to cookie
	  $this->firstname = $row[0]->firstname;
	  $this->lastname = $row[0]->lastname;
	  $this->email 	= $row[0]->email;

	  return true;
	}else{
	  return false;
	}
  }

  /**
   * Get a list of response by a member to across campaigns.
   * @return mixed
   */
  public function getMyResponses(){

	$this->load->database();

	//sql: return all recent check ins by datetime.

	$this->db->select("C.body as question, B.order, A.value as response, A.created_datetime");
	$this->db->from('response as A');
	$this->db->join('campaign_questions as B','A.question_id = B.question_id','left');
	$this->db->join('question as C','A.question_id = C.question_id','left');
	$this->db->where('A.member_id', $this->member_id);
	$this->db->order_by('A.created_datetime','asc');

	$query = $this->db->get();

	return $query->result();
  }

  /**
   * Get check in history of this member
   */
  public function getMyCheckins(){
	$this->load->database();

	//sql: return all recent check ins by datetime.

	  $this->db->select("datetime");
	$this->db->from('checkin');
	$this->db->where('member_id', $this->member_id);
	$this->db->order_by('datetime','asc');

	$query = $this->db->get();

	return $query->result();
  }

  /**
   *
   * Find out if a user exists in the database based on the info provided.
   *
   * @param $firstname
   * @param $lastname
   * @param $email
   * @return bool True if user exist in the database, false if not.
   */
  public function doIExist(){

	//load database class
	$this->load->database();

	$query = $this->db->get_where('member', array('firstname' => $this->firstname, 'lastname' => $this->lastname, 'email' => $this->email), 1);
	$row = $query->result();


	if($query->num_rows() > 0 ){
	  	$this->member_id = $row[0]->member_id;//for later to add user back to cookie
	  	return true;
	}else{
	  	return false;
	}

  }

  /**
   *
   * Search the member's ID in the database using contact info.
   *
   * @return bool	The member's ID if member exists. False if member doesn't exist.
   */

  public function getIDByInfo(){
	//load database class
	$this->load->database();

	$this->db->select('member_id');

	$this->db->where(array('firstname' => $this->firstname, 'lastname' => $this->lastname, 'email' => $this->email));
	$result = $this->db->get('member');

	if($result->num_row() > 0 )
	  return $result->row()->member_id;
	else
	  return false;
  }

  public function signUp(){

	$this->load->database();

	$data = array(
	  'firstname' => $this->firstname,
	  'lastname' => $this->lastname,
	  'email' => $this->email
	);

	$this->db->insert('member', $data);

	$this->member_id = $this->db->insert_id();

	return $this->member_id;
  }

}//class

?>