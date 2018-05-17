<?php

class Awards extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index($offset = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//caching
		//$this->output->cache($cache_duration_normal);
		$this->load->library('pagination');

		$data['rank_array'] = array();

		//make database call to grab the aircraft data
		$query = $this->db->query("	SELECT 	
			
										awards_index.id,
										awards_index.awardtype,
										awards_index.description,	
										awards_index.award_name,
										awards_index.automatic,
										awards_index.aggregate_award_name,
										awards_index.aggregate_award_rank
														
												
											
									FROM awards_index
									
										LEFT JOIN tour_legs
										ON tour_legs.award_id = awards_index.id
									
										LEFT JOIN tour_index
										ON tour_index.id = tour_legs.tour_id
										
									WHERE tour_index.enabled = '1' OR tour_index.enabled IS NULL
									
									GROUP BY awards_index.id
									
									ORDER BY awards_index.aggregate_award_name, awards_index.aggregate_award_rank, awards_index.award_name
											
										");

		$data['awards'] = $query->result();
		$data['num_rows'] = $query->num_rows();

		//paginatipon
		if ($offset == NULL || $offset == '') {
			$offset = 0;
		}

		$data['offset'] = $offset;
		$data['limit'] = '10';

		$pag_config['base_url'] = $data['base_url'] . 'awards/index/';
		$pag_config['total_rows'] = $data['num_rows'];
		$pag_config['per_page'] = $data['limit'];
		$pag_config['uri_segment'] = 3;

		$this->pagination->initialize($pag_config);

		$data['page_title'] = 'Awards';
		$data['no_links'] = '1';

		$this->view_fns->view('global/awards/awards_index', $data);
	}

}

/* End of file */