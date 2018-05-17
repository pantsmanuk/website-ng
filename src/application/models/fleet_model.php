<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fleet_model extends CI_Model {

	function get_download_types() {

		$query = $this->db->query("	SELECT 	aircraft_downloads_type.id AS id,
											aircraft_downloads_type.name AS name
											
									FROM aircraft_downloads_type
									
									ORDER BY aircraft_downloads_type.id
											
										");

		$types = $query->result();

		foreach ($types as $row) {

			$data[$row->id] = $row->name;

		}

		return $data;

	}

	function get_divisions() {

		$query = $this->db->query("	SELECT 	divisions.id AS id,
										divisions.division_longname AS name
											
									FROM divisions
									
									ORDER BY divisions.id
											
										");

		$divisions = $query->result();

		foreach ($divisions as $row) {

			$data[$row->id] = $row->name;

		}

		return $data;

	}

	function get_ranks() {

		$query = $this->db->query("	SELECT 	ranks.id AS id,
											ranks.rank AS rank,
											ranks.name AS name,
											ranks.hours AS hours,
											ranks.stats_order AS stats_order,
											ranks.class AS clss
											
									FROM ranks
									
									ORDER BY ranks.hours
											
										");

		$ranks = $query->result();

		foreach ($ranks as $row) {

			$data['ranks'][$row->id] = $row->name;
			$data['clss'][$row->clss] = 'Class ' . $row->clss;

		}

		return $data;

	}

//close class
}

?>
