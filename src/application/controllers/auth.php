<?php
 
class Auth extends CI_Controller {

	function Auth()
	{
		parent::__construct();
	}
	
	function index()
	{
		redirect('auth/login');
	}
	
	
	
	function adminlogin(){
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		
		$is_admin = $this->session->userdata('admin_cp');
		$acp_check = $this->session->userdata('admincp_time');
		$acp_check_time = strtotime($this->session->userdata('admincp_time'));
		$timeout_time = time() - $acp_timeout;
		
		$session_username = $this->session->userdata('username');
		
		$data['acp_check_time'] = $acp_check_time;
		$data['fifteenminsago'] = $timeout_time;
		
		if($acp_check_time != '' && $acp_check_time >= $timeout_time){
			//still valid, redirect
			redirect('admincp');	
		}		
		
		//check if user is already logged in - if not, redirect
		if($this->session->userdata('logged_in') != '1'){
		
			redirect('auth/login');
			
		}
		elseif($is_admin != '1'){
			//not an admin
			redirect('');
		}
		//if the user hasn't used admincp for a while (15mins)
		else{
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$username = $this->security->sanitize_filename($this->input->post('username'));
			$password = $this->security->sanitize_filename($this->input->post('password'));
						
			if($username != '' && $password != '' && $valid == 'true' && $username == $session_username){
				
				$query = $this->db->query("	SELECT 	pilots.id as id,
													pilots.username as username,
													pilots.email_confirmed as email_confirmed,
													pilots.flighthours as flight_hours,
													pilots.flightmins as flight_mins,
													pilots.fname as fname,
													pilots.sname as sname,
													pilots.country as country,
													pilots.status as status,
													pilots.password as password,
													hub.hub_icao as hub,
													hub.id as hub_id,
													ranks.rank as rank,
													ranks.name as rank_name,
													ranks.id as rank_id,
													usergroup_index.admin_cp as admin_cp
													
											FROM pilots 
													
												LEFT JOIN ranks
												ON ranks.id = pilots.rank
												
												LEFT JOIN hub
												on hub.id = pilots.hub
												
												LEFT JOIN usergroup_index
												ON usergroup_index.id = pilots.usergroup
											
											WHERE 	username = '".$session_username."' 
										");
				
				$result =  $query->result_array();
				$num_rows =  $query->num_rows();	
				
				
			}
			else{
				$num_rows = 0;
			}
	
			if ($num_rows == 1){
			
				//can test the password
				if($result['0']['password'] == $this->auth_fns->hash_password($result['0']['id'], $password)){
				//Can make the login
				
					//if we aren't banned or frozen and are an admin
					if($result['0']['status'] != '5' && $result['0']['status'] != '4' && $result['0']['admin_cp'] == '1'){
						//define session data
						$sessiondata = array(
							'admincp_time' => $gmt_mysql_datetime,
										);
																			
						//set data in session
						$this->session->set_userdata($sessiondata);
						
						//redirect to home page
						if($this->session->userdata('return_page') != ''){
							
							$return_page = $this->session->userdata('return_page');
							
							//clear the previous page
							$sessiondata['return_page'] = '';										
							//set data in session
							$this->session->set_userdata($sessiondata);
							
							redirect($return_page);
						}
						else{
							redirect('admincp');
						}
				
					}
					//if banned or frozen or not admin
					else{
						
						//define session data
						$sessiondata = array(
							'admincp_time' => NULL,
										);
																			
						//set data in session
						$this->session->set_userdata($sessiondata);
						
						
						redirect('auth/banned');
					}
				}	
					
				//redirect to home page
				redirect('');
			
	
			}
			//cannot authenticate
			else{
				//define session data as NULL
				//$sessiondata = array(
				//	'admincp_time' => NULL,
				//				);
																	
				//set data in session
				//$this->session->set_userdata($sessiondata);
			}
			
			
			//now we output the form for submission
			//Input and textarea field attributes
			$data['username'] = array('name' => 'username', 'id' => 'username','maxlength' => '30', 'size' => '30');
			$data['password'] = array('name' => 'password', 'id' => 'password','maxlength' => '30', 'size' => '30');
	
			if($valid == 'true'){
	
				//Exception Data
				$data['reqd1'] = "<span class=\"exception\">";
				$data['reqd2'] = "</span>";
				$data['exception'] = "The supplied information did not match our records. Please try again or contact the management team";
	
			}
			$data['page_title'] = 'Admin Verification';
			$this->view_fns->view('global/auth/admincplogin', $data);
			
			
		//close logged in
		}
		
	}
	
	
	
	
	function login()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		
		//check if user is already logged in - if so, redirect
		if($this->session->userdata('logged_in') == '1'){
		
			redirect('');
			
			//$data['page_title'] = 'Authentication';
			//$this->view_fns->view('global/auth/logged_in', $data);
			
		}
		else{
		
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$username = $this->security->sanitize_filename($this->input->post('username'));
			$password = $this->security->sanitize_filename($this->input->post('password'));
			
			//$hashed_password = $password;
			
			if($username != '' && $password != '' && $valid == 'true'){
				
				$query = $this->db->query("	SELECT 	pilots.id as id,
													pilots.username as username,
													pilots.usergroup as usergroup,
													pilots.management_pips as pips,
													pilots.email_confirmed as email_confirmed,
													pilots.flighthours as flight_hours,
													pilots.flightmins as flight_mins,
													pilots.fname as fname,
													pilots.sname as sname,
													pilots.country as country,
													pilots.status as status,
													pilots.password as password,
													hub.hub_icao as hub,
													hub.id as hub_id,
													ranks.rank as rank,
													ranks.name as rank_name,
													ranks.id as rank_id,
													usergroup_index.admin_cp as admin_cp
													
											FROM pilots 
													
												LEFT JOIN ranks
												ON ranks.id = pilots.rank
												
												LEFT JOIN hub
												on hub.id = pilots.hub
												
												LEFT JOIN usergroup_index
												ON usergroup_index.id = pilots.usergroup
											
											WHERE 	username = '".$username."' 
										");
				
				$result =  $query->result_array();
				$num_rows =  $query->num_rows();	
				
				
			}
			else{
				$num_rows = 0;
			}
	
			if ($num_rows == 1){
			
				//can test the password
				if($result['0']['password'] == $this->auth_fns->hash_password($result['0']['id'], $password)){
				//Can make the login
				
					//if we aren't banned or frozen
					if($result['0']['status'] != '5' && $result['0']['status'] != '4'){
						//define session data
						$sessiondata = array(
							'user_id' => $result['0']['id'],
							'username' => $username,
							'usergroup' => $result['0']['usergroup'],
							'pips' => $result['0']['pips'],
							'pilotname' => $result['0']['fname'].' '.$result['0']['sname'],
							'email_confirmed' => $result['0']['email_confirmed'],
							'flight_hours' => $result['0']['flight_hours'],
							'flight_mins' => $result['0']['flight_mins'],
							'fname' => $result['0']['fname'],
							'sname' => $result['0']['sname'],
							'rank_short' => $result['0']['rank'],
							'rank_long' => $result['0']['rank_name'],
							'rank_id' => $result['0']['rank_id'],
							'hub' => $result['0']['hub'],
							'hub_id' => $result['0']['hub_id'],
							'country' => $result['0']['country'],
							'admin_cp' => $result['0']['admin_cp'],
							'logged_in' => '1'
										);
																			
						//set data in session
						$this->session->set_userdata($sessiondata);
					}
					//if banned or frozen
					else{
						redirect('auth/banned');
					}
					
					
					//redirect to home page after login
					if($this->session->userdata('return_page') != ''){
							
						$return_page = $this->session->userdata('return_page');
						
						//clear the previous page
						$sessiondata['return_page'] = '';										
						//set data in session
						$this->session->set_userdata($sessiondata);
						
						redirect($return_page);
					}
					else{
						redirect('auth/login');
					}
				}
				else{
					//do nothing, form will be output
				}	
					
				
				
			
	
			}
			//cannot authenticate
			else{
				//do nothing
			}
			
			
			//now we output the form for submission
			//Input and textarea field attributes
			$data['username'] = array('name' => 'username', 'id' => 'username','maxlength' => '30', 'size' => '30');
			$data['password'] = array('name' => 'password', 'id' => 'password','maxlength' => '30', 'size' => '30');
	
			if($valid == 'true'){
	
				//Exception Data
				$data['reqd1'] = "<span class=\"exception\">";
				$data['reqd2'] = "</span>";
				$data['exception'] = "The supplied information did not match our records. Please try again or contact the management team";
	
			}
			$data['page_title'] = 'Login';
			$this->view_fns->view('global/auth/login', $data);
		//close not logged in
		}
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect('');
	}
		
}

/* End of file */