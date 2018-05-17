<?php

class Acp_missions extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function edit($mission_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('Admin_fns');
		//$this->load->model('Fleet_model');
		$this->load->model('Dispatch_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($mission_id == NULL) {
			redirect('admincp/mission_index_manage');
		}

		if ($mission_id > 0) {
			$data['mode'] = 'Edit';
		} else {
			$data['mode'] = 'Create';
		}

		$data['mission_id'] = $mission_id;
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

			//create aircraft array
			$aircraft_data = $this->Dispatch_model->get_aircraft_array('2,3,4,8,9', '', 0);

			$data['aircraft_array'] = $aircraft_data['aircraft_array_div'];

			ksort($data['aircraft_array']);

			$aircraft_array_div_id = $aircraft_data['aircraft_array_div_id'];

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$title = $this->security->sanitize_filename($this->input->post('title'));
			$description = htmlspecialchars($this->input->post('description'));
			$start_icao = $this->security->sanitize_filename($this->input->post('start_icao'));
			$end_icao = $this->security->sanitize_filename($this->input->post('end_icao'));
			$dep_time = $this->security->sanitize_filename($this->input->post('dep_time'));
			$arr_time = $this->security->sanitize_filename($this->input->post('arr_time'));
			$division = $this->security->sanitize_filename($this->input->post('division'));
			$clss = $this->security->sanitize_filename($this->input->post('clss'));
			$start_day = $this->security->sanitize_filename($this->input->post('start_day'));
			$start_month = $this->security->sanitize_filename($this->input->post('start_month'));
			$start_year = $this->security->sanitize_filename($this->input->post('start_year'));
			$end_day = $this->security->sanitize_filename($this->input->post('end_day'));
			$end_month = $this->security->sanitize_filename($this->input->post('end_month'));
			$end_year = $this->security->sanitize_filename($this->input->post('end_year'));
			$dep_weather = htmlspecialchars($this->input->post('dep_weather'));
			$arr_weather = htmlspecialchars($this->input->post('arr_weather'));
			$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));

			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('title', 'title', 'required');
			$this->form_validation->set_rules('description', 'description', 'required');
			$this->form_validation->set_rules('start_icao', 'start_icao', 'required');
			$this->form_validation->set_rules('end_icao', 'end_icao', 'required');
			$this->form_validation->set_rules('division', 'division', 'required');
			$this->form_validation->set_rules('clss', 'clss', 'required');
			$this->form_validation->set_rules('start_day', 'start_day', 'required');
			$this->form_validation->set_rules('start_month', 'start_month', 'required');
			$this->form_validation->set_rules('start_year', 'start_year', 'required');
			$this->form_validation->set_rules('end_day', 'end_day', 'required');
			$this->form_validation->set_rules('end_month', 'end_month', 'required');
			$this->form_validation->set_rules('end_year', 'end_year', 'required');
			$this->form_validation->set_rules('aircraft_id', 'aircraft_id', 'required');

			if ($this->form_validation->run() == FALSE) {
				$validation = 0;
				$data['error'] = 'Validation failed. Check fields and try again.';
			} else {
				$validation = 1;
			}

			//additional validation runs

			//aircraft not matching division selected
			if (!array_key_exists($division, $aircraft_array_div_id)
				|| !array_key_exists($aircraft_id, $aircraft_array_div_id[$division])
			) {
				$validation = 0;
				$data['error'] = 'Validation failed. The selected aircraft is not in the selected division.';
			}

			//departure and arrival times
			if (
				(
					$dep_time != '' && (
						substr($dep_time, 2, 1) != ':'
						|| !is_numeric(substr($dep_time, 0, 2))
						|| !is_numeric(substr($dep_time, 3, 2))
					))
				||
				(
					$arr_time != '' && (
						substr($arr_time, 2, 1) != ':'
						|| !is_numeric(substr($arr_time, 0, 2))
						|| !is_numeric(substr($arr_time, 3, 2))
					))
			) {
				$validation = 0;
				$data['error'] = 'Validation failed. Check fields and try again.';
			}

			if ($mission_id > 0) {

				//need to determine whether or not this is a valid mission - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												mission_index.id,
												mission_index.title,
												mission_index.description,	
												mission_index.start_icao,
												mission_index.end_icao,
												mission_index.dep_time,
												mission_index.arr_time,
												mission_index.division,
												mission_index.class as clss,
												mission_index.start_date,
												mission_index.end_date,
												mission_index.dep_weather,
												mission_index.arr_weather,
												mission_index.aircraft_id
												
												FROM mission_index
													
												WHERE mission_index.id = '$mission_id'
												
												LIMIT 1
											");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if no return, set create new
				if ($num_results < 1) {
					redirect('acp_missions/manage/');
				}

			}

			if ($valid == 'true' && $validation == 1) {

				//data has been submitted, array it and update the record	
				$start_date = $start_year . '-' . $start_month . '-' . $start_day;
				$end_date = $end_year . '-' . $end_month . '-' . $end_day;

				if ($dep_time == '' || $dep_time == '00:00:00') {
					$dep_time = NULL;
				} else {
					$dep_time = $dep_time . ':00';
				}
				if ($arr_time == '' || $arr_time == '00:00:00') {
					$arr_time = NULL;
				} else {
					$arr_time = $arr_time . ':00';
				}
				if ($dep_weather == '') {
					$dep_weather = NULL;
				}
				if ($arr_weather == '') {
					$arr_weather = NULL;
				}

				$mission_index_data = array(
					'title' => $title,
					'description' => $description,
					'start_icao' => $start_icao,
					'end_icao' => $end_icao,
					'dep_time' => $dep_time,
					'arr_time' => $arr_time,
					'division' => $division,
					'class' => $clss,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'dep_weather' => $dep_weather,
					'arr_weather' => $arr_weather,
					'aircraft_id' => $aircraft_id,
				);

				//if we are editing
				if ($mission_id > 0) {

					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('mission_index', $this->db->escape($mission_index_data));
				} else {

					//we are creating a new record
					$this->db->insert('mission_index', $this->db->escape($mission_index_data));

				}

				//if there were no errors
				if ($data['error'] == '') {
					redirect('acp_missions/manage/');
				} else {
					//output error message
					$data['page_title'] = 'Error';
					$this->view_fns->view('global/error/error', $data);
				}

			} // haven't had data submitted or failed validation
			else {

				if ($valid != 'true') {
					//initialise all values
					$title = '';
					$description = '';
					$start_icao = '';
					$end_icao = '';
					$aircraft_id = '';
					$dep_time = '';
					$arr_time = '';
					$division = '';
					$clss = '';
					$start_day = gmdate('d', time('now'));
					$start_month = gmdate('m', time('now'));
					$start_year = gmdate('Y', time('now'));
					$end_day = gmdate('d', strtotime('+3 months'));
					$end_month = gmdate('m', strtotime('+3 months'));
					$end_year = gmdate('Y', strtotime('+3 months'));
					$dep_weather = '';
					$arr_weather = '';
				}

				//if we are editing
				if ($mission_id > 0) {

					//prepare dropdowns etc for output from database

					$title = $result['0']['title'];
					$description = htmlspecialchars_decode($result['0']['description']);
					$start_icao = $result['0']['start_icao'];
					$end_icao = $result['0']['end_icao'];

					$aircraft_id = $result['0']['aircraft_id'];

					$dep_time = substr($result['0']['dep_time'], 0, 5);
					$arr_time = substr($result['0']['arr_time'], 0, 5);

					$division = $result['0']['division'];
					$clss = $result['0']['clss'];
					$start_date = $result['0']['start_date'];
					$end_date = $result['0']['end_date'];
					$dep_weather = htmlspecialchars_decode($result['0']['dep_weather']);
					$arr_weather = htmlspecialchars_decode($result['0']['arr_weather']);

					$start_day = substr($result['0']['start_date'], 8, 2);
					$start_month = substr($result['0']['start_date'], 5, 2);
					$start_year = substr($result['0']['start_date'], 0, 4);
					$end_day = substr($result['0']['end_date'], 8, 2);
					$end_month = substr($result['0']['end_date'], 5, 2);
					$end_year = substr($result['0']['end_date'], 0, 4);
				}

				//dropdowns
				$data['start_icao'] = $start_icao;
				$data['end_icao'] = $end_icao;
				$data['clss'] = $clss;
				$data['division'] = $division;
				$data['start_day'] = $start_day;
				$data['start_month'] = $start_month;
				$data['start_year'] = $start_year;
				$data['end_day'] = $end_day;
				$data['end_month'] = $end_month;
				$data['end_year'] = $end_year;
				$data['aircraft_id'] = $aircraft_id;

				//text area
				$data['description'] = array('name' => 'description', 'id' => 'description', 'value' => $description, 'rows' => '10', 'cols' => '45');
				$data['dep_weather'] = array('name' => 'dep_weather', 'id' => 'dep_weather', 'value' => $dep_weather, 'rows' => '10', 'cols' => '45');
				$data['arr_weather'] = array('name' => 'arr_weather', 'id' => 'arr_weather', 'value' => $arr_weather, 'rows' => '10', 'cols' => '45');

				//define form elements
				$data['title'] = array('name' => 'title', 'id' => 'title', 'value' => $title, 'maxlength' => '255', 'size' => '40');
				$data['dep_time'] = array('name' => 'dep_time', 'id' => 'dep_time', 'value' => $dep_time, 'maxlength' => '5', 'size' => '5');
				$data['arr_time'] = array('name' => 'arr_time', 'id' => 'arr_time', 'value' => $arr_time, 'maxlength' => '5', 'size' => '5');

				//define all the arrays			
				$data['divisions_array'] = $this->Dispatch_model->get_division_array();
				$data['airfield_array'] = $this->Dispatch_model->get_airfield_array();

				//day_array
				$i = 1;
				$data['day_array']['00'] = '';
				while ($i <= 31) {
					$data['day_array'][$i] = $i;
					$i++;
				}

				//month_array
				$i = 1;
				$data['month_array']['00'] = '';
				while ($i <= 12) {
					$data['month_array'][$i] = $i;
					$i++;
				}

				//year_array
				$current_year = date('Y', time());
				$data['year_array']['0000'] = '';
				$i = 2004;
				while ($i <= ($current_year + 3)) {
					$data['year_array'][$i] = $i;
					$i++;
				}

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

				//handle errors
				if ($data['error'] != '' && $valid == 'true') {
					$data['highlight1'] = '<center><span style="error">';
					$data['highlight2'] = '</span></center>';
				} else {
					$data['error'] = '';
				}

				//output page
				$data['page_title'] = 'ACP - Mission Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_missionsedit', $data);
			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_missions/edit/' . $mission_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function manage($division_restrict = NULL, $class_restrict = NULL, $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		$this->load->model('Dispatch_model');

		if ($division_restrict == NULL && $class_restrict == NULL) {
			redirect('acp_missions/manage/ALL/ALL/');
		} elseif ($class_restrict == NULL) {
			redirect('acp_missions/manage/' . $division_restrict . '/ALL/');
		} elseif ($division_restrict == NULL) {
			redirect('acp_missions/manage/ALL/' . $class_restrict);
		}

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		$data['division_restrict'] = $division_restrict;
		$data['class_restrict'] = $class_restrict;

		//grab post
		$post_division_restrict = $this->security->sanitize_filename($this->input->post('division_restrict'));
		$post_class_restrict = $this->security->sanitize_filename($this->input->post('class_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));

		if (
			($division_restrict != $post_division_restrict && $post_division_restrict != '')
			OR ($class_restrict != $post_class_restrict && $post_class_restrict != '')
		) {
			redirect('acp_missions/manage/' . $post_division_restrict . '/' . $post_class_restrict);
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
					$sqlsearch = "WHERE (mission_index.title LIKE '%" . $search . "%'";
					$sqlsearch .= " OR mission_index.description LIKE '%" . $search . "%'";
					foreach ($search_array as &$row) {
						$sqlsearch .= " OR mission_index.title LIKE '%" . $row . "%'";
						$sqlsearch .= " OR mission_index.description LIKE '%" . $row . "%'";
					}
					$sqlsearch .= ')';
				} //for single term searches
				else {
					$sqlsearch = "WHERE mission_index.title LIKE '%$search%' OR mission_index.description LIKE '%$search%'";
				}

			} else {

				//not searching, handle restriction

				if (is_numeric($division_restrict)) {
					$sqlsearch = "WHERE mission_index.division = '$division_restrict'";
				} else {
					$sqlsearch = '';
				}

				if (is_numeric($class_restrict)) {

					if ($sqlsearch == '') {
						$sqlsearch = "WHERE mission_index.class = '$class_restrict'";
					} else {
						$sqlsearch .= " AND mission_index.class = '$class_restrict'";
					}

				}

			}

			//grab all missions from the database
			$query = $this->db->query("	SELECT 	
											mission_index.id,
											mission_index.title,
											mission_index.start_icao,
											mission_index.end_icao,
											mission_index.division,
											mission_index.class as clss,
											mission_index.start_date,
											mission_index.end_date
													
											FROM mission_index
												
											$sqlsearch
											
											ORDER BY mission_index.start_date DESC, mission_index.division, mission_index.title
										");

			$data['result'] = $query->result();
			$data['num_rows'] = $query->num_rows();

			$data['divisions_array'] = $this->Dispatch_model->get_division_array();

			$data['divisions_array'] = array('All' => 'All') + $data['divisions_array'];

			//create class array
			$limit = 7;
			if ($limit < 1) {
				$limit = 1;
			}
			$i = 1;
			$data['clss_array'] = array('ALL' => 'All');
			while ($i <= $limit) {
				$data['clss_array'][$i] = 'Class ' . $i;
				$i++;
			}

			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search', 'maxlength' => '25', 'size' => '25', 'value' => $search);

			//paginatipon
			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '15';

			$pag_config['base_url'] = $data['base_url'] . 'acp_missions/manage/' . $division_restrict . '/' . $class_restrict;
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 5;

			$this->pagination->initialize($pag_config);

			//output page
			$data['page_title'] = 'ACP - Mission Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_missionsmanage', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_missions/manage/' . $division_restrict . '/' . $class_restrict . '/' . $offset;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

}

?>