<?php
 
class Fx_dispatch extends CI_Controller {

	function Fx_dispatch()
	{
		parent::__construct();	
	}


	function config(){
	
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		
		//do this if not logged in
		if ($this->session->userdata('logged_in') != TRUE){
	
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>login</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
	 
		}
		else{
			
			$limit = $this->session->userdata('rank_id')+1;
			
			$outputwrap = '<?xml version="1.0" encoding="utf-8"?>'."\n";
			$outputwrap .= '<codesets>'."\n";
			$output = array();
			
			//grab all divisions
			$query = $this->db->query("	SELECT 
												divisions.id as id,
												divisions.division_longname as division_longname
												
										FROM divisions
										
											
										WHERE divisions.primary = '1'
										
										ORDER BY divisions.id
										
											
										");
					
			
			$division_data = $query->result();	
			
			$output['division'] = '	<codeset name="divisions">'."\n";
			foreach($division_data as $row){
				$output['division'] .= '		<content>'."\n";
				$output['division'] .= "			<data>".$row->id."</data>\n";
				$output['division'] .= '			<label>'.$row->division_longname."</label>\n";
				$output['division'] .= '		</content>'."\n";
			}
			$output['division'] .=  '	</codeset>'."\n";
			
			
			
			
			//grab all classes
			$query = $this->db->query("	SELECT 
												ranks.class as clss
												
										FROM ranks
										
										WHERE ranks.class <= '$limit'
												
											");
					
			
			$clss_data = $query->result();	
			
			
			
			$output['clss'] = '	<codeset name="classes">'."\n";
			foreach($clss_data as $row){
				$output['clss'] .= '		<content>'."\n";
				$output['clss'] .= "			<data>".$row->clss."</data>\n";
				$output['clss'] .= '			<label>Class '.$row->clss."</label>\n";
				$output['clss'] .= '		</content>'."\n";
			}
			$output['clss'] .=  '	</codeset>'."\n";
			
			
			
			$current_pilot = $this->session->userdata('user_id');
			
			//grab all propilot data
			$query = $this->db->query("	SELECT 
												pilots.pp_location as pp_location,
												pilots.pp_lastflight,
												airports_data.lat as lat,
												airports_data.long as lon
												
										FROM pilots
										
											LEFT JOIN airports_data
											ON airports_data.icao = pilots.pp_location
										
										WHERE pilots.id = '$current_pilot'
												
											");
			
			$pp_data = $query->result();	
			
			
			
			$output['propilot'] = '	<codeset name="propilot">'."\n";
			foreach($pp_data as $row){
			
				//handle travel mode
				$travel_mode = 0;
				if($row->pp_lastflight == '' || $row->pp_lastflight == '0000-00-00 00:00:00' || strtotime($row->pp_lastflight) < strtotime($pp_compare_date)){
					$travel_mode = 1;
				}
			
			
				$output['propilot'] .= '		<content>'."\n";
				$output['propilot'] .= "			<travel>".$travel_mode."</travel>\n";
				$output['propilot'] .= "			<location>".$row->pp_location."</location>\n";
				$output['propilot'] .= "			<lat>".$row->lat."</lat>\n";
				$output['propilot'] .= "			<lon>".$row->lon."</lon>\n";
				$output['propilot'] .= '		</content>'."\n";
			}
			$output['propilot'] .=  '	</codeset>'."\n";
			
			
			
			
			
			$outputclose =  '</codesets>'."\n";
			
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo $outputwrap;
			foreach($output AS $row){
				echo $row;
			}
			echo $outputclose;
				
		//close logged in	
		}
	
	
		
	//close function
	}


	function submit($origin = NULL, $destination = NULL, $aircraft_id = NULL, $propilot_mode = 0){
	
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');
		$this->load->model('Dispatch_model');
		
		$aircraft_reserved = FALSE;
		$aircraft_state = TRUE;		
		
		//if propilot, need to get the data from the tail number.
			if($propilot_mode == 1){
				$query = $this->db->query("	SELECT 
												propilot_aircraft.id as tail_id,
												propilot_aircraft.aircraft_id as aircraft_id,
												propilot_aircraft.reserved,
												propilot_aircraft.state_id
												
										FROM propilot_aircraft
										
										
										WHERE propilot_aircraft.id = '$aircraft_id'
								
										LIMIT 1	
										");
					
			
				$tail_data = $query->result_array();	
				$num_tails = $query->num_rows();
				
				if($num_tails > 0){
					$tail_id = $aircraft_id;
					$aircraft_id = $tail_data['0']['aircraft_id'];
					$reserved = $tail_data['0']['reserved'];
					$state = $tail_data['0']['state_id'];
					
					if($reserved >= $pp_compare_date){
						//aircraft is reserved
						$aircraft_reserved = TRUE;
					}
					
					if($state != 1){
						$aircraft_state = FALSE;
					}
					
				}
				else{
					$aircraft_id = NULL;
				} 
			
			}
		
		
		//do this if not logged in
		if ($this->session->userdata('logged_in') != TRUE){
	
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>login</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
	 
		}
		elseif($aircraft_reserved == TRUE){
			
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>reserved</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
		}
		elseif($aircraft_state == FALSE){
			
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>state</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
		}
		elseif($origin == NULL ||  $destination == NULL || $aircraft_id == NULL){
		
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>error</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
		}
		else{
		
		
			$current_pilot = $this->session->userdata['user_id'];
		
			//we have the data, book it out
			
			$limit = $this->session->userdata('rank_id')+1;
			$division = 'ALL';
		
			
			
			//query to get lat and lon of start and end
			$query = $this->db->query("	SELECT 
												airports_data.icao as icao,
												airports_data.lat as lat,
												airports_data.long as lon
												
										FROM airports_data
										
										
										WHERE airports_data.icao = '$origin'
										OR airports_data.icao = '$destination'
								
											
										");
					
			
			$airport_data = $query->result();	
			$num_rows = $query->num_rows();
			
			if($num_rows == 2){
			
				$origin_lat = 0;
				$origin_lon = 0;
				$destination_lat = 0;
				$destination_lon = 0;
			
			
				foreach($airport_data as $row){
					if($row->icao == $origin){
						$origin_lat = $row->lat;
						$origin_lon = $row->lon;
					}
					elseif($row->icao == $destination){
						$destination_lat = $row->lat;
						$destination_lon = $row->lon;
					}
				}
				
				
			
				
				//query to get aircraft data
				$aircraft_data = $this->Dispatch_model->get_aircraft_array($division, $limit);
				$data['aircraft_array'] = $aircraft_data['aircraft_array'];
				$pax_array = $aircraft_data['pax_array'];
				$cargo_array = $aircraft_data['cargo_array'];
				
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
				$start_icao = $origin;
				$end_icao = $destination;
				$gcd_km = $this->geocalc_fns->GCDistance($origin_lat, $origin_lon, $destination_lat, $destination_lon);
				$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);
				$aircraft_id = $aircraft_id;
				$passengers = $num_pax;
				$cargo = $num_cargo;
				$created = $gmt_mysql_datetime;
				
				//propilot flight
				if($propilot_mode == 1){		
				
					//first check to make sure that there are no other flights booked. If so, return an error
					$query = $this->db->query("	SELECT 
														propilot_aircraft.id as id
														
												FROM propilot_aircraft
												
												
												WHERE propilot_aircraft.reserved_by = '$current_pilot'
												AND (propilot_aircraft.reserved IS NOT NULL 
													AND propilot_aircraft.reserved != '' 
													AND propilot_aircraft.reserved != '0000-00-00 00:00:00' 
													AND propilot_aircraft.reserved >= '$pp_compare_date')
												AND propilot_aircraft.state_id = '1'
										
													
												");
								
					$num_rows = $query->num_rows();	
					
					if($num_rows > 0){
					
						header('Content-Type: text/xml');
						header("Cache-Control: no-cache, must-revalidate");
						echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
						echo '<feedback>'."\n";
						echo '	<error>'."\n";
						echo '		<code>locked</code>'."\n";
						echo '	</error>'."\n";
						echo '</feedback>';
					
					}
					//not already locked
					else{
					
						//clear all locked flights by this pilot
						$propilot_clear_data = array(
									'reserved_by' => NULL,
									'reserved' => NULL,
									'destination' => NULL,
									'gcd' => NULL,
									'pax' => NULL,
									'cargo' => NULL
						);
						
						$this->db->where('reserved_by', $user_id);
						$this->db->update('propilot_aircraft', $this->db->escape($propilot_clear_data));
					
						//array data
						$propilot_aircraft_data = array(
									'reserved_by' => $user_id,
									'reserved' => $gmt_mysql_datetime,
									//'start_icao' => $start_icao,
									'destination' => $end_icao,
									'gcd' => $gcd_nm,
									'pax' => $passengers,
									'cargo' => $cargo
						);
				
						$this->db->where('id', $tail_id);
				
						//insert data
						if($this->db->update('propilot_aircraft', $this->db->escape($propilot_aircraft_data))){
						
							//update the pilot's data to remove deadheading
							$pilot_data = array(
									'deadhead_direct' => '0',
									'deadhead_dest' => NULL,
							);
					
							$this->db->where('id', $user_id);
							$this->db->update('pilots', $this->db->escape($pilot_data));
						
						
							header('Content-Type: text/xml');
							header("Cache-Control: no-cache, must-revalidate");
							echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
							echo '<feedback>'."\n";
							echo '	<error>'."\n";
							echo '		<code>Success</code>'."\n";
							echo '	</error>'."\n";
							echo '</feedback>';
		
						}
						else{
							header('Content-Type: text/xml');
							header("Cache-Control: no-cache, must-revalidate");
							echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
							echo '<feedback>'."\n";
							echo '	<error>'."\n";
							echo '		<code>error</code>'."\n";
							echo '	</error>'."\n";
							echo '</feedback>';
						}
				
					}
				
				}
				//normal flight
				else{
					
					//array data
					$pirep_assigned_data = array(
								'user_id' => $user_id,
								'start_icao' => $start_icao,
								'end_icao' => $end_icao,
								'gcd' => $gcd_nm,
								'aircraft_id' => $aircraft_id,
								'passengers' => $passengers,
								'cargo' => $cargo,
								'created' => $created
					);
			
					//insert data
					if($this->db->insert('pirep_assigned', $this->db->escape($pirep_assigned_data))){
						header('Content-Type: text/xml');
						header("Cache-Control: no-cache, must-revalidate");
						echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
						echo '<feedback>'."\n";
						echo '	<error>'."\n";
						echo '		<code>Success</code>'."\n";
						echo '	</error>'."\n";
						echo '</feedback>';
	
					}
					else{
						header('Content-Type: text/xml');
						header("Cache-Control: no-cache, must-revalidate");
						echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
						echo '<feedback>'."\n";
						echo '	<error>'."\n";
						echo '		<code>error</code>'."\n";
						echo '	</error>'."\n";
						echo '</feedback>';
					}
				
				}
				
			}
			//no rows
			else{
				header('Content-Type: text/xml');
				header("Cache-Control: no-cache, must-revalidate");
				echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
				echo '<feedback>'."\n";
				echo '	<error>'."\n";
				echo '		<code>error</code>'."\n";
				echo '	</error>'."\n";
				echo '</feedback>';
			}
		
		}
		
	}


	function destination($clss = NULL, $division = NULL, $origin = NULL, $propilot_mode = 0)
	{
		
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//$this->load->library('date_fns');
		$this->load->model('Dispatch_model');
		
		$floor = 0;
		$limit = $this->session->userdata('rank_id')+1;
		
		//restrict prop planes to prop routes
		if($clss <= 3 && $limit > 3){
			$limit = 3;
		}
		
		//restrict jet planes to jet routes
		if($clss >= 4 && $floor < 4){
			$floor = 4;
		}
		
		
		
		//handle class
		if($clss == NULL){
			//set to pilot maximum
			$clss = $limit; 
		}
		
		//handle division
		if($division == NULL){
			$division = 1;
		}
		
		$division_restrict = "AND timetable.division = '$division'";
		$clss_restrict = "AND timetable.class = '$clss'";
		
		//if propilot mode, remove restrictions on class and destination
		if($propilot_mode == 1){
			//$division_restrict = '';
			//$clss_restrict = '';
		}
		
		
		//do this if not logged in
		if ($this->session->userdata('logged_in') != TRUE){
	
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>login</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
	 
		}
		elseif($origin == NULL){
		
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>error</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
		}
		else{
	
			//grab post data *******************************************************************************************************
			//set up counties array
			
			
			//grab all airports
			$query = $this->db->query("	SELECT 
												airports_data.icao as icao,
												airports_data.lat as lat,
												airports_data.long as lon,
												airports.name as name,
												hub.id as hub_id
												
										FROM airports_data
										
											LEFT JOIN timetable
											ON timetable.arr_airport = airports_data.icao
											$clss_restrict
											$division_restrict
											AND timetable.dep_airport = '$origin'
											
											LEFT JOIN airports
											ON airports.icao = airports_data.icao
											
											LEFT JOIN hub
											ON airports.icao = hub.hub_icao
											
											
											WHERE timetable.flightnumber != ''
											AND timetable.active = '1'
											
											
											GROUP BY airports_data.icao
											
												
											");
					
			
			$airport_data = $query->result();	
			$num_rows = $query->num_rows();
			
			
			
			//grab all aircraft
			//$aircraft_data = $this->Dispatch_model->get_aircraft_array($division, $limit, $floor);
			$aircraft_data = $this->Dispatch_model->get_aircraft_array_restrict($division, $clss, $limit);
			
			
			//if propilot, grab all tails
			
			if($propilot_mode == 1){
			
				$aircraft_id_restrict = '';
			
				if(array_key_exists('aircraft_array_simple', $aircraft_data)){
				
					$i = 0;
				
					foreach($aircraft_data['aircraft_array_simple'] as $key => $value){
					
						if($i == 0){
							$aircraft_id_restrict .= 'AND (';
						}
						else{
							$aircraft_id_restrict .= ' OR ';
						}
						$aircraft_id_restrict .= " propilot_aircraft.aircraft_id = '$key' ";
						
					$i++;
					}
					
					if($i > 0){
						$aircraft_id_restrict .= ')';
					}
					
				}
			
				//grab unlocked aircraft
				$query = $this->db->query("	SELECT 
												propilot_aircraft.id as id,
												propilot_aircraft.tail_id as tail_id,
												aircraft.name as name
												
												
											FROM propilot_aircraft
											
												LEFT JOIN aircraft
												ON aircraft.id = propilot_aircraft.aircraft_id
											
											WHERE propilot_aircraft.location = '$origin'
											$aircraft_id_restrict
											AND (propilot_aircraft.reserved IS NULL OR propilot_aircraft.reserved = '' OR propilot_aircraft.reserved = '0000-00-00 00:00:00' OR propilot_aircraft.reserved < '$pp_compare_date')
											AND propilot_aircraft.state_id = '1'
											
											ORDER BY name, tail_id
										");
										
										
				$aircraft_tails = $query->result();	
				$num_tails = $query->num_rows();						
										
			}
			
			if(!array_key_exists('aircraft_array_simple', $aircraft_data)){
			
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>aircraft</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
			
			}
			elseif(($propilot_mode == 1 && $num_tails == 0)){
			
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>tails</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
			
			}
			else{
				//compile output
				//output the data *******************************************************************************************************
				$output1 = '';
				$output2 = '';
				$output3 = '';
				$output4 = '';
				$output5 = '';
				$output6 = '';
				
				$output1 .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
				$output1 .= '<data>'."\n";
				$output1 .= '	<group name="airports">'."\n";
				
				/*
				$output2 .= '	<results>'."\n";
				$output2 .=  '		<num_result>'.$num_rows."</num_result>\n";
				$output2 .=  '	</results>'."\n";
				*/
				foreach ($airport_data as $row)
					{
						
						$hub = '0';
						//check if this is a hub
						if($row->hub_id != ''){
							$hub = '1';
						}
						
						$output2 .= '		<airport>'."\n";
						$output2 .=  '			<icao>'.strtoupper($row->icao)."</icao>\n";
						$output2 .=  '			<name>'.$row->name."</name>\n";
						$output2 .=  '			<lat>'.$row->lat."</lat>\n";
						$output2 .=  '			<lon>'.$row->lon."</lon>\n";
						$output2 .=  '			<hub>'.$hub."</hub>\n";
						$output2 .=  '		</airport>'."\n";
		
					}
					
				$output3 .=  '	</group>'."\n";
				$output4 .= '	<group name="aircraft">'."\n";
				
				
				//propilot tails
				if($propilot_mode == 1){
				
					foreach($aircraft_tails as $row){
					
					
							$output5 .= '		<plane>'."\n";
							$output5 .=  '			<data>'.$row->id."</data>\n";
							$output5 .=  '			<label>'.$row->tail_id." | ".$row->name."</label>\n";
							$output5 .=  '		</plane>'."\n";
					
					}
				}
				//standard list
				else{
					foreach($aircraft_data['aircraft_array_simple'] as $id => $name){
					
					
							$output5 .= '		<plane>'."\n";
							$output5 .=  '			<data>'.$id."</data>\n";
							$output5 .=  '			<label>'.$name."</label>\n";
							$output5 .=  '		</plane>'."\n";
					
					}
				}
				
			
				
				
				
				
				$output6 .= '	</group>';
				$output6 .= '</data>';
				
				header('Content-Type: text/xml');
				header("Cache-Control: no-cache, must-revalidate");
				echo $output1.$output2.$output3;
				echo $output4.$output5.$output6;
			}
		}
			
	//close function
	}



	
	function origin($clss = NULL, $division = NULL, $propilot_mode = 0)
	{
		
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//$this->load->library('date_fns');
		
		$limit = $this->session->userdata('rank_id')+1;
		
		//handle class
		if($clss == NULL){
			//set to pilot maximum
			$clss = $limit; 
		}
		
		//handle division
		if($division == NULL){
			$division = 1;
		}
		
		//do this if not logged in
		if ($this->session->userdata('logged_in') != TRUE){
	
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>login</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
	 
		}
		else{
	
			//grab post data *******************************************************************************************************
			//set up counties array
			
			
			//grab all airports
			$query = $this->db->query("	SELECT 
												airports_data.icao as icao,
												airports_data.lat as lat,
												airports_data.long as lon,
												airports.name as name,
												hub.id as hub_id
												
										FROM airports_data
										
											LEFT JOIN timetable
											ON timetable.dep_airport = airports_data.icao
											AND timetable.class = '$clss'
											AND timetable.division = '$division'
											
											LEFT JOIN airports
											ON airports.icao = airports_data.icao
											
											LEFT JOIN hub
											ON airports.icao = hub.hub_icao
											
											WHERE timetable.flightnumber != ''
											AND timetable.active = '1'
											
											GROUP BY airports_data.icao
											
												
											");
					
			
			$airport_data = $query->result();	
			$num_rows = $query->num_rows();
			
			
			
			//compile output
			//output the data *******************************************************************************************************
			$output1 = '';
			$output2 = '';
			$output3 = '';
			
			$output1 .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
			$output1 .= '<airports>'."\n";
			/*
			$output2 .= '	<results>'."\n";
			$output2 .=  '		<num_result>'.$num_rows."</num_result>\n";
			$output2 .=  '	</results>'."\n";
			*/
			foreach ($airport_data as $row)
				{
					
					$hub = '0';
					//check if this is a hub
					if($row->hub_id != ''){
						$hub = '1';
					}
					
					$output2 .= '	<airport hub="'.$hub.'">'."\n";
					$output2 .=  '		<icao>'.strtoupper($row->icao)."</icao>\n";
					$output2 .=  '		<name>'.$row->name."</name>\n";
					$output2 .=  '		<lat>'.$row->lat."</lat>\n";
					$output2 .=  '		<lon>'.$row->lon."</lon>\n";
					$output2 .=  '		<hub>'.$hub."</hub>\n";
					$output2 .=  '	</airport>'."\n";
	
				}
				
			$output3 =  '</airports>'."\n";
			
			
			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo $output1.$output2.$output3;
		}
			
	//close function
	}
	
	
	
//close class
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */