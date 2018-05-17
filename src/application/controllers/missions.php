<?php

class Missions extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function assign($mission_id) {

		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');
		//load models
		$this->load->model('Dispatch_model');

		$current_pilot_username = $this->session->userdata['username'];
		$current_pilot_user_id = $this->session->userdata['user_id'];

		$today = date('Y-m-d', time());

		//first check that this is a valid mission
		$query = $this->db->query("	SELECT 	mission_index.id as id, 
											mission_index.start_icao as start_icao, 
											mission_index.end_icao as end_icao, 
											mission_index.aircraft_id as aircraft_id,
											mission_index.passengers as passengers,
											mission_index.cargo as cargo,
											mission_index.dep_time as dep_time,
											start_loc.lat as lat1,
											start_loc.long as lon1,
											end_loc.lat as lat2,
											end_loc.long as lon2
											
									FROM mission_index
									
										LEFT JOIN airports_data as start_loc
										ON start_loc.icao = mission_index.start_icao
										
										LEFT JOIN airports_data as end_loc
										ON end_loc.icao = mission_index.end_icao
									
									WHERE mission_index.id = '$mission_id'
									AND mission_index.start_date <= '$today'
									AND mission_index.end_date >= '$today'
									AND mission_index.aircraft_id  IS NOT NULL
									AND mission_index.start_icao  IS NOT NULL
									AND mission_index.end_icao  IS NOT NULL
									
									LIMIT 1
											
										");

		$mission_result = $query->result_array();
		$num_results = $query->num_rows();

		if ($num_results < 1) {
			//output error message
			$data['error'] = 'Invalid mission - cannot assign flight.';
			$data['page_title'] = 'Error';
			$this->view_fns->view('global/error/error', $data);
		}

		//check that it isn't already assigned to this pilot
		$query = $this->db->query("	SELECT 	pirep_assigned.id as id
											
									FROM pirep_assigned
									
									WHERE pirep_assigned.mission_id = '$mission_id'
									AND user_id = '$current_pilot_user_id'
									
									LIMIT 1
											
										");

		$results = $query->result();
		$num_results = $query->num_rows();

		if ($num_results > 0) {
			//output error message
			$data['error'] = 'Mission has already been assigned - cannot assign flight.';
			$data['page_title'] = 'Error';
			$this->view_fns->view('global/error/error', $data);
		} else {

			$lat1 = $mission_result[0]['lat1'];
			$lon1 = $mission_result[0]['lon1'];
			$lat2 = $mission_result[0]['lat2'];
			$lon2 = $mission_result[0]['lon2'];

			//calculate gcd
			$gcd_km = $this->geocalc_fns->GCDistance($lat1, $lon1, $lat2, $lon2);
			$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

			//restrict division and max flyable class of aircraft
			$division = 'ALL';
			$limit = $this->session->userdata('rank_id') + 1;

			//create aircraft_array
			$aircraft_data = $this->Dispatch_model->get_aircraft_array($division, $limit);

			$pax_array = $aircraft_data['pax_array'];
			$cargo_array = $aircraft_data['cargo_array'];

			$aircraft_id = $mission_result[0]['aircraft_id'];

			//loadout
			if (array_key_exists($mission_result[0]['aircraft_id'], $pax_array)) {
				//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
				$loadout = $this->pirep_fns->get_loadout($pax_array[$aircraft_id], $cargo_array[$aircraft_id]);

				//pax
				if ($mission_result[0]['passengers'] != '') {
					$num_pax = $mission_result[0]['passengers'];
				} else {
					$num_pax = $loadout['pax'];
				}

				//cargo
				if ($mission_result[0]['cargo'] != '') {
					$num_cargo = $mission_result[0]['cargo'];
				} else {
					$num_cargo = $loadout['cargo'];
				}
			} else {

				//pax
				if ($mission_result[0]['passengers'] != '') {
					$num_pax = $mission_result[0]['passengers'];
				} else {
					$num_pax = 0;
				}

				//cargo
				if ($mission_result[0]['cargo'] != '') {
					$num_cargo = $mission_result[0]['cargo'];
				} else {
					$num_cargo = 0;
				}
			}

			//assign the flight
			$mission_data = array(
				'user_id' => $current_pilot_user_id,
				'start_icao' => $mission_result[0]['start_icao'],
				'end_icao' => $mission_result[0]['end_icao'],
				'gcd' => $gcd_nm,
				'aircraft_id' => $aircraft_id,
				'passengers' => $num_pax,
				'cargo' => $num_cargo,
				'mission_id' => $mission_result[0]['id'],
				'created' => $data['gmt_mysql_datetime'],
				'dep_time' => $mission_result[0]['dep_time'],
			);

			//we are creating a new record
			$this->db->insert('pirep_assigned', $this->db->escape($mission_data));

			//redirect to dispatch
			redirect('dispatch');
		}

	}

	function index($selected_division_id = 2) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		$today = gmdate('Y-m-d', time());
		$clss_restrict = $this->session->userdata('rank_id') + 1;

		//if the supplied division code is not in the database, set to default
		if (!is_numeric($selected_division_id)) {
			$selected_division_id = 2;
		}

		//grab post data
		$post_selected_class = $this->security->sanitize_filename($this->input->post('selected_class'));

		if (
		($post_selected_class != '' && $post_selected_class != $selected_class)) {
			redirect('missions/index/' . $selected_division_id . '/' . $post_selected_class);
		}

		$i = 1;
		$data['class_array'] = array();
		while ($i <= $clss_restrict) {
			$data['class_array'][$i] = 'Class ' . $i;
			$i++;
		}

		$data['selected_division_id'] = $selected_division_id;

		//grab all divisions to build menu array
		$query = $this->db->query("	SELECT 	id, 
											division_longname, 
											prefix, 
											divisions.primary AS prim,
											divisions.missions AS missions,
											divisions.tours AS tours,
											blurb	
											
									FROM divisions
									
									WHERE public = '1'
									
									ORDER BY id
											
										");

		$division_results = $query->result();

		$data['division_array'] = array();
		$division_code_array = array();

		foreach ($division_results as $row) {
			$data['division_array'][$row->id]['id'] = $row->id;
			$data['division_array'][$row->id]['longname'] = $row->division_longname;
			$data['division_array'][$row->id]['prefix'] = $row->prefix;
			$data['division_array'][$row->id]['prim'] = $row->prim;
			$data['division_array'][$row->id]['missions'] = $row->missions;
			$data['division_array'][$row->id]['tours'] = $row->tours;
			$data['division_array'][$row->id]['blurb'] = $row->blurb;
			$division_code_array[$row->id] = $row->prefix;
		}

		$current_pilot_username = $this->session->userdata('username');
		$current_pilot_user_id = $this->session->userdata('user_id');

		//grab all missions
		$query = $this->db->query("	SELECT 	
										mission_index.id as id,
										mission_index.title as title,
										mission_index.description as description,
										mission_index.start_date as start_date,
										mission_index.end_date as end_date,
										mission_index.dep_weather as dep_weather,
										mission_index.arr_weather as arr_weather,
										mission_index.division as division,
										mission_index.class as clss,
										mission_index.start_icao as start_icao,
										mission_index.end_icao as end_icao,
										aircraft.name as aircraft
											
									FROM mission_index
									
										LEFT JOIN aircraft
										on aircraft.id = mission_index.aircraft_id
										
										LEFT JOIN pirep_assigned
										ON pirep_assigned.mission_id = mission_index.id
										AND pirep_assigned.user_id = '$current_pilot_user_id'
									
									WHERE mission_index.start_date <= '$today'
									AND mission_index.end_date >= '$today'
									AND mission_index.division = '$selected_division_id'
									AND pirep_assigned.id IS NULL
									AND mission_index.aircraft_id  IS NOT NULL
									AND mission_index.start_icao  IS NOT NULL
									AND mission_index.end_icao  IS NOT NULL
									
									ORDER BY mission_index.class, mission_index.division, mission_index.id
											
										");

		$mission_results = $query->result();

		$data['mission_array'] = array();
		$data['prefix'] = '';
		$data['division'] = '';

		foreach ($mission_results as $row) {
			$data['mission_array'][$row->clss][$row->id]['id'] = $row->id;
			$data['mission_array'][$row->clss][$row->id]['title'] = $row->title;
			$data['mission_array'][$row->clss][$row->id]['description'] = $row->description;
			$data['mission_array'][$row->clss][$row->id]['start_date'] = $row->start_date;
			$data['mission_array'][$row->clss][$row->id]['end_date'] = $row->end_date;
			$data['mission_array'][$row->clss][$row->id]['dep_weather'] = $row->dep_weather;
			$data['mission_array'][$row->clss][$row->id]['arr_weather'] = $row->arr_weather;
			$data['mission_array'][$row->clss][$row->id]['division'] = $row->division;
			$data['mission_array'][$row->clss][$row->id]['start_icao'] = $row->start_icao;
			$data['mission_array'][$row->clss][$row->id]['end_icao'] = $row->end_icao;
			$data['mission_array'][$row->clss][$row->id]['aircraft'] = $row->aircraft;
		}

		//set title
		$data['page_title'] = $data['division_array'][$selected_division_id]['longname'] . ' Missions';

		$this->view_fns->view('global/missions/missions_index', $data);
	}
}

/* End of file */