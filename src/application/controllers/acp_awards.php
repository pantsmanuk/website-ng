<?php
 
class Acp_awards extends CI_Controller {

	function Acp_awards()
	{
		parent::__construct();
	}
	
	
	function edit($award_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		//$this->load->model('Fleet_model');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($award_id == NULL){
			redirect('acp_awards/manage');
		}
		
		if($award_id > 0){
			$data['mode'] = 'Edit';
		}
		else{
			$data['mode'] = 'Create';
		}
		
		$data['award_id'] = $award_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';
		
		
		$data['allowed_types'] = 'png';
		$data['max_size'] = '20';
		$data['max_width'] = '30';
		$data['max_height'] = '30';
		
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
			$awardtype = $this->security->sanitize_filename($this->input->post('awardtype'));
			$description = $this->security->sanitize_filename($this->input->post('description'));
			$award_name = $this->security->sanitize_filename($this->input->post('award_name'));
			$automatic = $this->security->sanitize_filename($this->input->post('automatic'));
			$tour = $this->security->sanitize_filename($this->input->post('tour'));
			$event = $this->security->sanitize_filename($this->input->post('event'));
			$aggregate_award_name = $this->security->sanitize_filename($this->input->post('aggregate_award_name'));
			$aggregate_award_rank = $this->security->sanitize_filename($this->input->post('aggregate_award_rank'));
			
			
			//perform validation
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('awardtype', 'Award type', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required');
			$this->form_validation->set_rules('tour', 'Tour', 'required');
			$this->form_validation->set_rules('event', 'event', 'required');
			$this->form_validation->set_rules('award_name', 'award_name', 'required');
			$this->form_validation->set_rules('aggregate_award_name', 'aggregate_award_name', 'required');
			$this->form_validation->set_rules('aggregate_award_rank', 'aggregate_award_rank', 'required');
			
			if($this->form_validation->run() == FALSE){
				$validation = 0;
			}
			else{
				$validation = 1;
			}

			if($award_id > 0){
			
				if($tour == ''){ $tour = NULL; }
				if($event == ''){ $event = NULL; }
			
				//need to determine whether or not this is a valid award - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												awards_index.id,
												awards_index.awardtype,
												awards_index.description,	
												awards_index.award_name,
												awards_index.automatic,
												awards_index.tour,
												awards_index.event,
												awards_index.aggregate_award_name,
												awards_index.aggregate_award_rank
														
												FROM awards_index
													
												WHERE awards_index.id = '$award_id'
												
												LIMIT 1
											");
											
				$result = $query->result_array();
				$num_results = $query->num_rows();
				
				//if no return, set create new
				if($num_results < 1){
					redirect('acp_awards/manage/');
				}
			
			}
			
			if($valid == 'true' && $validation == 1){
				
				
				//data has been submitted, array it and update the record
				if($description == ''){ $description = NULL; }				
				
				$award_data = array(
						'awardtype' => $awardtype,
						'description' => $description,
						'award_name' => $award_name,
						'automatic' => $automatic,
						'tour' => $tour,
						'event' => $event,
						'aggregate_award_name' => $aggregate_award_name,
						'aggregate_award_rank' => $aggregate_award_rank,
				);
				
				
				//if we are editing
				if($award_id > 0){
					
					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('awards_index', $this->db->escape($award_data));
				}
				else{
				
				
				
					$award_data['submitted'] = $gmt_mysql_datetime;
					$award_data['submitted_by'] = $current_pilot_user_id;
				
					//we are creating a new record
					$this->db->insert('awards_index', $this->db->escape($award_data));
					
					
					//grab the record id for the upload
					$query = $this->db->query("	SELECT 	
													awards_index.id
													
													FROM awards_index
														
													WHERE awards_index.award_name = '$award_name'
													AND awards_index.submitted = '$gmt_mysql_datetime'
													AND awards_index.submitted_by = '$current_pilot_user_id'
													
													LIMIT 1
												");
												
					$result = $query->result_array();
					$num_results = $query->num_rows();
					
					if($num_results > 0){
						$award_id = $result['0']['id'];
					}
					
				}
				
				
				
				if($award_id > 0){
					
						// do upload
						$destination_path = $this->config->item('base_path').'assets/uploads/awards/';
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
							foreach (glob($destination_path.$award_id.'.*') as $filename) {
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
							
							if(!rename($upload_data['upload_data']['full_path'],$destination_path.$award_id.$ext)){
								
								$data['error'] .= 'Could not move file to final location. The sub folder ('.$destination_path.') may not be writable<br /><br />';
								
							}
							
							if(is_file($upload_data['upload_data']['full_path'])){
								unlink($upload_data['upload_data']['full_path']);
							}
							
							$upload_data['upload_data']['file_name'] = $award_id;
						
							
						}
					}
				
				
				
				
				//if there were no errors
				if($data['error'] == ''){
					redirect('acp_awards/manage/');
				}
				else{
					//output error message
					$data['page_title'] = 'Error';
					$this->view_fns->view('global/error/error', $data);
				}
				
				
			}
			// haven't had data submitted or failed validation
			else{
			
			
				
				
				if($valid != 'true'){
				//initialise all values
				$awardtype = '';
				$description = '';
				$award_name = '';
				$automatic = '';
				$aggregate_award_name = '';
				$aggregate_award_rank = '';
				}
			
				//if we are editing
				if($award_id > 0){
			
					//prepare dropdowns etc for output from database
					
					$awardtype = $result['0']['awardtype'];
					$description = $result['0']['description'];
					$award_name = $result['0']['award_name'];
					$automatic = $result['0']['automatic'];
					$tour = $result['0']['tour'];
					$event = $result['0']['event'];
					$aggregate_award_name = $result['0']['aggregate_award_name'];
					$aggregate_award_rank = $result['0']['aggregate_award_rank'];
				}
								
				
				//dropdowns
				//$data['primary'] = $primary;
				$data['automatic'] = $automatic;
				$data['tour'] = $tour;
				$data['event'] = $event;
				
				//text area
				$data['description'] = array( 'name' => 'description','id' => 'description','value' => $description, 'rows' => '10','cols' => '45');
								
				//define form elements
				$data['awardtype'] = array( 'name' => 'awardtype','id' => 'awardtype','value' => $awardtype, 'maxlength' => '50','size' => '30');
				$data['award_name'] = array( 'name' => 'award_name','id' => 'award_name','value' => $award_name, 'maxlength' => '30','size' => '30');
				$data['aggregate_award_name'] = array( 'name' => 'aggregate_award_name','id' => 'aggregate_award_name','value' => $aggregate_award_name, 'maxlength' => '100','size' => '30');
				$data['aggregate_award_rank'] = array( 'name' => 'aggregate_award_rank','id' => 'aggregate_award_rank','value' => $aggregate_award_rank, 'maxlength' => '3','size' => '3');
				
				
				//define all the arrays			
				$data['bool_array'] = array('' => '', '0' => 'No', '1' => 'Yes');
				$data['yesno_array'] = array('' => '', 'N' => 'No', 'Y' => 'Yes');
							
				
				//output page
				$data['page_title'] = 'ACP - Award Management';
				$data['admin_menu'] = 1;
				$this->view_fns->view('global/admincp/acp_awardsedit', $data);
			}

			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_awards/edit/'.$award_id.'/';										
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
			redirect('acp_awards/manage/ALL/');
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
			redirect('acp_awards/manage/'.$post_system_restrict.'/');
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
					$sqlsearch = "WHERE (award_name LIKE '%".$search."%'";
					foreach ($search_array as &$row){
						$sqlsearch .= " OR award_name LIKE '%".$row."%'";
					}
					$sqlsearch .= ')';
				}
					
					
				//for single term searches
				else{
				$sqlsearch = "WHERE (award_name LIKE '%$search%')";
				}
			
			}
			else{
			
				//not searching, handle restriction
				$restrict = '';
				if($system_restrict != 'ALL'){
					$sqlsearch = "WHERE awards_index.automatic = '$system_restrict'";
				}
			
			
			}
			
			
			//grab all awards from the database
			$query = $this->db->query("	SELECT 	
											awards_index.id,
											awards_index.awardtype,
											awards_index.description,
											awards_index.name,
											awards_index.award_name,
											awards_index.automatic,
											awards_index.tour,
											awards_index.event,
											awards_index.aggregate_award_name,
											awards_index.aggregate_award_rank
													
											FROM awards_index
												
											$sqlsearch
											
											ORDER BY awards_index.aggregate_award_name, awards_index.aggregate_award_rank
										");
				
			$data['result'] =  $query->result();	
			$data['num_rows'] =  $query->num_rows();	
			
			
			
			
			
			$system_array = array('ALL' => 'All', 'Y' => 'Automatic', 'N' => 'Manual');
			
			$data['system_array'] = $system_array;
			
			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search','maxlength' => '25', 'size' => '25', 'value' => $search);

			
			//paginatipon
			if($offset == NULL || $offset == ''){
				$offset = 0;
			}
			
			$data['offset'] = $offset;
			$data['limit'] = '15';
			
			$pag_config['base_url'] = $data['base_url'].'acp_awards/manage/'.$system_restrict;
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 4;
			
			$this->pagination->initialize($pag_config); 
			
			

			//output page
			$data['page_title'] = 'ACP - Award Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_awardsmanage', $data);
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_awards/manage/'.$system_restrict.'/'.$offset;										
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