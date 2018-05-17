<?php

class Ajax extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function latestflights() {

		$results[0]['pilot'] = 'EHM-1997 Alexander Worton';
		$results[0]['rank'] = 'ATP';
		$results[0]['aircraft'] = 'Boeing 737-800';
		$results[0]['bearing'] = '118';
		$results[0]['altitude'] = '37000';
		$results[0]['ias'] = '261';
		$results[0]['from_icao'] = 'EGLL';
		$results[0]['from_lat'] = '51.477497';
		$results[0]['from_lon'] = '-0.461389';
		$results[0]['to_icao'] = 'EHAM';
		$results[0]['to_lat'] = '52.308052';
		$results[0]['to_lon'] = '4.764167';
		$results[0]['position_lat'] = '51.5';
		$results[0]['position_lon'] = '-0.5';
		$results[0]['propilot'] = '1';

		//output xml
		header('Content-Type: text/xml');
		header("Cache-Control: no-cache, must-revalidate");
		echo '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n";
		echo '<xmlresponse>' . "\n";

		foreach ($results as $row) {
			echo '	<flight>' . "\n";
			echo '		<pilot>' . $row['pilot'] . '</pilot>' . "\n";
			echo '		<rank>' . $row['rank'] . '</rank>' . "\n";
			echo '		<aircraft>' . $row['aircraft'] . '</aircraft>' . "\n";
			echo '		<bearing>' . $row['bearing'] . '</bearing>' . "\n";
			echo '		<altitude>' . $row['altitude'] . '</altitude>' . "\n";
			echo '		<ias>' . $row['ias'] . '</ias>' . "\n";
			echo '		<fromicao>' . $row['from_icao'] . '</fromicao>' . "\n";
			echo '		<fromlat>' . $row['from_lat'] . '</fromlat>' . "\n";
			echo '		<fromlon>' . $row['from_lon'] . '</fromlon>' . "\n";
			echo '		<toicao>' . $row['to_icao'] . '</toicao>' . "\n";
			echo '		<tolat>' . $row['to_lat'] . '</tolat>' . "\n";
			echo '		<tolon>' . $row['to_lon'] . '</tolon>' . "\n";
			echo '		<positionlat>' . $row['position_lat'] . '</positionlat>' . "\n";
			echo '		<positionlon>' . $row['position_lon'] . '</positionlon>' . "\n";
			echo '		<propilot>' . $row['propilot'] . '</propilot>' . "\n";
			echo '	</flight>' . "\n";
			//echo '  <bod><![CDATA['.$content.']]></bod>'."\n";

		}

