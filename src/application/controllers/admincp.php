<?php
 
class Admincp extends CI_Controller {

	function Admincp()
	{
		parent::__construct();	
	}
	
	
	
	
	function template()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		
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
		
			
			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$username = $this->security->sanitize_filename($this->input->post('username'));
			$password = $this->security->sanitize_filename($this->input->post('password'));

			//output page
			$data['page_title'] = 'Admin Control Panel';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_index', $data);
		}
		//invalid admin login
		elseif($is_admin == '1'){
			//handle the previous page writer
			$sessiondata['return_page'] = 'admincp/template';										
			//set data in session
			$this->session->set_userdata($sessiondata);
			
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
	}
	









	
	
	function index()
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');
		//load helpers
		$this->load->helper('jpgraph');
		
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

			//grab post data
			$valid = $this->security->sanitize_filename($this->input->post('valid'));
			$username = $this->security->sanitize_filename($this->input->post('username'));
			$password = $this->security->sanitize_filename($this->input->post('password'));

			$three_years_ago = gmdate('Y-m-d', strtotime('-3 years')).' 0000:00:00';
			$two_years_ago = gmdate('Y-m-d', strtotime('-2 years')).' 0000:00:00';
			$year_ago = gmdate('Y-m-d', strtotime('-1 year')).' 0000:00:00';


			//grab total flights stats from the database

			$query = $this->db->query("SELECT
				COUNT(pirep.id) as pireps,
				COUNT(DISTINCT pirep.user_id) as pilots,
				EXTRACT(MONTH FROM pirep.submitdate) as month,
				EXTRACT(YEAR FROM pirep.submitdate) as year
			FROM pirep
			WHERE submitdate >= '$three_years_ago'
			GROUP BY EXTRACT(YEAR_MONTH FROM pirep.submitdate)
			ORDER BY EXTRACT(YEAR_MONTH FROM pirep.submitdate)
			");

			$results = $query->result();
			$num_results = $query->num_rows();

			//current month
			$current_month = gmdate('n', time());
			$current_year = gmdate('Y', time());

			$data['current_month'] = $current_month;
			$data['current_year'] = $current_year;

			//$month_float = $current_month;
			$year_float = $current_year-3;

			while($year_float<= $current_year){

				$i = 1;
				while($i <= 12){
					$data['activity_stats'][$year_float][$i]['pireps'] = 0;
					$data['activity_stats'][$year_float][$i]['pilots'] = 0;
					$data['activity_stats'][$year_float][$i]['signups'] = 0;
					
				$i++;
				}
				
			$year_float++;
			}
			
			
			
			foreach($results as $row){
			
				$data['activity_stats'][$row->year][$row->month]['pireps'] = $row->pireps;
				$data['activity_stats'][$row->year][$row->month]['pilots'] = $row->pilots;
				
			
			}
			
			
			
			//grab new signups
			$query = $this->db->query("	SELECT 	
											COUNT(pilots.id) as signups,
											EXTRACT(MONTH FROM pilots.signupdate) as month,
											EXTRACT(YEAR FROM pilots.signupdate) as year	
													
											FROM pilots
												
											WHERE pilots.signupdate >= '$three_years_ago'
											
											GROUP BY EXTRACT(YEAR_MONTH FROM pilots.signupdate)
											
											ORDER BY EXTRACT(YEAR_MONTH FROM pilots.signupdate)
										");
										
			$results = $query->result();
			$num_results = $query->num_rows();
			
			foreach($results as $row){
			
				$data['activity_stats'][$row->year][$row->month]['signups'] = $row->signups;
				
			
			}
			

			//output page
			$data['page_title'] = 'Admin Control Panel';
			$data['admin_menu'] = 1;
			$this->view_fns->view('global/admincp/acp_index', $data);
			
		}
		//invalid admin login
		elseif($is_admin == '1'){
			redirect('auth/adminlogin');
		}
		else{
			redirect('');
		}
		
	//close function	
	}
	



	



	

	







	
	

		
}

/* End of file */
