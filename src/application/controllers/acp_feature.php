<?php

class acp_feature extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function edit($feature_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		//load helpers
		//$this->load->helper('bbcode');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($feature_id == NULL) {
			redirect('acp_feature/manage');
		}

		if ($feature_id > 0) {
			$data['mode'] = 'Edit';
		} else {
			$data['mode'] = 'Create';
		}

		$data['feature_id'] = $feature_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$feature_type = $this->security->sanitize_filename($this->input->post('feature_type'));
			$feature_uri = $this->input->post('feature_uri');
			$feature_enabled = $this->security->sanitize_filename($this->input->post('feature_enabled'));
			$feature_order = $this->security->sanitize_filename($this->input->post('feature_order'));

			//perform validation

			$this->form_validation->set_rules('valid', 'valid', 'required');
			$this->form_validation->set_rules('feature_type', 'feature_type', 'required');
			$this->form_validation->set_rules('feature_uri', 'feature_uri', 'required');
			$this->form_validation->set_rules('feature_enabled', 'feature_enabled', 'required');

			if ($this->form_validation->run() == FALSE) {
				$validation = 0;
			} else {
				$validation = 1;
			}

			if ($feature_id > 0) {

				//need to determine whether or not this is a valid news item - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												config_featured.id,
												config_featured.type,
												config_featured.uri,
												config_featured.enabled,
												config_featured.order
														
												FROM config_featured
													
												WHERE config_featured.id = '$feature_id'
												
												LIMIT 1
											");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if no return, set create new
				if ($num_results < 1) {
					redirect('acp_feature/manage/');
				}

			}

			if ($valid == 'true' && $validation == 1) {

				//data has been submitted, array it and update the record

				$feature_data = array(
					'type' => $feature_type,
					'uri' => $feature_uri,
					'enabled' => $feature_enabled,
					'order' => $feature_order,
				);

				if ($feature_type == 'image') {
					$redirect_type = '0';
				} else {
					$redirect_type = '1';
				}

				//if we are editing
				if ($feature_id > 0) {

					$id_val = $result['0']['id'];
					//perform the update from db
					$this->db->where('id', $id_val);
					$this->db->update('config_featured', $this->db->escape($feature_data));

					//return to manage page
					redirect('acp_feature/manage/' . $redirect_type);
				} else {

					//$feature_data['submitted'] = $data['gmt_mysql_datetime'];
					//$feature_data['submitted_by'] = $current_pilot_user_id;

					//we are creating a new record
					$this->db->insert('config_featured', $this->db->escape($feature_data));
					//return to manage page
					redirect('acp_feature/manage/' . $redirect_type);

				}

			} // haven't had data submitted or failed validation
			else {

				//initialise all values
				/*
				if($feature_type == ''){
					$feature_type = $result['0']['feature_type'];
				}
				if($feature_uri == ''){
					$feature_uri = $result['0']['feature_uri'];
				}
				if($feature_type == ''){
					$feature_enabled = $result['0']['feature_enabled'];
				}
				if($feature_type == ''){
					$feature_order = $result['0']['feature_order'];
				}
				*/
				$feature_type = '';
				$feature_uri = '';
				$feature_enabled = '';
				$feature_order = '';

				//if we are editing
				if ($feature_id > 0) {

					//prepare dropdowns etc for output from database
					$feature_type = $result['0']['type'];
					$feature_uri = $result['0']['uri'];
					$feature_enabled = $result['0']['enabled'];
					$feature_order = $result['0']['order'];

				}

				//dropdowns
				$data['feature_type'] = $feature_type;
				$data['feature_enabled'] = $feature_enabled;

				//text area
				//$data['news_text'] = array( 'name' => 'news_text','id' => 'news_text','value' => $news_text, 'rows' => '10','cols' => '45');

				//define form elements
				$data['feature_uri'] = array('name' => 'feature_uri', 'id' => 'feature_uri', 'value' => $feature_uri, 'maxlength' => '500', 'size' => '50');
				$data['feature_order'] = array('name' => 'feature_order', 'id' => 'feature_order', 'value' => $feature_order, 'maxlength' => '5', 'size' => '5');

				//define all the arrays		
				//$data['year_array'] = array('');
				$data['type_array'] = array('image' => 'Image', 'video' => 'Video');
				$data['enabled_array'] = array('1' => 'Enabled', '0' => 'Disabled');

				//output page
				$data['page_title'] = 'ACP - Feature Management';
				$data['admin_menu'] = 1;

				$this->view_fns->view('global/admincp/acp_featureedit', $data);
			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_feature/edit/' . $feature_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function delete($feature_id = NULL) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		//load helpers
		//$this->load->helper('bbcode');

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		if ($feature_id == NULL) {
			redirect('acp_feature/manage');
		}

		if ($feature_id > 0) {
			$data['mode'] = 'Edit';
		} else {
			$data['mode'] = 'Create';
		}

		$data['feature_id'] = $feature_id;
		$data['error'] = '';
		$data['highlight1'] = '';
		$data['highlight2'] = '';

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			$current_pilot_username = $this->session->userdata['username'];
			$current_pilot_user_id = $this->session->userdata['user_id'];

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));

			if ($feature_id > 0) {

				//need to determine whether or not this is a valid news item - as well as grabbing details for confirm page
				$query = $this->db->query("	SELECT 	
												config_featured.id,
												config_featured.type,
												config_featured.uri,
												config_featured.enabled,
												config_featured.order
														
												FROM config_featured
													
												WHERE config_featured.id = '$feature_id'
												
												LIMIT 1
											");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				//if no return, set create new
				if ($num_results < 1) {
					redirect('acp_feature/manage/');
				}

			}

			if ($valid == 'true') {

				//delete the record
				if ($feature_id > 0) {

					$id_val = $result['0']['id'];
					//perform the delete from db
					$this->db->where('id', $id_val);
					$this->db->delete('config_featured');
					//return to manage page
					redirect('acp_feature/manage/');
				} else {

					//return to manage page
					redirect('acp_feature/manage/');

				}

			} // haven't had data submitted or failed validation
			else {

				//if we are getting delete confirmation
				if ($feature_id > 0) {

					//prepare dropdowns etc for output from database
					$data['feature_type'] = $result['0']['type'];
					$data['feature_uri'] = $result['0']['uri'];
					$data['feature_enabled'] = $result['0']['enabled'];
					$data['feature_order'] = $result['0']['order'];

					//output page
					$data['page_title'] = 'ACP - Feature Management';
					$data['admin_menu'] = 1;

					$this->view_fns->view('global/admincp/acp_featuredelete', $data);
				} else {
					//return to manage page
					redirect('acp_feature/manage/');
				}

			}

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_feature/edit/' . $feature_id . '/';
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

	function manage($system_restrict = NULL, $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		$this->load->library('pagination');

		if ($system_restrict == NULL) {
			redirect('acp_feature/manage/0/');
		}

		$is_admin = $this->session->userdata('admin_cp');
		$acp_check_time = $this->session->userdata('admincp_time');
		$timeout_time = time() - $data['acp_timeout'];

		$data['system_restrict'] = $system_restrict;

		//grab post
		$post_system_restrict = $this->security->sanitize_filename($this->input->post('system_restrict'));
		$valid = $this->security->sanitize_filename($this->input->post('valid'));
		$search = $this->security->sanitize_filename($this->input->post('search'));

		if ($system_restrict != $post_system_restrict && $post_system_restrict != '') {
			redirect('acp_feature/manage/' . $post_system_restrict . '/');
		}

		//check if user is already logged in - if so, redirect
		if ($this->session->userdata('logged_in') != '1') {

			//display a page not found message
			show_404('page');

		} //not an admin
		elseif ($is_admin != '1') {
			redirect('');
		} elseif ($acp_check_time != '' && strtotime($acp_check_time) >= $timeout_time && $is_admin == '1') {

			//define session data
			$sessiondata = array(
				'admincp_time' => $data['gmt_mysql_datetime'],
			);

			//update data in session
			$this->session->set_userdata($sessiondata);

			if ($system_restrict == '0') {
				$sql_restrict = "WHERE config_featured.type = 'image'";
			} elseif ($system_restrict == '1') {
				$sql_restrict = "WHERE config_featured.type = 'video'";
			} else {
				$sql_restrict = "WHERE (config_featured.type = 'video'
												OR config_featured.type = 'image')";
			}

			//grab all news from the database
			$query = $this->db->query("	SELECT 	
											config_featured.id,
											config_featured.type,
											config_featured.uri,
											config_featured.enabled,
											config_featured.order
													
											FROM config_featured
												
											$sql_restrict
											
											ORDER BY config_featured.order DESC, config_featured.id
										");

			$data['result'] = $query->result();
			$data['num_rows'] = $query->num_rows();

			$system_array = array('ALL' => 'All', '0' => 'Images', '1' => 'Videos');

			$data['system_array'] = $system_array;

			//paginatipon
			if ($offset == NULL || $offset == '') {
				$offset = 0;
			}

			$data['offset'] = $offset;
			$data['limit'] = '15';

			$pag_config['base_url'] = $data['base_url'] . 'acp_feature/manage/' . $system_restrict;
			$pag_config['total_rows'] = $data['num_rows'];
			$pag_config['per_page'] = $data['limit'];
			$pag_config['uri_segment'] = 4;

			$this->pagination->initialize($pag_config);

			//output page
			$data['page_title'] = 'ACP - Feature Management';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_featuremanage', $data);

		} //invalid admin login
		elseif ($is_admin == '1') {

			//handle the previous page writer
			$sessiondata['return_page'] = 'acp_feature/manage/' . $system_restrict . '/' . $offset;
			//set data in session
			$this->session->set_userdata($sessiondata);

			redirect('auth/adminlogin');
		} else {
			redirect('');
		}
	}

}

?>