<?php
 
class Events extends CI_Controller {

	function Events()
	{
		parent::__construct();
	}
	
	
	function details($event_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Dispatch_model');
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');
		
		
		//redirect if event_id is not valid
		if($event_id == NULL || !is_numeric($event_id)){
			redirect('events/');
		}
		
		
		$data['event_id'] = $event_id;
		
		
		$current_pilot_username = $this->session->userdata('username');
		$current_pilot_user_id = $this->session->userdata('user_id');
		
		
		//make database call to grab the propilot event
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
											aircraft.name as aircraft,
											
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
										
										LEFT JOIN aircraft
										ON aircraft.id = propilot_event_index.aircraft_id
										
									WHERE propilot_event_index.id = '$event_id'
									
									ORDER BY propilot_event_legs.sequence
											
										");
											
				$result = $query->result_array();
				$num_results = $query->num_rows();
				
				$data['num_results'] = $num_results;
				
				$data['pp_event_id'] = '';
				$data['pp_event_name'] = '';
				$data['pp_event_start_date'] = '';
				$data['pp_event_description'] = '';	
				$data['pp_event_difficulty'] = '';
				$data['pp_aircraft'] = '';
				
				if($num_results > 0){
					$data['pp_event_id'] = $result[0]['id'];
					$data['pp_event_name'] = $result[0]['name'];
					$data['pp_event_start_date'] = $result[0]['end_date'];
					$data['pp_event_description'] = $result[0]['description'];
					$data['pp_event_difficulty'] = $result[0]['difficulty'];
					$data['pp_aircraft'] = $result[0]['aircraft'];
				}
				
				$data['flight_array'] = array();
				$i = 0;
				foreach($result as $row){
				
				
					$data['active'] = $row['active'];
					
					$flight_array[$row['sequence']]['leg_id'] = $row['leg_id'];
					$flight_array[$row['sequence']]['sequence'] = $row['sequence'];
					$flight_array[$row['sequence']]['start_icao'] = $row['start_icao'];
					$flight_array[$row['sequence']]['start_name'] = $row['start_name'];
					$flight_array[$row['sequence']]['end_icao'] = $row['end_icao'];
					$flight_array[$row['sequence']]['end_name'] = $row['end_name'];
					$flight_array[$row['sequence']]['award_id'] = $row['award_id'];
					$flight_array[$row['sequence']]['award_name'] = $row['award_name'];
					$flight_array[$row['sequence']]['aircraft_id'] = $row['aircraft_id'];
					
					
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
				
				
				
		
			//make database call to deterine if current pilot has the event assigned
			$query = $this->db->query("	SELECT 	pirep_assigned.id as id
											
											
									FROM pirep_assigned
										
										
									WHERE pirep_assigned.event_id = '$event_id'
									AND pirep_assigned.user_id = '$current_pilot_user_id'
									
									LIMIT 1
											
										");
											
				$result = $query->result_array();
				$num_results = $query->num_rows();
				
				$data['signed_up'] = 0;
				if($num_results > 0){
					$data['signed_up'] = 1;
				}
				
				
			
				
				
				
				//grab post data
				//grab post data
				$valid = $this->security->sanitize_filename($this->input->post('valid'));
				$signup = $this->security->sanitize_filename($this->input->post('signup'));
				
				//perform validation
				$this->form_validation->set_rules('valid', 'valid', 'required');
				$this->form_validation->set_rules('signup', 'signup', 'required');
				
				if($this->form_validation->run() == TRUE && $valid == 'true' && $signup == '1'){
					//if exists, assign all legs of event to pilot if they aren't already signed up
					
					$aircraft_data = $this->Dispatch_model->get_aircraft_array('ALL', '');
					$data['aircraft_array'] = $aircraft_data['aircraft_array'];
					$pax_array = $aircraft_data['pax_array'];
					$cargo_array = $aircraft_data['cargo_array'];
					
					
					$group_id = $gmt_mysql_datetime;
		
					
					foreach($flight_array as $row){
					
						$aircraft_id = $row['aircraft_id'];
					
						//calculate pax and cargo load for each leg
						if(array_key_exists($aircraft_id, $pax_array) && array_key_exists($aircraft_id, $cargo_array)){
							//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
							$loadout = $this->pirep_fns->get_loadout($pax_array[$aircraft_id], $cargo_array[$aircraft_id]);
							$num_pax = $loadout['pax'];
							$num_cargo = $loadout['cargo'];
						}
						else{
							$num_pax = 0;
							$num_cargo = 0;
						}
						
						if($row['award_id'] != ''){
							$award_completion = 1;
							$award_id = $row['award_id'];
						}
						else{
							$award_completion = 0;
							$award_id = NULL;
						}
					
						//array leg for insert into assigned flights
						//array data
							$pirep_assigned_data = array(
										'user_id' => $current_pilot_user_id,
										'start_icao' => $row['start_icao'],
										'end_icao' => $row['end_icao'],
										'gcd' => $row['gcd_nm'],
										'aircraft_id' => $aircraft_id,
										'passengers' => $num_pax,
										'cargo' => $num_cargo,
										'group_id' => $group_id,
										'group_order' => $row['sequence'],
										'created' => $gmt_mysql_datetime,
										'event_id' => $event_id,
										'event_leg_id' => $row['leg_id'],
										'award_completion' => $award_completion,
										'award_id' => $award_id,
							);
					
							//insert data
							$this->db->insert('pirep_assigned', $this->db->escape($pirep_assigned_data));
							
							//set status to signed up
							$data['signed_up'] = 1;
						
					}
					
					
					
				}
					
				
				//make database call to grab all pilots who have signed up
				$query = $this->db->query("	SELECT 	
											pilots.fname,
											pilots.sname,
											pilots.username,
											pilots.pp_location
											
											
									FROM pirep_assigned
										
										LEFT JOIN pilots
										ON pilots.id = pirep_assigned.user_id
										
									WHERE pirep_assigned.event_id = '$event_id'
									
									GROUP BY pirep_assigned.user_id
									
									ORDER BY sname, fname
											
										");
											
				$data['participants'] = $query->result_array();
				//$num_results = $query->num_rows();
				
		
			$data['flight_array'] = $flight_array;
		
		//set title
		$data['page_title'] = 'Propilot Event Details';
		
		$this->view_fns->view('global/events/events_description', $data);
	}	
	
	
	
	function index()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		
		//make database call to grab any upcoming propilot events
		$query = $this->db->query("	SELECT 	
												propilot_event_index.id,
												propilot_event_index.name,
												propilot_event_index.start_date
														
												FROM propilot_event_index
												
													LEFT JOIN propilot_event_legs
													ON propilot_event_legs.event_id = propilot_event_index.id
													
												WHERE propilot_event_legs.end_date >= now()
												AND propilot_event_index.active = '1'
												
												GROUP BY propilot_event_index.id
												
												ORDER BY propilot_event_legs.sequence DESC, propilot_event_index.start_date
											");
											
				$result = $query->result();
				$num_results = $query->num_rows();
				
				$data['num_results'] = $num_results;
				$data['result'] = $result;
		
		
		//set title
		$data['page_title'] = 'Propilot Events';
		
		$this->view_fns->view('global/events/events_index', $data);
	}
}

/* End of file */