<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Award_model extends CI_Model {

	function get_awards_event() {

		//now grab all the automatic awards
		$query = $this->db->query("	SELECT 	
										awards_index.id,
										awards_index.awardtype,
										awards_index.description,	
										awards_index.award_name,
										awards_index.automatic,
										awards_index.aggregate_award_name,
										awards_index.aggregate_award_rank
												
										FROM awards_index
											
										WHERE awards_index.automatic = 'Y'
										AND awards_index.event = '1'
									");

		$awards = $query->result();

		$data = array('' => '');

		foreach ($awards as $row) {

			$data[$row->id] = substr($row->award_name, 0, 30);

		}

		return $data;

		//close get_awards_tour
	}

	function get_awards_tour() {

		//now grab all the automatic awards
		$query = $this->db->query("	SELECT 	
										awards_index.id,
										awards_index.awardtype,
										awards_index.description,	
										awards_index.award_name,
										awards_index.automatic,
										awards_index.aggregate_award_name,
										awards_index.aggregate_award_rank
												
										FROM awards_index
											
										WHERE awards_index.automatic = 'Y'
										AND awards_index.tour = '1'
										
										ORDER BY awards_index.aggregate_award_name, awards_index.aggregate_award_rank DESC, awards_index.award_name
									");

		$awards = $query->result();

		$data = array('' => '');

		foreach ($awards as $row) {

			$data[$row->id] = substr($row->award_name, 0, 30);

		}

		return $data;

		//close get_awards_tour
	}

//close class
}

?>
