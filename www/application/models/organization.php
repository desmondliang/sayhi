<?php

class Organization extends CI_Model {

  public $org_id;
  public  $name;

  /**
   * Construct an organization object using the organization id provided. If the ID = 0, return a shell.
   * @param int $org_id
   */
  function __construct($org_id = 0){

	// Call the Model constructor
	parent::__construct();

	if($org_id != 0){
		$this->org_id = $org_id;
		
	  //load database class
	  $this->load->database();

	  $query = $this->db->get_where('organization', array('org_id' => $org_id), 1);

	  $row = $query->result();

	  $this->org_id = $row[0]->org_id;
	  $this->name = $row[0]->name;
	}
  }

  /**
   * Find our if a member has a membership with this organization
   * @param $member_id
   * @return bool
   */
  public function isMembershipValid($member_id){

	$this->load->database();

	$query = $this->db->get_where('membership', array('member_id' => $member_id, 'org_id'=> $this->org_id));

	if($query->num_rows() > 0){
	  return true;
	}else{
	  return false;
	}

  }

  /**
   * Add a member to an organization.
   * @param $member
   * @param $organization
   * @return bool
   */
  public function addMember($member){

	date_default_timezone_set($this->config->item('default_timezone'));

	$data = array(
	  'member_id' => $member->member_id,
	  'org_id' => $this->org_id,
	  'member_since' => date('Y-m-d H:i:s a', time())
	);

	$this->db->insert('membership', $data);

	if($this->db->insert_id() != ''){
	  return $this->db->insert_id();
	}else{
	  return false;
	}
  }

  /**
   * Check-in a member to an organization.
   * @param $member
   * @param $organization
   * @return bool
   */
  public function memberCheckIn($member, $campaign_id){

	date_default_timezone_set($this->config->item('default_timezone'));

	$data = array(
	  'member_id' => $member->member_id,
	  'campaign_id' => $campaign_id,
	  'datetime' => date('Y-m-d H:i:s a', time())
	);

	$this->db->insert('checkin', $data);

	if($this->db->insert_id() != ''){
	  return $this->db->insert_id();
	}else{
	  return false;
	}
  }

  /**
   * Return a list of campaigns.
   * @return array a list of campaign dateset objects
   * 				 campaign['campaign_id', 'alias', 'campaign_name', 'org_id','created_datetime','num_members']
   */  
  public function get_my_campaigns_array(){
	$this->load->database();

	//Query all campaign belong to this organization and member count.
	$sql = "Select A.*, count(distinct(B.member_id)) as num_members from campaign as A left join checkin as B on A.campaign_id = B.campaign_id where A.org_id = ?;";
	$query = $this->db->query($sql, array($this->org_id));
	$result=$query->result();
	
	return $result;
  }
}