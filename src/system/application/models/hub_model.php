<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class Hub_model extends CI_Model{


	function get_hub_data($compare_date = NULL, $selected_hub_query = NULL){
		
		// --
		if($compare_date == NULL || $selected_hub_query == NULL){
			$hub_array = array();
			//echo 'compare_date: '.$compare_date;
			//echo '<br />';
			//echo 'selected_hub_query: '.$selected_hub_query;
		}
		else{
			
			//make database call to grab the selected hub data
			$query = $this->db->query("	SELECT 	hub.id as id,
												hub.hub_icao as hub_icao,
												hub.hub_name as hub_name,
												hub.connection_centre as connection_centre,
												hub_captain.fname as hub_captain_fname,
												hub_captain.sname as hub_captain_sname,
												hub.hub_description as hub_description,
												hub.hub_opened as hub_opened,
												pilots.fname as fname,
												pilots.sname as sname,
												pilots.id as pilot_id,
												pilots.country as country_code,
												pilots.curr_location as curr_location,
												pilots.pp_location as pp_location,
												pilots.lastflight as lastflight,
												pilots.pp_lastflight as pp_lastflight,
												pilots.lastflight as lastactive,
												flight_sim_versions.version_name as flight_sim,
												ranks.name as rank_long,
												ranks.rank as rank_short,
												pilots.flighthours as flighthours,
												pilots.flightmins as flightmins,
												countries.Name as country,
												hub_country.Name as hub_country
												
										FROM hub
										
											LEFT JOIN pilots
											on hub.id = pilots.hub
											AND lastflight >= '$compare_date'
											
											LEFT JOIN pilots as hub_captain
											ON hub_captain.id = hub.hub_captain_id
											
											LEFT JOIN ranks
											on ranks.id = pilots.rank
											
											LEFT JOIN airports
											on airports.ICAO = hub.hub_icao
											
											LEFT JOIN countries as hub_country
											on hub_country.Country = airports.country
											
											LEFT JOIN countries
											on pilots.country = countries.Country
											
											LEFT JOIN flight_sim_versions
											ON flight_sim_versions.id = pilots.fsversion
									
											
										WHERE hub.hub_icao = '$selected_hub_query'
										
										ORDER BY pilots.flighthours DESC, pilots.flightmins DESC
										
												
											");
					
			$hub_pilots =  $query->result();
			
			$hub_array = array();
			
			//organise the data so it can be outputted in tabular format
			foreach($hub_pilots as $row){
			
				$hub_icao = strtoupper($row->hub_icao);
				
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['name'] = $row->fname.' '.$row->sname;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['pilot_id'] = $row->pilot_id;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['country'] = $row->country;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['country_code'] = $row->country_code;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['curr_location'] = $row->curr_location;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['pp_location'] = $row->pp_location;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['flighthours'] = $row->flighthours;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['flightmins'] = $row->flightmins;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['rank_long'] = $row->rank_long;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['rank_short'] = $row->rank_short;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['flight_sim'] = $row->flight_sim;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['lastflight'] = $row->lastflight;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['pp_lastflight'] = $row->pp_lastflight;
				$hub_array[$hub_icao]['pilots'][$row->pilot_id]['lastactive'] = $row->lastactive;
				$hub_array[$hub_icao]['hub_name'] = $row->hub_name;
				$hub_array[$hub_icao]['hub_icao'] = $row->hub_icao;
				$hub_array[$hub_icao]['hub_captain'] = $row->hub_captain_fname.' '.$row->hub_captain_sname;
				$hub_array[$hub_icao]['hub_opened'] = $row->hub_opened;
				$hub_array[$hub_icao]['hub_description'] = $row->hub_description;
				$hub_array[$hub_icao]['hub_country'] = $row->hub_country;
				$hub_array[$hub_icao]['connection_centre'] = $row->connection_centre;
				
				
			}
		
		
			
		
		}
			
		return $hub_array;



	//close get_hub_data
	}
	
	function get_hub_list($restrict = NULL){
	
		if($restrict == 'hub'){
			//main hubs
			$hub_restrict = "WHERE hub.connection_centre = '0'";
		}
		elseif($restrict == 'all'){
			$hub_restrict = "";
		}
		elseif($restrict == 'cc'){
			//connection centres only
			$hub_restrict = "WHERE hub.connection_centre = '1'";
		}
		else{
			//default to returning main hubs only
			$hub_restrict = "WHERE hub.connection_centre = '0'";
		}
	
		//now grab all the hubs to build hub menu
		$query = $this->db->query("	SELECT 	hub.id as id,
											hub.hub_icao as hub_icao,
											hub.hub_name as hub_name,
											hub.connection_centre as connection_centre
											
									FROM hub		
									
									$hub_restrict
									
									ORDER BY hub.connection_centre, hub.hub_icao
											
										");
				
		$hub_list =  $query->result();
		
		foreach($hub_list as $row){
		
			$data[$row->hub_icao] = $row->hub_name;
		
		}
		
	return $data;
	
	//close get_hub_list
	}
	
		
//close class
}
?>
