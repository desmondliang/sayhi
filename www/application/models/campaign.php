<?php

class Campaign extends CI_Model {
  public $campaign_id = null;
  public $alias = null;
  public $campaign_name;
  public $org_id;
  public $questions = array();//questions in this campaign

  public $num_members = null;
  public $num_checkins = null;

  private $create_datetime;

  /**
   * Create a campaign object base on the campaign provided. If campaign ID is not provided. The object is a shell.
   * @param int $campaign_id
   * @return object The campaign object if the campaign can be found in the database.
   */
  function __construct($campaign_id = 0){

	// Call the Model constructor
	parent::__construct();

	$this->load->database();

	//load the campaign information from database
	if($campaign_id != 0){
	  $query = $this->db->get_where('campaign', array('campaign_id' => $campaign_id), 1);

	  if($query->num_rows()>0){
		$row = $query->result();

		$this->campaign_id 	    = $row[0]->campaign_id;
		$this->campaign_name 	= $row[0]->campaign_name;
		$this->created_date 	= $row[0]->created_datetime;
		$this->org_id 			= $row[0]->org_id;
		$this->alias			= $row[0]->alias;

		//Gather basic information about the campaign
		$sql = "Select count(distinct(member_id)) as num_members, count(*) as num_checkins from checkin where campaign_id = ?;";
		$query = $this->db->query($sql, array($this->campaign_id));
		$row = $query->result();

		$this->num_members = $row[0]->num_members;//total member count
		$this->num_checkins = $row[0]->num_checkins;//total check in count

	  }else{
		return;//return an empty object
	  }
	}else{
	  return;//return an empty object
	}
  }

  /**
   * get URL to the campaign.
   * @param bool $use_alias	Whether or not to use alias or campaign ID.
   * @return String if $use_alias is TRUE use alias (i.e. using alias domain.com/c/load/sfbridage) otherwise use campaign id to construct  domain.com/c/load/1)
   */
  public function getURL($use_alias = true){
	if($use_alias){
	  return $this->config->item('base_url').'/c/load/'.$this->alias;
	}else{
		return $this->config->item('base_url').'/c/load/'.$this->campaign_id;
	}
  }

  /**
   * Get an Organization that owns the campagin

   * @return Organization
   */
  public function getOwnerOrganization(){

	$this->load->model('Organization');

	$my_org = new Organization($this->org_id);
	return $my_org;
  }

  /**
   * Get a list of question in current campaign that had not been responded by member
   * @param $member
   * @return mixed
   */
  public function getUnrespondedQuestionsByMember($member){

	$this->load->database();

	//Get a list of response submitted by $member
	$this->db->select('*');
	$this->db->from('response');
	$this->db->where('member_id', $member->member_id);

	$query_response = $this->db->get();


	//put together a list questions that have been responded by the member
	if($query_response->num_rows() > 0){
	  $response_result = $query_response->result();
	  $member_responses = array();

	  foreach($response_result as $row){
		array_push($member_responses, $row->question_id);
	  }

	  //Get a list of unanswered questions in campaign
	  $this->db->select('campaign_id, campaign_questions.question_id, order, type, body, rank');
	  $this->db->from('campaign_questions');
	  $this->db->join('question', 'campaign_questions.question_id = question.question_id');
	  $this->db->where('campaign_id', $this->campaign_id);
	  $this->db->where_not_in('campaign_questions.question_id', $member_responses);
	  $this->db->limit(1);
	  $this->db->order_by('order', 'asc');

	  $query = $this->db->get();

	  return $query->result();
	}else{
	 //the member hasn't answered any question
	  //Get a list of  questions in
	  $this->db->select('campaign_id, campaign_questions.question_id, order, type, body, rank');
	  $this->db->from('campaign_questions');
	  $this->db->join('question', 'campaign_questions.question_id = question.question_id');
	  $this->db->where('campaign_id', $this->campaign_id);
	  $this->db->limit(1);
	  $this->db->order_by('order', 'asc');

	  $query = $this->db->get();

	  return $query->result();
	}
  }

