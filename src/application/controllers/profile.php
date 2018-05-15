<?php
 
class Profile extends CI_Controller {

	function Profile()
	{
		parent::__construct();	
	}


	function hub(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Pirep_model');
		
		//ensure logged in wrapper
		if ($this->session->userdata('logged_in') == TRUE){
			
			//set user_id
			$user_id = $this->session->userdata('user_id');
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$hub = $this->security->sanitize_filename($this->input->post('hub'));
			
			
			//grab the last hub change date
			$query = $this->db->query("	SELECT 
											pilots.id as id,
											pilots.hub_last_change as hub_last_change
										
										FROM pilots
										
										WHERE pilots.id = '$user_id'
										
										LIMIT 1
										
									");
			
			$list =  $query->result_array();
			$num_rows =  $query->num_rows();
			
			if($num_rows == 1){
				$hub_last_change = $list['0']['hub_last_change'];
			}
			else{
				$hub_last_change = '';
			}
			
			if($hub_last_change){
			
			}
			
			if($hub_last_change == '0000-00-00'){
				$time_remaining = 0;
			}
			else{
				$time_remaining = strtotime($hub_last_change) - strtotime('-1 month');
			}
			
			$data['time_remaining'] = $time_remaining;
			
			$today = date('Y-m-d', time() );
			
			//if submitted ok, perform update
			if($valid == 'true' && $time_remaining <= 0){
				//check data
				$pilots_data = array(
							'hub' => $hub,
							'hub_last_change' => $today
				);
				
				
				//perform update
				$this->db->where('id', $user_id);
				$this->db->update('pilots', $this->db->escape($pilots_data));
				
				//grab the ICAO code for the selected hub
				$query = $this->db->query("	SELECT 
												hub.id as id,
												hub.hub_icao as hub_icao
											
											FROM hub
											
											WHERE hub.id = '$hub'
											
											LIMIT 1
											
										");
				
				$list =  $query->result_array();
				$num_rows =  $query->num_rows();
				
				if($num_rows == 1){
					$hub_icao = $list['0']['hub_icao'];
				}
				else{
					$hub_icao = '';
				}
				
				//now update session data
				$sessiondata = array(
				'hub' => $hub_icao
							);
							
				//set data in session
				$this->session->set_userdata($sessiondata);
				
				//display success
				$data['exception'] = 'Details successfully updated.';
				$data['page_title'] = 'Edit Profile - Feedback';
				$this->view_fns->view('global/profile/profile_feedback', $data);
			}
			else{
			//otherwise set error and output again for editing
			
				//grab info from database
				$query = $this->db->query("	SELECT 
												pilots.id as id,
												pilots.hub as hub
											
											FROM pilots
											
											WHERE pilots.id = '$user_id'
											
											LIMIT 1
											
										");
				
				$list =  $query->result_array();
				$num_rows =  $query->num_rows();
				
				if($num_rows == 1){
					$hub = $list['0']['hub'];
				}
				else{
					$hub = '';
				}
				
			
				//hub dropdown
				$data['hub'] = $hub;
				$data['hub_array'] = array();
				$data['hub_array'] = $this->Pirep_model->get_hubs(0);
				
				//outut
				$data['page_title'] = 'Edit Profile - Details';
				$this->view_fns->view('global/profile/profile_hub', $data);
			}
			
			
		}
		//not logged in redirect
		else{
			redirect();
		}
	}




	function email(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('Pirep_fns');
		$this->load->library('email');
		
		//ensure logged in wrapper
		if ($this->session->userdata('logged_in') == TRUE){
			
			//set user_id and other session vars
			$user_id = $this->session->userdata('user_id');
			$username = $this->session->userdata('username');
			$fname = $this->session->userdata('fname');
			$sname = $this->session->userdata('sname');
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$oldpassword = $this->security->sanitize_filename($this->input->post('oldpassword'));
			$email1 = $this->security->sanitize_filename($this->input->post('checkfield1'));
			$email2 = $this->security->sanitize_filename($this->input->post('checkfield2'));
			
			//Validation Rules - need to supply old password if changing own
			$this->form_validation->set_rules('oldpassword', 'oldpassword', 'required');
			$this->form_validation->set_rules('checkfield1', 'checkfield1', 'required');
			$this->form_validation->set_rules('checkfield2', 'checkfield2', 'required');
			
			
			//encrypt password to match database figure
			$oldpassword = $this->auth_fns->hash_password($user_id, $oldpassword);
			
	
			if($valid == 'true'){
	
				//pull password from db to check match
				$query = $this->db->query("SELECT 	id, 
													password
													
													FROM pilots 
													
													WHERE id = '$user_id'
													
											");
		
				
				//grab result
				$result =  $query->result();			
	
				//ensure data clear
				$dbpassword = '';
	
				// set pulled data
				foreach($result as $row){
					if ($row->password){
						$dbpassword = $row->password;
					}
					else{
						$dbpassword = '';
					}
				}
			
			}
			else{
				$dbpassword = '';
			}
			
			//if submitted ok, perform update
			if($valid == 'true' 
			&& $email1 == $email2 
			&& $this->form_validation->run() == TRUE
			&& $oldpassword == $dbpassword 
			&& $dbpassword != ''
			&& $this->pirep_fns->verify_email($email1)
			){
				//check data
				
					
				
				$email_verify_code = $this->auth_fns->generate_password();
			
				$pilots_data = array(
							'emailaddress' => $email1,
							'email_confirmed' => '0',
							'email_verify_code' => $email_verify_code
				);
				
				
				//perform update
				$this->db->where('id', $user_id);
				$this->db->update('pilots', $this->db->escape($pilots_data));
				
				//now update session data
				$sessiondata = array(
					'email_confirmed' => '0',
							);
							
				//set data in session
				$this->session->set_userdata($sessiondata);
				
				//send confirmation email
$this->email->from('noreply@fly-euroharmony.com', 'Euroharmony VA');
$this->email->to($email1);

$this->email->subject('Change of Email Address');
$this->email->message(
"EHM-".$username.' '.$fname.", as you have changed the email address registered with Euroharmony, you will need to confirm this email address by following the link below. You will not be able to log flight time until this step is complete.\n
".$data['base_url']."join/emailconfirm/".$username."/".$email_verify_code."\n
Kind regards,\n
Euroharmony VA
");

$this->email->send();
				
				//display success
				$data['exception'] = 'Email changed. You will need to confirm the address is valid by following the link in the email before being able to post a pirep.<br /><br />Please allow several minutes for the email to arrive.';
				$data['page_title'] = 'Edit Profile - Feedback';
				$this->view_fns->view('global/profile/profile_feedback', $data);
				
				
					
				
				
			}
			else{
			//otherwise set error and output again for editing
				$data['exception'] = '';
				
				if($valid == 'true'){
					if($this->form_validation->run() == FALSE){
						$data['exception'] .= 'You did not complete all required fields';
					}
					elseif($oldpassword != $dbpassword && $dbpassword != ''){
						 $data['exception'] .= 'Your password was incorrect';
					}
					
					if($email1 != $email2){
					
						if($data['exception'] != ''){
							 $data['exception'] .= ' and y';
						}
						else{
							$data['exception'] .= 'Y';
						}
					
						$data['exception'] .= 'our Email fields did not match';
					}
					elseif(!$this->pirep_fns->verify_email($email1)){
							if($data['exception'] != ''){
							 $data['exception'] .= ' and y';
						}
						else{
							$data['exception'] .= 'Y';
						}
					
						$data['exception'] .= 'our Email Address was not valid';
					}
				
					if($data['exception'] != ''){
						 $data['exception'] .= '. Please try again.';
					}
				}
			
			
				//fields
				$data['oldpassword'] = array('name' => 'oldpassword', 'id' => 'oldpassword','maxlength' => '30', 'size' => '30');
				$data['checkfield1'] = array( 'name' => 'checkfield1','id' => 'checkfield1','value' => $email1, 'maxlength' => '60','size' => '30');
				$data['checkfield2'] = array( 'name' => 'checkfield2','id' => 'checkfield2','value' => $email2, 'maxlength' => '60','size' => '30');
				
				//inject javascript
				$data['javascript_file_array'] = array('matchFields');
				
				//outut
				$data['page_title'] = 'Edit Profile - Email Address';
				$this->view_fns->view('global/profile/profile_email', $data);
			}
			
			
		}
		//not logged in redirect
		else{
			redirect('');
		}
	}


	function details(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Pirep_model');
		
		//ensure logged in wrapper
		if ($this->session->userdata('logged_in') == TRUE){
			
			//set user_id
			$user_id = $this->session->userdata('user_id');
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$country = $this->security->sanitize_filename($this->input->post('country'));
			$flight_sim = $this->security->sanitize_filename($this->input->post('flight_sim'));
			$vatsim_id = $this->security->sanitize_filename($this->input->post('vatsim_id'));
			$ivao_id = $this->security->sanitize_filename($this->input->post('ivao_id'));
			$bulk_email = $this->security->sanitize_filename($this->input->post('bulk_email'));
			
			if($bulk_email == TRUE){
				$bulk_email = 1;
			}
			else{
				$bulk_email = 0;
			}
			
			//if submitted ok, perform update
			if($valid == 'true'){
				//check data
				$pilots_data = array(
							'country' => $country,
							'fsversion' => $flight_sim,
							'vatsim_uid' => $vatsim_id,
							'ivao_uid' => $ivao_id,
							'receive_emails' => $bulk_email
				);
				
				
				//perform update
				$this->db->where('id', $user_id);
				$this->db->update('pilots', $this->db->escape($pilots_data));
				
				//now update session data
				$sessiondata = array(
				'country' => $country
							);
							
				//set data in session
				$this->session->set_userdata($sessiondata);
				
				//display success
				$data['exception'] = 'Details successfully updated.';
				$data['page_title'] = 'Edit Profile - Feedback';
				$this->view_fns->view('global/profile/profile_feedback', $data);
			}
			else{
			//otherwise set error and output again for editing
			
				//grab info from database
				$query = $this->db->query("	SELECT 
												pilots.id as id,
												pilots.country as country,
												pilots.fsversion as fsversion,
												pilots.vatsim_uid as vatsim_uid,
												pilots.ivao_uid as ivao_uid,
												pilots.receive_emails as receive_emails
											
											FROM pilots
											
											WHERE pilots.id = '$user_id'
											
											LIMIT 1
											
										");
				
				$list =  $query->result_array();
				$num_rows =  $query->num_rows();
				
				if($num_rows == 1){
					$country = $list['0']['country'];
					$flightsim = $list['0']['fsversion'];
					$vatsimid = $list['0']['vatsim_uid'];
					$ivaoid = $list['0']['ivao_uid'];
					$bulk_email = $list['0']['receive_emails'];
				}
				else{
					$country = '';
					$flightsim = '';
					$vatsimid = '';
					$ivaoid = '';
					$bulk_email = '';
				}
				
				//sort out blank vatsim/ivao
				if($vatsimid == 0){ $vatsimid = ''; }
				if($ivaoid == 0){ $ivaoid = ''; }
			
				//country dropdown
				$data['country'] = $country;
				$data['country_array'] = array();
				$data['country_array'] = $this->Pirep_model->get_countries();
				//flight sim dropdown
				$data['flightsim'] = $flightsim;
				$data['flightsim_array'] = array();
				$data['flightsim_array'] = $this->Pirep_model->get_flightsims();
				//ivao and vatsim ids text
				$data['vatsimid'] = array( 'name' => 'vatsim_id','id' => 'vatsim_id','value' => $vatsimid, 'maxlength' => '7','size' => '7');
				$data['ivaoid'] = array( 'name' => 'ivao_id','id' => 'ivao_id','value' => $ivaoid, 'maxlength' => '7','size' => '7');
				//checkbox for bulk email opt-out
				$data['bulk_email'] = array('name' => 'bulk_email', 'id' => 'bulk_email', 'value' => 'accept', 'checked' => $bulk_email, 'style' => 'margin:10px');
				
				
				
				
				//outut
				$data['page_title'] = 'Edit Profile - Details';
				$this->view_fns->view('global/profile/profile_details', $data);
			}
			
			
		}
		//not logged in redirect
		else{
			redirect();
		}
	}

	
	function credentials(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Auth_model');
		
		//ensure logged in wrapper
		if ($this->session->userdata('logged_in') == TRUE){
			
			//set user_id
			$user_id = $this->session->userdata('user_id');
		  
			//Validation Rules - need to supply old password if changing own
			$this->form_validation->set_rules('oldpassword', 'oldpassword', 'required');
			$this->form_validation->set_rules('newpassword1', 'newpassword1', 'required');
			$this->form_validation->set_rules('newpassword2', 'newpassword2', 'required');
		
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$username = $this->session->userdata('username');
		
			//grab the new password data
			$newpassword1 = $this->security->sanitize_filename($this->input->post('newpassword1'));
			$newpassword2 = $this->security->sanitize_filename($this->input->post('newpassword2'));
		
			//check the submitted passwords match
			if ($newpassword1 != $newpassword2){
			$valid = 'false';
			$exception = 'passmatch';
			}
			else{
			$exception = '';
			}
		
		
			
			//do this if the form hasn't been submitted or not all req'd info is given
			if ($valid != 'true' || $this->form_validation->run() == FALSE){
				
				//grab all the userdata from database
				$userdata = $this->Auth_model->get_passchange_output($user_id);
				
		
				//Input and textarea field attributes
				$data['oldpassword'] = array('name' => 'oldpassword', 'id' => 'oldpassword','maxlength' => '30', 'size' => '30');
				$data['newpassword1'] = array('name' => 'newpassword1', 'id' => 'newpassword1','maxlength' => '30', 'size' => '30');
				$data['newpassword2'] = array('name' => 'newpassword2', 'id' => 'newpassword2','maxlength' => '30', 'size' => '30');
		
				//if it was the validation run that failed, but was submitted...
				if($this->form_validation->run() == FALSE && $valid == 'true'){
		
					//Exception Data
					$data['reqd1'] = "<span class=\"exception\">";
					$data['reqd2'] = "</span>";
					if ($exception == 'passmatch'){
						$data['exception'] = "Your new passwords did not match. Please try again.";
					}
					else{
						$data['exception'] = "You have not completed all the required fields.";
					}
		
				}
		
					//inject javascript
					$data['javascript_file_array'] = array('passStrength');
					
					//outut
					$data['page_title'] = 'Edit Profile - Password';
					$this->view_fns->view('global/profile/profile_credentials', $data);
					
			}
		
			//if the form has been submitted and all req'd info given
			else{
			
				$oldpassword = $this->security->sanitize_filename($this->input->post('oldpassword'));
				
				//encrypt password to match database figure
				$oldpassword = $this->auth_fns->hash_password($user_id, $oldpassword);
		
				
					//verify data against database
					//grab configuration data from the database
					$query = $this->db->query("SELECT 	id, 
														password, 
														username, 
														fname, 
														sname 
														
														FROM pilots 
														
														WHERE id = '$user_id'
														
												");
			
					
					//grab result
					$result =  $query->result();			
	
					//ensure data clear
					$dbpassword = '';
		
	
					// set pulled date
					foreach($result as $row){
						if ($row->password){
						$dbpassword = $row->password;
						$id = $row->id;
						$username = $row->username;
						$fname = $row->fname;
						$sname = $row->sname;
						}
						else{
						$dbpassword = '';
						$id = $row->id;
						$username = $row->username;
						$fname = $row->fname;
						$sname = $row->sname;
						}
					}
				
		
					//if we're sure everything is good
					if(($oldpassword == $dbpassword && $newpassword1 == $newpassword2)){
			
						//hash the password using chosen encryption
						$hashed_password = $this->auth_fns->hash_password($user_id, $newpassword1);
						//write to the internal database
						$affected_rows = $this->Auth_model->write_passchange_internal($user_id, $hashed_password);
			
			
						$data = array_merge($data, $affected_rows);
			
						//redirect to notification page
						//$this->view_fns->view('global/exception', $data);
						
						$data['page_title'] = 'Edit Profile - Feedback';
						$this->view_fns->view('global/profile/profile_feedback', $data);
					}
					else
					{
						
					
						//Input and textarea field attributes
						$data['oldpassword'] = array('name' => 'oldpassword', 'id' => 'oldpassword','maxlength' => '30', 'size' => '30');
						$data['newpassword1'] = array('name' => 'newpassword1', 'id' => 'newpassword1','maxlength' => '30', 'size' => '30');
						$data['newpassword2'] = array('name' => 'newpassword2', 'id' => 'newpassword2','maxlength' => '30', 'size' => '30');
			
						if($valid == 'true'){
			
							//Exception Data
							$data['reqd1'] = "<span class=\"exception\">";
							$data['reqd2'] = "</span>";
							$data['exception'] = "The 'current password' you supplied was incorrect.";
			
						}
						
						//grab userdata
						if ($user_id != NULL){
								$userdata = $this->Auth_model->get_passchange_output($user_id);
						}
						else{
							$userdata = array();
						}
			
						//inject javascript
						$data['javascript_file_array'] = array('passStrength');
						
						$data['page_title'] = 'Edit Profile - Password';
						$this->view_fns->view('global/profile/profile_credentials', $data);
						
					}
			
				}
			//close logged in	
			}	
			else{
				//handle the previous page writer
				$sessiondata['return_page'] = 'profile/credentials/';										
				//set data in session
				$this->session->set_userdata($sessiondata);
				
				//redirect
				redirect('auth/login');
			}
			
		//close credentials
		}





	
	
	function awards($current_pilot_id = NULL){
	
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
	
		//logged in
		if($this->session->userdata('logged_in') == '1'){
		
		
			if($current_pilot_id == NULL || !is_numeric($current_pilot_id)){
				redirect('profile/awards/'.$this->session->userdata('user_id'));
			}
			$current_pilot_username = $this->session->userdata('username');
			
			
			$query = $this->db->query("	SELECT 	pilots.id as id,
												pilots.fname,
												pilots.sname,
												pilots.username,
												ranks.name as rank
												
										FROM pilots
										
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
										
										WHERE pilots.id = '$current_pilot_id'
												
												");
												
			$pilot_result = $query->result_array();
			$num_pilot =  $query->num_rows();
			
			//handle case of no data retuend on pilot
			if($num_pilot > 0){
				$data['selected_name'] = $pilot_result['0']['fname'].' '.$pilot_result['0']['sname'];
				$data['selected_username'] = $pilot_result['0']['username'];
				$data['selected_rank'] = $pilot_result['0']['rank'];
			}
			else{
				redirect('profile/awards/'.$this->session->userdata('user_id'));
			}
			
			$data['current_pilot_id'] = $current_pilot_id;
		
		
			
			
			//query
			$query = $this->db->query("	SELECT 	awards_assigned.id as id,
												awards_assigned.user_id as user_id,
												awards_assigned.awards_index_id as awards_index_id,
												awards_index.description as description,
												awards_index.aggregate_award_rank as award_rank, 
												awards_index.award_name as award_name,
												awards_index.aggregate_award_name as aggregate_award_name
													
											FROM awards_index
											
												LEFT JOIN awards_assigned
												ON awards_index.id = awards_assigned.awards_index_id
											
											WHERE awards_assigned.user_id = '$current_pilot_id'
											
											ORDER BY awards_index.tour, awards_index.event, awards_index.aggregate_award_rank, awards_assigned.assigned_date DESC
												
												");
				
				
				$awards =  $query->result();	
				$num_awards =  $query->num_rows();	
				
				$i = 0;
				$data['awards'] = array();
				foreach($awards as $row){
				
					$aggregate_award_name = $row->aggregate_award_name;
					
					if(!array_key_exists($aggregate_award_name, $data['awards']) 
					|| (array_key_exists($aggregate_award_name, $data['awards']) && $row->award_rank <= $data['awards'][$aggregate_award_name]['award_rank'] )
					){
						$data['awards'][$aggregate_award_name]['award_rank'] = $row->award_rank;
						$data['awards'][$aggregate_award_name]['id'] = $row->id;
						$data['awards'][$aggregate_award_name]['awards_index_id'] = $row->awards_index_id;
						$data['awards'][$aggregate_award_name]['description'] = $row->description;
						$data['awards'][$aggregate_award_name]['award_name'] = $row->award_name;
					
					
					
						$i++;
					}
				}
				
				//$data['awards'] =
				$data['num_awards'] = $i;
				
			$data['page_title'] = 'Profile - Awards';
			$this->view_fns->view('global/profile/profile_awards', $data);
	
		}
		else{
			//handle the previous page writer
			$sessiondata['return_page'] = 'profile/awards/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			//redirect
			redirect('auth/login');
		}
	}
	
	function stats($current_pilot_id = NULL){
	
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Format_fns');
		
		//logged in
		if($this->session->userdata('logged_in') == '1'){
		
			if($current_pilot_id == NULL || !is_numeric($current_pilot_id)){
				redirect('profile/stats/'.$this->session->userdata('user_id'));
			}
			$current_pilot_username = $this->session->userdata('username');
			
			
			$query = $this->db->query("	SELECT 	pilots.id as id,
												pilots.fname,
												pilots.sname,
												pilots.username,
												ranks.name as rank
												
										FROM pilots
										
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
										
										WHERE pilots.id = '$current_pilot_id'
												
												");
												
			$pilot_result = $query->result_array();
			$num_pilot =  $query->num_rows();
			
			//handle case of no data retuend on pilot
			if($num_pilot > 0){
				$data['selected_name'] = $pilot_result['0']['fname'].' '.$pilot_result['0']['sname'];
				$data['selected_username'] = $pilot_result['0']['username'];
				$data['selected_rank'] = $pilot_result['0']['rank'];
			}
			else{
				redirect('profile/stats/'.$this->session->userdata('user_id'));
			}
			
			$data['current_pilot_id'] = $current_pilot_id;
		
			
			//query
			$query = $this->db->query("	SELECT 	pirep.id as id,
													pirep.start_icao as start_icao,
													start_airport.name as start_name,
													start_airport.country as start_country,
													start_airports_data.lat as dep_lat,
													start_airports_data.long as dep_long,
													end_airport.name as end_name,
													end_airport.country as end_country,
													end_airports_data.lat as arr_lat,
													end_airports_data.long as arr_long,
													pirep.end_icao as end_icao,
													pirep.passengers as passengers,
													pirep.cargo as cargo,
													pirep.submitdate  as submitdate,
													pirep.departure_time  as departure_time,
													pirep.landing_time  as landing_time,
													pirep.engine_start_time  as engine_start_time,
													pirep.engine_stop_time  as engine_stop_time,
													pirep.checked  as checked,
													pirep.comments  as comments,
													pirep.fl_version  as fl_version,
													pirep.propilot_flight  as propilot_flight,
													networks.name  as onoffline,
													pirep.cruisealt as cruisealt,
													pirep.cruisespd as cruisespd,
													pirep.approach as approach,
													pirep.fuelburnt as fuelburnt,
													pirep.pp_score as pp_score,
													aircraft.name as aircraft,
													pirep.aircraft as aircraft_id
													
											FROM pirep
											
												LEFT JOIN aircraft
												ON aircraft.id = pirep.aircraft
												
												LEFT JOIN airports as start_airport
												ON start_airport.icao = pirep.start_icao
												
												LEFT JOIN airports_data as start_airports_data
												ON start_airports_data.icao = start_airport.icao
												
												LEFT JOIN airports as end_airport
												ON end_airport.icao = pirep.end_icao
												
												LEFT JOIN airports_data as end_airports_data
												ON end_airports_data.icao = end_airport.icao
												
												LEFT JOIN networks
												ON networks.id = pirep.onoffline
											
											WHERE pirep.user_id = '$current_pilot_id'
											AND pirep.checked = '1'
											
											ORDER BY departure_time ASC
											
													
												");
				
				
				$data['timetable_flights'] =  $query->result();	
				$data['num_flights'] =  $query->num_rows();	
				
				$query = $this->db->query("	SELECT 	country,
													name
													
											FROM	countries
				
				
				");
				
				$data['country_data'] = $query->result();	
				
				$data['countries'] = array();
				foreach($data['country_data'] as $row){
					$data['countries'][$row->country] = $row->name;
				}
				
				//create stats
				$data['aircraft'] = array();
				$data['fav_aircraft'] = array();
				$data['fav_aircraft_pax'] = array();
				$data['fav_aircraft_cargo'] = array();
				$data['fav_aircraft_porder'] = array();
				$data['fav_aircraft_corder'] = array();
				$data['fav_aircraft_time'] = array();
				$data['fav_aircraft_torder'] = array();
				$data['fav_aircraft_flights'] = array();
				$data['fav_countries'] = array();
				$data['dep_airport'] = array();
				$data['arr_airport'] = array();
				$data['flight_types'] = array();
				$data['online_num'] = 0;
				$data['offline_num'] = 0;
				foreach($data['timetable_flights'] as $flight){
				
					//Favourite Aircraft flights
					if(!array_key_exists($flight->aircraft_id, $data['fav_aircraft'])){
						$data['fav_aircraft'][$flight->aircraft_id] = 0;
						$data['fav_aircraft_time'][$flight->aircraft_id] = 0;
					}
					
						$data['fav_aircraft'][$flight->aircraft_id]++;
						$flighttime = strtotime($flight->engine_stop_time) - strtotime($flight->engine_start_time);
						$data['fav_aircraft_time'][$flight->aircraft_id]+= $flighttime;
					
					
					//Favourite Aircraft time
					if(!array_key_exists($flight->aircraft_id, $data['fav_aircraft_torder'])){
						$data['fav_aircraft_torder'][$flight->aircraft_id] = 0;
						$data['fav_aircraft_flights'][$flight->aircraft_id] = 0;
					}
					
						$data['fav_aircraft_flights'][$flight->aircraft_id]++;
						$flighttime = strtotime($flight->engine_stop_time) - strtotime($flight->engine_start_time);
						$data['fav_aircraft_torder'][$flight->aircraft_id]+= $flighttime;
					
					
					//Favourite Aircraft pax and cargo
					
					if(!array_key_exists($flight->aircraft_id, $data['fav_aircraft_porder'])){
						$data['fav_aircraft_porder'][$flight->aircraft_id] = 0;
					}
					
					if(!array_key_exists($flight->aircraft_id, $data['fav_aircraft_cargo'])){
						$data['fav_aircraft_cargo'][$flight->aircraft_id] = 0;
					}
					
					$data['fav_aircraft_cargo'][$flight->aircraft_id]+= $this->format_fns->lbs_tonnes($flight->cargo);
					$data['fav_aircraft_porder'][$flight->aircraft_id]+= $flight->passengers;
					
					
					
					//list of aircraft names
					$data['aircraft'][$flight->aircraft_id] = $flight->aircraft;
					
					//Favourite Countries
						//start country
						if(!array_key_exists($flight->start_country, $data['fav_countries'])){
							$data['fav_countries'][$flight->start_country] = 0;
						}
						
						$data['fav_countries'][$flight->start_country]++;
						
						//end country
						if(!array_key_exists($flight->end_country, $data['fav_countries'])){
							$data['fav_countries'][$flight->end_country] = 0;
						}
						
						$data['fav_countries'][$flight->end_country]++;
						
						
						
						
					//Favourite Departure Airports
					
					
					//Favourite Destination Airports
					
					//Flight Types
					
					//Online/Offline
				
				
				}
				
				//sort the arrays into descending size
				arsort($data['fav_aircraft']);
				arsort($data['fav_aircraft_torder']);
				arsort($data['fav_aircraft_porder']);
				arsort($data['fav_aircraft_cargo']);
				arsort($data['fav_countries']);
			
		
			$data['page_title'] = 'Profile - Stats';
			$this->view_fns->view('global/profile/profile_stats', $data);
	
		}
		else{
			//handle the previous page writer
			$sessiondata['return_page'] = 'profile/stats/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			//redirect
			redirect('auth/login');
		}
	}
	
	
	
	function flightdata_print($pirep_id){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Geocalc_fns');
		
		//logged in
		if($this->session->userdata('logged_in') == '1'){
		
		
		
			//query to grab pirep details
			$query = $this->db->query("	SELECT 	pirep.id as id,
														pirep.start_icao as start_icao,
														start_airport.name as start_name,
														start_country.name as start_country,
														start_airports_data.lat as dep_lat,
														start_airports_data.long as dep_long,
														end_airport.name as end_name,
														end_country.name as end_country,
														end_airports_data.lat as arr_lat,
														end_airports_data.long as arr_long,
														pirep.end_icao as end_icao,
														pirep.passengers  as passengers,
														pirep.cargo  as cargo,
														pirep.submitdate  as submitdate,
														pirep.departure_time  as departure_time,
														pirep.landing_time  as landing_time,
														pirep.engine_start_time  as engine_start_time,
														pirep.engine_stop_time  as engine_stop_time,
														pirep.checked  as checked,
														pirep.comments  as comments,
														pirep.fl_version  as fl_version,
														pirep.propilot_flight  as propilot_flight,
														networks.name  as onoffline,
														pirep.cruisealt as cruisealt,
														pirep.cruisespd as cruisespd,
														approachcode.code_description as approach,
														pirep.fuelburnt as fuelburnt,
														pirep.pp_score as pp_score,
														aircraft.name as aircraft,
														pilots.fname,
														pilots.sname,
														pilots.username
														
												FROM pirep
												
													LEFT JOIN aircraft
													ON aircraft.id = pirep.aircraft
													
													LEFT JOIN airports as start_airport
													ON start_airport.icao = pirep.start_icao
													
													LEFT JOIN countries as start_country
													ON start_country.country = start_airport.country
													
													LEFT JOIN airports_data as start_airports_data
													ON start_airports_data.icao = start_airport.icao
													
													LEFT JOIN airports as end_airport
													ON end_airport.icao = pirep.end_icao
													
													LEFT JOIN countries as end_country
													ON end_country.country = end_airport.country
													
													LEFT JOIN airports_data as end_airports_data
													ON end_airports_data.icao = end_airport.icao
													
													LEFT JOIN networks
													ON networks.id = pirep.onoffline
													
													LEFT JOIN pilots
													ON pilots.id = pirep.user_id
													
													LEFT JOIN config_codesets as approachcode
													ON approachcode.code_id = pirep.approach
													AND approachcode.type = 'approach'
										
										WHERE pirep.id = '$pirep_id'
										
										LIMIT 1
														
									");
		
			$flight_data =  $query->result_array();	
			$num_flights =  $query->num_rows();	
		
		
			if($num_flights > 0){
			
			//caluclate distabnce between airports
		$gcd_km = $this->geocalc_fns->GCDistance($flight_data[0]['dep_lat'], $flight_data[0]['dep_long'], $flight_data[0]['arr_lat'], $flight_data[0]['arr_long']);
		$data['gcd_nm'] = number_format($this->geocalc_fns->ConvKilometersToMiles($gcd_km)).' nm';
		
		
			
				$data['pilot_id'] = 'EHM-'.$flight_data[0]['username'];
				$data['pilot_in_command'] = $flight_data[0]['fname'].' '.$flight_data[0]['sname'];
				if($flight_data[0]['engine_start_time'] != '' && $flight_data[0]['engine_start_time'] != '0000-00-00 00:00:00'){
				$data['flight_date'] = gmdate('d/m/Y', strtotime($flight_data[0]['engine_start_time']));
				}
				else{
					$data['flight_date'] = 'Unknown';
				}
				$data['aircraft'] = $flight_data[0]['aircraft'];
				$data['origin'] = $flight_data[0]['start_icao'].' '.$flight_data[0]['start_name'].' ('.$flight_data[0]['start_country'].')';
				$data['destination'] = $flight_data[0]['end_icao'].' '.$flight_data[0]['end_name'].' ('.$flight_data[0]['end_country'].')';
				
				$data['onoffline'] = $flight_data[0]['onoffline'];
				$data['passengers'] = number_format($flight_data[0]['passengers']);
				$data['cargo'] = number_format($flight_data[0]['cargo']).' lbs';
				
				
				if($flight_data[0]['engine_start_time'] != '' && $flight_data[0]['engine_start_time'] != '0000-00-00 00:00:00'){
				$data['engine_start_time'] = gmdate('H:i', strtotime($flight_data[0]['engine_start_time']));
				}
				else{
					$data['engine_start_time'] = 'Unknown';
				}
				
				
				if($flight_data[0]['departure_time'] != '' && $flight_data[0]['departure_time'] != '0000-00-00 00:00:00'){
				$data['departure_time'] = gmdate('H:i', strtotime($flight_data[0]['departure_time']));
				}
				else{
					$data['departure_time'] = 'Unknown';
				}
				
				
				if($flight_data[0]['landing_time'] != '' && $flight_data[0]['landing_time'] != '0000-00-00 00:00:00'){
				$data['landing_time'] = gmdate('H:i', strtotime($flight_data[0]['landing_time']));
				}
				else{
					$data['landing_time'] = 'Unknown';
				}
				
				
				if($flight_data[0]['engine_stop_time'] != '' && $flight_data[0]['engine_stop_time'] != '0000-00-00 00:00:00'){
				$data['engine_stop_time'] = gmdate('H:i', strtotime($flight_data[0]['engine_stop_time']));
				}
				else{
					$data['engine_stop_time'] = 'Unknown';
				}
				
				
				$data['cruisealt'] = number_format($flight_data[0]['cruisealt']).' ft';
				$data['cruisespd'] = $flight_data[0]['cruisespd'];
				$data['approach'] = $flight_data[0]['approach'];
				$data['fuelburnt'] = $flight_data[0]['fuelburnt'];
				$data['comments'] = nl2br(htmlspecialchars($flight_data[0]['comments']));
				$data['fl_version'] = $flight_data[0]['fl_version'];
				
				
		
				$this->load->view('global/profile/profile_flightdata_print.php', $data);
			}
			else{
				//redirect
				redirect('');
			}
			
		}
		else{
			//redirect
			redirect('');
		}
	
	}
	
	
	function flightlog($current_pilot_id = NULL, $offset = NULL)
	{
	
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Profile_fns');
		$this->load->library('pagination');
		$this->load->library('Geocalc_fns');
		
		
		$data['current_pilot_id'] = $current_pilot_id;
		
		//logged in
		if($this->session->userdata('logged_in') == '1'){
			
			if($current_pilot_id == NULL || !is_numeric($current_pilot_id)){
				redirect('profile/flightlog/'.$this->session->userdata('user_id').'/e/');
			}
			$current_pilot_username = $this->session->userdata('username');
			
			
			$query = $this->db->query("	SELECT 	pilots.id as id,
												pilots.fname,
												pilots.sname,
												pilots.username,
												ranks.name as rank
												
										FROM pilots
										
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
										
										WHERE pilots.id = '$current_pilot_id'
												
												");
												
			$pilot_result = $query->result_array();
			$num_pilot =  $query->num_rows();
			
			//handle case of no data retuend on pilot
			if($num_pilot > 0){
				$data['selected_name'] = $pilot_result['0']['fname'].' '.$pilot_result['0']['sname'];
				$data['selected_username'] = $pilot_result['0']['username'];
				$data['selected_rank'] = $pilot_result['0']['rank'];
			}
			else{
				redirect('profile/flightlog/'.$this->session->userdata('user_id').'/e/');
			}
			
			//grab the pilot's flights, count for pagination and set to last page
					$query = $this->db->query("	SELECT 	pirep.id as id,
														pirep.start_icao as start_icao,
														start_airport.name as start_name,
														start_airport.country as start_country,
														start_airports_data.lat as dep_lat,
														start_airports_data.long as dep_long,
														end_airport.name as end_name,
														end_airport.country as end_country,
														end_airports_data.lat as arr_lat,
														end_airports_data.long as arr_long,
														pirep.end_icao as end_icao,
														pirep.passengers  as passengers,
														pirep.cargo  as cargo,
														pirep.submitdate  as submitdate,
														pirep.departure_time  as departure_time,
														pirep.landing_time  as landing_time,
														pirep.engine_start_time  as engine_start_time,
														pirep.engine_stop_time  as engine_stop_time,
														pirep.pausetime_mins,
														pirep.blocktime_mins,
														pirep.checked  as checked,
														pirep.comments  as comments,
														pirep.fl_version  as fl_version,
														pirep.propilot_flight  as propilot_flight,
														networks.name  as onoffline,
														pirep.cruisealt as cruisealt,
														pirep.cruisespd as cruisespd,
														pirep.approach as approach,
														pirep.fuelburnt as fuelburnt,
														pirep.pp_score as pp_score,
														pirep.pp_score_ng as pp_score_ng,
														aircraft.name as aircraft
														
												FROM pirep
												
													LEFT JOIN aircraft
													ON aircraft.id = pirep.aircraft
													
													LEFT JOIN airports as start_airport
													ON start_airport.icao = pirep.start_icao
													
													LEFT JOIN airports_data as start_airports_data
													ON start_airports_data.icao = start_airport.icao
													
													LEFT JOIN airports as end_airport
													ON end_airport.icao = pirep.end_icao
													
													LEFT JOIN airports_data as end_airports_data
													ON end_airports_data.icao = end_airport.icao
													
													LEFT JOIN networks
													ON networks.id = pirep.onoffline
												
												WHERE pirep.user_id = '$current_pilot_id'
												AND pirep.checked = '1'
												
												ORDER BY departure_time ASC
												
														
													");
					
					
					$data['timetable_flights'] =  $query->result();	
					$data['num_flights'] =  $query->num_rows();	
			
					$data['limit'] = '10';
			
					if($offset == 'e'){
						$offset = floor(($data['num_flights']-1)/$data['limit'])*$data['limit'];
						if($offset < 0){
							$offset = 0;
						}
						redirect('profile/flightlog/'.$current_pilot_id.'/'.$offset);
						//$offset = 0;
					}
					
					$data['offset'] = $offset;
					
					
					$pag_config['base_url'] = $data['base_url'].'profile/flightlog/'.$current_pilot_id.'/';
					$pag_config['total_rows'] = $data['num_flights'];
					$pag_config['per_page'] = $data['limit'];
					//$config['first_link'] = 'start';
					$pag_config['uri_segment'] = 4;
					
					$this->pagination->initialize($pag_config); 
					//$data['num_flights'] = $current_pilot_username;
					
					$data['page_title'] = 'Profile - Flightlog';
					$this->view_fns->view('global/profile/profile_flightlog', $data);
			
		}
		else{
			//handle the previous page writer
			$sessiondata['return_page'] = 'profile/flightlog/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			//redirect
			redirect('auth/login');
		}
	
	}
	
	function index($current_pilot_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Profile_fns');
		$this->load->library('Format_fns');
		
		//logged in
		if($this->session->userdata('logged_in') == '1'){
			
			if($current_pilot_id == NULL || !is_numeric($current_pilot_id)){
				redirect('profile/index/'.$this->session->userdata('user_id'));
			}
			$current_pilot_username = $this->session->userdata('username');
			
			
			$query = $this->db->query("	SELECT 	pilots.id as id,
												pilots.fname,
												pilots.sname,
												pilots.username,
												ranks.name as rank
												
										FROM pilots
										
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
										
										WHERE pilots.id = '$current_pilot_id'
												
												");
												
			$pilot_result = $query->result_array();
			$num_pilot =  $query->num_rows();
			
			//handle case of no data retuend on pilot
			if($num_pilot > 0){
				$data['selected_name'] = $pilot_result['0']['fname'].' '.$pilot_result['0']['sname'];
				$data['selected_username'] = $pilot_result['0']['username'];
				$data['selected_rank'] = $pilot_result['0']['rank'];
			}
			else{
				redirect('profile/index/'.$this->session->userdata('user_id'));
			}
			
			$data['current_pilot_id'] = $current_pilot_id;
				
			//grab pilot information
			$query = $this->db->query("	SELECT 	pilots.id as id,
												pilots.date_of_birth as date_of_birth,
												pilots.signupdate as joined,
												pilots.lastflight as lastflight,
												status.name as status,
												pilots.flighthours as flighthours,
												pilots.flightmins as flightmins,
												pilots.curr_location as curr_location,
												pilots.pp_location as pp_location,
												ppairports.name as pp_loc_name,
												stdairports.name curr_loc_name,
												pilots.emailaddress as emailaddress,
												flight_sim_versions.version_name as fsversion,
												pilots.pp_lastflight as pp_lastflight
												
												
												
										FROM pilots
										
											LEFT JOIN status
											ON status.id = pilots.status
											
											LEFT JOIN flight_sim_versions
											ON flight_sim_versions.id = pilots.fsversion
											
											LEFT JOIN airports as ppairports
											ON ppairports.icao = pilots.pp_location
											
											LEFT JOIN airports as stdairports
											ON stdairports.icao = pilots.curr_location
										
										WHERE pilots.id = '$current_pilot_id'
										
												
											");
					
			$pilot_data =  $query->result_array();
			
			//initialise data
			$data['age'] = '';
			$data['joined'] = '';
			$data['lastflight'] = '';
			$data['status'] = '';
			$data['flighthours'] = '';
			$data['flightmins'] = '';
			$data['next_rank'] = '';
			$data['timetorank'] = '';
			$data['curr_location'] = '';
			$data['pp_location'] = '';
			$data['pp_lastflight'] = '';
			$data['emailaddress'] = '';
			$data['fsversion'] = '';
			
			$data['num_flights'] = '';
			$data['num_passengers'] = '';
			$data['num_cargo'] = '';
			
			//set data if we have database return
			if(array_key_exists('0', $pilot_data)){
				
				$data['age'] = $this->profile_fns->get_age($pilot_data[0]['date_of_birth']);
				$data['joined'] = date('d/m/Y',strtotime($pilot_data[0]['joined']));
				$data['lastflight'] = date('d/m/Y',strtotime($pilot_data[0]['lastflight']));
				$data['status'] = $pilot_data[0]['status'];
				$data['flighthours'] = $pilot_data[0]['flighthours'];
				$data['flightmins'] = $pilot_data[0]['flightmins'];
				$data['curr_location'] = $pilot_data[0]['curr_location'].' '.$pilot_data[0]['curr_loc_name']; 
				$data['pp_location'] = $pilot_data[0]['pp_location'].' '.$pilot_data[0]['pp_loc_name']; 
				$data['pp_lastflight'] = $pilot_data[0]['pp_lastflight'];
				$data['emailaddress'] = $pilot_data[0]['emailaddress'];
				$data['fsversion'] = $pilot_data[0]['fsversion'];
				
			}
			
			//grab pilot stats
			$query = $this->db->query("	SELECT 	COUNT(id) as num_flights,
												SUM(passengers) as num_passengers,
												SUM(cargo) as num_cargo
												
										FROM pirep
										
										WHERE pirep.user_id = '$current_pilot_id'
										AND checked = '1'
										
												
											");
			
			$pilot_stats =  $query->result_array();
			$num_pilot_stats =  $query->num_rows();	
			
			if($num_pilot_stats > 0){
				$data['num_flights'] = $pilot_stats[0]['num_flights'];
				$data['num_passengers'] = $pilot_stats[0]['num_passengers'];
				//convert lbs to metric tonnes
				$data['num_cargo'] = $this->format_fns->lbs_tonnes($pilot_stats[0]['num_cargo']);
			}
			
			
			$current_rank = $this->session->userdata('rank_id');
			$next_rank = $current_rank+1;
			//if at max rank
			if($next_rank >= 7){
				$data['next_rank'] = '-';
				$data['timetorank'] = '-';
			}
			else{
			
				//grab rank data
				$query = $this->db->query("	SELECT 	id,
													rank,
													hours
													
											FROM ranks
											
											WHERE ranks.id = '$next_rank'
													
												");
				
				$pilot_rank =  $query->result_array();
				$num_pilot_rank =  $query->num_rows();	
				
				if($num_pilot_rank > 0){
					$data['next_rank'] = $pilot_rank[0]['rank'];
					
					//calculate time to rank
					$flightmins = ($data['flighthours']*60) + $data['flightmins'];
					$nextmins = ($pilot_rank[0]['hours']*60);
					
					$togodiff = $nextmins - $flightmins;
					$timetorank_hours = floor($togodiff / 60);
					$timetorank_min = $togodiff - ($timetorank_hours*60);
					
					$data['timetorank'] = $timetorank_hours.'h '.$timetorank_min.'m';
				}
			
			}
			
			//grab the stats for propilot
			$query = $this->db->query("	SELECT 
											AVG(pirep.pp_score_ng) as pp_average,
											SUM(pirep.pp_score_ng) as pp_sum,
											COUNT(pirep.pp_score_ng) as pp_count,
											pirep.user_id
											
											
									FROM pirep
									
									WHERE pirep.submitdate >= '$ppstats_compare_datetime'
									AND pirep.propilot_flight = '1'
									AND pirep.user_id = '$current_pilot_id'
									
									GROUP BY pirep.user_id
									
									LIMIT 1
										
									");
											
			$pp_score_result = $query->result_array();
			$num_results = $query->num_rows();
			
			if($num_results < 1){
				$data['pp_average'] = 'N/A';
				$data['pp_sum'] = 'N/A';
				$data['pp_count'] = 'N/A';
			}
			else{
				$data['pp_average'] = $pp_score_result['0']['pp_average'];
				$data['pp_sum'] = $pp_score_result['0']['pp_sum'];
				$data['pp_count'] = $pp_score_result['0']['pp_count'];
			}
			
			
			$data['page_title'] = 'Profile';
			$this->view_fns->view('global/profile/profile_index', $data);
			
			
		//close logged_in
		}
		else{
			//handle the previous page writer
			$sessiondata['return_page'] = 'profile/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			//redirect
			redirect('auth/login');
		}
		
		
	}
}

/* End of file */