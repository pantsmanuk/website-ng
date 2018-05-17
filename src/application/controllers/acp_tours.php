<?php

class Acp_tours extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function legs_edit($tour_id = NULL, $leg_id = NULL, $sequence = NULL, $flight_sim = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');
		$this->load->model('Award_model');

		$data['tour_id'] = $tour_id;
		$data['leg_id'] = $leg_id;
		$data['sequence'] = $sequence;
		$data['flight_sim'] = $flight_sim;
		$data['error'] = '';

		$data['highlight1'] = '';
		$data['highlight2'] = '';

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($tour_id == NULL || !is_numeric($tour_id)) {
			redirect('acp_tours/manage/');
		} elseif ($leg_id == NULL || !is_numeric($leg_id)) {
			redirect('acp_tours/edit/' . $tour_id);
		} elseif ($sequence == NULL || !is_numeric($sequence)) {
			redirect('acp_tours/edit/' . $tour_id);
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
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			//$post_flight_sim = $this->security->sanitize_filename($this->input->post('flight_sim'));
			//$sequence = $this->security->sanitize_filename($this->input->post('sequence'));
			$start_icao = $this->security->sanitize_filename($this->input->post('start_icao'));
			$end_icao = $this->security->sanitize_filename($this->input->post('end_icao'));
			$altitude = $this->security->sanitize_filename($this->input->post('altitude'));
			$award_id = $this->security->sanitize_filename($this->input->post('award_id'));
			$post_flight_sim = $this->security->sanitize_filename($this->input->post('flight_sim'));

			//handle the case where we're set to edit on a new sim create.
			if ($flight_sim == 'A') {
				$data['leg_id'] = '0';
			}

			if ($flight_sim == 'A' && $valid == 'true') {
				$flight_sim = $post_flight_sim;
				$data['flight_sim'] = $flight_sim;
			}

			$current_pilot_username = $this->session->userdata('username');
			$current_pilot_user_id = $this->session->userdata('user_id');

			//perform validation
			$this->form_validation->set_rules('start_icao', 'start_icao', 'required');
			$this->form_validation->set_rules('end_icao', 'end_icao', 'required');

			if ($this->form_validation->run() == FALSE) {
				$validation = 0;
			} else {
				$validation = 1;
			}

			if ($valid == 'true' && $validation == 1) {

				if ($award_id == '') {
					$award_id = NULL;
				}

				if ($altitude == '') {
					$altitude = NULL;
				}

				//check to make sure there's no duplicate
				$query = $this->db->query("	SELECT 	tour_legs.id as id
											
										FROM tour_legs
										
										WHERE tour_legs.tour_id = '$tour_id'
										AND tour_legs.flight_sim = '$flight_sim'
										AND tour_legs.start_icao = '$start_icao'
										AND tour_legs.end_icao = '$end_icao'
										AND tour_legs.altitude = '$altitude'
										AND tour_legs.award_id = '$award_id'
										
										LIMIT 1
										
										");

				$insert_data = $query->result_array();
				$num_insert = $query->num_rows();

				//array data
				$tour_legs_data = array(
					'tour_id' => $tour_id,
					'sequence' => $sequence,
					'flight_sim' => $flight_sim,
					'start_icao' => strtoupper($start_icao),
					'end_icao' => strtoupper($end_icao),
					'altitude' => $altitude,
					'award_id' => $award_id,
				);

				//only create a new record if there is no existing and we're creating new
				if ($num_insert < 1 && $leg_id == 0) {

					//insert the data					
					$this->db->insert('tour_legs', $this->db->escape($tour_legs_data));

				} elseif ($leg_id > 0) {

					//update the data	
					$this->db->where('id', $leg_id);
					$this->db->update('tour_legs', $this->db->escape($tour_legs_data));

				}

				//redirect
				redirect('acp_tours/legs/' . $tour_id . '/' . $flight_sim);

			}

			if ($validation == 0 && $valid == 'true') {
				$data['error'] = 'Required fields were not completed';

				$data['highlight1'] = '<font color="red">';
				$data['highlight2'] = '</font>';
			}

			//assemble form data
			$start_icao = '';
			$end_icao = '';
			$altitude = '';
			$award_id = '';

			//make a database call to see if record exists and populate values if it does
			if ($leg_id > 0) {
				$query = $this->db->query("	SELECT 	
												tour_legs.id as id,
												tour_legs.start_icao as start_icao,
												tour_legs.end_icao as end_icao,
												tour_legs.altitude as altitude,
												tour_legs.award_id as award_id
					
												
											FROM tour_legs
											
											WHERE tour_legs.id = '$leg_id'
											LIMIT 1
											
											");

				$record_data = $query->result_array();
				$num_records = $query->num_rows();

				if ($num_records > 0) {
					$start_icao = $record_data['0']['start_icao'];
					$end_icao = $record_data['0']['end_icao'];
					$altitude = $record_data['0']['altitude'];
					$award_id = $record_data['0']['award_id'];
				}

			}

			//make a call to check the arrival on last leg to populate the next
			if ($sequence > 1) {

				$prev_sequence = $sequence - 1;

				if ($flight_sim == NULL || $flight_sim == 'A') {
					$flight_sim_check = "AND tour_legs.flight_sim IS NULL";
				} else {
					$flight_sim_check = "AND tour_legs.flight_sim = '$flight_sim'";
				}

				$query = $this->db->query("	SELECT 	
												tour_legs.id as id,
												tour_legs.start_icao as start_icao,
												tour_legs.end_icao as end_icao,
												tour_legs.altitude as altitude,
												tour_legs.award_id as award_id
					
												
											FROM tour_legs
											
											WHERE tour_legs.tour_id = '$tour_id'
											AND tour_legs.sequence = '$prev_sequence'
											$flight_sim_check
											LIMIT 1
											
											");

				$record_data = $query->result_array();
				$num_records = $query->num_rows();

				if ($num_records > 0) {
					$start_icao = $record_data['0']['end_icao'];
				}
			}

			//dropdowns
			//$data['start_icao'] = $start_icao;
			//$data['end_icao'] = $end_icao;
			$data['award_id'] = $award_id;

			//form input
			$data['altitude'] = array('name' => 'altitude', 'id' => 'altitude', 'value' => $altitude, 'maxlength' => '6', 'size' => '6');
			$data['start_icao'] = array('name' => 'start_icao', 'id' => 'start_icao', 'value' => $start_icao, 'maxlength' => '4', 'size' => '4');
			$data['end_icao'] = array('name' => 'end_icao', 'id' => 'end_icao', 'value' => $end_icao, 'maxlength' => '4', 'size' => '4');

			//text area
			//$data['description'] = array( 'name' => 'description','id' => 'description','value' => $description, 'rows' => '10','cols' => '45');

			//download type dropdown array
			$data['sim_array'] = $this->Pirep_model->get_flightsims_raw();
			$data['award_array'] = $this->Award_model->get_awards_tour();

			//output page
			$data['page_title'] = 'Tour Legs Edit';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_tourlegsedit', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_tours/legs_edit/' . $tour_id . '/' . $leg_id . '/' . $sequence . '/' . $flight_sim;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function legs($tour_id = NULL, $selected_version = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');
		$this->load->model('Pirep_model');
		$this->load->library('Geocalc_fns');

		$data['tour_id'] = $tour_id;
		$data['error'] = '';

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($tour_id == NULL) {
			redirect('acp_tours/manage');
		}

		$data['selected_version'] = $selected_version;

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
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//sql query to grab all the tour data
			$query = $this->db->query("	SELECT 	tour_index.id as id,
											tour_index.name as name,
											tour_index.author as author,
											tour_index.length as length,
											tour_index.difficulty as difficulty,
											tour_index.description as description,
											tour_index.detail_info as detail_info,
											tour_index.requirements as requirements,
											ranks.name as rank_name,
											ranks.id as rank_id,
											tour_legs.id as leg_id,
											tour_legs.flight_sim as flight_sim_id,
											tour_legs.sequence as sequence,
											tour_legs.start_icao as start_icao,
											tour_legs.altitude as altitude,
											tour_legs.award_id as award_id,
											awards_index.award_name as award_name,
											start_data.name as start_name,
											start_data.lat as start_lat,
											start_data.long as start_lon,
											flight_sim_versions.version_name as version_name,
											
											tour_legs.end_icao as end_icao,
											end_data.name as end_name,
											end_data.lat as end_lat,
											end_data.long as end_lon
											
									FROM tour_index
									
										LEFT JOIN ranks
										ON ranks.class = tour_index.class
										
										LEFT JOIN tour_legs
										ON tour_legs.tour_id = tour_index.id
										
										LEFT JOIN airports_data as start_data
										ON start_data.icao = tour_legs.start_icao
										
										LEFT JOIN airports_data as end_data
										ON end_data.icao = tour_legs.end_icao
										
										LEFT JOIN flight_sim_versions
										ON flight_sim_versions.id = tour_legs.flight_sim
										
										LEFT JOIN awards_index
										ON awards_index.id = tour_legs.award_id
										
									WHERE tour_index.id = '$tour_id'
									
									ORDER BY tour_legs.flight_sim, tour_legs.sequence
											
										");

			$tour_data = $query->result_array();
			$data['tour_data'] = $tour_data;
			$data['num_rows'] = $query->num_rows();
			$data['tour_name'] = $tour_data[0]['name'];
			$data['tour_author'] = $tour_data[0]['author'];

			if ($data['num_rows'] > 0) {

				//assign into groups of flight sim versions
				$flight_array = array();
				$versions = array('' => 'Generic');
				$initial_version = '';

				$i = 0;
				foreach ($tour_data as $row) {
					//ensure that selected version is set
					if ($i == 0) {
						$initial_version = $row['flight_sim_id'];
					}

					//create versions array
					if ($row['flight_sim_id'] != '') {
						//array for menu
						$versions[$row['flight_sim_id']] = $row['version_name'];
					}

					$i++;
				}

				//handle cases where $selected_version not in array

				if (!array_key_exists($selected_version, $versions) && count($versions) > 0) {
					$selected_version = $initial_version;
				}

				$i = 0;
				foreach ($tour_data as $row) {

					//initialise array
					if (!array_key_exists($row['flight_sim_id'], $flight_array)) {
						$flight_array[$row['flight_sim_id']] = array();
					}

					//use this version of the leg if we have this selected or if default and not already written to
					if ($row['flight_sim_id'] == '' && !array_key_exists($row['sequence'], $flight_array)
						|| $selected_version == $row['flight_sim_id']) {

						//if this is the selected version
						if ($selected_version == $row['flight_sim_id']) {
							$flight_array[$row['sequence']]['alt'] = 1;
						} else {
							$flight_array[$row['sequence']]['alt'] = 0;
						}

						$flight_array[$row['sequence']]['selected_version'] = $row['flight_sim_id'];

						$flight_array[$row['sequence']]['leg_id'] = $row['leg_id'];
						$flight_array[$row['sequence']]['sequence'] = $row['sequence'];
						$flight_array[$row['sequence']]['start_icao'] = $row['start_icao'];
						$flight_array[$row['sequence']]['start_name'] = $row['start_name'];
						$flight_array[$row['sequence']]['end_icao'] = $row['end_icao'];
						$flight_array[$row['sequence']]['end_name'] = $row['end_name'];
						$flight_array[$row['sequence']]['altitude'] = $row['altitude'];
						$flight_array[$row['sequence']]['award_id'] = $row['award_id'];
						$flight_array[$row['sequence']]['award_name'] = $row['award_name'];

						//lon and lat
						$flight_array[$row['sequence']]['start_lat'] = $row['start_lat'];
						$flight_array[$row['sequence']]['start_lon'] = $row['start_lon'];
						$flight_array[$row['sequence']]['end_lat'] = $row['end_lat'];
						$flight_array[$row['sequence']]['end_lon'] = $row['end_lon'];

						$lat1 = $row['start_lat'];
						$lon1 = $row['start_lon'];
						$lat2 = $row['end_lat'];
						$lon2 = $row['end_lon'];

						//calculate radial
						if (($lat1 == 0 && $lon1 == 0) || ($lat2 == 0 && $lon2 == 0)) {

							$flight_array[$row['sequence']]['gc_bearing'] = '-';
							$flight_array[$row['sequence']]['gcd_nm'] = '-';
						} else {
							$bearing = $this->geocalc_fns->getRhumbLineBearing($lat1, $lon1, $lat2, $lon2);
							$flight_array[$row['sequence']]['gc_bearing'] = $bearing;

							//calculate distance
							$gcd_km = $this->geocalc_fns->GCDistance($lat1, $lon1, $lat2, $lon2);
							$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

							$flight_array[$row['sequence']]['gcd_nm'] = $gcd_nm;
						}

					}

					$i++;
				}

				$data['flight_array'] = $flight_array;
				$data['versions'] = $versions;

			} else {
				$data['flight_array'] = array();
				$data['versions'] = array();
			}

			//output page
			$data['page_title'] = 'ACP - Tour Legs';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_tourlegs', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_tours/legs/' . $tour_id . '/' . $selected_version;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function aircraft($tour_id = NULL, $delete = 0, $tour_aircraft_id = NULL) {

		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Dispatch_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($tour_id == NULL || !is_numeric($tour_id)) {
			redirect('acp_tours/manage');
		}

		$data['tour_id'] = $tour_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		$data['allowed_types'] = 'png|gif|jpg|jpeg';
		$data['max_size'] = '75';
		$data['max_width'] = '120';
		$data['max_height'] = '80';

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
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//determine if this is a valid tour and grab all the aircraft
			$query = $this->db->query("	SELECT 	
												tour_index.id,
												tour_index.name,
												tour_index.author,	
												tour_index.length,
												tour_index.difficulty,
												tour_index.description,
												tour_index.class as clss,
												tour_index.detail_info,
												tour_index.requirements,
												tour_index.enabled,
												tour_aircraft.id as tour_aircraft_id,
												aircraft.name as aircraft_name,
												aircraft.clss as aircraft_clss,
												aircraft.in_fleet,
												aircraft.enabled as aircraft_enabled,
												divisions.division_longname as aircraft_division
																										
												FROM tour_index
												
													LEFT JOIN tour_aircraft
													ON tour_aircraft.tour_id = tour_index.id
													
													LEFT JOIN aircraft
													ON aircraft.id = tour_aircraft.aircraft_id
													
													LEFT JOIN divisions
													ON aircraft.division = divisions.id
													
												WHERE tour_index.id = '$tour_id'
												
											");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			//if no return, redirect
			if ($num_results < 1) {
				redirect('acp_tours/manage/');
			} else {

				//we have a return.
				$data['aircraft'] = $result;
				$data['tour_name'] = $result['0']['name'];
				if ($result['0']['enabled'] == '1') {
					$data['tour_enabled'] = 'Enabled';
				} else {
					$data['tour_enabled'] = 'Disabled';
				}

				//grab post data
				$valid = $this->security->sanitize_filename($this->input->post('valid'));
				$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));

				//perform validation
				$this->form_validation->set_rules('valid', 'valid', 'required');
				$this->form_validation->set_rules('aircraft_id', 'aircraft_id', 'required');

				if ($this->form_validation->run() == FALSE) {
					$validation = 0;
				} else {
					$validation = 1;
				}

				//if submitted, insert or delete as required
				if ($valid == 'true' && $validation == 1
					|| $delete == '1' && $tour_aircraft_id != NULL && is_numeric($tour_aircraft_id)
				) {

					if ($delete == '1' && $tour_aircraft_id != NULL && is_numeric($tour_aircraft_id)) {
						//delete this aircraft
						$this->db->where('id', $tour_aircraft_id);
						//$this->db->update('tour_index', $this->db->escape($tour_data));
						$this->db->delete('tour_aircraft');
					} else {

						//array data for insert
						$aircraft_data = array('aircraft_id' => $aircraft_id,
							'tour_id' => $tour_id,
						);

						$this->db->insert('tour_aircraft', $this->db->escape($aircraft_data));

					}

					//redirect to self
					redirect('acp_tours/aircraft/' . $tour_id);
				} else {
					//output list and submit form
					$data['aircraft_id'] = '';

					//grab aircraft dropdown 
					$aircraft_data = $this->Dispatch_model->get_aircraft_array('ALL', '', 0);
					$data['aircraft_array'] = $aircraft_data['aircraft_array_div'];

					//output page
					$data['page_title'] = 'ACP - Tour Aircraft';
					$data['admin_menu'] = 1;
					$this->view_fns->view('global/admincp/acp_touraircraft', $data);

				}

			}

		} //invalid admin login
		elseif ($is_admin == '1') {
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_tours/aircraft/' . $tour_id;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}

	}

	function edit($tour_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($tour_id == NULL) {
			redirect('acp_tours/manage');
		}

		if ($tour_id > 0) {
			$data['mode'] = 'Edit';
		} else {
			$data['mode'] = 'Create';
		}

		$data['tour_id'] = $tour_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		$data['allowed_types'] = 'png|gif|jpg|jpeg';
		$data['max_size'] = '75';
		$data['max_width'] = '120';
		$data['max_height'] = '80';

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
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$name = $this->security->sanitize_filename($this->input->post('name'));
			$author = $this->security->sanitize_filename($this->input->post('author'));
			$length = $this->security->sanitize_filename($this->input->post('length'));
			$difficulty = $this->security->sanitize_filename($this->input->post('difficulty'));
			$description = $this->security->sanitize_filename($this->input->post('description'));
			$clss = $this->security->sanitize_filename($this->input->post('clss'));
			$detail_info = $this->security->sanitize_filename($this->input->post('detail_info'));
			$requirements = $this->security->sanitize_filename($this->input->post('requirements'));
			$enabled = $this->security->sanitize_filename($this->input->post('enabled'));

			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('name', 'name', 'required');
			$this->form_validation->set_rules('clss', 'clss', 'required');

			if ($this->form_validation->run() == FALSE) {
				$validation = 0;
			} else {
				$validation = 1;
			}

			if ($tour_id > 0) {

				//need to determine whether or not this is a valid tour - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												tour_index.id,
												tour_index.name,
												tour_index.author,	
												tour_index.length,
												tour_index.difficulty,
												tour_index.description,
												tour_index.class as clss,
												tour_index.detail_info,
												tour_index.requirements,
												tour_index.enabled
																										
												FROM tour_index
													
												WHERE tour_index.id = '$tour_id'
												
												LIMIT 1
											");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if no return, set create new
				if ($num_results < 1) {
					redirect('acp_tours/manage/');
				}

			}

			if ($valid == 'true' && $validation == 1) {

				//data has been submitted, array it and update the record

				$tour_data = array(
					'name' => $name,
					'author' => $author,
					'length' => $length,
					'difficulty' => $difficulty,
					'description' => $description,
					'class' => $clss,
					'detail_info' => $detail_info,
					'requirements' => $requirements,
					'enabled' => $enabled,
				);

				//if we are editing
				if ($tour_id > 0) {

					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('tour_index', $this->db->escape($tour_data));
				} else {

					$tour_data['submitted'] = $data['gmt_mysql_datetime'];
					$tour_data['submitted_by'] = $current_pilot_user_id;

					//we are creating a new record
					$this->db->insert('tour_index', $this->db->escape($tour_data));

					//grab the record id for the upload
					$query = $this->db->query("	SELECT 	
													tour_index.id
													
													FROM tour_index
														
													WHERE tour_index.name = '$name'
													AND tour_index.submitted = '$data['gmt_mysql_datetime']'
													AND tour_index.submitted_by = '$current_pilot_user_id'
													
													LIMIT 1
												");

					$result = $query->result_array();
					$num_results = $query->num_rows();

					if ($num_results > 0) {
						$tour_id = $result['0']['id'];
					}
				}

				if ($tour_id > 0) {

					// do upload
					$destination_path = $this->config->item('base_path') . 'assets/uploads/tours/';
					$config['upload_path'] = $this->config->item('base_path') . 'assets/uploads/tmp/';
					$config['allowed_types'] = $data['allowed_types'];
					$config['max_size'] = $data['max_size'];
					$config['max_width'] = $data['max_width'];
					$config['max_height'] = $data['max_height'];

					//sort out upload folder
					if (!is_dir($destination_path)) {
						//create path
						if (!mkdir($destination_path, 0755, TRUE)) {
							$data['error'] .= 'Could not create destination folder: ' . $destination_path . '<br />';
						}
					}

					$this->load->library('upload', $config);

					if (!$this->upload->do_upload()) {

						$upload_data = array('upload_data' => $this->upload->data());

						//if we did upload, but error'd
						if ($upload_data['upload_data']['file_size'] > 0) {
							$data['error'] .= $this->upload->display_errors()
								. ' File must be smaller than ' . $config['max_size'] . 'k and no bigger than ' . $config['max_width']
								. 'x' . $config['max_height'] . '. Allowed file types are ' . $config['allowed_types'];
						}
						//else{
						//	$data['error'] .= $this->upload->display_errors();
						//}

					} else {

						$upload_data = array('upload_data' => $this->upload->data());

						//delete any previous images
						foreach (glob($destination_path . $tour_id . '.*') as $filename) {
							unlink($filename);
						}

						if ($upload_data['upload_data']['file_ext'] == '.jpeg') {
							$ext = '.jpg';
						} elseif ($upload_data['upload_data']['file_ext'] == 'jpeg') {
							$ext = 'jpg';
						} else {
							$ext = strtolower($upload_data['upload_data']['file_ext']);
						}

						if (!rename($upload_data['upload_data']['full_path'], $destination_path . $tour_id . $ext)) {

							$data['error'] .= 'Could not move file to final location. The sub folder (' . $destination_path . ') may not be writable<br /><br />';

						}

						if (is_file($upload_data['upload_data']['full_path'])) {
							unlink($upload_data['upload_data']['full_path']);
						}

						$upload_data['upload_data']['file_name'] = $tour_id;

					}
				}

				//if there were no errors
				if ($data['error'] == '') {
					redirect('acp_tours/manage/');
				} else {
					//output error message
					$data['page_title'] = 'Error';
					$this->view_fns->view('global/error/error', $data);
				}

			} // haven't had data submitted or failed validation
			else {

				//initialise all values
				$name = '';
				$author = '';
				$length = '';
				$cargo = '';
				$difficulty = '';
				$description = '';
				$clss = '';
				$detail_info = '';
				$requirements = '';
				$enabled = '';

				//if we are editing
				if ($tour_id > 0) {

					//prepare dropdowns etc for output from database
					$name = $result['0']['name'];
					$author = $result['0']['author'];
					$length = $result['0']['length'];
					$difficulty = $result['0']['difficulty'];
					$description = $result['0']['description'];
					$clss = $result['0']['clss'];
					$detail_info = $result['0']['detail_info'];
					$requirements = $result['0']['requirements'];
					$enabled = $result['0']['enabled'];

				}

				//dropdowns
				$data['clss'] = $clss;
				$data['enabled'] = $enabled;

				//text area
				$data['description'] = array('name' => 'description', 'id' => 'description', 'value' => $description, 'rows' => '10', 'cols' => '45');
				$data['length'] = array('name' => 'length', 'id' => 'length', 'value' => $length, 'rows' => '5', 'cols' => '45');
				$data['difficulty'] = array('name' => 'difficulty', 'id' => 'difficulty', 'value' => $difficulty, 'rows' => '5', 'cols' => '45');
				$data['detail_info'] = array('name' => 'detail_info', 'id' => 'detail_info', 'value' => $detail_info, 'rows' => '10', 'cols' => '45');
				$data['requirements'] = array('name' => 'requirements', 'id' => 'requirements', 'value' => $requirements, 'rows' => '10', 'cols' => '45');

				//define form elements
				$data['name'] = array('name' => 'name', 'id' => 'name', 'value' => $name, 'maxlength' => '40', 'size' => '40');
				$data['author'] = array('name' => 'author', 'id' => 'author', 'value' => $author, 'maxlength' => '80', 'size' => '40');

				//define all the arrays			
				$data['clss_array'] = array();

				$ranks_data = $this->Fleet_model->get_ranks();

				//$data['rank_array'] = $ranks_data['ranks'];
				$data['clss_array'] = $ranks_data['clss'];

				$data['enabled_array'] = array('0' => 'Disabled', '1' => 'Enabled');

				//output page
				$data['page_title'] = 'ACP - Tour Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_touredit', $data);
			}

		} //invalid admin login
		elseif ($is_admin == '1') {
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_tours/edit/' . $tour_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function manage($system_restrict = NULL, $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');

		if ($system_restrict == NULL) {
			redirect('acp_tours/manage/ALL/');
		}

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		$data['system_restrict'] = $system_restrict;

		//grab post
		$post_system_restrict = $this->security->sanitize_filename($this->input->post('system_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));

		if (
		($system_restrict != $post_system_restrict && $post_system_restrict != '')
		) {
			redirect('acp_tours/manage/' . $post_system_restrict . '/');
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
				'admincp_time' => $data['gmt_mysql_datetime'],
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
					$sqlsearch = "WHERE (tour_index.name LIKE '%" . $search . "%'";
					foreach ($search_array as &$row) {
						$sqlsearch .= " OR tour_index.name LIKE '%" . $row . "%'";
					}
					$sqlsearch .= ')';
				} //for single term searches
				else {
					$sqlsearch = "WHERE (tour_index.name LIKE '%$search%')";
				}

			} else {

				//not searching, handle restriction
				$sqlsearch = '';

				if ($system_restrict != 'ALL') {
					$sqlsearch = "WHERE tour_index.class = '$system_restrict'";
				}

			}

			//grab all tours from the database
			$query = $this->db->query("	SELECT 	
											tour_index.id,
											tour_index.name,
											tour_index.author,
											tour_index.length,
											tour_index.difficulty,
											tour_index.description,
											tour_index.class as clss,
											tour_index.detail_info,
											tour_index.requirements
													
											FROM tour_index
												
											$sqlsearch
											
											ORDER BY tour_index.name
										");

			$data['result'] = $query->result();
			$data['num_rows'] = $query->num_rows();

			//grab divisions for dropdown restrict

			//class array
			$query = $this->db->query("	SELECT 	
											ranks.id,
											ranks.class AS clss
													
											FROM ranks
											
											ORDER BY ranks.id
										");

			$result = $query->result();

			$data['system_array'] = array('ALL' => 'All');

			foreach ($result as $row) {
				$data['system_array'][$row->clss] = 'Class ' . $row->clss;
			}

			//$data['enabled_array'] = $enabled_array;

			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search', 'maxlength' => '25', 'size' => '25', 'value' => $search);

			//paginatipon
			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '15';

			$pag_config['base_url'] = $data['base_url'] . 'acp_tours/manage/' . $system_restrict . '/';
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 5;

			$this->pagination->initialize($pag_config);

			//output page
			$data['page_title'] = 'ACP - Tour Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_tourmanage', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_tours/manage/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

}

?>