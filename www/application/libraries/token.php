<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Token {

  public function __construct()
  {
	// Do something with $params
  }

  /**
   * package
   *
   * Replace all tokens in $string with values.
   *
   * @param $string
   * @return mixed
   */
  public function package($string){
	//$this->load->library('encrypt');
	//$this->load->helper('cookie');

	$string = str_replace("#FIRSTNAME#", get_cookie("firstname"),$string);

	return $string;
  }
}

?>