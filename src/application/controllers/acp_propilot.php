<?php

class Acp_propilot extends CI_Controller {

	function Acp_propilot()
	{
		parent::__construct();
	}


	function event_awards_remark($event_id = NULL, $pilot_id = NULL, $award_id = NULL){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');
		$this->load->model('Award_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;

		if($event_id == NULL || !is_numeric($event_id)){
			redirect('acp_propilot/event_manage/');
		}

		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){

			//display a page not found message
			show_404('page');

		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);

			//update data in session
			$this->session->set_userdata($sessiondata);


			//if a pilot id has been supplied, perform remark and redirect
			if(!empty($pilot_id) && !empty($award_id)){
				$data['event_award_return'] = $this->Pirep_model->event_award($pilot_id, $event_id, $award_id, $gmt_mysql_datetime);
			}




			//grab all pilots with a pirep for this event.
			$query = $this->db->query("	SELECT 	pilots.id as user_id,
												pilots.username as username,
												pilots.fname as fname,
												pilots.sname as sname,

												propilot_event_index.name as event_name,

												awards_assigned.awards_index_id as assigned_award_id,

												pirep.end_icao as end_icao,

												propilot_event_legs.id as event_leg_id,
												propilot_event_legs.award_id as award_id,
												propilot_event_legs.sequence as event_leg_sequence

									FROM propilot_event_index

										LEFT JOIN propilot_event_legs
										ON propilot_event_legs.event_id = propilot_event_index.id

										LEFT JOIN pirep
										ON propilot_event_legs.id = pirep.event_leg_id

										LEFT JOIN pilots
										ON pirep.user_id = pilots.id

										LEFT JOIN awards_assigned
										ON pilots.id = awards_assigned.user_id
										AND awards_assigned.awards_index_id = propilot_event_legs.award_id


									WHERE propilot_event_index.id = '$event_id'

									ORDER BY pilots.username, propilot_event_legs.sequence
									ASC

									");

			$event_pilots =  $query->result();
			$num_rows =  $query->num_rows();

			$data['event_id'] = $event_id;

			$data['pilot_list'] = array();
			$data['event_leg_list'] = array();
			$data['event_leg_count_array'] = array();

			foreach($event_pilots as $row){
				//initialise new pilot
				if(!array_key_exists($row->user_id, $data['pilot_list']) ){
					$data['pilot_list'][$row->user_id]['num_flights'] = 0;

				}

				//build pilot data
				$data['pilot_list'][$row->user_id]['user_id'] = $row->user_id;
				$data['pilot_list'][$row->user_id]['name'] = $row->fname.' '.$row->sname;
				$data['pilot_list'][$row->user_id]['username'] = 'EHM-'.$row->username;
				$data['pilot_list'][$row->user_id]['num_flights']++;
				$data['pilot_list'][$row->user_id]['awards'][$row->award_id] = $row->award_id;
				$data['pilot_list'][$row->user_id]['awards_assigned'][$row->assigned_award_id] = $row->assigned_award_id;
				$data['pilot_list'][$row->user_id]['last_location'] = $row->end_icao;
				$data['pilot_list'][$row->user_id]['last_leg'] = $row->event_leg_sequence;

				//build event_leg_data
				$data['event_leg_count_array'][$row->event_leg_sequence] = $row->event_leg_id;
				$data['event_leg_list'][$row->event_leg_sequence]['id'] = $row->event_leg_id;
				$data['event_leg_list'][$row->event_leg_sequence]['sequence'] = $row->event_leg_sequence;
			}



			$data['event_leg_count'] = count($data['event_leg_count_array']);

			$data['event_pilots'] = $event_pilots;

			//event_award($pilot_id = '', $event_id = '', $award_id = '', $gmt_mysql_datetime)
			//output page
			$data['page_title'] = 'ACP - Propilot Event Awards Remark';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_ppeventawardremark', $data);

		}


	}


	function event_legs_edit($event_id = NULL, $leg_id = NULL, $sequence = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');
		$this->load->model('Award_model');

		$data['event_id'] = $event_id;
		$data['leg_id'] = $leg_id;
		$data['sequence'] = $sequence;
		$data['error'] = '';

		$data['highlight1'] = '';
		$data['highlight2'] = '';

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;

		if($event_id == NULL || !is_numeric($event_id)){
			redirect('acp_propilot/event_manage/');
		}
		elseif($leg_id == NULL || !is_numeric($leg_id)){
			redirect('acp_propilot/event_edit/'.$event_id);
		}
		elseif($sequence == NULL || !is_numeric($sequence)){
			redirect('acp_propilot/event_edit/'.$event_id);
		}


		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){

			//display a page not found message
			show_404('page');

		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);

			//update data in session
			$this->session->set_userdata($sessiondata);


			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			//$post_flight_sim = $this->security->sanitize_filename($this->input->post('flight_sim'));
			//$sequence = $this->security->sanitize_filename($this->input->post('sequence'));
			$start_icao = $this->security->sanitize_filename($this->input->post('start_icao'));
			$end_icao = $this->security->sanitize_filename($this->input->post('end_icao'));
			$award_id = $this->security->sanitize_filename($this->input->post('award_id'));
			//start date
			$start_day = $this->security->sanitize_filename($this->input->post('start_day'));
			$start_month = $this->security->sanitize_filename($this->input->post('start_month'));
			$start_year = $this->security->sanitize_filename($this->input->post('start_year'));
			//end_date
			$end_day = $this->security->sanitize_filename($this->input->post('end_day'));
			$end_month = $this->security->sanitize_filename($this->input->post('end_month'));
			$end_year = $this->security->sanitize_filename($this->input->post('end_year'));


			$current_pilot_username = $this->session->userdata('username');
			$current_pilot_user_id = $this->session->userdata('user_id');


