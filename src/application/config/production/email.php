<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| EMAIL CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file contains the settings needed to access your email server.
|
| NOTE: THESE ARE PHPMAILER SETTINGS!
*/
$config['smtp_host'] = 'localhost';
$config['smtp_port'] = '25';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['wordwrap'] = 76;
$config['send_multipart'] = FALSE;

/* End of file email.php */
/* Location: application/config/production/email.php */