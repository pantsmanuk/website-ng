<?php
 
class Propilot extends CI_Controller {

	function Propilot()
	{
		parent::__construct();	
	}
	
	function index()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		
		//set title
		$data['page_title'] = 'Propilot';
		
		
		if($this->session->userdata('logged_in' == '1')){
		
		
			$this->view_fns->view('global/propilot/propilot_index', $data);
		}
		//close logged in
		else{
			//handle the previous page writer
			$sessiondata['return_page'] = 'propilot/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			//redirect
			redirect('auth/login');
		}
	}
}

/* End of file */