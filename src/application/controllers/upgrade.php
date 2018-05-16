<?php
 
class Upgrade extends CI_Controller {

	function Upgrade()
	{
		parent::__construct();
	}
	
	
	function test_cron(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->award_european_award();
	}
		
	function testpirep(){
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
		
		
		
		
		//test data *********************************************************************
		$validation = 1;
		//*******************************************************************************
		
		
		
		$post_username = '2131';
		$post_password = 'x';
		$post_aggregate_id = '201107091329452131';
		$post_timetable_id = '0';
		$post_assigned_id = '3630';
		$post_aircraft_id = '51';
		$post_origin = 'DAOO';
		$post_destination = 'LPPT';
		
		$post_propilot = '0';
		$post_propilot_score = '0';
		$post_aircraft_title = 'v4EHM-MD83';
		$post_flightnumber = '';
		$post_pax = '139';
		$post_cargo = '0';
		$post_engine_start_time = '2011-07-09 13:32:13';
		$post_engine_stop_time = '2011-07-09 15:07:15';
		$post_takeoff_time = '2011-07-09 13:42:08';
		$post_landing_time = '2011-07-09 15:00:46';
		$post_flight_minutes = '78';
		$post_blocktime_minutes = '94';
		
		$post_cruise_alt = '31000';
		$post_cruise_speed = '290';
		$post_fuel_burnt = '11824';
		$post_approach_type = '4';
		$post_network = '1';
		$post_gcd = '0';
		$post_comments = 'WEB page transation Failed';
		$post_fl_version = '4.1.4';
		$post_timegap_warning = '';
				

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
			if($pilot_rows > 0 
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
					$adjusted_propilot_score = $post_propilot_score / $four_hours * $post_blocktime_minutes;
					$adjusted_propilot_score = round($adjusted_propilot_score, 0);
					$pirep_data['pp_score_ng'] = $adjusted_propilot_score;
					
					$post_propilot_score = $adjusted_propilot_score;
					
					
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
	



	function test_ppevent(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		$post_user_id = '1406';
	
		$query = $this->db->query("	SELECT 
									pirep_assigned.id,
									pirep_assigned.start_icao,
									pirep_assigned.end_icao,
									pirep_assigned.gcd,
									pirep_assigned.aircraft_id,
									pirep_assigned.passengers,
									pirep_assigned.cargo,
									pirep_assigned.group_id,
									pirep_assigned.tour_id,
									pirep_assigned.tour_leg_id,
									pirep_assigned.event_id,
									pirep_assigned.event_leg_id,
									pirep_assigned.mission_id,
									pirep_assigned.fs_version,
									pirep_assigned.group_order,
									pirep_assigned.created,
									pirep_assigned.award_completion,
									pirep_assigned.award_id,
									tour_index.name as tour_name,
									mission_index.title as mission_name,
									propilot_event_legs.start_date as start_date,
									propilot_event_legs.end_date as end_date,
									pilots.pp_location
									
									
							FROM pirep_assigned
							
								LEFT JOIN tour_index
								ON tour_index.id = pirep_assigned.tour_id
								
								LEFT JOIN mission_index
								ON mission_index.id = pirep_assigned.mission_id
								
								LEFT JOIN propilot_event_legs
								ON propilot_event_legs.id = pirep_assigned.event_leg_id
								
								LEFT JOIN pilots 
								ON pirep_assigned.user_id = pilots.id
							
							WHERE pirep_assigned.user_id = '$post_user_id'
							AND pirep_assigned.event_id IS NOT NULL
							AND pirep_assigned.event_id != '0'
							
							ORDER BY pirep_assigned.event_leg_id, pirep_assigned.group_id, pirep_assigned.group_order ASC, pirep_assigned.created
							
							LIMIT 1
							
								");
								
						$propilot_flights =  $query->result_array();
						$propilot_num =  $query->num_rows();
					
                        
						if($propilot_num > 0){
						
							$current_time = time();
						
							//handle case where event dates have passed
							if(strtotime($propilot_flights['0']['start_date']) <= $current_time
							&&  strtotime($propilot_flights['0']['end_date']) > $current_time
							&& $propilot_flights['0']['start_icao'] == $propilot_flights['0']['pp_location']
							){
							
								echo 'Start Date '.strtotime($propilot_flights['0']['start_date']).'<br />';
								echo 'current_time '.$current_time.'<br />';
								echo 'End Date '.strtotime($propilot_flights['0']['end_date']).'<br />';
								
							
							}
							else{
								echo 'Failed Check<br />';
								echo 'Start Date '.strtotime($propilot_flights['0']['start_date']).'<br />';
								echo 'current_time '.$current_time.'<br />';
								echo 'End Date '.strtotime($propilot_flights['0']['end_date']).'<br />';
							}
						
						}
						else{
								echo '0 results returned';
						}
	
	}

	function test_uname(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
	
		$result = $this->auth_fns->generate_username();
		
		echo 'Result: '.$result;
		
		
		
	}

	function pp_locations(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
	
		//query to pull all aircraft and their location if exists
		$query = $this->db->query("	SELECT 	propilot_aircraft.id as id,
											dft_aircraft_on_airport.airport_icao,
											dft_aircraft_on_airport.since_when
											
									FROM propilot_aircraft
									
									LEFT JOIN dft_aircraft_on_airport
									ON dft_aircraft_on_airport.aircraft_unique_id = propilot_aircraft.id
									
											
										");
				
		$data_array =  $query->result();
		
		//iterate through each plane updating it's location
		
		foreach($data_array as $row){
		
			$loc = 'EGLL';
		
			if($row->airport_icao != ''){
				$loc = $row->airport_icao;
			}
		
			//array data for update
			$insert_data = array(
									'location' => $row->airport_icao,
									'last_flown' => $row->since_when,
									
									);
			
			
			//perform the update on db
			$this->db->where('id', $row->id);
			$this->db->update('propilot_aircraft', $this->db->escape($insert_data));
			
			echo 'Done';
		
		}
	
	
	}




	function test_tour_award(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Pirep_model');
		
	
		//data
		$pilot_id = '1406';
		$tour_id = 10;
		$award_id = 34;
		
		//call function
		$return_data = $this->Pirep_model->tour_award($pilot_id, $tour_id, $award_id, $gmt_mysql_datetime);
		
		echo 'return data: '.$return_data;
	
	}


	function migrate_missions(){
	//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		//query to get all missions without timetable data
		$query = $this->db->query("	SELECT 	mission_index.id as id,
											timetable.dep_airport,
											timetable.arr_airport,
											timetable.class as clss,
											timetable.dep_time,
											timetable.arr_time
											
									FROM mission_index
									
									LEFT JOIN timetable
									ON timetable.flightnumber = mission_index.flightnumber
									
									WHERE mission_index.end_icao IS NULL
									OR mission_index.start_icao IS NULL
									OR mission_index.dep_time IS NULL
									OR mission_index.arr_time IS NULL
									OR mission_index.class IS NULL
											
										");
				
		$data_array =  $query->result();
		
		foreach($data_array as $row){
			
			//array data for update
			$insert_data = array(
									'start_icao' => $row->dep_airport,
									'end_icao' => $row->arr_airport,
									'dep_time' => $row->dep_time,
									'arr_time' => $row->arr_time,
									'class' => $row->clss,
									
									);
			
			
			//perform the update on db
			$this->db->where('id', $row->id);
			$this->db->update('mission_index', $this->db->escape($insert_data));
		
		
		}
		
	
	}


	function set_pilot_ids_pirep(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		//query to get outstanding records
				$query = $this->db->query("	SELECT DISTINCT pirep.username as username,
											pilots.id as id
											
									FROM pirep
									
										LEFT JOIN pilots
										ON pilots.username = pirep.username
											
										");
				
				$pilots_array =  $query->result();
				
				foreach($pilots_array as $row){
				
					//array data for update
					$pilots_data = array(
									'user_id' => $row->id
									);
									
					if($row->id != ''){
					//perform the update on db
					$this->db->where('username', $row->username);
					$this->db->update('pirep', $this->db->escape($pilots_data));
					}
				}
				
				
				
				//echo updating
				$data['exception'] = '<h1>Updated: pirep</h1><a href="'.$data['base_url'].'upgrade/set_pilot_ids_pirep/award_log">Update: award_log</a>';
				//output confirmation page
				$data['page_title'] = 'convert pilot usernames to id';
				$data['no_links'] = '1';
				$this->view_fns->view('global/exception', $data);
		
		
	
	}
	
	
	function set_pilot_ids_propilot_aircraft_crash(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
				//query to get outstanding records
				$query = $this->db->query("	SELECT DISTINCT pilots.id as id,
											pilots.username as username
											
									FROM propilot_aircraft_crash
									
										LEFT JOIN pilots
										ON pilots.username = propilot_aircraft_crash.username
											
										");
				
				$pilots_array =  $query->result();
				
				foreach($pilots_array as $row){
				
					//array data for update
					$pilots_data = array(
									'user_id' => $row->id
									);
									
									//echo 'id: '.$row->id.' uname: '.$row->username.'<br />';
					if($row->id != ''){
					//perform the update on db
					$this->db->where('username', $row->username);
					$this->db->update('propilot_aircraft_crash', $this->db->escape($pilots_data));
					}
				}
				//echo updating
				$data['exception'] = '<h1>Updated: aircraft_crash</h1><a href="'.$data['base_url'].'upgrade/set_pilot_ids_pirep/award_log">Update: award_log</a>';
				//output confirmation page
				$data['page_title'] = 'convert pilot usernames to id';
				$data['no_links'] = '1';
				$this->view_fns->view('global/exception', $data);
		
		
	
	}


	function set_awards_id(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
				//query to get outstanding records
				$query = $this->db->query("	SELECT 
											awards_index.id as id,
											awards_index.awardtype
											
									FROM awards_index
											
										");
				
				$pilots_array =  $query->result();
				
				foreach($pilots_array as $row){
				
					//array data for update
					$pilots_data = array(
									'awards_index_id' => $row->id
									);
				
					if($row->id != ''){
					//perform the update on db
					$this->db->where('type', $row->awardtype);
					$this->db->update('awards_assigned', $this->db->escape($pilots_data));
					}
				}
				//echo updating
				$data['exception'] = '<h1>Updated: awards_assigned to insert award ids</h1>';
				//output confirmation page
				$data['page_title'] = 'convert pilot usernames to id';
				$data['no_links'] = '1';
				$this->view_fns->view('global/exception', $data);
		
		
	
	}

	
	function set_awards_award_log(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
				//query to get outstanding records
				$query = $this->db->query("	SELECT 
											pilots.id as id,
											pilots.username as username,
											award_log.awardtype,
											award_log.assigned_date
											
									FROM award_log
									
										LEFT JOIN pilots
										ON pilots.username = award_log.pilotid
											
										");
				
				$pilots_array =  $query->result();
				
				foreach($pilots_array as $row){
				
					//array data for update
					$pilots_data = array(
									'user_id' => $row->id,
									'assigned_date' => $row->assigned_date
									);
				
					if($row->id != ''){
					//perform the update on db
					$this->db->where('username', $row->username);
					$this->db->where('type', $row->awardtype);
					$this->db->update('awards_assigned', $this->db->escape($pilots_data));
					}
				}
				//echo updating
				$data['exception'] = '<h1>Updated: award_log</h1>';
				//output confirmation page
				$data['page_title'] = 'convert pilot usernames to id';
				$data['no_links'] = '1';
				$this->view_fns->view('global/exception', $data);
		
		
	
	}
	
	
	
	
	function set_pilot_ids_awards_assigned(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
				//query to get outstanding records
				$query = $this->db->query("	SELECT DISTINCT pilots.id as id,
											pilots.username as username
											
									FROM awards_assigned
									
										LEFT JOIN pilots
										ON pilots.username = awards_assigned.username
											
										");
				
				$pilots_array =  $query->result();
				
				foreach($pilots_array as $row){
				
					//array data for update
					$pilots_data = array(
									'user_id' => $row->id
									);
				
					if($row->id != ''){
					//perform the update on db
					$this->db->where('username', $row->username);
					$this->db->update('awards_assigned', $this->db->escape($pilots_data));
					}
				}
				//echo updating
				$data['exception'] = '<h1>Updated: awards_assigned</h1>';
				//output confirmation page
				$data['page_title'] = 'convert pilot usernames to id';
				$data['no_links'] = '1';
				$this->view_fns->view('global/exception', $data);
		
		
	
	}

	function pilots_hash_password(){
	
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		$this->load->library('Auth_fns');

			//grab pilots
			$query = $this->db->query("	SELECT 
											pilots.pilotname as pilotname,
											pilots.id as id,
											pilots.pilotpasw as pilotpasw
											
									FROM pilots
											
										");
				
			$list =  $query->result();
			$num_rows = $query->num_rows();
			
			$i = 0;
			foreach($list as $row){
			
			
				$hashed_password = $this->auth_fns->hash_password($row->id, $row->pilotpasw);
				
				$insert = array(
									'password' => $hashed_password,
									
				);
				
				//write split into db
				$this->db->where('id', $row->id);
				$this->db->update('pilots', $insert);
			$i++;
			}
				
			
				//echo updating
				$data['exception'] = '<h1>Updated: '.$i.' Remaining: '.($num_rows-$i).'</h1>';
				//output confirmation page
				$data['page_title'] = 'convert pilot usernames to id';
				$data['no_links'] = '1';
				$this->view_fns->view('global/exception', $data);
	
	
	}


	function pilots_split_name(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		
				//create an array for the country codes from location -> country
				//now grab all the countries
				$query = $this->db->query("	SELECT 	countries.Country as country,
													countries.Name as name,
													countries.alt_name as alt_name
													
											FROM countries								
													
											ORDER BY Name, Country
													
												");
						
				$country_list =  $query->result();
				
				$country_array = array('greatbritain' => 'GB', 'newzealand ' => 'NZ', 'papuanew' => 'PG');
				
				
				foreach($country_list as $row){
					
					
					if($row->alt_name != ''){
						
						$country_array[strtolower($row->alt_name)] = $row->country;
						$country_array[$row->alt_name] = $row->country;
					}
					else{
						$country_array[strtolower(str_replace (" ", "", $row->name))] = $row->country;
						$country_array[str_replace (" ", "", $row->name)] = $row->country;
					}
				}
				
				
				
		
				
				$query = $this->db->query("	SELECT 
											pilots.pilotname as pilotname,
											pilots.fname as fname,
											pilots.curr_location as curr_location,
											pilots.location as location,
											pilots.fsversion as fsversion,
											pilots.id as id,
											hub.hub_icao as hub
											
									FROM pilots
										
										LEFT JOIN hub
										ON hub.id = pilots.hub
											
										");
				
				$list =  $query->result();
				$num_rows = $query->num_rows();
				$error_list = '';
				$i = 0;
				foreach($list as $row){
				
					$proc_name = ucwords(strtolower($row->pilotname));
				
					//find where the first space occurs
					$pos = strpos($proc_name, " ");
					
					//grab the text up to that point
					$fname = substr($proc_name, 0, $pos);
					
					//count the firstname to include all the surname inc spaces eg 'de graf'
					
					$sname = substr($proc_name, ($pos + 1));
					
					if(array_key_exists($row->location,$country_array)){
						$count_val = $country_array[$row->location];
					}
					else{
						$error_list .= 'Key: "'.$row->location.'"<br />';
						$count_val = '';
					}
					
					$pp_loc = $row->curr_location;
					
					if($row->curr_location != ''){
					
						$pp_loc = $row->curr_location;
						$loc = $row->curr_location;
					
					}
					else{
					
						$pp_loc = $row->hub;
						$loc = $row->hub;
						
					}
					
					
					$insert = array(
										'fname' => $fname,
										'sname' => $sname,
										'pp_location' => $pp_loc,
										'curr_location' => $loc,
										'country' => $count_val,
					);
					
					//write split into db
					$this->db->where('id', $row->id);
					$this->db->update('pilots', $insert);
				$i++;
				}
				
				
				//now update all pilot ranks 7 to be rank 6
				
				//array data for update
					$pilots_data = array(
									'rank' => '6'
									);
									
					if($row->id != ''){
					//perform the update on db
					$this->db->where('rank', '7');
					$this->db->update('pilots', $this->db->escape($pilots_data));
					}
				
			
				//echo updating
				$data['exception'] = '<h1>Updated: '.$i.' Remaining: '.($num_rows-$i).'</h1>'.$error_list;
				//output confirmation page
				$data['page_title'] = 'convert pilot usernames to id';
				$data['no_links'] = '1';
				$this->view_fns->view('global/exception', $data);
		
		
	
	}





	function set_timetable_days(){
	
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		//query to get all timetables
		$query = $this->db->query("	SELECT 	timetable.id as id,
											timetable.days as days
											
									FROM timetable
									
									WHERE timetable.mon = '0'
									AND timetable.tue = '0'
									AND timetable.wed = '0'
									AND timetable.thu = '0'
									AND timetable.fri = '0'
									AND timetable.sat = '0'
									AND timetable.sun = '0'
																		
									ORDER BY timetable.id
											
										");
				
		$timetable_array =  $query->result();
		
		foreach($timetable_array as $row){
			
			//check the days to determine which days to write in
			if(strpos($row->days,'1') !== FALSE){
				$mon = '1';
			}
			else{
				$mon = '0';
			}
			
			if(strpos($row->days,'2') !== FALSE){
				$tue = '1';
			}
			else{
				$tue = '0';
			}
			
			if(strpos($row->days,'3') !== FALSE){
				$wed = '1';
			}
			else{
				$wed = '0';
			}
			
			if(strpos($row->days,'4') !== FALSE){
				$thu = '1';
			}
			else{
				$thu = '0';
			}
			
			if(strpos($row->days,'5') !== FALSE){
				$fri = '1';
			}
			else{
				$fri = '0';
			}
			if(strpos($row->days,'6') !== FALSE){
				$sat = '1';
			}
			else{
				$sat = '0';
			}
			if(strpos($row->days,'7') !== FALSE){
				$sun = '1';
			}
			else{
				$sun = '0';
			}
		
		
			//array data for update
			$timetable_data = array(
									'mon' => $mon,
									'tue' => $tue,
									'wed' => $wed,
									'thu' => $thu,
									'fri' => $fri,
									'sat' => $sat,
									'sun' => $sun,
									
									);
			
			
			//perform the update from db
			$this->db->where('id', $row->id);
			$this->db->update('timetable', $this->db->escape($timetable_data));
			
		}
	
	}

	
	function set_pirep_blocktimes(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		//query to get all pireps
		$query = $this->db->query("	SELECT 	pirep.id as id,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time
											
									FROM pirep
									
									WHERE pirep.blocktime_mins IS NULL
									
									ORDER BY pirep.id
											
										");
										
		$num_rows = $query->num_rows();
		
		//query to get all pireps
		$query = $this->db->query("	SELECT 	pirep.id as id,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time
											
									FROM pirep
									
									WHERE pirep.blocktime_mins IS NULL
									
									ORDER BY pirep.id
									
									LIMIT 10000
											
										");
				
		$pirep_array =  $query->result();
		$i = 0;
		foreach($pirep_array as $row){
			//calculate blocktime
			
			
			$blocktime_seconds = strtotime($row->engine_stop_time) - strtotime($row->engine_start_time);
			$blocktime_minutes = round(($blocktime_seconds / 60),0);
			
			//write blocktime into database
			$pirep_data['blocktime_mins'] = $blocktime_minutes;
			
			//perform the update from db
			$this->db->where('id', $row->id);
			$this->db->update('pirep', $this->db->escape($pirep_data));
		$i++;	
		}
		
		echo 'Done '.number_format($i).' iterations. '.number_format($num_rows-$i).' remaining';
		
	}
	
	function index()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		//perform checks, see if changes have been made
		
		//	>> Config settings
		
		//	>> Write access
		
		//	>> Database tables 
		
		/*
		$data['rank_array'] = array();
		
		//make database call to grab the aircraft data
		$query = $this->db->query("	SELECT 	ranks.id as id,
											ranks.rank as rank,
											ranks.name as name,
											ranks.hours as hours,
											ranks.stats_order as stats_order,
											ranks.class as clss
											
									FROM ranks
									
									WHERE ranks.id != '7'
									
									ORDER BY ranks.hours
											
										");
				
		$data['rank_array'] =  $query->result_array();
		*/
	
							
		$data['page_title'] = 'Upgrade';
		$data['no_links'] = '1';
		
		$this->view_fns->view('global/upgrade/upgrade_index', $data);
	}
	
	
}

/* End of file */