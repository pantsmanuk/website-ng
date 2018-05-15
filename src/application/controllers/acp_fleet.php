<?php
 
class Acp_fleet extends CI_Controller {

	function Acp_fleet()
	{
		parent::__construct();	
	}
	
	
	
	function edit($aircraft_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($aircraft_id == NULL){
			redirect('acp_fleet/manage');
		}
		
		if($aircraft_id > 0){
			$data['mode'] = 'Edit';
		}
		else{
			$data['mode'] = 'Create';
		}
		
		$data['aircraft_id'] = $aircraft_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';
		
		$data['allowed_types'] = 'jpg|jpeg';
		$data['max_size'] = '75';
		$data['max_width'] = '200';
		$data['max_height'] = '350';
		
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
			$current_pilot_user_id = $this->session->userdata['user_id'];
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$name = $this->security->sanitize_filename($this->input->post('name'));
			$clss = $this->security->sanitize_filename($this->input->post('clss'));
			$pax = $this->security->sanitize_filename($this->input->post('pax'));
			$cargo = $this->security->sanitize_filename($this->input->post('cargo'));
			$division = $this->security->sanitize_filename($this->input->post('division'));
			$in_fleet = $this->security->sanitize_filename($this->input->post('in_fleet'));
			$charter = $this->security->sanitize_filename($this->input->post('charter'));
			$enabled = $this->security->sanitize_filename($this->input->post('enabled'));
			$charter = $this->security->sanitize_filename($this->input->post('charter'));
			$icao_code = $this->security->sanitize_filename($this->input->post('icao_code'));
			$variant = $this->security->sanitize_filename($this->input->post('variant'));
			$aircraft_type = $this->security->sanitize_filename($this->input->post('aircraft_type'));
			$description = htmlspecialchars($this->input->post('description'));
			
			$length = $this->security->sanitize_filename($this->input->post('length'));
			$wingspan = $this->security->sanitize_filename($this->input->post('wingspan'));
			$height = $this->security->sanitize_filename($this->input->post('height'));
			$engine = htmlspecialchars($this->input->post('engine'));
			$engine_manufacturer = htmlspecialchars($this->input->post('engine_manufacturer'));
			$cruise_speed = $this->security->sanitize_filename($this->input->post('cruise_speed'));
			$service_ceiling = $this->security->sanitize_filename($this->input->post('service_ceiling'));
			$gross_weight = $this->security->sanitize_filename($this->input->post('gross_weight'));
			$crew = $this->security->sanitize_filename($this->input->post('crew'));
			$price = $this->security->sanitize_filename($this->input->post('price'));
			$manufacturer = $this->security->sanitize_filename($this->input->post('manufacturer'));
			$oew = $this->security->sanitize_filename($this->input->post('oew'));
			$mtow = $this->security->sanitize_filename($this->input->post('mtow'));
			$fuel_capacity = $this->security->sanitize_filename($this->input->post('fuel_capacity'));
			$fuel_weight = $this->security->sanitize_filename($this->input->post('fuel_weight'));
			$long_range_altitude = $this->security->sanitize_filename($this->input->post('long_range_altitude'));
			$long_range_speed = $this->security->sanitize_filename($this->input->post('long_range_speed'));
			$max_speed = $this->security->sanitize_filename($this->input->post('max_speed'));
			$range_mload = $this->security->sanitize_filename($this->input->post('range_mload'));
			$range_mfuel = $this->security->sanitize_filename($this->input->post('range_mfuel'));
			$engine_thrust = $this->security->sanitize_filename($this->input->post('engine_thrust'));
			$to_rwy_length_min = $this->security->sanitize_filename($this->input->post('to_rwy_length_min'));
			$to_rwy_length_max = $this->security->sanitize_filename($this->input->post('to_rwy_length_max'));
			$land_rwy_length = $this->security->sanitize_filename($this->input->post('land_rwy_length'));
			$v_rotate = $this->security->sanitize_filename($this->input->post('v_rotate'));
			$v_approach = $this->security->sanitize_filename($this->input->post('v_approach'));
			$flaps_rotate = $this->security->sanitize_filename($this->input->post('flaps_rotate'));
			$flaps_approach = $this->security->sanitize_filename($this->input->post('flaps_approach'));
			
			$maximum_climb_rate = $this->security->sanitize_filename($this->input->post('maximum_climb_rate'));
			$maximum_desc_rate = $this->security->sanitize_filename($this->input->post('maximum_desc_rate'));
			
			
			
			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('name', 'name', 'required');
			$this->form_validation->set_rules('clss', 'clss', 'required');
			$this->form_validation->set_rules('pax', 'pax', 'required');
			$this->form_validation->set_rules('cargo', 'cargo', 'required');
			$this->form_validation->set_rules('division', 'division', 'required');
			$this->form_validation->set_rules('in_fleet', 'in_fleet', 'required');
			$this->form_validation->set_rules('charter', 'charter', 'required');
			$this->form_validation->set_rules('enabled', 'enabled', 'required');
			$this->form_validation->set_rules('aircraft_type', 'aircraft_type', 'required');
			
			
			if($this->form_validation->run() == FALSE){
				$validation = 0;
			}
			else{
				$validation = 1;
			}

			if($aircraft_id > 0){
			
				//need to determine whether or not this is a valid aircraft - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												aircraft.id,
												aircraft.name,
												aircraft.clss,	
												aircraft.pax,
												aircraft.cargo,
												aircraft.division,
												aircraft.in_fleet,
												aircraft.charter,
												aircraft.enabled,
												aircraft.icao_code,
												aircraft.variant,
												aircraft.aircraft_type,
												aircraft.description,
												aircraft.length,
												aircraft.wingspan,
												aircraft.height,
												aircraft.engine,
												aircraft.engine_manufacturer,
												aircraft.cruise_speed,
												aircraft.service_ceiling,
												aircraft.gross_weight,
												aircraft.crew,
												aircraft.price,
												aircraft.manufacturer,
												aircraft.oew,
												aircraft.mtow,
												aircraft.fuel_capacity,
												aircraft.fuel_weight,
												aircraft.long_range_altitude,
												aircraft.long_range_speed,
												aircraft.max_speed,
												aircraft.range_mload,
												aircraft.range_mfuel,
												aircraft.engine_thrust,
												aircraft.to_rwy_length_min,
												aircraft.to_rwy_length_max,
												aircraft.land_rwy_length,
												aircraft.v_rotate,
												aircraft.v_approach,
												aircraft.flaps_rotate,
												aircraft.flaps_approach,
												aircraft.maximum_climb_rate,
												aircraft.maximum_desc_rate
												
														
												FROM aircraft
													
												WHERE aircraft.id = '$aircraft_id'
												
												LIMIT 1
											");
											
				$result = $query->result_array();
				$num_results = $query->num_rows();
				
				//if no return, set create new
				if($num_results < 1){
					redirect('acp_fleet/manage/');
				}
			
			}
			
			if($valid == 'true' && $validation == 1){
				
				
				//data has been submitted, array it and update the record
				if($length == ''){ $length = NULL; }
				if($wingspan == ''){ $wingspan = NULL; }
				if($height == ''){ $height = NULL; }
				
				if($cruise_speed == ''){ $cruise_speed = NULL; }
				if($service_ceiling == ''){ $service_ceiling = NULL; }
				if($gross_weight == ''){ $gross_weight = NULL; }
				if($price == ''){ $price = NULL; }
				
				if($crew == ''){ $crew = NULL; }
				
				if($oew == ''){ $oew = NULL; }
				if($mtow == ''){ $mtow = NULL; }
				if($fuel_capacity == ''){ $fuel_capacity = NULL; }
				if($fuel_weight == ''){ $fuel_weight = NULL; }
				
				if($long_range_speed == ''){ $long_range_speed = NULL; }
				if($max_speed == ''){ $max_speed = NULL; }
				if($range_mload == ''){ $range_mload = NULL; }
				if($range_mfuel == ''){ $range_mfuel = NULL; }
				
				if($to_rwy_length_min == ''){ $to_rwy_length_min = NULL; }
				if($to_rwy_length_max == ''){ $to_rwy_length_max = NULL; }
				if($land_rwy_length == ''){ $land_rwy_length = NULL; }
				if($v_rotate == ''){ $v_rotate = NULL; }
				if($v_approach == ''){ $v_approach = NULL; }
				
				
				
				if($variant == ''){
					$variant = NULL;
				}
				
				
				if($length != '' && is_numeric($length)){ $length_in = ($length*100); } else{ $length_in = $length; }
				if($wingspan != '' && is_numeric($wingspan)){ $wingspan_in = ($wingspan*100); } else{ $wingspan_in = $wingspan; }
				if($height != '' && is_numeric($height)){ $height_in = ($height*100); } else{ $height_in = $height; }
				if($gross_weight != '' && is_numeric($gross_weight)){ $gross_weight_in = ($gross_weight*100); } else{ $gross_weight_in = $gross_weight; }
				if($price != '' && is_numeric($price)){ $price_in = ($price*1000000); } else{ $price_in = $price; }
				
				if($maximum_climb_rate != '' && is_numeric($maximum_climb_rate)){ $maximum_climb_rate_in = $maximum_climb_rate; } else{ $maximum_climb_rate_in = '3600'; }
				if($maximum_desc_rate != '' && is_numeric($maximum_desc_rate)){ $maximum_desc_rate_in = $maximum_desc_rate; } else{ $maximum_desc_rate_in = '3000'; }
				
				//handle mach conversion
				if(strtoupper(substr($long_range_speed, 0, 1)) == 'M'){
					$long_range_speed = substr($long_range_speed,1);
					if(is_numeric($long_range_speed)){
						$long_range_speed = $long_range_speed*600;
					}
				}
				elseif(strtoupper(substr($max_speed, -1)) == 'M'){
					$long_range_speed = substr($long_range_speed,0,-1);
					if(is_numeric($long_range_speed)){
						$long_range_speed = $long_range_speed*600;
					}
				}
				
				if(strtoupper(substr($max_speed, 0, 1)) == 'M'){
					$max_speed = substr($max_speed,1);
					if(is_numeric($max_speed)){
						$max_speed = $max_speed*600;
					}
				}
				elseif(strtoupper(substr($max_speed, -1)) == 'M'){
					$max_speed = substr($max_speed,0,-1);
					if(is_numeric($max_speed)){
						$max_speed = $max_speed*600;
					}
				}
				
				$aircraft_data = array(
						'name' => $name,
						'clss' => $clss,
						'pax' => $pax,
						'cargo' => $cargo,
						'division' => $division,
						'in_fleet' => $in_fleet,
						'charter' => $charter,
						'enabled' => $enabled,
						'rank' => $clss+1,
						'icao_code' => $icao_code,
						'variant' => $variant,
						'aircraft_type' => $aircraft_type,
						'description' => $description,
						'length' => $length_in,
						'wingspan' => $wingspan_in,
						'height' => $height_in,
						'engine' => $engine,
						'engine_manufacturer' => $engine_manufacturer,
						'cruise_speed' => $cruise_speed,
						'service_ceiling' => $service_ceiling,
						'gross_weight' => $gross_weight_in,
						'crew' => $crew,
						'price' => $price_in,
						'manufacturer' => $manufacturer,
						'oew' => $oew,
						'mtow' => $mtow,
						'fuel_capacity' => $fuel_capacity,
						'fuel_weight' => $fuel_weight,
						'long_range_altitude' => $long_range_altitude,
						'long_range_speed' => $long_range_speed,
						'max_speed' => $max_speed,
						'range_mload' => $range_mload,
						'range_mfuel' => $range_mfuel,
						'engine_thrust' => $engine_thrust,
						'to_rwy_length_min' => $to_rwy_length_min,
						'to_rwy_length_max' => $to_rwy_length_max,
						'land_rwy_length' => $land_rwy_length,
						'v_rotate' => $v_rotate,
						'v_approach' => $v_approach,
						'flaps_rotate' => $flaps_rotate,
						'flaps_approach' => $flaps_approach,
						'maximum_climb_rate' => $maximum_climb_rate_in,
						'maximum_desc_rate' => $maximum_desc_rate_in,
						'modified' => $gmt_mysql_datetime,
				);
				
				
				//if we are editing
				if($aircraft_id > 0){
					
					
					
					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('aircraft', $this->db->escape($aircraft_data));
				}
				else{
				
					$aircraft_data['submitted'] = $gmt_mysql_datetime;
					$aircraft_data['submitted_by'] = $current_pilot_user_id;
				
					//we are creating a new record
					$this->db->insert('aircraft', $this->db->escape($aircraft_data));
					
					//grab the record id for the upload
					$query = $this->db->query("	SELECT 	
													aircraft.id,
													
													FROM aircraft
														
													WHERE aircraft.name = '$name'
													AND aircraft.submitted = '$gmt_mysql_datetime'
													AND aircraft.submitted_by = '$current_pilot_user_id'
													
													LIMIT 1
												");
												
					$result = $query->result_array();
					$num_results = $query->num_rows();
					
					if($num_results > 0){
						$aircraft_id = $result['0']['id'];
					}
				}
				
					if($aircraft_id > 0){
					
						// do upload
						$destination_path = $this->config->item('base_path').'assets/uploads/aircraft/'.$aircraft_id.'/';
						$config['upload_path'] = $this->config->item('base_path').'assets/uploads/tmp/';
						$config['allowed_types'] = $data['allowed_types'];
						$config['max_size']	= $data['max_size'];
						$config['max_width']  = $data['max_width'];
						$config['max_height']  = $data['max_height'];
						
						
							
						//sort out upload folder
						if(!is_dir($destination_path)){
							//create path
							if(!mkdir($destination_path, 0755, true)){
								$data['error'] .= 'Could not create destination folder: '.$destination_path.'<br />';
							}
						}
						
						$this->load->library('upload', $config);
					
						if ( ! $this->upload->do_upload())
						{
							
							$upload_data = array('upload_data' => $this->upload->data());
							
							//if we did upload, but error'd
							if($upload_data['upload_data']['file_size'] > 0){
									$data['error'] .= $this->upload->display_errors()
								.' File must be smaller than '.$config['max_size'].'k and no bigger than '.$config['max_width']
								.'x'.$config['max_height'].'. Allowed file types are '.$config['allowed_types'];
							}
							//else{
							//	$data['error'] .= $this->upload->display_errors();
							//}
							
						}	
						else
						{
							
							$upload_data = array('upload_data' => $this->upload->data());
							
							//delete any previous images
							foreach (glob($destination_path.'aircraft.*') as $filename) {
							   unlink($filename);
							}
						
							
							if($upload_data['upload_data']['file_ext'] == '.jpeg'){
								$ext = '.jpg';
							}
							elseif($upload_data['upload_data']['file_ext'] == 'jpeg'){
								$ext = 'jpg';				
							}
							else{
								$ext = strtolower($upload_data['upload_data']['file_ext']);
							}
							
							if(!rename($upload_data['upload_data']['full_path'],$destination_path.'aircraft'.$ext)){
								
								$data['error'] .= 'Could not move file to final location. The sub folder ('.$destination_path.') may not be writable<br /><br />';
								
							}
							
							if(is_file($upload_data['upload_data']['full_path'])){
								unlink($upload_data['upload_data']['full_path']);
							}
							
							$upload_data['upload_data']['file_name'] = 'aircraft';
						
							
						}
					}
				
				//if there were no errors
				if($data['error'] == ''){
					redirect('acp_fleet/manage/');
				}
				else{
					//output error message
					$data['page_title'] = 'Error';
					$this->view_fns->view('global/error/error', $data);
				}
				
				
			}
			// haven't had data submitted or failed validation
			else{
				
				//initialise all values
				$name = '';
				$clss = '';
				$pax = '';
				$cargo = '';
				$division = '';
				$in_fleet = '';
				$charter = '';
				$enabled = '';
				$icao_code = '';
				$variant = '';
				$aircraft_type = '';
				$description = '';
				
				$length = '';
				$wingspan = '';
				$height = '';
				$engine = '';
				$engine_manufacturer = '';
				$cruise_speed = '';
				$service_ceiling = '';
				$gross_weight = '';
				$crew = '';
				$price = '';
				$manufacturer = '';
				$oew = '';
				$mtow = '';
				$fuel_capacity = '';
				$fuel_weight = '';
				$long_range_altitude = '';
				$long_range_speed = '';
				$max_speed = '';
				$range_mload = '';
				$range_mfuel = '';
				$engine_thrust = '';
				$to_rwy_length_min = '';
				$to_rwy_length_max = '';
				$land_rwy_length = '';
				$v_rotate = '';
				$v_approach = '';
				$flaps_rotate = '';
				$flaps_approach = '';
				$maximum_climb_rate = '';
				$maximum_desc_rate = '';
			
			
				//if we are editing
				if($aircraft_id > 0){
			
					//prepare dropdowns etc for output from database
					$name = $result['0']['name'];
					$clss = $result['0']['clss'];
					$pax = $result['0']['pax'];
					$cargo = $result['0']['cargo'];
					$division = $result['0']['division'];
					$in_fleet = $result['0']['in_fleet'];
					$charter = $result['0']['charter'];
					$enabled = $result['0']['enabled'];
					$icao_code = $result['0']['icao_code'];
					$variant = $result['0']['variant'];
					$aircraft_type = $result['0']['aircraft_type'];
					$description = htmlspecialchars_decode($result['0']['description']);
					
					if($result['0']['length'] > 0){ $length = ($result['0']['length']/100); }
					else{ $length = ''; }
					if($result['0']['wingspan'] > 0){ $wingspan = ($result['0']['wingspan']/100); }
					else{ $wingspan = ''; }
					if($result['0']['height'] > 0){ $height = ($result['0']['height']/100); }
					else{ $height = ''; }
					$engine = htmlspecialchars_decode($result['0']['engine']);
					$engine_manufacturer = htmlspecialchars_decode($result['0']['engine_manufacturer']);
					$cruise_speed = $result['0']['cruise_speed'];
					$service_ceiling = $result['0']['service_ceiling'];
					if($result['0']['gross_weight'] > 0){ $gross_weight = ($result['0']['gross_weight']/100); }
					else{ $gross_weight = ''; }
					$crew = $result['0']['crew'];
					if($result['0']['price'] > 0){ $price = ($result['0']['price']/1000000); }
					else{ $price = ''; }
					$manufacturer = $result['0']['manufacturer'];
					$oew = $result['0']['oew'];
					$mtow = $result['0']['mtow'];
					$fuel_capacity = $result['0']['fuel_capacity'];
					$fuel_weight = $result['0']['fuel_weight'];
					$long_range_altitude = $result['0']['long_range_altitude'];
					$long_range_speed = $result['0']['long_range_speed'];
					$max_speed = $result['0']['max_speed'];
					$range_mload = $result['0']['range_mload'];
					$range_mfuel = $result['0']['range_mfuel'];
					$engine_thrust = $result['0']['engine_thrust'];
					$to_rwy_length_min = $result['0']['to_rwy_length_min'];
					$to_rwy_length_max = $result['0']['to_rwy_length_max'];
					$land_rwy_length = $result['0']['land_rwy_length'];
					$v_rotate = $result['0']['v_rotate'];
					$v_approach = $result['0']['v_approach'];
					$flaps_rotate = $result['0']['flaps_rotate'];
					$flaps_approach = $result['0']['flaps_approach'];
					
					$maximum_climb_rate = $result['0']['maximum_climb_rate'];
					$maximum_desc_rate = $result['0']['maximum_desc_rate'];
					
					
				}
								
				
				//dropdowns
				$data['clss'] = $clss;
				$data['division'] = $division;
				$data['in_fleet'] = $in_fleet;
				$data['charter'] = $charter;
				$data['enabled'] = $enabled;
				$data['aircraft_type'] = $aircraft_type;
				
				//text area
				$data['description'] = array( 'name' => 'description','id' => 'description','value' => $description, 'rows' => '10','cols' => '45');
								
				//define form elements
				$data['name'] = array( 'name' => 'name','id' => 'name','value' => $name, 'maxlength' => '30','size' => '30');
				$data['pax'] = array( 'name' => 'pax','id' => 'pax','value' => $pax, 'maxlength' => '3','size' => '3');
				$data['cargo'] = array( 'name' => 'cargo','id' => 'cargo','value' => $cargo, 'maxlength' => '10','size' => '10');
				$data['icao_code'] = array( 'name' => 'icao_code','id' => 'icao_code','value' => $icao_code, 'maxlength' => '4','size' => '4');
				$data['variant'] = array( 'name' => 'variant','id' => 'variant','value' => $variant, 'maxlength' => '6','size' => '6');
				$data['length'] = array( 'name' => 'length','id' => 'length','value' => $length, 'maxlength' => '6','size' => '6');
				$data['wingspan'] = array( 'name' => 'wingspan','id' => 'wingspan','value' => $wingspan, 'maxlength' => '6','size' => '6');
				$data['height'] = array( 'name' => 'height','id' => 'height','value' => $height, 'maxlength' => '6','size' => '6');
				$data['engine'] = array( 'name' => 'engine','id' => 'engine','value' => $engine, 'maxlength' => '25','size' => '25');
				$data['engine_manufacturer'] = array( 'name' => 'engine_manufacturer','id' => 'engine_manufacturer','value' => $engine_manufacturer, 'maxlength' => '25','size' => '25');
				$data['cruise_speed'] = array( 'name' => 'cruise_speed','id' => 'cruise_speed','value' => $cruise_speed, 'maxlength' => '6','size' => '6');
				$data['service_ceiling'] = array( 'name' => 'service_ceiling','id' => 'service_ceiling','value' => $service_ceiling, 'maxlength' => '8','size' => '8');
				$data['gross_weight'] = array( 'name' => 'gross_weight','id' => 'gross_weight','value' => $gross_weight, 'maxlength' => '6','size' => '6');
				$data['crew'] = array( 'name' => 'crew','id' => 'crew','value' => $crew, 'maxlength' => '25','size' => '25');
				$data['price'] = array( 'name' => 'price','id' => 'price','value' => $price, 'maxlength' => '20','size' => '20');
				$data['manufacturer'] = array( 'name' => 'manufacturer','id' => 'manufacturer','value' => $manufacturer, 'maxlength' => '25','size' => '25');
				$data['oew'] = array( 'name' => 'oew','id' => 'oew','value' => $oew, 'maxlength' => '10','size' => '10');
				$data['mtow'] = array( 'name' => 'mtow','id' => 'mtow','value' => $mtow, 'maxlength' => '10','size' => '10');
				$data['fuel_capacity'] = array( 'name' => 'fuel_capacity','id' => 'fuel_capacity','value' => $fuel_capacity, 'maxlength' => '6','size' => '6');
				$data['fuel_weight'] = array( 'name' => 'fuel_weight','id' => 'fuel_weight','value' => $fuel_weight, 'maxlength' => '8','size' => '8');
				$data['long_range_altitude'] = array( 'name' => 'long_range_altitude','id' => 'long_range_altitude','value' => $long_range_altitude, 'maxlength' => '20','size' => '20');
				$data['long_range_speed'] = array( 'name' => 'long_range_speed','id' => 'long_range_speed','value' => $long_range_speed, 'maxlength' => '6','size' => '6');
				$data['max_speed'] = array( 'name' => 'max_speed','id' => 'max_speed','value' => $max_speed, 'maxlength' => '6','size' => '6');
				$data['range_mload'] = array( 'name' => 'range_mload','id' => 'range_mload','value' => $range_mload, 'maxlength' => '6','size' => '6');
				$data['range_mfuel'] = array( 'name' => 'range_mfuel','id' => 'range_mfuel','value' => $range_mfuel, 'maxlength' => '6','size' => '6');
				$data['engine_thrust'] = array( 'name' => 'engine_thrust','id' => 'engine_thrust','value' => $engine_thrust, 'maxlength' => '20','size' => '20');
				$data['to_rwy_length_min'] = array( 'name' => 'to_rwy_length_min','id' => 'to_rwy_length_min','value' => $to_rwy_length_min, 'maxlength' => '5','size' => '5');
				$data['to_rwy_length_max'] = array( 'name' => 'to_rwy_length_max','id' => 'to_rwy_length_max','value' => $to_rwy_length_max, 'maxlength' => '5','size' => '5');
				$data['land_rwy_length'] = array( 'name' => 'land_rwy_length','id' => 'land_rwy_length','value' => $land_rwy_length, 'maxlength' => '5','size' => '5');
				$data['v_rotate'] = array( 'name' => 'v_rotate','id' => 'v_rotate','value' => $v_rotate, 'maxlength' => '4','size' => '4');
				$data['v_approach'] = array( 'name' => 'v_approach','id' => 'v_approach','value' => $v_approach, 'maxlength' => '4','size' => '4');
				$data['flaps_rotate'] = array( 'name' => 'flaps_rotate','id' => 'flaps_rotate','value' => $flaps_rotate, 'maxlength' => '10','size' => '10');
				$data['flaps_approach'] = array( 'name' => 'flaps_approach','id' => 'flaps_approach','value' => $flaps_approach, 'maxlength' => '10','size' => '10');
				$data['maximum_climb_rate'] = array( 'name' => 'maximum_climb_rate','id' => 'maximum_climb_rate','value' => $maximum_climb_rate, 'maxlength' => '6','size' => '10');
				$data['maximum_desc_rate'] = array( 'name' => 'maximum_desc_rate','id' => 'maximum_desc_rate','value' => $maximum_desc_rate, 'maxlength' => '6','size' => '10');
				
				//define all the arrays			
				$data['clss_array'] = array();
				$data['division_array'] = array();
				$data['bool_array'] = array('' => '', '0' => 'No', '1' => 'Yes');
				$data['rank_array'] = array('' => '');
				$data['type_array'] = array('' => '', 'P' => 'Prop', 'J' => 'Jet', 'H' => 'Helicopter');
				
				$ranks_data = $this->Fleet_model->get_ranks();
				
				$data['rank_array'] = $ranks_data['ranks'];
				$data['clss_array'] = $ranks_data['clss'];
				
				$data['division_array'] = $this->Fleet_model->get_divisions();
				
				
				//output page
				$data['page_title'] = 'ACP - Fleet Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_fleetedit', $data);
			}

			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_fleet/edit/'.$aircraft_id.'/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}

	
	
	
	
