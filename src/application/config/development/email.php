<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| EMAIL CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file contains the settings needed to access your email server.
|
| NOTE: THESE ARE PHPMAILER SETTINGS IN THIS PROJECT!
*/

$config['smtp_host'] = '10.0.75.2';
$config['smtp_port'] = '1025';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['wordwrap'] = 76;
$config['send_multipart'] = FALSE;

/* End of file email.php */
/* Location: application/config/development/email.php */
