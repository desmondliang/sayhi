<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ask extends CI_Controller {
	public function index(){

	}

	public function name()
	{

	  $this->load->helper('form');

	  $fields['firstname'] = array(
								'name'=>'firstname',
								'class'=>'form-group form-control',
								'maxlength'=>'45',
								'data-parsley-required'=>"true"
	  							);

	  $fields['lastname'] = array(
								'name'=>'lastname',
								'class'=>'form-group form-control',
								'maxlength'=>'45',
								'data-parsley-required'=>"true"
	  							);

	  $fields['submit'] = array(
								'class'=>'btn btn-default',
								'value'=>'Next'
	  							);

	  $data_page['form'] = form_open('ask/email',array('role'=>'form'))
	  					  .form_label('First Name', 'firstname').form_input($fields['firstname'])
						  .form_label('Last Name', 'lastname').form_input($fields['lastname'])
						  .form_submit($fields['submit'])
	  					  .form_close();

	  $data['main_content'] = $this->load->view('ask/name', $data_page ,true);
	  $this->load->view('template/page', $data);


	}

  /**
   * Ask the known user a question.
   * @assumption: member_id, org_id, campaign_id exist in the cookie/session
   */
	public function question()
	{
	  	$this->load->library('encrypt');
	  	$this->load->library('session');
	  	$this->load->library('token');
	  	$this->load->helper('url');
	  	$this->load->helper('cookie');
	  	$this->load->helper('form');

	  	//Check if member_id, org_id, campaign_id exist in the cookie/session
		if((!$this->input->cookie('member_id'))
		   OR (!$this->input->cookie('firstname'))
		   OR (!$this->input->cookie('lastname'))
		   OR (!$this->input->cookie('email')))
		{
		  if(($this->input->cookie('firstname'))
			AND ($this->input->cookie('lastname'))
			AND ($this->input->cookie('email')))
		  {//if name and id exist in cookie but email. Ask for email.
			redirect("/ask/email", "location");
		  }else{//if name and id don't exist. Ask for name
		  	redirect("/ask/name", "location");
		  }
		}

	  	//Return all questions that have not been responded.
	  	$this->load->model("Campaign");
	  	$this->load->model("Member");

	    $member = new Member();
	  	$member->member_id = $this->encrypt->decode($this->input->cookie('member_id'));
	  	$member->firstname = $this->input->cookie('firstname');
	  	$member->lastname = $this->input->cookie('lastname');
	  	$member->email = $this->encrypt->decode($this->input->cookie('email'));

	    $my_campaign = new Campaign($this->session->userdata('campaign_id'));

		$question = $my_campaign->getUnrespondedQuestionsByMember($member);//this function only returns one question.

		//Handle when the campaign runs out of the question.
		if(count($question)==0){
		  redirect("/reward/goal", "location");
		}

	    $data_page['question'] = $this->token->package($question[0]->body);

	    //open form
	  	$data_page['form'] = form_open('response');

	  	switch($question[0]->type){
		  case 'text':
			$data_page['form'].=form_input(array(
				'name'        => 'response',
				'id'          => 'response',
			    'class'		  => 'form-control form-group',
			    'data-parsley-required' => 'true',
			    'maxlength'   => '1024',
				'value'       => ''
			  ));
		  break;

		  default:
			//report error.
			$data_page['message'] = "Unknown quest type:".$question[0]->type;
			$data['main_content'] = $this->load->view('error', $data_page ,true);
			$this->load->view('template/page', $data);
			return;
		  break;
		}

	    $data_page['form'] .= form_submit(array('name'=>'submit','class'=>'btn btn-default'), 'Next');
	    $data_page['form'] .= form_hidden('question_id', $question[0]->question_id);
	    $data_page['form'] .= form_hidden('campaign_id', $my_campaign->campaign_id);
	    $data_page['form'] .= form_hidden('member_id', $this->encrypt->encode($member->member_id));

	    $data_page['form'] .= form_close();//close form

		$data['main_content'] = $this->load->view('ask/question',$data_page,true);
		$this->load->view('template/page', $data);
	}

  /**
   * Ask the user for email addresss
   */
  	public function email()
	{

	  $this->load->helper(array('url','form'));

	  if(($this->input->post("firstname") == false) OR ($this->input->post("lastname") == false)){
		//no data is pasted from the last view
		redirect("/ask/name", "location");
	  }else{

		$fields['email'] = array(
			'name'	=> 'email',
		  	'type'	=> 'email',
		  	'class'	=> 'form-group form-control',
		  	'data-parsley-required' => 'true',
		  	'value' => ''
		);

		$fields['submit'] = array(
		  'name' => 'submit',
		  'class' => 'btn btn-default',
		  'value' => 'Next'
		);

		$data_page['form'] = form_open('ask/addmember',array('role'=>'form'))
		  					.form_label('What is your email address?', 'email').form_input($fields['email'])
		  					.form_hidden('firstname',$this->input->post("firstname"))
							.form_hidden('lastname',$this->input->post("lastname"))
							.form_submit($fields['submit'])
							.form_close();


		$data_page['firstname'] = $this->input->post("firstname");

		$data['main_content'] = $this->load->view('ask/email', $data_page ,true);

		$this->load->view('template/page', $data);
	  }
	}

  /**
   * This is call either when a member in the cookie is not in the organization that owns the campaign, or when there
   * is no member in cookie at all.
   */
  	public function addmember(){
	  	//$this->output->enable_profiler(TRUE);

	  	$this->load->helper('url');
	  	$this->load->library('encrypt');
	  	$this->load->library('session');

		$this->load->model("Member");
	  	$this->load->model('Organization');


		if(($this->session->userdata('campaign_id') == '') OR ($this->session->userdata('org_id'))==''){
		  //campaign id and organization id are not found in session
		  //report error.
		  $data_page['message'] = "Campaign and organization info missing in session.";
		  $data['main_content'] = $this->load->view('error', $data_page ,true);
		  $this->load->view('template/page', $data);
		  return;
		}

	  	$new_member = new Member();

	  	if($this->input->post("email")==false){

		  //return to email page if email is not pasted to here
		  redirect("/ask/email","location");

		}else if(($this->input->post("firstname") == false) OR ($this->input->post("lastname") == false)){
		  //return to name page if first name or last name is missing from post.
		  redirect("/ask/name", "location");

		}else{

			$new_member->firstname 	= $this->input->post("firstname");
		  	$new_member->lastname 	= $this->input->post("lastname");
		  	$new_member->email		= $this->input->post("email");


		  	if($new_member->doIExist()){
			//Member information found in DB. Go to welcome back message.

				$this->save_member_in_cookie($new_member);
			  	redirect("/reward/join_org", "location");

			}else{
			//member information not found in DB. insert a new row.
			  	$new_member->signUp();
				$this->save_member_in_cookie($new_member);

				//add a new row to membership, and add a row to checkin
				$new_member_org = new Organization($this->session->userdata('org_id'));
				$new_member_org->addMember($new_member);
				$new_member_org->memberCheckIn($new_member, $this->session->userdata('campaign_id'));

				redirect("/reward/welcome", "location");
			}
		}

	}

  /**
   * Save a Member Object to cookie.
   * @param $member : a Member object
   */
  private function save_member_in_cookie($member){

	$this->load->library('session');

	//If the user choose now to track cookie user informations, skip this step.
	$is_cookie_allowed = $this->session->userdata('allow_cookie');
	if(!$is_cookie_allowed)
	  return;

	$this->load->library('encrypt');
	$this->load->helper('cookie');



	$cookies = array(
	  array(
		'name'   => 'member_id',
		'value'  => $this->encrypt->encode($member->member_id)
	  ),
	  array(
		'name'   => 'firstname',
		'value'  => $member->firstname
	  ),
	  array(
		'name'   => 'lastname',
		'value'  => $member->lastname
	  ),
	  array(
		'name'   => 'email',
		'value'  => $this->encrypt->encode($member->email)
	  )
	);

	foreach($cookies as $cookie){
	  setcookie($cookie['name'],
				$cookie['value'],
				time()+$this->config->item('cookie_expire'),
				$this->config->item('cookie_path'),
				$this->config->item('cookie_domain'));
	}
  }
}//class