  /**
   * Get all members signed into this campaign
   * @return mixed Return a data set of all members signed in to this campaign. False if no one has signed into this campaign.
   */
  public function getMembers($limit  = null, $offset = 0){
	$this->load->database();

	//sql: return all member from a specific campaign order by last check in date time.
	$this->db->select('B.member_id, B.firstname, B.lastname, B.email, max(A.datetime) as last_checkin');
	$this->db->from('checkin as A');
	$this->db->join('member as B', 'A.member_id = B.member_id','left');
	$this->db->where('A.campaign_id', $this->campaign_id);
	$this->db->group_by('B.member_id');
	$this->db->order_by('A.datetime','desc');

	if(isset($limit)){
		$this->db->limit($limit, $offset);
	}

	$query = $this->db->get();

	if($query->num_rows() > 0){
	  return $query->result();
	}else{
	  return false;
	}
  }

  /**
   * Get most recent check in list. default limit to 100 rows.
   *
   * @param int $limit
   * @return mixed Rows object that contains information of the most recent check ins. False if the dataset is empty.
   */
  public function getRecentCheckIns($limit = null, $offset = 0){
	$this->load->database();

	//sql: return all recent check ins by datetime.

	$this->db->select('B.member_id, B.firstname, B.lastname, B.email, A.datetime as last_checkin');
	$this->db->from('checkin as A');
	$this->db->join('member as B', 'A.member_id = B.member_id','left');
	$this->db->where('A.campaign_id', $this->campaign_id);
	$this->db->order_by('A.datetime','desc');

	if(isset($limit)){
	  $this->db->limit($limit, $offset);
	}else{
	  $this->db->limit(100, $offset);//default check in history to 100 for performance.
	}

	$query = $this->db->get();

	if($query->num_rows() > 0){
	  return $query->result();
	}else{
	  return false;
	}
  }

  /**
   * count all check ins of this campaign by a specific time span. Default by 'month'
   * @param string $data_group_by	The time span the results should be grouped by. month (default), day, hour
   *
   * @return mixed	Query result set
   */
  public function countCheckInsBy($data_group_by = 'month'){

	switch($data_group_by){
	  case 'month':
		$mysql_group_by = "DATE_FORMAT(A.datetime, ('%Y-%m'))";
		break;

	  case 'day':
		$mysql_group_by = "DATE_FORMAT(A.datetime, ('%Y-%m-%d'))";
		break;

	  case 'hour':
		$mysql_group_by = "DATE_FORMAT(A.datetime, ('%Y-%m-%d %h'))";
		break;
	}

	$this->load->database();

	//sql: return all recent check ins by datetime.

	$this->db->select($mysql_group_by." as label, COUNT(*) as num");
	$this->db->from('checkin as A');
	$this->db->where('A.campaign_id', $this->campaign_id);
	$this->db->group_by($mysql_group_by);
	$this->db->order_by('A.datetime','asc');

	$query = $this->db->get();

	return $query->result();
  }


  /**
   * Count all members of this campaign by a specific time span. Default by 'month'
   * @param string $data_group_by
   * @return mixed	Query result set
   */
  public function countNewMembersBy($data_group_by = 'month'){
	switch($data_group_by){
	  case 'month':
		$mysql_group_by = "DATE_FORMAT(A.datetime, ('%Y-%m'))";
		break;

	  case 'day':
		$mysql_group_by = "DATE_FORMAT(A.datetime, ('%Y-%m-%d'))";
		break;

	  case 'hour':
		$mysql_group_by = "DATE_FORMAT(A.datetime, ('%Y-%m-%d %h'))";
		break;
	}

	$this->load->database();

	//sql: return all recent check ins by datetime.

	$this->db->select($mysql_group_by." as label, COUNT(*) as num");
	$this->db->from('(SELECT * FROM checkin GROUP BY member_id) as A');
	$this->db->where('A.campaign_id', $this->campaign_id);
	$this->db->group_by($mysql_group_by);
	$this->db->order_by('A.datetime','asc');

	$query = $this->db->get();

	return $query->result();
  }
}
