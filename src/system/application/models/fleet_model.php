<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class Fleet_model extends CI_Model{
	

	function get_download_types(){
		
		$query = $this->db->query("	SELECT 	aircraft_downloads_type.id as id,
											aircraft_downloads_type.name as name
											
									FROM aircraft_downloads_type
									
									ORDER BY aircraft_downloads_type.id
											
										");
		
		$types =  $query->result();
		
		foreach($types as $row){
		
			$data[$row->id] = $row->name;
		
		}
		
		return $data;
		
		
	}
	
	
	function get_divisions(){
		
		$query = $this->db->query("	SELECT 	divisions.id as id,
										divisions.division_longname as name
											
									FROM divisions
									
									ORDER BY divisions.id
											
										");
				
		$divisions =  $query->result();
		
		foreach($divisions as $row){
		
			$data[$row->id] = $row->name;
		
		}
		
		return $data;
		
	}
	
	
	function get_ranks(){
		
		$query = $this->db->query("	SELECT 	ranks.id as id,
											ranks.rank as rank,
											ranks.name as name,
											ranks.hours as hours,
											ranks.stats_order as stats_order,
											ranks.class as clss
											
									FROM ranks
									
									ORDER BY ranks.hours
											
										");
				
		$ranks =  $query->result();
		
		foreach($ranks as $row){
		
			$data['ranks'][$row->id] = $row->name;
			$data['clss'][$row->clss] = 'Class '.$row->clss;
		
		}
		
		return $data;
		
	}
	
		
//close class
}
?>
