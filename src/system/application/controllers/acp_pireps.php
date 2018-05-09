<?php
 
class Acp_pireps extends CI_Controller {

	function Acp_pireps()
	{
		parent::__construct();
	}
	
	
	
	function edit($pirep_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($pirep_id == NULL){
			redirect('acp_pireps/validate');
		}
		
		$data['pirep_id'] = $pirep_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';
		
		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){
		
			//display a page not found message
			show_404('page');
			
		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){
					
			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);
																
			//update data in session
			$this->session->set_userdata($sessiondata);
		
			$current_pilot_username = $this->session->userdata['username'];
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$onlineoffline = $this->security->sanitize_filename($this->input->post('onlineoffline'));
			$altitude = $this->security->sanitize_filename($this->input->post('altitude'));
			$speed = $this->security->sanitize_filename($this->input->post('speed'));
			$approach = $this->security->sanitize_filename($this->input->post('approach'));
			$fuelburnt = $this->security->sanitize_filename($this->input->post('fuelburnt'));
			$comments = $this->security->sanitize_filename($this->input->post('comments'));
			
			$enginestart_dd = $this->security->sanitize_filename($this->input->post('enginestart_dd'));
			$enginestart_mo = $this->security->sanitize_filename($this->input->post('enginestart_mo'));
			$enginestart_yy = $this->security->sanitize_filename($this->input->post('enginestart_yy'));
			$enginestart_hh = $this->security->sanitize_filename($this->input->post('enginestart_hh'));
			$enginestart_mm = $this->security->sanitize_filename($this->input->post('enginestart_mm'));
			$takeoff_dd = $this->security->sanitize_filename($this->input->post('takeoff_dd'));
			$takeoff_mo = $this->security->sanitize_filename($this->input->post('takeoff_mo'));
			$takeoff_yy = $this->security->sanitize_filename($this->input->post('takeoff_yy'));
			$takeoff_hh = $this->security->sanitize_filename($this->input->post('takeoff_hh'));
			$takeoff_mm = $this->security->sanitize_filename($this->input->post('takeoff_mm'));
			$landing_dd = $this->security->sanitize_filename($this->input->post('landing_dd'));
			$landing_mo = $this->security->sanitize_filename($this->input->post('landing_mo'));
			$landing_yy = $this->security->sanitize_filename($this->input->post('landing_yy'));
			$landing_hh = $this->security->sanitize_filename($this->input->post('landing_hh'));
			$landing_mm = $this->security->sanitize_filename($this->input->post('landing_mm'));
			$engineoff_dd = $this->security->sanitize_filename($this->input->post('engineoff_dd'));
			$engineoff_mo = $this->security->sanitize_filename($this->input->post('engineoff_mo'));
			$engineoff_yy = $this->security->sanitize_filename($this->input->post('engineoff_yy'));
			$engineoff_hh = $this->security->sanitize_filename($this->input->post('engineoff_hh'));
			$engineoff_mm = $this->security->sanitize_filename($this->input->post('engineoff_mm'));
			
			
			
			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('onlineoffline', 'onlineoffline', 'required');
			$this->form_validation->set_rules('enginestart_dd', 'enginestart_dd', 'required');
			$this->form_validation->set_rules('enginestart_mo', 'enginestart_mo', 'required');
			$this->form_validation->set_rules('enginestart_yy', 'enginestart_yy', 'required');
			$this->form_validation->set_rules('enginestart_hh', 'enginestart_hh', 'required');
			$this->form_validation->set_rules('enginestart_mm', 'enginestart_mm', 'required');
			$this->form_validation->set_rules('takeoff_dd', 'takeoff_dd', 'required');
			$this->form_validation->set_rules('takeoff_mo', 'takeoff_mo', 'required');
			$this->form_validation->set_rules('takeoff_yy', 'takeoff_yy', 'required');
			$this->form_validation->set_rules('takeoff_hh', 'takeoff_hh', 'required');
			$this->form_validation->set_rules('takeoff_mm', 'takeoff_mm', 'required');
			$this->form_validation->set_rules('landing_dd', 'landing_dd', 'required');
			$this->form_validation->set_rules('landing_mo', 'landing_mo', 'required');
			$this->form_validation->set_rules('landing_yy', 'landing_yy', 'required');
			$this->form_validation->set_rules('landing_hh', 'landing_hh', 'required');
			$this->form_validation->set_rules('landing_mm', 'landing_mm', 'required');
			$this->form_validation->set_rules('engineoff_dd', 'engineoff_dd', 'required');
			$this->form_validation->set_rules('engineoff_mo', 'engineoff_mo', 'required');
			$this->form_validation->set_rules('engineoff_yy', 'engineoff_yy', 'required');
			$this->form_validation->set_rules('engineoff_hh', 'engineoff_hh', 'required');
			$this->form_validation->set_rules('engineoff_mm', 'engineoff_mm', 'required');
			
