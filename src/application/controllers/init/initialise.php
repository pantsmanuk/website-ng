<?php

//benchmarking on
//WARNING: TURNING THIS ON PRODUCTION SERVER WILL BREAK ALL REMOTE XML TRANSFER INCLUDING FLOGGER AND FLIGHTMAP!

//restricted to Alexander
if (isset($this->session->userdata['username']) && $this->session->userdata['username'] == '1997') {
//	$this->output->enable_profiler(TRUE);
}

//Global initialisation file
$data['version'] = '2.0';

//cache in minutes
$cache_duration_normal = 5;

//load all required libraries
$this->load->library('View_fns');

$this->load->library('form_validation');
$this->load->helper('security');

//grab useful variables
$data['time_unix'] = time();
$data['time_mysql'] = gmdate("Y-m-d H:i:s", $data['time_unix']);
$data['forum_url'] = 'https://www.fly-euroharmony.com/forum/';
$data['base_url'] = $this->config->item('base_url') . $this->config->item('index_page') . '/';
$data['base_url_minimal'] = $this->config->item('base_url');
$data['tmpl_global_url'] = $this->config->item('base_url') . 'application/views/global/';
$data['assets_url'] = $this->config->item('base_url') . 'assets/';
$data['assets_path'] = $this->config->item('base_path') . '/assets/';
$data['flash_url'] = $this->config->item('base_url') . 'assets/swf/';
$data['base_path'] = $this->config->item('base_path');
$data['template'] = $this->config->item('template');

/*
//handle mobile skin vs normal
if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/iphone|Android|Blackberry/i", $_SERVER['HTTP_USER_AGENT'])) { 
	$data['template'] = 'mobile'; 
}
*/

include_once($this->config->item('full_base_path') . 'application/views/templates/' . $data['template'] . '/template_config.php');

//reCAPTCHA
$data['recaptcha_public'] = $this->config->item('recaptcha_public');
$data['recaptcha_private'] = $this->config->item('recaptcha_private');

//logged in
//$data['logged_in'] = $this->session->userdata('logged_in');

//tmp upload path
$tmp_upload_path = $data['base_path'] . 'assets/uploads/tmp/';
$data['tmp_upload_path'] = $tmp_upload_path;

//set defaults
$data['col2'] = '';
$data['menu_main'] = '';

//grab config data
$data['flash_vars'] = '';

//template data
$data['image_url'] = $this->config->item('base_url') . 'assets/images/';
$data['tmpl_image_url'] = $this->config->item('base_url') . 'assets/images/templates/' . $data['template'] . '/';
$data['image_path'] = $data['base_path'] . 'assets/images/';
$data['tmpl_global_path'] = $data['base_path'] . 'application/views/global/';
$data['tmpl_main_width'] = $data['tmpl_cont_width'] - $data['tmpl_menu_width'] - ($data['tmpl_main_padding'] * 2);
$data['full_base_url'] = $data['base_url'] . $this->config->item('index_page');
$data['full_base_path'] = $data['base_path'] . 'index.php';
$data['view_base_path'] = $data['base_path'] . 'application/views/';

//time
//these are inactive compares
$pp_compare_date = gmdate('Y-m-d', strtotime('-2 days'));
$month_compare_date = gmdate('Y-m-d', strtotime('-1 month'));
$active_compare_date = gmdate('Y-m-d', strtotime('-90 days'));
$active_compare_datetime = gmdate('Y-m-d h:m:s', strtotime('-90 days'));
$ppstats_compare_datetime = gmdate('Y-m-d h:m:s', strtotime('-90 days'));

$data['pp_compare_date'] = $pp_compare_date;
$data['month_compare_date'] = $month_compare_date;
$data['active_compare_date'] = $active_compare_date;
$data['active_compare_datetime'] = $active_compare_datetime;

//sql insert
$gmt_mysql_datetime = gmdate("Y-m-d H:i:s", time());
$data['gmt_mysql_datetime'] = $gmt_mysql_datetime;
//admincp timeout
$acp_timeout = (60 * 90);
$data['acp_timeout'] = $acp_timeout;
//javascript
$data['js_loader'] = '';

$featured_video_array = array(
	'https://www.youtube.com/watch?v=uO5z_FmR5No',
	'https://www.youtube.com/watch?v=NYOjrqbxydQ',
	'https://www.youtube.com/watch?v=gHVgwpUyJBU',
	'https://www.youtube.com/watch?v=qeh4mDmCPqw',
	'https://www.youtube.com/watch?v=LIkE2o-ghjA',
	'https://www.youtube.com/watch?v=tJ8uXhkchI0',
	'https://www.youtube.com/watch?v=tPomU9_8WAo',
	'https://www.youtube.com/watch?v=5rlkkPTuC4A',
	'https://www.youtube.com/watch?v=sCfDl0tc_SE',
	'https://www.youtube.com/watch?v=kmetbvY-Vg0',
	'https://www.youtube.com/watch?v=JZ24olrnRGU',
	'https://www.youtube.com/watch?v=g_aCS5Cjcvg',
	'https://www.youtube.com/watch?v=xc6EGuq8F-E',
	'https://www.youtube.com/watch?v=cQWwKiQHmV8',
	'https://www.youtube.com/watch?v=zzAD0HTvcus',
	'https://www.youtube.com/watch?v=Ggz4u647YD0',
	'https://www.youtube.com/watch?v=S5JHPES1CyA',
	'https://www.youtube.com/watch?v=Q0ZGdvox6No',
	'https://www.youtube.com/watch?v=aoq0ChqtcSw',
	'https://www.youtube.com/watch?v=Kzo7u3cnrBc',

);

//overwrite to feature the anniversary vid
/*
$featured_video_array = array(
'https://www.youtube.com/watch?v=Kzo7u3cnrBc',
);
*/

$data['flogger_latest'] = $data['forum_url'] . 'index.php?action=media;sa=media;in=1275;dl';
$data['ops_manual_link'] = $data['assets_url'] . 'files/manuals/ehm_ops_manual_2012.pdf';

$data['flogger_version'] = '4.1.8';

//load libraries
$this->load->library('Format_fns');
