<?php
 
class Acp_divisions extends CI_Controller {

	function Acp_divisions()
	{
		parent::__construct();	
	}
	
	
	
	function edit($division_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($division_id == NULL){
			redirect('acp_divisions/manage');
		}
		
		if($division_id > 0){
			$data['mode'] = 'Edit';
		}
		else{
			$data['mode'] = 'Create';
		}
		
		$data['division_id'] = $division_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';
		
		
		$data['allowed_types'] = 'jpg|jpeg';
		$data['max_size'] = '75';
		$data['max_width'] = '550';
		$data['max_height'] = '100';
		
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
			$division_shortname = $this->security->sanitize_filename($this->input->post('division_shortname'));
			$division_longname = $this->security->sanitize_filename($this->input->post('division_longname'));
			$description = htmlspecialchars($this->input->post('description'));
			$colour = $this->security->sanitize_filename($this->input->post('colour'));
			$text = htmlspecialchars($this->input->post('text'));
			$prefix = $this->security->sanitize_filename($this->input->post('prefix'));
			$primary = $this->security->sanitize_filename($this->input->post('primary'));
			$public = $this->security->sanitize_filename($this->input->post('public'));
			$blurb = htmlspecialchars($this->input->post('blurb'));
			
			
			
			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('division_shortname', 'division_shortname', 'required');
			$this->form_validation->set_rules('division_longname', 'division_longname', 'required');
			$this->form_validation->set_rules('prefix', 'prefix', 'required');
			$this->form_validation->set_rules('primary', 'primary', 'required');
			$this->form_validation->set_rules('public', 'public', 'required');
			
			if($this->form_validation->run() == FALSE){
				$validation = 0;
			}
			else{
				$validation = 1;
			}

			if($division_id > 0){
			
				//need to determine whether or not this is a valid division - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												divisions.id,
												divisions.division_shortname,
												divisions.division_longname,	
												divisions.description,
												divisions.colour,
												divisions.text,
												divisions.prefix,
												divisions.primary,
												divisions.public,
												divisions.blurb
														
												FROM divisions
													
												WHERE divisions.id = '$division_id'
												
												LIMIT 1
											");
											
				$result = $query->result_array();
				$num_results = $query->num_rows();
				
				//if no return, set create new
				if($num_results < 1){
					redirect('acp_divisions/manage/');
				}
			
			}
			
			if($valid == 'true' && $validation == 1){
				
				
				//data has been submitted, array it and update the record
				if($description == ''){ $description = NULL; }				
				
				$division_data = array(
						'division_shortname' => $division_shortname,
						'division_longname' => $division_longname,
						'description' => $description,
						'prefix' => $prefix,
						'primary' => $primary,
						'public' => $public,
						'blurb' => $blurb,
				);
				
				//only insert colour data if supplied, else leave or use defaults
				if($colour != ''){ $division_data['colour'] = $colour; }
				if($text != ''){ $division_data['text'] = $text; }
				
				//if we are editing
				if($division_id > 0){
					
					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('divisions', $this->db->escape($division_data));
				}
				else{
				
				
					$division_data['submitted'] = $gmt_mysql_datetime;
					$division_data['submitted_by'] = $current_pilot_user_id;
				
					//we are creating a new record
					$this->db->insert('divisions', $this->db->escape($division_data));
					
					
					//grab the record id for the upload
					$query = $this->db->query("	SELECT 	
													aircraft.id,
													
													FROM divisions
														
													WHERE divisions.division_longname = '$division_longname'
													AND divisions.submitted = '$gmt_mysql_datetime'
													AND divisions.submitted_by = '$current_pilot_user_id'
													
													LIMIT 1
												");
												
					$result = $query->result_array();
					$num_results = $query->num_rows();
					
					if($num_results > 0){
						$division_id = $result['0']['id'];
					}
					
				}
				
				
				
				if($division_id > 0){
					
						// do upload
						$destination_path = $this->config->item('base_path').'assets/uploads/divisions/'.$division_id.'/';
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
							foreach (glob($destination_path.'logo.*') as $filename) {
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
							
							if(!rename($upload_data['upload_data']['full_path'],$destination_path.'logo'.$ext)){
								
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
					redirect('acp_divisions/manage/');
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
				$division_shortname = '';
				$division_longname = '';
				$description = '';
				$colour = '';
				$text = '';
				$prefix = '';
				$primary = '';
				$public = '';
				$blurb = '';
				
			
				//if we are editing
				if($division_id > 0){
			
					//prepare dropdowns etc for output from database
					
					$division_shortname = $result['0']['division_shortname'];
					$division_longname = $result['0']['division_longname'];
					$text = $result['0']['text'];
					$colour = $result['0']['colour'];
					$text = $result['0']['text'];
					$prefix = $result['0']['prefix'];
					$primary = $result['0']['primary'];
					$public = $result['0']['public'];
					$blurb = $result['0']['blurb'];
					$description = $result['0']['description'];
				}
								
				
				//dropdowns
				$data['primary'] = $primary;
				$data['public'] = $public;
				
				//text area
				$data['description'] = array( 'name' => 'description','id' => 'description','value' => htmlspecialchars_decode($description), 'rows' => '10','cols' => '45');
				$data['blurb'] = array( 'name' => 'blurb','id' => 'blurb','value' => htmlspecialchars_decode($blurb), 'rows' => '10','cols' => '45');
								
				//define form elements
				$data['division_shortname'] = array( 'name' => 'division_shortname','id' => 'division_shortname','value' => $division_shortname, 'maxlength' => '100','size' => '30');
				$data['division_longname'] = array( 'name' => 'division_longname','id' => 'division_longname','value' => $division_longname, 'maxlength' => '100','size' => '30');
				$data['colour'] = array( 'name' => 'colour','id' => 'colour','value' => $colour, 'maxlength' => '6','size' => '6');
				$data['text'] = array( 'name' => 'text','id' => 'text','value' => htmlspecialchars_decode($text), 'maxlength' => '6','size' => '6');
				$data['prefix'] = array( 'name' => 'prefix','id' => 'prefix','value' => $prefix, 'maxlength' => '4','size' => '4');
				
				
				//define all the arrays			
				$data['bool_array'] = array('' => '', '0' => 'No', '1' => 'Yes');
							
				
				//output page
				$data['page_title'] = 'ACP - Division Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_divisionedit', $data);
			}

			
		}
		//invalid admin login
		elseif($is_admin == '1'){
			
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_divisions/edit/'.$division_id.'/';										
			//set data in session
			$this->session->set_userdata($sessiondata);
		
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}






	function manage($system_restrict = NULL, $offset = 0)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		
		if($system_restrict == NULL){
			redirect('acp_divisions/manage/ALL/');
		}
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		$data['system_restrict'] = $system_restrict;
		
		//grab post
		$post_system_restrict = $this->security->sanitize_filename($this->input->post('system_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));
		
		if($system_restrict != $post_system_restrict && $post_system_restrict != ''){
			redirect('acp_divisions/manage/'.$post_system_restrict.'/');
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
					$sqlsearch = "WHERE (division_longname LIKE '%".$search."%'";
					$sqlsearch .= " OR division_shortname LIKE '%".$search."%'";
					foreach ($search_array as &$row){
						$sqlsearch .= " OR division_longname LIKE '%".$row."%'";
						$sqlsearch .= " OR division_shortname LIKE '%".$row."%'";
					}
					$sqlsearch .= ')';
				}
					
					
				//for single term searches
				else{
				$sqlsearch = "WHERE division_longname LIKE '%$search%' OR division_shortname LIKE '%$search%'";
				}
			
			}
			else{
			
				//not searching, handle restriction
				$restrict = '';
				
				switch($system_restrict){
					case 'P':
						$sqlsearch = "WHERE divisions.public = '1'";
					break;
					
					case 'H':
						$sqlsearch = "WHERE divisions.public = '0'";
					break;
					
					default:
						$sqlsearch = "";
					break;
				}
			
			
			}
			
			
			//grab all awards from the database
			$query = $this->db->query("	SELECT 	
											divisions.id,
											divisions.division_longname,
											divisions.description,
											divisions.prefix,
											divisions.primary,
											divisions.public,
											divisions.blurb
													
											FROM divisions
												
											$sqlsearch
											
											ORDER BY divisions.id
										");
				
			$data['result'] =  $query->result();	
			$data['num_rows'] =  $query->num_rows();	
			
			
			
			
			
			$system_array = array('ALL' => 'All', 'P' => 'Public', 'H' => 'Hidden');
			
			$data['system_array'] = $system_array;
			
			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search','maxlength' => '25', 'size' => '25', 'value' => $search);

			
			//paginatipon
			if($offset == NULL || $offset == ''){
				$offset = 0;
			}
			
			$data['offset'] = $offset;
			$data['limit'] = '15';
			
			$pag_config['base_url'] = $data['base_url'].'acp_divisions/manage/'.$system_restrict;
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 4;
			
			$this->pagination->initialize($pag_config); 
			
			

			//output page
			$data['page_title'] = 'ACP - Division Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_divisionsmanage', $data);
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_divisions/manage/'.$system_restrict.'/'.$offset;										
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