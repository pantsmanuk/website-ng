<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron_model extends CI_Model {

	function award_european_award() {

		//45 countries in europe. Flag added in database. This query really pushed my understanding of UNION ALL. Got the bastard in the end.

		$query = $this->db->query("SELECT 		aaa.id,
												aaa.username,
												COUNT(DISTINCT aaa.europe) AS european,
												aaa.b1 AS blue_award,
												aaa.b2 AS silver_award,
												aaa.b3 AS cobalt_award
			
												FROM 
												
												(
												
												   SELECT 	pilots.id AS id,
															pilots.username AS username,
															blue.awards_index_id AS b1,
															silver.awards_index_id AS b2,
															cobalt.awards_index_id AS b3,
															countries.country AS europe
															
												   FROM pirep 
												
													LEFT JOIN pilots
													ON pilots.id = pirep.user_id
												
													LEFT JOIN awards_assigned AS blue
													ON blue.user_id = pilots.id
													AND blue.awards_index_id = '1'
													
													LEFT JOIN awards_assigned AS silver
													ON silver.user_id = pilots.id
													AND silver.awards_index_id = '2'
													
													LEFT JOIN awards_assigned AS cobalt
													ON cobalt.user_id = pilots.id
													AND cobalt.awards_index_id = '3'
													
													LEFT JOIN airports
													ON airports.ICAO = pirep.start_icao
												   
												   	LEFT JOIN countries
													ON airports.Country = countries.country
													AND countries.europe = '1'
													
													WHERE (pilots.status = '0'
														OR pilots.status = '1')
													
													
												   UNION ALL
												   
												   
												   SELECT 	pilots.id AS id,
															pilots.username AS username,
															blue.awards_index_id AS b1,
															silver.awards_index_id AS b2,
															cobalt.awards_index_id AS b3,
															countries.country AS europe
												   
												   FROM pirep
												   
												   	LEFT JOIN pilots
													ON pilots.id = pirep.user_id
													
													LEFT JOIN awards_assigned AS blue
													ON blue.user_id = pilots.id
													AND blue.awards_index_id = '1'
													
													LEFT JOIN awards_assigned AS silver
													ON silver.user_id = pilots.id
													AND silver.awards_index_id = '2'
													
													LEFT JOIN awards_assigned AS cobalt
													ON cobalt.user_id = pilots.id
													AND cobalt.awards_index_id = '3'
												   
												   	LEFT JOIN airports
													ON airports.ICAO = pirep.end_icao
												   
												   	LEFT JOIN countries
													ON airports.Country = countries.country
													AND countries.europe = '1'
													
													WHERE (pilots.status = '0'
														OR pilots.status = '1')
													
													
												) aaa
												
												
												
												
												GROUP BY aaa.id
												ORDER BY aaa.id
												
												
												");

		$result = $query->result();
		$num_rows = $query->num_rows();

		if ($num_rows > 0) {

			$date_val = gmdate('Y-m-d', time());

			$i = 0;
			//here we would iterate through the pilots and insert missing awards
			foreach ($result as $row) {

				//echo 'Pilot: EHM-'.$row->username.'<br />';
				//echo 'Aircraft: '.$row->aircraft.'<br />';

				if ($row->european >= 10 && empty($row->blue_award)
					|| $row->european >= 20 && empty($row->silver_award)
					|| $row->european >= 40 && empty($row->cobalt_award)
				) {

					echo 'Pilot: EHM-' . $row->username . '<br />';
					echo 'European Award. Countries: ' . $row->european . '<br />';
					echo 'Blue: ' . $row->blue_award . '<br />';
					echo 'Silver: ' . $row->silver_award . '<br />';
					echo 'Cobalt: ' . $row->cobalt_award . '<br />';
				}

				if ($row->european >= 10 && empty($row->blue_award)) {
					//assign the blue award
					echo 'Blue award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '1',
						'type' => 'blueeuropean',
						'notes' => 'Awarded when you have visited at least 10 different European countries (departuredestination) during your reported flights',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->european >= 20 && empty($row->silver_award)) {
					//assign the blue award
					echo 'Silver award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '2',
						'type' => 'silvereuropean',
						'notes' => 'Awarded when you have visited at least 20 different European countries (departuredestination) during your reported flights',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->european >= 40 && empty($row->cobalt_award)) {
					//assign the blue award
					echo 'Cobalt award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '3',
						'type' => 'cobalteuropean',
						'notes' => 'Awarded when you have visited at least 40 different European countries (departuredestination) during your reported flights',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->european >= 10 && empty($row->blue_award)
					|| $row->european >= 20 && empty($row->silver_award)
					|| $row->european >= 40 && empty($row->cobalt_award)
				) {
					echo '<br />';
				}

			}

			if ($i == 0) {
				echo 'No pilots need european award.';
			}

		} else {
			echo 'No pilots meet european award.';
		}

		//close function
	}

	function award_online_award() {

		//pull all active / inactive pilots with greater than 10 missions who do not already have the blue award (13).

		$query = $this->db->query("SELECT 		pilots.id,
												pilots.username,
												SUM(pirep.blocktime_mins) AS minutes,
												blue.awards_index_id AS blue_award,
												silver.awards_index_id AS silver_award,
												cobalt.awards_index_id AS cobalt_award
			
												FROM pirep 
												
													LEFT JOIN pilots
													ON pilots.id = pirep.user_id
												
													LEFT JOIN awards_assigned AS blue
													ON blue.user_id = pilots.id
													AND blue.awards_index_id = '16'
													
													LEFT JOIN awards_assigned AS silver
													ON silver.user_id = pilots.id
													AND silver.awards_index_id = '17'
													
													LEFT JOIN awards_assigned AS cobalt
													ON cobalt.user_id = pilots.id
													AND cobalt.awards_index_id = '18'
													
												
												WHERE (pilots.status = '0'
														OR pilots.status = '1')
												AND (pirep.onoffline = '1' OR pirep.onoffline = '2')
												
												GROUP BY pilots.id
												
												ORDER BY pilots.id
												
												");

		$result = $query->result();
		$num_rows = $query->num_rows();

		if ($num_rows > 0) {

			$date_val = gmdate('Y-m-d', time());

			$i = 0;
			//here we would iterate through the pilots and insert missing awards
			foreach ($result as $row) {

				//echo 'Pilot: EHM-'.$row->username.'<br />';
				//echo 'Aircraft: '.$row->aircraft.'<br />';

				if ($row->minutes > 0 && empty($row->blue_award)
					|| $row->minutes >= 600 && empty($row->silver_award)
					|| $row->minutes >= 1500 && empty($row->cobalt_award)
				) {
					echo 'Pilot: EHM-' . $row->username . '<br />';
					echo 'Online Award<br />';
				}

				if ($row->minutes > 0 && empty($row->blue_award)) {
					//assign the blue award
					echo 'Blue award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '16',
						'type' => 'blueonline',
						'notes' => 'Awarded when you have flown at least 1 flight on the VATSIM andor IVAO networks using a EuroHarmony callsign and flying EuroHarmony scheduled flights.',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->minutes >= 600 && empty($row->silver_award)) {
					//assign the blue award
					echo 'Silver award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '17',
						'type' => 'silveronline',
						'notes' => 'Awarded when you have flown at least 10 hours on the VATSIM andor IVAO networks using a EuroHarmony callsign and flying EuroHarmony scheduled flights.',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->minutes >= 1500 && empty($row->cobalt_award)) {
					//assign the blue award
					echo 'Cobalt award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '18',
						'type' => 'cobaltonline',
						'notes' => 'Awarded when you have flown at least 25 hours on the VATSIM andor IVAO networks using a EuroHarmony callsign and flying EuroHarmony scheduled flights.',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->minutes > 0 && empty($row->blue_award)
					|| $row->minutes >= 600 && empty($row->silver_award)
					|| $row->minutes >= 1500 && empty($row->cobalt_award)
				) {
					echo '<br />';
				}

			}

			if ($i == 0) {
				echo 'No pilots need online award.';
			}

		} else {
			echo 'No pilots meet online award.';
		}

		//close function
	}

	function award_certified_pilot_award() {

		//pull all active / inactive pilots with greater than 10 missions who do not already have the blue award (13).

		$query = $this->db->query("SELECT 		pilots.id,
												pilots.username,
												COUNT(DISTINCT pirep.aircraft) AS aircraft,
												blue.awards_index_id AS blue_award,
												silver.awards_index_id AS silver_award,
												cobalt.awards_index_id AS cobalt_award
			
												FROM pirep 
												
													LEFT JOIN pilots
													ON pilots.id = pirep.user_id
												
													LEFT JOIN awards_assigned AS blue
													ON blue.user_id = pilots.id
													AND blue.awards_index_id = '4'
													
													LEFT JOIN awards_assigned AS silver
													ON silver.user_id = pilots.id
													AND silver.awards_index_id = '5'
													
													LEFT JOIN awards_assigned AS cobalt
													ON cobalt.user_id = pilots.id
													AND cobalt.awards_index_id = '6'
													
												
												WHERE (pilots.status = '0'
														OR pilots.status = '1')
												
												GROUP BY pilots.id
												
												ORDER BY pilots.id
												
												");

		$result = $query->result();
		$num_rows = $query->num_rows();

		if ($num_rows > 0) {

			$date_val = gmdate('Y-m-d', time());

			$i = 0;
			//here we would iterate through the pilots and insert missing awards
			foreach ($result as $row) {

				//echo 'Pilot: EHM-'.$row->username.'<br />';
				//echo 'Aircraft: '.$row->aircraft.'<br />';

				if ($row->aircraft >= 8 && empty($row->blue_award)
					|| $row->aircraft >= 16 && empty($row->silver_award)
					|| $row->aircraft >= 25 && empty($row->cobalt_award)
				) {
					echo 'Pilot: EHM-' . $row->username . '<br />';
					echo 'Certified Pilot Award<br />';
				}

				if ($row->aircraft >= 8 && empty($row->blue_award)) {
					//assign the blue award
					echo 'Blue award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '4',
						'type' => 'bluecertified',
						'notes' => 'Awarded when you have flown at least 8 EuroHarmony (or EuroHarmony division) planes and reported the flights using the PIREP',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->aircraft >= 16 && empty($row->silver_award)) {
					//assign the blue award
					echo 'Silver award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '5',
						'type' => 'silvercertified',
						'notes' => 'Awarded when you have flown at least 16 EuroHarmony (or EuroHarmony division) planes and reported the flights using the PIREP',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->aircraft >= 25 && empty($row->cobalt_award)) {
					//assign the blue award
					echo 'Cobalt award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '6',
						'type' => 'cobaltcertified',
						'notes' => 'Awarded when you have flown at least 25 EuroHarmony (or EuroHarmony division) planes and reported the flights using the PIREP',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->aircraft >= 8 && empty($row->blue_award)
					|| $row->aircraft >= 16 && empty($row->silver_award)
					|| $row->aircraft >= 25 && empty($row->cobalt_award)
				) {
					echo '<br />';
				}

			}

			if ($i == 0) {
				echo 'No pilots need certified pilot award.';
			}

		} else {
			echo 'No pilots meet certified pilot award.';
		}

		//close function
	}

	function award_mission_award() {

		//pull all active / inactive pilots with greater than 10 missions who do not already have the blue award (13).

		$query = $this->db->query("SELECT 		pilots.id,
												pilots.username,
												COUNT(pirep.id) AS pireps,
												blue.awards_index_id AS blue_award,
												silver.awards_index_id AS silver_award,
												cobalt.awards_index_id AS cobalt_award
			
												FROM pirep 
												
													LEFT JOIN pilots
													ON pilots.id = pirep.user_id
												
													LEFT JOIN awards_assigned AS blue
													ON blue.user_id = pilots.id
													AND blue.awards_index_id = '13'
													
													LEFT JOIN awards_assigned AS silver
													ON silver.user_id = pilots.id
													AND silver.awards_index_id = '14'
													
													LEFT JOIN awards_assigned AS cobalt
													ON cobalt.user_id = pilots.id
													AND cobalt.awards_index_id = '15'
													
												
												WHERE (pilots.status = '0'
														OR pilots.status = '1')
												AND pirep.mission_id IS NOT NULL
												
												GROUP BY pilots.id
												
												ORDER BY pilots.id
												
												");

		$result = $query->result();
		$num_rows = $query->num_rows();

		if ($num_rows > 0) {

			$date_val = gmdate('Y-m-d', time());

			$i = 0;
			//here we would iterate through the pilots and insert missing awards
			foreach ($result as $row) {

				if ($row->pireps >= 10 && empty($row->blue_award)
					|| $row->pireps >= 25 && empty($row->silver_award)
					|| $row->pireps >= 40 && empty($row->cobalt_award)
				) {
					echo 'Pilot: EHM-' . $row->username . '<br />';
					echo 'Mission Award<br />';
				}

				if ($row->pireps >= 10 && empty($row->blue_award)) {
					//assign the blue award
					echo 'Blue award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '13',
						'type' => 'bluemission',
						'notes' => 'Awarded after completing at least 10 different EuroHarmony missions (requested from the Request Mission page',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->pireps >= 25 && empty($row->silver_award)) {
					//assign the blue award
					echo 'Silver award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '14',
						'type' => 'silvermission',
						'notes' => 'Awarded after completing at least 25 different EuroHarmony missions (requested from the Request Mission page',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->pireps >= 40 && empty($row->cobalt_award)) {
					//assign the blue award
					echo 'Cobalt award: To be assigned<br />';

					//array the data
					$award_data = array(
						//award data
						'user_id' => $row->id,
						'username' => $row->username,
						'awards_index_id' => '15',
						'type' => 'cobaltmission',
						'notes' => 'Awarded after completing at least 40 different EuroHarmony missions (requested from the Request Mission page',
						'assigned_date' => $date_val,
					);

					//write into db pilots data
					$this->db->insert('awards_assigned', $this->db->escape($award_data));

					$i++;
				}

				if ($row->pireps >= 10 && empty($row->blue_award)
					|| $row->pireps >= 25 && empty($row->silver_award)
					|| $row->pireps >= 40 && empty($row->cobalt_award)
				) {
					echo '<br />';
				}

			}

			if ($i == 0) {
				echo 'No pilots need mission award.';
			}

		} else {
			echo 'No pilots meet mission award.';
		}

		//close function
	}

	function pilot_status($active_compare_date = NULL) {

		if ($active_compare_date != NULL) {
			// Placeholder - Check all pilots
			// Status - 0=Active, 1=Inactive
			$sql = "UPDATE pilots SET status=1 WHERE usergroup IS NULL AND status=0 AND lastflight<=?";
			$query = $this->db->query($sql, array($active_compare_date));
			$num_rows = $this->db->affected_rows();

			log_message('info', 'Scheduler => ' . $num_rows . ' pilots changed to Inactive status.');
		}

		//close function
	}

	function pilot_avatar($username = NULL, $data = NULL) {

		$clean['images'] = $data['assets_path'] . 'images/';
		$clean['uploads'] = $data['assets_path'] . 'uploads/';

		if (!empty($username)) {
			// pilot username supplied
			$query = $this->db->query("SELECT 	pilots.id,
												pilots.username,
												pilots.department,  
												pilots.management_pips, 
												pilots.rank,
												awards_assigned.id as awards_assigned_id,
												awards_index.id as awards_index_id,
												awards_index.aggregate_award_name,
												awards_index.aggregate_award_rank
			
												FROM pilots 
												
													LEFT JOIN awards_assigned
													ON awards_assigned.user_id = pilots.id
													
													LEFT JOIN awards_index
													ON awards_assigned.awards_index_id = awards_index.id
												
												WHERE pilots.username = '$username'
												
												ORDER BY pilots.id, awards_assigned.assigned_date
												
												");
		} else {
			// no pilot username supplied, process all pilots (active and inactive)
			$query = $this->db->query("SELECT 	pilots.id,
												pilots.username,
												pilots.department,  
												pilots.management_pips, 
												pilots.rank,
												awards_assigned.id AS awards_assigned_id,
												awards_index.id AS awards_index_id,
												awards_index.aggregate_award_name,
												awards_index.aggregate_award_rank
			
												FROM pilots 
												
													LEFT JOIN awards_assigned
													ON awards_assigned.user_id = pilots.id
													
													LEFT JOIN awards_index
													ON awards_assigned.awards_index_id = awards_index.id
												
												WHERE pilots.status = '0'
												OR pilots.status = '1'
												
												ORDER BY pilots.id, awards_index.tour, awards_index.event, awards_index.aggregate_award_rank, awards_assigned.assigned_date DESC
												
												");

		}

		$result = $query->result();
		$num_rows = $query->num_rows();

		if ($num_rows > 0) {
			foreach ($result as $row) {
				$pilot[$row->id]['username'] = $row->username;
				$pilot[$row->id]['department'] = $row->department;
				$pilot[$row->id]['management_pips'] = $row->management_pips;
				$pilot[$row->id]['rank'] = $row->rank;

				//only hold the highest award for each aggregate type
				if (!array_key_exists('awards', $pilot[$row->id]) || !array_key_exists($row->aggregate_award_name, $pilot[$row->id]['awards'])
					|| $row->aggregate_award_rank <= $pilot[$row->id]['awards'][$row->aggregate_award_name]['aggregate_award_rank']) {
					$pilot[$row->id]['awards'][$row->aggregate_award_name]['award_id'] = $row->awards_index_id;
					$pilot[$row->id]['awards'][$row->aggregate_award_name]['aggregate_award_rank'] = $row->aggregate_award_rank;
				}
			}

		} else {
			$pilot = array();
		}

		// Make flying!
		foreach ($pilot as $data) {

			// Make the epaulette image
			if (!empty($data['department'])) {
				// Staff pilot
				$epaulette = imagecreatefrompng($clean['images'] . 'ranks/' . $data['rank'] . '_' . $data['management_pips'] . '.png');
			} else {
				// Normal pilot
				$epaulette = imagecreatefrompng($clean['images'] . 'ranks/' . $data['rank'] . '.png');
			}

			//create awards array
			$awards_array = $data['awards'];

			// Pretty much duplicate the V1 site avatar code here!
			$columns = 4;
			$rows = 0;
			$count = count($awards_array);
			$rows = ceil($count / 4) + 1;
			$image = imagecreatetruecolor((30 * $columns), (30 * $rows));
			$colorBackgr = imagecolorallocate($image, 255, 255, 255);
			imagefill($image, 0, 0, $colorBackgr);
			imagecopy($image, $epaulette, 0, 0, 0, 0, 120, 28);
			$pos_x = 0;
			$pos_y = 30;
			$i = 1;

			if ($count > 0) {
				foreach ($awards_array as $award) {
					if ($i > 4) {
						$pos_y = $pos_y + 30;
						$pos_x = 0;
						$i = 1;
					}

					$tmp_img_path = $clean['uploads'] . 'awards/' . $award['award_id'] . '.png';

					if (!file_exists($tmp_img_path)) {
						//use the fallback image instead
						$tmp_img_path = $clean['uploads'] . 'awards/no-image.png';
					}

					$imgAward = imagecreatefrompng($tmp_img_path);
					$colorBackgr = imagecolorallocate($imgAward, 255, 255, 255);
					imagecopy($image, $imgAward, $pos_x, $pos_y, 0, 0, 30, 30);
					$pos_x = $pos_x + 30;
					$i++;
				}
			}
			//imagetruecolortopalette($image,false,256);
			//imageinterlace($image, 1);
			$imgLocation = $clean['images'] . 'avatars/' . $data['username'] . '.png';
			//header ("Content-type: image/png");
			//imagepng($image,NULL);
			imagepng($image, $imgLocation);
			imagedestroy($image);
			chmod($imgLocation, 0644);
		}

		//close function
	}

	function pilot_signature($username = NULL, $data = NULL) {

		//$clean['images'] = "/home/euroharm/public_html/site2/assets/images/signatures";
		$clean['images'] = $data['assets_path'] . 'images/signatures';

		if (!empty($username)) {
			// pilot username supplied
			$username = array($username);
		} else {
			// no pilot username supplied, process all pilots (active and inactive)
			$sql = "SELECT pilots.username FROM pilots WHERE pilots.status IN (0,1) ORDER BY pilots.username";
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$username[] = $row->username;
				}
			}
		}

		// Make flying!
		foreach ($username as $value) {
			$sql = "SELECT 	pilots.fname, 
							pilots.sname, 
							pilots.pp_location, 
							pilots.flighthours, 
							pilots.flightmins, 
							UPPER(ranks.rank) AS rank, 
							LOWER(hub.hub_icao) AS icao 
							
					FROM pilots 
							
							LEFT JOIN hub 
							ON pilots.hub=hub.id 
							
							LEFT JOIN ranks 
							ON pilots.rank=ranks.id 
							
					WHERE pilots.username = ?";

			$query = $this->db->query($sql, array($value));

			if ($query->num_rows() > 0) {
				$row = $query->row();
				$line[] = $row->fname . " " . $row->sname; // First line; pilot name
				$line[] = "EHM-" . $value . " Rank: " . $row->rank; // Second line; callsign and rank
				$line[] = "Location: " . $row->pp_location; // Third line; pilot location
				$line[] = "Flight hours: " . $row->flighthours . ":" . $row->flightmins; // Final line; flight hours
				$image = imagecreatefrompng($clean['images'] . "/backgrounds/status_" . $row->icao . ".png");
				$black = imagecolorat($image, 241, 56);
				$font = $clean['images'] . '/fonts/calibrib.ttf';
				imagettftext($image, 11, 0, 7, 35, $black, $font, $line[0]);
				$font = $clean['images'] . '/fonts/calibri.ttf';
				imagettftext($image, 10, 0, 7, 50, $black, $font, $line[1]);
				imagettftext($image, 10, 0, 7, 80, $black, $font, $line[3]);
				imagettftext($image, 10, 0, 7, 65, $black, $font, $line[2]);
				imagealphablending($image, FALSE);
				imagesavealpha($image, TRUE);
				$imgLocation = $clean['images'] . '/' . $value . '.png';
				//header ("Content-type: image/png");
				//imagepng($image,NULL);
				imagepng($image, $imgLocation);
				imagedestroy($image);
				chmod($imgLocation, 0644);
				unset($line);
			}
		}

		//close function
	}

	function pilot_deadhead($username = NULL) {

		if (!empty($username)) {
			// pilot username supplied
			$username = array($username);
		} else {
			// no pilot username supplied, process all pilots (active and inactive)
			$sql = "SELECT pilots.username
			 
					FROM pilots
					 
					WHERE TIMESTAMPDIFF(HOUR,pilots.deadhead_set,NOW())>=24
					
					OR (pilots.deadhead_set IS NOT NULL AND pilots.pp_lastflight IS NULL)
					
					OR (pilots.deadhead_set IS NOT NULL AND pilots.pp_lastflight IS NOT NULL
						AND DATEDIFF(NOW(),pilots.pp_lastflight)>=7)
					
					ORDER BY pilots.username";

			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$username[] = $row->username;
				}
			}
		}

		// Make flying!
		if (!empty($username)) {
			foreach ($username as $value) {
				$sql = "SELECT	pilots.id, 
								pilots.deadhead_dest, 
								hub.hub_icao
								 
						FROM pilots
						
							LEFT JOIN hub 
							ON hub.id=pilots.hub
							
						WHERE pilots.username=?
						 
						ORDER BY pilots.username;";
				$query = $this->db->query($sql, array($value));
				if ($query->num_rows() > 0) {
					$row = $query->row();

					if (empty($row->deadhead_dest)) {
						$row->deadhead_dest = $row->hub_icao;
					}
					//array the data
					$pilots_data = array(
						'pp_location' => $row->deadhead_dest,
						'curr_location' => $row->deadhead_dest,
						'deadhead_dest' => NULL,
						'deadhead_direct' => '0',
						'deadhead_set' => NULL,
					);

					//perform update
					$this->db->where('id', $row->id);
					$this->db->update('pilots', $this->db->escape($pilots_data));
				}
			}
		}

		//close function
	}

//close class
}

?>
