<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pirep_model extends CI_Model {

	function deadhead_pilots($origin, $destination) {

		//update all pilots to the destination who are at the origin, where the destination is their 'final destination' or they are flagged for circuitous route.
		$data = array(
			'pp_location' => $destination,
			'curr_location' => $destination,
		);

		$where = "	(deadhead_dest = '$destination' AND pp_location = '$origin')
		
					OR
						(
						deadhead_direct = '0' 
						AND pp_location = '$origin' 
						AND deadhead_dest != '$origin'
						AND deadhead_dest IS NOT NULL 
						AND deadhead_dest != ''
						)
						
					";

		$this->db->where($where, NULL, FALSE);
		$this->db->update('pilots', $data);

		//now, clear deadhead data for pilots who are at their destination
		$data = array(
			'deadhead_dest' => NULL,
			'deadhead_direct' => '1',
			'deadhead_set' => NULL,
		);

		$this->db->where('deadhead_dest', $destination);
		$this->db->where('pp_location', $destination);
		$this->db->update('pilots', $data);

		if ($this->db->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

	function tour_award($pilot_id = '', $tour_id = '', $award_id = '', $p_gmt_mysql_datetime) {

		if ($pilot_id == '' || !is_numeric($pilot_id) || $pilot_id < 1
			|| $tour_id == '' || !is_numeric($tour_id) || $tour_id < 1
			|| $award_id == '' || !is_numeric($award_id) || $award_id < 1
		) {
			return 'fail data';
		} else {
			//pull all flights for tour that have been flown by the pilot
			$query = $this->db->query("	SELECT 	
											pirep.id,
											pirep.tour_leg_id,
											pirep.submitdate
												
										FROM pirep
										
										WHERE pirep.user_id = '$pilot_id'
										AND pirep.tour_id = '$tour_id'
										
										ORDER BY pirep.submitdate
									");

			$pilot_result = $query->result();
			$num_results = $query->num_rows();

			//ensure the selected tour actually has legs
			if ($num_results > 0) {

				$flown_legs_array = array();
				$stop = 0;
				//iterate up to the tour_leg with the award_id
				foreach ($pilot_result as $row) {

					$flown_legs_array[$row->tour_leg_id] = $row->tour_leg_id;

					//echo 'tour_leg_id: '.$row->tour_leg_id.'<br />';

				}

				//grab the legs of the tour
				$query = $this->db->query("	SELECT 	
											tour_legs.id,
											tour_legs.sequence,
											tour_legs.award_id
												
										FROM tour_legs
										
										WHERE tour_legs.tour_id = '$tour_id'
										
										ORDER BY tour_legs.sequence, tour_legs.id
									");

				$result = $query->result();
				$num_results = $query->num_rows();
				$success = 1;
				$stop = 0;
				if ($num_results > 0) {
					//echo '<br /><br />';
					//iterate up to the tour_leg with the award_id
					foreach ($result as $row) {

						//only process if the selected award hasn't been reached or is an award leg
						if ($stop == 0 || ($row->award_id != '' && $row->award_id == $award_id)) {
							//add to array indexed by the sequence. Have to do this to enable checking if any valid leg has been flown (multi-sim)
							$tour_array[$row->sequence][$row->id] = $row->id;
						}

						//check if award reached for stop value
						if ($row->award_id != '' && $row->award_id == $award_id) {
							$stop = 1;
						}
					}

					//check each leg for a match on one of the possible flights
					$overall_success = 1;
					foreach ($tour_array as $row) {

						//check every possible flight for at least one match
						$success = 0;
						foreach ($row as $flight) {

							if (array_key_exists($flight, $flown_legs_array)) {
								$success = 1;
							}
						}
						if ($success == 0) {
							$overall_success = 0;
						}

					}

					//if so, assign tour award and return true
					if ($overall_success == 1) {

						//assign tour award

						//array data for insert only if the pilot doesn't have it

						$query = $this->db->query("	SELECT 	
											awards_assigned.id
												
										FROM awards_assigned
										
										WHERE awards_assigned.awards_index_id = '$award_id'
										AND awards_assigned.user_id = '$pilot_id'
										
										LIMIT 1
									");

						$result = $query->result();
						$num_results = $query->num_rows();

						if ($num_results < 1) {

							$award_data = array(
								'user_id' => $pilot_id,
								'awards_index_id' => $award_id,
								'notes' => 'Tour award automatically assigned',
								'assigned_date' => $p_gmt_mysql_datetime,
							);

							//insert
							$this->db->insert('awards_assigned', $this->db->escape($award_data));

							return TRUE;
						} else {
							return 'fail: already assigned';
						}
					} else {
						//else return false
						return 'fail assign';
					}
				} else {
					return 'fail num_results tour legs';;
				}
			} else {
				return 'fail num_results pilot legs';
			}

		}

	}

	function mission_award($pilot_id, $mission_id = '') {

		if ($pilot_id == '' || !is_numeric($pilot_id) || $pilot_id < 1
			|| $mission_id == '' || !is_numeric($mission_id) || $mission_id < 1
		) {
			return FALSE;
		} else {

			//grab the dates from the mission
			$query = $this->db->query("	SELECT 	
											mission_index.id,
											mission_index.start_date,
											mission_index.end_date,
											mission_index.division
												
										FROM mission_index
										
										WHERE mission_index.id = '$mission_id'
										
										LIMIT 1
									");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($num_results > 0) {
				//valid mission_id

				$start_date = $result[0]['start_date'];
				$end_date = $result[0]['end_date'];
				$division = $result[0]['division'];

				//grab all missions with those dates in the same division from db, and join them with pireps from the pilot
				$query = $this->db->query("	SELECT 	
											mission_index.id,
											mission_index.start_date,
											mission_index.end_date,
											pirep.user_id
												
										FROM mission_index
										
											LEFT JOIN pirep
											ON pirep.mission_id = mission_index.id
											AND pirep.user_id = '$pilot_id'
										
										WHERE mission_index.start_date = '$start_date'
										AND mission_index.end_date = '$end_date'
										AND mission_index.division = '$division'
										
									");

				$result = $query->result();
				$num_results = $query->num_rows();

				//check pilot has all missions flown
				$success = 1;
				foreach ($result as $row) {
					if ($row->user_id == '' || is_null($row->user_id)) {
						$success = 0;
					}
				}

				//if so, assign division mission award and return true
				if ($success == 1) {

					$award_id = '';
					$data['gmt_mysql_datetime'] = gmdate("Y-m-d H:i:s", time());

					//lack support for later division awards.
					switch ($division) {
						//Business
						case '2':
							//set award id
							$award_id = '30';
							break;

						//Cargo
						case '3':
							//set award id
							$award_id = '35';
							break;

						//holidays
						case '4':
							//set award id
							$award_id = '41';
							break;

						//wild
						case '8':
							//set award id
							$award_id = '58';
							break;

						//hopper
						case '9':
							//set award id
							$award_id = '59';
							break;
					}

					//check to see if the pilot already has the award, if so, do nothing.
					$already_awarded = 1;
					if ($award_id != '' && $pilot_id != '') {
						//db query
						$query = $this->db->query("	SELECT 	
											awards_assigned.id
												
										FROM awards_assigned
										
										WHERE awards_assigned.user_id = '$pilot_id'
										AND awards_assigned.awards_index_id = '$award_id'
										
									");

						$result = $query->result();
						$num_results = $query->num_rows();

						if ($num_results < 1) {
							$already_awarded = 0;
						}
					}

					if ($already_awarded != 1) {

						//array data
						$award_data = array(
							'user_id' => $pilot_id,
							'awards_index_id' => $award_id,
							'notes' => 'Tour award automatically assigned',
							'assigned_date' => $data['gmt_mysql_datetime'],
						);

						if ($award_id != '') {
							//insert data
							$this->db->insert('awards_assigned', $this->db->escape($award_data));

							return TRUE;
						} else {
							return 'Fail: No award_id set';
						}
					} else {
						//else return false
						return 'Fail: Pilot already has award';
					}

				} //else return false
				else {
					return 'Fail: Not all missions in batch flown';
				}

			} else {
				return 'Fail: Invalid mission_id';
			}

		}

	}

	function event_award($pilot_id = '', $event_id = '', $award_id = '', $p_gmt_mysql_datetime) {

		if ($pilot_id == '' || !is_numeric($pilot_id) || $pilot_id < 1
			|| $event_id == '' || !is_numeric($event_id) || $event_id < 1
			|| $award_id == '' || !is_numeric($award_id) || $award_id < 1
		) {
			return 'fail data';
		} else {
			//pull all flights for event that have been flown by the pilot
			$query = $this->db->query("	SELECT 	
											pirep.id,
											pirep.event_leg_id,
											pirep.submitdate
												
										FROM pirep
										
										WHERE pirep.user_id = '$pilot_id'
										AND pirep.event_id = '$event_id'
										
										ORDER BY pirep.submitdate
									");

			$pilot_result = $query->result();
			$num_results = $query->num_rows();

			//ensure the selected tour actually has legs
			if ($num_results > 0) {

				$flown_legs_array = array();
				$stop = 0;
				//iterate up to the tour_leg with the award_id
				foreach ($pilot_result as $row) {

					$flown_legs_array[$row->event_leg_id] = $row->event_leg_id;

					//echo 'tour_leg_id: '.$row->tour_leg_id.'<br />';

				}

				//grab the legs of the event
				$query = $this->db->query("	SELECT 	
											propilot_event_legs.id,
											propilot_event_legs.sequence,
											propilot_event_legs.award_id
												
										FROM propilot_event_legs
										
										WHERE propilot_event_legs.event_id = '$event_id'
										
										ORDER BY propilot_event_legs.sequence, propilot_event_legs.id
									");

				$result = $query->result();
				$num_results = $query->num_rows();
				$success = 1;
				$stop = 0;
				if ($num_results > 0) {
					//echo '<br /><br />';
					//iterate up to the event_leg with the award_id
					foreach ($result as $row) {

						//only process if the selected award hasn't been reached or is an award leg
						if ($stop == 0 || ($row->award_id != '' && $row->award_id == $award_id)) {
							//add to array indexed by the sequence. Have to do this to enable checking if any valid leg has been flown (multi-sim)
							$event_array[$row->sequence][$row->id] = $row->id;
						}

						//check if award reached for stop value
						if ($row->award_id != '' && $row->award_id == $award_id) {
							$stop = 1;
						}
					}

					//check each leg for a match on one of the possible flights
					$overall_success = 1;
					foreach ($event_array as $row) {

						//check every possible flight for at least one match
						$success = 0;
						foreach ($row as $flight) {

							if (array_key_exists($flight, $flown_legs_array)) {
								$success = 1;
							}
						}
						if ($success == 0) {
							$overall_success = 0;
						}

					}

					//if so, assign event award and return true
					if ($overall_success == 1) {

						//assign event award

						//array data for insert only if the pilot doesn't have it

						$query = $this->db->query("	SELECT 	
											awards_assigned.id
												
										FROM awards_assigned
										
										WHERE awards_assigned.awards_index_id = '$award_id'
										AND awards_assigned.user_id = '$pilot_id'
										
										LIMIT 1
									");

						$result = $query->result();
						$num_results = $query->num_rows();

						if ($num_results < 1) {

							$award_data = array(
								'user_id' => $pilot_id,
								'awards_index_id' => $award_id,
								'notes' => 'Event award automatically assigned',
								'assigned_date' => $p_gmt_mysql_datetime,
							);

							//insert
							$this->db->insert('awards_assigned', $this->db->escape($award_data));

							return TRUE;
						} else {
							return 'fail: already assigned';
						}
					} else {
						//else return false
						return 'fail assign';
					}
				} else {
					return 'fail num_results event legs';;
				}
			} else {
				return 'fail num_results pilot legs';
			}

		}

	}

	function update_hours($pilot_id = NULL, $current_rank = NULL, $sess = NULL) {

		//deal with NULLS
		if ($pilot_id == NULL || !is_numeric($pilot_id)) {
			//do nothing
			return FALSE;

		} else {

			//we need to update the pilot' hours
			//grab sum of pilots blocktime from database where pirep is approved.
			$query = $this->db->query("	SELECT 	
								pirep.user_id as user_id,
								SUM(blocktime_mins) as total_mins
										
								FROM pirep
								
								WHERE pirep.user_id = '$pilot_id'
								AND pirep.checked = '1'
								
								GROUP BY pirep.user_id
							");

			$result = $query->result_array();
			$num_results = $query->num_rows();

			if ($num_results > 0) {
				$total_mins = $result['0']['total_mins'];
			} else {
				$total_mins = 0;
			}

			//convert to hours
			$total_mm = $total_mins % 60;

			$subt = ($total_mins - $total_mm);

			if ($subt > 0) {
				$total_hh = $subt / 60;
			} else {
				$total_hh = 0;
			}

			//grab the rank that we should be at these hours
			$query = $this->db->query("	SELECT 	ranks.id as id,
											ranks.rank as rank,
											ranks.name as name,
											ranks.hours as hours,
											ranks.stats_order as stats_order,
											ranks.class as clss
											
									FROM ranks
									
									WHERE ranks.hours <= $total_hh
									
									ORDER BY ranks.hours DESC
									
									LIMIT 1
											
										");

			$rank_data = $query->result_array();
			$num_results = $query->num_rows();

			if ($num_results == 1) {

				$new_rank_id = $rank_data['0']['id'];
				$new_rank_short = $rank_data['0']['rank'];
				$new_rank_long = $rank_data['0']['name'];
				//check against expected rank_id
				if ($new_rank_id != $current_rank) {
					$pilots_data['rank'] = $new_rank_id;
				}
			}

			// update pilot hours
			$pilots_data['flighthours'] = $total_hh;
			$pilots_data['flightmins'] = $total_mm;

			//perform the update from db
			$this->db->where('id', $pilot_id);
			$this->db->update('pilots', $this->db->escape($pilots_data));

			if ($sess != '0') {
				//now update the session data
				$sessiondata = array(
					'rank_short' => $new_rank_short,
					'rank_long' => $new_rank_long,
					'rank_id' => $new_rank_id,
				);

				//set data in session
				$this->session->set_userdata($sessiondata);

			}

			//if promoted insert into news table **************************************************************************
			if (!empty($current_rank) && $new_rank_id > $current_rank) {
				//get the id of the oldest entry
				$this->db->select('id');
				$this->db->order_by("promoted", "asc");
				$this->db->limit(1);
				$query = $this->db->get('pilots_promotion');

				$promoted_data = $query->result_array();
				$num_results = $query->num_rows();

				if ($num_results == 1) {

					$promoted_id = $promoted_data['0']['id'];

					$data['gmt_mysql_datetime'] = gmdate("Y-m-d H:i:s", time());
					//update oldest entry
					$pilots_promotion_data = array(
						'pilots_id' => $pilot_id,
						'rank_id' => $new_rank_id,
						'promoted' => $data['gmt_mysql_datetime'],
					);

					//perform update
					$this->db->where('id', $promoted_id);
					$this->db->update('pilots_promotion', $this->db->escape($pilots_promotion_data));
				}

				return TRUE;

			} else {
				return FALSE;
			}

		}

	}

	function get_countries() {

		//now grab all the countries
		$query = $this->db->query("	SELECT 	countries.Country AS country,
											countries.Name AS name
											
									FROM countries								
											
									ORDER BY Name, Country
											
										");

		$country_list = $query->result();

		$data = array('' => '');

		foreach ($country_list as $row) {

			$data[$row->country] = substr($row->name, 0, 30);

		}

		return $data;

		//close get_countries
	}

	function get_hubs($nospacer = '1', $short = '0', $connection = '0') {

		if ($connection == '1') {
			$conn = "";
		} else {
			$conn = "WHERE hub.connection_centre = '0'";
		}

		//now grab all the countries
		$query = $this->db->query("	SELECT 	hub.id as id,
											hub.hub_name as hub_name,
											hub.hub_icao as hub_icao,
											countries.name as country
											
									FROM hub						
									
											LEFT JOIN airports
											on airports.icao = hub.hub_icao
											
											LEFT JOIN countries
											on airports.country = countries.country		
											
									$conn
											
									ORDER BY hub.hub_icao, hub.hub_name
											
										");

		$hub_list = $query->result();

		if ($nospacer == '0') {
			$data = array();
		} elseif ($nospacer == '1') {
			$data = array('' => '');
		} else {
			$data = array('ALL' => 'All');
		}

		foreach ($hub_list as $row) {
			if ($short == '1') {
				$data[$row->id] = $row->hub_icao . ' ' . $row->hub_name;
			} else {
				$data[$row->id] = $row->hub_icao . ' ' . $row->hub_name . ' (' . $row->country . ')';
			}

		}

		return $data;

		//close get_hubs
	}

	function get_aircraft($rank_id = 1, $nochopper = 0, $noprop = 0, $nojet = 0) {

		$add_restict = ' ';

		if ($nochopper == 1) {
			$add_restict .= "AND aircraft.aircraft_type != 'H' ";
		}

		if ($noprop == 1) {
			$add_restict .= "AND aircraft.aircraft_type != 'P' ";
		}

		if ($nojet == 1) {
			$add_restict .= "AND aircraft.aircraft_type != 'J' ";
		}

		//now grab all the countries
		$query = $this->db->query("	SELECT 	aircraft.id as id,
											divisions.division_longname as division,
											aircraft.name as name
											
									FROM aircraft		
									
										LEFT JOIN divisions
										ON divisions.id = aircraft.division
									
									WHERE aircraft.clss <= $rank_id
									AND aircraft.in_fleet = '1'
									AND aircraft.division != '5'
									AND aircraft.division != '7'
									$add_restict
											
									ORDER BY aircraft.division, aircraft.rank, aircraft.name
											
										");

		$aircraft_list = $query->result();

		$data = array('' => array(' ' => ' '));

		foreach ($aircraft_list as $row) {

			$data[$row->division][$row->id] = $row->name;

		}

		return $data;

		//close get_aircraft
	}

	function get_status() {

		$query = $this->db->query("	SELECT 	status.id AS id,
											status.name AS name
											
									FROM status
											
									ORDER BY status.id
											
										");

		$sim_list = $query->result();
		//$data[''] = array('' => '');
		foreach ($sim_list as $row) {
			$data[$row->id] = $row->name;
		}

		return $data;

		//close get_status
	}

	function get_locations() {

		$query = $this->db->query("	SELECT 	airports.icao AS icao,
											airports.name AS name,
											countries.name AS country
											
									FROM airports
									
										LEFT JOIN countries
										ON countries.country = airports.country
											
									ORDER BY airports.icao
											
										");

		$sim_list = $query->result();
		//$data[''] = array('' => '');
		foreach ($sim_list as $row) {
			$data[$row->icao] = $row->icao . ' ' . $row->name . ' (' . $row->country . ')';
		}

		return $data;

		//close get_locations
	}

	function get_locations_full() {

		$query = $this->db->query("	SELECT 	airports_data.icao AS icao,
											airports_data.name AS name
											
									FROM airports_data
											
									ORDER BY airports_data.icao
											
										");

		$sim_list = $query->result();

		foreach ($sim_list as $row) {
			$data[$row->icao] = $row->icao . ' ' . $row->name;
		}

		return $data;

		//close get_locations_full
	}

	function get_flightsims() {

		$query = $this->db->query("	SELECT 	flight_sim_versions.id AS id,
											flight_sim_versions.version_number AS version_number,
											flight_sim_versions.version_name  AS version_name,
											flight_sim_series.name AS series_name,
											flight_sim_series.supported AS supported,
											flight_sim_series.display AS display
											
									FROM flight_sim_versions
									
										LEFT JOIN flight_sim_series
										ON flight_sim_versions.series_id = flight_sim_series.id
											
									ORDER BY flight_sim_versions.version_number
											
										");

		$sim_list = $query->result();
		//$data[''] = array('' => '');
		foreach ($sim_list as $row) {
			$data[$row->series_name][$row->id] = $row->version_name;
		}

		$data['Other'][0] = 'Other';

		return $data;

		//close get_flightsims
	}

	function get_flightsims_raw() {

		$query = $this->db->query("	SELECT 	flight_sim_versions.id AS id,
											flight_sim_versions.version_number AS version_number,
											flight_sim_versions.version_name  AS version_name,
											flight_sim_series.name AS series_name,
											flight_sim_series.supported AS supported,
											flight_sim_series.display AS display
											
									FROM flight_sim_versions
									
										LEFT JOIN flight_sim_series
										ON flight_sim_versions.series_id = flight_sim_series.id
											
									ORDER BY flight_sim_series.name, flight_sim_versions.version_number
											
										");

		$data[''] = 'Generic';
		$sim_list = $query->result();
		//$data[''] = array('' => '');
		foreach ($sim_list as $row) {
			$data[$row->id] = $row->version_name;
		}

		$data[0] = 'Other';

		return $data;

		//close get_flightsims
	}

	function assign_hub($hub_1 = NULL, $hub_2 = NULL, $hub_3 = NULL, $active_compare_date = NULL) {

		if ($active_compare_date == NULL) {
			$active_compare_date = date('Y-m-d', strtotime('-1 month'));
		}

		//set defaults in case call fails
		if ($hub_1 != NULL && is_numeric($hub_1)) {
			$hub['id'] = $hub_1;
		} elseif ($hub_2 != NULL && is_numeric($hub_2)) {
			$hub['id'] = $hub_2;
		} elseif ($hub_3 != NULL && is_numeric($hub_3)) {
			$hub['id'] = $hub_3;
		}

		//echo 'hub1: '.$hub_1.' hub2: '.$hub_2.' hub3: '.$hub_3.' hub_id: '.$hub['id'].' Compare_date: '.$active_compare_date.'<br />';

		//ensure hubs are all given 
		if (is_numeric($hub_1) && is_numeric($hub_2) && is_numeric($hub_3)) {

			$hub_list = $hub_1 . ',' . $hub_2 . ',' . $hub_3;

			//count populations of supplied hubs
			$query = $this->db->query("	SELECT 	COUNT(pilots.id) as num_hub,
												pilots.hub as hub,
												hub.hub_icao as hub_icao
												
										FROM hub
										
											LEFT JOIN pilots
											ON hub.id = pilots.hub
										
										WHERE pilots.hub IN($hub_list)
										AND pilots.lastflight > '$active_compare_date'
										
										GROUP BY pilots.hub
										
										");

			$hub_count = $query->result();

			//loop through and weight scores before picking
			$win_score = '-1';
			$i = 0;
			foreach ($hub_count as $row) {

				if ($row->hub == $hub_1) {
					$score = 25 * $row->num_hub;
				} elseif ($row->hub == $hub_2) {
					$score = 50 * $row->num_hub;
				} elseif ($row->hub == $hub_3) {
					$score = 100 * $row->num_hub;
				}

				$hub_choice_id = $row->hub;
				$hub_icao = $row->hub_icao;

				//current winner
				if ($win_score == '-1') {
					$winner = $row->hub;
					$winner_icao = $row->hub_icao;
					$win_score = $score;
				} else {

					if ($score < $win_score) {
						$winner = $row->hub;
						$winner_icao = $row->hub_icao;
						$win_score = $score;
					}

				}

				//$hubset[$hub_choice]['hub_choice'] = $hub_choice;
				//$hubset[$hub_choice]['hub'] = $row->hub;
				//$hubset[$hub_choice]['num_hub'] = $row->num_hub;
				//$hubset[$hub_choice]['score'] = $row->num_hub;
				$i++;
			}

			if ($i > 0) {
				$hub['id'] = $winner;
				$hub['icao'] = $winner_icao;
			} else {
				$hub_id = $hub['id'];

				$query = $this->db->query("	SELECT 	
												
												hub.hub_icao as hub_icao
												
										FROM hub
										
										WHERE hub.id = '$hub_id'
										
										LIMIT 1
										
										");

				$hub_data = $query->result_array();

				$hub['icao'] = $hub_data['0']['hub_icao'];
			}

		} else {
			$hub['id'] = 0;
			$hub['icao'] = 'EGLL';
		}

		//echo '$hub["id"]: '.$hub['id'].' $hub["icao"]: '.$hub['icao'].'<br />';

		return $hub;

		//close assign_hub
	}

//close class
}

?>
