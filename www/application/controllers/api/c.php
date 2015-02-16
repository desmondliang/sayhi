<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class campaign This API response to request related to campaigns.
 */
class C extends CI_Controller {

  public function index(){}

  /**
   * Return all members in a specific campaign.
   * @param $campaign_id
   */
  public function get_members($campaign_id = null){

	if(!isset($campaign_id)){
		echo '{"error":"fatal: Campaign id is missing."}';
	    die();
	}

	$this->load->model('campaign');

	$mycampaign = new campaign($campaign_id);

	$data = $mycampaign->getMembers();

	print_r(json_encode($data));
  }

  /**
   * Return all check ins in a specific campaign.
   * @param $campaign_id int The ID of the campaign.
   * @param $num_rows	int Max number of rows included in the result set.
   */
  public function get_checkins($campaign_id = null, $num_rows = 100){

	if(!isset($campaign_id)){
	  echo '{"error":"fatal: Campaign id is missing."}';
	  die();
	}

	$this->load->model('campaign');

	$mycampaign = new campaign($campaign_id);

	$data = $mycampaign->getRecentCheckIns($num_rows);

	print_r(json_encode($data));
  }

  /**
   * Get a data set that is used to generate a line chart using chart.js
   * @param null $campaign_id
   * @param null $data_group_by 			How the data will be devided by ( ie. month, day, hour ). If no specified, the controller will decide on its own.
   * @param null $start_datetime	The date & time after which all check-ins should be included in the dataset.
   * @param null $ene_datetime		The date & time before which all check-ins should be included in the dataset.
   * @return mixed	array['label'=>value, 'lable' => value]
   */
  public function get_checkin_chart_dataset($campaign_id = null, $data_group_by = 'month', $start_datatime = null, $end_datetime = null){

	if(!isset($campaign_id)){
	  echo '{"error":"fatal: Campaign id is missing."}';
	  die();
	}


	//determine the scale of the labels ( ie. month, day, hour)
	//TODO: dynamically decide how the data should be grouped if $data_group_by is not provided
	/*
	$first_entry = $data[0]->last_checkin;
	$last_entry = $data[sizeof($data)-1];

	if(!isset($scale)){//$scale of the chart is not specified. This controller must make a decision.
	  $days_btw_first_last = ($last_entry - $first_entry)/(60*60*24);//Calculate the number of days between first and last check in.

	  if($days_btw_first_last > 180){
		// more than 6 months. Use month as default threshold.
		$scale  = 'month';
	  }

	  if(($days_btw_first_last > 0) && ($days_btw_first_last < 180)){
		//more than 0 days but less than 6 months. Use day as default threshold
		$scale = 'day';
	  }

	  if($days_btw_first_last < 0){
		//Less than a day. Use hour as default thredshold.
		$scale = 'hour';
	  }
	}
	*/

	$this->load->model('campaign');
	$mycampaign = new campaign($campaign_id);
	$data = $mycampaign->countCheckInsBy($data_group_by);

	$data = array('span'=>$data_group_by, 'data'=>$data);

	print_r(json_encode($data));
  }

  /**
   * Get a data set that is used to generate a line chart to show membership trend using chart.js
   * @param null $campaign_id
   * @param null $data_group_by 			How the data will be devided by ( ie. month, day, hour ). If no specified, the controller will decide on its own.
   * @param null $start_datetime	The date & time after which all check-ins should be included in the dataset.
   * @param null $ene_datetime		The date & time before which all check-ins should be included in the dataset.
   * @return mixed	array['label'=>value, 'lable' => value]
   */
  public function get_new_members_chart_dataset($campaign_id = null, $data_group_by = 'month', $start_datatime = null, $end_datetime = null){

	if(!isset($campaign_id)){
	  echo '{"error":"fatal: Campaign id is missing."}';
	  die();
	}


	//determine the scale of the labels ( ie. month, day, hour)
	//TODO: dynamically decide how the data should be grouped if $data_group_by is not provided
	/*
	$first_entry = $data[0]->last_checkin;
	$last_entry = $data[sizeof($data)-1];

	if(!isset($scale)){//$scale of the chart is not specified. This controller must make a decision.
	  $days_btw_first_last = ($last_entry - $first_entry)/(60*60*24);//Calculate the number of days between first and last check in.

	  if($days_btw_first_last > 180){
		// more than 6 months. Use month as default threshold.
		$scale  = 'month';
	  }

	  if(($days_btw_first_last > 0) && ($days_btw_first_last < 180)){
		//more than 0 days but less than 6 months. Use day as default threshold
		$scale = 'day';
	  }

	  if($days_btw_first_last < 0){
		//Less than a day. Use hour as default thredshold.
		$scale = 'hour';
	  }
	}
	*/

	$this->load->model('campaign');
	$mycampaign = new campaign($campaign_id);
	$data = $mycampaign->countNewMembersBy($data_group_by);

	$data = array('span'=>$data_group_by, 'data'=>$data);

	print_r(json_encode($data));
  }
}