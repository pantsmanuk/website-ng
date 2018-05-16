<?php

class Flogger_push extends CI_Controller {

	function Flogger_push()
	{
		parent::__construct();
	}


	function acars(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models


		//grab post
		$post_username = $this->security->sanitize_filename($this->input->post('username'));
		$post_password = $this->security->sanitize_filename($this->input->post('password'));

		//$post_timestamp = $this->security->sanitize_filename($this->input->post('timestamp'));

		$post_lat = $this->security->sanitize_filename($this->input->post('lat'));
		$post_lon = $this->security->sanitize_filename($this->input->post('lon'));
		$post_bearing = $this->security->sanitize_filename($this->input->post('bearing'));
		$post_altitude = $this->security->sanitize_filename($this->input->post('altitude'));
		$post_ias = $this->security->sanitize_filename($this->input->post('ias'));
		$post_fuel = $this->security->sanitize_filename($this->input->post('fuel'));
		$post_aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft'));

		$post_propilot = $this->security->sanitize_filename($this->input->post('propilot'));

		$post_aggregate_id = $this->security->sanitize_filename($this->input->post('flightId'));
		$post_origin = $this->security->sanitize_filename($this->input->post('origin'));
		$post_destination = $this->security->sanitize_filename($this->input->post('destination'));

		//perform validation
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->form_validation->set_rules('lat', 'lat', 'required');
		$this->form_validation->set_rules('lon', 'lon', 'required');
		$this->form_validation->set_rules('bearing', 'bearing', 'required');
		$this->form_validation->set_rules('altitude', 'altitude', 'required');
		$this->form_validation->set_rules('ias', 'ias', 'required');
		$this->form_validation->set_rules('fuel', 'fuel', 'required');
		$this->form_validation->set_rules('aircraft', 'aircraft', 'required');
		$this->form_validation->set_rules('propilot', 'propilot', 'required');

		//if validation fails (a flight id or assigned id must be supplied)
		if($this->form_validation->run() == FALSE){
			$validation = 0;
		}
		else{
			$validation = 1;
		}
		/*
		//test data ******************************************************************************************
		$post_username = '1997';
		$post_password = '';
		$post_lat = '5.345';
		$post_lon = '-10.345';
		$post_bearing = '180';

		$post_ias = '140';
		$post_fuel = '15000';
		$post_aircraft_id = '3';
		$post_propilot = '0';
		$validation = 1;
		//test data ******************************************************************************************
		*/

		//check auth and get user_id
		//check authentication passes
		if($post_username != '' && $post_password != '' && $validation === 1){

				$query = $this->db->query("	SELECT 	pilots.id as id,
													pilots.username as username,
													pilots.usergroup as usergroup,
													pilots.management_pips as pips,
													pilots.email_confirmed as email_confirmed,
													pilots.flighthours as flight_hours,
													pilots.flightmins as flight_mins,
													pilots.fname as fname,
													pilots.sname as sname,
													pilots.country as country,
													pilots.status as status,
													pilots.password as password,
													hub.hub_icao as hub,
													hub.id as hub_id,
													ranks.rank as rank,
													ranks.name as rank_name,
													ranks.id as rank_id,
													usergroup_index.admin_cp as admin_cp

											FROM pilots

												LEFT JOIN ranks
												ON ranks.id = pilots.rank

												LEFT JOIN hub
												on hub.id = pilots.hub

												LEFT JOIN usergroup_index
												ON usergroup_index.id = pilots.usergroup

											WHERE 	pilots.username = '$post_username'

											LIMIT 1

										");

				$result =  $query->result_array();
				$pilot_rows =  $query->num_rows();

				if($pilot_rows > 0){

					$pilot_id = $result['0']['id'];
					$status = $result['0']['status'];
					$email_confirmed = $result['0']['email_confirmed'];
					$hub_id = $result['0']['hub_id'];
					$user_id = $result['0']['username'];
					$password = $result['0']['password'];

					//see if any acars data for this pilot exists
					$query = $this->db->query("	SELECT 	acars.id as id

												FROM acars

												WHERE acars.user_id = '$pilot_id'
												");

					$acars_result = $query->result_array();
					$num_acars = $query->num_rows();

					if($num_acars > 1){
					//clear out any extra rows by this pilot
					$this->db->where('user_id', $pilot_id);
					$this->db->where('id !=', $acars_result['0']['id']);
					$this->db->delete('acars');
					}


					//array data
					$acars_data = array(
						'username' => $post_username,
						'user_id' => $pilot_id,
						'updated' => $gmt_mysql_datetime,
						'aggregate_id' => $post_aggregate_id,
						'origin' => $post_origin,
						'destination' => $post_destination,
						'lat' => $post_lat,
						'lon' => $post_lon,
						'bearing' => $post_bearing,
						'altitude' => $post_altitude,
						'ias' => $post_ias,
						'fuel' => $post_fuel,
						'aircraft' => $post_aircraft_id,
						'propilot_flight' => $post_propilot,
					);


					if($num_acars > 0){
						//update
						$this->db->where('id', $acars_result['0']['id']);
						$this->db->update('acars', $this->db->escape($acars_data));
					}
					else{
						//insert
						$this->db->insert('acars', $this->db->escape($acars_data));
					}



					echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
					//root element header
					echo '<response>'."\n";
					echo '	<header>'."\n";
					echo '		<timestamp>'.$gmt_mysql_datetime.'</timestamp>'."\n";
					echo '		<errcode>success</errcode>'."\n";
					echo '		<errmessage>ACARS successfully reported</errmessage>'."\n";
					echo '	</header>'."\n";
					echo '	<data />'."\n";
					echo '</response>'."\n";

				}
				else{

					echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
					//root element header
					echo '<response>'."\n";
					echo '	<header>'."\n";
					echo '		<timestamp>'.$gmt_mysql_datetime.'</timestamp>'."\n";
					echo '		<errcode>errAuth</errcode>'."\n";
					echo '		<errmessage>ACARS authentication failed</errmessage>'."\n";
					echo '	</header>'."\n";
					echo '	<data />'."\n";
					echo '</response>'."\n";

				}



		}
		else{
		//output fail return
		echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
		//root element header
		echo '<response>'."\n";
		echo '	<header>'."\n";
		echo '		<timestamp>'.$gmt_mysql_datetime.'</timestamp>'."\n";
		echo '		<errcode>errValidation</errcode>'."\n";
		echo '		<errmessage>ACARS validation failed</errmessage>'."\n";
		echo '	</header>'."\n";
		echo '	<data />'."\n";
		echo '</response>'."\n";
		}


	}


	function pirep(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('Pirep_fns');
		$this->load->library('Geocalc_fns');

		$this->load->model('Pirep_model');
		$this->load->model('Dispatch_model');


		//get post data
		$post_username = $this->security->sanitize_filename($this->input->post('username'));
		$post_password = $this->security->sanitize_filename($this->input->post('password'));
		$post_aggregate_id = $this->security->sanitize_filename($this->input->post('flightId'));
		$post_timetable_id = $this->security->sanitize_filename($this->input->post('timetableId'));
		$post_assigned_id = $this->security->sanitize_filename($this->input->post('assignedId'));
		$post_aircraft_id = $this->security->sanitize_filename($this->input->post('aircraftId'));
		$post_origin = $this->security->sanitize_filename($this->input->post('origin'));
		$post_destination = $this->security->sanitize_filename($this->input->post('destination'));

		$post_propilot = $this->security->sanitize_filename($this->input->post('propilot'));
		$post_propilot_score = $this->security->sanitize_filename($this->input->post('propilotScore'));
		$post_aircraft_title = $this->security->sanitize_filename($this->input->post('aircraftTitle'));
		$post_flightnumber = $this->security->sanitize_filename($this->input->post('flightNumber'));
		$post_pax = $this->security->sanitize_filename($this->input->post('pax'));
		$post_cargo = $this->security->sanitize_filename($this->input->post('cargo'));
		$post_engine_start_time = $this->security->sanitize_filename($this->input->post('startupTime'));
		$post_engine_stop_time = $this->security->sanitize_filename($this->input->post('shutdownTime'));
		$post_takeoff_time = $this->security->sanitize_filename($this->input->post('takeoffTime'));
		$post_landing_time = $this->security->sanitize_filename($this->input->post('landTime'));
		$post_flight_minutes = $this->security->sanitize_filename($this->input->post('flightMinutes'));
		$post_blocktime_minutes = $this->security->sanitize_filename($this->input->post('totalMinutes'));

		$post_cruise_alt = $this->security->sanitize_filename($this->input->post('cruiseAlt'));
		$post_cruise_speed = $this->security->sanitize_filename($this->input->post('cruiseSpeed'));
		$post_fuel_burnt = $this->security->sanitize_filename($this->input->post('fuelBurnt'));
		$post_approach_type = $this->security->sanitize_filename($this->input->post('approachType'));
		$post_network = $this->security->sanitize_filename($this->input->post('network'));
		$post_gcd = $this->security->sanitize_filename($this->input->post('gcd'));
		$post_comments = $this->security->sanitize_filename($this->input->post('comments'));
		$post_fl_version = $this->security->sanitize_filename($this->input->post('floggerVersion'));

		//warnings and errors
		$post_timegap_warning = $this->security->sanitize_filename($this->input->post('timeGapWarning'));


		//perform validation
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->form_validation->set_rules('flightId', 'flightId', 'required');
		$this->form_validation->set_rules('aircraftId', 'aircraftId', 'required');
		$this->form_validation->set_rules('origin', 'origin', 'required');
		$this->form_validation->set_rules('destination', 'destination', 'required');
		$this->form_validation->set_rules('propilot', 'propilot', 'required');
		$this->form_validation->set_rules('propilotScore', 'propilotScore', 'required');
		//$this->form_validation->set_rules('aircraftTitle', 'aircraftTitle', 'required');
		$this->form_validation->set_rules('pax', 'pax', 'required');
		$this->form_validation->set_rules('cargo', 'cargo', 'required');
		$this->form_validation->set_rules('startupTime', 'startupTime', 'required');
		$this->form_validation->set_rules('shutdownTime', 'shutdownTime', 'required');
		$this->form_validation->set_rules('takeoffTime', 'takeoffTime', 'required');
		$this->form_validation->set_rules('landTime', 'landTime', 'required');
		$this->form_validation->set_rules('flightMinutes', 'flightMinutes', 'required');
		$this->form_validation->set_rules('totalMinutes', 'totalMinutes', 'required');
		$this->form_validation->set_rules('cruiseAlt', 'cruiseAlt', 'required');
		$this->form_validation->set_rules('cruiseSpeed', 'cruiseSpeed', 'required');
		$this->form_validation->set_rules('fuelBurnt', 'fuelBurnt', 'required');
		$this->form_validation->set_rules('approachType', 'approachType', 'required');
		$this->form_validation->set_rules('network', 'network', 'required');



		//test normal flight --- passed
		//test assigned flight --- passed
		//test propilot flight with deadhead --- passed
		/*
		//test data *********************************************************************
		$post_username = '1997';
		$post_password = '';
		$post_aggregate_id = '201104041110241997';
		$post_timetable_id = '0';
		$post_assigned_id = '18';
		$post_aircraft_id = '0';
		$post_origin = 'DAAG';
		$post_destination = 'EGLL';
		$post_propilot = '1';
		$post_propilot_score = '50';
		$post_aircraft_title = 'Feelthere A330 RR Airbus';
		$post_flightnumber = '';
		$post_pax = '328';
		$post_cargo = '0';
		$post_engine_start_time = '2011-04-04 11:12:18';
		$post_engine_stop_time = '2011-04-04 13:50:44';
		$post_takeoff_time = '2011-04-04 11:14:22';
		$post_landing_time = '2011-04-04 13:46:26';
		$post_flight_minutes = '152';
		$post_blocktime_minutes = '158';
		$post_cruise_alt = '36000';
		$post_cruise_speed = '280';
		$post_fuel_burnt = '29313';
		$post_approach_type = '3';
		$post_network = '0';
		$post_gcd = '900';
		$post_comments = '';
		$post_fl_version = '4.1.0';
		*/

		//*******************************************************************************


		//if validation fails (a flight id or assigned id must be supplied)
		if($this->form_validation->run() == FALSE){
			$validation = 0;
		}
		else{
			$validation = 1;
		}

		//test data *********************************************************************
		//$validation = 1;
		//*******************************************************************************

		//write log file
		$myFile = $data['base_path']."assets/uploads/tmp/flogger_pirep_log.txt";
		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "==============================================="."\n";
		$stringData .= "timestamp: ".$gmt_mysql_datetime."\n";
		$stringData .= "==============================================="."\n";
		$stringData .= "validation: ".$validation."\n";
		$stringData .= "post_username: ".$post_username."\n";
		//$stringData .= "post_password: ".$post_password."\n";
		$stringData .= "post_password: NOT LOGGED"."\n";
		$stringData .= "post_aggregate_id: ".$post_aggregate_id."\n";
		$stringData .= "post_timetable_id: ".$post_timetable_id."\n";
		$stringData .= "post_aircraft_id: ".$post_aircraft_id."\n";
		$stringData .= "post_assigned_id: ".$post_assigned_id."\n";
		$stringData .= "post_origin: ".$post_origin."\n";
		$stringData .= "post_destination: ".$post_destination."\n";
		$stringData .= "post_propilot: ".$post_propilot."\n";
		$stringData .= "post_propilot_score: ".$post_propilot_score."\n";
		$stringData .= "post_aircraft_title: ".$post_aircraft_title."\n";
		$stringData .= "post_flightnumber: ".$post_flightnumber."\n";
		$stringData .= "post_pax: ".$post_pax."\n";
		$stringData .= "post_cargo: ".$post_cargo."\n";
		$stringData .= "post_engine_start_time: ".$post_engine_start_time."\n";
		$stringData .= "post_takeoff_time: ".$post_takeoff_time."\n";
		$stringData .= "post_landing_time: ".$post_landing_time."\n";
		$stringData .= "post_engine_stop_time: ".$post_engine_stop_time."\n";
		$stringData .= "post_flight_minutes: ".$post_flight_minutes."\n";
		$stringData .= "post_blocktime_minutes: ".$post_blocktime_minutes."\n";
		$stringData .= "post_cruise_alt: ".$post_cruise_alt."\n";
		$stringData .= "post_cruise_speed: ".$post_cruise_speed."\n";
		$stringData .= "post_fuel_burnt: ".$post_fuel_burnt."\n";
		$stringData .= "post_approach_type: ".$post_approach_type."\n";
		$stringData .= "post_network: ".$post_network."\n";
		$stringData .= "post_gcd: ".$post_gcd."\n";
		$stringData .= "post_comments: ".$post_comments."\n";
		$stringData .= "post_fl_version: ".$post_fl_version."\n";
		$stringData .= "post_timegap_warning: ".$post_timegap_warning."\n";
		$stringData .= "==============================================="."\n";
		$stringData .= "\n";
		fwrite($fh, $stringData);
		fclose($fh);

		$status = '';
		$email_confirmed = '';
		$hub_id = '';
		$user_id = '';

		if($post_propilot_score == ''){ $post_propilot_score = NULL; }

		//check authentication passes
		if($post_username != '' && $post_password != '' && $validation === 1){

				$query = $this->db->query("	SELECT 	pilots.id as id,
													pilots.username as username,
													pilots.rank as pilot_rank,
													pilots.usergroup as usergroup,
													pilots.management_pips as pips,
													pilots.email_confirmed as email_confirmed,
													pilots.flighthours as flight_hours,
													pilots.flightmins as flight_mins,
													pilots.fname as fname,
													pilots.sname as sname,
													pilots.country as country,
													pilots.status as status,
													pilots.password as password,
													hub.hub_icao as hub,
													hub.id as hub_id,
													ranks.rank as rank,
													ranks.name as rank_name,
													ranks.id as rank_id,
													usergroup_index.admin_cp as admin_cp

											FROM pilots

												LEFT JOIN ranks
												ON ranks.id = pilots.rank

												LEFT JOIN hub
												on hub.id = pilots.hub

												LEFT JOIN usergroup_index
												ON usergroup_index.id = pilots.usergroup

											WHERE 	pilots.username = '$post_username'

											LIMIT 1

										");

				$result =  $query->result_array();
				$pilot_rows =  $query->num_rows();

				$pilot_id = $result['0']['id'];
				$status = $result['0']['status'];
				$email_confirmed = $result['0']['email_confirmed'];
				$hub_id = $result['0']['hub_id'];
				$user_id = $result['0']['username'];
				$password = $result['0']['password'];
				$pilot_rank = $result['0']['pilot_rank'];

				$flightmins = ($result['0']['flight_hours']*60) + $result['0']['flight_mins'];

			}
			else{
				$pilot_rows = 0;

				$pilot_id = '';
				$status = '';
				$email_confirmed = '';
				$hub_id = '';
				$user_id = '';
				$password = '';
			}

			//check flight hasn't already been submitted
			$query = $this->db->query("	SELECT 	pirep.id as id,
												pirep.aggregate_id as aggregate_id

											FROM pirep

											WHERE 	(pirep.aggregate_id = '".$post_aggregate_id."'
													AND pirep.aggregate_id != '')

											OR 		(pirep.submitdate = '$gmt_mysql_datetime'
													AND pirep.user_id = '$pilot_id'
													AND pirep.start_icao = '$post_origin'
													AND pirep.end_icao = '$post_destination'
													)

											LIMIT 1

										");

			$aggregate_result =  $query->result_array();
			$aggregate_rows =  $query->num_rows();




			//if we got a hit back on the username, check the password and user status
			if($pilot_rows > 0 && $password == $this->auth_fns->hash_password($pilot_id, $post_password)
				&& $status != '5' && $status != '4' && $email_confirmed == '1'
				//don't process if we have an actual duplicate submission, but allow through a duplicate aggregate id until a client side fix is implemented.
				&& ($aggregate_rows < 1 || $aggregate_rows > 0 && $aggregate_result['0']['aggregate_id'] == $post_aggregate_id)
			){

				//calculate pausetime
				$enginestart_hh = gmdate('H',strtotime($post_engine_start_time));
				$enginestart_mm = gmdate('i',strtotime($post_engine_start_time));

				$engineoff_hh = gmdate('H',strtotime($post_engine_stop_time));
				$engineoff_mm = gmdate('i',strtotime($post_engine_stop_time));

				$blocktime_mins = $this->pirep_fns->calculate_blocktime_minutes($enginestart_hh, $enginestart_mm, $engineoff_hh, $engineoff_mm);
				$pausetime_mins = $blocktime_mins - $post_blocktime_minutes;


				//if no passenger and cargo data is returned, catch it and generate plausible data (in future handle propilot and exclude from this)
				if($post_pax == '' && $post_cargo == '' || $post_pax == '0' && $post_cargo == ''){

					$division = 'ALL';
					$aircraft_data = $this->Dispatch_model->get_aircraft_array($division);
					$pax_array = $aircraft_data['pax_array'];
					$cargo_array = $aircraft_data['cargo_array'];
					//if a passenger flight
					if(array_key_exists($post_aircraft_id, $pax_array)){
						//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
						$loadout = $this->pirep_fns->get_loadout($pax_array[$post_aircraft_id], $cargo_array[$post_aircraft_id]);
						if($post_pax == ''){
							$post_pax = $loadout['pax'];
						}
						$post_cargo = $loadout['cargo'];
					}
					else{
						$post_pax = 0;
						$post_cargo = 0;
					}

				}


				//determine if this flight is propilot, assigned or timetable

				//If we are an assigned flight, grab extra information
				if($post_assigned_id != '' && is_numeric($post_assigned_id)){

					//grab data from assigned flight
					$query = $this->db->query("	SELECT
											pirep_assigned.id as id,
											pirep_assigned.user_id as user_id,
											aircraft.name as aircraft,
											aircraft.id as aircraft_id,
											pirep_assigned.passengers as passengers,
											pirep_assigned.cargo as cargo,
											pirep_assigned.dep_time as dep_time,
											pirep_assigned.start_icao as start_icao,
											pirep_assigned.end_icao as end_icao,
											pirep_assigned.created as created,
											pirep_assigned.award_completion as award_completion,
											pirep_assigned.award_id as award_id,
											pirep_assigned.tour_id as tour_id,
											pirep_assigned.tour_leg_id as tour_leg_id,
											pirep_assigned.event_id as event_id,
											pirep_assigned.event_leg_id as event_leg_id,
											pirep_assigned.mission_id as mission_id,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name

									FROM pirep_assigned

										LEFT JOIN aircraft
										ON aircraft.id = pirep_assigned.aircraft_id

										LEFT JOIN airports as dep_icao
										ON dep_icao.ICAO = pirep_assigned.start_icao

										LEFT JOIN airports as arr_icao
										ON arr_icao.ICAO = pirep_assigned.end_icao

									WHERE pirep_assigned.id = '$post_assigned_id'
									AND pirep_assigned.user_id = '$pilot_id'

									ORDER BY pirep_assigned.created
									LIMIT 1

										");
					$result = $query->result_array();
					$num_results = $query->num_rows();
					$valid_id = $num_results;

					if($valid_id > 0){
						//overwrite the post data with assigned flight data
						$award_completion = $result['0']['award_completion'];
						$award_id = $result['0']['award_id'];
						if($award_id == ''){ $data['award_id'] = NULL; }
						$aircraft_id = $result['0']['aircraft_id'];
						$passengers = $result['0']['passengers'];
						$post_cargo = $result['0']['cargo'];
						$start_icao = $result['0']['start_icao'];
						$dep_name = $result['0']['dep_name'];
						$end_icao = $result['0']['end_icao'];
						$arr_name = $result['0']['arr_name'];
						$assigned = $num_results;

						$award_completion = $result['0']['award_completion'];
						$award_id = $result['0']['award_id'];
						$tour_id = $result['0']['tour_id'];
						$tour_leg_id = $result['0']['tour_leg_id'];
						$mission_id = $result['0']['mission_id'];
						$event_id = $result['0']['event_id'];
						$event_leg_id = $result['0']['event_leg_id'];

					}
					else{

					//set values to post
					$tour_id = NULL;
					$tour_leg_id = NULL;
					$mission_id = NULL;
					$award_id = NULL;
					$event_id = NULL;
					$event_leg_id = NULL;

					}



				}
				//if not assigned flight
				else{

					//set values to post
					$tour_id = NULL;
					$tour_leg_id = NULL;
					$mission_id = NULL;
					$award_id = NULL;
					$event_id = NULL;
					$event_leg_id = NULL;

				}


				//if we are timetable just insert.

				//if propilot handle both locked flights and events. insert is the same as for a normal flight



				//if ok, perform insert of pirep

				$date_now = date('Y-m-d', time());

				if($tour_id == ''){ $tour_id = NULL; }
				if($tour_leg_id == ''){ $tour_leg_id = NULL; }
				if($mission_id == ''){ $mission_id = NULL; }
				if($award_id == ''){ $award_id = NULL; }
				if($event_id == ''){ $event_id = NULL; }

				//pirep table
				$pirep_data = array(
								'username' => $post_username,
								'user_id' => $pilot_id,
								'hub' =>  $hub_id,
								'aircraft' => $post_aircraft_id,
								'onoffline' =>  $post_network,
								'flightnumber' =>  $post_flightnumber,
								'start_icao' =>  $post_origin,
								'end_icao' =>  $post_destination,
								'passengers' =>  $post_pax,
								'cargo' =>  $post_cargo,
								'cruisealt' =>  $post_cruise_alt,
								'cruisespd' =>  $post_cruise_speed,
								'approach' =>  $post_approach_type,
								'fuelburnt' =>  $post_fuel_burnt,
								'comments' =>  $post_comments,
								'submitdate' => $gmt_mysql_datetime,
								'last_updated' => $gmt_mysql_datetime,
								'checked' =>  '1',
								'engine_start_time' =>  $post_engine_start_time,
								'engine_stop_time' =>  $post_engine_stop_time,
								'departure_time' =>  $post_takeoff_time,
								'landing_time' =>  $post_landing_time,
								'blocktime_mins' => $post_blocktime_minutes,
								'pausetime_mins' => $pausetime_mins,
								'comments_mt' =>  'Flogger submission',
								'archived' =>  '0',
								'circular_distance' =>  $post_gcd,
								'from_fl' =>  '1',
								'act_different' =>  '0',
								'fl_version' =>  $post_fl_version,
								'pp_score_ng' =>  $post_propilot_score,
								'aircraft_tech_name' =>  $post_aircraft_title,
								'propilot_flight' =>  $post_propilot,
								'tour_id' => $tour_id,
								'tour_leg_id' => $tour_leg_id,
								'mission_id' => $mission_id,
								'award_id' => $award_id,
								'event_id' => $event_id,
								'event_leg_id' => $event_leg_id,
								'aggregate_id' => $post_aggregate_id,
				);

				//in case of tamper warning
				if(!empty($post_timegap_warning)){
					$pirep_data['checked'] = '0';
					$pirep_data['comments'] = 'Warning: '.$post_timegap_warning."  \n\n".$post_comments."  \n\n";
				}

				if($aggregate_rows > 0){
					$pirep_data['checked'] = '0';
					$pirep_data['comments'] = "Warning: Duplicate aggregate id, verify if flight previously submitted.  \n\n".$pirep_data['comments']."  \n\n";
				}

				if($post_propilot == '1'){

					//handle adjustment of points based on time

					//mid point is a 4 hour flight for the 50 points.
					$four_hours = 60*4;

					//multiply by ratio of duration vs optimal point.
					if($post_fl_version == '4.1.5' || $post_fl_version == '4.1.4' || $post_fl_version == ''){
						$adjusted_propilot_score = $post_propilot_score / $four_hours * $post_blocktime_minutes;
						$adjusted_propilot_score = round($adjusted_propilot_score, 0);
						$pirep_data['pp_score_ng'] = $adjusted_propilot_score;
						$post_propilot_score = $adjusted_propilot_score;
					}




				}


				//perform pirep insert
				$this->db->insert('pirep', $this->db->escape($pirep_data));


				//update pilot table
				$pilot_data = array(
					//'flighthours' => $new_hours, don't update hours until approved
					//'flightmins' => $new_mins,
					'status' => '0',
					'lastactive' => $gmt_mysql_datetime,
					'lastflight' => $date_now,
					'curr_location'=> $post_destination,
				);

				if($post_propilot == '1'){
					$pilot_data['pp_location'] = $post_destination;
					$pilot_data['pp_lastflight'] = $gmt_mysql_datetime;


				}

				$this->db->where('id', $pilot_id);
				$this->db->update('pilots', $this->db->escape($pilot_data));

				//if propilot
				if($post_propilot == '1'){

					//clear the aircraft reservation and move it to destination
					$propilot_aircraft_data = array(
													'reserved' => NULL,
													'reserved_by' => NULL,
													'location' => $post_destination,
													'destination' => NULL,
													'pax' => NULL,
													'cargo' => NULL,
													'gcd' => NULL,
													'last_flown' => $gmt_mysql_datetime,
					);

					//perform the update from db
					$this->db->where('reserved_by', $pilot_id);
					$this->db->where('location', $post_origin);
					$this->db->where('destination', $post_destination);
					$this->db->where('aircraft_id', $post_aircraft_id);
					$this->db->update('propilot_aircraft', $propilot_aircraft_data);

					//move any 'travel pilots from origin to this destination.
					$shunt = $this->Pirep_model->deadhead_pilots($post_origin, $post_destination);


				}

				//if the flight was 'assigned', remove assigned pirep (this handles propilot events as well).
				if($post_assigned_id != '' && is_numeric($post_assigned_id)){
					//now delete the assigned flight
					$this->db->where('id', $post_assigned_id);
					$this->db->delete('pirep_assigned');
				}



				//handle promotion
				$promoted = $this->Pirep_model->update_hours($pilot_id, $pilot_rank);
				$rank = $this->session->userdata('rank_short');


				$pp_score = $post_propilot_score;

				if(!is_numeric($pp_score) || $pp_score == ''){
					$pp_score = 0;
				}

				$flightmins = $flightmins + $post_blocktime_minutes;

				//fire award scripts
				//call award function and pass the tour_id/mission_id
				if($tour_id != ''){
					$tour_award_return = $this->Pirep_model->tour_award($pilot_id, $tour_id, $award_id, $gmt_mysql_datetime);
				}

				if($mission_id != ''){
					$mission_award_return = $this->Pirep_model->mission_award($pilot_id, $mission_id);
				}

				if($event_id != '' && $award_id != ''){
					$event_award_return = $this->Pirep_model->event_award($pilot_id, $event_id, $award_id, $gmt_mysql_datetime);
				}


				//handle fuel burn tables for each aircraft using gcd and fuel burn


					if($post_fuel_burnt > 0 && is_numeric($post_aircraft_id)){

						//calculate gcd

						//grab lat and lon for origin and destination
						$query = $this->db->query("	SELECT
											airports_data.ICAO as icao,
											airports_data.lat as lat,
											airports_data.long as lon

									FROM airports_data

									WHERE airports_data.ICAO = '$post_origin'
									OR airports_data.ICAO = '$post_destination'

										");
						$result = $query->result();
						$num_latlon = $query->num_rows();

						if($num_latlon >= 2){

							$dep_lat = '-';
							$dep_lon = '-';
							$arr_lat = '-';
							$arr_lon = '-';

							foreach($result as $row){
								if($row->icao == $post_origin){
									$dep_lat = $row->lat;
									$dep_lon = $row->lon;
								}

								if($row->icao == $post_destination){
									$arr_lat = $row->lat;
									$arr_lon = $row->lon;
								}
							}

							if($dep_lat != '-' && $dep_lon != '-' && $arr_lat != '-' && $arr_lon != '-'){
								$gcd_km = $this->geocalc_fns->GCDistance($dep_lat, $dep_lon, $arr_lat, $arr_lon);
								$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);


								//array data for insert
								$fuel_data = array(
															'aircraft_id' => $post_aircraft_id,
															'pilot_id' => $pilot_id,
															'pilot_username' => $post_username,
															'aircraft_title' => $post_aircraft_title,
															'gcd' => $gcd_nm,
															'fuel_burnt' => $post_fuel_burnt,
															'duration' => $post_blocktime_minutes,
															'cruise_alt' => $post_cruise_alt,
															'cruise_spd' => $post_cruise_speed,
															'propilot' => $post_propilot,
								);

								//insert
								$this->db->insert('fuel_burn', $this->db->escape($fuel_data));
							}
						}


					}


				//return xml
				//output response
				echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
				//root element header
				echo '<response>'."\n";
				echo '	<header>'."\n";
				echo '		<timestamp>'.$gmt_mysql_datetime.'</timestamp>'."\n";
				echo '		<errcode>success</errcode>'."\n";
				echo '		<errmessage>Pirep successfully reported</errmessage>'."\n";
				echo '	</header>'."\n";
				echo '	<data>'."\n";
				echo '	<account-info>'."\n";
				echo '		<total-time>'.$flightmins.'</total-time>'."\n";
				echo '		<propilot-score>'.$pp_score.'</propilot-score>'."\n";
				echo '		<new-rank>'.$rank.'</new-rank>'."\n";
				echo '	</account-info>'."\n";
				echo '	</data>'."\n";
				echo '</response>'."\n";



			}
			else{
				//output response
				echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
				//root element header
				echo '<response>'."\n";
				echo '	<header>'."\n";
				echo '		<timestamp>'.$gmt_mysql_datetime.'</timestamp>'."\n";
				//handle return codes
				if($aggregate_rows > 0){
					//flight already posted
					echo '		<errcode>errExists</errcode>'."\n";
					echo '		<errmessage>Flight has already been submitted</errmessage>'."\n";
				}
				elseif($validation != 1){
					//validation failed
					echo '		<errcode>errValidation</errcode>'."\n";
					echo '		<errmessage>Validation failed</errmessage>'."\n";
				}
				elseif($status == '4'){
					//account frozen
					echo '		<errcode>authDenied</errcode>'."\n";
					echo '		<errmessage>User is currently frozen</errmessage>'."\n";
				}
				elseif($status == '5'){
					//banned
					echo '		<errcode>authDenied</errcode>'."\n";
					echo '		<errmessage>User is currently banned</errmessage>'."\n";
				}
				elseif($email_confirmed != '1' && $pilot_rows > 0){
					//email not confirmed
					echo '		<errcode>authDenied</errcode>'."\n";
					echo '		<errmessage>User\'s email is not confirmed</errmessage>'."\n";
				}
				else{
					//failed to authenticate
					echo '		<errcode>authFail</errcode>'."\n";
					echo '		<errmessage>Username or Password incorrect</errmessage>'."\n";
				}
				echo '	</header>'."\n";
				echo '	<data />'."\n";
				echo '</response>'."\n";
			}
















	}

}