<?php
 
class Join extends CI_Controller {

	function Join()
	{
		parent::__construct();	
	}
	
	
	function emailconfirm($username = NULL, $email_confirm_code = NULL){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		if($username == NULL || $email_confirm_code == NULL){
			//output the fail page
			$data['page_title'] = 'Email Confirmation';
			$data['no_links'] = '0';
			$this->view_fns->view('global/join/join_emailconfirm_fail', $data);
		}
		else{
			
			
			//grab value from database
			$query = $this->db->query("	SELECT 
											pilots.username as username,
											pilots.email_verify_code as email_verify_code,
											pilots.email_confirmed as email_confirmed
											
									FROM pilots
									
									WHERE pilots.username = '$username'
									
									LIMIT 1
											
										");
				
			$pilot_details =  $query->result_array();
			
			//if the pilot is already confirmed, let them know
			if($pilot_details['0']['email_confirmed'] == 1){
				//output the already done page
				$data['page_title'] = 'Email Confirmation';
				$data['no_links'] = '0';
				$this->view_fns->view('global/join/join_emailconfirm_invalid', $data);
			}
			//do a check, if match, update
			elseif($pilot_details['0']['email_verify_code'] == $email_confirm_code){
			
				//write change to pilots to set email validated flag
				$pilots_array = array('email_confirmed' => '1');
				
				$this->db->where('username', $pilot_details['0']['username']);
				$this->db->update('pilots', $this->db->escape($pilots_array));
			
				//if matched and logged in, write session data
				if($this->session->userdata('logged_in') == '1'){
					$sessiondata = array(
						'email_confirmed' => '1',
									);
									
					//set data in session
					$this->session->set_userdata($sessiondata);
				}
				
				//output success page
				$data['page_title'] = 'Email Confirmation';
				$data['no_links'] = '0';
				$this->view_fns->view('global/join/join_emailconfirm_success', $data);
			}
			else{
				//output fail page
				$data['page_title'] = 'Email Confirmation';
				$data['no_links'] = '0';
				$this->view_fns->view('global/join/join_emailconfirm_fail', $data);
			}
		
		}
	
	}
	
	function index()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		//load addtional libraries
	  	$this->load->helper(array('form', 'url'));
		$this->load->model('Pirep_model');
		$this->load->library('Auth_fns');
		$this->load->library('Pirep_fns');
		$this->load->library('Profile_fns');
		$this->load->library('email');
		
		//if we are logged, in, we should not see this page.
		if($this->session->userdata('logged_in') == '1'){
			redirect ('');
		}
		
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';
		
		//get list of hubs from db
		$data['hub_array'] = $this->Pirep_model->get_hubs();
		
		//grab all post data
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$fname = ucfirst(strtolower($this->security->sanitize_filename($this->input->post('fname'))));
		$sname = ucfirst(strtolower($this->security->sanitize_filename($this->input->post('sname'))));
		$dobday = $this->security->sanitize_filename($this->input->post('dobday'));
		$dobmonth = $this->security->sanitize_filename($this->input->post('dobmonth'));
		$dobyear = $this->security->sanitize_filename($this->input->post('dobyear'));
		$email = $this->security->sanitize_filename($this->input->post('email'));
		$email2 = $this->security->sanitize_filename($this->input->post('email2'));
		$country = $this->security->sanitize_filename($this->input->post('country'));
		$flightsim = $this->security->sanitize_filename($this->input->post('flightsim'));
		$flightdate = $this->security->sanitize_filename($this->input->post('flightdate'));
		$hub_1 = $this->security->sanitize_filename($this->input->post('hub_1'));
		$hub_2 = $this->security->sanitize_filename($this->input->post('hub_2'));
		$hub_3 = $this->security->sanitize_filename($this->input->post('hub_3'));
		$other_va = $this->security->sanitize_filename($this->input->post('other_va'));
		$know_va = $this->security->sanitize_filename($this->input->post('know_va'));
		$flightsim_experience = $this->security->sanitize_filename($this->input->post('flightsim_experience'));
		$hear_about = $this->security->sanitize_filename($this->input->post('hear_about'));
		$vatsim_id = $this->security->sanitize_filename($this->input->post('vatsim_id'));
		$ivao_id = $this->security->sanitize_filename($this->input->post('ivao_id'));
		$aircraft = $this->security->sanitize_filename($this->input->post('aircraft'));
		$onlineoffline = $this->security->sanitize_filename($this->input->post('onlineoffline'));
		$altitude = $this->security->sanitize_filename($this->input->post('altitude'));
		$speed = $this->security->sanitize_filename($this->input->post('speed'));
		$approach = $this->security->sanitize_filename($this->input->post('approach'));
		$fuelburnt = $this->security->sanitize_filename($this->input->post('fuelburnt'));
		$vatsimid = $this->security->sanitize_filename($this->input->post('vatsimid'));
		$ivaoid = $this->security->sanitize_filename($this->input->post('ivaoid'));
		$comments = $this->security->sanitize_filename($this->input->post('comments'));
		$alt_units = $this->security->sanitize_filename($this->input->post('alt_units'));
		$speed_units = $this->security->sanitize_filename($this->input->post('speed_units'));
		$enginestart_hh = $this->security->sanitize_filename($this->input->post('enginestart_hh'));
		$enginestart_mm = $this->security->sanitize_filename($this->input->post('enginestart_mm'));
		$takeoff_hh = $this->security->sanitize_filename($this->input->post('takeoff_hh'));
		$takeoff_mm = $this->security->sanitize_filename($this->input->post('takeoff_mm'));
		$landing_hh = $this->security->sanitize_filename($this->input->post('landing_hh'));
		$landing_mm = $this->security->sanitize_filename($this->input->post('landing_mm'));
		$engineoff_hh = $this->security->sanitize_filename($this->input->post('engineoff_hh'));
		$engineoff_mm = $this->security->sanitize_filename($this->input->post('engineoff_mm'));
		$fuel_units = $this->security->sanitize_filename($this->input->post('fuel_units'));
		
		//perform validation
		$this->form_validation->set_rules('valid', 'valid', 'required');
		$this->form_validation->set_rules('fname', 'fname', 'required');
		$this->form_validation->set_rules('sname', 'sname', 'required');
		$this->form_validation->set_rules('email', 'email', 'required');
		$this->form_validation->set_rules('email2', 'email2', 'required');
		$this->form_validation->set_rules('country', 'country', 'required');
		$this->form_validation->set_rules('dobday', 'dobday', 'required');
		$this->form_validation->set_rules('dobmonth', 'dobmonth', 'required');
		$this->form_validation->set_rules('dobyear', 'dobyear', 'required');
		$this->form_validation->set_rules('flightsim', 'flightsim', 'required');
		$this->form_validation->set_rules('hub_1', 'hub_1', 'required');
		$this->form_validation->set_rules('hub_2', 'hub_2', 'required');
		$this->form_validation->set_rules('hub_3', 'hub_3', 'required');
		$this->form_validation->set_rules('other_va', 'other_va', 'required');
		$this->form_validation->set_rules('flightdate', 'flightdate', 'required');
		$this->form_validation->set_rules('aircraft', 'aircraft', 'required');
		$this->form_validation->set_rules('onlineoffline', 'onlineoffline', 'required');
		$this->form_validation->set_rules('enginestart_hh', 'enginestart_hh', 'required');
		$this->form_validation->set_rules('enginestart_mm', 'enginestart_mm', 'required');
		$this->form_validation->set_rules('takeoff_hh', 'takeoff_hh', 'required');
		$this->form_validation->set_rules('takeoff_mm', 'takeoff_mm', 'required');
		$this->form_validation->set_rules('engineoff_hh', 'engineoff_hh', 'required');
		$this->form_validation->set_rules('engineoff_mm', 'engineoff_mm', 'required');
		
		
		if($this->form_validation->run() == FALSE){
			$validation = 0;
		}
		else{
			$validation = 1;
		}
		
		//additional checks
		$additional = '1';
		//email address
		if($email != $email2){ $additional == '0'; }
		
		//verify email
		if($this->pirep_fns->verify_email($email)){
			$verify_mail = '1';
		}
		else{
			$additional = '0';
			$verify_mail = '0';
		} 
		
		//selected hubs
		if($hub_1 == $hub_2 || $hub_1 == $hub_3 || $hub_2 == $hub_3){ $additional = '0'; }
		//times of flight stages (must be sequential
		$time_sequence = '1';
		//if engines started after takeoff //removed - have to allow for flights over midnight.
		/*
		if($enginestart_hh > $takeoff_hh || ($enginestart_hh == $takeoff_hh && $enginestart_mm > $takeoff_mm)){
			$time_sequence = '0';
			$additional = '0';
		}
		
		if($takeoff_hh > $landing_hh || ($takeoff_hh == $landing_hh && $takeoff_mm > $landing_mm)){
			$time_sequence = '0';
			$additional = '0';
		}
		
		if($landing_hh > $engineoff_hh || ($landing_hh == $engineoff_hh && $landing_mm > $engineoff_mm)){
			$time_sequence = '0';
			$additional = '0';
		}
		*/
		//check the date
		$dob_valid = '1';
		if($dobday != '' && $dobmonth != '' && $dobyear != ''){
			if(!checkdate($dobmonth, $dobday, $dobyear)){
				//date isn't valid
				$additional = '0';
				$dob_valid = '0';
			}
		}
		else{
			$additional = '0';
		}
		
		$assign_id = '1';
		//check to see if we can assign a pilot id
		if($this->auth_fns->generate_username() == FALSE){
			$additional = '0';
			$assign_id = '0';
		}
		
		
		//get countries from db
		$data['country_array'] = $this->Pirep_model->get_countries();
		
		
		//echo '<font color="#ffffff">$additional: '.$additional.'<br />';
		
		//if the required information is submitted
		if($validation == 1 && $valid == '1' && $additional == '1'){
		
		
			$dob_combination = $dobyear.'-'.$dobmonth.'-'.$dobday;
		
			//check that the email address, and (fname, sname, dob combination) isn't in use
			$query = $this->db->query("	SELECT 
											pilots.id as id
											
									FROM pilots
									
									WHERE pilots.emailaddress = '$email'
									OR (pilots.fname = '$fname' AND pilots.sname = '$sname' AND pilots.date_of_birth = '$dob_combination')
											
										");
				
			//$list =  $query->result();
			$num_rows =  $query->num_rows();
			
			if($num_rows > 0){
				//redirect to check with management
				redirect('join/duplicate');
				
			}
			
			
			//perform the write to create the pilot ***************************************************************************************************
			
			//assign to hub
			$hub = $this->Pirep_model->assign_hub($hub_1, $hub_2, $hub_3, $active_compare_date);
					
			$hub_id = $hub['id'];
			$hub_icao = $hub['icao'];
			
			
			//calculations
			$blocktime_mins = $this->pirep_fns->calculate_blocktime_minutes($enginestart_hh, $enginestart_mm, $engineoff_hh, $engineoff_mm);
			
			$blocktime_mm = $blocktime_mins % 60;
			$blocktime_hh = ($blocktime_mins - $blocktime_mm) / 60;
			
			//calculate dates and times
			$flight_dates = $this->pirep_fns->calculate_flightdates($enginestart_hh, $enginestart_mm, $takeoff_hh, $takeoff_mm, $landing_hh, $landing_mm, $engineoff_hh, $engineoff_mm, $flightdate);
			if($flight_dates != FALSE){
				$engine_start_time = $flight_dates['start_date'];
				$departure_time = $flight_dates['take_date'];
				$landing_time = $flight_dates['land_date'];
				$engine_stop_time = $flight_dates['off_date'];
			}
			else{
				redirect('join/error');
			}
			
			
			
			//need to assign username
			$username = $this->auth_fns->generate_username();
			
			//generate_password
			$password = $this->auth_fns->generate_password();
			
			$email_verify_code = $this->auth_fns->generate_password();
			
			$today = date('Y-m-d', time() );
			
			//array the data
			$pilot_data = array(
									//pilot data
									'username' => $username,
									'fname' => $fname,
									'sname' => $sname,
									'emailaddress' => $email,
									'email_verify_code' => $email_verify_code,
									'email_confirmed' => '0',
									'email_valid' => '1',
									'country' => $country,
									'date_of_birth' => $dob_combination,
									'hub' => $hub_id,
									'hub_last_change' => $today,
									'otherva' => $other_va,
									'comments' => $comments,
									'vatsim_uid' => $vatsimid,
									'ivao_uid' => $ivaoid,
									'curr_location' => $hub_icao,
									'pp_location' => $hub_icao,
									'iplog' => $_SERVER['REMOTE_ADDR'],
									'signupdate' => $today,
									'have_final_id' => 'Y',
									
									
									//pirep data
									//'flighthours' => $blocktime_hh,
									//'flightmins' => $blocktime_mm,
									'flighthours' => '0',
									'flightmins' => '0',
									'status' => '6',
									'rank' => '0',
									'fsversion' => $flightsim,
									'lastactive' => $gmt_mysql_datetime,
									//'lastflight' => $today,
									
			);
			
			//write into db pilots data
			$this->db->insert('pilots', $this->db->escape($pilot_data));
			
			//grab the user_id
			$this->db->select('id');
			$this->db->from('pilots');
			$this->db->where('username', $username);
			$query = $this->db->get();
			$result =  $query->result_array();
			
			$user_id = $result['0']['id'];
			
			
			//encrypt password
			$hashed_password = $this->auth_fns->hash_password($user_id, $password);
			
			$pilot_data = array('password' => $hashed_password);
			
			//second insert to write hashed pw
			$this->db->where('id', $user_id);
			$this->db->update('pilots', $this->db->escape($pilot_data));
			
			
			
			$pirep_data = array(
									'username' => $username,
									'user_id' => $user_id,
									'hub' =>  $hub_id,	
									'aircraft' =>  $aircraft,	
									'onoffline' =>  $onlineoffline,
									'flightnumber' =>  '',
									'start_icao' =>  'EGCC',
									'end_icao' =>  'EGBB',
									'passengers' =>  '0', //training flight
									'cruisealt' =>  $altitude,
									'cruisespd' =>  $speed.' '.$speed_units,
									'approach' =>  $approach,
									'fuelburnt' =>  $fuelburnt.' '.$fuel_units,
									'comments' =>  'Initial Flight',
									'submitdate' =>  $gmt_mysql_datetime,
									'last_updated' =>  $gmt_mysql_datetime,
									'checked' =>  '0',
									'engine_start_time' =>  $engine_start_time,
									'engine_stop_time' =>  $engine_stop_time,
									'departure_time' =>  $departure_time,
									'landing_time' =>  $landing_time,
									'blocktime_mins' => $blocktime_mins,
									'comments_mt' =>  '',
									'archived' =>  '0',
									'circular_distance' =>  '0',
									'from_fl' =>  '0',
									'act_different' =>  '0',
									'fl_version' =>  '0',
									'aggregate_id' =>  '',
									'aircraft_tech_name' =>  '',
									'propilot_flight' =>  '0',
			);
			
			//write into db pirep data
			$this->db->insert('pirep', $this->db->escape($pirep_data));
			
			//log pilot in.
			//define session data
			$sessiondata = array(
				'user_id' => $user_id,
				'username' => $username,
				'pilotname' => $fname.' '.$sname,
				'email_confirmed' => '0',
				'flight_hours' => $blocktime_hh,
				'flight_mins' => $blocktime_mm,
				'sess' => $password,
				'fname' => $fname,
				'sname' => $sname,
				'rank_long' => 'First Officer',
				'rank_short' => 'FO',
				'rank_id' => '0',
				'hub' => $hub_icao,
				'hub_id' => $hub_id,
				'country' => $country,
				'logged_in' => '1'
							);
							
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			
			/*INSERT CODE *************************************************************************************************************************************************************************/
			// need to execute the forum bridge to create forum account, and log in there as well.
			
					
			//send pilot confirmation email
			$this->email->from('info@fly-euroharmony.com', 'Euroharmony VA');
			$this->email->to($email);
			
			$this->email->subject('Application');
			$this->email->message(
"Congratulations ".$fname.", your Euroharmony Virtual Airline account has been created.\n
Your credentials to log in are:\n
Username: EHM-".$username." 
Password: ".$password."\n
(the EHM- section of the callsign is not required for signing in)\n
You need to confirm your email address by following the link below. You will not be able to log flight time until this final step is complete.\n
".$data['base_url']."join/emailconfirm/".$username."/".$email_verify_code."\n
You have been assigned to ".$data['hub_array'][$hub_id].".\n
Good luck with your virtual career and welcome to the VA! ");
			
			$this->email->send();
			
	//send management notification email
	
		//get countries from db
		$country_array = $this->Pirep_model->get_countries();
		
		//get list of flight sims
		$flightsim_array = $this->Pirep_model->get_flightsims_raw();
			
			
			if(array_key_exists($country, $country_array)){
				$country_name = htmlspecialchars_decode($country_array[$country]);
			}
			else{
				$country_name = 'Unknown Country: '.$country;
			}
			
			if(array_key_exists($flightsim, $flightsim_array)){
				$flightsim_name = $flightsim_array[$flightsim];
			}
			else{
				$flightsim_name = 'Unknown Sim: '.$flightsim;
			}
			
			
			
			
			$age = $this->profile_fns->get_age($dob_combination);
			
			$this->email->from('noreply@fly-euroharmony.com', 'Euroharmony VA');
			$this->email->to('info@fly-euroharmony.com');
			
			$this->email->subject('Application: '.$fname.' '.$sname);
			$this->email->message(
"Euroharmony Management,\n\nNew Member: ".$fname." ".$sname."\n\n".
"EHM-".$username." ".$fname." ".$sname." (".$age."), who flies ".$flightsim_name." from ".$country_name." has created a Euroharmony Virtual Airline account at ".gmdate('d/m/Y H:i:s', time())."z and has been assigned to ".$data['hub_array'][$hub_id].".\n\nShould you wish to contact them, the email address used to sign up was ".$email."\n\nThe stored address for this pilot may have changed or may be changed when they attempt to validate/confirm their email address to unlock the restrictions on their account.\n\nRequest originated from: ".$_SERVER['REMOTE_ADDR']);
			
			$this->email->send();

			
			
			//redirect to the email confirmation page
			redirect('join/email/');
			
			
		
		}
		//if the required information is not submitted
		else{
		
		
			
			if($valid == '1'){
				//post failed validation, set error
				$data['error'] .= 'There are the following problems with the form:<ul>';
				$data['highlight1'] = '<font color="#cc0000">';
				$data['highlight2'] = '</font>';
				
				if($validation == 0){ $data['error'] .= '<li>Not all required fields have been completed</li>'; }
				if($email != $email2){ $data['error'] .= '<li>The email addresses do not match</li>'; }
				if($verify_mail == '0'){ $data['error'] .= '<li>The email address is not valid</li>'; }
				if($dob_valid == '0'){ $data['error'] .= '<li>The date of birth is not a valid date</li>'; }
				if($hub_1 == $hub_2 || $hub_1 == $hub_3 || $hub_2 == $hub_3){ $data['error'] .= '<li>Selected hubs are not unique</li>'; }
				if($time_sequence == '0'){ $data['error'] .= '<li>There is an error in the flight times</li>'; }
				if($assign_id == '0'){ $data['error'] .= '<li>Unable to assign a username. Please contact an administrator.</li>'; }
				
				$data['error'] .= '</ul> Please check and resubmit.';
				
				
				//do not write default vals, use post data
			}
			else{
				//default all values that are not submitted on last run
				$fname = '';
				$sname = '';
				$dobday = '';
				$dobmonth = '';
				$dobyear = '';
				$email = '';
				$email2 = '';
				$country = '';
				$flightsim = '';
				$hub_1 = '';
				$hub_2 = '';
				$hub_3 = '';
				$other_va = '';
				$know_va = '';
				$flightsim_experience = '';
				$hear_about = '';
				$vatsim_id = '';
				$ivao_id = '';
				$aircraft = '';
				$onlineoffline = '';
				$altitude = '';
				$speed = '';
				$approach = 'visual';
				$fuelburnt = '';
				$fuel_units = 'lbs';
				$vatsimid = '';
				$ivaoid = '';
				$comments = '';
				$alt_units = 'ft';
				$speed_units = 'ias';
				$flightdate = '';
				
				$enginestart_hh = '';
				$enginestart_mm = '';
				$takeoff_hh = '';
				$takeoff_mm = '';
				$landing_hh = '';
				$landing_mm = '';
				$engineoff_hh = '';
				$engineoff_mm = '';
				
			}
			
		
			//define all the form elements
			$data['fname'] = array( 'name' => 'fname','id' => 'fname','value' => $fname, 'maxlength' => '20','size' => '20','style' => 'width:30%');
			$data['sname'] = array( 'name' => 'sname','id' => 'sname','value' => $sname, 'maxlength' => '20','size' => '20','style' => 'width:30%');
			$data['email'] = array( 'name' => 'email','id' => 'email','value' => $email, 'maxlength' => '60','size' => '20','style' => 'width:30%');
			$data['email2'] = array( 'name' => 'email2','id' => 'email2','value' => $email2, 'maxlength' => '60','size' => '20','style' => 'width:30%');
			$data['altitude'] = array( 'name' => 'altitude','id' => 'altitude','value' => $altitude, 'maxlength' => '100','size' => '10','style' => 'width:20%');
			$data['speed'] = array( 'name' => 'speed','id' => 'speed','value' => $speed, 'maxlength' => '20','size' => '10','style' => 'width:20%');
			$data['fuelburnt'] = array( 'name' => 'fuelburnt','id' => 'fuelburnt','value' => $fuelburnt, 'maxlength' => '100','size' => '10','style' => 'width:20%');
			$data['vatsimid'] = array( 'name' => 'vatsimid','id' => 'vatsimid','value' => $vatsimid, 'maxlength' => '20','size' => '10','style' => 'width:20%');
			$data['ivaoid'] = array( 'name' => 'ivaoid','id' => 'ivaoid','value' => $ivaoid, 'maxlength' => '20','size' => '10','style' => 'width:20%');
			$data['comments'] = array( 'name' => 'comments','id' => 'comments','value' => $comments, 'rows' => '5','cols' => '12','style' => 'width:50%');
			
			$data['enginestart_hh'] = array( 'name' => 'enginestart_hh','id' => 'enginestart_hh','value' => $enginestart_hh, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			$data['enginestart_mm'] = array( 'name' => 'enginestart_mm','id' => 'enginestart_mm','value' => $enginestart_mm, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			$data['takeoff_hh'] = array( 'name' => 'takeoff_hh','id' => 'takeoff_hh','value' => $takeoff_hh, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			$data['takeoff_mm'] = array( 'name' => 'takeoff_mm','id' => 'takeoff_mm','value' => $takeoff_mm, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			$data['landing_hh'] = array( 'name' => 'landing_hh','id' => 'landing_hh','value' => $landing_hh, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			$data['landing_mm'] = array( 'name' => 'landing_mm','id' => 'landing_mm','value' => $landing_mm, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			$data['engineoff_hh'] = array( 'name' => 'engineoff_hh','id' => 'engineoff_hh','value' => $engineoff_hh, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			$data['engineoff_mm'] = array( 'name' => 'engineoff_mm','id' => 'engineoff_mm','value' => $engineoff_mm, 'maxlength' => '2','size' => '2','style' => 'width:10%');
			
			//define all vars
			$data['country'] = $country;
			$data['flightsim'] = $flightsim;
			$data['hub_1'] = $hub_1;
			$data['hub_2'] = $hub_2;
			$data['hub_3'] = $hub_3;
			$data['other_va'] = $other_va;
			$data['aircraft'] = $aircraft;
			$data['onlineoffline'] = $onlineoffline;
			$data['alt_units'] = $alt_units;
			$data['fuel_units'] = $fuel_units;
			$data['speed_units'] = $speed_units;
			$data['approach'] = $approach;
			$data['flightdate'] = $flightdate;
			$data['dobday'] = $dobday;
			$data['dobmonth'] = $dobmonth;
			$data['dobyear'] = $dobyear;
			
			
							
			//define all the arrays			
			$data['country_array'] = array();
			$data['otherva_array'] = array('No' => 'No','I was' => 'I was','I am' => 'I am');
			$data['aircraft_array'] = array();
			$data['onlineoffline_array'] = array('0' => 'Offline', '1' => 'Online (Vatsim)', '2' => 'Online (IVAO)', '3' => 'Online (Other)');
			$data['approach_array'] = array('Visual' => 'Visual', 'ILS' => 'ILS', 'NDB' => 'NDB', 'VOR' => 'VOR');
			$data['alt_unit_array'] = array('m' => 'Metres', 'ft' => 'Feet');
			$data['fuel_units_array'] = array('usgal' => 'US Gallons', 'impgal' => 'Imperial Gallons', 'lbs' => 'Pounds');
			$data['speed_units_array'] = array('ias' => 'IAS', 'tas' => 'TAS', 'gs' => 'GS', 'mach' => 'Mach');
			$data['flightsim_array'] = array();
			$data['flightdate_array'] = array();
			$data['dobday_array'] = array('' => '');
			$data['dobmonth_array'] = array('' => '');
			$data['dobyear_array'] = array('' => '');
			
			//day_array
			$i = 1;
			while($i <= 31){
				$data['dobday_array'][$i] = $i;
				$i++;
			}
			
			//month_array
			$i = 1;
			while($i <= 12){
				$data['dobmonth_array'][$i] = $i;
				$i++;
			}
			
			
			//year_array
			$current_year = date('Y', time());
			
			$i = $current_year - 8;
			while($i >= ($current_year - 100)){
				$data['dobyear_array'][$i] = $i;
				$i--;
			}
			
			//flightdate
			$current_year = date('Y', time());
			
			$i = 0;
			$today = date('Y-m-d', time());
			$yesterday = date('Y-m-d', strtotime('-1 day'));
			
			while($i <= 7){
			
				$expression = '-'.$i.'day';
			
				$date = date('Y-m-d', strtotime($expression));
				
				
				if($date == $today){
					$label = 'Today';
				}
				elseif($date == $yesterday){
					$label = 'Yesterday';
				}
				else{
					$label = date('d/m/Y', strtotime($date));
				}
			
				$data['flightdate_array'][$date] = $label;
				$i++;
			}
			
			
			
			
			//get list of flight sims
			$data['flightsim_array'] = $this->Pirep_model->get_flightsims();
			
			
			
			//get countries from db
			$data['country_array'] = $this->Pirep_model->get_countries();
			
			
			
			//get aircraft from db //rank, nochopper, noprop, nojet
			$data['aircraft_array'] = $this->Pirep_model->get_aircraft(1,1,0,0);
		
		}
		
		$data['page_title'] = 'Join Euroharmony';
		$data['right_bar'] = '0';
		$this->view_fns->view('global/join/join_index', $data);
	}
	
	
	
	function duplicate()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		$data['page_title'] = 'Join Euroharmony';
		$data['right_bar'] = '0';
		$this->view_fns->view('global/join/join_duplicate', $data);
	}
	
	function email()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		if($this->session->userdata('logged_in') == '1'){
		
		$hub_icao = $this->session->userdata('hub');
		$data['fname'] = $this->session->userdata('fname');
		$data['sname'] = $this->session->userdata('sname');
		$data['username'] = $this->session->userdata('username');
		$data['password'] = $this->session->userdata('sess');
		
		//grab the hub name
		$query = $this->db->query("	SELECT 	
												hub.hub_icao as hub_icao,
												hub.hub_name as hub_name,
												pilots.fname as captain_fname,
												pilots.sname as captain_sname,
												countries.Name as country
												
										FROM hub
										
											LEFT JOIN airports
											on airports.ICAO = hub.hub_icao
											
											LEFT JOIN countries
											on airports.Country = countries.Country
											
											LEFT JOIN pilots
											ON pilots.id = hub.hub_captain_id
																					
										WHERE hub.hub_icao = '$hub_icao'
										
										LIMIT 1
										
										");
										
				$hub_data =  $query->result_array();
				
				
				$data['hub_icao'] = $hub_data['0']['hub_icao'];
				$data['hub_name'] = $hub_data['0']['hub_name'];
				$data['hub_captain'] = $hub_data['0']['captain_fname'].' '.$hub_data['0']['captain_sname'];
				$data['country'] = $hub_data['0']['country'];
		
		$data['page_title'] = 'Join Euroharmony';
		$data['right_bar'] = '0';
		$this->view_fns->view('global/join/join_email', $data);
		
		}
		else{
			redirect('');
		}
	}
	
}

/* End of file */