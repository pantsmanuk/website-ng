<?php

class Ranks extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index($fleet_type = 'C') {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//caching
		//$this->output->cache($cache_duration_normal);

		$data['rank_array'] = array();

		//make database call to grab the aircraft data
		$query = $this->db->query("	SELECT 	ranks.id AS id,
											ranks.rank AS rank,
											ranks.name AS name,
											ranks.hours AS hours,
											ranks.stats_order AS stats_order,
											ranks.class AS clss
											
									FROM ranks
									
									ORDER BY ranks.hours
											
										");

		$data['rank_array'] = $query->result_array();

		$data['page_title'] = 'Ranks';
		$data['no_links'] = '1';

		$this->view_fns->view('global/ranks/ranks_index', $data);
	}

}

/* End of file */