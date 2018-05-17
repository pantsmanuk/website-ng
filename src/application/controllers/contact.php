<?php

class Contact extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	/************************************************************************************************************/

	function index($recipient = NULL) {

		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('email');

		$data['page_title'] = 'Contact Us';

		$data['exception'] = '';

		//Validation
		//Validation Rules
		$this->form_validation->set_rules('name', 'name', 'required');
		$this->form_validation->set_rules('email', 'email', 'required');
		$this->form_validation->set_rules('contact_nature', 'contact_nature', 'required');
		$this->form_validation->set_rules('contact_title', 'contact_title', 'required');
		$this->form_validation->set_rules('contact_message', 'contact_message', 'required');

		//grab maangement to build array
		$query = $this->db->query("	SELECT 	pilots.id AS id, 
										pilots.username AS username,
										pilots.fname AS fname,
										pilots.sname AS sname,
										pilots.title AS title,
										pilots.signupdate AS signupdate,
										pilots.country AS country_code
										
										
								FROM pilots
								
									LEFT JOIN usergroup_index
									ON usergroup_index.id = pilots.usergroup
								
								WHERE usergroup_index.management = '1'
								
								ORDER BY pilots.sname, pilots.fname
										
									");

		$management_results = $query->result();

		//init array
		$data['contact_nature_array']['info'] = 'Management';
		//build dropdown array
		foreach ($management_results as $row) {
			$data['contact_nature_array'][$row->id] = $row->fname . ' ' . $row->sname;
			$data['address_array'][$row->id] = strtolower($row->fname);
		}

		//get ip address
		$ip_address = $_SERVER['REMOTE_ADDR'];

		//get POST data, clean it, prepare it for the email
		$contact_submit = $this->security->sanitize_filename($this->input->post('contact_submit'));
		$name = $this->security->sanitize_filename($this->input->post('name'));
		$email = $this->security->sanitize_filename($this->input->post('email'));
		$contact_nature = $this->security->sanitize_filename($this->input->post('contact_nature'));
		$contact_title = $this->security->sanitize_filename($this->input->post('contact_title'));
		$contact_message = $this->security->sanitize_filename($this->input->post('contact_message'));

		$generic = 0;

		if (is_numeric($contact_nature) && array_key_exists($contact_nature, $data['address_array'])) {
			$toemail = $data['address_array'][$contact_nature] . '@fly-euroharmony.com';
		} elseif ($contact_nature == 'info') {
			$toemail = 'info@fly-euroharmony.com';
			$generic = 1;
		} else {
			$valid_email = FALSE;
			if ($contact_submit == 'true') {
				$data['exception'] .= 'No contact of that name';
			}
		}

		//if @ character is in mail string
		if (substr_count($email, '@') > 0) {

//------------------------------------RECODE-----------------------------RECODE------------------------
			// take email address and split it into username and domain.
			list($userName, $mailDomain) = split("@", $email);
			if (checkdnsrr($mailDomain, "MX")) {
				// this is a valid email domain!
				$valid_email = TRUE;
			} else {
				// this email domain doesn't exist! bad dog! no biscuit!
				$valid_email = FALSE;
				$data['exception'] .= 'The supplied email address is invalid. ';
			}
//------------------------------------RECODE-----------------------------RECODE------------------------
		} else {
			$valid_email = FALSE;
			if ($contact_submit == 'true') {
				$data['exception'] .= 'The supplied email address is invalid. ';
			}
		}

		//If all fields have been submitted
		if ($this->form_validation->run() != FALSE && $valid_email == TRUE) {

			$this->email->from($email, $name);
			$this->email->to($toemail);

			if ($generic == 0) {
				$this->email->cc('info@fly-euroharmony.com');
			}

			$this->email->subject('Web: ' . $contact_title);
			$this->email->message(
				$name . "\n"
				. 'Email:' . $email . "\n"
				. 'IP: ' . $ip_address . "\n"
				. 'Has sent ' . $data['contact_nature_array'][$contact_nature] . ' a message from the Euroharmony web contact form.' . "\n" . "\n"
				. $contact_message);

			$this->email->send();

			//echo $this->email->print_debugger();

			//echo '<br />';
			//echo '<br />';

			//echo 'Email: '.$email.'<br />';
			//echo 'IP: '.$ip_address.'<br />';
			//echo 'To: '.$toemail.'<br />';
			//echo 'Message: '.$contact_message.'<br />';

			$this->view_fns->view('global/contact/cnt_feedback', $data);

		} else {

			//Input and textarea field attributes
			$data['name'] = array('name' => 'name', 'id' => 'name', 'maxlength' => '30', 'size' => '30', 'value' => $name);

			$data['email'] = array('name' => 'email', 'id' => 'email', 'maxlength' => '50', 'size' => '30', 'value' => $email);

			$data['contact_message'] = array('name' => 'contact_message', 'id' => 'contact_message', 'rows' => '15', 'cols' => '40', 'value' => $contact_message);
			$data['contact_title'] = array('name' => 'contact_title', 'id' => 'contact_title', 'maxlength' => '50', 'size' => '30', 'value' => $contact_title);
			$data['contact_nature'] = $contact_nature;

			//check if submitted
			if ($contact_submit == 'true') {
				//Exception Data
				$data['reqd1'] = "<span class=\"exception\">";
				$data['reqd2'] = "</span>";
				$data['exception'] .= "You have not completed all the required fields. These are now highlighted.";
			} else {
				//initialise variable data
				$fname = '';
				$sname = '';
				$email = '';
				$telephone = '';
				$contact_nature = '';
				$contact_title = '';
				$contact_message = '';
			}

			/*
			$data['contact_nature_array'] = array(	'info' => 'Management',
													'murray' => 'Murray Crane',
													'andrei' => 'Andrei Vatasescu',
													'dominic' => 'Dominic Mahon',
													'alexander' => 'Alexander Worton',
													'robert' => 'Robert Szikszo',
			);
			*/
			$recipient = strtolower($recipient);

			if (!array_key_exists($recipient, $data['contact_nature_array'])) {
				$recipient = 'info';
			}

			$data['contact_nature'] = $recipient;

			//output the data to the view
			$this->view_fns->view('global/contact/cnt_index', $data);

		}

//close function index
	}

//close class
}

/************************************************************************************************************/