			if($this->form_validation->run() == FALSE){
				$validation = 0;
			}
			else{
				$validation = 1;
			}
		
			
			//need to determine whether or not this is a valid delete - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											pirep.id as id,
											pirep.username as username,
											pilots.fname as fname,
											pilots.sname as sname,
											pirep.hub as hub,
											aircraft.name as aircraft,
											pirep.onoffline as onoffline,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.cruisealt as cruisealt,
											pirep.cruisespd as cruisespd,
											pirep.approach as approach,
											pirep.fuelburnt as fuelburnt,
											pirep.comments as comments,
											pirep.circular_distance as gcd,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time,
											pirep.departure_time as departure_time,
											pirep.landing_time as landing_time,
											pirep.comments_mt as comments_mt,
											pirep.submitdate as submitdate,
											pirep.checked as checked,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name
													
											FROM pirep
											
												LEFT JOIN pilots 
												ON pilots.id = pirep.user_id
												
												LEFT JOIN aircraft 
												ON aircraft.id = pirep.aircraft
												
												LEFT JOIN airports as dep_icao
												ON dep_icao.ICAO = pirep.start_icao
												
												LEFT JOIN airports as arr_icao
												ON arr_icao.ICAO = pirep.end_icao
											
											WHERE pirep.id = '$pirep_id' 
											
											LIMIT 1
										");
										
			$result = $query->result_array();
			$num_results = $query->num_rows();
			
