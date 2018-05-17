<?php

class Acp_timetables extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function delete($timetable_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($timetable_id == NULL) {
			redirect('acp_timetables/manage');
		}

		$data['timetable_id'] = $timetable_id;

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

			$current_pilot_username = $this->session->userdata['username'];

			//need to determine whether or not this is a valid delete - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											timetable.id,
											timetable.flightnumber,
											timetable.dep_airport as dep_icao,	
											timetable.arr_airport as arr_icao,
											timetable.dep_time,
											timetable.arr_time,
											dep_airport.name as dep_name,
											arr_airport.name as arr_name
													
											FROM timetable
											
												LEFT JOIN airports as dep_airport 
												ON dep_airport.icao = timetable.dep_airport
												
												LEFT JOIN airports as arr_airport 
												ON arr_airport.icao = timetable.arr_airport
												
											WHERE timetable.id = '$timetable_id'
											
											LIMIT 1
										");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($valid == 'true') {

				//if we actually got a hit back, then we're valid
				if ($num_results > 0) {

					//use the db returned value as an extra check
					$id_val = $result['0']['id'];

					//perform the delete from timetable
					$this->db->where('id', $id_val);
					$this->db->delete('timetable');

					//update last updated value for flogger
					$timetable_data = array(
						'code_description' => $data['gmt_mysql_datetime'],
					);
					$this->db->where('type', 'timetable');
					$this->db->where('code_id', 'last_update');
					$this->db->update('config_codesets', $this->db->escape($timetable_data));

				}

				//now redirect back to index
				redirect('acp_timetables/manage/');

			} else {
				//if there is such a result
				if ($num_results > 0) {
					$data['id'] = $result['0']['id'];
					$data['flightnumber'] = $result['0']['flightnumber'];
					$data['dep_icao'] = $result['0']['dep_icao'];
					$data['arr_icao'] = $result['0']['arr_icao'];
					$data['dep_time'] = $result['0']['dep_time'];
					$data['arr_time'] = $result['0']['arr_time'];
					$data['dep_name'] = $result['0']['dep_name'];
					$data['arr_name'] = $result['0']['arr_name'];

					//output confirmation page
					$data['page_title'] = 'Delete confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/admincp/acp_timetabledelete', $data);
				} else {
					redirect('admincp/pirep_validate');
				}

			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_timetables/delete/' . $timetable_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function edit($timetable_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('Admin_fns');
		//$this->load->model('Fleet_model');
		$this->load->model('Dispatch_model');
		$this->load->model('Pirep_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($timetable_id == NULL) {
			redirect('acp_timetables/manage');
		}

		if ($timetable_id > 0) {
			$data['mode'] = 'Edit';
		} else {
			$data['mode'] = 'Create';
		}

		$data['timetable_id'] = $timetable_id;
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
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$flightnumber = $this->security->sanitize_filename($this->input->post('flightnumber'));
			$dep_airport = $this->security->sanitize_filename($this->input->post('dep_airport'));
			$arr_airport = $this->security->sanitize_filename($this->input->post('arr_airport'));
			$dep_time = $this->security->sanitize_filename($this->input->post('dep_time'));
			$arr_time = $this->security->sanitize_filename($this->input->post('arr_time'));
			$sun = $this->security->sanitize_filename($this->input->post('sun'));
			$mon = $this->security->sanitize_filename($this->input->post('mon'));
			$tue = $this->security->sanitize_filename($this->input->post('tue'));
			$wed = $this->security->sanitize_filename($this->input->post('wed'));
			$thu = $this->security->sanitize_filename($this->input->post('thu'));
			$fri = $this->security->sanitize_filename($this->input->post('fri'));
			$sat = $this->security->sanitize_filename($this->input->post('sat'));
			$season_month_start = $this->security->sanitize_filename($this->input->post('season_month_start'));
			$season_month_end = $this->security->sanitize_filename($this->input->post('season_month_end'));

			$hub = $this->security->sanitize_filename($this->input->post('hub'));

			$clss = $this->security->sanitize_filename($this->input->post('clss'));
			$division = $this->security->sanitize_filename($this->input->post('division'));
			$active = $this->security->sanitize_filename($this->input->post('active'));
			$return = $this->security->sanitize_filename($this->input->post('return'));

			//set the checkbox values
			if ($sun == TRUE) {
				$sun = 1;
			} else {
				$sun = 0;
			}
			if ($mon == TRUE) {
				$mon = 1;
			} else {
				$mon = 0;
			}
			if ($tue == TRUE) {
				$tue = 1;
			} else {
				$tue = 0;
			}
			if ($wed == TRUE) {
				$wed = 1;
			} else {
				$wed = 0;
			}
			if ($thu == TRUE) {
				$thu = 1;
			} else {
				$thu = 0;
			}
			if ($fri == TRUE) {
				$fri = 1;
			} else {
				$fri = 0;
			}
			if ($sat == TRUE) {
				$sat = 1;
			} else {
				$sat = 0;
			}
			if ($return == TRUE) {
				$return = 1;
			} else {
				$return = 0;
			}

			if ($season_month_start == '') {
				$season_month_start = NULL;
			}
			if ($season_month_end == '') {
				$season_month_end = NULL;
			}

			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('dep_airport', 'dep_airport', 'required');
			$this->form_validation->set_rules('arr_airport', 'arr_airport', 'required');
			$this->form_validation->set_rules('clss', 'clss', 'required');
			$this->form_validation->set_rules('division', 'division', 'required');
			$this->form_validation->set_rules('active', 'active', 'required');
			$this->form_validation->set_rules('hub', 'hub', 'required');

			if ($this->form_validation->run() == FALSE) {
				$validation = 0;
				$data['error'] = 'Validation failed. Check fields and try again.';
			} else {
				$validation = 1;
			}

			//additional validation runs
			//departure and arrival times
			if (substr($dep_time, 2, 1) != ':'
				|| substr($arr_time, 2, 1) != ':'

				|| !is_numeric(substr($dep_time, 0, 2))
				|| !is_numeric(substr($arr_time, 0, 2))

				|| !is_numeric(substr($dep_time, 3, 2))
				|| !is_numeric(substr($arr_time, 3, 2))
			) {
				$validation = 0;
				$data['error'] = 'Validation failed. Check fields and try again.';
			}

			if ($timetable_id > 0) {

				//need to determine whether or not this is a valid award - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												timetable.id,
												timetable.flightnumber,
												timetable.hub,
												timetable.dep_airport,	
												timetable.arr_airport,
												timetable.dep_time,
												timetable.arr_time,
												timetable.sun,
												timetable.mon,
												timetable.tue,
												timetable.wed,
												timetable.thu,
												timetable.fri,
												timetable.sat,
												timetable.season_month_start,
												timetable.season_month_end,
												timetable.class as clss,
												timetable.division,
												timetable.active
												
												FROM timetable
													
												WHERE timetable.id = '$timetable_id'
												
												LIMIT 1
											");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if no return, set create new
				if ($num_results < 1) {
					redirect('admincp/awards_manage/');
				}

			}

			if ($valid == 'true' && $validation == 1) {

				//data has been submitted, array it and update the record			

				$timetable_data = array(
					'dep_airport' => $dep_airport,
					'arr_airport' => $arr_airport,
					'dep_time' => $dep_time . ':00',
					'arr_time' => $arr_time . ':00',
					'hub' => $hub,
					'sun' => $sun,
					'mon' => $mon,
					'tue' => $tue,
					'wed' => $wed,
					'thu' => $thu,
					'fri' => $fri,
					'sat' => $sat,
					'season_month_start' => $season_month_start,
					'season_month_end' => $season_month_end,
					'class' => $clss,
					'division' => $division,
					'active' => $active,
				);

				//if we are editing
				if ($timetable_id > 0) {

					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('timetable', $this->db->escape($timetable_data));

					//update last updated value for flogger
					$cc_timetable_data = array(
						'code_description' => $data['gmt_mysql_datetime'],
					);
					$this->db->where('type', 'timetable');
					$this->db->where('code_id', 'last_update');
					$this->db->update('config_codesets', $this->db->escape($cc_timetable_data));
				} else {

					//deal with flight number
					$timetable_data['flightnumber'] = $this->admin_fns->generate_flightnumber();

					if ($timetable_data['flightnumber'] != FALSE) {
						//we are creating a new record
						$this->db->insert('timetable', $this->db->escape($timetable_data));

						//update last updated value for flogger
						$cc_timetable_data = array(
							'code_description' => $data['gmt_mysql_datetime'],
						);
						$this->db->where('type', 'timetable');
						$this->db->where('code_id', 'last_update');
						$this->db->update('config_codesets', $this->db->escape($cc_timetable_data));

						//if the return leg is ticked, now create the return leg
						if ($return == '1') {

							//deal with flight number
							$timetable_data['flightnumber'] = $this->admin_fns->generate_flightnumber();

							if ($timetable_data['flightnumber'] != FALSE) {

								$timetable_data['dep_airport'] = $arr_airport;
								$timetable_data['arr_airport'] = $dep_airport;

								//update times, set return 1 hour later
								$return_depart = date("H:i:s", strtotime($arr_time . ':00') + (60 * 60));

								//difference
								$duration_sec = strtotime($arr_time) - strtotime($dep_time);
								$return_arrive_sec = strtotime($return_depart) + ($duration_sec);
								$return_arrive = date("H:i:s", $return_arrive_sec);

								$timetable_data['dep_time'] = $return_depart;
								$timetable_data['arr_time'] = $return_arrive;

								//we are creating a return record
								$this->db->insert('timetable', $this->db->escape($timetable_data));

								//update last updated value for flogger
								$timetable_data = array(
									'code_description' => $data['gmt_mysql_datetime'],
								);
								$this->db->where('type', 'timetable');
								$this->db->where('code_id', 'last_update');
								$this->db->update('config_codesets', $this->db->escape($timetable_data));

							} else {
								$error = 'Failed to generate return flight number';
							}
						}
					} else {
						$error = 'Failed to generate flight number';
					}
				}

				//if there were no errors
				if ($data['error'] == '') {
					redirect('acp_timetables/manage/');
				} else {
					//output error message
					$data['page_title'] = 'Error';
					$this->view_fns->view('global/error/error', $data);
				}

			} // haven't had data submitted or failed validation
			else {

				//initialise all values
				$flightnumber = '';
				$dep_airport = '';
				$arr_airport = '';
				$dep_time = '';
				$arr_time = '';
				$sun = '1';
				$mon = '1';
				$tue = '1';
				$wed = '1';
				$thu = '1';
				$fri = '1';
				$sat = '1';
				$season_month_start = '';
				$season_month_end = '';
				$clss = '';
				$division = '';
				$active = '';
				$hub = '';

				//if we are editing
				if ($timetable_id > 0) {

					//prepare dropdowns etc for output from database

					$flightnumber = $result['0']['flightnumber'];
					$hub = $result['0']['hub'];
					$dep_airport = $result['0']['dep_airport'];
					$arr_airport = $result['0']['arr_airport'];
					$dep_time = substr($result['0']['dep_time'], 0, 5);
					$arr_time = substr($result['0']['arr_time'], 0, 5);

					$sun = $result['0']['sun'];
					$mon = $result['0']['mon'];
					$tue = $result['0']['tue'];
					$wed = $result['0']['wed'];
					$thu = $result['0']['thu'];
					$fri = $result['0']['fri'];
					$sat = $result['0']['sat'];

					$season_month_start = $result['0']['season_month_start'];
					$season_month_end = $result['0']['season_month_end'];

					$clss = $result['0']['clss'];
					$division = $result['0']['division'];
					$active = $result['0']['active'];
				}

				//dropdowns
				//$data['dep_airport'] = $dep_airport;
				//$data['arr_airport'] = $arr_airport;
				$data['active'] = $active;
				$data['clss'] = $clss;
				$data['division'] = $division;
				$data['flightnumber'] = $flightnumber;
				$data['hub'] = $hub;
				$data['season_month_start'] = $season_month_start;
				$data['season_month_end'] = $season_month_end;

				//checkboxes
				$data['return'] = array('name' => 'return', 'id' => 'return', 'value' => 'accept', 'checked' => '1', 'style' => 'margin:10px');
				$data['sun'] = array('name' => 'sun', 'id' => 'sun', 'value' => 'accept', 'checked' => $sun, 'style' => 'margin:10px');
				$data['mon'] = array('name' => 'mon', 'id' => 'mon', 'value' => 'accept', 'checked' => $mon, 'style' => 'margin:10px');
				$data['tue'] = array('name' => 'tue', 'id' => 'tue', 'value' => 'accept', 'checked' => $tue, 'style' => 'margin:10px');
				$data['wed'] = array('name' => 'wed', 'id' => 'wed', 'value' => 'accept', 'checked' => $wed, 'style' => 'margin:10px');
				$data['thu'] = array('name' => 'thu', 'id' => 'thu', 'value' => 'accept', 'checked' => $thu, 'style' => 'margin:10px');
				$data['fri'] = array('name' => 'fri', 'id' => 'fri', 'value' => 'accept', 'checked' => $fri, 'style' => 'margin:10px');
				$data['sat'] = array('name' => 'sat', 'id' => 'sat', 'value' => 'accept', 'checked' => $sat, 'style' => 'margin:10px');

				//text area
				//$data['obs'] = array( 'name' => 'obs','id' => 'obs','value' => $obs, 'rows' => '10','cols' => '45');

				//define form elements
				//$data['flightnumber'] = array( 'name' => 'flightnumber','id' => 'flightnumber','value' => $flightnumber, 'maxlength' => '5','size' => '5');
				$data['dep_airport'] = array('name' => 'dep_airport', 'id' => 'dep_airport', 'value' => $dep_airport, 'maxlength' => '4', 'size' => '4');
				$data['arr_airport'] = array('name' => 'arr_airport', 'id' => 'arr_airport', 'value' => $arr_airport, 'maxlength' => '4', 'size' => '4');

				$data['dep_time'] = array('name' => 'dep_time', 'id' => 'dep_time', 'value' => $dep_time, 'maxlength' => '5', 'size' => '5');
				$data['arr_time'] = array('name' => 'arr_time', 'id' => 'arr_time', 'value' => $arr_time, 'maxlength' => '5', 'size' => '5');

				//define all the arrays			
				$data['bool_array'] = array('' => '', '0' => 'No', '1' => 'Yes');
				$data['divisions_array'] = $this->Dispatch_model->get_division_array();
				//$data['airfield_array'] = $this->Dispatch_model->get_airfield_array();

				//create class array
				$limit = 7;
				if ($limit < 1) {
					$limit = 1;
				}
				$i = 1;
				$data['clss_array'] = array();
				while ($i <= $limit) {
					$data['clss_array'][$i] = 'Class ' . $i;
					$i++;
				}

				//create season month array
				$data['season_month_array'] = array('' => '-');
				$data['season_month_array'][1] = 'Jan';
				$data['season_month_array'][2] = 'Feb';
				$data['season_month_array'][3] = 'Mar';
				$data['season_month_array'][4] = 'Apr';
				$data['season_month_array'][5] = 'May';
				$data['season_month_array'][6] = 'Jun';
				$data['season_month_array'][7] = 'Jul';
				$data['season_month_array'][8] = 'Aug';
				$data['season_month_array'][9] = 'Sep';
				$data['season_month_array'][10] = 'Oct';
				$data['season_month_array'][11] = 'Nov';
				$data['season_month_array'][12] = 'Dec';

				//get list of hubs from db
				$data['hub_array'] = $this->Pirep_model->get_hubs(1, 0, 1);

				//handle errors
				if ($data['error'] != '' && $valid == 'true') {
					$data['highlight1'] = '<center><span style="error">';
					$data['highlight2'] = '</span></center>';
				} else {
					$data['error'] = '';
				}

				//output page
				$data['page_title'] = 'ACP - Timetable Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_timetableedit', $data);
			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_timetables/edit/' . $timetable_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function manage($system_restrict = NULL, $division = NULL, $hub_restrict = NULL, $searchurl = NULL, $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		$this->load->model('Pirep_model');

		//$this->output->enable_profiler(TRUE);

		if ($system_restrict == NULL && $division == NULL && $hub_restrict == NULL) {
			redirect('acp_timetables/manage/ALL/ALL/ALL/');
		} elseif ($system_restrict == NULL && $hub_restrict == NULL) {
			redirect('acp_timetables/manage/ALL/' . $division . '/ALL/');
		} elseif ($system_restrict == NULL && $division == NULL) {
			redirect('acp_timetables/manage/ALL/ALL' . $hub_restrict . '/');
		} elseif ($division == NULL && $hub_restrict == NULL) {
			redirect('acp_timetables/manage/' . $system_restrict . '/ALL/ALL/');
		} elseif ($division == NULL) {
			redirect('acp_timetables/manage/' . $system_restrict . '/ALL/' . $hub_restrict);
		} elseif ($system_restrict == NULL) {
			redirect('acp_timetables/manage/ALL/' . $division . '/' . $hub_restrict);
		} elseif ($hub_restrict == NULL) {
			redirect('acp_timetables/manage/' . $system_restrict . '/' . $division . '/ALL/');
		}

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		$data['system_restrict'] = $system_restrict;
		$data['division'] = $division;
		$data['hub_restrict'] = $hub_restrict;

		//grab post
		$post_system_restrict = $this->security->sanitize_filename($this->input->post('system_restrict'));
		$post_division = $this->security->sanitize_filename($this->input->post('division'));
		$post_hub_restrict = $this->security->sanitize_filename($this->input->post('hub_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));

		if ($searchurl != NULL && $search == '' && $searchurl != '%20') {
			$search = $searchurl;
		}

		if (
			($system_restrict != $post_system_restrict && $post_system_restrict != '')
			OR ($division != $post_division && $post_division != '')
			OR ($hub_restrict != $post_hub_restrict && $post_hub_restrict != '')
		) {
			redirect('acp_timetables/manage/' . $post_system_restrict . '/' . $post_division . '/' . $post_hub_restrict);
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
			if ($search != '') {

				//split up the search into constituent terms
				$search_array = explode(" ", $search);
				$num_search = count($search_array);

				//for multiple term searches
				if ($num_search > 1) {
					$sqlsearch = "WHERE (timetable.flightnumber LIKE '%" . $search . "%'";
					$sqlsearch .= " OR timetable.dep_airport LIKE '%" . $search . "%'";
					$sqlsearch .= " OR timetable.arr_airport LIKE '%" . $search . "%'";
					foreach ($search_array as $row) {
						$sqlsearch .= " OR timetable.flightnumber LIKE '%" . $row . "%'";
						$sqlsearch .= " OR timetable.dep_airport LIKE '%" . $row . "%'";
						$sqlsearch .= " OR timetable.arr_airport LIKE '%" . $row . "%'";
					}
					$sqlsearch .= ')';
				} //for single term searches
				else {
					$sqlsearch = "WHERE (timetable.flightnumber LIKE '%$search%')";
					$sqlsearch .= " OR timetable.dep_airport LIKE '%" . $search . "%'";
					$sqlsearch .= " OR timetable.arr_airport LIKE '%" . $search . "%'";
				}

			} else {

				//not searching, handle restriction
				//$sqlsearch = '';

				switch ($system_restrict) {

					case 'A':
						$sqlsearch = "WHERE timetable.active = '1'";
						break;

					case 'I':
						$sqlsearch = "WHERE timetable.active = '0'";
						break;

					default:
						$sqlsearch = "WHERE (timetable.active = '0' OR timetable.active = '1')";
						break;

				}
			}

			if (is_numeric($division)) {

				$sqlsearch .= "AND timetable.division = '$division'";

			}

			if ($hub_restrict != '' && $hub_restrict != 'ALL') {

				$sqlsearch .= "AND timetable.hub = '$hub_restrict'";

			}

			//grab all timetables from the database
			$query = $this->db->query("	SELECT 	
											timetable.id,
											timetable.flightnumber,
											timetable.dep_airport,
											timetable.arr_airport,
											timetable.dep_time,
											timetable.arr_time,
											timetable.season_month_start,
											timetable.season_month_end,
											timetable.class as clss,
											timetable.division,
											timetable.active,
											divisions.division_shortname,
											hub.hub_name as hub,
											dep_dat.lat as dep_lat,
											dep_dat.long as dep_lon,
											arr_dat.lat as arr_lat,
											arr_dat.long as arr_lon
													
											FROM timetable
											
												LEFT JOIN divisions
												ON divisions.id = timetable.division
												
												LEFT JOIN hub
												ON hub.id = timetable.hub
												
												LEFT JOIN airports_data as dep_dat
												ON dep_dat.icao = timetable.dep_airport
												
												LEFT JOIN airports_data as arr_dat
												ON arr_dat.icao = timetable.arr_airport
												
											$sqlsearch
											
											ORDER BY timetable.flightnumber
										");

			$data['result'] = $query->result();
			$data['num_rows'] = $query->num_rows();

			//grab divisions for dropdown restrict

			$system_array = array('ALL' => 'All', 'A' => 'Active', 'I' => 'Inactive');

			//divisions array
			$query = $this->db->query("	SELECT 	
											divisions.id,
											divisions.division_shortname AS name
													
											FROM divisions
											
											ORDER BY divisions.id
										");

			$result = $query->result();

			$data['division_array'] = array('ALL' => 'All');

			foreach ($result as $row) {
				$data['division_array'][$row->id] = $row->name;
			}

			$data['system_array'] = $system_array;

			//get list of hubs from db
			$data['hub_array'] = $this->Pirep_model->get_hubs('ALL', '1');

			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search', 'maxlength' => '25', 'size' => '25', 'value' => $search);

			//paginatipon
			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			if ($searchurl == '' && $search != '') {
				$searchurl = $search;
			} elseif ($search == '') {
				$searchurl = ' ';
			}

			$data['season_month_array'] = array('' => '-');
			$data['season_month_array'][1] = 'Jan';
			$data['season_month_array'][2] = 'Feb';
			$data['season_month_array'][3] = 'Mar';
			$data['season_month_array'][4] = 'Apr';
			$data['season_month_array'][5] = 'May';
			$data['season_month_array'][6] = 'Jun';
			$data['season_month_array'][7] = 'Jul';
			$data['season_month_array'][8] = 'Aug';
			$data['season_month_array'][9] = 'Sep';
			$data['season_month_array'][10] = 'Oct';
			$data['season_month_array'][11] = 'Nov';
			$data['season_month_array'][12] = 'Dec';

			$data['offset'] = $offset;
			$data['limit'] = '15';

			$pag_config['base_url'] = $data['base_url'] . 'acp_timetables/manage/' . $system_restrict . '/' . $division . '/' . $hub_restrict . '/' . $searchurl . '/';
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 7;

			$this->pagination->initialize($pag_config);

			//output page
			$data['page_title'] = 'ACP - Timetable Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_timetablemanage', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_timetables/manage/' . $system_restrict . '/' . $division . '/' . $searchurl . '/' . $offset . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

}

?>