		echo '</xmlresponse>';

	}

	function pilotnews($item_id = 0) {

		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//config
		$num_limit = 4;
		$half_limit = floor($num_limit / 2);
		//grab seconds
		$secs = date('s', time());

		/*
		//possible tickers:
		
		Hub Transfers
		New pilots
		Pilot Promotions
		Propilot crashes
			
		*/

		$output_valid = 0;
		$title = '';
		$content = '';

		//alternator
		//first 15 seconds of minute
		//if($secs / 15 <= 1){
		if ($item_id == 1) {
			//if(1 == 1){
			//grab new pilots
			$query = $this->db->query("	SELECT 	
												pilots.username as username,
												pilots.fname as fname,
												pilots.sname as sname,
												pilots.signupdate as signupdate,
												ranks.rank as rank
												
										FROM pilots
										
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
										
										WHERE pilots.email_confirmed = '1'
										
										ORDER BY pilots.signupdate DESC, pilots.username DESC
										
										LIMIT $num_limit
										
										");

			$new_pilot_data = $query->result();
			$i = 1;
			$tr = 0;
			$title = 'Recruitment';
			$content = '<table border="0">';
			foreach ($new_pilot_data as $row) {
				if ($tr == 0) {
					$content .= '<tr>';
					$tr = 1;
				}
				$content .= '<td><b>[' . $row->rank . '] EHM-' . $row->username . '</b> ' . $row->fname . ' ' . $row->sname . ' joined on ' . date('d/m/Y', strtotime($row->signupdate)) . '</td>';

				if ($i % 2 == 0) {
					$content .= '</tr>';
					$tr = 0;
				}

				$i++;
			}

			if ($tr == 1) {
				$content .= '</tr>';
			}
			$content .= '</table>';

			if ($i > 1) {
				$output_valid = 1;
			}

		}


		//second 15 seconds of minute
		//if($secs / 15 <= 2 && $secs / 15 > 1 
		//&& $output_valid == 0){
		elseif ($item_id == 2) {

			$title = 'Promotions';
			$content = '';

			//grab latest promotions
			$query = $this->db->query("	SELECT 	
												pilots_promotion.promoted AS promoted,
												pilots.username AS username,
												pilots.fname AS fname,
												pilots.sname AS sname,
												ranks.rank AS rank_short,
												ranks.name AS rank_long
												
										FROM pilots_promotion
										
											LEFT JOIN pilots
											ON pilots.id = pilots_promotion.pilots_id
											
											LEFT JOIN ranks
											ON ranks.id = pilots_promotion.rank_id
										
										WHERE pilots_promotion.pilots_id != 0
										
										ORDER BY pilots_promotion.promoted DESC
										
										LIMIT 2
										
										");

			$promotion_data = $query->result();
			$i = 1;
			$tr = 0;
			$content = '<table border="0">';
			foreach ($promotion_data as $row) {

				$content .= '<tr>';
				$tr = 1;
				$content .= '<td><b>EHM-' . $row->username . ' ' . $row->fname . ' ' . $row->sname . '</b> was promoted to ' . $row->rank_long . ' on ' . date('d/m/Y', strtotime($row->promoted)) . '</td>';
				$content .= '</tr>';

				$i++;
			}

			$content .= '</table>';

			if ($i > 1) {
				$output_valid = 1;
			}

		}

		//third 15 seconds of minute
		//if($secs / 15 <= 3 && $secs / 15 > 2 
		//&& $output_valid == 0){
		elseif ($item_id == 3) {

			//grab pilot awards
			$query = $this->db->query("	SELECT 	
												pilots.fname as fname,
												pilots.sname as sname,
												awards_index.award_name as award_name,
												awards_assigned.assigned_date as assigned_date,
												ranks.rank as rank
												
										FROM awards_assigned
										
											LEFT JOIN awards_index
											ON awards_index.id = awards_assigned.awards_index_id
										
											LEFT JOIN pilots
											ON awards_assigned.user_id = pilots.id
											
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
										
										WHERE pilots.email_confirmed = '1'
										
										ORDER BY awards_assigned.assigned_date DESC
										
										LIMIT $half_limit
										
										");

			$new_pilot_data = $query->result();
			$i = 1;
			$tr = 0;
			$title = 'Awards and recognition';
			$content = '<table border="0">';
			foreach ($new_pilot_data as $row) {
				if ($tr == 0) {
					$content .= '<tr>';
					$tr = 1;
				}
				$content .= '<td><b>[' . $row->rank . ']</b> ' . $row->fname . ' ' . $row->sname . ' has been awarded \'' . $row->award_name . '\' on ' . date('d/m/Y', strtotime($row->assigned_date)) . ' in recognition of their achievement</td>';

				if ($i % 2 == 0) {
					$content .= '</tr>';
					$tr = 0;
				}

				$i++;
			}

			if ($tr == 1) {
				$content .= '</tr>';
			}
			$content .= '</table>';

			if ($i > 1) {
				$output_valid = 1;
			}

		}

		//catch all default
		//if($output_valid == 0){

		elseif ($item_id == 3) {

			$crash_compare = gmdate('Y-m-d H:i:s', strtotime('-3 months'));

			//grab news
			$query = $this->db->query("	SELECT 	
												pilots.fname as fname,
												pilots.sname as sname,
												ranks.rank as rank,
												propilot_aircraft.tail_id as tail_id,
												propilot_aircraft_crash.datetime_crash as datetime_crash,
												aircraft.name as aircraft_name
												
										FROM propilot_aircraft_crash
										
											LEFT JOIN pilots
											ON propilot_aircraft_crash.user_id = pilots.id
											
											LEFT JOIN ranks
											ON ranks.id = pilots.rank
											
											LEFT JOIN propilot_aircraft
											ON propilot_aircraft.id = propilot_aircraft_crash.aircraft_unique_id
											
											LEFT JOIN aircraft
											ON propilot_aircraft.aircraft_id = aircraft.id
										
										WHERE pilots.email_confirmed = '1'
										AND datetime_crash >= '$crash_compare'
										
										ORDER BY propilot_aircraft_crash.datetime_crash DESC
										
										LIMIT $half_limit
										
										");

			$new_pilot_data = $query->result();
			$num_crashes = $query->num_rows();
			$i = 1;
			$tr = 0;
			$title = 'Air crash investigations';
			$content = '<table border="0">';
			foreach ($new_pilot_data as $row) {
				if ($tr == 0) {
					$content .= '<tr>';
					$tr = 1;
				}

				$n = ' ';
				$vowel_array = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U');
				if (in_array(substr($row->aircraft_name, 0, 1), $vowel_array)) {
					$n = 'n ';
				}

				$content .= '<td><b>[' . $row->rank . ']</b> ' . $row->fname . ' ' . $row->sname . ' was involved in an incident with a' . $n . $row->aircraft_name . ' tail number ' . $row->tail_id . ' on ' . date('d/m/Y', strtotime($row->datetime_crash)) . '</td>';

				if ($i % 2 == 0) {
					$content .= '</tr>';
					$tr = 0;
				}

				$i++;
			}

			if ($num_crashes < 1) {
				$content = '<table border="0"><tr><td><b>There have been no aircraft incidents in the last 3 months.</b></td></tr>';
			}

			if ($tr == 1) {
				$content .= '</tr>';
			}
			$content .= '</table>';

		} elseif ($item_id == 0) {
			$title = 'Protect your login credentials';
			$content = '<table border="0"><tr><td>Euroharmony management will <b>never</b> ask you for your password.</td></tr></table>';
		} else {
			$title = 'Welcome to the NG Website';
			$content = '<table border="0"><tr><td>Please report any issues you experience in the forums.</td></tr></table>';

		}

		//output xml
		header('Content-Type: text/xml');
		header("Cache-Control: no-cache, must-revalidate");

		echo '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n";
		echo '<xmlresponse>' . "\n";

		echo '  <title>' . $title . '</title>' . "\n";
		echo '  <bod><![CDATA[' . $content . ']]></bod>' . "\n";

		echo '  <error_code> 0 </error_code>' . "\n";
		echo '</xmlresponse>';

	}

	function index() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//$this->view_fns->view('global/home', $data);
	}
}

/* End of file */