			//redirect if no return
			if($num_results < 1){
				redirect('acp_pireps/query/');
			}
			
			
			if($valid == 'true' && $validation == 1){
				
				//data has been submitted, array it and update the record
				
				//array update data
				$engine_start_time = $enginestart_yy.'-'.$enginestart_mo.'-'.$enginestart_dd.' '.$enginestart_hh.':'.$enginestart_mm.':00';
				$engine_stop_time = $engineoff_yy.'-'.$engineoff_mo.'-'.$engineoff_dd.' '.$engineoff_hh.':'.$engineoff_mm.':00';
				$departure_time = $takeoff_yy.'-'.$takeoff_mo.'-'.$takeoff_dd.' '.$takeoff_hh.':'.$takeoff_mm.':00';
				$landing_time = $landing_yy.'-'.$landing_mo.'-'.$landing_dd.' '.$landing_hh.':'.$landing_mm.':00';
				
				$pirep_data = array(
						'onoffline' => 'onlineoffline',
						'engine_start_time' => $engine_start_time,
						'engine_stop_time' => $engine_stop_time,
						'departure_time' => $departure_time,
						'landing_time' => $landing_time,
						'cruisealt' => $altitude,
						'cruisespd' => $speed,
						'approach' => $approach,
						'fuelburnt' => $fuelburnt,
						'comments' => $comments,
				);
				
				
				$id_val = $result['0']['id'];
				//perform the update from db
				$this->db->where('id', $id_val);
				$this->db->update('pirep', $this->db->escape($pirep_data));
				
				redirect('acp_pireps/query/'.$id_val);
				//redirect('acp_pireps/validate/');
				
				
			}
			// haven't had data submitted or failed validation
			else{
			
				//prepare dropdowns etc for output from database
				$onlineoffline = $result['0']['onoffline'];
				$altitude = $result['0']['cruisealt'];
				$speed = $result['0']['cruisespd'];
				$approach = $result['0']['approach'];
				$fuelburnt = $result['0']['fuelburnt'];
				$comments = $result['0']['comments'];
				
				$engine_start_time = strtotime($result['0']['engine_start_time']);
				$engine_stop_time = strtotime($result['0']['engine_stop_time']);
				$departure_time = strtotime($result['0']['departure_time']);
				$landing_time = strtotime($result['0']['landing_time']);
				
				$enginestart_dd = date('d', $engine_start_time);
				$enginestart_mo = date('m', $engine_start_time);
				$enginestart_yy = date('Y', $engine_start_time);
				$enginestart_hh = date('H', $engine_start_time);
				$enginestart_mm = date('i', $engine_start_time);
				$takeoff_dd = date('d', $departure_time);
				$takeoff_mo = date('m', $departure_time);
				$takeoff_yy = date('Y', $departure_time);
				$takeoff_hh = date('H', $departure_time);
				$takeoff_mm = date('i', $departure_time);
				$landing_dd = date('d', $landing_time);
				$landing_mo = date('m', $landing_time);
				$landing_yy = date('Y', $landing_time);
				$landing_hh = date('H', $landing_time);
				$landing_mm = date('i', $landing_time);
				$engineoff_dd = date('d', $engine_stop_time);
				$engineoff_mo = date('m', $engine_stop_time);
				$engineoff_yy = date('Y', $engine_stop_time);
				$engineoff_hh = date('H', $engine_stop_time);
				$engineoff_mm = date('i', $engine_stop_time);
				
				//define all vars
				$data['onlineoffline'] = $onlineoffline;
				$data['approach'] = $approach;
				
				//fixed vars
				$data['start_icao'] = $result['0']['start_icao'];
				$data['end_icao'] = $result['0']['end_icao'];
				$data['dep_name'] = $result['0']['dep_name'];
				$data['arr_name'] = $result['0']['arr_name'];
				$data['aircraft'] = $result['0']['aircraft'];
				$data['passengers'] = $result['0']['passengers'];
				$data['cargo'] = $result['0']['cargo'];
				
				//dropdowns
				$data['takeoff_dd'] = $takeoff_dd;
				$data['takeoff_mo'] = $takeoff_mo;
				$data['takeoff_yy'] = $takeoff_yy;
				
				$data['enginestart_dd'] = $enginestart_dd;
				$data['enginestart_mo'] = $enginestart_mo;
				$data['enginestart_yy'] = $enginestart_yy;
				
				$data['landing_dd'] = $landing_dd;
				$data['landing_mo'] = $landing_mo;
				$data['landing_yy'] = $landing_yy;
				
				$data['engineoff_dd'] = $engineoff_dd;
				$data['engineoff_mo'] = $engineoff_mo;
				$data['engineoff_yy'] = $engineoff_yy;
				
				//define form elements
				
				$data['enginestart_hh'] = array( 'name' => 'enginestart_hh','id' => 'enginestart_hh','value' => $enginestart_hh, 'maxlength' => '2','size' => '2');
				$data['enginestart_mm'] = array( 'name' => 'enginestart_mm','id' => 'enginestart_mm','value' => $enginestart_mm, 'maxlength' => '2','size' => '2');
				
				$data['takeoff_hh'] = array( 'name' => 'takeoff_hh','id' => 'takeoff_hh','value' => $takeoff_hh, 'maxlength' => '2','size' => '2');
				$data['takeoff_mm'] = array( 'name' => 'takeoff_mm','id' => 'takeoff_mm','value' => $takeoff_mm, 'maxlength' => '2','size' => '2');
				
				$data['landing_hh'] = array( 'name' => 'landing_hh','id' => 'landing_hh','value' => $landing_hh, 'maxlength' => '2','size' => '2');
				$data['landing_mm'] = array( 'name' => 'landing_mm','id' => 'landing_mm','value' => $landing_mm, 'maxlength' => '2','size' => '2');
				
				$data['engineoff_hh'] = array( 'name' => 'engineoff_hh','id' => 'engineoff_hh','value' => $engineoff_hh, 'maxlength' => '2','size' => '2');
				$data['engineoff_mm'] = array( 'name' => 'engineoff_mm','id' => 'engineoff_mm','value' => $engineoff_mm, 'maxlength' => '2','size' => '2');
				$data['altitude'] = array( 'name' => 'altitude','id' => 'altitude','value' => $altitude, 'maxlength' => '100','size' => '10','style' => 'width:20%');
				$data['speed'] = array( 'name' => 'speed','id' => 'speed','value' => $speed, 'maxlength' => '20','size' => '10','style' => 'width:20%');
				$data['fuelburnt'] = array( 'name' => 'fuelburnt','id' => 'fuelburnt','value' => $fuelburnt, 'maxlength' => '100','size' => '10','style' => 'width:20%');
				$data['comments'] = array( 'name' => 'comments','id' => 'comments','value' => $comments, 'rows' => '5','cols' => '12','style' => 'width:50%');
				
				//define all the arrays			
				$data['country_array'] = array();
				$data['hub_array'] = array();
				$data['otherva_array'] = array('No' => 'No','I was' => 'I was','I am' => 'I am');
				$data['aircraft_array'] = array();
				$data['onlineoffline_array'] = array('0' => 'Offline', '1' => 'Online (Vatsim)', '2' => 'Online (IVAO)', '3' => 'Online (Other)');
				$data['approach_array'] = array('Visual' => 'Visual', 'ILS' => 'ILS', 'NDB' => 'NDB', 'VOR' => 'VOR');
				//$data['alt_unit_array'] = array('m' => 'Metres', 'ft' => 'Feet');
				//$data['fuel_units_array'] = array('usgal' => 'US Gallons', 'impgal' => 'Imperial Gallons', 'lbs' => 'Pounds');
				//$data['speed_units_array'] = array('ias' => 'IAS', 'tas' => 'TAS', 'gs' => 'GS', 'mach' => 'Mach');
				$data['flightsim_array'] = array();
				$data['dobday_array'] = array('' => '');
				$data['dobmonth_array'] = array('' => '');
				$data['dobyear_array'] = array('' => '');
				
				//day_array
				$i = 1;
				while($i <= 31){
					$ival = $i;
					if($i < 10){
						$ival = '0'.$i;
					}
					
					$data['dobday_array'][$ival] = $ival;
					$i++;
				}
				
				//month_array
				$i = 1;
				while($i <= 12){
					$ival = $i;
					if($i < 10){
						$ival = '0'.$i;
					}
				
					$data['dobmonth_array'][$ival] = $ival;
					$i++;
				}
				

				//year_array
				$current_year = date('Y', time());
				
				$i = $current_year - 8;
				while($i >= ($current_year - 100)){
					$data['dobyear_array'][$i] = $i;
					$i--;
				}
				
				//year_array
				$i = $current_year;
				while($i >= ($current_year - 5)){
					$data['pirepyear_array'][$i] = $i;
					$i--;
				}
				
				//output page
				$data['page_title'] = 'Admin Control Panel';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_pirepedit', $data);
			}

			
		}
		//invalid admin login
		elseif($is_admin == '1'){
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pireps/edit/'.$pirep_id;										
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}
	


	function delete($pirep_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($pirep_id == NULL){
			redirect('acp_pireps/validate');
		}
		
		
		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){
		
			//display a page not found message
			show_404('page');
			
		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){
			
			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);
																
			//update data in session
			$this->session->set_userdata($sessiondata);
		
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			
			$current_pilot_username = $this->session->userdata['username'];
			
			
			//need to determine whether or not this is a valid delete - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											pirep.id as id,
											pirep.username as username,
											pilots.fname as fname,
											pilots.fname as sname,
											pirep.hub as hub,
											aircraft.name as aircraft,
											pirep.onoffline as onoffline,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.cruisealt as cruisealt,
											pirep.cruisespd as cruisespd,
											pirep.approach as approach,
											pirep.fuelburnt as fuelburnt,
											pirep.comments as comments,
											pirep.circular_distance as gcd,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time,
											pirep.departure_time as departure_time,
											pirep.landing_time as landing_time,
											pirep.comments_mt as comments_mt,
											pirep.submitdate as submitdate,
											pirep.checked as checked,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name
													
											FROM pirep
											
												LEFT JOIN pilots 
												ON pilots.id = pirep.user_id
												
												LEFT JOIN aircraft 
												ON aircraft.id = pirep.aircraft
												
												LEFT JOIN airports as dep_icao
												ON dep_icao.ICAO = pirep.start_icao
												
												LEFT JOIN airports as arr_icao
												ON arr_icao.ICAO = pirep.end_icao
											
											WHERE pirep.id = '$pirep_id' 
											
											LIMIT 1
										");
										
			$result = $query->result_array();
			$num_results = $query->num_rows();
			
			if($valid == 'true'){
				
				//if we actually got a hit back, then we're valid
				if($num_results > 0){
					
					//only permit for unchecked PIREP
					if($result['0']['checked'] != '1'){
					
						//use the db returned value as an extra check
						$id_val = $result['0']['id'];
						//perform the delete from db
						$this->db->where('id', $id_val);
						$this->db->delete('pirep');
						
						//clear out any messages for this pirep
						$this->db->where('pirep_id', $id_val);
						$this->db->delete('pirep_queries');
						
					}
					
				}
				
				//now redirect back to index
				redirect('acp_pireps/validate');
				
			}
			else{
				//if there is such a result
				if($num_results > 0){
					$data['aircraft'] = $result['0']['aircraft'];
					$data['username'] = $result['0']['username'];
					$data['passengers'] = $result['0']['passengers'];
					$data['submitdate'] = $result['0']['submitdate'];
					$data['cargo'] = $result['0']['cargo'];
					$data['dep_name'] = $result['0']['dep_name'];
					$data['start_icao'] = $result['0']['start_icao'];
					$data['end_icao'] = $result['0']['end_icao'];
					$data['arr_name'] = $result['0']['arr_name'];
					$data['pirep_id'] = $pirep_id;
					
					//output confirmation page
					$data['page_title'] = 'Delete confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/admincp/acp_pirepdelete', $data);
				}
				else{
					redirect('acp_pireps/validate');
				}
				
			}
		
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pireps/delete/'.$pirep_id;										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}	
	
	
	
	function query($pirep_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($pirep_id == NULL){
			redirect('acp_pireps/validate');
		}
		
		
		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){
		
			//display a page not found message
			show_404('page');
			
		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){
			
			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);
																
			//update data in session
			$this->session->set_userdata($sessiondata);
		
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$mt_comment = $this->security->sanitize_filename($this->input->post('mt_comment'));
			$query_toggle = $this->security->sanitize_filename($this->input->post('query_toggle'));
						
			$current_pilot_username = $this->session->userdata['username'];
			
			
			//need to determine whether or not this is a valid pirep - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											pirep.id as id,
											pirep.username as username,
											pilots.fname as fname,
											pilots.sname as sname,
											pirep.hub as hub,
											aircraft.name as aircraft,
											networks.name as onoffline,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.cruisealt as cruisealt,
											pirep.cruisespd as cruisespd,
											pirep.approach as approach,
											pirep.fuelburnt as fuelburnt,
											pirep.comments as comments,
											pirep.circular_distance as gcd,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time,
											pirep.departure_time as departure_time,
											pirep.landing_time as landing_time,
											pirep.comments_mt as comments_mt,
											pirep.submitdate as submitdate,
											pirep.checked as checked,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name
													
											FROM pirep
											
												LEFT JOIN pilots 
												ON pilots.id = pirep.user_id
												
												LEFT JOIN aircraft 
												ON aircraft.id = pirep.aircraft
												
												LEFT JOIN airports as dep_icao
												ON dep_icao.ICAO = pirep.start_icao
												
												LEFT JOIN airports as arr_icao
												ON arr_icao.ICAO = pirep.end_icao
												
												LEFT JOIN networks
												ON networks.id = pirep.onoffline
											
											WHERE pirep.id = '$pirep_id' 
											
											LIMIT 1
										");
										
			$result = $query->result_array();
			$num_results = $query->num_rows();
			
			if($valid == 'true'){
				
				//if we actually got a hit back, then we're valid
				if($num_results > 0){
					
					//only permit for unchecked PIREP
					if($result['0']['checked'] != '1'){
						
						//global array
						$perform_update = 0;
							
						if(($result['0']['checked'] == '0' || $result['0']['checked'] == '4') && $query_toggle == 'queried'){
							//using value of 3 to indicate a queried pirep. 0 for unchecked, 1 for checked, 2 for invalid					
							$pirep_data['checked'] = 3;
							$perform_update = 1;
							
						}
						elseif($result['0']['checked'] == '3' && $query_toggle == 'unchecked'){
							//unquery it
							$pirep_data['checked'] = 0;
							$perform_update = 1;
						}
						
						if($perform_update == 1){
					
							//use the db returned value as an extra check
							$id_val = $result['0']['id'];
							//perform the update from db
							$this->db->where('id', $id_val);
							$this->db->update('pirep', $this->db->escape($pirep_data));
						
						}
						
						//if we have a non-blank comment, insert it
						if($mt_comment != ''){
							$pirep_queries_data = array(
												'user_id' => $this->session->userdata('user_id'),
												'pirep_id' => $result['0']['id'],
												'from_pilot' => '0',
												'comment' => $mt_comment,
												'submitted' => $gmt_mysql_datetime,
												);
												
							$this->db->insert('pirep_queries', $this->db->escape($pirep_queries_data));
								
						}
						
						if($query_toggle == 'approve'){
							//send on to approval
							redirect('acp_pireps/approve/'.$pirep_id);
						}
						
						if($query_toggle == 'delete'){
							//send on to deletion
							redirect('acp_pireps/delete/'.$pirep_id);
						}
						
						
					}
					
				}
				
				//now redirect back to index
				redirect('acp_pireps/validate');
				
			}
			else{
				//if there is such a result
				if($num_results > 0){
					$data['aircraft'] = $result['0']['aircraft'];
					$data['engine_start_time'] = $result['0']['engine_start_time'];
					$data['engine_stop_time'] = $result['0']['engine_stop_time'];
					$data['departure_time'] = $result['0']['departure_time'];
					$data['landing_time'] = $result['0']['landing_time'];
					$data['onoffline'] = $result['0']['onoffline'];
					$data['username'] = $result['0']['username'];
					$data['fname'] = $result['0']['fname'];
					$data['sname'] = $result['0']['sname'];
					$data['passengers'] = $result['0']['passengers'];
					$data['submitdate'] = $result['0']['submitdate'];
					$data['cargo'] = $result['0']['cargo'];
					$data['dep_name'] = $result['0']['dep_name'];
					$data['start_icao'] = $result['0']['start_icao'];
					$data['end_icao'] = $result['0']['end_icao'];
					$data['arr_name'] = $result['0']['arr_name'];
					$data['pirep_id'] = $pirep_id;
					
					
					//form input
					$data['mt_comment'] = array(
								  'name'        => 'mt_comment',
								  'id'          => 'mt_comment',
								  'value'       => '',
								  'rows'   => '5',
								  'cols'        => '50',
								);
								
								
					//pull all the query messages
					$query = $this->db->query("	SELECT 	
													pirep_queries.id as id,
													pirep_queries.user_id as user_id,
													pirep_queries.pirep_id as pirep_id,
													pirep_queries.from_pilot as from_pilot,
													pirep_queries.comment as comment,
													pirep_queries.submitted as submitted,
													pilots.id as pilot_id,
													pilots.username as username,
													pilots.fname as fname
															
													FROM pirep_queries
													
														LEFT JOIN pilots
														ON pilots.id = pirep_queries.user_id
													
													WHERE pirep_queries.pirep_id = '$pirep_id' 
													
													ORDER BY pirep_queries.submitted
												");
												
					$data['messages'] = $query->result();		
					
					//output confirmation page
					$data['page_title'] = 'Query Pirep';
					$data['no_links'] = '1';
					$this->view_fns->view('global/admincp/acp_pirepquery', $data);
				}
				else{
					redirect('acp_pireps/validate');
				}
				
			}
		
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
			
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pireps/query/'.$pirep_id;										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}	
	
	
function approve($pirep_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Pirep_model');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($pirep_id == NULL){
			redirect('acp_pireps/validate');
		}
		
		
		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){
		
			//display a page not found message
			show_404('page');
			
		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){
			
			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);
																
			//update data in session
			$this->session->set_userdata($sessiondata);
		
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			
			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];
			
			
			//need to determine whether or not this is a valid pirep - as well as grabbing details for confirm page
			$query = $this->db->query("	SELECT 	
											pirep.id as id,
											pirep.username as username,
											pilots.fname as fname,
											pilots.fname as sname,
											pilots.id as pilot_id,
											pilots.rank as pilot_rank,
											pirep.hub as hub,
											aircraft.name as aircraft,
											pirep.onoffline as onoffline,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.cruisealt as cruisealt,
											pirep.cruisespd as cruisespd,
											pirep.approach as approach,
											pirep.fuelburnt as fuelburnt,
											pirep.comments as comments,
											pirep.circular_distance as gcd,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time,
											pirep.departure_time as departure_time,
											pirep.landing_time as landing_time,
											pirep.comments_mt as comments_mt,
											pirep.submitdate as submitdate,
											pirep.checked as checked,
											pirep.award_id as award_id,
											pirep.tour_id as tour_id,
											pirep.tour_leg_id as tour_leg_id,
											pirep.mission_id as mission_id,
											dep_icao.Name as dep_name,
											arr_icao.Name as arr_name
													
											FROM pirep
											
												LEFT JOIN pilots 
												ON pilots.id = pirep.user_id
												
												LEFT JOIN aircraft 
												ON aircraft.id = pirep.aircraft
												
												LEFT JOIN airports as dep_icao
												ON dep_icao.ICAO = pirep.start_icao
												
												LEFT JOIN airports as arr_icao
												ON arr_icao.ICAO = pirep.end_icao
											
											WHERE pirep.id = '$pirep_id' 
											
											LIMIT 1
										");
										
			$result = $query->result_array();
			$num_results = $query->num_rows();
			
			if($valid == 'true'){
				
				//if we actually got a hit back, then we're valid
				if($num_results > 0){
					
					//only permit for unchecked PIREP (or queried/responded)
					if($result['0']['checked'] == '0' || $result['0']['checked'] == '3' || $result['0']['checked'] == '4'){
							
						$pilot_id = $result['0']['pilot_id'];
						$current_rank_id = $result['0']['pilot_rank'];
							
						//first update status on the pirep					
						$pirep_data = array(
							'checked' => '1',
						);
						
						//use the db returned value as an extra check
						$id_val = $result['0']['id'];
						//perform the update from db
						$this->db->where('id', $id_val);
						$this->db->update('pirep', $this->db->escape($pirep_data));
						
						//clear out any messages for this pirep
						$this->db->where('pirep_id', $id_val);
						$this->db->delete('pirep_queries');
						
						//check rank upgrade and update hours
						$promoted = $this->Pirep_model->update_hours($pilot_id, $current_rank_id, 0);
						
						//finally, if we're running an award from completion of this flight, execute award script
						$tour_id = $result['0']['tour_id'];
						$mission_id = $result['0']['mission_id'];
						$award_id = $result['0']['award_id'];
						
						//call award function and pass the tour_id/mission_id 
						if($tour_id != ''){
							$tour_award_return = $this->Pirep_model->tour_award($pilot_id, $tour_id, $award_id, $gmt_mysql_datetime);
						}
						
						if($mission_id != ''){
							$mission_award_return = $this->Pirep_model->mission_award($pilot_id, $mission_id);
						}
						
						redirect('acp_pireps/validate');
					}
					
				}
				
				//now redirect back to index
				redirect('acp_pireps/validate');
				
			}
			else{
				//if there is such a result
				if($num_results > 0){
					$data['aircraft'] = $result['0']['aircraft'];
					$data['username'] = $result['0']['username'];
					$data['passengers'] = $result['0']['passengers'];
					$data['submitdate'] = $result['0']['submitdate'];
					$data['cargo'] = $result['0']['cargo'];
					$data['dep_name'] = $result['0']['dep_name'];
					$data['start_icao'] = $result['0']['start_icao'];
					$data['end_icao'] = $result['0']['end_icao'];
					$data['arr_name'] = $result['0']['arr_name'];
					$data['pirep_id'] = $pirep_id;
					
					//form input
					$data['mt_comment'] = array(
								  'name'        => 'mt_comment',
								  'id'          => 'mt_comment',
								  'value'       => $result['0']['comments_mt'],
								  'rows'   => '10',
								  'cols'        => '50',
								);
					
					//output confirmation page
					$data['page_title'] = 'Query confirmation';
					$data['no_links'] = '1';
					$this->view_fns->view('global/admincp/acp_pirepapprove', $data);
				}
				else{
					redirect('acp_pireps/validate');
				}
				
			}
		
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pireps/approve/'.$pirep_id;										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}	
	
	
	
	function validate($offset = 0)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') != '1'){
		
			//display a page not found message
			show_404('page');
			
		}
		//not an admin
		elseif($is_admin != '1'){
			redirect('');
		}
		elseif($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1'){
			
			//define session data
			$sessiondata = array(
				'admincp_time' => $gmt_mysql_datetime,
							);
																
			//update data in session
			$this->session->set_userdata($sessiondata);
			
			//grab all unchecked pireps from the database
			$query = $this->db->query("	SELECT 	
											pirep.id as id,
											pilots.id as pilot_id,
											pirep.username as username,
											pilots.fname as fname,
											pilots.sname as sname,
											pirep.hub as hub,
											aircraft.name as aircraft,
											pirep.onoffline as onoffline,
											pirep.start_icao as start_icao,
											pirep.end_icao as end_icao,
											pirep.passengers as passengers,
											pirep.cargo as cargo,
											pirep.cruisealt as cruisealt,
											pirep.cruisespd as cruisespd,
											pirep.approach as approach,
											pirep.fuelburnt as fuelburnt,
											pirep.comments as comments,
											pirep.circular_distance as gcd,
											pirep.engine_start_time as engine_start_time,
											pirep.engine_stop_time as engine_stop_time,
											pirep.departure_time as departure_time,
											pirep.landing_time as landing_time,
											pirep.comments_mt as comments_mt,
											pirep.checked as checked,
											pirep.submitdate as submitdate
													
											FROM pirep
											
												LEFT JOIN pilots 
												ON pilots.id = pirep.user_id
												
												LEFT JOIN aircraft 
												ON aircraft.id = pirep.aircraft
											
											WHERE pirep.checked = '0' 
											OR pirep.checked = '3'
											OR pirep.checked = '4' 
											
											ORDER BY pirep.submitdate ASC
										");
				
			$data['result'] =  $query->result();	
			$data['num_rows'] =  $query->num_rows();	
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$username = $this->security->sanitize_filename($this->input->post('username'));
			$password = $this->security->sanitize_filename($this->input->post('password'));
			
			
			//paginatipon
			if($offset == NULL || $offset == ''){
				$offset = 0;
			}
			
			$data['offset'] = $offset;
			$data['limit'] = '10';
			
			$pag_config['base_url'] = $data['base_url'].'acp_pireps/validate/';
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 3;
			
			$this->pagination->initialize($pag_config); 
			
			

			//output page
			$data['page_title'] = 'ACP - Pirep Validation';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_pirepvalidate', $data);
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_pireps/validate/'.$offset.'/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}
	
	
}
?>