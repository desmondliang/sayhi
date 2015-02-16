<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reward extends CI_Controller {

  public function index(){

  }

  public function generic(){
	//Show the default reward if no specify - a generic thank you message.

	$this->load->helper('cookie');

	$data_page['firstname'] = $this->input->cookie('firstname');

	$data['main_content'] = $this->load->view('reward/default', $data_page ,true);
	$this->load->view('template/page', $data);
  }

  public function welcome(){
  //show the welcome screen after collecting the member's name and email
	$this->load->helper('cookie');

	$data_page['firstname'] = $this->input->cookie('firstname');

	$data['main_content'] = $this->load->view('reward/welcome', $data_page ,true);
	$this->load->view('template/page', $data);
  }

  public function join_org(){
	//Show a welcome screen to someone who's known to the system for joining a new org
	$this->load->helper('cookie');

	$data_page['firstname'] = get_cookie('firstname');

	$data['main_content'] = $this->load->view('reward/join_org', $data_page ,true);
	$this->load->view('template/page', $data);
  }

  public function goal(){
  //A member will end up here when a campaign runs out question to ask.
	$this->load->helper('cookie');

	$data_page['firstname'] = get_cookie('firstname');

	$data['main_content'] = $this->load->view('reward/goal', $data_page ,true);
	$this->load->view('template/page', $data);
  }

}