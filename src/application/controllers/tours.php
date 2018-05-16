<?php
 
class Tours extends CI_Controller {

	function Tours()
	{
		parent::__construct();	
	}
	
	function index()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		
		//sql query to grab all the tours
		$query = $this->db->query("	SELECT 	tour_index.id as id,
											tour_index.name as name,
											tour_index.author as author,
											tour_index.length as length,
											tour_index.difficulty as difficulty,
											tour_index.description as description,
											ranks.name as rank_name,
											ranks.id as rank_id
											
									FROM tour_index
									
										LEFT JOIN ranks
										ON ranks.class = tour_index.class
										
									WHERE tour_index.enabled = '1'
									
									ORDER BY tour_index.class ASC, tour_index.id ASC
											
										");
				
		$data['tour_array'] =  $query->result();
		
		
		$data['page_title'] = 'Aeroclub Tours';
		$data['no_links'] = '1';
	
		$this->view_fns->view('global/tours/tours_index', $data);
	}
	
	
	
	
	
	function details($tour_id = NULL, $selected_version = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		$this->load->model('Dispatch_model');
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');
		
		//redirect if $tour_id isn't valid
		if($tour_id == NULL || !is_numeric($tour_id)){
			redirect('tours');
		}
				
		$data['selected_version'] = $selected_version;
		$data['exception'] = '';
		$pilot_rank = $this->session->userdata('rank_id');
		$data['pilot_rank'] = $pilot_rank;
		
		//create aircraft array
		//$division = '5,8';
		$limit = $this->session->userdata('rank_id')+1;
		
		$aircraft_data = $this->Dispatch_model->get_aircraft_array('ALL', $limit);
		//$data['aircraft_array'] = $aircraft_data['aircraft_array'];
		$pax_array = $aircraft_data['pax_array'];
		$cargo_array = $aircraft_data['cargo_array'];
		
		//select allowed aircraft
		$query = $this->db->query("	SELECT 	
												aircraft.id as id,
												aircraft.name as name
																										
												FROM tour_aircraft
													
													LEFT JOIN aircraft
													ON aircraft.id = tour_aircraft.aircraft_id
													
													LEFT JOIN divisions
													ON aircraft.division = divisions.id
													
												WHERE tour_aircraft.tour_id = '$tour_id'
												ORDER BY aircraft.clss, aircraft.name
												
											");
											
			$ac_result = $query->result();
			
			$data['aircraft_array'] = array();
			
			foreach($ac_result as $row){
			
				$data['aircraft_array'][$row->id] = $row->name;
			
			}
			
		
		
		
		//sql query to grab all the tour data
		$query = $this->db->query("SELECT 	tour_index.id as id,
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
										
										LEFT JOIN flight_sim_versions
										ON flight_sim_versions.id = tour_legs.flight_sim
										
										LEFT JOIN airports_data as start_data
										ON start_data.icao = tour_legs.start_icao
										
										LEFT JOIN airports_data as end_data
										ON end_data.icao = tour_legs.end_icao
										
										
									WHERE tour_index.id = '$tour_id'
									AND tour_index.enabled = '1'
									
									ORDER BY tour_legs.flight_sim, tour_legs.sequence
											
										");

				
		$tour_data =  $query->result_array();
		$num_rows = $query->num_rows();
		
		if($num_rows < 1){
			redirect('tours');
		}
		else{
			
			
			
			//assign into groups of flight sim versions
			$flight_array = array();
			$versions = array('' => 'Generic');
			$initial_version = '';
			
			
			$i = 0;
			foreach($tour_data as $row){
				//ensure that selected version is set
				if($i == 0){
					$initial_version = $row['flight_sim_id'];
				}
				
				
				//create versions array
				if($row['flight_sim_id'] != ''){
					//array for menu
					$versions[$row['flight_sim_id']] = $row['version_name'];
				}
				
			$i++;
			}
			
			
			//handle cases where $selected_version not in array
			
			if(!array_key_exists($selected_version, $versions) && count($versions) > 0){	
				$selected_version = $initial_version;
			}
			
			
			$i = 0;
			foreach($tour_data as $row){
				
				//initialise array
				if(!array_key_exists($row['flight_sim_id'], $flight_array)){
					$flight_array[$row['flight_sim_id']] = array();
				}
				
				
				//use this version of the leg if we have this selected or if default and not already written to
				if($row['flight_sim_id'] == '' && !array_key_exists($row['sequence'], $flight_array)
				|| $selected_version == $row['flight_sim_id']){
					
					$flight_array[$row['sequence']]['leg_id'] = $row['leg_id'];
					$flight_array[$row['sequence']]['sequence'] = $row['sequence'];
					$flight_array[$row['sequence']]['start_icao'] = $row['start_icao'];
					$flight_array[$row['sequence']]['start_name'] = $row['start_name'];
					$flight_array[$row['sequence']]['end_icao'] = $row['end_icao'];
					$flight_array[$row['sequence']]['end_name'] = $row['end_name'];
					$flight_array[$row['sequence']]['altitude'] = $row['altitude'];
					$flight_array[$row['sequence']]['award_id'] = $row['award_id'];
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
					
					
				}
				
				
			$i++;
			}
			
			$data['flight_array'] = $flight_array;
			$data['versions'] = $versions;
			
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			//$tour_id = $this->security->sanitize_filename($this->input->post('tour_id'));
			//$fs_version = $this->security->sanitize_filename($this->input->post('fs_version'));
			$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));
					
			//if valid, array and write then redirect
			if($valid == 'true' && $aircraft_id != '' && $tour_id != ''){
			
				$current_user_id = $this->session->userdata('user_id');
				
				$logged_in = $this->session->userdata('logged_in');
				
				if($logged_in == '1'){
				
				//first check that pilot does not already have a tour route assignment
				$query = $this->db->query("	SELECT 	
												pirep_assigned.id as id
				
											FROM pirep_assigned
											
											WHERE pirep_assigned.tour_id IS NOT NULL
											AND pirep_assigned.user_id = '$current_user_id'
											
											");
											
				$num_rows = $query->num_rows();
				
				}
				else{
				
					$num_rows = 0;
				
				}
				
				//if ok and the pilot is of the right rank, continue, else list exception
				$rank_id = $tour_data['0']['rank_id'];
				if($logged_in == '1' && $num_rows == 0 && $pilot_rank >= $rank_id){
					
					
					$group_id = $gmt_mysql_datetime;
					
					//iterate through the flight array and write each leg into the database
					foreach($data['flight_array'] as $row){
						if(array_key_exists('sequence', $row)){
							
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
							
							$user_id = $this->session->userdata('user_id');
							
							if($selected_version == '' || $selected_version == 0){
								$selected_version = NULL;	
							}
							
							//echo 'Tour_id: '.$tour_id.'<br />';
							//echo 'Selected_version: '.$selected_version.'<br />';
							//echo 'Num_rows: '.$num_rows;
							
							
							if($row['award_id'] != ''){
								$award_completion = 1;
								$award_id = $row['award_id'];
							}
							else{
								$award_completion = 0;
								$award_id = NULL;
							}
							
							//array data
							$pirep_assigned_data = array(
										'user_id' => $user_id,
										'start_icao' => $row['start_icao'],
										'end_icao' => $row['end_icao'],
										'gcd' => $row['gcd_nm'],
										'aircraft_id' => $aircraft_id,
										'passengers' => $num_pax,
										'cargo' => $num_cargo,
										'group_id' => $group_id,
										'group_order' => $row['sequence'],
										'created' => $gmt_mysql_datetime,
										'tour_id' => $tour_id,
										'tour_leg_id' => $row['leg_id'],
										'fs_version' => $selected_version,
										'award_completion' => $award_completion,
										'award_id' => $award_id,
							);
					
							//insert data
							$this->db->insert('pirep_assigned', $this->db->escape($pirep_assigned_data));
						}
						
					}
					
				//redirect to dispatch
				redirect('dispatch');	
					
				//close num_rows
				}
				else{
					if($logged_in != '1'){
						$data['exception'] = 'You must be logged in to assigna  tour.';
					}
					elseif($pilot_rank < $rank_id){
						$data['exception'] = 'Could not assign this tour as you do not meet the rank requirements.';
					}
					else{
						$data['exception'] = 'Could not assign this tour as you already have a tour assigned.';
					}
				}
				
			}
			
			
			
			$data['aircraft_id'] = $aircraft_id;
			
			
			$tour_name = $tour_data['0']['name'];
			
			$data['id'] = $tour_data['0']['id'];
			$data['rank_id'] = $tour_data['0']['rank_id'];
			$data['name'] = $tour_data['0']['name'];
			$data['detail_info'] = $tour_data['0']['detail_info'];
			$data['requirements'] = $tour_data['0']['requirements'];
			
			$data['page_title'] = $tour_name;
			$data['no_links'] = '1';
		
			$this->view_fns->view('global/tours/tours_details', $data);
			
		}
		
	}
	
	
}

/* End of file */