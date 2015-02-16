<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Response extends CI_Controller {

  /**
   * A member responses to a question
   */
  public function index(){

	$this->load->library('encrypt');
	$this->load->helper('url');
	$this->load->helper('cookie');
	$this->load->database();

	if(($this->input->post('response'))
	  AND $this->input->post('question_id')
	  AND $this->input->post('member_id')
	  AND ($this->input->post('campaign_id'))){
	  //All elements of a response must be present before a response is handled.

	  date_default_timezone_set($this->config->item('default_timezone'));

	  $data = array(
		'question_id' 		=> $this->input->post('question_id'),
		'member_id' 		=> $this->encrypt->decode($this->input->post('member_id')),
		'value' 			=> $this->input->post('response'),
		'created_datetime' 	=> date('Y-m-d H:i:s a', time())
	  );

	  $this->db->insert('response', $data);

	  if(!$this->db->insert_id()){
		//Something we wrong when saving the response to the database;

		$data_page['message'] = "Something went wrong when saving your response.";
		$data['main_content'] = $this->load->view('error', $data_page ,true);
		$this->load->view('template/page', $data);
	  }else{
		//Process the reward
		$this->db->select('reward.data,reward.type');
		//$this->db->from('question');
		$this->db->join('reward','question.reward_id = reward.reward_id','left');
		$this->db->where('question.question_id =', $this->input->post('question_id'));
		$query = $this->db->get('question');


		if($query->num_rows() > 0){
		  $row = $query->result();

		  switch($row[0]->type){
			case 'text':
			  //Reward type = text
				$objTextReward = json_decode($row[0]->data);

				$data_page['message'] = $objTextReward->message;

				$data['main_content'] = $this->load->view('reward/text', $data_page ,true);
				$this->load->view('template/page', $data);

			  break;

			default:
			  //Unknown reward type. Show error message
			  $data_page['message'] = "Reward type unknown.";
			  $data['main_content'] = $this->load->view('error', $data_page ,true);
			  $this->load->view('template/page', $data);

			  break;
		  }

		}else{
		  $data_page['firstname'] = $this->input->cookie('firstname');
		  $data['main_content'] = $this->load->view('reward/default', $data_page ,true);
		  $this->load->view('template/page', $data);
		}

	  }

	}else{
	  //If any information required to process a response is missing. Prompt the user to re-enter name and email address.
	  //A possible causes of this error is expired cookie.
	  redirect("/ask/name", "location");
	}
  }
}