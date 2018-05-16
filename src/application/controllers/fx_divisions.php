<?php
 
class Fx_divisions extends CI_Controller {

	function Fx_divisions()
	{
		parent::__construct();
	}
	
	function index()
	{
		
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//$this->load->library('date_fns');
		
		//do this if not logged in
		if ($this->session->userdata('logged_in') != TRUE){
	
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<feedback>'."\n";
			echo '	<error>'."\n";
			echo '		<code>login</code>'."\n";
			echo '	</error>'."\n";
			echo '</feedback>';
	 
		}

		//grab post data *******************************************************************************************************
		//set up counties array
		
		
		//grab all airports
		$query = $this->db->query("	SELECT 
											airports_data.icao as icao,
											airports_data.lat as lat,
											airports_data.long as lon,
											airports.name as name,
											hub.id as hub_id
											
									FROM airports
									
										LEFT JOIN airports_data
										ON airports.icao = airports_data.icao
										
										LEFT JOIN hub
										ON airports.icao = hub.icao
											
										");
				
		
		$airport_data = $query->result();	
		$num_rows = $query->num_rows();
		
		
		
		//compile output
		//output the data *******************************************************************************************************
		$output1 = '';
		$output2 = '';
		$output3 = '';
		
		$output1 .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$output1 .= '<airports>'."\n";
		
		
		foreach ($airport_data as $row)
			{
				
				$hub = '0';
				//check if this is a hub
				if($row->hub_id != ''){
					$hub = '1';
				}
				
				$output2 .= '	<airport hub="'.$hub.'>'."\n";
				$output2 .=  '		<icao>'.$row->icao."</icao>\n";
				$output2 .=  '		<name>'.$row->name."</name>\n";
				$output2 .=  '		<lat>'.$row->lat."</lat>\n";
				$output2 .=  '		<lon>'.$row->lon."</lon>\n";
				$output2 .=  '		<hub>'.$hub."</hub>\n";
				$output2 .=  '	</airport>'."\n";

			}
			
		$output3 =  '</airports>'."\n";
		
		
		header('Content-Type: text/xml');
		header("Cache-Control: no-cache, must-revalidate");
		echo $output1.$output2.$output3;
			
			
	//close fn_index
	}
	
	
	
//close class
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */