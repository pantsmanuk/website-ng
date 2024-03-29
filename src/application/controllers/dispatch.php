<?php

class Dispatch extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function submit($origin = NULL, $destination = NULL, $aircraft_id = NULL, $propilot_mode = 0) {

		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');
		$this->load->model('Dispatch_model');

		$aircraft_reserved = FALSE;
		$aircraft_state = TRUE;

		//if propilot, need to get the data from the tail number.
		if ($propilot_mode == 1) {
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

			if ($num_tails > 0) {
				$tail_id = $aircraft_id;
				$aircraft_id = $tail_data['0']['aircraft_id'];
				$reserved = $tail_data['0']['reserved'];
				$state = $tail_data['0']['state_id'];

				if ($reserved >= $pp_compare_date) {
					//aircraft is reserved
					$aircraft_reserved = TRUE;
				}

				if ($state != 1) {
					$aircraft_state = FALSE;
				}

			} else {
				$aircraft_id = NULL;
			}

		}

		//do this if not logged in
		if ($this->session->userdata('logged_in') != TRUE) {

			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			echo '<feedback>' . "\n";
			echo '	<error>' . "\n";
			echo '		<code>login</code>' . "\n";
			echo '	</error>' . "\n";
			echo '</feedback>';

		} elseif ($aircraft_reserved == TRUE) {

			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			echo '<feedback>' . "\n";
			echo '	<error>' . "\n";
			echo '		<code>reserved</code>' . "\n";
			echo '	</error>' . "\n";
			echo '</feedback>';
		} elseif ($aircraft_state == FALSE) {

			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			echo '<feedback>' . "\n";
			echo '	<error>' . "\n";
			echo '		<code>state</code>' . "\n";
			echo '	</error>' . "\n";
			echo '</feedback>';
		} elseif ($origin == NULL || $destination == NULL || $aircraft_id == NULL) {

			header('Content-Type: text/xml');
			header("Cache-Control: no-cache, must-revalidate");
			echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			echo '<feedback>' . "\n";
			echo '	<error>' . "\n";
			echo '		<code>error</code>' . "\n";
			echo '	</error>' . "\n";
			echo '</feedback>';
		} else {

			$current_pilot = $this->session->userdata['user_id'];

			//we have the data, book it out

			$limit = $this->session->userdata('rank_id') + 1;
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

			if ($num_rows == 2) {

				$origin_lat = 0;
				$origin_lon = 0;
				$destination_lat = 0;
				$destination_lon = 0;

				foreach ($airport_data as $row) {
					if ($row->icao == $origin) {
						$origin_lat = $row->lat;
						$origin_lon = $row->lon;
					} elseif ($row->icao == $destination) {
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
				if (array_key_exists($aircraft_id, $pax_array) && array_key_exists($aircraft_id, $cargo_array)) {
					//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
					$loadout = $this->pirep_fns->get_loadout($pax_array[$aircraft_id], $cargo_array[$aircraft_id]);
					$num_pax = $loadout['pax'];
					$num_cargo = $loadout['cargo'];
				} else {
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
				$created = $data['gmt_mysql_datetime'];

				//propilot flight
				if ($propilot_mode == 1) {

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

					if ($num_rows > 0) {

						header('Content-Type: text/xml');
						header("Cache-Control: no-cache, must-revalidate");
						echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
						echo '<feedback>' . "\n";
						echo '	<error>' . "\n";
						echo '		<code>locked</code>' . "\n";
						echo '	</error>' . "\n";
						echo '</feedback>';

					} //not already locked
					else {

						//clear all locked flights by this pilot
						$propilot_clear_data = array(
							'reserved_by' => NULL,
							'reserved' => NULL,
							'destination' => NULL,
							'gcd' => NULL,
							'pax' => NULL,
							'cargo' => NULL,
						);

						$this->db->where('reserved_by', $user_id);
						$this->db->update('propilot_aircraft', $this->db->escape($propilot_clear_data));

						//array data
						$propilot_aircraft_data = array(
							'reserved_by' => $user_id,
							'reserved' => $data['gmt_mysql_datetime'],
							//'start_icao' => $start_icao,
							'destination' => $end_icao,
							'gcd' => $gcd_nm,
							'pax' => $passengers,
							'cargo' => $cargo,
						);

						$this->db->where('id', $tail_id);

						//insert data
						if ($this->db->update('propilot_aircraft', $this->db->escape($propilot_aircraft_data))) {

							//update the pilot's data to remove deadheading
							$pilot_data = array(
								'deadhead_direct' => '0',
								'deadhead_dest' => NULL,
							);

							$this->db->where('id', $user_id);
							$this->db->update('pilots', $this->db->escape($pilot_data));

							//redirect on success
							redirect('dispatch');

							header('Content-Type: text/xml');
							header("Cache-Control: no-cache, must-revalidate");
							echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
							echo '<feedback>' . "\n";
							echo '	<error>' . "\n";
							echo '		<code>Success</code>' . "\n";
							echo '	</error>' . "\n";
							echo '</feedback>';

						} else {
							header('Content-Type: text/xml');
							header("Cache-Control: no-cache, must-revalidate");
							echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
							echo '<feedback>' . "\n";
							echo '	<error>' . "\n";
							echo '		<code>error</code>' . "\n";
							echo '	</error>' . "\n";
							echo '</feedback>';

						}

					}

				} //normal flight
				else {

					//array data
					$pirep_assigned_data = array(
						'user_id' => $user_id,
						'start_icao' => $start_icao,
						'end_icao' => $end_icao,
						'gcd' => $gcd_nm,
						'aircraft_id' => $aircraft_id,
						'passengers' => $passengers,
						'cargo' => $cargo,
						'created' => $created,
					);

					//insert data
					if ($this->db->insert('pirep_assigned', $this->db->escape($pirep_assigned_data))) {

						redirect('dispatch');

						header('Content-Type: text/xml');
						header("Cache-Control: no-cache, must-revalidate");
						echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
						echo '<feedback>' . "\n";
						echo '	<error>' . "\n";
						echo '		<code>Success</code>' . "\n";
						echo '	</error>' . "\n";
						echo '</feedback>';

					} else {
						header('Content-Type: text/xml');
						header("Cache-Control: no-cache, must-revalidate");
						echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
						echo '<feedback>' . "\n";
						echo '	<error>' . "\n";
						echo '		<code>error</code>' . "\n";
						echo '	</error>' . "\n";
						echo '</feedback>';

					}

				}

			} //no rows
			else {
				header('Content-Type: text/xml');
				header("Cache-Control: no-cache, must-revalidate");
				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				echo '<feedback>' . "\n";
				echo '	<error>' . "\n";
				echo '		<code>error</code>' . "\n";
				echo '	</error>' . "\n";
				echo '</feedback>';
			}

		}

	}

	function propilot_lock($aircraft_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		$this->load->library('Geocalc_fns');
		$this->load->model('Dispatch_model');

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//redirect to login
			redirect('auth/login');

		} else {

			//check that aircraft id supplied
			if (is_null($aircraft_id)) {
				redirect('dispatch');
			}

			//WE HAVE THE AIRCRAFT_ID, GRAB CURRENT LOCATION AND AIRCRAFT DATA
			$query = $this->db->query("	SELECT 
												propilot_aircraft.id as aircraft_id,
												propilot_aircraft.location as aircraft_location,
												propilot_aircraft.reserved as aircraft_reserved,
												airports_data.lat as lat,
												airports_data.long as lon,
												airports_data.name as airport_name,
												aircraft.clss as aircraft_clss,
												aircraft.name as aircraft,
												aircraft.division as aircraft_division
												
										FROM propilot_aircraft
										
											LEFT JOIN aircraft
											ON propilot_aircraft.aircraft_id = aircraft.id

											LEFT JOIN airports_data
											ON airports_data.ICAO = propilot_aircraft.location
										
										WHERE propilot_aircraft.id = '$aircraft_id'
												
											");

			$aircraft_data = $query->result();
			$num_ac = $query->num_rows();

			$aircraft_clss = 0;
			$aircraft_division = 0;
			$aircraft_location = NULL;
			$aircraft_reserved = NULL;
			$aircraft = NULL;
			$aircraft_lat = NULL;
			$aircraft_lon = NULL;
			$airport_name = NULL;
			foreach ($aircraft_data as $acrow) {
				//$pp_location = $pprow->pp_location;
				$aircraft_clss = $acrow->aircraft_clss;
				$aircraft_division = $acrow->aircraft_division;
				$aircraft_location = $acrow->aircraft_location;
				$aircraft_reserved = $acrow->aircraft_reserved;
				$aircraft = $acrow->aircraft;
				$aircraft_lat = $acrow->lat;
				$aircraft_lon = $acrow->lon;
				$airport_name = $acrow->airport_name;

			}

			//echo 'num_ac: '.$num_ac;

			$floor = 0;
			$limit = $this->session->userdata('rank_id') + 1;

			//restrict prop planes to prop routes
			if ($aircraft_clss <= 3 && $limit > 3) {
				$limit = 3;
			}

			//restrict jet planes to jet routes
			if ($aircraft_clss >= 4 && $floor < 4) {
				$floor = 4;
			}

			//grab all the potential destinations for this aircraft and display
			$division_restrict = "AND timetable.division = '$aircraft_division'";
			$clss_restrict = "AND timetable.class = '$aircraft_clss'";

			//echo $division_restrict.'<br><br>'.$clss_restrict;

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
											$division_restrict
											$clss_restrict
											AND timetable.dep_airport = '$aircraft_location'
											AND timetable.active = '1'
											
											LEFT JOIN airports
											ON airports.icao = airports_data.icao
											
											LEFT JOIN hub
											ON airports.icao = hub.hub_icao
											
											
											WHERE timetable.flightnumber != ''
											AND timetable.active = '1'
											
											
											GROUP BY airports_data.icao
											
												
											");

			$data['airport_data'] = $query->result();
			$num_rows = $query->num_rows();
			$data['aircraft'] = $aircraft;
			$data['aircraft_location'] = $aircraft_location;
			$data['lat1'] = $aircraft_lat;
			$data['lon1'] = $aircraft_lon;
			$data['start_icao'] = $aircraft_location;
			$data['start_name'] = $airport_name;
			$data['aircraft_id'] = $aircraft_id;

			//output page
			$data['page_title'] = 'Dispatch - Propilot Lock Flight';
			if ($aircraft_clss <= $limit) {
				$this->view_fns->view('global/dispatch/dispatch_pplock', $data);
			} else {
				$data['pperror'] = 'aclimit';
				$this->view_fns->view('global/dispatch/dispatch_pplockerror', $data);
			}

		}
	}

	function propilot_aircraft($aircraft_restrict = NULL, $location_restrict = NULL, $status_restrict = NULL, $acstatus_restrict = NULL, $search_url = NULL, $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		$this->load->model('Dispatch_model');

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//redirect to login
			redirect('auth/login');

		} else {

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

			$pp_location = 'ALL';

			foreach ($pp_data as $pprow) {
				$pp_location = $pprow->pp_location;
			}

			if ($aircraft_restrict == NULL && $location_restrict == NULL && $status_restrict == NULL && $acstatus_restrict == NULL) {
				redirect('dispatch/propilot_aircraft/ALL/' . $pp_location . '/ALL/1');
			}

			$data['pp_location'] = $pp_location;

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

			if ($search == '' && $search_url != '' && $search_url != ' ' && $search_url != '%20') {
				$search = $search_url;
			}

			$data['search_url'] = $search;

			if (
				($aircraft_restrict != $post_aircraft_restrict && $post_aircraft_restrict != '')
				OR ($location_restrict != $post_location_restrict && $post_location_restrict != '')
				OR ($status_restrict != $post_status_restrict && $post_status_restrict != '')
				OR ($acstatus_restrict != $post_acstatus_restrict && $post_acstatus_restrict != '')
			) {
				redirect('dispatch/propilot_aircraft/' . $post_aircraft_restrict . '/' . $post_location_restrict . '/' . $post_status_restrict . '/' . $post_acstatus_restrict . '/' . $search_url);
			}

			//define session data
			$sessiondata = array(
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$sqlsearch = '';
			//handle search

			//first trim whitespace
			$search = trim($search);

			if ($search != '' && $search != ' ' && $search != '%20') {

				//split up the search into constituent terms
				$search_array = explode(" ", $search);
				$num_search = count($search_array);

				//for multiple term searches
				if ($num_search > 1) {
					$sqlsearch = "WHERE (propilot_aircraft.tail_id LIKE '%" . $search . "%'";
					$sqlsearch .= " OR propilot_aircraft.location LIKE '%" . $search . "%'";
					$sqlsearch .= " OR aircraft.name LIKE '%" . $search . "%'";
					$sqlsearch .= " OR propilot_aircraft_state.state_name LIKE '%" . $search . "%'";
					foreach ($search_array as $row) {
						$sqlsearch .= " OR propilot_aircraft.title LIKE '%" . $row . "%'";
						$sqlsearch .= " OR propilot_aircraft.description LIKE '%" . $row . "%'";
						$sqlsearch .= " OR aircraft.name LIKE '%" . $row . "%'";
						$sqlsearch .= " OR propilot_aircraft_state.state_name LIKE '%" . $row . "%'";
					}
					$sqlsearch .= ')';

					$sqlsearch .= " AND propilot_aircraft.state_id = '1'";

				} //for single term searches
				else {
					$sqlsearch = "WHERE (propilot_aircraft.tail_id LIKE '%$search%' 
				OR propilot_aircraft.location LIKE '%$search%' 
				OR aircraft.name LIKE '%$search%'
				OR propilot_aircraft_state.state_name LIKE '%$search%')
				";
				}

				$sqlsearch .= " AND propilot_aircraft.state_id = '1'";

			}

			//not searching, handle restriction

			if (is_numeric($aircraft_restrict)) {
				if ($sqlsearch == '') {
					$sqlsearch = "WHERE propilot_aircraft.aircraft_id = '$aircraft_restrict'";
				} else {
					$sqlsearch .= " AND propilot_aircraft.aircraft_id = '$aircraft_restrict'";
				}

			}

			if ($location_restrict != 'ALL') {

				if ($sqlsearch == '') {
					$sqlsearch = "WHERE propilot_aircraft.location = '$location_restrict'";
				} else {
					$sqlsearch .= " AND propilot_aircraft.location = '$location_restrict'";
				}

			}
			/* DISABLE VIEW OF NON OPERATIONAL AIRCRAFT
			if(is_numeric($acstatus_restrict)){
					if($sqlsearch == ''){
						$sqlsearch = "WHERE propilot_aircraft.state_id = '$acstatus_restrict'";
					}
					else{
						$sqlsearch .= " AND propilot_aircraft.state_id = '$acstatus_restrict'";
					}

				}
			*/
			if ($sqlsearch == '') {
				$sqlsearch = "WHERE propilot_aircraft.state_id = '1'";
			} else {
				$sqlsearch .= " AND propilot_aircraft.state_id = '1'";
			}

			if ($status_restrict != 'ALL') {

				switch ($status_restrict) {

					case 'Locked':
						if ($sqlsearch == '') {
							$sqlsearch = "WHERE (propilot_aircraft.reserved IS NOT NULL 
													AND propilot_aircraft.reserved != '' 
													AND propilot_aircraft.reserved != '0000-00-00 00:00:00' 
													AND propilot_aircraft.reserved >= '$pp_compare_date')";
						} else {
							$sqlsearch .= " AND (propilot_aircraft.reserved IS NOT NULL 
													AND propilot_aircraft.reserved != '' 
													AND propilot_aircraft.reserved != '0000-00-00 00:00:00' 
													AND propilot_aircraft.reserved >= '$pp_compare_date')";
						}
						break;

					case 'Unlocked':
						if ($sqlsearch == '') {
							$sqlsearch = "WHERE (propilot_aircraft.reserved IS NULL 
													OR propilot_aircraft.reserved = '' 
													OR propilot_aircraft.reserved = '0000-00-00 00:00:00' 
													OR propilot_aircraft.reserved < '$pp_compare_date')";
						} else {
							$sqlsearch .= " AND (propilot_aircraft.reserved IS NULL 
													OR propilot_aircraft.reserved = '' 
													OR propilot_aircraft.reserved = '0000-00-00 00:00:00' 
													OR propilot_aircraft.reserved < '$pp_compare_date')";
						}
						break;

					case 'Reserved':
						if ($sqlsearch == '') {
							$sqlsearch = "WHERE propilot_aircraft.owner IS NOT NULL";
						} else {
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
											reserver.sname as reserver_sname,
											timetable1.flightnumber as flightnumber
											
																								
											FROM propilot_aircraft
											
												LEFT JOIN aircraft
												ON aircraft.id = propilot_aircraft.aircraft_id
												
												LEFT JOIN propilot_aircraft_state
												ON propilot_aircraft_state.id = propilot_aircraft.state_id
												
												LEFT JOIN pilots
												on pilots.id = propilot_aircraft.owner
												
												LEFT JOIN pilots as reserver
												on reserver.id = propilot_aircraft.reserved_by

												LEFT JOIN timetable as timetable1
												ON  timetable1.flightnumber = 

												(
												  SELECT timetable2.flightnumber FROM timetable AS timetable2
												  WHERE timetable2.dep_airport=propilot_aircraft.location
												  AND timetable2.division = aircraft.division
												  AND timetable2.class = aircraft.clss
												  LIMIT 1
												 )
												
												
											$sqlsearch
											
											GROUP BY propilot_aircraft.tail_id
											ORDER BY aircraft.name, propilot_aircraft.tail_id
																			");

			$data['result'] = $query->result();
			$data['num_rows'] = $query->num_rows();

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
			foreach ($result as $row) {
				$acstatus_array[$row->id] = $row->state_name;
			}

			$data['acstatus_array'] = $acstatus_array;

			//status_resrict	
			$data['status_array'] = array('ALL' => 'All Reservation',
				'Locked' => 'Locked',
				'Unlocked' => 'Unlocked',
				'Reserved' => 'Reserved',
			);

			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search', 'maxlength' => '25', 'size' => '25', 'value' => $search);

			//paginatipon
			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '15';

			if ($search == '') {
				$search = ' ';
			}

			$pag_config['base_url'] = $data['base_url'] . 'dispatch/propilot_aircraft/' . $aircraft_restrict . '/' . $location_restrict . '/' . $status_restrict . '/' . $acstatus_restrict . '/' . $search . '/';
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 8;

			$this->pagination->initialize($pag_config);

			//output page
			$data['page_title'] = 'Dispatch - Propilot Aircraft';
			$this->view_fns->view('global/dispatch/dispatch_ppaircraft', $data);

		}

	}

