<?php

class Ehm extends CI_Controller {

	function Ehm()
	{
		parent::__construct();
	}

	function index($media_select = NULL)
	{
		//grab global initialisation
		include_once($this->config->item('full_base_path').'system/application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Format_fns');
		$this->load->library('Rss');
		//load helpers
		$this->load->helper('bbcode');



		//make database call to grab latest news
		$query = $this->db->query("	SELECT 	id,
											news_title,
											news_text,
											news_start_date_time,
											news_end_date_time,
											submitted,
											submitted_by,
											branch_type,
											context

									FROM news

									WHERE news_start_date_time <= now()
									AND (news_end_date_time >= now() OR news_end_date_time = '0000-00-00 00:00:00')

									AND branch_type = '0'
									ORDER by news_start_date_time desc
									LIMIT 4

										");

		$data['news_items'] =  $query->result();

		//AND (news_end_date_time >= now() OR news_end_date_time = '0000-00-00 00:00:00')

		//make database call to grab the recent members
		$query = $this->db->query("	SELECT 	pilots.id as id,
											pilots.username as username,
											pilots.fname as fname,
											pilots.sname as sname,
											pilots.status as status,
											pilots.lastflight as lastflight

									FROM pilots

									WHERE pilots.email_confirmed = '1'
									AND (pilots.flighthours > 0 AND pilots.flightmins > 0)

									ORDER BY signupdate desc

										");

		$data['home_members'] =  $query->result();

		$this_month = gmdate('Y-m-01',time());

		//make database call to grab the recent flights
		$query = $this->db->query("	SELECT 	pirep.id as id,
											pirep.submitdate as submitdate,
											pirep.passengers as passengers,
											pirep.cargo as cargo


									FROM pirep

									WHERE pirep.submitdate > '$this_month'

									GROUP BY pirep.id asc

										");

		$data['home_flights'] =  $query->result();

		//make database call to grab active pilots sim versions
		$query = $this->db->query("	SELECT 	pilots.id as id,
											flight_sim_versions.version_name as version_name


									FROM pilots

										LEFT JOIN flight_sim_versions
										ON pilots.fsversion = flight_sim_versions.id

									WHERE pilots.lastactive > '$active_compare_date'
									OR pilots.lastflight > '$active_compare_date'

										");

		$data['sim_stats'] =  $query->result();

		//if user is logged in -------------------------------
		//make database call to grab any upcoming propilot events
		//if($this->session->userdata('logged_in') == '1'){
		$query = $this->db->query("	SELECT
												propilot_event_index.id,
												propilot_event_index.name,
												propilot_event_index.start_date

												FROM propilot_event_index

													LEFT JOIN propilot_event_legs
													ON propilot_event_legs.event_id = propilot_event_index.id
													AND propilot_event_legs.sequence = '1'

												WHERE propilot_event_legs.end_date >= now()
												AND propilot_event_index.active = '1'

												ORDER BY RAND()

												LIMIT 1
											");

				$result = $query->result_array();
				$num_results = $query->num_rows();

				if($num_results > 0){
					$data['pp_event_id'] = $result['0']['id'];
					$data['pp_event_name'] = $result['0']['name'];
					$data['pp_event_start_date'] = $result['0']['start_date'];
				}
				else{
					$data['pp_event_id'] = '';
					$data['pp_event_name'] = '';
					$data['pp_event_start_date'] = '';
				}
		//}


		//add the ticker to the javascript loader
		$data['js_loader'] .= 	"var url = '".$data['base_url']."ajax/pilotnews';\n
				getPilotNews(url);";




				$query = $this->db->query("	SELECT
											config_featured.type,
											config_featured.uri,
											config_featured.enabled,
											config_featured.order

											FROM config_featured

											WHERE config_featured.enabled = '1'
											AND (config_featured.type = 'video'
												OR config_featured.type = 'image')

											ORDER BY config_featured.order, RAND()
										");

				$result = $query->result();
				$num_results = $query->num_rows();

				if($num_results < 1){
					$data['featured_img_enabled'] = 0;
					$data['featured_vid_enabled'] = 0;
				}
				else{
					$num_vid = 0;
					$num_img = 0;
					foreach($result as $row){
						if($row->type == 'video'){
							$featured_video_array[$num_vid] = $row->uri;
							$num_vid++;
						}
						elseif($row->type == 'image'){
							$data['cycleimages'][$num_img] = $row->uri;
							$num_img++;
						}

					}
				}

			//pick random video from array
			$max = count($featured_video_array) - 1;
			if($max >= 0 && $num_vid != 0){
				$video_index = rand ( 0 , $max );
				$data['featured_video'] = $featured_video_array[$video_index];
			}
			else{
				$data['featured_video'] = '';
				$data['featured_vid_enabled'] = 0;
			}

			if($num_img == 0){
				$data['featured_img_enabled'] = 0;
			}




		//Pull RSS Feed from forum posts
/*
		$this->rss->set_items_limit(10); // how many items to retrieve from each feed
		$this->rss->set_cache_life(10); // cache life in minutes
		$this->rss->set_cache_path($data['base_path']."assets/uploads/tmp/"); // by default library used CI default cache path, or path that you set in config.php
		//$this->rss->set_debug(); // in debug mode library will output on screen useful data

		// parameter can be array or string
		$this->rss->set_url(array(	//'https://www.fly-euroharmony.com/forum/index.php?action=.xml;type=rss2;sa=recent;limit=5',
									'https://www.fly-euroharmony.com/forum/index.php?action=.xml;type=rss2;sa=news;limit=10',
		                     ));
		// return array of objects containing rss data from all feeds
		$data['news'] = $this->rss->parse();
*/
$data['news'] = array();

		//select to show video or images - 0 video, 1 images
		if($media_select == 'image'){
			$vidorimage = 1;
		}
		elseif($media_select == 'video'){
			$vidorimage = 0;
		}
		else{
			$vidorimage = rand(0, 1);
		}

		$data['vidorimage'] = $vidorimage;

		if($vidorimage == '1'){
		//define javascrip for image cycle
		$data['page_js'] = '
		<script  type="text/javascript" src="'.$data['assets_url'].'javascript/functions/jquery.cycle.all.min.js"></script>
		<script  type="text/javascript" src="'.$data['assets_url'].'javascript/functions/cycle.js"></script>';
		}
		else{
		$data['page_js'] = '';
		}

		//call the gmaps script
		//$data['js_loader'] .= 	"startslides();";

		$this->view_fns->view('global/home', $data);
	}
}

/* End of file */