			//perform validation
			$this->form_validation->set_rules('start_icao', 'start_icao', 'required');
			$this->form_validation->set_rules('end_icao', 'end_icao', 'required');

			$this->form_validation->set_rules('start_day', 'start_day', 'required');
			$this->form_validation->set_rules('start_month', 'start_month', 'required');
			$this->form_validation->set_rules('start_year', 'start_year', 'required');

			$this->form_validation->set_rules('end_day', 'end_day', 'required');
			$this->form_validation->set_rules('end_month', 'end_month', 'required');
			$this->form_validation->set_rules('end_year', 'end_year', 'required');

			if($this->form_validation->run() == FALSE){
				$validation = 0;
			}
			else{
				$validation = 1;
			}



			//check end date
			if($valid == 'true'
			&& is_numeric($end_day) && is_numeric($end_month) && is_numeric($end_year)
			&& !checkdate($end_month, $end_day, $end_year)){
				$validation = 0;
				$data['error'] = 'Start Date is not valid';
			}
			elseif($valid == 'true'
			&& (!is_numeric($end_day) || !is_numeric($end_month) || !is_numeric($end_year))){
				$validation = 0;
				$data['error'] = 'Start Date is not valid';
			}


			//check start date
			if($valid == 'true'
			&& is_numeric($start_day) && is_numeric($start_month) && is_numeric($start_year)
			&& !checkdate($start_month, $start_day, $start_year)){
				$validation = 0;
				$data['error'] = 'Start Date is not valid';
			}
			elseif($valid == 'true'
			&& (!is_numeric($start_day) || !is_numeric($start_month) || !is_numeric($start_year))){
				$validation = 0;
				$data['error'] = 'Start Date is not valid';
			}


			//set start and end date
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;

