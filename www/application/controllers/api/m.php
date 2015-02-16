<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class campaign This API response to request related to a member.
 */
class M extends CI_Controller {

  public function index(){}


  /**
   * Get responses by a member across campaign.
   * @param null $member_id
   */
  public function get_responses($member_id = null){
	if(!isset($member_id)){
	  echo '{"error":"fatal: Member id is missing."}';
	  die();
	}

	$this->load->model('member');

	$member = new member();

	if($member->getInfoByID($member_id) == false){
	  echo '{"error":"fatal: Member id is invalid."}';
	  die();
	}

	$responses = $member->getMyResponses();

	print_r(json_encode($responses));
  }

  public function get_checkins($member_id = null){
	if(!isset($member_id)){
	  echo '{"error":"fatal: Member id is missing."}';
	  die();
	}

	$this->load->model('member');

	$member = new member();

	if($member->getInfoByID($member_id) == false){
	  echo '{"error":"fatal: Member id is invalid."}';
	  die();
	}

	$responses = $member->getMyCheckins();

	print_r(json_encode($responses));
  }
}