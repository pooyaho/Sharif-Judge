<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get "now" time
 *
 * Returns time() or its GMT equivalent based on the config file preference
 *
 * @access	public
 * @return	integer
 */

if ( ! function_exists('shj_now'))
{
	function shj_now()
	{
		$CI =& get_instance();
		$CI->load->model('settings_model');
		return gmt_to_local(now(),$CI->settings_model->get_timezone(),TRUE);
	}
}


/* End of file MY_date_helper.php */
/* Location: ./application/helpers/date_helper.php */