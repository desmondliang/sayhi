<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C extends CI_Controller {

  public $campaign_id;
  public $campaign_name;
  private $org_id;
  public $created_date;
  public $organization;

  public function index(){
		//TODO: Need to add default behavior.
  }

  /**
   * Load a campaign. If member in the cookie is in the organization that owns the campaign, ask a question.
   * If member in cookie isn't in the organization that owns the campaign, clear the cookie and start over from welcome.
   *
   * @param $campaign_id
   * @param $allow_cookie mixed	Whether or not to cookie the member after sign up.
   * 								null default, enable cookie
   * @return bool
   */
  public function load($campaign_id = 0, $allow_cookie = null){

	$this->load->library('encrypt');
	$this->load->helper('cookie');
	$this->load->helper('url');

	$this->load->library('session');

	$this->load->model("Member");
	$this->load->model("Campaign");

	//load database class
	$this->load->database();


	//enable/disable cookie globally. This is useful when people share one sign up station.
	if(is_null($allow_cookie)){
	  $this->session->set_userdata(array('allow_cookie' => true));
	}else{
	  $this->session->set_userdata(array('allow_cookie'=> false));
	}

	if(!is_numeric($campaign_id)){
	  //campaign is an alias. Get the campaign id from database
	  $this->db->select('campaign_id');
	  $this->db->where('alias', $campaign_id);
	  $query = $this->db->get('campaign');

	  if($query->num_rows() > 0 ){
 	  //Found the campaign id associated to the alias

		$rows = $query->result();
		$campaign_id = $rows[0]->campaign_id;
	  }else{
	  //can't find an campaign id associated to the alias
		$data_page['message'] = "Invalid campaign alias.";
		$data['main_content'] = $this->load->view('error', $data_page ,true);
		$this->load->view('template/page', $data);
		return;
	  }
	}

	$my_campaign = new Campaign($campaign_id);

	//Check if campaign exist.
	if(is_null($my_campaign->campaign_id)){
	  //campaign is not in the database. Critical error.
	  $data_page['message'] = "Campaign Not Found.";
	  $data['main_content'] = $this->load->view('error', $data_page ,true);
	  $this->load->view('template/page', $data);
	  return;
	}

	$campaign_owner_org = $my_campaign->getOwnerOrganization();

	//Check if anyone is in the cookie
	if((get_cookie('member_id') != '') AND (get_cookie('firstname') != '') AND (get_cookie('lastname')!='') AND (get_cookie('email')!='')){
	  //Found member in cookie. Check if the user is already known to the system

	  $member_cookie = new Member();
	  $member_cookie->member_id 	= $this->encrypt->decode(get_cookie("member_id"));
	  $member_cookie->firstname 	= get_cookie("firstname");
	  $member_cookie->lastname 	= get_cookie("lastname");
	  $member_cookie->email		= $this->encrypt->decode(get_cookie("email"));

	  if($member_cookie->doIExist()){
		//Member is know to the system. Check if member in cookie is known to the campaign.
		if($campaign_owner_org->isMembershipValid($this->encrypt->decode(get_cookie('member_id')))){

		  $campaign_owner_org->memberCheckIn($member_cookie, $my_campaign->campaign_id);

		  //put the campaign and organization inform in session
		  $this->session->set_userdata(array('campaign_id'=>$my_campaign->campaign_id));
		  $this->session->set_userdata(array('org_id'=> $campaign_owner_org->org_id));

		  //Goto Ask Question
		  redirect("/ask/question", "location");

		}else{
		  //membership is valid => member in cookie doesn't belong to an organization. Create a new session and go to the welcome screen,
		  //confirm member information in cookie.

		  $campaign_owner_org->addMember($member_cookie);

		  //refresh the cookie
		  $cookies = array(
			array(
			  'name'   => 'member_id',
			  'value'  => $this->encrypt->encode($member_cookie->member_id)
			),
			array(
			  'name'   => 'firstname',
			  'value'  => $member_cookie->firstname
			),
			array(
			  'name'   => 'lastname',
			  'value'  => $member_cookie->lastname
			),
			array(
			  'name'   => 'email',
			  'value'  => $this->encrypt->encode($member_cookie->email)
			)
		  );

		  foreach($cookies as $cookie){
			setcookie($cookie['name'], $cookie['value'], time()+$this->config->item('cookie_expire'), $this->config->item('cookie_path'));
		  }

		  //put the campaign and organization inform in session
		  $this->session->set_userdata(array('campaign_id'=>$campaign_id));
		  $this->session->set_userdata(array('org_id'=> $this->organization->org_id));
		  redirect("/reward/join_org", "location");
		}
	  }else{
		//member info found in the cookie but the member can't be found in the database
		//empty the cookies
		delete_cookie("member_id");
		delete_cookie("firstname");
		delete_cookie("lastname");
		delete_cookie("email");

		//goto ask/name
		redirect("/ask/name", "location");
	  }

	}else{
	  //no member in cookie. Create a new session and ask for the user's name
	  $this->session->set_userdata(array('campaign_id'=>$campaign_id, 'org_id'=> $campaign_owner_org->org_id));
	  redirect("/ask/name", "location");
	}
  }
}