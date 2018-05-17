<?php

class Hubs extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index($selected_hub_icao = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load addtional libraries
		$this->load->model('Hub_model');

		if ($selected_hub_icao == NULL) {
			//if logged in
			if ($this->session->userdata('hub') != '') {
				//set to own hub
				$selected_hub_icao = $this->session->userdata('hub');
			} else {
				$selected_hub_icao = 'EGLL';
			}
		}

		$data['selected_hub_icao'] = $selected_hub_icao;

		$selected_hub_query = strtolower($selected_hub_icao);

		//make database call for the hub data
		$data['hub_array'] = $this->Hub_model->get_hub_data($active_compare_date, $selected_hub_query);

		//make database call to get list of hubs for menu
		$data['hub_list'] = $this->Hub_model->get_hub_list('all');

		//set title
		if (array_key_exists($selected_hub_icao, $data['hub_array'])) {
			$data['page_title'] = '[' . $data['hub_array'][$selected_hub_icao]['hub_country'] . '] ' . $selected_hub_icao . ' ' . $data['hub_array'][$selected_hub_icao]['hub_name'];
		} else {
			$data['page_title'] = '';
		}

		//load the view and pass data to it
		$this->view_fns->view('global/hubs/hubs_index', $data);
	}
}

/* End of file */