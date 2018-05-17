<?php

class Divisions extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index($selected_division_id = 1) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		//grab all divisions to build menu array
		$query = $this->db->query("	SELECT 	id, 
											division_longname, 
											prefix, 
											divisions.primary AS prim,
											divisions.missions AS missions,
											divisions.tours AS tours,
											blurb											
											
									FROM divisions
									
									WHERE public = '1'
									
									ORDER BY id
											
										");

		$division_results = $query->result();

		$data['division_array'] = array();
		$division_code_array = array();

		foreach ($division_results as $row) {
			$data['division_array'][$row->id]['id'] = $row->id;
			$data['division_array'][$row->id]['longname'] = $row->division_longname;
			$data['division_array'][$row->id]['prefix'] = $row->prefix;
			$data['division_array'][$row->id]['prim'] = $row->prim;
			$data['division_array'][$row->id]['missions'] = $row->missions;
			$data['division_array'][$row->id]['tours'] = $row->tours;
			$data['division_array'][$row->id]['blurb'] = $row->blurb;
			$division_id_array[$row->id] = $row->id;
			if ($row->id == $selected_division_id) {
				$selected_division_id = $row->id;
			}
		}

		//if the supplied division code is not in the database, set to default
		if (!in_array($selected_division_id, $division_id_array)) {
			$selected_division_id = 1;
		}

		$data['selected_division_id'] = $selected_division_id;

		//set title
		$data['page_title'] = $data['division_array'][$selected_division_id]['longname'];

		$this->view_fns->view('global/divisions/divisions_index', $data);
	}
}

/* End of file */