	function downloads($aircraft_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');
		$this->load->model('Pirep_model');
		
		$data['aircraft_id'] = $aircraft_id;
		$data['error'] = '';
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($aircraft_id == NULL){
			redirect('acp_fleet/manage');
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
		
			
			//need to grab all the downloads for the list
			$query = $this->db->query("	SELECT 	aircraft_downloads.id as id,
											aircraft_downloads.aircraft_id,
											aircraft_downloads.location,
											aircraft_downloads.payware,
											aircraft_downloads.model,
											aircraft_downloads.description,
											aircraft_downloads.aircraft_id,
											flight_sim_series.name as series_name,
											flight_sim_versions.version_name,
											aircraft.name,
											aircraft_downloads_type.name as type
																					
										FROM aircraft_downloads
										
											LEFT JOIN aircraft_downloads_type
											ON aircraft_downloads_type.id = aircraft_downloads.type
										
											LEFT JOIN aircraft
											ON aircraft.id = aircraft_downloads.aircraft_id
										
											LEFT JOIN flight_sim_versions
											ON flight_sim_versions.id = aircraft_downloads.flight_sim_id
											
											LEFT JOIN flight_sim_series
											ON flight_sim_series.id = flight_sim_versions.series_id
										
										WHERE aircraft_downloads.aircraft_id = '$aircraft_id'
										
										ORDER BY flight_sim_series.name, flight_sim_versions.version_name, aircraft_downloads_type.name, aircraft_downloads.payware
												
											");
					
			$data['downloads_data'] =  $query->result();
			$data['num_downloads'] =  $query->num_rows();
			
			
			
			//output page
			$data['page_title'] = 'ACP - Fleet Downloads';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_fleetdownloads', $data);
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
			
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_fleet/downloads/'.$aircraft_id.'/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}	
	
	
	
	
	
	
	
	
	function downloads_edit($aircraft_id = NULL, $download_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');
		$this->load->model('Pirep_model');
		
		$data['aircraft_id'] = $aircraft_id;
		$data['download_id'] = $download_id;
		$data['error'] = '';
		$data['type_name'] = 'Livery';
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($download_id == NULL || !is_numeric($download_id)){
			redirect('acp_fleet/manage_edit'.$aircraft_id);
		}
		elseif($aircraft_id == NULL || !is_numeric($aircraft_id)){
			redirect('acp_fleet/manage');
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
			
			$type = $this->security->sanitize_filename($this->input->post('type'));
			$flight_sim_id = $this->security->sanitize_filename($this->input->post('flight_sim_id'));
			$model = $this->security->sanitize_filename($this->input->post('model'));
			$location = $this->input->post('location');
			$payware = $this->security->sanitize_filename($this->input->post('payware'));
			$file = $this->security->sanitize_filename($this->input->post('file'));
			$description = $this->input->post('description');
			
			$current_pilot_username = $this->session->userdata('username');
			$current_pilot_user_id = $this->session->userdata('user_id');
			
			//dropdown array
			$data['type_array'] = $this->Fleet_model->get_download_types();
			
			$data['allowed_types'] = 'gif|jpg|jpeg|png';
			$data['max_size'] = '75';
			$data['max_width'] = '500';
			$data['max_height'] = '180';
			
			if($valid == 'true'){
				
				$type_name = strtolower($data['type_array'][$type]);
				
				//create the record
				//array the data for insert
				if($model == ''){ $model = NULL; }	
				
				
				$sql_location = addslashes($location);
				$sql_description = addslashes($description);
				
				//check to make sure there's no duplicate
				$query = $this->db->query("	SELECT 	aircraft_downloads.id as id
											
										FROM aircraft_downloads
										
										WHERE aircraft_downloads.aircraft_id = '$aircraft_id'
										AND aircraft_downloads.type = '$type'
										AND aircraft_downloads.flight_sim_id = '$flight_sim_id'
										AND aircraft_downloads.location = '$sql_location'
										AND aircraft_downloads.payware = '$payware'
										AND aircraft_downloads.description = '$sql_description'
										AND aircraft_downloads.submitted_by = '$current_pilot_user_id'
										
										LIMIT 1
										
										");
									
				$insert_data =  $query->result_array();
				$num_insert =  $query->num_rows();
				
				//array data
				$aircraft_downloads_data = array(
												'aircraft_id' => $aircraft_id,
												'type' => $type,
												'flight_sim_id' => $flight_sim_id,
												'model' => $model,
												'location' => $location,
												'payware' => $payware,
												'description' => $description,
												);
				
				
				//only create a new record if there is no existing and we're creating new
				if($num_insert < 1 && $download_id == 0){
				
					
					//add extra fields
					$aircraft_downloads_data['submitted'] = $gmt_mysql_datetime;
					$aircraft_downloads_data['submitted_by'] = $current_pilot_user_id;
					
					//insert the data					
					$this->db->insert('aircraft_downloads', $this->db->escape($aircraft_downloads_data));
					
					$stripped_description = $this->db->escape($description);
					
					//pull the record to get the id
					$query = $this->db->query("	SELECT 	aircraft_downloads.id as id
													
												FROM aircraft_downloads
												
												WHERE aircraft_downloads.aircraft_id = '$aircraft_id'
												AND aircraft_downloads.type = '$type'
												AND aircraft_downloads.flight_sim_id = '$flight_sim_id'
												AND aircraft_downloads.location = '$location'
												AND aircraft_downloads.payware = '$payware'
												AND aircraft_downloads.description = $stripped_description
												AND aircraft_downloads.submitted = '$gmt_mysql_datetime'
												AND aircraft_downloads.submitted_by = '$current_pilot_user_id'
												
												LIMIT 1
												
												");
											
					$insert_data =  $query->result_array();
					$num_insert =  $query->num_rows();
					
				}
				elseif($download_id > 0){
				
					//update the data	
					$this->db->where('id', $download_id);				
					$this->db->update('aircraft_downloads', $this->db->escape($aircraft_downloads_data));
					
					$num_insert = 1;
				}
				
				if($num_insert == 1){
					
					if($download_id == 0){
						$download_id = $insert_data['0']['id'];
					}
					
					// do upload
					$destination_path = $this->config->item('base_path').'assets/uploads/aircraft/'.$aircraft_id.'/';
					$config['upload_path'] = $this->config->item('base_path').'assets/uploads/tmp/';
					$config['allowed_types'] = $data['allowed_types'];
					$config['max_size']	= $data['max_size'];
					$config['max_width']  = $data['max_width'];
					$config['max_height']  = $data['max_height'];
					
					
						
					//sort out upload folder
					if(!is_dir($destination_path)){
						//create path
						if(!mkdir($destination_path, 0755, true)){
							echo $destination_path;
						}
					}
					
					$this->load->library('upload', $config);
				
					if ( ! $this->upload->do_upload())
					{
						
						$upload_data = array('upload_data' => $this->upload->data());
						
						//if we did upload, but error'd
						if($upload_data['upload_data']['file_size'] > 0){
								$data['error'] .= $this->upload->display_errors()
							.' File must be smaller than '.$config['max_size'].'k and no bigger than '.$config['max_width']
							.'x'.$config['max_height'].'. Allowed file types are '.$config['allowed_types'];
						}
						
					}	
					else
					{
						
						$upload_data = array('upload_data' => $this->upload->data());
						
						//delete any previous images
						foreach (glob($destination_path.$type_name.'-'.$download_id.'.*') as $filename) {
						   unlink($filename);
						}
					
						
						if($upload_data['upload_data']['file_ext'] == '.jpeg'){
							$ext = '.jpg';
						}
						elseif($upload_data['upload_data']['file_ext'] == 'jpeg'){
							$ext = 'jpg';				
						}
						else{
							$ext = strtolower($upload_data['upload_data']['file_ext']);
						}
						
						if(!rename($upload_data['upload_data']['full_path'],$destination_path.$type_name.'-'.$download_id.$ext)){
							
							$data['error'] .= 'Could not move file to final location. The sub folder ('.$destination_path.') may not be writable<br /><br />';
							
						}
						
						if(is_file($upload_data['upload_data']['full_path'])){
							unlink($upload_data['upload_data']['full_path']);
						}
						
						$upload_data['upload_data']['file_name'] = $type_name.'-'.$download_id;
					
						
					}
					
				
				}
				else{
					
					$data['error'] .= 'Error writing record into database<br /><br />';
					
				}
				
				//on completion return to list
				if($data['error'] == ''){
					redirect('acp_fleet/downloads/'.$aircraft_id);	
				}
			}
			
			
			
			//assemble form data
			$model = '';
			$location = '';
			$description = '';
			
			//dropdowns
			$data['type'] = '1';
			$data['payware'] = '0';
			$data['flight_sim_id'] = '3';
			
		
			//make a database call to see if record exists and populate values if it does
			if($download_id > 0){
				$query = $this->db->query("	SELECT 	aircraft_downloads.id,
													aircraft_downloads.model,
													aircraft_downloads.location,
													aircraft_downloads.description,
													aircraft_downloads.type,
													aircraft_downloads.payware,
													aircraft_downloads.flight_sim_id
											
										FROM aircraft_downloads
										
										WHERE aircraft_downloads.id = '$download_id'
										
										LIMIT 1
										
										");
									
				$record_data =  $query->result_array();
				$num_records =  $query->num_rows();
				
				if($num_records > 0){
					$model = $record_data['0']['model'];
					$location = $record_data['0']['location'];
					$description = $record_data['0']['description'];
					
					$data['type'] = $record_data['0']['type'];
					$data['payware'] = $record_data['0']['payware'];
					$data['flight_sim_id'] = $record_data['0']['flight_sim_id'];
					$data['type_name'] = $data['type_array'][$record_data['0']['type']];
					
				}
				
			}
			
			
			
			//form input
			$data['model'] = array( 'name' => 'model','id' => 'model','value' => $model, 'maxlength' => '200','size' => '45');
			$data['location'] = array( 'name' => 'location','id' => 'location','value' => $location, 'maxlength' => '400','size' => '45');
			
			//text area
			$data['description'] = array( 'name' => 'description','id' => 'description','value' => $description, 'rows' => '10','cols' => '45');
				
			
			
			//download type dropdown array
			$data['payware_array'] = array('0' => 'No', '1' => 'Yes');
			$data['sim_array'] = $this->Pirep_model->get_flightsims();
			
			
			
			//output confirmation page
			$data['page_title'] = 'Fleet downloads';
			$data['no_links'] = '1';
			
			//if there were no errors
			if($data['error'] == ''){
				//output page
				$data['page_title'] = 'ACP - Fleet Downloads Edit';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_fleetdownloadsedit', $data);
			}
			else{
				$this->view_fns->view('global/error/error', $data);
			}
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_fleet/downloads_edit/'.$aircraft_id.'/'.$download_id;										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}	
	
		
	
	
	
	
	

	function manage($system_restrict = NULL, $division = NULL, $offset = 0)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		
		
		if($system_restrict == NULL && $division == NULL){
			redirect('acp_fleet/manage/ALL/ALL');
		}
		elseif($system_restrict == NULL){
			redirect('acp_fleet/manage/ALL/'.$division);
		}
		elseif($division == NULL){
			redirect('acp_fleet/manage/'.$system_restrict.'/ALL');
		}
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		$data['system_restrict'] = $system_restrict;
		$data['division'] = $division;
		
		//grab post
		$post_system_restrict = $this->security->sanitize_filename($this->input->post('system_restrict'));
		$post_division = $this->security->sanitize_filename($this->input->post('division'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));
		
		if(
		($system_restrict != $post_system_restrict && $post_system_restrict != '')
		OR ($division != $post_division && $post_division != '')
		){
			redirect('acp_fleet/manage/'.$post_system_restrict.'/'.$post_division);
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
			
			$sqlsearch = '';
			//handle search
			if($valid == 'true' && $search != ''){
			
				//split up the search into constituent terms
				$search_array = explode(" ",$search);
				$num_search = count($search_array);
	
				//for multiple term searches
				if ($num_search > 1){
					$sqlsearch = "WHERE (aircraft.name LIKE '%".$search."%'";
					foreach ($search_array as &$row){
						$sqlsearch .= " OR aircraft.name LIKE '%".$row."%'";
					}
					$sqlsearch .= ')';
				}
					
					
				//for single term searches
				else{
				$sqlsearch = "WHERE (aircraft.name LIKE '%$search%')";
				}
			
			}
			else{
			
				//not searching, handle restriction
				$sqlsearch = '';
				
				switch($system_restrict){
				
					case 'C':
						$sqlsearch = "WHERE aircraft.in_fleet = '1'";
					break;
					
					case 'H':
						$sqlsearch = "WHERE aircraft.in_fleet = '0'";
					break;
					
					default:
						$sqlsearch = "WHERE (aircraft.in_fleet = '0' OR aircraft.in_fleet = '1')";
					break;
				
				}
				
				if(is_numeric($division)){

					$sqlsearch .= "AND aircraft.division = '$division'";
				
				}
				
				
				
			
			
			}
			
			
			//grab all aircraft from the database
			$query = $this->db->query("	SELECT 	
											aircraft.id,
											aircraft.name,
											aircraft.clss,
											aircraft.pax,
											aircraft.cargo,
											divisions.division_shortname as division,
											aircraft.in_fleet,
											aircraft.charter,
											aircraft.enabled,
											aircraft.rank,
											aircraft.variant
													
											FROM aircraft
											
												LEFT JOIN divisions
												ON divisions.id = aircraft.division
												
											$sqlsearch
											
											ORDER BY aircraft.variant, aircraft.name
										");
				
			$data['result'] =  $query->result();	
			$data['num_rows'] =  $query->num_rows();	
			
			
			//grab divisions for dropdown restrict
			
			
			$system_array = array('ALL' => 'All', 'C' => 'Current', 'H' => 'Historic');
			$enabled_array = array('ALL' => 'All', '0' => 'Disabled', '1' => 'Enabled');
			
			
			
			//divisions array
			$query = $this->db->query("	SELECT 	
											divisions.id,
											divisions.division_shortname as name
													
											FROM divisions
											
											ORDER BY divisions.id
										");
				
			$result =  $query->result();	

			$data['division_array'] = array('ALL' => 'All');
			
			foreach($result as $row){				
				$data['division_array'][$row->id] = $row->name;
			}
			
			$data['system_array'] = $system_array;
			$data['enabled_array'] = $enabled_array;
			
			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search','maxlength' => '25', 'size' => '25', 'value' => $search);

			
			//paginatipon
			if($offset == NULL || $offset == ''){
				$offset = 0;
			}
			
			$data['offset'] = $offset;
			$data['limit'] = '15';
			
			$pag_config['base_url'] = $data['base_url'].'acp_fleet/manage/'.$system_restrict.'/'.$division;
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 5;
			
			$this->pagination->initialize($pag_config); 
			
			

			//output page
			$data['page_title'] = 'ACP - Fleet Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_fleetmanage', $data);
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_fleet/manage/'.$system_restrict.'/'.$division.'/'.$offset;										
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