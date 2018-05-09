<?php
 
/**
 * 
 **/
class Cron extends CI_Controller {

	function Cron()
	{
		parent::__construct();	
	}

	

	function testmissionaward(){
	
		//testing data
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Pirep_model');
		
		$pilot_id = '3004'; //david sifuentes
		$mission_id = '336';
	
		
		echo 'Pilot ID: '.$pilot_id.'<br />';
		echo 'Mission ID: '.$mission_id.'<br />';
		
		$mission_award_return = $this->Pirep_model->mission_award($pilot_id, $mission_id);
		
		echo 'Status: '.$mission_award_return;
	
	}


	function recalculatehours($pilot_id = NULL){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Pirep_model');
		
		
		$this->Pirep_model->update_hours($pilot_id, NULL, 0);
		echo 'Pilot hours recalculated<br />';
	}

	
	function index($method = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		// Call all the cron jobs
		$this->Cron_model->pilot_status($active_compare_date);
		echo 'pilot_status done<br />';
		$this->Cron_model->pilot_avatar(NULL, $data);
		echo 'pilot_avatar done<br />';
		$this->Cron_model->pilot_signature(NULL, $data);
		echo 'pilot_signature done<br />';
		$this->Cron_model->pilot_deadhead();
		echo 'pilot_deadhead done<br />';
		
	}
	
	
	
	function awards_index()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		// Call all the cron jobs
		$this->Cron_model->award_mission_award();
		echo '----------award_mission_award done----------<br />';
		$this->Cron_model->award_certified_pilot_award();
		echo '----------award_certified_pilot_award done----------<br />';
		$this->Cron_model->award_online_award();
		echo '----------award_online_award done----------<br />';
		$this->Cron_model->award_european_award();
		echo '----------award_european_award done----------<br />';
	}
	
	function award_european_award(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->award_european_award();
	}
	
	function award_online_award(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->award_online_award();
	}
	
	function award_certified_pilot_award(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->award_certified_pilot_award();
	}
	
	
	function award_mission_award(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->award_mission_award();
	}
	
	/**
	 * Set inactive status for inactive non-MT pilots.
	 */
	function pilot_status()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		//call function
		$this->Cron_model->pilot_status($active_compare_date);
		
	}
	
	/**
	 * Generate forum avatar images for all active/inactive pilots.
	 * 
	 * @param string $username
	 */
	function pilot_avatar($username = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->pilot_avatar($username, $data);
			
	}
	
	/**
	 * Generate forum signature images for active/inactive pilots.
	 * 
	 * @param string $username
	 */
	function pilot_signature($username = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->pilot_signature($username, $data);
	}
	
	/**
	 * Process deadhead pilots who've not moved for three days
	 * 
	 * @param string $username
	 */
	function pilot_deadhead($username = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->model('Cron_model');
		
		$this->Cron_model->pilot_deadhead($username);
		
	}
	
	function pilot_awards($username = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		// Placeholder - Per default, check for awards for all pilots
	}
}

/* End of file */