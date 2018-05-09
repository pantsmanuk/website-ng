<?php

class Acp_news extends CI_Controller {

	function Acp_news()
	{
		parent::__construct();	
	}
	



	function edit($news_id = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->model('Fleet_model');
		//load helpers
		$this->load->helper('bbcode');
		
		
				
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $acp_timeout;
		
		if($news_id == NULL){
			redirect('acp_news/manage');
		}
		
		if($news_id > 0){
			$data['mode'] = 'Edit';
		}
		else{
			$data['mode'] = 'Create';
		}
		
		$data['news_id'] = $news_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';
		
		
		$data['allowed_types'] = 'jpg|jpeg|png|gif';
		$data['max_size'] = '75';
		$data['max_width'] = '85';
		$data['max_height'] = '70';
		
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
			$news_title = $this->security->sanitize_filename($this->input->post('news_title'));
			$news_text = htmlspecialchars($this->input->post('news_text'));
			$news_start_day = $this->security->sanitize_filename($this->input->post('news_start_day'));
			$news_start_month = $this->security->sanitize_filename($this->input->post('news_start_month'));
			$news_start_year = $this->security->sanitize_filename($this->input->post('news_start_year'));
			$news_end_day = $this->security->sanitize_filename($this->input->post('news_end_day'));
			$news_end_month = $this->security->sanitize_filename($this->input->post('news_end_month'));
			$news_end_year = $this->security->sanitize_filename($this->input->post('news_end_year'));
			
			//perform validation
			
			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('news_title', 'news_title', 'required');
			$this->form_validation->set_rules('news_text', 'news_text', 'required');
			$this->form_validation->set_rules('news_start_day', 'news_start_day', 'required');
			$this->form_validation->set_rules('news_start_month', 'news_start_month', 'required');
			$this->form_validation->set_rules('news_start_year', 'news_start_year', 'required');
			
			if($this->form_validation->run() == FALSE){
				$validation = 0;
			}
			else{
				$validation = 1;
			}

			if($news_id > 0){
			
				//need to determine whether or not this is a valid news item - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												news.id,
												news.news_title,
												news.news_text,	
												news.news_start_date_time,
												news.news_end_date_time
														
												FROM news
													
												WHERE news.id = '$news_id'
												
												LIMIT 1
											");
											
				$result = $query->result_array();
				$num_results = $query->num_rows();
				
				//if no return, set create new
				if($num_results < 1){
					redirect('acp_news/manage/');
				}
			
			}
			
			if($valid == 'true' && $validation == 1){
				
				
				//data has been submitted, array it and update the record
				
				$start_date = $news_start_year.'-'.$news_start_month.'-'.$news_start_day.' 00:00:00';
				$end_date = $news_end_year.'-'.$news_end_month.'-'.$news_end_day.' 00:00:00';
				
				$news_data = array(
						'news_title' => $news_title,
						'news_text' => $news_text,
						'news_start_date_time' => $start_date,
						'news_end_date_time' => $end_date,
				);
				
				//if we are editing
				if($news_id > 0){
					
					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('news', $this->db->escape($news_data));
				}
				else{
				
				
					$division_data['submitted'] = $gmt_mysql_datetime;
					$division_data['submitted_by'] = $current_pilot_user_id;
				
					//we are creating a new record
					$this->db->insert('news', $this->db->escape($news_data));
					
					
					//grab the record id for the upload
					$query = $this->db->query("	SELECT 	
													news.id
														
													FROM news
														
													WHERE news.news_title = '$news_title'
													AND news.submitted = '$gmt_mysql_datetime'
													AND news.submitted_by = '$current_pilot_user_id'
												
													LIMIT 1
												");
												
					$result = $query->result_array();
					$num_results = $query->num_rows();
					
					if($num_results > 0){
						$news_id = $result['0']['id'];
					}
					
				}
				
				
				
				if($news_id > 0){
					
						// do upload
						$destination_path = $this->config->item('base_path').'assets/uploads/news/';
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
							foreach (glob($destination_path.$news_id.'.*') as $filename) {
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
							
							if(!rename($upload_data['upload_data']['full_path'],$destination_path.$news_id.$ext)){
								
								$data['error'] .= 'Could not move file to final location. The sub folder ('.$destination_path.') may not be writable<br /><br />';
								
							}
							
							if(is_file($upload_data['upload_data']['full_path'])){
								unlink($upload_data['upload_data']['full_path']);
							}
							
							$upload_data['upload_data']['file_name'] = $news_id;
						
							
						}
					}
				
				
				
				
				//if there were no errors
				if($data['error'] == ''){
					redirect('acp_news/manage/');
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
				$news_title = '';
				$news_text = '';
				$news_start_day = gmdate('d', time('now'));
				$news_start_month = gmdate('m', time('now'));
				$news_start_year = gmdate('Y', time('now'));
				$news_end_day = '';
				$news_end_month = '';
				$news_end_year = '';
				
			
				//if we are editing
				if($news_id > 0){
			
					//prepare dropdowns etc for output from database
					
					$news_title = $result['0']['news_title'];
					$news_text = $result['0']['news_text'];
					$news_start_day = substr($result['0']['news_start_date_time'],8,2);
					$news_start_month = substr($result['0']['news_start_date_time'],5,2);
					$news_start_year = substr($result['0']['news_start_date_time'],0,4);
					$news_end_day = substr($result['0']['news_end_date_time'],8,2);
					$news_end_month = substr($result['0']['news_end_date_time'],5,2);
					$news_end_year = substr($result['0']['news_end_date_time'],0,4);
				}
								
				
				//dropdowns
				$data['news_start_day'] = $news_start_day;
				$data['news_start_month'] = $news_start_month;
				$data['news_start_year'] = $news_start_year;
				$data['news_end_day'] = $news_end_day;
				$data['news_end_month'] = $news_end_month;
				$data['news_end_year'] = $news_end_year;
				
				//text area
				$data['news_text'] = array( 'name' => 'news_text','id' => 'news_text','value' => $news_text, 'rows' => '10','cols' => '45');
								
				//define form elements
				$data['news_title'] = array( 'name' => 'news_title','id' => 'news_title','value' => $news_title, 'maxlength' => '255','size' => '50');
				
				
				//define all the arrays			
					//day_array
					$i = 1;
					$data['day_array']['00'] = '';
					while($i <= 31){
						$data['day_array'][$i] = $i;
						$i++;
					}
					
					//month_array
					$i = 1;
					$data['month_array']['00'] = '';
					while($i <= 12){
						$data['month_array'][$i] = $i;
						$i++;
					}
					
					
					//year_array
					$current_year = date('Y', time());
					$data['year_array']['0000'] = '';
					$i = 2004;
					while($i <= ($current_year + 3)){
						$data['year_array'][$i] = $i;
						$i++;
					}
							
				
				//output page
				$data['page_title'] = 'ACP - News Management';
				$data['admin_menu'] = 1;
				
				$bb_java = js_insert_bbcode('news_text');

				$data['page_js'] = 	' '.$bb_java.' ';
				$this->view_fns->view('global/admincp/acp_newsedit', $data);
			}

			
		}
		//invalid admin login
		elseif($is_admin == '1'){
			
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_news/edit/'.$news_id.'/';										
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
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');
		
		if($system_restrict == NULL){
			redirect('acp_news/manage/0/');
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
			redirect('acp_news/manage/'.$post_system_restrict.'/');
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
					$sqlsearch = "WHERE (news_title LIKE '%".$search."%'";
					$sqlsearch .= " OR news_text LIKE '%".$search."%'";
					foreach ($search_array as &$row){
						$sqlsearch .= " OR news_title LIKE '%".$row."%'";
						$sqlsearch .= " OR news_text LIKE '%".$row."%'";
					}
					$sqlsearch .= ')';
				}
					
					
				//for single term searches
				else{
				$sqlsearch = "WHERE news_text LIKE '%$search%' OR news_text LIKE '%$search%'";
				}
			
			}
			else{
			
				//not searching, handle restriction
				$restrict = '';
				switch($system_restrict){
					case '0':
						$sqlsearch = "WHERE branch_type = '0'";
					break;
					
					case '1':
						$sqlsearch = "WHERE branch_type = '1'";
					break;
					
					default:
						
					break;
				}
			
			
			}
			
			
			//grab all news from the database
			$query = $this->db->query("	SELECT 	id,
											news_title,
											news_text,
											news_image_name,
											news_start_date_time,
											news_end_date_time,
											branch_type,
											context
											
										FROM news
										
										$sqlsearch
										
										ORDER by news_start_date_time desc
										");
				
			$data['result'] =  $query->result();	
			$data['num_rows'] =  $query->num_rows();	
			
			
			
			
			
			$system_array = array('ALL' => 'All', '0' => 'Airline News', '1' => 'Propilot News');
			
			$data['system_array'] = $system_array;
			
			//search input
			$data['search'] = array('name' => 'search', 'id' => 'search','maxlength' => '25', 'size' => '25', 'value' => $search);

			
			//paginatipon
			if($offset == NULL || $offset == ''){
				$offset = 0;
			}
			
			$data['offset'] = $offset;
			$data['limit'] = '15';
			
			$pag_config['base_url'] = $data['base_url'].'acp_news/manage/'.$system_restrict;
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 4;
			
			$this->pagination->initialize($pag_config); 
			
			

			//output page
			$data['page_title'] = 'ACP - News Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_newsmanage', $data);
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
		
			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_news/manage/'.$system_restrict.'/'.$offset;										
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