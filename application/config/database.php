<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
These comments removed
*/

$active_group = 'default';
$active_record = TRUE;

/* Database connection settings: */
/* Provide your database connection settings here: */
$db['default']['hostname'] = 'localhost'; // database host
$db['default']['username'] = '';          // database username
$db['default']['password'] = '';          // database password
$db['default']['database'] = '';          // database name
$db['default']['dbprefix'] = 'shj_';      // database table prefix

/* other: */
$db['default']['dbdriver'] = 'mysql';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */