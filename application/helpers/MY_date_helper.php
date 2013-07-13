<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('shj_now'))
{

	/*
	 * Returns server time (uses time zone in settings table)
	 */
	function shj_now()
	{
		$CI =& get_instance();
		$CI->load->model('settings_model');
		return gmt_to_local(now(),$CI->settings_model->get_setting('timezone'),TRUE);
	}
}


/* End of file MY_date_helper.php */
/* Location: ./application/helpers/date_helper.php */