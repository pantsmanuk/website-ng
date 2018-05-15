<?php
/* unfinished, but the calls are right. perhaps store data in the database relating to files along with allowed usergroups (check that unregistered has a usergroup) this will then obfuscate the actual paths to files served up for download - every little helps right? :)
It can also be used to generate new files or to read current files and then provide them for download.
*/


class Documents extends CI_Controller {

	function Documents()
	{
		parent::__construct();	
	}

	function download()
	{

	//initialise
	//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
  	//load addtional libraries
  	$this->load->helper('download');
	$this->load->helper('security');
	

		//set page and file
		//$page = $this->uri->segment(1, 0);
		$file = $this->uri->segment(3, 0);

		$file_path = $this->config->item('file_path').$file;

		
		//only do this if the file exists
		if (file_exists($file_path)){
		$data = file_get_contents($file_path); // Read the file's contents
		$name = $file;
		force_download($name, $data);
		}
		else{

			echo "The file you have requested does not appear to exist<br />
				If you followed a link, please contact the office so that this can be fixed";
		
		}


		//$this->load->view('front_page', $data);
	}

	
function save()
	{
		
	//initialise
	//grab global initialisation
	include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
  	//load addtional libraries
  	$this->load->helper('download');
	$this->load->helper('security');


		//grab file		
		$file_contents = $this->input->post('file_contents');
		$file_name = $this->input->post('file_name');
		
		
		if($file_contents!='' && $file_name!=''){
			force_download($file_name, $file_contents);
		}
		else{

			echo "The file you have requested does not appear to exist<br />
				If you followed a link, please file a bug report so that this can be fixed";
		
		}


		//$this->load->view('front_page', $data);
	}
}
?>