			if($valid == 'true' && $validation == 1){

				if($award_id == ''){
					$award_id = NULL;
				}




				//check to make sure there's no duplicate
				$query = $this->db->query("	SELECT 	propilot_event_legs.id as id

										FROM propilot_event_legs

										WHERE propilot_event_legs.event_id = '$event_id'
										AND propilot_event_legs.start_icao = '$start_icao'
										AND propilot_event_legs.end_icao = '$end_icao'
										AND propilot_event_legs.award_id = '$award_id'

										AND propilot_event_legs.start_date = '$start_date'
										AND propilot_event_legs.end_date = '$end_date'

										LIMIT 1

										");

				$insert_data =  $query->result_array();
				$num_insert =  $query->num_rows();

				//array data
				$propilot_event_legs_data = array(
												'event_id' => $event_id,
												'sequence' => $sequence,
												'start_icao' => strtoupper($start_icao),
												'end_icao' => strtoupper($end_icao),
												'award_id' => $award_id,
												'start_date' => $start_date,
												'end_date' => $end_date,
												);


				//only create a new record if there is no existing and we're creating new
				if($num_insert < 1 && $leg_id == 0){


					//insert the data
					$this->db->insert('propilot_event_legs', $this->db->escape($propilot_event_legs_data));


				}
				elseif($leg_id > 0){

					//update the data
					$this->db->where('id', $leg_id);
					$this->db->update('propilot_event_legs', $this->db->escape($propilot_event_legs_data));

				}


				//redirect
				redirect('acp_propilot/event_legs/'.$event_id);

			}

			if($validation == 0 && $valid == 'true'){
				$data['error'] = 'Required fields were not completed';

				$data['highlight1'] = '<font color="red">';
				$data['highlight2'] = '</font>';
			}

			//assemble form data
			$start_icao = '';
			$end_icao = '';
			$award_id = '';


			//make a database call to see if record exists and populate values if it does
			if($leg_id > 0){
				$query = $this->db->query("	SELECT
												propilot_event_legs.id as id,
												propilot_event_legs.start_icao as start_icao,
												propilot_event_legs.end_icao as end_icao,
												propilot_event_legs.start_date as start_date,
												propilot_event_legs.end_date as end_date,
												propilot_event_legs.award_id as award_id


											FROM propilot_event_legs

											WHERE propilot_event_legs.id = '$leg_id'
											LIMIT 1

											");

				$record_data =  $query->result_array();
				$num_records =  $query->num_rows();

				if($num_records > 0){
					$start_icao = $record_data['0']['start_icao'];
					$end_icao = $record_data['0']['end_icao'];
					$start_date = $record_data['0']['start_date'];
					$end_date = $record_data['0']['end_date'];
					$award_id = $record_data['0']['award_id'];
				}

			}


			$data['start_day'] = gmdate('d', strtotime($start_date));
			$data['start_month'] = gmdate('m', strtotime($start_date));
			$data['start_year'] = gmdate('Y', strtotime($start_date));

			$data['end_day'] = gmdate('d', strtotime($end_date));
			$data['end_month'] = gmdate('m', strtotime($end_date));
			$data['end_year'] = gmdate('Y', strtotime($end_date));

			//make a call to check the arrival on last leg (and date) to populate the next (if this is not an edit)
			if($sequence > 1 && $leg_id == 0){

				$prev_sequence = $sequence - 1;

				$query = $this->db->query("	SELECT
												propilot_event_legs.id as id,
												propilot_event_legs.start_icao as start_icao,
												propilot_event_legs.end_icao as end_icao,
												propilot_event_legs.start_date as start_date,
												propilot_event_legs.end_date as end_date,
												propilot_event_legs.award_id as award_id


											FROM propilot_event_legs

											WHERE propilot_event_legs.event_id = '$event_id'
											AND propilot_event_legs.sequence = '$prev_sequence'
											LIMIT 1

											");

				$record_data =  $query->result_array();
				$num_records =  $query->num_rows();

				//if we got a hit
				if($num_records > 0){
					$start_icao = $record_data['0']['end_icao'];

					$week = 60*60*24*7;

					$start_time = strtotime($record_data['0']['start_date']) + $week;

					$end_time = strtotime($record_data['0']['end_date']) + $week;

					$data['start_day'] = gmdate('d', $start_time);
					$data['start_month'] = gmdate('m', $start_time);
					$data['start_year'] = gmdate('Y', $start_time);

					$data['end_day'] = gmdate('d', $end_time);
					$data['end_month'] = gmdate('m', $end_time);
					$data['end_year'] = gmdate('Y', $end_time);
				}
			}
			//dropdowns
			//$data['start_icao'] = $start_icao;
			//$data['end_icao'] = $end_icao;
			$data['award_id'] = $award_id;




			//form input
			$data['start_icao'] = array( 'name' => 'start_icao','id' => 'start_icao','value' => $start_icao, 'maxlength' => '4','size' => '4');
			$data['end_icao'] = array( 'name' => 'end_icao','id' => 'end_icao','value' => $end_icao, 'maxlength' => '4','size' => '4');

			//text area
			//$data['description'] = array( 'name' => 'description','id' => 'description','value' => $description, 'rows' => '10','cols' => '45');



			//download type dropdown array
			$data['award_array'] = $this->Award_model->get_awards_event();


				//day_array
				$i = 1;
				$data['day_array']['00'] = '';
				while($i <= 31){

					$j = str_pad($i, 2, "0", STR_PAD_LEFT);

					$data['day_array'][$j] = $j;
					$i++;
				}

				//month_array
				$i = 1;
				$data['month_array']['00'] = '';
				while($i <= 12){

					$j = str_pad($i, 2, "0", STR_PAD_LEFT);

					$data['month_array'][$j] = $j;
					$i++;
				}


				//year_array
				$current_year = date('Y', time());
				$data['year_array']['0000'] = '';
				$i = 2004;
				while($i <= ($current_year + 3)){
					$data['year_array'][$i] = $i;
					$i++;
				}





			//output page
			$data['page_title'] = 'ACP - Propilot Event Legs Edit';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_ppeventlegsedit', $data);

		}
		//invalid admin login
		elseif($is_admin == '1'){

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_propilot/event_legs_edit/'.$event_id.'/'.$leg_id.'/'.$sequence;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}



	function event_legs($event_id = NULL, $selected_version = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');
		$this->load->model('Pirep_model');
		$this->load->library('Geocalc_fns');

		$data['event_id'] = $event_id;
		$data['error'] = '';

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;

		if($event_id == NULL){
			redirect('acp_propilot/event_manage');
		}


		$data['selected_version'] = $selected_version;

		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){

			//display a page not found message
			show_404('page');

		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);

			//update data in session
			$this->session->set_userdata($sessiondata);


			//sql query to grab all the event data
		$query = $this->db->query("	SELECT 	propilot_event_index.id as id,
											propilot_event_index.name as name,
											propilot_event_index.aircraft_id as aircraft_id,
											propilot_event_index.difficulty as difficulty,
											propilot_event_index.description as description,
											propilot_event_index.start_date as start_date,
											propilot_event_index.active as active,
											propilot_event_legs.id as leg_id,
											propilot_event_legs.sequence as sequence,
											propilot_event_legs.start_icao as start_icao,
											propilot_event_legs.end_icao as end_icao,
											propilot_event_legs.start_date as start_date,
											propilot_event_legs.end_date as end_date,
											propilot_event_legs.award_id as award_id,
											awards_index.award_name as award_name,
											start_data.name as start_name,
											start_data.lat as start_lat,
											start_data.long as start_lon,

											end_data.name as end_name,
											end_data.lat as end_lat,
											end_data.long as end_lon

									FROM propilot_event_index

										LEFT JOIN propilot_event_legs
										ON propilot_event_legs.event_id = propilot_event_index.id

										LEFT JOIN airports_data as start_data
										ON start_data.icao = propilot_event_legs.start_icao

										LEFT JOIN airports_data as end_data
										ON end_data.icao = propilot_event_legs.end_icao

										LEFT JOIN awards_index
										ON awards_index.id = propilot_event_legs.award_id

									WHERE propilot_event_index.id = '$event_id'

									ORDER BY propilot_event_legs.sequence

										");

		$tour_data =  $query->result_array();
		$data['tour_data'] = $tour_data;
		$data['num_rows'] = $query->num_rows();
		$data['tour_name'] =  $tour_data[0]['name'];

		if($data['num_rows'] > 0){

			//assign into groups of flight sim versions
			$flight_array = array();



			$i = 0;
			foreach($tour_data as $row){


				$data['active'] = $row['active'];

				$flight_array[$row['sequence']]['leg_id'] = $row['leg_id'];
				$flight_array[$row['sequence']]['sequence'] = $row['sequence'];
				$flight_array[$row['sequence']]['start_icao'] = $row['start_icao'];
				$flight_array[$row['sequence']]['start_name'] = $row['start_name'];
				$flight_array[$row['sequence']]['end_icao'] = $row['end_icao'];
				$flight_array[$row['sequence']]['end_name'] = $row['end_name'];
				$flight_array[$row['sequence']]['award_id'] = $row['award_id'];
				$flight_array[$row['sequence']]['award_name'] = $row['award_name'];

				$flight_array[$row['sequence']]['start_date'] = $row['start_date'];
				$flight_array[$row['sequence']]['end_date'] = $row['end_date'];

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
				$bearing = $this->geocalc_fns->getRhumbLineBearing($lat1, $lon1, $lat2, $lon2);
				$flight_array[$row['sequence']]['gc_bearing'] = $bearing;

				//calculate distance
				$gcd_km = $this->geocalc_fns->GCDistance($lat1, $lon1, $lat2, $lon2);
				$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

				$flight_array[$row['sequence']]['gcd_nm'] = $gcd_nm;



			$i++;
			}

			$data['flight_array'] = $flight_array;

		}
		else{
		$data['flight_array'] = array();
		}

		//output page
		$data['page_title'] = 'ACP - Propilot Event Legs';
		$data['admin_menu'] = 1;
		$this->view_fns->view('global/admincp/acp_ppeventlegs', $data);

		}
		//invalid admin login
		elseif($is_admin == '1'){
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_propilot/event_legs/'.$event_id.'/'.$selected_version;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}







	function event_edit($event_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Dispatch_model');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;

		if($event_id == NULL){
			redirect('acp_propilot/event_manage');
		}

		if($event_id > 0){
			$data['mode'] = 'Edit';
		}
		else{
			$data['mode'] = 'Create';
		}

		$data['event_id'] = $event_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		$data['allowed_types'] = 'png|gif|jpg|jpeg';
		$data['max_size'] = '75';
		$data['max_width'] = '600';
		$data['max_height'] = '80';

		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){

			//display a page not found message
			show_404('page');

		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$name = $this->security->sanitize_filename($this->input->post('name'));
			$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));
			$difficulty = $this->security->sanitize_filename($this->input->post('difficulty'));
			$description = $this->security->sanitize_filename($this->input->post('description'));
			$start_day = $this->security->sanitize_filename($this->input->post('start_day'));
			$start_month = $this->security->sanitize_filename($this->input->post('start_month'));
			$start_year = $this->security->sanitize_filename($this->input->post('start_year'));
			$active = $this->security->sanitize_filename($this->input->post('active'));


			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('name', 'name', 'required');
			$this->form_validation->set_rules('aircraft_id', 'aircraft_id', 'required');
			$this->form_validation->set_rules('start_day', 'start_day', 'required');
			$this->form_validation->set_rules('start_month', 'start_month', 'required');
			$this->form_validation->set_rules('start_year', 'start_year', 'required');
			$this->form_validation->set_rules('active', 'active', 'required');

			if($this->form_validation->run() == FALSE){
				$validation = 0;
			}
			else{
				$validation = 1;
			}

			//check date
			if($valid == 'true'
			&& is_numeric($start_day) && is_numeric($start_month) && is_numeric($start_year)
			&& !checkdate($start_month, $start_day, $start_year)){
				$validation = 0;
				$data['error'] = 'Date is not valid';
			}
			elseif($valid == 'true'
			&& (!is_numeric($start_day) || !is_numeric($start_month) || !is_numeric($start_year))){
				$validation = 0;
				$data['error'] = 'Date is not valid';
			}

			if($event_id > 0){

				//need to determine whether or not this is a valid tour - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT
												propilot_event_index.id,
												propilot_event_index.name,
												propilot_event_index.aircraft_id,
												propilot_event_index.difficulty,
												propilot_event_index.description,
												propilot_event_index.start_date,
												propilot_event_index.active

												FROM propilot_event_index

												WHERE propilot_event_index.id = '$event_id'

												LIMIT 1
											");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if no return, set create new
				if($num_results < 1){
					redirect('acp_propilot/event_manage/');
				}

			}

			if($valid == 'true' && $validation == 1){


				//data has been submitted, array it and update the record

				$start_date = $start_year.'-'.$start_month.'-'.$start_day;

				$event_data = array(
						'name' => $name,
						'aircraft_id' => $aircraft_id,
						'difficulty' => $difficulty,
						'description' => $description,
						'start_date' => $start_date,
						'active' => $active,
				);

				//if we are editing
				if($event_id > 0){

					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('propilot_event_index', $this->db->escape($event_data));
				}
				else{

					$event_data['submitted'] = $gmt_mysql_datetime;
					$event_data['submitted_by'] = $current_pilot_user_id;

					//we are creating a new record
					$this->db->insert('propilot_event_index', $this->db->escape($event_data));

					//grab the record id for the upload
					$query = $this->db->query("	SELECT
													propilot_event_index.id

													FROM propilot_event_index

													WHERE propilot_event_index.name = '$name'
													AND propilot_event_index.submitted = '$gmt_mysql_datetime'
													AND propilot_event_index.submitted_by = '$current_pilot_user_id'

													LIMIT 1
												");

					$result = $query->result_array();
					$num_results = $query->num_rows();

					if($num_results > 0){
						$event_id = $result['0']['id'];
					}
				}

					if($event_id > 0){

						// do upload
						$destination_path = $this->config->item('base_path').'assets/uploads/events/';
						$config['upload_path'] = $this->config->item('base_path').'assets/uploads/tmp/';
						$config['allowed_types'] = $data['allowed_types'];
						$config['max_size']	= $data['max_size'];
						$config['max_width']  = $data['max_width'];
						$config['max_height']  = $data['max_height'];



						//sort out upload folder
						if(!is_dir($destination_path)){
							//create path
							if(!mkdir($destination_path, 0755, true)){
								$data['error'] .= 'Could not create destination folder: '.$destination_path.'<br />';
							}
						}

						$this->load->library('upload', $config);

						if ( ! $this->upload->do_upload())
						{

							$upload_data = array('upload_data' => $this->upload->data());

							//if we did upload, but error'd
							if($upload_data['upload_data']['file_size'] > 0){
									$data['error'] .= $this->upload->display_errors()
								.' File must be smaller than '.$config['max_size'].'k and no bigger than '.$config['max_width']
								.'x'.$config['max_height'].'. Allowed file types are '.$config['allowed_types'];
							}
							//else{
							//	$data['error'] .= $this->upload->display_errors();
							//}

						}
						else
						{

							$upload_data = array('upload_data' => $this->upload->data());

							//delete any previous images
							foreach (glob($destination_path.$event_id.'.*') as $filename) {
							   unlink($filename);
							}


							if($upload_data['upload_data']['file_ext'] == '.jpeg'){
								$ext = '.jpg';
							}
							elseif($upload_data['upload_data']['file_ext'] == 'jpeg'){
								$ext = 'jpg';
							}
							else{
								$ext = strtolower($upload_data['upload_data']['file_ext']);
							}

							if(!rename($upload_data['upload_data']['full_path'],$destination_path.$event_id.$ext)){

								$data['error'] .= 'Could not move file to final location. The sub folder ('.$destination_path.') may not be writable<br /><br />';

							}

							if(is_file($upload_data['upload_data']['full_path'])){
								unlink($upload_data['upload_data']['full_path']);
							}

							$upload_data['upload_data']['file_name'] = $event_id;


						}
					}

				//if there were no errors
				if($data['error'] == ''){
					redirect('acp_propilot/event_manage/');
				}
				else{
					//output error message
					$data['page_title'] = 'Error';
					$this->view_fns->view('global/error/error', $data);
				}


			}
			// haven't had data submitted or failed validation
			else{

				//initialise all values
				$name = '';
				$aircraft_id = '';
				$difficulty = '';
				$description = '';
				$start_day = gmdate('d', time());
				$start_month = gmdate('m', time());
				$start_year = gmdate('Y', time());
				$active = '0';


				//if we are editing
				if($event_id > 0){

					//prepare dropdowns etc for output from database
					$name = $result['0']['name'];
					$aircraft_id = $result['0']['aircraft_id'];
					$difficulty = $result['0']['difficulty'];
					$description = $result['0']['description'];
					$start_day = gmdate('d', strtotime($result['0']['start_date']));
					$start_month = gmdate('m', strtotime($result['0']['start_date']));
					$start_year = gmdate('Y', strtotime($result['0']['start_date']));
					$active = $result['0']['active'];

				}


				//dropdowns
				$data['aircraft_id'] = $aircraft_id;
				$data['start_day'] = $start_day;
				$data['start_month'] = $start_month;
				$data['start_year'] = $start_year;
				$data['active'] = $active;


				//text area
				$data['description'] = array( 'name' => 'description','id' => 'description','value' => $description, 'rows' => '10','cols' => '45');
				$data['difficulty'] = array( 'name' => 'difficulty','id' => 'difficulty','value' => $difficulty, 'rows' => '10','cols' => '45');


				//define form elements
				$data['name'] = array( 'name' => 'name','id' => 'name','value' => $name, 'maxlength' => '40','size' => '40');


				//define all the arrays

				$data['active_array'] = array('0' => 'Inactive', '1' => 'Active');

				//aircraft_array
				$aircraft_data = $this->Dispatch_model->get_aircraft_array('ALL', '', 0);

				$data['aircraft_array'] = $aircraft_data['aircraft_array_div'];

				ksort($data['aircraft_array']);

				//day_array
				$i = 1;
				$data['day_array']['00'] = '';
				while($i <= 31){
					$data['day_array'][$i] = $i;
					$i++;
				}

				//month_array
				$i = 1;
				$data['month_array']['00'] = '';
				while($i <= 12){
					$data['month_array'][$i] = $i;
					$i++;
				}


				//year_array
				$current_year = date('Y', time());
				$data['year_array']['0000'] = '';
				$i = 2004;
				while($i <= ($current_year + 3)){
					$data['year_array'][$i] = $i;
					$i++;
				}


				//output page
				$data['page_title'] = 'ACP - Propilot Event Edit';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_ppeventedit', $data);
			}


		}
		//invalid admin login
		elseif($is_admin == '1'){
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_propilot/event_edit/'.$event_id.'/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}









	function event_manage($system_restrict = NULL, $offset = 0)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');


		if($system_restrict == NULL){
			redirect('acp_propilot/event_manage/ALL/');
		}

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;

		$data['system_restrict'] = $system_restrict;

		//grab post
		$post_system_restrict = $this->security->sanitize_filename($this->input->post('system_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));

		if(
		($system_restrict != $post_system_restrict && $post_system_restrict != '')
		){
			redirect('acp_propilot/event_manage/'.$post_system_restrict.'/');
		}

		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){

			//display a page not found message
			show_404('page');

		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$sqlsearch = '';
			//handle search
			if($valid == 'true' && $search != ''){

				//split up the search into constituent terms
				$search_array = explode(" ",$search);
				$num_search = count($search_array);

				//for multiple term searches
				if ($num_search > 1){
					$sqlsearch = "WHERE (propilot_event_index.name LIKE '%".$search."%'";
					foreach ($search_array as &$row){
						$sqlsearch .= " OR propilot_event_index.name LIKE '%".$row."%'";
					}
					$sqlsearch .= ')';
				}


				//for single term searches
				else{
				$sqlsearch = "WHERE (propilot_event_index.name LIKE '%$search%')";
				}

			}
			else{

				//not searching, handle restriction
				$sqlsearch = '';

				if($system_restrict != 'ALL'){
					$sqlsearch = "WHERE YEAR(propilot_event_index.start_date) = '$system_restrict'";
				}

			}


			//grab all events from the database
			$query = $this->db->query("	SELECT
											propilot_event_index.id,
											propilot_event_index.name,
											propilot_event_index.aircraft_id,
											aircraft.name as aircraft,
											propilot_event_index.difficulty,
											propilot_event_index.description,
											propilot_event_index.start_date,
											propilot_event_index.active

											FROM propilot_event_index

												LEFT JOIN aircraft
												ON aircraft.id = propilot_event_index.aircraft_id

											$sqlsearch

											ORDER BY propilot_event_index.start_date DESC
										");

			$data['result'] =  $query->result();
			$data['num_rows'] =  $query->num_rows();


			//current year
			$current_year = gmdate('Y', time('now'));

			$current_year++;

			//restrict based on year
			$data['system_array'] = array('ALL' =>'All Years');
			while($current_year >= 2010){
				$data['system_array'][$current_year] = $current_year;

				$current_year--;
			}


			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search','maxlength' => '25', 'size' => '25', 'value' => $search);


			//paginatipon
			if($offset == NULL || $offset == ''){
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '15';

			$pag_config['base_url'] = $data['base_url'].'acp_propilot/event_manage/'.$system_restrict.'/';
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 5;

			$this->pagination->initialize($pag_config);



			//output page
			$data['page_title'] = 'ACP - Propilot Event Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_ppeventsmanage', $data);

		}
		//invalid admin login
		elseif($is_admin == '1'){
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_propilot/event_manage/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}







	function aircraft_edit($pp_aircraft_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('Admin_fns');
		//$this->load->model('Fleet_model');
		$this->load->model('Dispatch_model');


		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;

		if($pp_aircraft_id == NULL){
			redirect('acp_propilot/aicraft_manage');
		}

		if($pp_aircraft_id > 0){
			$data['mode'] = 'Edit';
		}
		else{
			$data['mode'] = 'Create';
		}


		$data['pp_aircraft_id'] = $pp_aircraft_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){

			//display a page not found message
			show_404('page');

		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);

			//update data in session
			$this->session->set_userdata($sessiondata);


			//create aircraft array
			$aircraft_data = $this->Dispatch_model->get_aircraft_array('ALL', '', 0);

			$data['aircraft_array'] = $aircraft_data['aircraft_array_div'];

			ksort($data['aircraft_array']);

			$aircraft_array_div_id = $aircraft_data['aircraft_array_div_id'];



			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$tail_id = $this->security->sanitize_filename($this->input->post('tail_id'));
			$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));
			$state_id = $this->security->sanitize_filename($this->input->post('state_id'));
			$location = $this->security->sanitize_filename($this->input->post('location'));
			$owner = $this->security->sanitize_filename($this->input->post('owner'));


			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('aircraft_id', 'aircraft_id', 'required');
			$this->form_validation->set_rules('tail_id', 'tail_id', 'required');
			$this->form_validation->set_rules('state_id', 'state_id', 'required');
			$this->form_validation->set_rules('location', 'location', 'required');


			if($this->form_validation->run() == FALSE){
				$validation = 0;
				$data['error'] = 'Validation failed. Check fields and try again.';
			}
			else{
				$validation = 1;
			}

			//verify tail_id isn't a duplicate if we're non editing
			if($valid == 'true' && $tail_id != '' && $pp_aircraft_id <= 0){
				$query = $this->db->query("	SELECT
												propilot_aircraft.id,
												propilot_aircraft.tail_id

												FROM propilot_aircraft

												WHERE propilot_aircraft.tail_id = '$tail_id'

												LIMIT 1
											");


				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if we get a return, then the tail id already exists
				if($num_results > 0){
					$validation = 0;
					$data['error'] = 'Validation failed. Tail number already exists.';
				}
			}

			//additional validation runs


			if($pp_aircraft_id > 0){

				//need to determine whether or not this is a valid propilot aircraft - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT
												propilot_aircraft.id,
												propilot_aircraft.aircraft_id,
												propilot_aircraft.tail_id,
												propilot_aircraft.state_id,
												propilot_aircraft.location,
												propilot_aircraft.owner,
												propilot_aircraft.rollout,
												propilot_aircraft.last_maintenance,
												propilot_aircraft.last_flown

												FROM propilot_aircraft

												WHERE propilot_aircraft.id = '$pp_aircraft_id'

												LIMIT 1
											");


				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if no return, set create new
				if($num_results < 1){
					redirect('acp_propilot/aircraft_manage/');
				}

			}

			if($valid == 'true' && $validation == 1){

				if($owner == ''){
					$owner = NULL;
				}

				$propilot_aircraft_data = array(
						'aircraft_id' => $aircraft_id,
						'tail_id' => $tail_id,
						'state_id' => $state_id,
						'location' => $location,
						'owner' => $owner,
				);


				//if we are editing
				if($pp_aircraft_id > 0){

					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('propilot_aircraft', $this->db->escape($propilot_aircraft_data));
				}
				else{

					//array additional data
					$propilot_aircraft_data['rollout'] = $gmt_mysql_datetime;
					$propilot_aircraft_data['last_maintenance'] = $gmt_mysql_datetime;
					$propilot_aircraft_data['last_flown'] = $gmt_mysql_datetime;

					//we are creating a new record
					$this->db->insert('propilot_aircraft', $this->db->escape($propilot_aircraft_data));

				}


				//if there were no errors
				if($data['error'] == ''){
					redirect('acp_propilot/aircraft_manage/');
				}
				else{
					//output error message
					$data['page_title'] = 'Error';
					$this->view_fns->view('global/error/error', $data);
				}


			}
			// haven't had data submitted or failed validation
			else{

				if($valid != 'true'){
				//initialise all values
				$aircraft_id = '';
				$tail_id = '';
				$state_id = '1';
				$location = 'EGLL';
				$owner = '';
				}

				//if we are editing
				if($pp_aircraft_id > 0){

					//prepare dropdowns etc for output from database

					$aircraft_id = $result['0']['aircraft_id'];
					$tail_id = $result['0']['tail_id'];
					$state_id = $result['0']['state_id'];
					$location = $result['0']['location'];
					$owner = $result['0']['owner'];
				}


				//dropdowns
				$data['aircraft_id'] = $aircraft_id;
				$data['state_id'] = $state_id;
				$data['location'] = $location;
				$data['owner'] = $owner;

				//text area
				//$data['description'] = array( 'name' => 'description','id' => 'description','value' => $description, 'rows' => '10','cols' => '45');

				//define form elements
				$data['tail_id'] = array( 'name' => 'tail_id','id' => 'tail_id','value' => $tail_id, 'maxlength' => '15','size' => '5');


				//define all the arrays
				//$data['divisions_array'] = $this->Dispatch_model->get_division_array();
				$data['airfield_array'] = $this->Dispatch_model->get_airfield_array();



				//array of pilots
				$query = $this->db->query("	SELECT
												pilots.id,
												pilots.username,
												pilots.fname,
												pilots.sname

												FROM pilots

												WHERE pilots.email_confirmed = '1'
												AND pilots.lastflight >= '$active_compare_date'

												ORDER BY pilots.username
											");


				$result = $query->result();
				$data['pilot_array'] = array('' => 'None');
				foreach($result as $row){
					$data['pilot_array'][$row->id] = '[EHM-'.$row->username.'] '.$row->fname.' '.$row->sname;
				}


				//array of aircraft states
				$query = $this->db->query("	SELECT
												propilot_aircraft_state.id,
												propilot_aircraft_state.state_name

												FROM propilot_aircraft_state

												ORDER BY propilot_aircraft_state.state_name
											");


				$result = $query->result();
				$data['state_array'] = array('' => '');
				foreach($result as $row){
					$data['state_array'][$row->id] = $row->state_name;
				}


				//handle errors
				if($data['error'] != '' && $valid == 'true'){
					$data['highlight1'] = '<center><span style="error">';
					$data['highlight2'] = '</span></center>';
				}
				else{
					$data['error'] = '';
				}

				//output page
				$data['page_title'] = 'ACP - Propilot Aircraft Edit';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_ppaircraftedit', $data);
			}


		}
		//invalid admin login
		elseif($is_admin == '1'){

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_propilot/aircraft_edit/'.$pp_aircraft_id.'/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}











	function aircraft_manage($aircraft_restrict = NULL, $location_restrict = NULL, $status_restrict = NULL, $acstatus_restrict = NULL, $search_url = NULL, $offset = 0)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		$this->load->model('Dispatch_model');


		if($aircraft_restrict == NULL && $location_restrict == NULL && $status_restrict == NULL && $acstatus_restrict == NULL){
			redirect('acp_propilot/aircraft_manage/ALL/ALL/ALL/ALL');
		}

		/*

		if($aircraft_restrict == NULL && $location_restrict == NULL && $status_restrict == NULL){
			redirect('acp_propilot/aircraft_manage/ALL/ALL/ALL/');
		}
		if($status_restrict == NULL && $location_restrict == NULL){
			redirect('acp_propilot/aircraft_manage/'.$aircraft_restrict.'/ALL/ALL/');
		}
		elseif($status_restrict == NULL){
			redirect('acp_propilot/aircraft_manage/'.$aircraft_restrict.'/'.$location_restrict.'/ALL');
		}
		*/
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;

		$data['aircraft_restrict'] = $aircraft_restrict;
		$data['location_restrict'] = $location_restrict;
		$data['status_restrict'] = $status_restrict;
		$data['acstatus_restrict'] = $acstatus_restrict;

		//grab post
		$post_aircraft_restrict = $this->security->sanitize_filename($this->input->post('aircraft_restrict'));
		$post_location_restrict = $this->security->sanitize_filename($this->input->post('location_restrict'));
		$post_status_restrict = $this->security->sanitize_filename($this->input->post('status_restrict'));
		$post_acstatus_restrict = $this->security->sanitize_filename($this->input->post('acstatus_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));

		if($search == '' && $search_url != '' && $search_url != ' ' && $search_url != '%20'){
			$search = $search_url;
		}

		$data['search_url'] = $search;

		if(
		($aircraft_restrict != $post_aircraft_restrict && $post_aircraft_restrict != '')
		OR ($location_restrict != $post_location_restrict && $post_location_restrict != '')
		OR ($status_restrict != $post_status_restrict && $post_status_restrict != '')
		OR ($acstatus_restrict != $post_acstatus_restrict && $post_acstatus_restrict != '')
		){
			redirect('acp_propilot/aircraft_manage/'.$post_aircraft_restrict.'/'.$post_location_restrict.'/'.$post_status_restrict.'/'.$post_acstatus_restrict.'/'.$search_url);
		}

		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){

			//display a page not found message
			show_404('page');

		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){

			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$sqlsearch = '';
			//handle search
			if($search != '' && $search != ' ' && $search != '%20'){

				//split up the search into constituent terms
				$search_array = explode(" ",$search);
				$num_search = count($search_array);

				//for multiple term searches
				if ($num_search > 1){
					$sqlsearch = "WHERE (propilot_aircraft.tail_id LIKE '%".$search."%'";
					$sqlsearch .= " OR propilot_aircraft.location LIKE '%".$search."%'";
					$sqlsearch .= " OR aircraft.name LIKE '%".$search."%'";
					$sqlsearch .= " OR propilot_aircraft_state.state_name LIKE '%".$search."%'";
					foreach ($search_array as $row){
						$sqlsearch .= " OR propilot_aircraft.title LIKE '%".$row."%'";
						$sqlsearch .= " OR propilot_aircraft.description LIKE '%".$row."%'";
						$sqlsearch .= " OR aircraft.name LIKE '%".$row."%'";
						$sqlsearch .= " OR propilot_aircraft_state.state_name LIKE '%".$row."%'";
					}
					$sqlsearch .= ')';
				}




				//for single term searches
				else{
				$sqlsearch = "WHERE propilot_aircraft.tail_id LIKE '%$search%'
				OR propilot_aircraft.location LIKE '%$search%'
				OR aircraft.name LIKE '%$search%'
				OR propilot_aircraft_state.state_name LIKE '%$search%'
				";
				}

			}

				//not searching, handle restriction


				if(is_numeric($aircraft_restrict)){
					if($sqlsearch == ''){
					$sqlsearch = "WHERE propilot_aircraft.aircraft_id = '$aircraft_restrict'";
					}
					else{
					$sqlsearch .= " AND propilot_aircraft.aircraft_id = '$aircraft_restrict'";
					}

				}

				if($location_restrict != 'ALL'){

					if($sqlsearch == ''){
						$sqlsearch = "WHERE propilot_aircraft.location = '$location_restrict'";
					}
					else{
						$sqlsearch .= " AND propilot_aircraft.location = '$location_restrict'";
					}

				}

				if(is_numeric($acstatus_restrict)){
						if($sqlsearch == ''){
							$sqlsearch = "WHERE propilot_aircraft.state_id = '$acstatus_restrict'";
						}
						else{
							$sqlsearch .= " AND propilot_aircraft.state_id = '$acstatus_restrict'";
						}

					}


				if($status_restrict != 'ALL'){

					switch($status_restrict){

						case 'Locked':
							if($sqlsearch == ''){
								$sqlsearch = "WHERE (propilot_aircraft.reserved IS NOT NULL
													AND propilot_aircraft.reserved != ''
													AND propilot_aircraft.reserved != '0000-00-00 00:00:00'
													AND propilot_aircraft.reserved >= '$pp_compare_date')";
							}
							else{
								$sqlsearch .= " AND (propilot_aircraft.reserved IS NOT NULL
													AND propilot_aircraft.reserved != ''
													AND propilot_aircraft.reserved != '0000-00-00 00:00:00'
													AND propilot_aircraft.reserved >= '$pp_compare_date')";
							}
						break;

						case 'Unlocked':
							if($sqlsearch == ''){
								$sqlsearch = "WHERE (propilot_aircraft.reserved IS NULL
													OR propilot_aircraft.reserved = ''
													OR propilot_aircraft.reserved = '0000-00-00 00:00:00'
													OR propilot_aircraft.reserved < '$pp_compare_date')";
							}
							else{
								$sqlsearch .= " AND (propilot_aircraft.reserved IS NULL
													OR propilot_aircraft.reserved = ''
													OR propilot_aircraft.reserved = '0000-00-00 00:00:00'
													OR propilot_aircraft.reserved < '$pp_compare_date')";
							}
						break;

						case 'Reserved':
							if($sqlsearch == ''){
								$sqlsearch = "WHERE propilot_aircraft.owner IS NOT NULL";
							}
							else{
								$sqlsearch .= " AND propilot_aircraft.owner IS NOT NULL";
							}
						break;

					}




			}


			//grab all aircraft from the database
			$query = $this->db->query("	SELECT
											propilot_aircraft.id,
											propilot_aircraft.owner,
											propilot_aircraft.tail_id,
											propilot_aircraft.state_id,
											propilot_aircraft.location,
											propilot_aircraft.reserved,
											propilot_aircraft.reserved_by,
											propilot_aircraft.last_flown,
											aircraft.name as name,
											propilot_aircraft_state.state_name as status,
											pilots.username as owner_username,
											pilots.fname as owner_fname,
											pilots.sname as owner_sname,
											reserver.username as reserver_username,
											reserver.fname as reserver_fname,
											reserver.sname as reserver_sname

											FROM propilot_aircraft

												LEFT JOIN aircraft
												ON aircraft.id = propilot_aircraft.aircraft_id

												LEFT JOIN propilot_aircraft_state
												ON propilot_aircraft_state.id = propilot_aircraft.state_id

												LEFT JOIN pilots
												on pilots.id = propilot_aircraft.owner

												LEFT JOIN pilots as reserver
												on reserver.id = propilot_aircraft.reserved_by

											$sqlsearch

											ORDER BY aircraft.name, propilot_aircraft.tail_id										");

			$data['result'] =  $query->result();
			$data['num_rows'] =  $query->num_rows();


			//$data['divisions_array'] = $this->Dispatch_model->get_division_array();

			//$data['divisions_array'] = array('All' => 'All')+$data['divisions_array'];

			//location_array
			$data['airfield_array'] = array('ALL' => 'All Airfields');
			$data['airfield_array'] += $this->Dispatch_model->get_airfield_array();

			//create aircraft array
			$aircraft_data = $this->Dispatch_model->get_aircraft_array('ALL', '', 0);

			$data['aircraft_array'] = array('ALL' => 'All Aircraft');
			$data['aircraft_array'] += $aircraft_data['aircraft_array_div'];


			//grab status for dropdown


			//grab all aircraft from the database
			$query = $this->db->query("	SELECT
											propilot_aircraft_state.id,
											propilot_aircraft_state.state_name

										FROM propilot_aircraft_state

										ORDER BY propilot_aircraft_state.state_name

											");

			$result = $query->result();
			$acstatus_array = array('ALL' => 'All Status');
			foreach($result as $row){
				$acstatus_array[$row->id] = $row->state_name;
			}

			$data['acstatus_array'] = $acstatus_array;


			//status_resrict
			$data['status_array'] = array(	'ALL' => 'All Reservation',
									'Locked' => 'Locked',
									'Unlocked' => 'Unlocked',
									'Reserved' => 'Reserved',
			);

			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search','maxlength' => '25', 'size' => '25', 'value' => $search);


			//paginatipon
			if($offset == NULL || $offset == ''){
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '15';

			if($search == ''){
				$search = ' ';
			}

			$pag_config['base_url'] = $data['base_url'].'acp_propilot/aircraft_manage/'.$aircraft_restrict.'/'.$location_restrict.'/'.$status_restrict.'/'.$acstatus_restrict.'/'.$search.'/';
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 8;

			$this->pagination->initialize($pag_config);



			//output page
			$data['page_title'] = 'ACP - Propilot Aircraft Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_ppaircraftmanage', $data);

		}
		//invalid admin login
		elseif($is_admin == '1'){

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_propilot/aircraft_manage/'.$aircraft_restrict.'/'.$location_restrict.'/'.$status_restrict.'/'.$acstatus_restrict.'/'.$search_url.'/'.$offset;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}



}

?>