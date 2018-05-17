<?php

class Acp_pilots extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function flightdelete($flight_id = NULL, $pilot_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');
		$this->load->model('Auth_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($flight_id == NULL || $pilot_id == NULL) {
			redirect('acp_pilots/manage');
		}

		$data['flight_id'] = $flight_id;
		$data['pilot_id'] = $pilot_id;

		//check if user is already logged in - if not, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} //all good, do your stuff
		elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$post_flight_id = $this->security->sanitize_filename($this->input->post('flight_id'));
			$confirmed = $this->security->sanitize_filename($this->input->post('confirmed'));

			//grab flight details from database
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
													pilots.username as pilot_username,
													pilots.fname as pilot_fname,
													pilots.sname as pilot_sname,
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
												
												LEFT JOIN pilots
												ON pilots.id = pirep.user_id
											
											WHERE pirep.user_id = '$pilot_id'
											AND pirep.id = '$flight_id'
											
											LIMIT 1
											
													
												");

			$data['timetable_flights'] = $query->result_array();
			$data['num_flights'] = $query->num_rows();

			if (($valid != 'true' && $confirmed != '1') || $post_flight_id != $flight_id) {

				//output confirmation page
				$data['page_title'] = 'ACP Pilot - Approved Flight Delete';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_pilotflightdelete', $data);
			} else {

				if ($data['num_flights'] > 0 && $data['timetable_flights'][0]['id'] == $flight_id) {
					//erase flight data
					$this->db->where('user_id', $pilot_id);
					$this->db->where('id', $flight_id);
					$this->db->delete('pirep');

					//recalculate pilot hours and pilot rank
					$promoted = $this->Pirep_model->update_hours($pilot_id, '', 0);
				}

				//redirect
				redirect('profile/flightlog/' . $pilot_id);
			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/flightdelete/' . $flight_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}

	}

	function credentials($pilot_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');
		$this->load->model('Auth_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($pilot_id == NULL) {
			redirect('acp_pilots/manage');
		}

		$data['pilot_id'] = $pilot_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		$data['pilot_id'] = $pilot_id;

		//check if user is already logged in - if not, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//set user_id
			$user_id = $pilot_id;

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
			$result = $query->result();

			//ensure data clear
			$dbpassword = '';

			// set pulled date
			foreach ($result as $row) {
				if ($row->password) {
					$data['username'] = $row->username;
					$data['fname'] = $row->fname;
					$data['sname'] = $row->sname;
					$dbpassword = $row->password;
					$id = $row->id;
					$username = $row->username;
					$fname = $row->fname;
					$sname = $row->sname;
				} else {
					$data['username'] = 'Unknown Pilot';
					$data['fname'] = '';
					$data['sname'] = '';
					$dbpassword = '';
					$id = $row->id;
					$username = $row->username;
					$fname = $row->fname;
					$sname = $row->sname;
				}
			}

			//generate a password
			$generated_password = $this->auth_fns->generate_password();
			$data['generated_password'] = $generated_password;

			//Validation Rules - need to supply old password if changing own
			$this->form_validation->set_rules('newpassword1', 'newpassword1', 'required');
			$this->form_validation->set_rules('newpassword2', 'newpassword2', 'required');

			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			//$username = $this->session->userdata('username');

			//grab the new password data
			$newpassword1 = $this->security->sanitize_filename($this->input->post('newpassword1'));
			$newpassword2 = $this->security->sanitize_filename($this->input->post('newpassword2'));

			//check the submitted passwords match
			if ($newpassword1 != $newpassword2) {
				$valid = 'false';
				$exception = 'passmatch';
			} else {
				$exception = '';
			}

			//do this if the form hasn't been submitted or not all req'd info is given
			if ($valid != 'true' || $this->form_validation->run() == FALSE) {

				//grab all the userdata from database
				$userdata = $this->Auth_model->get_passchange_output($user_id);

				//set default password to the ne generated one

				//Input and textarea field attributes
				$data['newpassword1'] = array('name' => 'newpassword1', 'id' => 'newpassword1', 'maxlength' => '30', 'size' => '30', 'value' => $generated_password);
				$data['newpassword2'] = array('name' => 'newpassword2', 'id' => 'newpassword2', 'maxlength' => '30', 'size' => '30', 'value' => $generated_password);

				//if it was the validation run that failed, but was submitted...
				if ($this->form_validation->run() == FALSE && $valid == 'true') {

					//Exception Data
					$data['reqd1'] = "<span class=\"exception\">";
					$data['reqd2'] = "</span>";
					if ($exception == 'passmatch') {
						$data['exception'] = "Your new passwords did not match. Please try again.";
					} else {
						$data['exception'] = "You have not completed all the required fields.";
					}

				}

				//inject javascript
				$data['javascript_file_array'] = array('passStrength');

				//outut
				$data['page_title'] = 'ACP Pilot - Password Reset';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_pilotcredentials', $data);

			} //if the form has been submitted and all req'd info given
			else {

				//$oldpassword = $this->security->sanitize_filename($this->input->post('oldpassword'));

				//encrypt password to match database figure
				//$oldpassword = $this->auth_fns->hash_password($user_id, $oldpassword);

				//verify data against database

				//if we're sure everything is good
				//if(($oldpassword == $dbpassword && $newpassword1 == $newpassword2)){
				if ($newpassword1 == $newpassword2) {

					//hash the password using chosen encryption
					$hashed_password = $this->auth_fns->hash_password($user_id, $newpassword1);
					//write to the internal database
					$affected_rows = $this->Auth_model->write_passchange_internal($user_id, $hashed_password);

					$data = array_merge($data, $affected_rows);

					//redirect to notification page
					//$this->view_fns->view('global/exception', $data);

					//$data['page_title'] = 'Edit Profile - Feedback';
					//$this->view_fns->view('global/profile/profile_feedback', $data);

					redirect('acp_pilots/manage');
				} else {

					//Input and textarea field attributes
					$data['newpassword1'] = array('name' => 'newpassword1', 'id' => 'newpassword1', 'maxlength' => '30', 'size' => '30');
					$data['newpassword2'] = array('name' => 'newpassword2', 'id' => 'newpassword2', 'maxlength' => '30', 'size' => '30');

					if ($valid == 'true') {

						//Exception Data
						$data['reqd1'] = "<span class=\"exception\">";
						$data['reqd2'] = "</span>";
						$data['exception'] = "The 'current password' you supplied was incorrect.";

					}

					//grab userdata
					if ($user_id != NULL) {
						$userdata = $this->Auth_model->get_passchange_output($user_id);
					} else {
						$userdata = array();
					}

					//inject javascript
					$data['javascript_file_array'] = array('passStrength');

					$data['page_title'] = 'ACP Pilot - Password Reset';
					$data['admin_menu'] = 1;
					$this->view_fns->view('global/admincp/acp_pilotcredentials', $data);

				}

			}
			//close allowed	
		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/credentials/' . $pilot_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}

		//close credentials
	}

	function edit($pilot_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($pilot_id == NULL) {
			redirect('acp_pilots/manage');
		}

		$data['pilot_id'] = $pilot_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$title = $this->security->sanitize_filename($this->input->post('title'));
			$fname = $this->security->sanitize_filename($this->input->post('fname'));
			$sname = $this->security->sanitize_filename($this->input->post('sname'));
			$emailaddress = $this->security->sanitize_filename($this->input->post('emailaddress'));
			$country = $this->security->sanitize_filename($this->input->post('country'));

			$dobday = $this->security->sanitize_filename($this->input->post('dobday'));
			$dobmonth = $this->security->sanitize_filename($this->input->post('dobmonth'));
			$dobyear = $this->security->sanitize_filename($this->input->post('dobyear'));

			$hub = $this->security->sanitize_filename($this->input->post('hub'));
			$status = $this->security->sanitize_filename($this->input->post('status'));
			$fsversion = $this->security->sanitize_filename($this->input->post('fsversion'));
			$curr_location = $this->security->sanitize_filename($this->input->post('curr_location'));
			$pp_location = $this->security->sanitize_filename($this->input->post('pp_location'));

			$usergroup = $this->security->sanitize_filename($this->input->post('usergroup'));
			$department = $this->security->sanitize_filename($this->input->post('department'));
			$pips = $this->security->sanitize_filename($this->input->post('pips'));
			$title = $this->security->sanitize_filename($this->input->post('title'));

			if ($usergroup == '') {
				$usergroup = NULL;
				$title = NULL;
				$department = NULL;
				$pips = NULL;
			}

			if ($title == '') {
				$title = NULL;
			}

			if ($department == '') {
				$department = NULL;
			}

			if ($pips == '') {
				$pips = NULL;
			}

			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('fname', 'fname', 'required');
			$this->form_validation->set_rules('sname', 'sname', 'required');
			$this->form_validation->set_rules('emailaddress', 'emailaddress', 'required');
			$this->form_validation->set_rules('country', 'country', 'required');
			$this->form_validation->set_rules('dobday', 'dobday', 'required');
			$this->form_validation->set_rules('dobmonth', 'dobmonth', 'required');
			$this->form_validation->set_rules('dobyear', 'dobyear', 'required');
			$this->form_validation->set_rules('hub', 'hub', 'required');
			$this->form_validation->set_rules('status', 'status', 'required');
			$this->form_validation->set_rules('curr_location', 'curr_location', 'required');
			$this->form_validation->set_rules('pp_location', 'pp_location', 'required');

			if ($this->form_validation->run() == FALSE) {
				$validation = 0;
			} else {
				$validation = 1;
			}

			//need to determine whether or not this is a valid pilot - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											pilots.id,
											pilots.username,
											pilots.usergroup,
											pilots.department,
											pilots.title,
											pilots.management_pips,
											pilots.fname,
											pilots.sname,
											pilots.emailaddress,
											pilots.curr_location,
											pilots.pp_location,
											pilots.date_of_birth,
											pilots.email_valid,
											pilots.email_confirmed,
											pilots.country,
											pilots.hub,
											pilots.status as status_id,
											pilots.signupdate,
											pilots.lastflight,
											pilots.flighthours,
											pilots.flightmins,
											ranks.rank,
											pilots.fsversion,
											pilots.status as status_id,
											status.name as status
													
											FROM pilots
											
												LEFT JOIN ranks
												ON ranks.id = pilots.rank
												
												LEFT JOIN status
												ON status.id = pilots.status
												
											WHERE pilots.id = '$pilot_id'
											
											LIMIT 1
										");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			//redirect if no return
			if ($num_results < 1) {
				redirect('acp_pilots/manage/');
			}

			if ($valid == 'true' && $validation == 1) {

				//data has been submitted, array it and update the record

				//array update data
				$date_of_birth = $dobyear . '-' . $dobmonth . '-' . $dobday;

				$pilot_data = array(
					'fname' => $fname,
					'sname' => $sname,
					'emailaddress' => $emailaddress,
					'country' => $country,
					'date_of_birth' => $date_of_birth,
					'hub' => $hub,
					'status' => $status,
					'curr_location' => $curr_location,
					'pp_location' => $pp_location,
				);

				//only permit superadmin to modify the usergroup
				if ($this->session->userdata('usergroup') == 1) {
					$pilot_data['usergroup'] = $usergroup;
					$pilot_data['department'] = $department;
					$pilot_data['title'] = $title;
					$pilot_data['management_pips'] = $pips;
				}

				$id_val = $result['0']['id'];
				//perform the update from db
				$this->db->where('id', $id_val);
				$this->db->update('pilots', $this->db->escape($pilot_data));

				redirect('acp_pilots/manage/');

			} // haven't had data submitted or failed validation
			else {

				//prepare dropdowns etc for output from database
				$fname = $result['0']['fname'];
				$sname = $result['0']['sname'];
				$emailaddress = $result['0']['emailaddress'];
				$date_of_birth = $result['0']['date_of_birth'];
				$country = $result['0']['country'];
				$hub = $result['0']['hub'];
				$status = $result['0']['status_id'];
				$curr_location = $result['0']['curr_location'];
				$pp_location = $result['0']['pp_location'];

				$usergroup = $result['0']['usergroup'];
				$department = $result['0']['department'];
				$title = $result['0']['title'];
				$pips = $result['0']['management_pips'];

				$dobday = substr($date_of_birth, 8, 2);
				$dobmonth = substr($date_of_birth, 5, 2);
				$dobyear = substr($date_of_birth, 0, 4);

				//dropdowns
				$data['dobday'] = $dobday;
				$data['dobmonth'] = $dobmonth;
				$data['dobyear'] = $dobyear;
				$data['curr_location'] = $curr_location;
				$data['pp_location'] = $pp_location;
				$data['status'] = $status;
				$data['country'] = $country;
				$data['hub'] = $hub;
				$data['usergroup'] = $usergroup;
				$data['department'] = $department;
				$data['pips'] = $pips;

				//define form elements

				$data['fname'] = array('name' => 'fname', 'id' => 'fname', 'value' => $fname, 'maxlength' => '25', 'size' => '25');
				$data['sname'] = array('name' => 'sname', 'id' => 'sname', 'value' => $sname, 'maxlength' => '25', 'size' => '25');
				$data['emailaddress'] = array('name' => 'emailaddress', 'id' => 'emailaddress', 'value' => $emailaddress, 'maxlength' => '60', 'size' => '25');
				$data['title'] = array('name' => 'title', 'id' => 'title', 'value' => $title, 'maxlength' => '100', 'size' => '25');

				//define all the arrays			
				$data['country_array'] = array();
				$data['hub_array'] = array();
				$data['flightsim_array'] = array();
				$data['dobday_array'] = array('' => '');
				$data['dobmonth_array'] = array('' => '');
				$data['dobyear_array'] = array('' => '');
				$data['status_array'] = array();
				$data['location_array'] = array();
				$data['department_array'] = array();

				//get list of hubs from db
				$data['hub_array'] = $this->Pirep_model->get_hubs();

				//get countries from db
				$data['country_array'] = $this->Pirep_model->get_countries();

				//get list of flight sims
				$data['flightsim_array'] = $this->Pirep_model->get_flightsims();

				//get list of status
				$data['status_array'] = $this->Pirep_model->get_status();

				//get list of locations
				$data['location_array'] = $this->Pirep_model->get_locations();

				//if super-admin 
				if ($this->session->userdata('usergroup') == 1) {

					//get list of usergroups
					$query = $this->db->query("	SELECT 	
											usergroup_index.id,
											usergroup_index.name
													
											FROM usergroup_index
												
											ORDER BY usergroup_index.order, usergroup_index.name
										");

					$result = $query->result();
					$data['usergroup_array'] = array('' => '');
					foreach ($result as $row) {
						$data['usergroup_array'][$row->id] = $row->name;
					}

					//get array of departments
					$query = $this->db->query("	SELECT 	
													management_departments.id,
													management_departments.name
														
												FROM management_departments
													
												ORDER BY management_departments.order
											");

					$result = $query->result();
					$data['department_array'] = array('' => '');
					foreach ($result as $row) {
						$data['department_array'][$row->id] = $row->name;
					}

					//get array of management ranks
					$query = $this->db->query("	SELECT 	
													management_ranks.id,
													management_ranks.pips
														
												FROM management_ranks
													
												ORDER BY management_ranks.pips
											");

					$result = $query->result();
					$data['pips_array'] = array('' => '');
					foreach ($result as $row) {
						$data['pips_array'][$row->id] = $row->pips . ' Pips';;
					}
				}

				//day_array
				$i = 1;
				while ($i <= 31) {
					$ival = $i;
					if ($i < 10) {
						$ival = '0' . $i;
					}

					$data['dobday_array'][$ival] = $ival;
					$i++;
				}

				//month_array
				$i = 1;
				while ($i <= 12) {
					$ival = $i;
					if ($i < 10) {
						$ival = '0' . $i;
					}

					$data['dobmonth_array'][$ival] = $ival;
					$i++;
				}

				//year_array
				$current_year = date('Y', time());

				$i = $current_year - 8;
				while ($i >= ($current_year - 100)) {
					$data['dobyear_array'][$i] = $i;
					$i--;
				}

				//output page
				$data['page_title'] = 'ACP - Pilot Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_pilotedit', $data);
			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/edit/' . $pilot_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function delete($pilot_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($pilot_id == NULL) {
			redirect('acp_pilots/manage');
		}

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			$current_pilot_username = $this->session->userdata['username'];

			//need to determine whether or not this is a valid delete - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											pilots.id,
											pilots.username,
											pilots.usergroup,	
											pilots.title,
											pilots.fname,
											pilots.sname,
											pilots.email_valid,
											pilots.email_confirmed,
											pilots.country,
											pilots.hub,
											pilots.signupdate,
											pilots.lastflight,
											pilots.flighthours,
											pilots.flightmins,
											ranks.rank,
											pilots.fsversion,
											pilots.status as status_id,
											status.name as status
													
											FROM pilots
											
												LEFT JOIN ranks
												ON ranks.id = pilots.rank
												
												LEFT JOIN status
												ON status.id = pilots.status
												
											WHERE pilots.id = '$pilot_id'
											
											LIMIT 1
										");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($valid == 'true') {

				//if we actually got a hit back, then we're valid
				if ($num_results > 0) {

					//use the db returned value as an extra check
					$id_val = $result['0']['id'];
					$username = $result['0']['username'];
					//perform the delete from db
					$this->db->where('id', $id_val);
					$this->db->delete('pilots');

					//clear out any pireps for this pilot
					$this->db->where('user_id', $id_val);
					$this->db->or_where('username', $username);
					$this->db->delete('pirep');

					//clear out any assigned flights for this pilot
					$this->db->where('user_id', $id_val);
					$this->db->delete('pirep_assigned');

					//remove any awards for this pilot
					$this->db->where('user_id', $id_val);
					$this->db->delete('awards_assigned');

					//unlock any propilot flights
					//array data
					$propilot_aircraft_data = array(
						'reserved' => NULL,
						'reserved_by' => NULL,
						'destination' => NULL,
						'pax' => NULL,
						'cargo' => NULL,
					);

					//perform the update from db
					$this->db->where('reserved_by', $id_val);
					$this->db->update('propilot_aircraft', $propilot_aircraft_data);

				}

				//now redirect back to index
				redirect('acp_pilots/manage');

			} else {
				//if there is such a result
				if ($num_results > 0) {
					$data['fname'] = $result['0']['fname'];
					$data['username'] = $result['0']['username'];
					$data['sname'] = $result['0']['sname'];
					$data['rank'] = $result['0']['rank'];
					$data['signupdate'] = $result['0']['signupdate'];
					$data['status'] = $result['0']['status'];
					$data['pilot_id'] = $pilot_id;

					//output confirmation page
					$data['page_title'] = 'Delete confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/admincp/acp_pilotdelete', $data);
				} else {
					redirect('admincp/pirep_validate');
				}

			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/delete/' . $pilot_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function award_delete($pilot_id = NULL, $award_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($pilot_id == NULL && $award_id == NULL) {
			redirect('acp_pilots/manage');
		} elseif ($award_id == NULL) {
			redirect('acp_pilots/awards' . $pilot_id . '/0');
		}

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			//need to determine whether or not this is a valid delete - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											awards_assigned.id as id,
											awards_assigned.user_id as user_id,
											pilots.username,
											pilots.usergroup,	
											pilots.title,
											pilots.fname,
											pilots.sname,
											awards_index.award_name,
											awards_assigned.notes as notes,
											awards_assigned.awards_index_id as awards_index_id,
											awards_assigned.assigned_date
													
											FROM awards_assigned
											
												LEFT JOIN awards_index
												ON awards_index.id = awards_assigned.awards_index_id
												
												LEFT JOIN pilots
												ON pilots.id = awards_assigned.user_id
												
											WHERE awards_assigned.user_id = '$pilot_id'
											AND awards_assigned.id = '$award_id'
											
											LIMIT 1
										");
			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($valid == 'true') {

				//if we actually got a hit back, then we're valid
				if ($num_results > 0) {

					//use the db returned value as an extra check
					$id_val = $result['0']['id'];
					$user_id = $result['0']['user_id'];

					//perform the delete from db
					$this->db->where('id', $id_val);
					$this->db->where('user_id', $user_id);
					$this->db->delete('awards_assigned');

				}

				//now redirect back to index
				redirect('acp_pilots/awards/' . $user_id);

			} else {
				//if there is such a result
				if ($num_results > 0) {
					$data['fname'] = $result['0']['fname'];
					$data['username'] = $result['0']['username'];
					$data['sname'] = $result['0']['sname'];
					$data['award_name'] = $result['0']['award_name'];
					$data['pilot_id'] = $pilot_id;
					$data['award_id'] = $award_id;
					if ($result['0']['assigned_date'] != '' && $result['0']['assigned_date'] != '0000-00-00') {
						$data['assigned_date'] = gmdate('d/m/Y', strtotime($result['0']['assigned_date']));
					} else {
						$data['assigned_date'] = 'Unknown';
					}

					//output confirmation page
					$data['page_title'] = 'Delete confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/admincp/acp_pilotawarddelete', $data);
				} else {
					redirect('acp_pilots/manage');
				}

			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/award_delete/' . $pilot_id . '/' . $award_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function award_edit($pilot_id = NULL, $award_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($pilot_id == NULL && $award_id == NULL) {
			redirect('acp_pilots/manage');
		} elseif ($award_id == NULL) {
			redirect('acp_pilots/awards' . $pilot_id . '/0');
		}

		$data['pilot_id'] = $pilot_id;
		$data['award_id'] = $award_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$awards_index_id = $this->security->sanitize_filename($this->input->post('awards_index_id'));
			$notes = $this->security->sanitize_filename($this->input->post('notes'));

			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('awards_index_id', 'awards_index_id', 'required');

			if ($this->form_validation->run() == FALSE) {
				$validation = 0;
			} else {
				$validation = 1;
			}

			//need to determine whether or not this is a valid pilot, and a valid award - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											awards_assigned.id as id,
											pilots.username,
											pilots.usergroup,	
											pilots.title,
											pilots.fname,
											pilots.sname,
											awards_assigned.notes as notes,
											awards_assigned.awards_index_id as awards_index_id
													
											FROM awards_assigned
											
												LEFT JOIN pilots
												ON pilots.id = awards_assigned.user_id
												
											WHERE awards_assigned.user_id = '$pilot_id'
											AND awards_assigned.id = '$award_id'
											
											LIMIT 1
										");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			//redirect if no return
			if ($num_results < 1 && $award_id != 0) {
				redirect('acp_pilots/manage/');
			}

			if ($valid == 'true' && $validation == 1) {

				//data has been submitted, array it and update the record

				$award_data = array(
					'awards_index_id' => $awards_index_id,
					'notes' => $notes,
				);

				if ($award_id > 0) {
					//edit mode
					$id_val = $result['0']['id'];

					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->where('user_id', $pilot_id);
					$this->db->update('awards_assigned', $this->db->escape($award_data));
				} else {
					//otherwise is a new assignment
					$award_data['user_id'] = $pilot_id;
					$award_data['assigned_date'] = $gmt_mysql_datetime;

					$this->db->insert('awards_assigned', $this->db->escape($award_data));

				}

				redirect('acp_pilots/awards/' . $pilot_id);

			} // haven't had data submitted or failed validation
			else {

				$awards_index_id = '';
				$notes = '';

				if ($num_results > 0) {
					//prepare dropdowns etc for output from database
					$awards_index_id = $result['0']['awards_index_id'];
					$notes = $result['0']['notes'];
				}

				//dropdowns
				$data['awards_index_id'] = $awards_index_id;

				//define textarea elements
				$data['notes'] = array('name' => 'notes', 'id' => 'notes', 'value' => $notes, 'rows' => '10', 'cols' => '45');

				//define all the arrays			
				$data['awards_array'] = array('' => '');

				//grab awards for array
				$query = $this->db->query("	SELECT 	
										awards_index.id AS id,
										awards_index.award_name AS award_name,
										awards_index.automatic AS automatic,
										awards_index.tour AS tour,
										awards_index.event AS event
												
										FROM awards_index
											
										ORDER BY awards_index.award_name 
									");

				$result = $query->result();

				foreach ($result as $row) {

					$section = 'Other';
					if ($row->tour == 1) {
						$section = 'System - Tours';
					} elseif ($row->event == 1) {
						$section = 'System - Events';
					} elseif ($row->automatic == 'Y') {
						$section = 'System - Other';
					} else {
						$section = 'Manual';
					}

					$data['awards_array'][$section][$row->id] = $row->award_name;
				}

				ksort($data['awards_array']);

				//output page
				$data['page_title'] = 'ACP - Pilot Award Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_pilotawardedit', $data);
			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/award_edit/' . $pilot_id . '/' . $award_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function awards($pilot_id = NULL, $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($pilot_id == NULL || $pilot_id < 1) {
			redirect('acp_pilots/manage');
		}

		$data['pilot_id'] = $pilot_id;

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			$current_pilot_username = $this->session->userdata['username'];

			$query = $this->db->query("	SELECT 	
											pilots.id,
											pilots.username,
											pilots.usergroup,	
											pilots.title,
											pilots.fname,
											pilots.sname,
											pilots.status as status_id,
											status.name as status
											
											FROM pilots
												
												LEFT JOIN status
												ON status.id = pilots.status
											
											WHERE pilots.id = '$pilot_id'
											LIMIT 1
											");

			$pilot_data = $query->result_array();
			$num_rows = $query->num_rows();

			$data['pilot_name'] = 'Unknown Pilot';
			$data['status'] = '';
			$data['username'] = '';

			if ($num_rows > 0) {
				$data['pilot_name'] = $pilot_data[0]['fname'] . ' ' . $pilot_data[0]['sname'];
				$data['status'] = $pilot_data[0]['status'];
				$data['username'] = $pilot_data[0]['username'];
			}

			//need to grab pilot details and awards for output page
			$query = $this->db->query("	SELECT 	
											awards_assigned.id as id,
											awards_assigned.notes,
											awards_assigned.assigned_date,
											awards_index.award_name as award_name,
											awards_index.automatic as automatic
													
											FROM awards_assigned
											
												LEFT JOIN awards_index
												ON awards_index.id = awards_assigned.awards_index_id
												
											WHERE awards_assigned.user_id = '$pilot_id'
											
											ORDER BY awards_assigned.assigned_date DESC, awards_index.award_name
										");

			$data['results'] = $query->result();
			$num_results = $query->num_rows();

			//paginatipon
			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '10';

			$pag_config['base_url'] = $data['base_url'] . 'acp_pilots/awards/' . $pilot_id;
			$pag_config['total_rows'] = $num_results;
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 4;

			$this->pagination->initialize($pag_config);

			//output confirmation page
			$data['page_title'] = 'Pilot Award management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_pilotawards', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/awards/' . $pilot_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function manage($status_restrict = NULL, $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');

		if ($status_restrict == NULL) {
			redirect('acp_pilots/manage/0/');
		}

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		$data['status_restrict'] = $status_restrict;

		//grab post
		$post_status_restrict = $this->security->sanitize_filename($this->input->post('status_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));

		if ($status_restrict != $post_status_restrict && $post_status_restrict != '') {
			redirect('acp_pilots/manage/' . $post_status_restrict . '/');
		}

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$sqlsearch = '';
			//handle search
			if ($valid == 'true' && $search != '') {

				//split up the search into constituent terms
				$search_array = explode(" ", $search);
				$num_search = count($search_array);

				//for multiple term searches
				if ($num_search > 1) {
					$sqlsearch = "WHERE (fname LIKE '%" . $search . "%'";
					foreach ($search_array as &$row) {
						$sqlsearch .= " OR fname LIKE '%" . $row . "%'";
						$sqlsearch .= " OR sname LIKE '%" . $row . "%'";
						$sqlsearch .= " OR username LIKE '%" . $row . "%'";
						$sqlsearch .= " OR emailaddress LIKE '%" . $row . "%'";
					}
					$sqlsearch .= ')';
				} //for single term searches
				else {
					$sqlsearch = "WHERE (fname LIKE '%$search%' OR sname LIKE '%$search%' OR username LIKE '%$search%' OR emailaddress LIKE '%$search%')";
				}

			} else {

				//not searching, handle restriction
				$restrict = '';
				if ($status_restrict != 'ALL' && $status_restrict != 'MAN' && is_numeric($status_restrict)) {
					$sqlsearch = "WHERE pilots.status = '$status_restrict'";
				} elseif ($status_restrict == 'MAN') {
					$sqlsearch = "WHERE pilots.usergroup IS NOT NULL";
				}

			}

			//grab all pilots from the database
			$query = $this->db->query("	SELECT 	
											pilots.id,
											pilots.username,
											pilots.usergroup,	
											pilots.title,
											pilots.fname,
											pilots.sname,
											pilots.email_valid,
											pilots.email_confirmed,
											pilots.country,
											pilots.hub,
											pilots.signupdate,
											pilots.lastflight,
											pilots.flighthours,
											pilots.flightmins,
											ranks.rank,
											pilots.fsversion,
											pilots.status as status_id,
											status.name as status,
											usergroup_index.name as usergroup_name,
											usergroup_index.management as management,
											usergroup_index.admin_cp as admin_cp
													
											FROM pilots
											
												LEFT JOIN ranks
												ON ranks.id = pilots.rank
												
												LEFT JOIN status
												ON status.id = pilots.status
												
												LEFT JOIN usergroup_index
												ON usergroup_index.id = pilots.usergroup
												
											$sqlsearch
											
											ORDER BY pilots.username
										");

			$data['result'] = $query->result();
			$data['num_rows'] = $query->num_rows();

			//dropdown for status'
			$query = $this->db->query("	SELECT 	
												status.id,
												status.name
										FROM status
										ORDER BY status.id");

			$status_data = $query->result();

			$status_array = array('ALL' => 'All', 'MAN' => 'Management');
			//build status array
			foreach ($status_data as $row) {
				$status_array[$row->id] = $row->name;
			}

			$data['status_array'] = $status_array;

			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search', 'maxlength' => '25', 'size' => '25', 'value' => $search);

			//paginatipon
			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '15';

			$pag_config['base_url'] = $data['base_url'] . 'acp_pilots/manage/' . $status_restrict;
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 4;

			$this->pagination->initialize($pag_config);

			//output page
			$data['page_title'] = 'ACP - Pilot Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_pilotsmanage', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pilots/manage/' . $status_restrict . '/' . $offset;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

}

?>