<?php
/**
 * Sharif Judge online judge
 * @file header.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title><?php echo $title ?> - Sharif Judge</title>
	<meta content="text/html" charset="UTF-8">
	<link rel="icon" href="<?php echo base_url("assets/images/favicon.ico") ?>"/>
	<link rel="stylesheet" type='text/css' href="<?php echo base_url("assets/styles/$style")  ?>"/>
	<link rel="stylesheet" type='text/css' href="<?php echo base_url("assets/styles/splashy.css")  ?>"/>
	<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-1.10.2.min.js") ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("assets/js/moment.min.js") ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.hoverIntent.minified.js") ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.cookie.js") ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.ba-resize.min.js") ?>"></script>
	<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/nano_scroller/nanoscroller.css") ?>'/>
	<script type='text/javascript' src="<?php echo base_url("assets/nano_scroller/jquery.nanoscroller.min.js") ?>"></script>
</head>
<body id="body" class="nano">
<div class="content">