<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_fns {

	function generate_flightnumber() {

		//grab all flightnumbers from database
		$CI =& get_instance();

		$today = date('Y-m-d');

		//grab the term from the database
		$query = $CI->db->query("	SELECT 	
									timetable.flightnumber
									
							FROM timetable 
							
							ORDER BY timetable.flightnumber
							ASC
						");

		$num_results = $query->num_rows();
		$results = $query->result();

		$flightnumber = '-';
		$selected = FALSE;

		$taken_flightnumbers = array();

		if ($num_results == 0) {
			//issue first id
			$flightnumber = '00001';
		} else {
			//iterate through all the id's and assign the first unassigned id

			foreach ($results as $row) {

				$taken_flightnumbers[$row->flightnumber] = $row->flightnumber;

			}

			//now iterate through flightnumbers, checking each one is not in the array
			$i = 1;
			while ($i <= 99999) {

				if (!array_key_exists(str_pad((int)$i, 5, "0", STR_PAD_LEFT), $taken_flightnumbers)) {
					$flightnumber = $i;
					break;
				}

				$i++;
			}

		}

		//deal with the case where all ids are taken or the id is assigned out of bounds
		if ($flightnumber == '-' || $flightnumber > '99999') {
			//we didn't set the id during the run.
			return FALSE;
		} else {

			$flightnumber = str_pad((int)$flightnumber, 5, "0", STR_PAD_LEFT);
			return $flightnumber;
		}

	}

}
/* End of file */