<?php
 
class Fleet extends CI_Controller {

	function Fleet()
	{
		parent::__construct();
	}
	
	
	function aircraft($selected_division = NULL, $selected_aircraft = NULL){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		
		$data['historical_set'] = 0;
		$data['division_set'] = 0;
		
		//handle historic and current fleet
		if($selected_division == 'H'){
			$menu_restrict = "WHERE aircraft.in_fleet = '0' AND aircraft.enabled = '1'";
			
			$data['historical_set'] = 1;
			
		}
		else{
			$menu_restrict = "WHERE aircraft.in_fleet = '1'";
		}
		
		//sort out  selected division - aircraft & division
		if(
		($selected_division != NULL && is_numeric($selected_division)) 
		&& ($selected_aircraft != NULL && is_numeric($selected_aircraft))
		){
			$div_restrict = "WHERE aircraft.id = '$selected_aircraft'";
			$menu_restrict .= "AND aircraft.division = '$selected_division'";
			
			$data['division_set'] = 1;
			
		}
		//we have an aircraft id, but no division
		elseif($selected_aircraft != NULL && is_numeric($selected_aircraft)){
			$div_restrict = "WHERE aircraft.id = '$selected_aircraft'";
			$menu_restrict .= "AND divisions.public = '1'";
						
		}
		//we have a division, but no aircraft
		elseif($selected_division != NULL && $selected_division != 'A' && $selected_division != 'H'){
			$div_restrict = "WHERE aircraft.division = '$selected_division' AND aircraft.in_fleet = '1'";
			$menu_restrict .= "AND aircraft.division = '$selected_division'";
			
			$data['division_set'] = 1;
			
		}
		elseif($selected_division == 'A'){
			$div_restrict = "WHERE aircraft.in_fleet = '1' AND divisions.public = '1'";	
			$menu_restrict .= "AND divisions.public = '1'";	
			
		}
		elseif($selected_division == 'H'){
			$div_restrict = "WHERE aircraft.in_fleet = '0' AND aircraft.enabled = '1' AND divisions.public = '1'";
			$menu_restrict .= "AND divisions.public = '1'";
		}
		else{
			//redirect('fleet');
		}
		
		
		
		//grab post
		$post_aircraft = $this->security->sanitize_filename($this->input->post('selected_aircraft'));
		
		if($post_aircraft != '' && $post_aircraft != $selected_aircraft){
			redirect('fleet/aircraft/'.$selected_division.'/'.$post_aircraft);
		}
	
		//make database call to grab the aircraft data
		$query = $this->db->query("	SELECT 	aircraft.id as id,
											aircraft.name as name,
											aircraft.clss as clss,
											aircraft.pax as pax,
											aircraft.cargo as cargo,
											aircraft.division as division,
											aircraft.in_fleet as in_fleet,
											aircraft.rank as rank,
											aircraft.icao_code as icao_code,
											aircraft.variant as variant,
											aircraft.aircraft_type as aircraft_type,
											aircraft.description as description,
											aircraft.length as length,
											aircraft.wingspan as wingspan,
											aircraft.height as height,
											aircraft.engine as engine,
											aircraft.engine_manufacturer as engine_manufacturer,
											aircraft.cruise_speed as cruise_speed,
											aircraft.service_ceiling as service_ceiling,
											aircraft.gross_weight as gross_weight,
											aircraft.crew as crew,
											aircraft.price as price,
											aircraft.manufacturer as manufacturer,
											aircraft.oew as oew,
											aircraft.mtow as mtow,
											aircraft.fuel_capacity as fuel_capacity,
											aircraft.fuel_weight as fuel_weight,
											aircraft.long_range_altitude as long_range_altitude,
											aircraft.long_range_speed as long_range_speed,
											aircraft.max_speed as max_speed,
											aircraft.range_mload as range_mload,
											aircraft.range_mfuel as range_mfuel,
											aircraft.engine_thrust as engine_thrust,
											aircraft.to_rwy_length_min as to_rwy_length_min,
											aircraft.to_rwy_length_max as to_rwy_length_max,
											aircraft.land_rwy_length as land_rwy_length,
											aircraft.v_rotate as v_rotate,
											aircraft.v_approach as v_approach,
											aircraft.flaps_rotate as flaps_rotate,
											aircraft.flaps_approach as flaps_approach,
											aircraft.maximum_climb_rate as maximum_climb_rate,
											aircraft.maximum_desc_rate as maximum_desc_rate,
											divisions.division_longname as division_name,
											divisions.colour as division_colour,
											divisions.text as division_text_colour
																					
									FROM aircraft
									
										LEFT JOIN divisions
										ON divisions.id = aircraft.division
										
										$div_restrict
									
									ORDER BY aircraft.clss, aircraft.name
									
									LIMIT 1
											
										");
				
		$aircraft_data =  $query->result_array();
		$num_aircraft =  $query->num_rows();
		
		if($num_aircraft < 1){
			redirect('fleet');
		}
		
		foreach($aircraft_data['0'] as $key => $value){
			$data[$key] = $value;
		}
		
		$pulled_aircraft_id = $data['id'];
		
		$data['selected_aircraft'] = $pulled_aircraft_id;
		$data['selected_division'] = $selected_division;
		
		
		//grab downloads
		$query = $this->db->query("	SELECT 	aircraft_downloads.id as id,
											aircraft_downloads.aircraft_id,
											aircraft_downloads.location,
											aircraft_downloads.payware,
											aircraft_downloads.model,
											aircraft_downloads.description,
											aircraft_downloads.aircraft_id,
											flight_sim_series.name as series_name,
											flight_sim_versions.version_name,
											aircraft_downloads_type.name as type
											
																					
									FROM aircraft_downloads
									
										LEFT JOIN aircraft_downloads_type
										ON aircraft_downloads_type.id = aircraft_downloads.type
									
										LEFT JOIN flight_sim_versions
										ON flight_sim_versions.id = aircraft_downloads.flight_sim_id
										
										LEFT JOIN flight_sim_series
										ON flight_sim_series.id = flight_sim_versions.series_id
									
									WHERE aircraft_downloads.aircraft_id = '$pulled_aircraft_id'
									
									ORDER BY flight_sim_series.name, flight_sim_versions.version_name, aircraft_downloads_type.name, aircraft_downloads.payware
											
										");
				
		$data['downloads_data'] =  $query->result();
		$data['num_downloads'] =  $query->num_rows();
		
		//now grab number of aircraft and their state
		$query = $this->db->query("	SELECT 	
		
										COUNT(propilot_aircraft.aircraft_id) as num_aircraft,
										propilot_aircraft_state.state_name as state
										
									FROM propilot_aircraft
									
										LEFT JOIN propilot_aircraft_state
										ON propilot_aircraft_state.id = propilot_aircraft.state_id
									
									WHERE propilot_aircraft.aircraft_id = '$pulled_aircraft_id'
									AND propilot_aircraft.state_id != '3'
									AND propilot_aircraft.state_id != '4'
									
									GROUP BY propilot_aircraft.state_id
		");
		
		$data['aircraft_numbers'] = $query->result();
		
		
		//grab index of craft for dropdown menu
		$query = $this->db->query("	SELECT 	aircraft.id as id,
											aircraft.name as name,
											aircraft.clss as clss,
											aircraft.division as division
											
									FROM aircraft
									
										LEFT JOIN divisions
										ON divisions.id = aircraft.division
									
									$menu_restrict
									
									ORDER BY aircraft.clss, aircraft.name
											
										");
				
		$aircraft_results =  $query->result();
		
		foreach($aircraft_results as $row){
			$data['aircraft_array']['Class '.$row->clss][$row->id] = $row->name;
		}
		
		
		//grab all divisions to build menu array
		$query = $this->db->query("	SELECT 	id, division_longname, prefix, blurb
											
									FROM divisions
									
									WHERE public = '1'
									
									ORDER BY id
											
										");
				
		$division_results =  $query->result();
		
		
		$data['division_array'] = array();
		$division_code_array = array();
		
		foreach($division_results as $row){
			$data['division_array'][$row->id]['longname'] = $row->division_longname;
			$data['division_array'][$row->id]['id'] = $row->id;
			$data['division_array'][$row->id]['prefix'] = $row->prefix;
			$data['division_array'][$row->id]['blurb'] = $row->blurb;
			$division_code_array[$row->id] = $row->prefix;
		}
		
		
		
		$data['page_title'] = $data['name'].' - '.$data['division_name'];
		$data['no_links'] = '1';
		
		$this->view_fns->view('global/fleet/fleet_aircraft', $data);
	}
	
	function index($fleet_type = 'C', $flight_sim = 'A')
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		
		//grab post
		$post_flight_sim = $this->security->sanitize_filename($this->input->post('flight_sim'));
		
		if($post_flight_sim != '' && $post_flight_sim != $flight_sim){
			redirect('fleet/index/'.$fleet_type.'/'.$post_flight_sim);
		}
		
		$data['fleet_type'] = $fleet_type;
		$data['flight_sim'] = $flight_sim;
		
		$data['fleet_menu_array'] = array(
					'C' => 'Current Fleet',
					'H' => 'Historical Fleet'
		);
		
		//handle unknown entries
		if(!array_key_exists($fleet_type, $data['fleet_menu_array'])){
			$fleet_type = 'C';
		}
		
		
		$flightsim_restrict = '';
		
		if($flight_sim != 'A'){
			$flightsim_restrict = "AND aircraft_downloads.flight_sim_id = $flight_sim";
		}
		
		
		//make database call to grab the aircraft data
		$query = $this->db->query("	SELECT 	aircraft.id as id,
											aircraft.name as name,
											aircraft.clss as clss,
											aircraft.pax as pax,
											aircraft.division as division,
											aircraft.in_fleet as in_fleet,
											aircraft.enabled as enabled,
											aircraft.rank as rank,
											aircraft.icao_code as icao_code,
											aircraft.variant as variant,
											aircraft.aircraft_type as aircraft_type,
											divisions.division_longname as division_longname,
											aircraft_downloads.id as aircraft_downloads_id
											
									FROM aircraft
									
										LEFT JOIN divisions
										ON divisions.id = aircraft.division
										
										LEFT JOIN aircraft_downloads
										ON aircraft_downloads.aircraft_id = aircraft.id
										$flightsim_restrict
									
									WHERE aircraft.division != '7'
									AND enabled = '1'
									
									GROUP BY aircraft.id, aircraft_downloads.id
									
									ORDER BY aircraft.clss, aircraft.name
									
									
											
										");
				
		$aircraft_results =  $query->result();
		
		$aircraft = array();
		$alt_aircraft = array();
		
		//initialise
		$j = 1;
		
		while($j <= 7){
			$k = 1;
			while($k <= 4){
			$aircraft[$j][$k] = '';
			$k++;
			}
		$j++;
		}
		
		$j = 1;
		
		while($j <= 7){
			$k = 1;
			while($k <= 4){
			$historical[$j][$k] = '';
			$k++;
			}
		$j++;
		}
		
		
		foreach($aircraft_results as $row){
				
			//if we are main, cargo, business or charter	
			if($row->division <= 4 && $row->in_fleet == '1'){
				$aircraft[$row->clss][$row->division][$row->icao_code]['aircraft_id'] = $row->id;
				$aircraft[$row->clss][$row->division][$row->icao_code]['name'] = $row->name;
				$aircraft[$row->clss][$row->division][$row->icao_code]['clss'] = $row->clss;
				$aircraft[$row->clss][$row->division][$row->icao_code]['pax'] = $row->pax;
				$aircraft[$row->clss][$row->division][$row->icao_code]['division'] = $row->division;
				$aircraft[$row->clss][$row->division][$row->icao_code]['division_longname'] = $row->division_longname;
				$aircraft[$row->clss][$row->division][$row->icao_code]['in_fleet'] = $row->in_fleet;
				$aircraft[$row->clss][$row->division][$row->icao_code]['enabled'] = $row->enabled;
				$aircraft[$row->clss][$row->division][$row->icao_code]['rank'] = $row->rank;
				$aircraft[$row->clss][$row->division][$row->icao_code]['icao_code'] = $row->icao_code;
				$aircraft[$row->clss][$row->division][$row->icao_code]['variant'] = $row->variant;
				$aircraft[$row->clss][$row->division][$row->icao_code]['aircraft_type'] = $row->aircraft_type;
				$aircraft[$row->clss][$row->division][$row->icao_code]['aircraft_downloads_id'] = $row->aircraft_downloads_id;
			}
			else{
			
				if($row->division <= 4 && $row->in_fleet != '1'){
				
				$historical[$row->clss][$row->division][$row->icao_code]['aircraft_id'] = $row->id;
				$historical[$row->clss][$row->division][$row->icao_code]['name'] = $row->name;
				$historical[$row->clss][$row->division][$row->icao_code]['clss'] = $row->clss;
				$historical[$row->clss][$row->division][$row->icao_code]['pax'] = $row->pax;
				$historical[$row->clss][$row->division][$row->icao_code]['division'] = $row->division;
				$historical[$row->clss][$row->division][$row->icao_code]['division_longname'] = $row->division_longname;
				$historical[$row->clss][$row->division][$row->icao_code]['in_fleet'] = $row->in_fleet;
				$historical[$row->clss][$row->division][$row->icao_code]['enabled'] = $row->enabled;
				$historical[$row->clss][$row->division][$row->icao_code]['rank'] = $row->rank;
				$historical[$row->clss][$row->division][$row->icao_code]['icao_code'] = $row->icao_code;
				$historical[$row->clss][$row->division][$row->icao_code]['variant'] = $row->variant;
				$historical[$row->clss][$row->division][$row->icao_code]['aircraft_type'] = $row->aircraft_type;
				$historical[$row->clss][$row->division][$row->icao_code]['aircraft_downloads_id'] = $row->aircraft_downloads_id;
				
				}
				else{
			
				$alt_aircraft[$row->division]['division_longname'] = $row->division_longname;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['aircraft_id'] = $row->id;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['name'] = $row->name;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['clss'] = $row->clss;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['pax'] = $row->pax;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['division'] = $row->division;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['division_longname'] = $row->division_longname;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['in_fleet'] = $row->in_fleet;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['enabled'] = $row->enabled;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['rank'] = $row->rank;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['icao_code'] = $row->icao_code;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['variant'] = $row->variant;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['aircraft_type'] = $row->aircraft_type;
				$alt_aircraft[$row->division]['clss'][$row->clss][$row->icao_code]['aircraft_downloads_id'] = $row->aircraft_downloads_id;
				
				}
			}
		}
	
		$data['aircraft'] = $aircraft;
		$data['alt_aircraft'] = $alt_aircraft;
		$data['historical'] = $historical;
		
		$data['fleet_type'] = $fleet_type;
		
		
		$data['clss_detail'] = array(
							'1' => 'Small Turboprops - FO',
							'2' => 'Medium Turboprops - TC',
							'3' => 'Large Turboprops - STC',
							'4' => 'Regional Jets - FC',
							'5' => 'Mid-size Jets - CC',
							'6' => 'Large Jets - SCC',
							'7' => 'Transcontinental - ATP'	
							);
							
		$data['page_title'] = $data['fleet_menu_array'][$fleet_type];
		$data['no_links'] = '1';
		
		
		//grab all the flight sim types in db to build array
		$query = $this->db->query("	SELECT 	flight_sim_series.id as series_id,
											flight_sim_versions.id as version_id,
											flight_sim_series.name as series_name,
											flight_sim_versions.version_name as version_name
											
									FROM flight_sim_series
									
										LEFT JOIN flight_sim_versions
										ON flight_sim_series.id = flight_sim_versions.series_id
										
									WHERE flight_sim_series.supported = '1'
									AND flight_sim_series.display = '1'
									
									ORDER BY flight_sim_series.name, flight_sim_versions.version_number
									
										");
				
		$sim_results =  $query->result();
		
		$data['flight_sim_array'] = array('A' => 'Show All');
		
		foreach($sim_results as $row){
			$data['flight_sim_array'][$row->version_id] = $row->version_name;
		}
		
		
		$this->view_fns->view('global/fleet/fleet_index', $data);
	}
	
	
}

/* End of file */
