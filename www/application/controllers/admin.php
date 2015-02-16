<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	/**
	login page
	*/
	
	const ERROR_MISSING_CREDENTIAL = 'missing_credential';
	const ERROR_INCORRECT_CREDENTIAL = 'incorrect_credential';
	
  	public function index(){

	$this->load->library(array('aauth'));
	$this->load->helper(array('url'));

	//If user is already logged in, jump straight to dashboard.
	if($this->aauth->is_loggedin()){
	  redirect("admin/dashboard", "location");
	}

	//Handle "err" error messages.
	if(isset($_GET['err'])) {
		switch($_GET['err']) {
			case self::ERROR_MISSING_CREDENTIAL:		
				$data_page['message'] = '<div class="alert alert-danger" role="alert">To log in, please enter both email and password.</div>';
			break;
			
			case self::ERROR_INCORRECT_CREDENTIAL:
				$data_page['message'] = '<div class="alert alert-danger" role="alert">Incorrect email and password combo.</div>';			
			break;
			
			default:
				$data_page['message'] = '';
			break;
		}
	}else {
		$data_page['message'] = '';
	}
	
	$this->load->helper('form');

	$data_page['form'] = form_open('admin/dashboard',array('role'=>'form'));
	$data_page['form'] .= '<div class="form-group">'
	  					.form_label('Email', 'email')
				    	.form_input(array('name'=>'email','id'=>'email', 'class'=>'form-control', 'placeholder'=>''))
	  					.'</div>';

	$data_page['form'] .= '<div class="form-group">'
	  					.form_label('Password', 'pwd')
	  					.form_password(array('name'=>'pwd','id'=>'pwd', 'class'=>'form-control'))
	  					.'</div>';

	$data_page['form'] .= '<div class="form-group">'
						.form_submit(array('name'=>'submit', 'value'=>"Let's go!", 'class'=>'btn btn-default'))
						.'</div>';

	$data_page['form'] .= form_close();

	$data['main_content'] = $this->load->view('admin/login',$data_page,true);
	$this->load->view('template/page', $data);
  }

	/**
	/Sign up for admin account
	/@param $operation
	/	'submit'	create an admin
	/	'confirm'	confirm the creation of an admin
	*/
	public function signup($operation = null) {
		$this->load->helper(array('url','form'));
		$this->load->library(array('aauth'));

		switch($operation) {
			case 'submit':
			//process the sign up request
				 if(($this->input->post("username") == '') OR ($this->input->post("email") == '') OR ($this->input->post("pwd") == '')){
				 	//push back to /admin if no date is passed from the admin page.
				 	redirect("admin/signup", "location");
				 }else{
				 	die($this->aauth->create_user($this->input->post('email'), $this->input->post('pwd'),$this->input->post('username')));
				 }		
			
			break;
			
			case 'confirm':
			break;
			
			default:
				$data_page['form'] = form_open('admin/signup/submit',array('role'=>'form'));

				$data_page['form'] .= '<div class="form-group">'
			  					.form_label('Your name', 'username')
						    	.form_input(array('name'=>'username','id'=>'username', 'class'=>'form-control', 'placeholder'=>'ie. John Smith'))
			  					.'</div>';
			  					
				$data_page['form'] .= '<div class="form-group">'
			  					.form_label('Email', 'email')
						    	.form_input(array('name'=>'email','id'=>'email', 'class'=>'form-control', 'placeholder'=>''))
			  					.'</div>';
		
				$data_page['form'] .= '<div class="form-group">'
			  					.form_label('Password', 'pwd')
			  					.form_password(array('name'=>'pwd','id'=>'pwd', 'class'=>'form-control'))
			  					.'</div>';
		
				$data_page['form'] .= '<div class="form-group">'
								.form_submit(array('name'=>'submit', 'value'=>"Sign Up", 'class'=>'btn btn-default'))
								.'</div>';
		
				$data_page['form'] .= form_close();
		
				$data['main_content'] = $this->load->view('admin/signup',$data_page,true);
				$this->load->view('template/page', $data);
			break;		
		
		}
	}
	
	/**
	/dashboard page: check for user identification. If ID is not presented. Redirect to /admin
	*/
	public function dashboard() {
		$this->load->helper(array('url'));
		$this->load->library(array('aauth'));
		$this->load->model('user');

	    if($this->aauth->is_loggedin()){
	    //If user is already logged in, push the id to a new user object.

		  	$user = new user($this->aauth->get_user_id());

		}else if(($this->input->post("email") == '') OR ($this->input->post("pwd") == '')){
		//If user is no logged in, and no email or password is passed from the previous page.
		//push back to /admin.
		 	redirect("admin?err=".self::ERROR_MISSING_CREDENTIAL, "location");
		}else{
		//If user is not logged in, and email and password are passed from the previous page.
		//Verifiy the user's.
		 	
		 	$user = new user();
		 	$user->email = $this->input->post("email");
		 	$user->password = $this->input->post("pwd");
		 	
			if(!$user->login()){
			//Incorrect login credential. Push back.
				redirect("admin?err=".self::ERROR_INCORRECT_CREDENTIAL, "location");
			}//if
		}//if

	  	//At this point, the user's identify should be verifited.
		$campaigns = $user->get_my_campaigns_array();

	  	//navigation options to view
	  	$data['nav_options'] = array(
		  	array('url'=>'/admin/logout', 'text'=>'Log Out')
		);

	    //pass campaigns to view
		$data_page['campaigns'] = $campaigns;


		$data['main_content'] = $this->load->view('admin/dashboard',$data_page,true);
		$this->load->view('template/page', $data);

	}//function

  /**
   * Display detail information about a campaign
   * @param $campaign_id
   */
  public function campaign($campaign_id){
	$this->load->library(array('aauth'));
	$this->load->helper(array('url'));
	$this->load->model(array('campaign'));

	//$this->load->model('user');

	if(!$this->aauth->is_loggedin()){//if user is not logged in, push back.
	  redirect("/admin", "location");
	}//if

	if(!isset($campaign_id)){//if campaign id is not passed. push back to dashboard .
	  	redirect("/admin/dashboard", "location");
	}//if

	$campaign = new campaign($campaign_id);

	if($campaign->campaign_id == null){//attempt to load campaign information from database failed.

	  $data_page['message'] = 'An attempt to load campaign information from database has failed. <a href="/admin/dashboard">Return to dashboard.</a>';
	  $data['main_content'] = $this->load->view('error',$data_page,true);
	  $this->load->view('template/page', $data);
	}else{

	  //show the campaign information view

	  //navigation options to view
	  $data['nav_options'] = array(
		array('url'=>'/admin/dashboard','text'=>'Dashboard'),
		array('url'=>'/admin/logout', 'text'=>'Log Out')
	  );

	  $data['scripts'] = [
		'vendors/Chart.min.js',
		'pages/admin/campaign.js'
	  ];

	  $data_page['campaign'] = $campaign;
	  $data_page['organization'] = $campaign->getOwnerOrganization();

	  $data['main_content'] = $this->load->view('admin/campaign',$data_page,true);
	  $this->load->view('template/page',$data);
	}

  }

  /**
   * Display detail inforamtion about a member.
   * @param #campaign_id The id of the campaign.
   * @param $member_id	The id of the member
   */
  public function member($campaign_id, $member_id){
	$this->load->library(array('aauth'));
	$this->load->helper(array('url'));
	$this->load->model(array('campaign'));
	$this->load->model(array('member'));

	if(!$this->aauth->is_loggedin()){
	//if user is not logged in, push back.
	  redirect("/admin", "location");
	}//if

	if((!isset($campaign_id)) OR (!isset($member_id))){
	//if campaign id or member id is not presented. push back to dashboard .
	  redirect("/admin/dashboard", "location");
	}//if

	//Prepare information about the campaign
	$campaign = new campaign($campaign_id);
	if($campaign->campaign_id == null){//attempt to load campaign information from database failed.

	  $data_page['message'] = 'An attempt to load campaign information from database has failed. <a href="/admin/dashboard">Return to dashboard.</a>';
	  $data['main_content'] = $this->load->view('error',$data_page,true);
	  $this->load->view('template/page', $data);
	}

	//Prepare information about the member.
	$member = new member();
	if($member->getInfoByID($member_id) == false){
		//unable to get member info. Throw an error.
	  $data_page['message'] = 'An attempt to load member information from database has failed. <a href="/admin/dashboard">Return to dashboard.</a>';
	  $data['main_content'] = $this->load->view('error',$data_page,true);
	  $this->load->view('template/page', $data);
	}

	//navigation options to view
	$data['nav_options'] = array(
	  array('url'=>'/admin/dashboard','text'=>'Dashboard'),
	  array('url'=>'/admin/campaign/'.$campaign_id,'text'=>'Campaign'),
	  array('url'=>'/admin/logout', 'text'=>'Log Out')
	);

	$data['scripts'] = [
	  'pages/admin/member.js'
	];

	$data_page['campaign'] = $campaign;
	$data_page['member'] = $member;

	$data['main_content'] = $this->load->view('admin/member',$data_page,true);
	$this->load->view('template/page',$data);
  }

  /**
   * Log out current user session.
   */
  public function logout(){
	  $this->load->library(array('aauth'));
	  $this->aauth->logout();

	  $data['main_content'] = $this->load->view('admin/logout');
	  $this->load->view('template/page', $data);
	}
}