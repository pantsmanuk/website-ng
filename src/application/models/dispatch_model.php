<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dispatch_model extends CI_Model {

	function get_division_array() {

		$query = $this->db->query("	SELECT 
											divisions.id AS id,
											divisions.division_longname AS division_longname
											
									FROM divisions
									
									WHERE divisions.primary = '1'
									
									ORDER BY divisions.id
									
										");

		$divisions = $query->result();

		$divisions_array = array();
		foreach ($divisions as $row) {
			$divisions_array[$row->id] = $row->division_longname;
		}

		return $divisions_array;

		//close get_division_array
	}

	function get_airfield_array() {

		$query = $this->db->query("	SELECT 
											airports.icao AS icao,
											airports.name AS name
											
									FROM airports
									
									ORDER BY airports.icao
									
										");

		$airfields = $query->result();

		$airfield_array = array();
		foreach ($airfields as $row) {
			$airfield_array[$row->icao] = $row->icao . ' ' . $row->name;
		}

		return $airfield_array;

		//close get_hub_array
	}

	function get_hub_array() {

		$query = $this->db->query("	SELECT 
											hub.hub_icao AS hub_icao,
											hub.hub_name AS hub_name
											
									FROM hub
									
									ORDER BY hub.hub_icao
									
										");

		$hubs = $query->result();

		$hub_array = array();
		foreach ($hubs as $row) {
			$hub_array[$row->hub_icao] = '[' . $row->hub_icao . '] ' . $row->hub_name;
		}

		return $hub_array;

		//close get_hub_array
	}

	function get_aircraft_array_restrict($division, $cls, $limit) {

		$div_where = '';
		$cls_where = '';

		if ($division != '' && strtoupper($division) != 'ALL') {

			//handle where multiple divisions are given (if comma is found in string)
			if (strpos($division, ',') != FALSE) {
				$div_where = "AND aircraft.division IN ($division)";
			} else {
				$div_where = "AND aircraft.division = '$division'";
			}
		}

		if ($cls != '') {

			if ($cls == 7) {
				$cls_where = "AND aircraft.clss = '7'";
			} elseif ($cls < 7 && $cls > 3) {
				$cls_where = "AND aircraft.clss >= '4'";
				if ($limit != '' && $limit < 6) {
					$cls_where .= "AND aircraft.clss <= '$limit'";
				} else {
					$cls_where .= "AND aircraft.clss <= '6'";
				}
			} else {

				if ($limit != '' && $limit < 3) {
					$cls_where .= "AND aircraft.clss <= '$limit'";
				} else {
					$cls_where = "AND aircraft.clss <= '3'";
				}
			}

		}

		$query = $this->db->query("	SELECT 
											aircraft.id as id,
											aircraft.name as name,
											aircraft.clss as clss,
											aircraft.pax as pax,
											aircraft.cargo as cargo,
											divisions.division_longname as div_name,
											divisions.id as div_id
											
									FROM aircraft
									
										LEFT JOIN divisions
										ON divisions.id = aircraft.division
									
									WHERE aircraft.enabled = '1'
									$div_where
									$cls_where
									
									ORDER BY aircraft.clss, aircraft.name
									
										");

		$aircraft = $query->result();

		$data['aircraft_array'] = array('' => '');
		$data['pax_array'] = array();
		$data['cargo_array'] = array();
		foreach ($aircraft as $row) {
			$data['aircraft_array']['Class ' . $row->clss][$row->id] = $row->name;
			$data['aircraft_array_simple'][$row->id] = $row->name;
			$data['aircraft_array_div'][$row->div_name][$row->id] = '[' . $row->clss . '] ' . $row->name;
			$data['aircraft_array_div_id'][$row->div_id][$row->id] = $row->name;
			$data['pax_array'][$row->id] = $row->pax;
			$data['cargo_array'][$row->id] = $row->cargo;
		}

		return $data;

		//close get_aircraft_array_restrict
	}

	function get_aircraft_array($division, $limit, $floor = 0, $require_charter_enabled = 0) {

		$div_where = '';
		$cls_where = '';
		$charter_where = "";

		if ($division != '' && strtoupper($division) != 'ALL') {

			//handle where multiple divisions are given (if comma is found in string)
			if (strpos($division, ',') != FALSE) {
				$div_where = "AND aircraft.division IN ($division)";
			} else {
				$div_where = "AND aircraft.division = '$division'";
			}
		}

		if ($limit != '') {
			$cls_where = "AND aircraft.clss >= '$floor'";
			$cls_where .= "
			AND aircraft.clss <= '$limit'";
		}

		if ($require_charter_enabled == '1') {
			$charter_where = " 
			AND aircraft.charter = '1'";
		}

		$query = $this->db->query("	SELECT 
											aircraft.id as id,
											aircraft.name as name,
											aircraft.clss as clss,
											aircraft.pax as pax,
											aircraft.cargo as cargo,
											divisions.division_longname as div_name,
											divisions.id as div_id
											
									FROM aircraft
									
										LEFT JOIN divisions
										ON divisions.id = aircraft.division
									
									WHERE aircraft.enabled = '1'
									$div_where
									$cls_where
									$charter_where

									ORDER BY aircraft.clss, aircraft.name
									
										");

		$aircraft = $query->result();

		$data['aircraft_array'] = array('' => '');
		$data['pax_array'] = array();
		$data['cargo_array'] = array();
		foreach ($aircraft as $row) {
			$data['aircraft_array']['Class ' . $row->clss][$row->id] = $row->name;
			$data['aircraft_array_simple'][$row->id] = $row->name;
			$data['aircraft_array_div'][$row->div_name][$row->id] = '[' . $row->clss . '] ' . $row->name;
			$data['aircraft_array_div_id'][$row->div_id][$row->id] = $row->name;
			$data['pax_array'][$row->id] = $row->pax;
			$data['cargo_array'][$row->id] = $row->cargo;
		}

		return $data;

		//close get_aircraft_array
	}

	function get_next_hop($icao, $location_lat, $location_lon, $dest_icao, $destination_lat, $destination_lon, $range_km, $hop_num, $i, $round_trip, $route_array) {

		$data['fail'] = FALSE;
		$data['new_lat'] = '';
		$data['new_lon'] = '';
		$data['new_icao'] = '';
		$data['exception'] = '';

		if ($i <= $hop_num / 2) {
			$return = 0;
		} else {
			$return = 1;
		}

		//check if the destination is within a quarter of the aircraft range, if so, fly direct towards it.
		$dest_dist = $this->geocalc_fns->GCDistance($location_lat, $location_lon, $destination_lat, $destination_lon);

		if (
			($dest_dist <= ($range_km / 4) || $dest_dist <= 500)
			&& ($return == 1 || $round_trip == 0)
		) {

			$data['new_lat'] = $destination_lat;
			$data['new_lon'] = $destination_lon;
			$data['new_icao'] = $dest_icao;

		} else {

			$lat_per_km = $this->geocalc_fns->getLatPerKm();
			$lon_per_km = $this->geocalc_fns->getKmPerLonAtLat($location_lat);

			//calculate max lat change
			$max_lat_change = $lat_per_km * $range_km;
			$min_lat_change = $lat_per_km * $range_km;

			//calculate max long change
			$max_long_change = $lon_per_km * $range_km;
			$min_long_change = $lon_per_km * $range_km;

			//direct towards destination if half way, or destination != origin
			if ($return == 1 || $round_trip == 0) {

				if ($destination_lon > $location_lon) {
					$min_long_change = 0;
				} else {
					$max_long_change = 0;
				}

				if ($destination_lat > $location_lat) {
					$min_lat_change = 0;
				} else {
					$max_lat_change = 0;
				}

			}

			$min_lat = $location_lat - $min_lat_change;
			$max_lat = $location_lat + $max_lat_change;

			$max_long = $location_lon + $max_long_change;
			$min_long = $location_lon - $min_long_change;

			$sql_restrict = '';
			//iterate through route_array and build restrictions to prevent selection of previous airport
			foreach ($route_array as $route) {

				//if it isn't the destination
				if ($route['icao'] != $dest_icao) {

					$tmp_icao = $route['icao'];

					$sql_restrict .= "AND airports_data.icao != '$tmp_icao'
					";
				}

			}

			//now get the next airport
			$query = $this->db->query("	SELECT 
								airports_data.icao as icao,
								airports_data.lat as latitude,
								airports_data.long as longitude
								
						FROM airports
						
							LEFT JOIN airports_data
							ON airports.icao = airports_data.icao
						
						WHERE airports_data.lat <= $max_lat
						AND airports_data.lat >= $min_lat
						AND airports_data.long <= $max_long
						AND airports_data.long >= $min_long
						AND airports_data.icao != '$icao'
						$sql_restrict
						
						ORDER BY RAND()
						
							");

			$airports = $query->result();
			$num_rows = $query->num_rows();

			if ($num_rows > 0) {
				//iterate through array, pick first case where distance is within range
				foreach ($airports as $row) {

					$gcdist = $this->geocalc_fns->GCDistance($location_lat, $location_lon, $row->latitude, $row->longitude);

					$dest_dist = $this->geocalc_fns->GCDistance($row->latitude, $row->longitude, $destination_lat, $destination_lon);

					//last hop must be to destination
					if ($hop_num == $i) {
						$data['new_lat'] = $destination_lat;
						$data['new_lon'] = $destination_lon;
						$data['new_icao'] = $dest_icao;
					}
					//cannot move to a location that is out of range for return range * remaining hops)
					//target must also be in range
					elseif ($gcdist <= $range_km && $dest_dist <= ($range_km * ($hop_num - $i))) {
						//set current row as the selected airport and break loop
						$data['new_lat'] = $row->latitude;
						$data['new_lon'] = $row->longitude;
						$data['new_icao'] = $row->icao;

						break;
					}
				}

			} else {
				$data['fail'] = TRUE;
				if ($icao != $dest_icao && $i != $hop_num) {
					$data['exception'] = 'Could not continue route to destination';
				}
			}

			//close if destination within quarter of range
		}

		if ($data['new_lat'] == '' || $data['new_lon'] == '' || $data['new_icao'] == '') {
			$data['fail'] = TRUE;
			$data['exception'] = 'Could not continue route to destination';
		}

		return $data;

		//close get_next_hop
	}

//close class
}

?>
