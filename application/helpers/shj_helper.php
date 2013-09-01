<?php
/**
 * Sharif Judge online judge
 * @file shj.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');


if ( ! function_exists('shj_now'))
{
	/*
	 * Returns server time (uses time zone in settings table)
	 */
	function shj_now()
	{
		$CI =& get_instance();
		$CI->load->model('settings_model');
		$now = new DateTime('now', new DateTimeZone($CI->settings_model->get_setting('timezone')));
		sscanf($now->format('j-n-Y G:i:s'), '%d-%d-%d %d:%d:%d', $day, $month, $year, $hour, $minute, $second);
		return mktime($hour, $minute, $second, $month, $day, $year);
	}
}


if ( ! function_exists('filetype_to_extension'))
{

	/*
	 * Converts code type to file extension
	 */
	function filetype_to_extension($filetype)
	{
		$filetype = strtolower($filetype);
		switch ($filetype) {
			case 'c': return 'c';
			case 'cpp': return 'cpp';
			case 'py2': return 'py';
			case 'py3': return 'py';
			case 'java': return 'java';
			case 'zip': return 'zip';
			default: return FALSE;
		}
	}
}


/* End of file shj.php */
/* Location: ./application/helpers/shj.php */