	function deadhead() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Dispatch_model');

		$error = '';

		//check if user is already logged in - if not, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//redirect to login

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/pirepquery/' . $pirep_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');

		} else {

			//get current pilot
			$current_user_id = $this->session->userdata('user_id');

			//see if the user is currently set to deadhead and get current location
			$query = $this->db->query("	SELECT 	
											pilots.id as id,
											pilots.pp_location as pp_location,
											pilots.deadhead_dest,
											pilots.deadhead_direct,
											airports.name,
											countries.name as country
											
										FROM pilots
										
											LEFT JOIN airports
											ON airports.ICAO = pilots.pp_location
											
											LEFT JOIN countries
											ON countries.country = airports.country
										
										WHERE pilots.id = '$current_user_id'
										
										LIMIT 1
			
									");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($num_results < 1) {

				//no match redirect
				redirect('dispatch');

			}

			$pp_location = $result['0']['pp_location'];
			$pp_location_name = $result['0']['name'];
			$pp_location_country = $result['0']['country'];
			$curr_destination = $result['0']['deadhead_dest'];
			$curr_direct = $result['0']['deadhead_direct'];

			//grab post
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$destination = $this->security->sanitize_filename($this->input->post('destination'));
			$destination_usr = $this->security->sanitize_filename($this->input->post('destination_usr'));
			$destination_usr = strtoupper($destination_usr);
			$direct = $this->security->sanitize_filename($this->input->post('direct'));
			$clear = $this->security->sanitize_filename($this->input->post('clear'));

			//if $destination_usr isn't empty and destination is 
			if ($valid == 'true' && $destination_usr != '' && $destination == '') {
				//check that the supplied airfield is valid
				$query = $this->db->query("	SELECT 	
											airports_data.id as id
													
											FROM airports_data
											
											WHERE airports_data.icao = '$destination_usr' 
											
											LIMIT 1
										");

				$result = $query->result_array();
				$dest_num_results = $query->num_rows();

				//if we actually got a hit back, then we're valid
				if ($dest_num_results > 0) {
					//if so, set this to overwrite destination
					$destination = $destination_usr;
					$destination_usr = '';
				} else {
					$error .= 'Destination not recognised in our database<br />';
				}

			}

			//if we are to clear the deadhead, do so
			if ($valid == 'true' && $clear == '1') {

				//array the data
				$pilots_data = array(
					'deadhead_dest' => NULL,
					'deadhead_direct' => '0',
					'deadhead_set' => NULL,
				);

				//perform update
				$this->db->where('id', $current_user_id);
				$this->db->update('pilots', $this->db->escape($pilots_data));

				$curr_destination = '';
				$curr_direct = '0';

				//redirect
				redirect('dispatch/propilot/');

			} elseif ($valid == 'true' && !empty($destination) && !empty($destination_usr)) {
				//if we were given both destination values
				$error .= 'Cannot choose destination if you complete both destination fields<br />';
			} //if we are to change the deadhead, do so
			elseif ($valid == 'true' && $destination != '' && $pp_location != $destination) {

				//array the data
				$pilots_data = array(
					'deadhead_dest' => $destination,
					'deadhead_set' => $data['gmt_mysql_datetime'],
				);

				if ($direct == '1') {
					$pilots_data['deadhead_direct'] = '1';
				} else {
					$pilots_data['deadhead_direct'] = '0';
				}

				//perform update
				$this->db->where('id', $current_user_id);
				$this->db->update('pilots', $this->db->escape($pilots_data));

				$curr_destination = $destination;
				$curr_direct = $pilots_data['deadhead_direct'];

				//remove any locked propilot flights (if we're deadheading)
				//clear any aircraft reservation
				$propilot_aircraft_data = array(
					'reserved' => NULL,
					'reserved_by' => NULL,
					'location' => $destination,
					'destination' => NULL,
					'pax' => NULL,
					'cargo' => NULL,
					'gcd' => NULL,
				);

				//perform the update from db
				$this->db->where('reserved_by', $current_user_id);
				$this->db->update('propilot_aircraft', $propilot_aircraft_data);

				//redirect
				redirect('dispatch/propilot/');

			} elseif ($valid == 'true' && $destination == '' && $destination_usr == '') {
				$error .= 'Destination not set<br />';
			} elseif ($valid == 'true' && $pp_location == $destination) {
				$error .= 'Cannot set destination to current location<br />';
			} elseif ($valid == 'true') {
				$error .= 'Something went wrong. I don\'t know what, but I did nothing!<br />';
			}

			$data['pp_location'] = $pp_location;
			$data['curr_destination'] = $curr_destination;
			$data['curr_direct'] = $curr_direct;
			$data['pp_location_name'] = $pp_location_name;
			$data['pp_location_country'] = $pp_location_country;
			$data['error'] = $error;

			$data['destination'] = '';
			$data['direct'] = '0';

			//grab all travel locations for dropdown
			$data['airfield_array'] = array('' => '');
			$data['airfield_array'] += $this->Dispatch_model->get_airfield_array();

			$data['direct_array'] = array('0' => 'Allow indirect', '1' => 'Direct only');

			//inputs
			$data['destination_usr'] = array('name' => 'destination_usr', 'id' => 'destination_usr', 'value' => $destination_usr, 'maxlength' => '4', 'size' => '4', 'style' => 'width:10%');

			//output page
			$data['page_title'] = 'Deadhead Status';
			$data['no_links'] = '1';
			$this->view_fns->view('global/dispatch/dispatch_deadhead', $data);

			//close logged in
		}

		//close function
	}

	function pirepquery($pirep_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');

		if ($pirep_id == NULL) {
			redirect('dispatch/index');
		}

		//check if user is already logged in - if not, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//redirect to login

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/pirepquery/' . $pirep_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');

		} else {

			//define session data
			$sessiondata = array(
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$pilot_comment = $this->security->sanitize_filename($this->input->post('pilot_comment'));

			$current_pilot_username = $this->session->userdata['username'];

			//need to determine whether or not this is a valid pirep - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											pirep.id as id,
											pirep.username as username,
											pilots.fname as fname,
											pilots.fname as sname,
											pirep.hub as hub,
											aircraft.name as aircraft,
											networks.name as onoffline,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.cruisealt as cruisealt,
											pirep.cruisespd as cruisespd,
											pirep.approach as approach,
											pirep.fuelburnt as fuelburnt,
											pirep.comments as comments,
											pirep.circular_distance as gcd,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time,
											pirep.departure_time as departure_time,
											pirep.landing_time as landing_time,
											pirep.comments_mt as comments_mt,
											pirep.submitdate as submitdate,
											pirep.checked as checked,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name
													
											FROM pirep
											
												LEFT JOIN pilots 
												ON pilots.id = pirep.user_id
												
												LEFT JOIN aircraft 
												ON aircraft.id = pirep.aircraft
												
												LEFT JOIN airports as dep_icao
												ON dep_icao.ICAO = pirep.start_icao
												
												LEFT JOIN airports as arr_icao
												ON arr_icao.ICAO = pirep.end_icao
												
												LEFT JOIN networks
												ON networks.id = pirep.onoffline
											
											WHERE pirep.id = '$pirep_id' 
											AND pirep.username = '$current_pilot_username'
											
											LIMIT 1
										");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($valid == 'true') {

				//if we actually got a hit back, then we're valid
				if ($num_results > 0) {

					//only permit for unchecked PIREP
					if ($result['0']['checked'] != '1') {

						//using value of 3 to indicate a queried pirep. 0 for unchecked, 1 for checked, 2 for invalid, 3 query, 4 response				
						$pirep_data['checked'] = 4;
						$pirep_data['last_updated'] = $data['gmt_mysql_datetime'];
						//use the db returned value as an extra check
						$id_val = $result['0']['id'];
						//perform the update from db
						$this->db->where('id', $id_val);
						$this->db->update('pirep', $this->db->escape($pirep_data));

						//if we have a non-blank comment, insert it
						if ($pilot_comment != '') {
							$pirep_queries_data = array(
								'user_id' => $this->session->userdata('user_id'),
								'pirep_id' => $result['0']['id'],
								'from_pilot' => '1',
								'comment' => $pilot_comment,
								'submitted' => $data['gmt_mysql_datetime'],
							);

							$this->db->insert('pirep_queries', $this->db->escape($pirep_queries_data));

						}

					}

				}

				//now redirect back to index
				redirect('dispatch/index');

			} else {
				//if there is such a result
				if ($num_results > 0) {
					$data['aircraft'] = $result['0']['aircraft'];
					$data['engine_start_time'] = $result['0']['engine_start_time'];
					$data['engine_stop_time'] = $result['0']['engine_stop_time'];
					$data['departure_time'] = $result['0']['departure_time'];
					$data['landing_time'] = $result['0']['landing_time'];
					$data['onoffline'] = $result['0']['onoffline'];
					$data['username'] = $result['0']['username'];
					$data['passengers'] = $result['0']['passengers'];
					$data['submitdate'] = $result['0']['submitdate'];
					$data['cargo'] = $result['0']['cargo'];
					$data['dep_name'] = $result['0']['dep_name'];
					$data['start_icao'] = $result['0']['start_icao'];
					$data['end_icao'] = $result['0']['end_icao'];
					$data['arr_name'] = $result['0']['arr_name'];
					$data['pirep_id'] = $pirep_id;

					//form input
					$data['pilot_comment'] = array(
						'name' => 'pilot_comment',
						'id' => 'pilot_comment',
						'value' => '',
						'rows' => '5',
						'cols' => '50',
					);

					//pull all the query messages
					$query = $this->db->query("	SELECT 	
													pirep_queries.id as id,
													pirep_queries.user_id as user_id,
													pirep_queries.pirep_id as pirep_id,
													pirep_queries.from_pilot as from_pilot,
													pirep_queries.comment as comment,
													pirep_queries.submitted as submitted,
													pilots.username as username,
													pilots.fname as fname
															
													FROM pirep_queries
													
														LEFT JOIN pilots
														ON pilots.id = pirep_queries.user_id
													
													WHERE pirep_queries.pirep_id = '$pirep_id' 
													
													ORDER BY pirep_queries.submitted
												");

					$data['messages'] = $query->result();

					//only if this is our pirep
					if ($current_pilot_username == $data['username']) {

						//output confirmation page
						$data['page_title'] = 'Pirep Query';
						$data['no_links'] = '1';
						$this->view_fns->view('global/dispatch/dispatch_pirepquery', $data);

					} else {
						redirect('dispatch/index');
					}
				} else {
					redirect('dispatch/index');
				}

			}
		}

	}

	function tourcraft($assigned_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//$this->load->model('Pirep_model');
		//$this->load->library('Pirep_fns');

		//handle $assigned_id
		if ($assigned_id == NULL || !is_numeric($assigned_id)) {
			redirect('dispatch');
		}

		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		//confirm logged in
		if ($this->session->userdata('logged_in') == 1) {

			$current_pilot = $this->session->userdata('user_id');

			$data['assigned_id'] = $assigned_id;

			//grab details from db to check valid
			$query = $this->db->query("	SELECT 
											pirep_assigned.id as id,
											pirep_assigned.user_id as user_id,
											pirep_assigned.aircraft_id as aircraft_id,
											pirep_assigned.passengers as passengers,
											pirep_assigned.cargo as cargo,
											pirep_assigned.dep_time as dep_time,
											pirep_assigned.start_icao as start_icao,
											pirep_assigned.end_icao as end_icao,
											pirep_assigned.created as created,
											pirep_assigned.award_completion as award_completion,
											pirep_assigned.award_id as award_id,
											pirep_assigned.tour_id as tour_id,
											pirep_assigned.event_id as event_id,
											pirep_assigned.tour_leg_id as tour_leg_id,
											pirep_assigned.mission_id as mission_id,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name
											
									FROM pirep_assigned
										
										LEFT JOIN airports as dep_icao
										ON dep_icao.ICAO = pirep_assigned.start_icao
										
										LEFT JOIN airports as arr_icao
										ON arr_icao.ICAO = pirep_assigned.end_icao
									
									WHERE pirep_assigned.id = '$assigned_id'
									AND pirep_assigned.user_id = '$current_pilot'
									AND (pirep_assigned.event_id IS NULL OR pirep_assigned.event_id = '0')
									
									ORDER BY pirep_assigned.created
									LIMIT 1								
											
										");
			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($num_results < 1 || $result['0']['tour_id'] == '' || !is_numeric($result['0']['tour_id'])) {
				//redirect
				redirect('dispatch');
			} else {

				$tour_id = $result['0']['tour_id'];

				$data['aircraft_id'] = $result['0']['aircraft_id'];
				$data['start_icao'] = $result['0']['start_icao'];
				$data['end_icao'] = $result['0']['end_icao'];
				$data['dep_name'] = $result['0']['dep_name'];
				$data['arr_name'] = $result['0']['arr_name'];

				//grab post
				$valid = $this->security->sanitize_filename($this->input->post('valid'));
				$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));

				//if valid, modify the aircraft
				if ($valid == 'true' && $aircraft_id != '' && is_numeric($aircraft_id)) {

					//array data for update
					$update_data = array(
						'aircraft_id' => $aircraft_id,
					);

					//updat
					$this->db->where('id', $assigned_id);
					$this->db->update('pirep_assigned', $this->db->escape($update_data));

					//redirect to dispatch
					redirect('dispatch');

				} //else display edit
				else {

					//select allowed aircraft
					$query = $this->db->query("	SELECT
													aircraft.id as id,
													aircraft.name as name,
													tour_index.name as tour_name
																											
													FROM tour_aircraft
														
														LEFT JOIN aircraft
														ON aircraft.id = tour_aircraft.aircraft_id
														
														LEFT JOIN divisions
														ON aircraft.division = divisions.id
														
														LEFT JOIN tour_index
														ON tour_aircraft.tour_id = tour_index.id
														
													WHERE tour_aircraft.tour_id = '$tour_id'
													ORDER BY aircraft.clss, aircraft.name
													
												");

					$ac_result = $query->result();

					$i = 0;
					foreach ($ac_result as $row) {

						$data['aircraft_array'][$row->id] = $row->name;

						if ($i == 0) {

							$data['tour_name'] = $row->tour_name;

						}

						$i++;
					}

					$data['page_title'] = 'Tour Aircraft Edit';
					$data['no_links'] = '1';

					$this->view_fns->view('global/dispatch/dispatch_tourac_edit', $data);

				}

			}

		}

		//close function
	}

	function pirep($assigned_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		$this->load->model('Pirep_model');
		$this->load->library('Pirep_fns');

		//handle $assigned_id
		if ($assigned_id == NULL || !is_numeric($assigned_id)) {
			redirect('dispatch');
		}

		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		//confirm logged in
		if ($this->session->userdata('logged_in') == 1) {

			//confirm able to post (email confirmed)
			$email_confirmed = $this->session->userdata['email_confirmed'];
			if ($email_confirmed != 1) {
				//output confirmation page
				$data['page_title'] = 'Pirep restricted';
				$data['no_links'] = '1';
				$this->view_fns->view('global/dispatch/dispatch_nopirep', $data);
			} else {

				//grab post data
				$valid = $this->security->sanitize_filename($this->input->post('valid'));
				$flightdate = $this->security->sanitize_filename($this->input->post('flightdate'));
				$onlineoffline = $this->security->sanitize_filename($this->input->post('onlineoffline'));
				$altitude = $this->security->sanitize_filename($this->input->post('altitude'));
				$speed = $this->security->sanitize_filename($this->input->post('speed'));
				$approach = $this->security->sanitize_filename($this->input->post('approach'));
				$fuelburnt = $this->security->sanitize_filename($this->input->post('fuelburnt'));
				$comments = $this->security->sanitize_filename($this->input->post('comments'));
				$alt_units = $this->security->sanitize_filename($this->input->post('alt_units'));
				$speed_units = $this->security->sanitize_filename($this->input->post('speed_units'));
				$enginestart_hh = $this->security->sanitize_filename($this->input->post('enginestart_hh'));
				$enginestart_mm = $this->security->sanitize_filename($this->input->post('enginestart_mm'));
				$takeoff_hh = $this->security->sanitize_filename($this->input->post('takeoff_hh'));
				$takeoff_mm = $this->security->sanitize_filename($this->input->post('takeoff_mm'));
				$landing_hh = $this->security->sanitize_filename($this->input->post('landing_hh'));
				$landing_mm = $this->security->sanitize_filename($this->input->post('landing_mm'));
				$engineoff_hh = $this->security->sanitize_filename($this->input->post('engineoff_hh'));
				$engineoff_mm = $this->security->sanitize_filename($this->input->post('engineoff_mm'));
				$fuel_units = $this->security->sanitize_filename($this->input->post('fuel_units'));

				$current_pilot = $this->session->userdata['user_id'];

				//need to determine whether or not this is a valid pirep - item is 'owned' by current user as well as grabbing details for confirm page
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
											pirep_assigned.event_id as event_id,
											pirep_assigned.tour_leg_id as tour_leg_id,
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
									
									WHERE pirep_assigned.id = '$assigned_id'
									AND pirep_assigned.user_id = '$current_pilot'
									AND (pirep_assigned.event_id IS NULL OR pirep_assigned.event_id = '0')
									
									ORDER BY pirep_assigned.created
									LIMIT 1								
											
										");
				$result = $query->result_array();
				$num_results = $query->num_rows();
				$valid_id = $num_results;

				if ($valid_id != 1) {
					redirect('dispatch');
				}

				$data['aircraft'] = $result['0']['aircraft'];
				$data['award_completion'] = $result['0']['award_completion'];
				$data['award_id'] = $result['0']['award_id'];
				if ($data['award_id'] == '') {
					$data['award_id'] = NULL;
				}
				$data['aircraft_id'] = $result['0']['aircraft_id'];
				$data['passengers'] = $result['0']['passengers'];
				$data['cargo'] = $result['0']['cargo'];
				$data['start_icao'] = $result['0']['start_icao'];
				$data['dep_name'] = $result['0']['dep_name'];
				$data['end_icao'] = $result['0']['end_icao'];
				$data['arr_name'] = $result['0']['arr_name'];
				$data['assigned_id'] = $assigned_id;
				$data['assigned'] = $num_results;

				$data['award_completion'] = $result['0']['award_completion'];
				$data['award_id'] = $result['0']['award_id'];
				$data['tour_id'] = $result['0']['tour_id'];
				$data['tour_leg_id'] = $result['0']['tour_leg_id'];
				$data['mission_id'] = $result['0']['mission_id'];

				//perform validation
				$this->form_validation->set_rules('valid', 'valid', 'required');
				$this->form_validation->set_rules('flightdate', 'flightdate', 'required');
				$this->form_validation->set_rules('onlineoffline', 'onlineoffline', 'required');
				$this->form_validation->set_rules('enginestart_hh', 'enginestart_hh', 'required');
				$this->form_validation->set_rules('enginestart_mm', 'enginestart_mm', 'required');
				$this->form_validation->set_rules('takeoff_hh', 'takeoff_hh', 'required');
				$this->form_validation->set_rules('takeoff_mm', 'takeoff_mm', 'required');
				$this->form_validation->set_rules('landing_hh', 'landing_hh', 'required');
				$this->form_validation->set_rules('landing_mm', 'landing_mm', 'required');
				$this->form_validation->set_rules('engineoff_hh', 'engineoff_hh', 'required');
				$this->form_validation->set_rules('engineoff_mm', 'engineoff_mm', 'required');

				if ($this->form_validation->run() == FALSE) {
					$validation = 0;
				} else {
					$validation = 1;
				}

				//if no aircraft data
				if ($data['aircraft_id'] == '') {
					$validation = 0;
				}

				//if valid and required post is submitted
				if ($valid == 'true' && $validation == 1) {

					//calculations
					$blocktime_mins = $this->pirep_fns->calculate_blocktime_minutes($enginestart_hh, $enginestart_mm, $engineoff_hh, $engineoff_mm);

					$blocktime_mm = $blocktime_mins % 60;
					$blocktime_hh = ($blocktime_mins - $blocktime_mm) / 60;

					//calculate dates and times
					$flight_dates = $this->pirep_fns->calculate_flightdates($enginestart_hh, $enginestart_mm, $takeoff_hh, $takeoff_mm, $landing_hh, $landing_mm, $engineoff_hh, $engineoff_mm, $flightdate);
					if ($flight_dates != FALSE) {
						$engine_start_time = $flight_dates['start_date'];
						$departure_time = $flight_dates['take_date'];
						$landing_time = $flight_dates['land_date'];
						$engine_stop_time = $flight_dates['off_date'];
					} else {
						redirect('dispatch/error');
					}

					//array the data for db insert
					//grab pilots current flight hours
					$new_mins = 0;
					$new_hours = 0;
					$flight_mins = $this->session->userdata('flight_mins');
					$flight_hours = $this->session->userdata('flight_hours');

					if ($blocktime_mm + $flight_mins >= 60) {
						$new_mins = ($blocktime_mm + $flight_mins) - 60;
						$new_hours = $flight_hours + $blocktime_hh + 1;
					} else {
						$new_mins = ($blocktime_mm + $flight_mins);
						$new_hours = $flight_hours + $blocktime_hh;
					}
					/*
					//grab the ranks
					$query = $this->db->query("	SELECT
										id,
										rank,
										name

								FROM ranks

								WHERE hours >= $new_hours

								ORDER BY id
								LIMIT 1

									");
					$ranks = $query->result_array();

					$new_rank = $ranks['0']['id'];
					*/
					//pilot table
					$pilot_data = array(
						//'flighthours' => $new_hours, don't update hours until approved
						//'flightmins' => $new_mins,
						'status' => '0',
						'lastactive' => $data['gmt_mysql_datetime'],
						'lastflight' => $flightdate,
						'curr_location' => $data['end_icao'],
					);

					$this->db->where('id', $this->session->userdata('user_id'));
					$this->db->update('pilots', $this->db->escape($pilot_data));

					//pirep table
					$pirep_data = array(
						'username' => $this->session->userdata('username'),
						'user_id' => $this->session->userdata('user_id'),
						'hub' => $this->session->userdata('hub_id'),
						'aircraft' => $data['aircraft_id'],
						'onoffline' => $onlineoffline,
						'flightnumber' => '',
						'start_icao' => $data['start_icao'],
						'end_icao' => $data['end_icao'],
						'passengers' => $data['passengers'],
						'cargo' => $data['cargo'],
						'cruisealt' => $altitude,
						'cruisespd' => $speed . ' ' . $speed_units,
						'approach' => $approach,
						'fuelburnt' => $fuelburnt . ' ' . $fuel_units,
						'comments' => $comments,
						'submitdate' => $data['gmt_mysql_datetime'],
						'last_updated' => $data['gmt_mysql_datetime'],
						'checked' => '0',
						'engine_start_time' => $engine_start_time,
						'engine_stop_time' => $engine_stop_time,
						'departure_time' => $departure_time,
						'landing_time' => $landing_time,
						'blocktime_mins' => $blocktime_mins,
						'comments_mt' => '',
						'archived' => '0',
						'circular_distance' => '0',
						'from_fl' => '0',
						'act_different' => '0',
						'fl_version' => '0',
						'aggregate_id' => '',
						'pp_score' => '0.00',
						'aircraft_tech_name' => '',
						'propilot_flight' => '0',
						'tour_id' => $data['tour_id'],
						'tour_leg_id' => $data['tour_leg_id'],
						'mission_id' => $data['mission_id'],
						'award_id' => $data['award_id'],
					);

					//perform pirep insert
					$this->db->insert('pirep', $this->db->escape($pirep_data));

					//now delete the assigned flight
					$this->db->where('id', $assigned_id);
					$this->db->delete('pirep_assigned');

					/*
					//if promoted, redirect to promotion page
					if($new_rank > $this->session->userdata('rank_id')){
						//we have been promoted, update session
						$sessiondata = array(
								'rank_short' => $ranks['0']['rank'],
								'rank_long' => $ranks['0']['name'],
								'rank_id' => $ranks['0']['id']
								);
						$this->session->set_userdata($sessiondata);
						//redirect
						redirect('dispatch/promotion');
					}
					*/
					//now redirect to flight log

					redirect('dispatch');

				} else {

					//default all values that are not submitted on last run
					$aircraft_id = '';
					$onlineoffline = '';
					$altitude = '';
					$speed = '';
					$approach = 'visual';
					$fuelburnt = '';
					$fuel_units = 'lbs';
					$vatsimid = '';
					$ivaoid = '';
					$comments = '';
					$alt_units = 'ft';
					$speed_units = 'ias';
					$flightdate = '';

					$enginestart_hh = '';
					$enginestart_mm = '';
					$takeoff_hh = '';
					$takeoff_mm = '';
					$landing_hh = '';
					$landing_mm = '';
					$engineoff_hh = '';
					$engineoff_mm = '';

					//define all vars
					$data['aircraft_id'] = $aircraft_id;
					$data['onlineoffline'] = $onlineoffline;
					$data['alt_units'] = $alt_units;
					$data['fuel_units'] = $fuel_units;
					$data['speed_units'] = $speed_units;
					$data['approach'] = $approach;
					$data['flightdate'] = $flightdate;

					//define form elements
					$data['enginestart_hh'] = array('name' => 'enginestart_hh', 'id' => 'enginestart_hh', 'value' => $enginestart_hh, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['enginestart_mm'] = array('name' => 'enginestart_mm', 'id' => 'enginestart_mm', 'value' => $enginestart_mm, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['takeoff_hh'] = array('name' => 'takeoff_hh', 'id' => 'takeoff_hh', 'value' => $takeoff_hh, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['takeoff_mm'] = array('name' => 'takeoff_mm', 'id' => 'takeoff_mm', 'value' => $takeoff_mm, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['landing_hh'] = array('name' => 'landing_hh', 'id' => 'landing_hh', 'value' => $landing_hh, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['landing_mm'] = array('name' => 'landing_mm', 'id' => 'landing_mm', 'value' => $landing_mm, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['engineoff_hh'] = array('name' => 'engineoff_hh', 'id' => 'engineoff_hh', 'value' => $engineoff_hh, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['engineoff_mm'] = array('name' => 'engineoff_mm', 'id' => 'engineoff_mm', 'value' => $engineoff_mm, 'maxlength' => '2', 'size' => '2', 'style' => 'width:10%');
					$data['altitude'] = array('name' => 'altitude', 'id' => 'altitude', 'value' => $altitude, 'maxlength' => '100', 'size' => '10', 'style' => 'width:20%');
					$data['speed'] = array('name' => 'speed', 'id' => 'speed', 'value' => $speed, 'maxlength' => '20', 'size' => '10', 'style' => 'width:20%');
					$data['fuelburnt'] = array('name' => 'fuelburnt', 'id' => 'fuelburnt', 'value' => $fuelburnt, 'maxlength' => '100', 'size' => '10', 'style' => 'width:20%');
					$data['comments'] = array('name' => 'comments', 'id' => 'comments', 'value' => $comments, 'rows' => '5', 'cols' => '12', 'style' => 'width:50%');

					//define all the arrays			
					$data['country_array'] = array();
					$data['hub_array'] = array();
					$data['otherva_array'] = array('No' => 'No', 'I was' => 'I was', 'I am' => 'I am');
					$data['aircraft_array'] = array();
					$data['onlineoffline_array'] = array('0' => 'Offline', '1' => 'Online (Vatsim)', '2' => 'Online (IVAO)', '3' => 'Online (Other)');
					$data['approach_array'] = array('4' => 'Visual', '2' => 'ILS', '1' => 'NDB', '0' => 'VOR');
					$data['alt_unit_array'] = array('m' => 'Metres', 'ft' => 'Feet');
					$data['fuel_units_array'] = array('usgal' => 'US Gallons', 'impgal' => 'Imperial Gallons', 'lbs' => 'Pounds');
					$data['speed_units_array'] = array('ias' => 'IAS', 'tas' => 'TAS', 'gs' => 'GS', 'mach' => 'Mach');
					$data['flightsim_array'] = array();
					$data['flightdate_array'] = array();
					$data['dobday_array'] = array('' => '');
					$data['dobmonth_array'] = array('' => '');
					$data['dobyear_array'] = array('' => '');

					//day_array
					$i = 1;
					while ($i <= 31) {
						$data['dobday_array'][$i] = $i;
						$i++;
					}

					//month_array
					$i = 1;
					while ($i <= 12) {
						$data['dobmonth_array'][$i] = $i;
						$i++;
					}

					//year_array
					$current_year = date('Y', time());

					$i = $current_year - 8;
					while ($i >= ($current_year - 100)) {
						$data['dobyear_array'][$i] = $i;
						$i--;
					}

					//flightdate
					$current_year = date('Y', time());

					$i = 0;
					$today = date('Y-m-d', time());
					$yesterday = date('Y-m-d', strtotime('-1 day'));

					while ($i <= 7) {

						$expression = '-' . $i . 'day';

						$date = date('Y-m-d', strtotime($expression));

						if ($date == $today) {
							$label = 'Today';
						} elseif ($date == $yesterday) {
							$label = 'Yesterday';
						} else {
							$label = date('d/m/Y', strtotime($date));
						}

						$data['flightdate_array'][$date] = $label;
						$i++;
					}

					//get countries from db
					$data['country_array'] = $this->Pirep_model->get_countries();

					//get aircraft from db
					$data['aircraft_array'] = $this->Pirep_model->get_aircraft($this->session->userdata('rank_id') + 1);

					$data['page_title'] = 'Pilot Flight Report';

					//output pirep form page
					$data['no_links'] = '1';
					$this->view_fns->view('global/dispatch/dispatch_pirep', $data);

				}
				//close email confirm
			}
			//close logged in
		} else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/pirep/' . $pirep_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			//redirect
			redirect('auth/login');
		}

	}

	function pirepdelete($pirep_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//handle $pirep_id
		if ($pirep_id == NULL || !is_numeric($pirep_id)) {
			redirect('dispatch');
		}

		//confirm logged in
		if ($this->session->userdata('logged_in') == 1) {

			//confirm able to post (email confirmed)
			$email_confirmed = $this->session->userdata['email_confirmed'];
			if ($email_confirmed != 1) {
				//output confirmation page
				$data['page_title'] = 'Pirep restricted';
				$data['no_links'] = '1';
				$this->view_fns->view('global/dispatch/dispatch_nopirep', $data);
			} else {

				//grab post data
				$valid = $this->security->sanitize_filename($this->input->post('valid'));

				$current_pilot_username = $this->session->userdata['username'];
				$current_pilot_id = $this->session->userdata['user_id'];

				//need to determine whether or not this is a valid delete - item is 'owned' by current user as well as grabbing details for confrim page
				$query = $this->db->query("	SELECT 
											pirep.id as id,
											pirep.username as username,
											aircraft.name as aircraft,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.submitdate as submitdate,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.checked as checked,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name
											
									FROM pirep
									
										LEFT JOIN aircraft
										ON aircraft.id = pirep.aircraft
										
										LEFT JOIN airports as dep_icao
										ON dep_icao.ICAO = pirep.start_icao
										
										LEFT JOIN airports as arr_icao
										ON arr_icao.ICAO = pirep.end_icao
									
									WHERE pirep.id = '$pirep_id'
									AND pirep.user_id = '$current_pilot_id'
									
									LIMIT 1								
											
										");
				$result = $query->result_array();
				$num_results = $query->num_rows();

				if ($valid == 'true') {

					//if we actually got a hit back, then we're valid
					if ($num_results > 0) {

						//only permit for unchecked PIREP disallow for queried etc
						if ($result['0']['checked'] == '0') {

							//use the db returned value as an extra check
							$id_val = $result['0']['id'];
							//perform the delete from db
							$this->db->where('id', $id_val);
							$this->db->delete('pirep');

						}

					}

					//echo 'Checked: '.$result['0']['checked'];
					//now redirect back to index
					redirect('dispatch');

				} else {
					//if there is such a result
					if ($num_results > 0) {
						$data['aircraft'] = $result['0']['aircraft'];
						$data['passengers'] = $result['0']['passengers'];
						$data['submitdate'] = $result['0']['submitdate'];
						$data['cargo'] = $result['0']['cargo'];
						$data['dep_name'] = $result['0']['dep_name'];
						$data['start_icao'] = $result['0']['start_icao'];
						$data['end_icao'] = $result['0']['end_icao'];
						$data['arr_name'] = $result['0']['arr_name'];
						$data['pirep_id'] = $pirep_id;

						//output confirmation page
						$data['page_title'] = 'Delete confirmation';
						$data['no_links'] = '1';
						$this->view_fns->view('global/dispatch/dispatch_pirepdelete', $data);
					} else {
						redirect('dispatch');
					}

				}

				//close email confirmed alt
			}
			//close logged in
		} else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/pirepdelete/' . $pirep_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			//redirect
			redirect('auth/login');
		}

	}

	function unlock($propilot_aircraft_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//handle $assigned_id
		if ($propilot_aircraft_id == NULL || !is_numeric($propilot_aircraft_id)) {
			redirect('dispatch');
		}

		//confirm logged in
		if ($this->session->userdata('logged_in') == 1) {

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			$current_pilot = $this->session->userdata['user_id'];

			//need to determine whether or not this is a valid delete - item is 'owned' by current user as well as grabbing details for confrim page
			$query = $this->db->query("	SELECT 
										propilot_aircraft.id as id,
										propilot_aircraft.tail_id as tail_id,
										aircraft.name as aircraft,
										propilot_aircraft.pax as passengers,
										propilot_aircraft.cargo as cargo,
										propilot_aircraft.location as start_icao,
										propilot_aircraft.destination as end_icao,
										propilot_aircraft.reserved as reserved,
										dep_icao.Name as dep_name,
										arr_icao.Name as arr_name
										
								FROM propilot_aircraft
								
									LEFT JOIN aircraft
									ON aircraft.id = propilot_aircraft.aircraft_id
									
									LEFT JOIN airports as dep_icao
									ON dep_icao.ICAO = propilot_aircraft.location
									
									LEFT JOIN airports as arr_icao
									ON arr_icao.ICAO = propilot_aircraft.destination
								
								WHERE propilot_aircraft.id = '$propilot_aircraft_id'
								AND propilot_aircraft.reserved_by = '$current_pilot'
								
								ORDER BY propilot_aircraft.reserved
								LIMIT 1								
										
									");
			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($valid == 'true') {

				//if we actually got a hit back, then we're valid
				if ($num_results > 0) {

					//use the db returned value as an extra check
					$id_val = $result['0']['id'];

					//array data
					$propilot_aircraft_data = array(
						'reserved' => NULL,
						'reserved_by' => NULL,
						'destination' => NULL,
						'pax' => NULL,
						'cargo' => NULL,
					);

					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('propilot_aircraft', $propilot_aircraft_data);
				}

				//now redirect back to index
				redirect('dispatch');

			} else {
				//if there is such a result
				if ($num_results > 0) {
					$data['tail_id'] = $result['0']['tail_id'];
					$data['aircraft'] = $result['0']['aircraft'];
					$data['start_icao'] = $result['0']['start_icao'];
					$data['dep_name'] = $result['0']['dep_name'];
					$data['end_icao'] = $result['0']['end_icao'];
					$data['arr_name'] = $result['0']['arr_name'];
					$data['propilot_aircraft_id'] = $propilot_aircraft_id;

					//output confirmation page
					$data['page_title'] = 'Delete confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/dispatch/dispatch_unlock', $data);
				} else {
					redirect('dispatch');
				}

			}
			//close logged in
		} else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/unlock/' . $propilot_aircraft_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			//redirect
			redirect('dispatch');
		}

	}

	function unassign($assigned_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//handle $assigned_id
		if ($assigned_id == NULL || !is_numeric($assigned_id)) {
			redirect('dispatch');
		}

		//confirm logged in
		if ($this->session->userdata('logged_in') == 1) {

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			$current_pilot = $this->session->userdata['user_id'];

			//need to determine whether or not this is a valid delete - item is 'owned' by current user as well as grabbing details for confrim page
			$query = $this->db->query("	SELECT 
										pirep_assigned.id as id,
										pirep_assigned.user_id as user_id,
										aircraft.name as aircraft,
										pirep_assigned.passengers as passengers,
										pirep_assigned.cargo as cargo,
										pirep_assigned.dep_time as dep_time,
										pirep_assigned.start_icao as start_icao,
										pirep_assigned.end_icao as end_icao,
										pirep_assigned.created as created,
										dep_icao.Name as dep_name,
										arr_icao.Name as arr_name
										
								FROM pirep_assigned
								
									LEFT JOIN aircraft
									ON aircraft.id = pirep_assigned.aircraft_id
									
									LEFT JOIN airports as dep_icao
									ON dep_icao.ICAO = pirep_assigned.start_icao
									
									LEFT JOIN airports as arr_icao
									ON arr_icao.ICAO = pirep_assigned.end_icao
								
								WHERE pirep_assigned.id = '$assigned_id'
								AND pirep_assigned.user_id = '$current_pilot'
								
								ORDER BY pirep_assigned.created
								LIMIT 1								
										
									");
			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($valid == 'true') {

				//if we actually got a hit back, then we're valid
				if ($num_results > 0) {

					//use the db returned value as an extra check
					$id_val = $result['0']['id'];
					//perform the delete from db
					$this->db->where('id', $id_val);
					$this->db->delete('pirep_assigned');
				}

				//now redirect back to index
				redirect('dispatch');

			} else {
				//if there is such a result
				if ($num_results > 0) {
					$data['aircraft'] = $result['0']['aircraft'];
					$data['start_icao'] = $result['0']['start_icao'];
					$data['dep_name'] = $result['0']['dep_name'];
					$data['end_icao'] = $result['0']['end_icao'];
					$data['arr_name'] = $result['0']['arr_name'];
					$data['assigned_id'] = $assigned_id;

					//output confirmation page
					$data['page_title'] = 'Delete confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/dispatch/dispatch_unassign', $data);
				} else {
					redirect('dispatch');
				}

			}
			//close logged in
		} else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/unassign/' . $assigned_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			//redirect
			redirect('dispatch');
		}

	}

	function killroute($route_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//handle $assigned_id
		if ($route_id == NULL) {
			redirect('dispatch');
		}

		$route_id = str_replace("%20", " ", $route_id);

		$data['route_id'] = $route_id;

		//confirm logged in
		if ($this->session->userdata('logged_in') == 1) {

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			$current_pilot = $this->session->userdata['user_id'];

			//need to determine whether or not this is a valid delete - item is 'owned' by current user as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 
										pirep_assigned.id as id,
										pirep_assigned.user_id as user_id,
										aircraft.name as aircraft,
										pirep_assigned.passengers as passengers,
										pirep_assigned.cargo as cargo,
										pirep_assigned.dep_time as dep_time,
										pirep_assigned.start_icao as start_icao,
										pirep_assigned.end_icao as end_icao,
										pirep_assigned.group_id as group_id,
										pirep_assigned.group_order as group_order,
										pirep_assigned.created as created,
										dep_icao.Name as dep_name,
										arr_icao.Name as arr_name
										
								FROM pirep_assigned
								
									LEFT JOIN aircraft
									ON aircraft.id = pirep_assigned.aircraft_id
									
									LEFT JOIN airports as dep_icao
									ON dep_icao.ICAO = pirep_assigned.start_icao
									
									LEFT JOIN airports as arr_icao
									ON arr_icao.ICAO = pirep_assigned.end_icao
								
								WHERE pirep_assigned.group_id = '$route_id'
								AND pirep_assigned.user_id = '$current_pilot'
								
								ORDER BY pirep_assigned.group_order		
										
									");
			$result = $query->result();
			$num_results = $query->num_rows();

			if ($valid == 'true') {

				//if we actually got a hit back, then we're valid
				if ($num_results > 0) {

					//perform the delete from db
					$this->db->where('group_id', $route_id);
					$this->db->where('user_id', $current_pilot);
					$this->db->delete('pirep_assigned');
				}

				//now redirect back to index
				redirect('dispatch');

			} else {
				//if there is such a result
				if ($num_results > 0) {

					$data['pireps'] = $result;

					//output confirmation page
					$data['page_title'] = 'Delete confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/dispatch/dispatch_killroute', $data);
				} else {
					redirect('dispatch');
				}

			}
			//close logged in
		} else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/killroute/' . $route_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			//redirect
			redirect('dispatch');
		}

	}

	function propilot() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//check logged in
		if ($this->session->userdata('logged_in') == 1) {

			//if search submitted, query

			//grab the stats for propilot
			$query = $this->db->query("	SELECT 
											AVG(pirep.pp_score_ng) as pp_average,
											AVG(pirep.blocktime_mins) as pp_average_blocktime_mins,
											pirep.user_id,
											pilots.username,
											pilots.fname,
											pilots.sname
											
											
									FROM pirep
									
										LEFT JOIN pilots
										ON pilots.id = pirep.user_id
									
									WHERE pirep.submitdate >= '$ppstats_compare_datetime'
									AND pirep.propilot_flight = '1'
									
									GROUP BY pirep.user_id
									
									ORDER BY pp_average DESC, sname, fname
									
									LIMIT 10
										
									");

			$average_result = $query->result();
			$num_results = $query->num_rows();

			$data['average_result'] = $average_result;
			$data['average_count'] = $num_results;

			//grab the stats for propilot
			$query = $this->db->query("	SELECT 
											SUM(pirep.pp_score_ng) as pp_sum,											
											SUM(pirep.blocktime_mins) as pp_sum_blocktime_mins,
											pirep.user_id,
											pilots.username,
											pilots.fname,
											pilots.sname
											
											
									FROM pirep
									
										LEFT JOIN pilots
										ON pilots.id = pirep.user_id
									
									WHERE pirep.submitdate >= '$ppstats_compare_datetime'
									AND pirep.propilot_flight = '1'
									
									GROUP BY pirep.user_id
									
									ORDER BY pp_sum DESC, sname, fname
									
									LIMIT 10
										
									");

			$sum_result = $query->result();
			$num_results = $query->num_rows();

			$data['sum_result'] = $sum_result;
			$data['sum_count'] = $num_results;

			//grab the number stats for propilot
			$query = $this->db->query("	SELECT 
											COUNT(pirep.pp_score_ng) as pp_count,
											pirep.user_id,
											pilots.username,
											pilots.fname,
											pilots.sname
											
											
									FROM pirep
									
										LEFT JOIN pilots
										ON pilots.id = pirep.user_id
									
									WHERE pirep.submitdate >= '$ppstats_compare_datetime'
									AND pirep.propilot_flight = '1'
									
									GROUP BY pirep.user_id
									
									ORDER BY pp_count DESC, sname, fname
									
									LIMIT 10
										
									");

			$count_result = $query->result();
			$num_results = $query->num_rows();

			$data['count_result'] = $count_result;
			$data['count_count'] = $num_results;

			//grab the active deadheaders
			$query = $this->db->query("	SELECT 	
											pilots.id as id,
											pilots.username,
											pilots.fname,
											pilots.sname,
											pilots.pp_location as pp_location,
											pilots.deadhead_dest,
											pilots.deadhead_direct,
											pilots.deadhead_set,
											pilots.pp_lastflight,
											airports.name,
											countries.name as country
											
										FROM pilots
										
											LEFT JOIN airports
											ON airports.ICAO = pilots.pp_location
											
											LEFT JOIN countries
											ON countries.country = airports.country
										
										WHERE pilots.lastflight > '$active_compare_date'
										AND pilots.deadhead_dest IS NOT NULL
										
										ORDER BY pilots.pp_lastflight DESC, pilots.lastflight DESC
										
			
									");

			$dh_result = $query->result_array();
			$num_results = $query->num_rows();

			$data['deadhead_result'] = $dh_result;
			$data['deadhead_num'] = $num_results;

			$data['page_title'] = 'Dispatch - Propilot';

			//output for view
			$data['no_links'] = '1';
			$this->view_fns->view('global/dispatch/dispatch_propilot', $data);

		} //if not logged in, redirect
		else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/propilot/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');
		}

	}

	function charter() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		$this->load->model('Dispatch_model');
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');

		//check logged in
		if ($this->session->userdata('logged_in') == 1) {

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));
			$start_icao = $this->security->sanitize_filename($this->input->post('start_icao'));
			$end_icao = $this->security->sanitize_filename($this->input->post('end_icao'));

			$data['exception'] = '';

			//build the dropdown arrays

			//create airfields_array
			$data['airfield_array'] = $this->Dispatch_model->get_airfield_array();

			//restrict division and max flyable class of aircraft
			//$division = '8,5,2'; //set dfivision to all below, use this to restrict
			$division = '';
			$require_charter_enabled = '1';
			$limit = $this->session->userdata('rank_id') + 1;
			$floor = 0;
			if ($limit < 1) {
				$limit = 1;
			}
			//create aircraft_array
			$aircraft_data = $this->Dispatch_model->get_aircraft_array($division, $limit, $floor, $require_charter_enabled);
			$data['aircraft_array'] = $aircraft_data['aircraft_array'];
			$pax_array = $aircraft_data['pax_array'];
			$cargo_array = $aircraft_data['cargo_array'];

			//initialise vars
			if ($start_icao == '') {
				$data['start_icao'] = $this->session->userdata('hub');
			} else {
				$data['start_icao'] = $start_icao;
			}

			if ($end_icao == '') {
				$data['end_icao'] = $this->session->userdata('hub');
			} else {
				$data['end_icao'] = $end_icao;
			}

			$data['aircraft_id'] = $aircraft_id;

			//if data submitted	
			if ($valid == 'true') {

				//check that aircraft has range, otherwise set exception
				//query aircraft range
				$query = $this->db->query("	SELECT 
											aircraft.id as id,
											aircraft.range_mload as range_mload,
											aircraft.range_mfuel as range_mfuel
											
									FROM aircraft
									
									WHERE aircraft.id = '$aircraft_id'
									
									LIMIT 1
										
									");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				if ($num_results == 1) {
					$range_mload = $result['0']['range_mload'];
					$range_mfuel = $result['0']['range_mfuel'];

					if ($range_mload > $range_mfuel) {
						$range = $range_mload;
					} else {
						$range = $range_mfuel;
					}

				} else {
					$range = 0;
					$range_mload = 0;
					$range_mfuel = 0;
				}

				//query location of start and end airports
				$query = $this->db->query("		SELECT 
											airports_data.icao as icao,
											airports_data.lat as lat,
											airports_data.long as lon
											
									FROM airports_data
									
									WHERE airports_data.icao = '$start_icao'
									OR airports_data.icao = '$end_icao'
									
										");

				$result = $query->result_array();
				$num_results = $query->num_rows();
				$no_gcd = FALSE;

				if ($num_results == 2) {
					$lat1 = $result['0']['lat'];
					$lon1 = $result['0']['lon'];
					$lat2 = $result['1']['lat'];
					$lon2 = $result['1']['lon'];
				} else {
					$lat1 = 0;
					$lon1 = 0;
					$lat2 = 0;
					$lon2 = 0;

					$no_gcd = TRUE;
				}

				if ($no_gcd == FALSE) {
					//gcd calculation on airports
					$gcd_km = $this->geocalc_fns->GCDistance($lat1, $lon1, $lat2, $lon2);
					$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);
				}

				if ($no_gcd == TRUE) {
					$data['exception'] .= 'An error occurred attempting to calculate distance between the airports. Airports returned: ' . $num_results . ' Unable to create flight.';
				} elseif ($range < $gcd_nm) {
					$data['exception'] .= 'The aircraft you have selected does not have the range to complete that flight. (Max Load: ' . number_format($range_mload, 0) . 'nm, Max Fuel: ' . number_format($range_mfuel, 0) . 'nm), distance (' . number_format($gcd_nm, 0) . 'nm).';
				} elseif ($start_icao == $end_icao) {
					if ($data['exception'] != '') {
						$data['exception'] .= '<br />';
					}

					$data['exception'] .= 'Flying to the airport you started at is considered a waste of aviation fuel.';
				} else {

					if (array_key_exists($aircraft_id, $pax_array)) {
						//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
						$loadout = $this->pirep_fns->get_loadout($pax_array[$aircraft_id], $cargo_array[$aircraft_id]);
						$num_pax = $loadout['pax'];
						$num_cargo = $loadout['cargo'];
					} else {
						$num_pax = 0;
						$num_cargo = 0;
					}

					//write flight into database
					//insert data
					$insert_array = array(
						'user_id' => $this->session->userdata('user_id'),
						'start_icao' => $start_icao,
						'end_icao' => $end_icao,
						'gcd' => $gcd_nm,
						'aircraft_id' => $aircraft_id,
						'passengers' => $num_pax,
						'cargo' => $num_cargo,
						'created' => $data['gmt_mysql_datetime'],
						'event_id' => 0,
						'event_leg_id' => 0,
					);

					//perform the write
					$this->db->insert('pirep_assigned', $this->db->escape($insert_array));

					//redirect
					redirect('dispatch');

				}

			}

			$data['page_title'] = 'Dispatch - Flight Charters';

			//output for view
			$data['no_links'] = '1';
			$this->view_fns->view('global/dispatch/dispatch_charter', $data);

		} //if not logged in, redirect
		else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/charter/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');
		}

	}

	function assign($success = 1) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');
		$this->load->model('Dispatch_model');

		$data['err_message'] = '';
		if ($success == 0) {
			$data['err_message'] = '<font color="red">Unable to assign flight, please try again.</font><br /><br />';
		}

		//check logged in
		if ($this->session->userdata('logged_in') == 1) {

			$user_id = $this->session->userdata('user_id');
			$limit = $this->session->userdata('rank_id') + 1;

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$accept = $this->security->sanitize_filename($this->input->post('accept'));
			$selected_aircraft_id = $this->security->sanitize_filename($this->input->post('selected_aircraft_id'));
			$post_flightnumber = $this->security->sanitize_filename($this->input->post('flightnumber'));
			$post_aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));

			//grab aircraft list for dropdown
			$data['selected_aircraft_id'] = $selected_aircraft_id;
			$data['aircraft_array'] = $this->Dispatch_model->get_aircraft_array('1,2,3,4', $limit);

			if ($accept == 'true') {

				//grab data for flightnumber
				$query = $this->db->query("	SELECT 
										timetable.flightnumber as flightnumber,
										timetable.dep_airport as dep_airport,
										timetable.arr_airport as arr_airport,
										depgc.lat as dep_lat,
										depgc.long as dep_lon,
										depgc.name as dep_name,
										arrgc.lat as arr_lat,
										arrgc.long as arr_lon,
										arrgc.name as arr_name,
										timetable.dep_time as dep_time,
										timetable.class as clss,
										timetable.division as division,
										divisions.prefix as prefix,
										divisions.division_longname as division_name
										
										
								FROM timetable
								
									LEFT JOIN divisions
									ON timetable.division = divisions.id
									
									LEFT JOIN airports_data AS depgc
									ON depgc.icao = timetable.dep_airport
									
									LEFT JOIN airports_data AS arrgc
									ON arrgc.icao = timetable.arr_airport
								
								WHERE timetable.flightnumber = '$post_flightnumber'
										
									");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if we have a flight
				if ($num_results == 1) {
					$flightnumber = $result['0']['flightnumber'];
					$dep_airport = $result['0']['dep_airport'];
					$arr_airport = $result['0']['arr_airport'];
					$dep_time = $result['0']['dep_time'];
					$clss = $result['0']['clss'];
					$division = $result['0']['division'];
					$division_name = $result['0']['division_name'];
					$prefix = $result['0']['prefix'];
					$dep_lat = $result['0']['dep_lat'];
					$dep_lon = $result['0']['dep_lon'];
					$arr_lat = $result['0']['arr_lat'];
					$arr_lon = $result['0']['arr_lon'];
					$dep_name = $result['0']['dep_name'];
					$arr_name = $result['0']['arr_name'];
				} else {
					redirect('dispatch');
				}

				//write the data and redirect
				$division = 'ALL';
				$aircraft_data = $this->Dispatch_model->get_aircraft_array($division, $limit);
				$pax_array = $aircraft_data['pax_array'];
				$cargo_array = $aircraft_data['cargo_array'];
				//if a passenger flight
				if (array_key_exists($post_aircraft_id, $pax_array)) {
					//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
					$loadout = $this->pirep_fns->get_loadout($pax_array[$post_aircraft_id], $cargo_array[$post_aircraft_id]);
					$num_pax = $loadout['pax'];
					$num_cargo = $loadout['cargo'];
				} else {
					$num_pax = 0;
					$num_cargo = 0;
				}

				$gcd_km = $this->geocalc_fns->GCDistance($dep_lat, $dep_lon, $arr_lat, $arr_lon);
				$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

				//insert data
				$insert_array = array(
					'user_id' => $this->session->userdata('user_id'),
					'start_icao' => $dep_airport,
					'end_icao' => $arr_airport,
					'gcd' => $gcd_nm,
					'aircraft_id' => $post_aircraft_id,
					'passengers' => $num_pax,
					'cargo' => $num_cargo,
					'dep_time' => $dep_time,
					'created' => $data['gmt_mysql_datetime'],
					'event_id' => 0,
					'event_leg_id' => 0,
				);

				//perform the write
				$this->db->insert('pirep_assigned', $this->db->escape($insert_array));

				redirect('dispatch');

			} else {

				if ($valid == 'true') {
					//assign a route by picking one from the timetable that starts from current location (if current location is blank, failover to hub).

					//grab location from database.
					$query = $this->db->query("	SELECT 
										pilots.id as id,
										pilots.curr_location as curr_location
										
								FROM pilots
								
								WHERE pilots.id = '$user_id'	
										
									");

					$result = $query->result_array();
					$num_results = $query->num_rows();

					if ($num_results == 1 && $result['0']['curr_location'] != '') {
						$current_location = $result['0']['curr_location'];
					} else {
						$current_location = $this->session->userdata('hub');
					}

					$ac_restrict = '';
					if (!empty($selected_aircraft_id) && is_numeric($selected_aircraft_id)) {
						//we have an aircraft selection
						$ac_restrict = "AND aircraft.id = '$selected_aircraft_id'";
					}

					//first pick an aircraft that is at or below current rank
					$query = $this->db->query("	SELECT 
										aircraft.id as aircraft_id,
										aircraft.name as aircraft_name,
										aircraft.clss as aircraft_class,
										aircraft.division as aircraft_division
										
								FROM aircraft
								
								WHERE aircraft.clss <= '$limit'
								AND aircraft.enabled = '1'" .
						//AND aircraft.in_fleet = '1'
						"	AND aircraft.division != '5'
								AND aircraft.division != '6'
								AND aircraft.division != '7'
								
								$ac_restrict
								
								ORDER BY RAND()
								LIMIT 1
										
									");

					$result = $query->result_array();
					$num_results = $query->num_rows();

					//if we have an aircraft
					if ($num_results > 0) {
						$data['aircraft_id'] = $result['0']['aircraft_id'];
						$data['aircraft_name'] = $result['0']['aircraft_name'];
						$aircraft_class = $result['0']['aircraft_class'];
						$aircraft_division = $result['0']['aircraft_division'];
					} else {
						redirect('dispatch/assign/0');
					}

					//set up class range
					if ($aircraft_class <= 3) {
						if ($limit >= 3) {
							$cls_range = "	AND timetable.class >= '1'
											AND timetable.class <= '3'
											";
						} else {
							$cls_range = "	AND timetable.class >= '1'
											AND timetable.class <= '$limit'
											";
						}
					} elseif ($aircraft_class <= 6) {
						if ($limit >= 6) {
							$cls_range = "	AND timetable.class >= '4'
											AND timetable.class <= '6'
											";
						} else {
							$cls_range = "	AND timetable.class >= '4'
											AND timetable.class <= '$limit'
											";
						}
					} else {
						$cls_range = "	AND timetable.class = '7'
											";
					}

					//now we have the current location, query the database for flights starting at that location, but that are from the division and class range of the aircraft selected
					$query = $this->db->query("	SELECT 
										timetable.flightnumber as flightnumber,
										timetable.dep_airport as dep_airport,
										timetable.arr_airport as arr_airport,
										depgc.lat as dep_lat,
										depgc.long as dep_lon,
										depgc.name as dep_name,
										arrgc.lat as arr_lat,
										arrgc.long as arr_lon,
										arrgc.name as arr_name,
										timetable.dep_time as dep_time,
										timetable.class as clss,
										timetable.division as division,
										divisions.prefix as prefix,
										divisions.division_longname as division_name
										
										
								FROM timetable
								
									LEFT JOIN divisions
									ON timetable.division = divisions.id
									
									LEFT JOIN airports_data AS depgc
									ON depgc.icao = timetable.dep_airport
									
									LEFT JOIN airports_data AS arrgc
									ON arrgc.icao = timetable.arr_airport
								
								WHERE timetable.dep_airport = '$current_location'	
								AND timetable.division = '$aircraft_division'
								
								$cls_range
								
								ORDER BY RAND()
								LIMIT 1
										
									");

					$result = $query->result_array();
					$num_results = $query->num_rows();

					//if we have a flight
					if ($num_results == 1) {
						$data['flightnumber'] = $result['0']['flightnumber'];
						$data['dep_airport'] = $result['0']['dep_airport'];
						$data['arr_airport'] = $result['0']['arr_airport'];
						$data['dep_time'] = $result['0']['dep_time'];
						$data['clss'] = $result['0']['clss'];
						$data['division'] = $result['0']['division'];
						$data['division_name'] = $result['0']['division_name'];
						$data['prefix'] = $result['0']['prefix'];
						$data['dep_lat'] = $result['0']['dep_lat'];
						$data['dep_lon'] = $result['0']['dep_lon'];
						$data['arr_lat'] = $result['0']['arr_lat'];
						$data['arr_lon'] = $result['0']['arr_lon'];
						$data['dep_name'] = $result['0']['dep_name'];
						$data['arr_name'] = $result['0']['arr_name'];

						$division = $result['0']['division'];
						$clss = $data['clss'];
					} else {
						redirect('dispatch/assign/0');
					}

				}

				$data['page_title'] = 'Dispatch - Flight Assignment';

				//output for view
				$data['no_links'] = '1';
				$this->view_fns->view('global/dispatch/dispatch_assign', $data);

			}

		} //if not logged in, redirect
		else {

			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/assign/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');
		}

	}

	function route() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		$this->load->library('Geocalc_fns');
		$this->load->library('Pirep_fns');
		$this->load->model('Dispatch_model');

		//check logged in
		if ($this->session->userdata('logged_in') == 1) {

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			//selection postdata
			$start_hub_icao = $this->security->sanitize_filename($this->input->post('start_hub_icao'));
			$aircraft_id = $this->security->sanitize_filename($this->input->post('aircraft_id'));
			$hop_num = $this->security->sanitize_filename($this->input->post('hop_num'));
			$end_hub_icao = $this->security->sanitize_filename($this->input->post('end_hub_icao'));
			//confirmation post data
			$route_path = $this->security->sanitize_filename($this->input->post('route_path'));
			$data['exception'] = '';

			//build the dropdown arrays

			//create hub_array
			$data['hub_array'] = $this->Dispatch_model->get_hub_array();

			//restrict division and max flyable class of aircraft
			$division = '1,2,3,4,5,8,9';
			$limit = $this->session->userdata('rank_id') + 1;
			if ($limit < 1) {
				$limit = 1;
			}
			//create aircraft_array
			$aircraft_data = $this->Dispatch_model->get_aircraft_array($division, $limit);
			$data['aircraft_array'] = $aircraft_data['aircraft_array'];
			$pax_array = $aircraft_data['pax_array'];
			$cargo_array = $aircraft_data['cargo_array'];

			//create hop num array
			$i = 2;
			while ($i <= 10) {
				$data['hop_num_array'][$i] = $i . ' legs';
				$i++;
			}

			//initialise vars
			if ($start_hub_icao == '') {
				$data['start_hub_icao'] = $this->session->userdata('hub');
			} else {
				$data['start_hub_icao'] = $start_hub_icao;
			}

			if ($end_hub_icao == '') {
				$data['end_hub_icao'] = $this->session->userdata('hub');
			} else {
				$data['end_hub_icao'] = $end_hub_icao;
			}

			$data['aircraft_id'] = $aircraft_id;
			$data['hop_num'] = $hop_num;

			//initialise route
			$data['route'] = array();
			$data['route_gen'] = 0;

			//if route accepted, write route into database and redirect
			if ($route_path != '') {

				//unserialise the array

				//echo $route_path.'<br />';

				//echo base64_decode($route_path).'<br />';

				$route_path_array = unserialize(base64_decode($route_path));

				//set group identifier as datetime
				$identifier = $data['gmt_mysql_datetime'];

				$prev_icao = '';
				$i = 0;
				$count = 0;
				foreach ($route_path_array as $row) {
					if ($i > 0 && $prev_icao != $row) {

						//calculate pax and cargo load for each leg
						if (array_key_exists($aircraft_id, $pax_array) && array_key_exists($aircraft_id, $cargo_array)) {
							//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
							$loadout = $this->pirep_fns->get_loadout($pax_array[$aircraft_id], $cargo_array[$aircraft_id]);
							$num_pax = $loadout['pax'];
							$num_cargo = $loadout['cargo'];
						} else {
							$num_pax = 0;
							$num_cargo = 0;
						}

						$user_id = $this->session->userdata('user_id');
						$start_icao = $prev_icao;
						$end_icao = $row['icao'];
						$gcd_km = $this->geocalc_fns->GCDistance($prev_lat, $prev_lon, $row['lat'], $row['lon']);
						$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);
						$aircraft_id = $aircraft_id;
						$passengers = $num_pax;
						$cargo = $num_cargo;
						$dep_time = ''; //not writing this in
						$group_id = $identifier;
						$group_order = $i;
						$created = $data['gmt_mysql_datetime'];

						//array data
						$pirep_assigned_data = array(
							'user_id' => $user_id,
							'start_icao' => $start_icao,
							'end_icao' => $end_icao,
							'gcd' => $gcd_nm,
							'aircraft_id' => $aircraft_id,
							'passengers' => $passengers,
							'cargo' => $cargo,
							'group_id' => $group_id,
							'group_order' => $group_order,
							'created' => $created,
						);

						//insert data
						$this->db->insert('pirep_assigned', $this->db->escape($pirep_assigned_data));

						$count++;
					}

					$prev_icao = $row['icao'];
					$prev_lat = $row['lat'];
					$prev_lon = $row['lon'];

					$i++;
				}

				redirect('dispatch/');

			}

			//if submitted, generate route
			if ($valid == 'true') {
				//generate route
				$data['route'] = array();

				//get the range of the aircraft, max for each hop
				$query = $this->db->query("	SELECT 
											aircraft.id as id,
											aircraft.range_mload as range_mload,
											aircraft.range_mfuel as range_mfuel
											
									FROM aircraft
									
									WHERE aircraft.id = '$aircraft_id'
									
										");

				$aircraft_data = $query->result_array();
				$num_rows = $query->num_rows();

				if ($num_rows == 1) {
					//use the range when fully loaded as the max for our hops
					$range_nm = $aircraft_data['0']['range_mload'];
					$data['range_mload'] = $aircraft_data['0']['range_mload'];
					$data['range_mfuel'] = $aircraft_data['0']['range_mfuel'];
				} else {
					$range_nm = 0;
					$data['range_mload'] = 'Unknown ';
					$data['range_mfuel'] = 'Unknown ';
				}

				//convert range (nm) to km
				$range_km = $this->geocalc_fns->ConvMilesToKilometers($range_nm);

				//from current location, grab lat long
				$query = $this->db->query("	SELECT 
											airports_data.id as id,
											airports_data.lat as latitude,
											airports_data.long as longitude
											
									FROM airports_data
									
									WHERE airports_data.icao = '$start_hub_icao'
									
										");

				$location = $query->result_array();
				$num_rows = $query->num_rows();

				if ($num_rows == 1) {
					//use the range when fully loaded as the max for our hops
					$location_lat = $location['0']['latitude'];
					$location_lon = $location['0']['longitude'];
					$start_lat = $location_lat;
					$start_lon = $location_lon;
				} else {
					$location_lat = 0;
					$location_lon = 0;
				}

				//from destination, grab lat long
				$query = $this->db->query("	SELECT 
											airports_data.id as id,
											airports_data.lat as latitude,
											airports_data.long as longitude
											
									FROM airports_data
									
									WHERE airports_data.icao = '$end_hub_icao'
									
										");

				$location = $query->result_array();
				$num_rows = $query->num_rows();

				if ($num_rows == 1) {
					//use the range when fully loaded as the max for our hops
					$destination_lat = $location['0']['latitude'];
					$destination_lon = $location['0']['longitude'];
				} else {
					$destination_lat = 0;
					$destination_lon = 0;
				}

				//first check that the distance of a single hop (range * num_hops is more than distance start to finish)
				$total_distance = $this->geocalc_fns->GCDistance($location_lat, $location_lon, $destination_lat, $destination_lon);
				$range_distance = $range_km * $hop_num;

				$icao = $start_hub_icao;

				//only if destination is in range of this aircraft
				if ($range_distance >= $total_distance) {

					$round_trip = 0;
					if ($start_hub_icao == $end_hub_icao) {
						$round_trip = 1;
					}

					$route_array = array();
					$route_array['0']['icao'] = $start_hub_icao;
					$route_array['0']['lat'] = $start_lat;
					$route_array['0']['lon'] = $start_lon;

					$i = 1;
					//start loop with start icao
					while ($i <= $hop_num) {

						//query database for these airports, randomise order
						$airport_data = $this->Dispatch_model->get_next_hop($icao, $location_lat, $location_lon, $end_hub_icao, $destination_lat, $destination_lon, $range_km, $hop_num, $i, $round_trip, $route_array);

						if ($airport_data['new_icao'] != '') {

							//add to array
							$route_array[$i]['icao'] = $airport_data['new_icao'];
							$route_array[$i]['lat'] = $airport_data['new_lat'];
							$route_array[$i]['lon'] = $airport_data['new_lon'];

							//feedback into itself
							$icao = $airport_data['new_icao'];
							$location_lat = $airport_data['new_lat'];
							$location_lon = $airport_data['new_lon'];
						} else {
							break;
						}

						if ($i > 1 && $icao == $end_hub_icao) {
							break;
						}

						$i++;
					}

					$data['route_array'] = $route_array;
					$data['route_gen'] = 1;
					//iterate through and pick the first where distance is less than than range - 5%
				} else {

					//aircraft doesn't have range. Fail.
					$data['exception'] .= 'Destination is out of range for this aircraft<br />';

				}

				//if we didn't reach the destination don't display
				if ($icao != $end_hub_icao) {
					$data['route_array'] = array();
					$data['route_gen'] = 0;
					if ($data['exception'] == '') {
						$data['exception'] .= 'While the aircraft has the range to fly this route over the max number of legs, a route all the way to destination could not be created. Please try again.<br />If this issue persists, it may not be possible to use this aircraft on this route due to one or more hops being out of the aircraft range.<br />';
					}
				}

			}

			//if search submitted, query

			$data['page_title'] = 'Dispatch - Flight Routing';

			//output for view
			$data['no_links'] = '1';
			$this->view_fns->view('global/dispatch/dispatch_route', $data);

		} //if not logged in, redirect
		else {
			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/route/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');
		}

	}

	function timetable_map($pp_mode = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//check logged in
		if ($this->session->userdata('logged_in') == 1) {

			$pilot_id = $this->session->userdata('user_id');

			if ($pp_mode != 1) {
				$pp_mode = 0;
			}

			$data['swf'] = 'flightmap';
			$data['height'] = '650';
			$data['flash_vars'] = '&pilotId=' . $pilot_id;
			$data['flash_vars'] .= '&propilot=' . $pp_mode;
			//$data['flash_vars'] .= '&edit=0&create=0&delete=0';
			$data['debug'] = 'Debug=true';

			$data['page_title'] = 'Dispatch - Timetable Map';
			$data['no_links'] = '1';
			$this->view_fns->view('global/swf/swf_loader', $data);
		} else {
			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/timetable_map/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');
		}

	}

	function timetable($division = NULL, $hub_icao = NULL, $class = NULL, $origin = '-', $destination = '-', $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		$this->load->library('pagination');
		$this->load->library('Pirep_fns');
		$this->load->library('Geocalc_fns');
		$this->load->model('Dispatch_model');

		//check logged in
		if ($this->session->userdata('logged_in') == 1) {

			$data['error'] = '';

			if ($hub_icao == NULL || $division == NULL || $class == NULL) {
				redirect('dispatch/timetable/1/EGLL/' . ($this->session->userdata('rank_id') + 1));
			}

			$data['hub_icao'] = $hub_icao;
			$data['division'] = $division;
			$data['class'] = $class;
			$data['origin'] = $origin;
			$data['destination'] = $destination;

			//grab post data
			$post_hub_icao = $this->security->sanitize_filename($this->input->post('hub_icao'));
			$post_division = $this->security->sanitize_filename($this->input->post('division'));
			$post_class = $this->security->sanitize_filename($this->input->post('class'));
			$post_origin = $this->security->sanitize_filename($this->input->post('origin'));
			$post_destination = $this->security->sanitize_filename($this->input->post('destination'));

			//assign post
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$post_aircraft = $this->security->sanitize_filename($this->input->post('aircraft'));
			$post_flight_num = $this->security->sanitize_filename($this->input->post('flight_num'));

			$data['aircraft_id'] = $post_aircraft;

			if ($valid == 'true' && $post_aircraft == '') {
				$data['error'] .= 'You must select an aircraft';
			}

			if ($valid == 'true' && $post_flight_num == '') {
				if ($data['error'] != '') {
					$data['error'] .= '<br />';
				}
				$data['error'] .= 'You must select a flight';
			}

			//handle blank values for search
			if ($post_origin == '') {
				$post_origin = '-';
			}

			if ($post_destination == '') {
				$post_destination = '-';
			}

			if (
				($post_hub_icao != '' && $post_hub_icao != $hub_icao)
				|| ($post_division != '' && $post_division != $division)
				|| ($post_class != '' && $post_class != $class)
				|| ($post_class != '' && $post_origin != $origin)
				|| ($post_class != '' && $post_destination != $destination)
			) {
				redirect('dispatch/timetable/' . $post_division . '/' . $post_hub_icao . '/' . $post_class . '/' . $post_origin . '/' . $post_destination);
			}

			$origin_link = $origin;
			$destination_link = $destination;

			//now convert blanks back to empties
			if ($origin == '-') {
				$origin = '';
				$origin_link = '-';
			}

			if ($destination == '-') {
				$destination = '';
				$destination_link = '-';
			}

			//create class array
			$limit = $this->session->userdata('rank_id') + 1;
			if ($limit < 1) {
				$limit = 1;
			}
			$i = 1;
			$data['class_array'] = array();
			while ($i <= $limit) {
				$data['class_array'][$i] = 'Class ' . $i;
				$i++;
			}
			/*
			if($class > $limit){
				$class = $limit;
			}
			*/
			//create division array
			$data['divisions_array'] = $this->Dispatch_model->get_division_array();

			//create hub_array
			$data['hub_array'] = $this->Dispatch_model->get_hub_array();

			//create aircraft_array
			$aircraft_data = $this->Dispatch_model->get_aircraft_array_restrict($division, $class, $limit);
			$data['aircraft_array'] = $aircraft_data['aircraft_array'];
			$pax_array = $aircraft_data['pax_array'];
			$cargo_array = $aircraft_data['cargo_array'];

			if (!empty($origin) || !empty($destination)) {

				if (!empty($origin) && !empty($destination)) {
					$restrict = "AND (timetable.dep_airport = '$origin'
											AND timetable.arr_airport = '$destination')
                            	";
				} else {
					$restrict = "AND (timetable.dep_airport = '$origin'
											OR timetable.arr_airport = '$destination')
                            	";
				}

				if (!is_numeric($offset)) {
					$restrict .= " AND timetable.division = '$division'
												AND timetable.class = '$class'";
				}

			} else {

				$restrict = "AND (timetable.dep_airport = '$hub_icao'
										OR timetable.arr_airport = '$hub_icao'
										OR hub.hub_icao = '$hub_icao')
                                
                                        AND timetable.division = '$division'
										AND timetable.class = '$class'
                            ";

			}

			//if search submitted, query
			$query = $this->db->query("	SELECT 
											timetable.flightnumber as flightnumber,
											timetable.dep_airport as dep_airport,
											timetable.arr_airport as arr_airport,
											timetable.dep_time as dep_time,
											timetable.arr_time as arr_time,
											timetable.sun as sun,
											timetable.mon as mon,
											timetable.tue as tue,
											timetable.wed as wed,
											timetable.thu as thu,
											timetable.fri as fri,
											timetable.sat as sat,
											timetable.class as clss,
											dep_icao.Country as dep_country,
											arr_icao.Country as arr_country,
											dep_airdata.Name as dep_name,
											arr_airdata.Name as arr_name,
											dep_airdata.lat as dep_lat,
											dep_airdata.long as dep_long,
											arr_airdata.lat as arr_lat,
											arr_airdata.long as arr_long,
											timetable.division as division_id,
											divisions.division_longname as division,
											divisions.prefix as prefix
											
									FROM timetable
									
										LEFT JOIN divisions
										ON divisions.id = timetable.division
										
										LEFT JOIN airports as dep_icao
										ON dep_icao.ICAO = timetable.dep_airport
										
										LEFT JOIN airports_data as dep_airdata
										ON dep_airdata.ICAO = timetable.dep_airport
										
										LEFT JOIN airports as arr_icao
										ON arr_icao.ICAO = timetable.arr_airport
										
										LEFT JOIN airports_data as arr_airdata
										ON arr_airdata.ICAO = timetable.arr_airport
										
										LEFT JOIN hub
										ON hub.id = timetable.hub
									
									WHERE timetable.active = '1'
									
									$restrict
										
									AND (dep_airdata.lat IS NOT NULL AND dep_airdata.long IS NOT NULL
										AND arr_airdata.lat IS NOT NULL AND arr_airdata.long IS NOT NULL)
										
									
									
									ORDER BY timetable.flightnumber
									
											
										");

			$data['timetable_flights'] = $query->result();
			$data['num_flights'] = $query->num_rows();

			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			if ($offset == 'X') {
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '10';

			$pag_config['base_url'] = $data['base_url'] . 'dispatch/timetable/' . $division . '/' . $hub_icao . '/' . $class . '/' . $origin_link . '/' . $destination_link;
			$pag_config['total_rows'] = $data['num_flights'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 8;

			$this->pagination->initialize($pag_config);

			//all ok to assign a flight
			if ($valid == 'true' && $post_flight_num != '' && $post_aircraft != '') {

				//iterate through pulled data to get flight details
				foreach ($data['timetable_flights'] as $row) {
					//only interested in seleted flight
					if ($row->flightnumber == $post_flight_num) {

						//if a passenger flight
						if (array_key_exists($post_aircraft, $pax_array)) {
							//loadout returns an array for passenger and cargo load based on the capacity and type - max_pax :: max_cargo
							$loadout = $this->pirep_fns->get_loadout($pax_array[$post_aircraft], $cargo_array[$post_aircraft]);
							$num_pax = $loadout['pax'];
							$num_cargo = $loadout['cargo'];
						} else {
							$num_pax = 0;
							$num_cargo = 0;
						}

						$gcd_km = $this->geocalc_fns->GCDistance($row->dep_lat, $row->dep_long, $row->arr_lat, $row->arr_long);
						$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

						//insert data
						$insert_array = array(
							'user_id' => $this->session->userdata('user_id'),
							'start_icao' => $row->dep_airport,
							'end_icao' => $row->arr_airport,
							'gcd' => $gcd_nm,
							'aircraft_id' => $post_aircraft,
							'passengers' => $num_pax,
							'cargo' => $num_cargo,
							'dep_time' => $row->dep_time,
							'created' => $data['gmt_mysql_datetime'],
							'event_id' => 0,
							'event_leg_id' => 0,
						);

						//perform the write
						$this->db->insert('pirep_assigned', $this->db->escape($insert_array));

						//redirect
						redirect('dispatch');
					}
				}

			}
			/*
			$data['error'] = '';
			if($post_aircraft == ''){
				$data['error'] .= 'You must select an aircraft';
			}
			if($post_flight_num == ''){
				if($data['error'] != ''){
				$data['error'] .= '<br />';
				}
				$data['error'] .= 'You must select a flight';
			}
			*/

			//form input
			$data['origin_input'] = array('name' => 'origin', 'id' => 'origin', 'value' => $origin, 'maxlength' => '4', 'size' => '4');
			$data['destination_input'] = array('name' => 'destination', 'id' => 'destination', 'value' => $destination, 'maxlength' => '4', 'size' => '4');

			$data['page_title'] = 'Dispatch - Flight Timetable';
			$data['post_flight_num'] = $post_flight_num;

			//output for view

			$data['no_links'] = '1';

			$this->view_fns->view('global/dispatch/dispatch_timetable', $data);

		} //if not logged in, redirect
		else {
			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/timetable/' . $division . '/' . $hub_icao . '/' . $class . '/' . $offset . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');
		}

	}

	function index() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//check logged in
		if ($this->session->userdata('logged_in') == 1) {

			$current_pilot = $this->session->userdata('user_id');
			$current_username = $this->session->userdata('username');

			//grab pilot data and update session if necessary
			$query = $this->db->query("	SELECT 
											pilots.rank as rank_id,
											pilots.pp_location,
											pilots.curr_location,
											ranks.rank as rank_short,
											ranks.name as rank_long
										
										FROM pilots
										
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
										
										WHERE pilots.id = '$current_pilot'
			");

			$rank_data = $query->result_array();

			$sessiondata = array(
				'rank_short' => $rank_data['0']['rank_short'],
				'rank_long' => $rank_data['0']['rank_long'],
				'rank_id' => $rank_data['0']['rank_id'],
				'pp_location' => $rank_data[0]['pp_location'],
				'curr_location' => $rank_data[0]['curr_location'],
			);

			$this->session->set_userdata($sessiondata);

			//grab all assigned flights		
			$query = $this->db->query("	SELECT 
											pirep_assigned.id as id,
											pirep_assigned.user_id as user_id,
											aircraft.name as aircraft,
											pirep_assigned.passengers as passengers,
											pirep_assigned.cargo as cargo,
											pirep_assigned.dep_time as dep_time,
											pirep_assigned.start_icao as start_icao,
											pirep_assigned.end_icao as end_icao,
											pirep_assigned.gcd as gcd,
											pirep_assigned.group_id as group_id,
											pirep_assigned.group_order as group_order,
											pirep_assigned.created as created,
											pirep_assigned.tour_id as tour_id,
											pirep_assigned.event_id as event_id,
											pirep_assigned.mission_id as mission_id,
											tour_index.name as tour_name,
											propilot_event_index.name as event_name,
											pirep_assigned.fs_version as fs_version,
											pirep_assigned.award_completion as award_completion,
											pirep_assigned.award_id as award_id,
											dep_icao.name as dep_name,
											arr_icao.name as arr_name
											
									FROM pirep_assigned
									
										LEFT JOIN aircraft
										ON aircraft.id = pirep_assigned.aircraft_id
										
										LEFT JOIN airports_data as dep_icao
										ON dep_icao.ICAO = pirep_assigned.start_icao
										
										LEFT JOIN airports_data as arr_icao
										ON arr_icao.ICAO = pirep_assigned.end_icao
										
										LEFT JOIN tour_index
										ON tour_index.id = pirep_assigned.tour_id
										
										LEFT JOIN propilot_event_index
										ON propilot_event_index.id = pirep_assigned.event_id
									
									WHERE pirep_assigned.user_id = '$current_pilot'
									
									ORDER BY pirep_assigned.group_id, pirep_assigned.group_order, pirep_assigned.created
									
											
										");

			$data['assigned_flights'] = $query->result();

			//grab propilot flight
			$query = $this->db->query("	SELECT 
											propilot_aircraft.id as id,
											propilot_aircraft.reserved_by as user_id,
											aircraft.name as aircraft,
											propilot_aircraft.tail_id,
											propilot_aircraft.pax as passengers,
											propilot_aircraft.cargo as cargo,
											propilot_aircraft.location as start_icao,
											propilot_aircraft.destination as end_icao,
											propilot_aircraft.gcd as gcd,
											propilot_aircraft.last_flown as last_flown,
											dep_icao.name as dep_name,
											arr_icao.name as arr_name
											
									FROM propilot_aircraft
									
										LEFT JOIN aircraft
										ON aircraft.id = propilot_aircraft.aircraft_id
										
										LEFT JOIN airports_data as dep_icao
										ON dep_icao.ICAO = propilot_aircraft.location
										
										LEFT JOIN airports_data as arr_icao
										ON arr_icao.ICAO = propilot_aircraft.destination
												
										LEFT JOIN pilots 
										ON propilot_aircraft.reserved_by = pilots.id
									
									WHERE propilot_aircraft.reserved_by = '$current_pilot'
									AND (propilot_aircraft.reserved IS NOT NULL 
										AND propilot_aircraft.reserved != '' 
										AND propilot_aircraft.reserved != '0000-00-00 00:00:00' 
										AND propilot_aircraft.reserved >= '$pp_compare_date')
									AND propilot_aircraft.state_id = '1'
									AND pilots.pp_location = propilot_aircraft.location
									
									ORDER BY propilot_aircraft.reserved
									
											
										");

			$data['propilot_flights'] = $query->result();

			//grab all approval pending flights		
			$query = $this->db->query("	SELECT 
											pirep.id as id,
											aircraft.name as aircraft,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.submitdate as submitdate,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name,
											pirep.checked as checked
											
									FROM pirep
									
										LEFT JOIN aircraft
										ON aircraft.id = pirep.aircraft
										
										LEFT JOIN airports as dep_icao
										ON dep_icao.ICAO = pirep.start_icao
										
										LEFT JOIN airports as arr_icao
										ON arr_icao.ICAO = pirep.end_icao
									
									WHERE pirep.username = '$current_username'
									AND (pirep.checked = '0'
									OR pirep.checked = '3'
									OR pirep.checked = '4')
									
									ORDER BY pirep.submitdate
									
											
										");

			$data['unchecked_flights'] = $query->result();
			$data['num_unchecked'] = $query->num_rows();

			$data['page_title'] = 'Dispatch';

			$data['no_links'] = '1';
			$this->view_fns->view('global/dispatch/dispatch_index', $data);

		} //if not logged in, redirect
		else {
			//handle the previous page writer
			$sessiondata['return_page'] = 'dispatch/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/login');
		}
	}
}

/* End of file */
