<?php

/**
 * Membership GDPR Consent Controller
 *
 * @author Murray Crane <murray.crane@gmail.com>
 * @copyright 2018 (c) Euroharmony VA
 * @license https://tldrlegal.com/license/bsd-3-clause-license-%28revised%29#fulltext
 * @version 0.1
 *
 * @colophon Three stage process:
 *             1. Pre-populate the pilots table with a "random value",
 *             2. Send the secret to the pilot via email, and
 *             3. Get back the secret from the pilot via a hyperlink.
 */
class Gdpr extends CI_Controller {

	static $c_head = '<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Euroharmony VA and GDPR - Consent to future emails</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" type="text/css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous" type="text/css" />
	<link rel="stylesheet" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css" type="text/css" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<style type="text/css">body {width: 640px; font-family: Roboto, sans-serif; font-size: 16px;}</style>
    <script src="//code.jquery.com/jquery-1.12.4.js" type="application/javascript"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js" type="application/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous" type="application/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js" type="application/javascript"></script>
</head>
<body>
	<div class="container">';

	static $c_foot = '	</div> <!-- Container -->
</body>
</html>';

	function __construct() {
		parent::__construct();
	}

	/**
	 *    For production, until we upgrade it to CI 3.1.X
	 *
	 * function Gdpr()
	 * {
	 * parent::__construct();
	 * } */

	/**
	 * Pre-populate the pilot table with a "random value" we can get back from pilots
	 */
	function prepop() {
		// Has the database table already been altered?
		$query = $this->db->query("
	SELECT *
	FROM information_schema.COLUMNS
	WHERE
	TABLE_SCHEMA = 'euroharmony_ng'
	AND TABLE_NAME = 'pilots'
	AND COLUMN_NAME = 'crc32'
		");
		$query->result_array();
		$num_results = $query->num_rows();
		unset($query);

		// If no result, alter the database table
		if ($num_results < 1) {
			$this->db->query("
	ALTER TABLE `pilots`
	ADD COLUMN `crc32` VARCHAR(8) NOT NULL AFTER `authcode`
			");
			$num_rows = $this->db->affected_rows();

			// Something went wrong?
			if ($num_rows < 1) {
				log_message('info', 'gdpr_controller: prepop: error altering database table.');
			} else {
				log_message('info', 'gdpr_controller: prepop: altered pilots table.');
			}
		} else {
			log_message('info', 'gdpr_controller: prepop: pilots table already altered.');
		}

		// Process all pilots
		$query = $this->db->query("
	SELECT `pilots`.`id`, `pilots`.`username`, `pilots`.`password`
	FROM `pilots`
	WHERE `pilots`.`email_valid`=1
	ORDER BY `pilots`.`id`
		");

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				// Perform update - output of get_crc32 to pilots.crc32
				$this->db->where('id', $row->id);
				$this->db->update('pilots', array("receive_emails" => 0, "crc32" => $this->get_crc32()));
			}
		}
	}

	/**
	 * Our last marketing "mail bomb" before GDPR comes into effect.
	 *
	 * NOTE: We're going to be sending emails to people who may have chosen to "unsub" in the past.
	 */
	function send_emails() {
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		set_time_limit(600);

		$query = $this->db->query("
	SELECT `pilots`.`username`, `pilots`.`fname`, `pilots`.`emailaddress`,`pilots`.`crc32`
	FROM `pilots`
	WHERE `pilots`.`email_valid`=1
	ORDER BY `pilots`.`username`
		");

		$t_email_count = 1;
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				// Construct the email message
				$t_policy_url = $data['forum_url'] . "index.php/topic,7816.0.html";
				$t_landing_url = $data['base_url'] . "gdpr/landing_page/" . $row->username . "~" . $row->crc32;
				$t_email_data['to'] = $row->emailaddress;
				$t_email_data['subject'] = 'Euroharmony VA and GDPR - Consent to future emails';
				$t_email_data['message'] = Gdpr::$c_head . PHP_EOL . '<div class="row">';
				$t_email_data['message'] .= '<p>Hi ' . $row->fname . ',</p>';
				$t_email_data['message'] .= '<p>First off, if you had previously indicated that you don&rsquo;t want to receive emails from the VA, I personally apologise for this but this could well be the last email you receive from the VA and it&rsquo;s kind of important we keep you informed one last time.</p>' . PHP_EOL;
				$t_email_data['message'] .= '<p>Secondly, yes, this is Yet Another GDPR Email. The EU&rsquo;s GDPR regulations come into effect on 25 May, and if we want to be able to send future "marketing" emails from the VA to you, we require your informed consent to send them. We don&rsquo;t send such emails very often, so it&rsquo;s not like we&rsquo;ll be a regular fixture in your inbox. We also respect your rights, and have implemented a [ <a href="' . $t_policy_url . '">privacy policy</a> ] that details our possible usage of your personal data.</p>' . PHP_EOL;
				$t_email_data['message'] .= '<p>If you&rsquo;re happy to continue receiving emails from Euroharmony VA, please indicate consent by click the button below. If you don&rsquo;t want to hear from us again, do nothing; your account will be mark as "do not email" and our mailer will ensure you don&rsquo;t receive any "marketing" emails.</p></div>' . PHP_EOL;
				$t_email_data['message'] .= '<div class="row"><div class="form-group"><div class="col-sm-4 col-sm-offset-2"><a class="btn btn-success" href="' . $t_landing_url . '" role="button">I consent to receiving emails from the VA</a></div></div></div></p>' . PHP_EOL;
				$t_email_data['message'] .= '<div class="row"><p>If for some reason the button doesn&rsquo;t work, please go to [ <a href="' . $t_landing_url . '">' . $t_landing_url . '</a> ] in your web browser of choice.</p>' . PHP_EOL;
				$t_email_data['message'] .= '<p>Kind regards, and I hope we can continue into the future.</p>' . PHP_EOL;
				$t_email_data['message'] .= '<p><strong>Murray Crane</strong><br/>President - Euroharmony VA</p>' . PHP_EOL;
				$t_email_data['message'] .= '<p><em style="font-size: small">This is an automated email. Please do not reply.</em></p>' . PHP_EOL;
				$t_email_data['message'] .= '</div>' . Gdpr::$c_foot . PHP_EOL;
				// Send the email
				if (substr_count($row->emailaddress, 'fly-euroharmony.com') < 1) {
					$this->send_email($t_email_data);
					log_message('debug', 'gdpr_controller: send_emails: email #' . $t_email_count .
						' sent to ' . $t_email_data['to'] . '.');
					$t_email_count++;
				}
			}
		}
		$t_email_count--;
		echo Gdpr::$c_head . PHP_EOL . '<div class="row">';
		echo "<div class='jumbotron'><h1>Euroharmony VA<br><small>GDPR Communication Consent</small></h1></div>";
		echo "<p>${t_email_count} emails sent.</p>";
		echo '</div>' . Gdpr::$c_foot . PHP_EOL;
	}

	/**
	 * The pilot is happy to receive emails from us, update pilots table
	 *
	 * @param string $p_token
	 * @return bool
	 */
	function landing_page($p_token = "") {
		if (!empty($p_token)) {
			$t_token = explode('~', $p_token);
			$t_username = $t_token[0];
			$t_crc32 = $t_token[1];
			unset($t_token);

			if (strlen($t_username) != 4 && strlen($t_crc32) != 8) {
				// Something went wrong!
				return FALSE;
			} else {
				$query = $this->db->query("
	SELECT `pilots`.`id`
	FROM `pilots`
	WHERE `pilots`.`username`='${t_username}'
	AND `pilots`.`crc32`='${t_crc32}'
				");

				if ($query->num_rows() == 1) {
					// Perform update - receive_emails to TRUE, blank out crc32
					$this->db->where('username', $t_username);
					$this->db->update('pilots', array("receive_emails" => 1, "crc32" => ""));
					// Landing page to say thank-you and such
					echo Gdpr::$c_head . PHP_EOL . '<div class="row">';
					echo "<div class='jumbotron'><h1>Euroharmony VA<br><small>GDPR Communication Consent</small></h1></div>";
					echo "<p>Thank you. We may not email you all that often (we have the forum, after all), but if we need to, we now know that we can. Enjoy the rest of your day.</p>";
					echo '</div>' . Gdpr::$c_foot . PHP_EOL;
				} else {
					// Something went wrong!
					return FALSE;
				}
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Send email using PHPMailer
	 *
	 * @param $p_email_config
	 * @return bool
	 * @throws phpmailerException
	 */
	private function send_email($p_email_config) {
		require_once $this->config->item('full_base_path') . 'application/third_party/PHPMailer/PHPMailerAutoload.php';
		$this->config->load('email');

		$t_mail = new PHPMailer;
		try {
			$t_mail->isSMTP();
			// Comment the following if you don't want (blocking) debug output
			if (ENVIRONMENT == 'development') {
				//$t_mail->SMTPDebug = 2;
				//$t_mail->Debugoutput = 'html';
			}
			$t_mail->Host = $this->config->item('smtp_host');
			$t_mail->Port = $this->config->item('smtp_port');
			$t_mail->SMTPAuth = FALSE;
			$t_mail->SMTPAutoTLS = FALSE;
			$t_mail->Priority = 1; // Highest priority
			$t_mail->isHTML(TRUE);
			$t_mail->setFrom('murray.crane@fly-euroharmony.com', 'Murray Crane, VA President');
			$t_mail->addReplyTo('donotreply@fly-euroharmony.com', 'Do not reply');
			$t_mail->addAddress($p_email_config['to']);
			if (isset($p_email_config['cc']) && !empty($p_email_config['cc'])) {
				foreach ($p_email_config['cc'] as $t_cc_address) {
					$t_mail->addCC($t_cc_address);
				}
			}
			$t_mail->Subject = $p_email_config['subject'];
			$t_mail->msgHTML($p_email_config['message']);
			$t_mail->AltBody = $t_mail->html2text($p_email_config['message']);

			if (!$t_mail->send()) {
				log_message('error', 'gdpr_controller: send_email: Error:' . $t_mail->ErrorInfo);
			}
		} catch (phpmailerException $err) {
			log_message('error', 'gdpr_controller: send_email: Exception:' . $err->errorMessage());
		} catch (Exception $err) {
			log_message('error', 'gdpr_controller: send_email: Exception:' . $err->getMessage());
		}
		unset($t_mail);
		return TRUE;
	}

	/**
	 * Produce a random CRC32 "checksum"
	 *
	 * @return string
	 */
	private function get_crc32() {
		$t_crc32 = hash('crc32b', $this->make_random_string());
		log_message('debug', 'gdpr_controller: get_crc32: t_crc32: ' . $t_crc32 . '.');

		return $t_crc32;
	}

	/**
	 * Make a random string of $p_bits length
	 *
	 * @param int $p_bits
	 * @return string
	 */
	private function make_random_string($p_bits = 256) {
		$t_bytes = ceil($p_bits / 8);
		$t_return = '';
		for ($i = 0; $i < $t_bytes; $i++) {
			$t_return .= chr(mt_rand(0, 255));
		}
		return $t_return;
	}
}

/* End of file gdpr.php */
/* Location: application/controllers/gdpr.php */