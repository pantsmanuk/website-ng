<?php

class Information extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function twitter() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		$data['page_title'] = 'Information - Tweets';
		$this->view_fns->view('global/information/inf_twitter', $data);
	}

	function discounts() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		$data['page_title'] = 'Information - Pilot Discounts';
		$this->view_fns->view('global/information/inf_discounts', $data);

	}

	function latest_flights() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load additional libraries
		$this->load->library('Format_fns');

		//grab all current flights

		$check_datetime = gmdate('Y-m-d H-i-s', strtotime('-5 minutes'));

		$query = $this->db->query("
		
			SELECT 	acars.origin as start_icao,
					acars.destination as end_icao,
					acars.lat,
					acars.lon,
					acars.bearing,
					acars.altitude,
					acars.ias,
					acars.propilot_flight,
					pilots.username,
					pilots.fname,
					pilots.sname,
					ranks.rank,
					aircraft.name,
					dep_data.lat as dep_lat,
					dep_data.long as dep_lon,
					arr_data.lat as arr_lat,
					arr_data.long as arr_lon
			
			FROM acars
			
				LEFT JOIN pilots
				ON pilots.id = acars.user_id
				
				LEFT JOIN ranks
				ON pilots.rank = ranks.id
				
				LEFT JOIN pirep
				on acars.aggregate_id = pirep.aggregate_id
				
				LEFT JOIN aircraft
				ON aircraft.id = acars.aircraft
				
				LEFT JOIN airports_data as dep_data
				ON dep_data.icao = acars.origin
				
				LEFT JOIN airports_data as arr_data
				ON arr_data.icao = acars.destination
				
			WHERE acars.updated >= '$check_datetime'
			AND pirep.id IS NULL
			
			ORDER BY pirep.submitdate DESC
			
			LIMIT 10
		
		");

		$data['current_flights'] = $query->result();
		$data['num_current_flights'] = $query->num_rows();

		$check_datetime = gmdate('Y-m-d H-i-s', strtotime('-24 hours'));

		//grab the last 10 pireps
		$query = $this->db->query("
		
			SELECT 	pirep.start_icao,
					pirep.end_icao,
					pirep.passengers,
					pirep.cargo,
					pirep.landing_time,
					pirep.propilot_flight,
					pilots.username,
					pilots.fname,
					pilots.sname,
					hub.hub_icao,
					hub.hub_name,
					hub.id as hub_id,
					aircraft.name
			
			FROM pirep
			
				LEFT JOIN pilots
				ON pilots.id = pirep.user_id
				
				LEFT JOIN hub
				on hub.id = pirep.hub
				
				LEFT JOIN aircraft
				ON aircraft.id = pirep.aircraft
				
			WHERE pirep.landing_time >= '$check_datetime'
			
			ORDER BY pirep.submitdate DESC
		
		");

		$data['recent_flights'] = $query->result();
		$data['num_recent_flights'] = $query->num_rows();

		$focus_lat = 51.477497;
		$focus_lon = -0.461389;

		$current_pilot = $this->session->userdata('user_id');

		//grab hub location to focus on
		if ($this->session->userdata('logged_in') == '1') {

			$query = $this->db->query("
		
			SELECT 	pilots.username,
					pilots.fname,
					pilots.sname,
					airports_data.lat as lat,
					airports_data.long as lon
			
			FROM pilots
				
				LEFT JOIN hub
				on hub.id = pilots.hub
				
				LEFT JOIN airports_data
				ON airports_data.icao = hub.hub_icao
				
			WHERE pilots.id = '$current_pilot'
		
		");

			$loc_result = $query->result_array();

			if ($query->num_rows() == 1) {
				if ($loc_result['0']['lat'] != '') {
					$focus_lat = $loc_result['0']['lat'];
				}
				if ($loc_result['0']['lon'] != '') {
					$focus_lon = $loc_result['0']['lon'];
				}
			}

		}

		//define javascrip for google maps API
		$data['page_js'] = '
		<script  type="text/javascript" src="' . $data['assets_url'] . 'javascript/functions/kinetic.min.js"></script>
		<script  type="text/javascript" src="' . $data['assets_url'] . 'javascript/resources/worldMap.js"></script>';

		$data['page_js'] .= "<script>
      function map_initialise(){
        var stage = new Kinetic.Stage({
          container: 'flightmapcontainer',
          width: 638,
          height: 285
        });
        var mapLayer = new Kinetic.Layer({
          y: 20,
          scale: 0.65
        });

        /*
         * loop through country data stored in the worldMap
         * variable defined in the worldMap.js asset
         */
        for(var key in worldMap.shapes) {
          var c = worldMap.shapes[key];

          var path = new Kinetic.Path({
            commands: c,
            fill: '#ccc',
            stroke: '#555',
            strokeWidth: 1,
            alpha: 0.7
          });

          path.on('mouseover', function() {
          	/*
            this.setFill('red');
            this.setAlpha(1);
            mapLayer.draw();
            */
          });

          path.on('mouseout', function() {
          	/*
            this.setFill('#ccc');
            this.setAlpha(0.7);
            mapLayer.draw();
            */
          });

          mapLayer.add(path);
        }
        
        

        stage.add(mapLayer);
      };

    </script>
		";

		/*
		$data['page_js'] .= 'var aircraft = [';
		$i = 0;
		foreach($data['current_flights'] as $row){
			
			if($i > 0){
			$data['page_js'] .= ',';
			}
			$data['page_js'] .= '
		';
			$data['page_js'] .= '[\'EHM-'.$row->username.' '.$row->fname.' '.$row->sname.'\', '.$row->lat.', '.$row->lon.', '.$row->dep_lat.', '.$row->dep_lon.', '.$row->arr_lat.', '.$row->arr_lon.']';
		$i++;
		}
		$data['page_js'] .= '
		];</script>';
			*/

		//call the gmaps script
		$data['js_loader'] .= "map_initialise();";

		$data['page_title'] = 'Information - Latest Flights';
		$this->view_fns->view('global/information/inf_latestflights', $data);

	}

	function latest_flights_o() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load additional libraries
		$this->load->library('Format_fns');

		//grab all current flights

		$check_datetime = gmdate('Y-m-d H-i-s', strtotime('-5 minutes'));

		$query = $this->db->query("
		
			SELECT 	acars.origin as start_icao,
					acars.destination as end_icao,
					acars.lat,
					acars.lon,
					acars.bearing,
					acars.altitude,
					acars.ias,
					acars.propilot_flight,
					pilots.username,
					pilots.fname,
					pilots.sname,
					ranks.rank,
					aircraft.name,
					dep_data.lat as dep_lat,
					dep_data.long as dep_lon,
					arr_data.lat as arr_lat,
					arr_data.long as arr_lon
			
			FROM acars
			
				LEFT JOIN pilots
				ON pilots.id = acars.user_id
				
				LEFT JOIN ranks
				ON pilots.rank = ranks.id
				
				LEFT JOIN pirep
				on acars.aggregate_id = pirep.aggregate_id
				
				LEFT JOIN aircraft
				ON aircraft.id = acars.aircraft
				
				LEFT JOIN airports_data as dep_data
				ON dep_data.icao = acars.origin
				
				LEFT JOIN airports_data as arr_data
				ON arr_data.icao = acars.destination
				
			WHERE acars.updated >= '$check_datetime'
			AND pirep.id IS NULL
			
			ORDER BY pirep.submitdate DESC
			
			LIMIT 10
		
		");

		$data['current_flights'] = $query->result();
		$data['num_current_flights'] = $query->num_rows();

		$check_datetime = gmdate('Y-m-d H-i-s', strtotime('-24 hours'));

		//grab the last 10 pireps
		$query = $this->db->query("
		
			SELECT 	pirep.start_icao,
					pirep.end_icao,
					pirep.passengers,
					pirep.cargo,
					pirep.landing_time,
					pirep.propilot_flight,
					pilots.username,
					pilots.fname,
					pilots.sname,
					hub.hub_icao,
					hub.hub_name,
					hub.id as hub_id,
					aircraft.name
			
			FROM pirep
			
				LEFT JOIN pilots
				ON pilots.id = pirep.user_id
				
				LEFT JOIN hub
				on hub.id = pirep.hub
				
				LEFT JOIN aircraft
				ON aircraft.id = pirep.aircraft
				
			WHERE pirep.landing_time >= '$check_datetime'
			
			ORDER BY pirep.submitdate DESC
		
		");

		$data['recent_flights'] = $query->result();
		$data['num_recent_flights'] = $query->num_rows();

		$focus_lat = 51.477497;
		$focus_lon = -0.461389;

		$current_pilot = $this->session->userdata('user_id');

		//grab hub location to focus on
		if ($this->session->userdata('logged_in') == '1') {

			$query = $this->db->query("
		
			SELECT 	pilots.username,
					pilots.fname,
					pilots.sname,
					airports_data.lat as lat,
					airports_data.long as lon
			
			FROM pilots
				
				LEFT JOIN hub
				on hub.id = pilots.hub
				
				LEFT JOIN airports_data
				ON airports_data.icao = hub.hub_icao
				
			WHERE pilots.id = '$current_pilot'
		
		");

			$loc_result = $query->result_array();

			if ($query->num_rows() == 1) {
				if ($loc_result['0']['lat'] != '') {
					$focus_lat = $loc_result['0']['lat'];
				}
				if ($loc_result['0']['lon'] != '') {
					$focus_lon = $loc_result['0']['lon'];
				}
			}

		}

		//define javascrip for google maps API
		$data['page_js'] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
		';

		$data['page_js'] .= 'var aircraft = [';
		$i = 0;
		foreach ($data['current_flights'] as $row) {

			if ($i > 0) {
				$data['page_js'] .= ',';
			}
			$data['page_js'] .= '
		';
			$data['page_js'] .= '[\'EHM-' . $row->username . ' ' . $row->fname . ' ' . $row->sname . '\', ' . $row->lat . ', ' . $row->lon . ', ' . $row->dep_lat . ', ' . $row->dep_lon . ', ' . $row->arr_lat . ', ' . $row->arr_lon . ']';
			$i++;
		}
		$data['page_js'] .= '
		];';

		$data['page_js'] .= '
		
		
			function gmaps_initialize() {
				var latlng = new google.maps.LatLng(' . $focus_lat . ', ' . $focus_lon . ');
				var myOptions = {
					zoom: 3,
					center: latlng,
					mapTypeControl: true,
					mapTypeControlOptions: {
					  style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
					},
					zoomControl: true,
					zoomControlOptions: {
					  style: google.maps.ZoomControlStyle.SMALL
					},
					mapTypeId: google.maps.MapTypeId.SATELLITE

				};
			
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				
				setMarkers(map, aircraft);
				
			}
			
			
			
			function setMarkers(map, locations) {
			// Add markers to the map

				var image = new google.maps.MarkerImage(\'' . $data['assets_url'] . 'images/icons/application/gmap_aircraft.png\',
					new google.maps.Size(24,24),
					new google.maps.Point(0,0),
					new google.maps.Point(12,12));
				
				 
				for (var i = 0; i < locations.length; i++) {
					var craft = locations[i];
					var myLatLng = new google.maps.LatLng(craft[1], craft[2]);
					var marker = new google.maps.Marker({
						position: myLatLng,
						map: map,
						icon: image,
						title: craft[0]
					});
					
					//polylines green completed, red not
					
					var flightPlanCoordinates1 = [
					new google.maps.LatLng(craft[3], craft[4]),
					new google.maps.LatLng(craft[1], craft[2]),
					];
				  
					var flightPathComplete = new google.maps.Polyline({
						path: flightPlanCoordinates1,
						strokeColor: "#aaaaaa",
						strokeOpacity: 1.0,
						strokeWeight: 2,
						geodesic: true
					});
	
					flightPathComplete.setMap(map);
					
					var flightPlanCoordinates2 = [
					new google.maps.LatLng(craft[1], craft[2]),
					new google.maps.LatLng(craft[5], craft[6]),
					];
				  
					var flightPathIncomplete = new google.maps.Polyline({
						path: flightPlanCoordinates2,
						strokeColor: "#FF0000",
						strokeOpacity: 1.0,
						strokeWeight: 2,
						geodesic: true
					});
	
					flightPathIncomplete.setMap(map);
					
				}
			}
			
			
			function centreLoc(lat, lon){
			
				map.setCenter(new google.maps.LatLng(lat, 
                                     lon));
				map.setZoom(5);
			}
			
			
			function gmapTicker2(){
			
				//deleteOverlays();
				addFlights();
				//showOverlays();
				
				//setTimeout("gmapTicker()", 10000);
			
			}
			
		</script>		
		';

		//call the gmaps script
		$data['js_loader'] .= "gmaps_initialize();";

		$data['page_title'] = 'Information - Latest Flights';
		$this->view_fns->view('global/information/inf_latestflights', $data);

	}

	function online() {

		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');

		/*
		PHP codes to generate online VATSIM pilots names according to their callsign
		PHP codes by Naresh Gurung 931375 and Lee Collier 892337
		
		You must have status.txt and satnet-data.txt files in the root directory of
		your webpage.  Leave the filenames unchanged.
		
		status.txt file will be updated every 24 hours.
		satnet-data.txt file will be updated every 5 minutes.
		
		You can download status.txt file from:http://usa-s1.vatsim.net/data/status.txt
		You can download satnet-data.txt file from either of these links depending on availability:
		http://www.vatsim.net/data/satnet-data.txt
		http://vatsim.liveatc.net/satnet-data.txt
		http://vatsim.info/servinfo/satnet-data.txt
		http://usa-w.vatsim.net/data/satnet-data.txt
		http://level27.ca/data/satnet-data.txt
		http://vatsim.metacraft.com/satnet-data.txt
		
		Before uploading this php file to your website, you must change few things in the codes below:
		1: Change three letter VAName to the first three letter of your VA callsign, for example: AAL
		2: Change the download time (in seconds) for status.txt file [default is set at 21600 seconds (6 HRs)
		3: Change the download time (in seconds) for satnet-data.txt file [default is set at 120 seconds (2 MINS)
		
		Any questions, just email me baba@gurungtons.com
		*/

		$VAName = "EHM";                        //* Enter your three letter VA name
		$downloadstatustime = 86400;               //* Enter in seconds (for status.txt file)
		$downloadsatnetdatatime = 300;            //* Enter in seconds (for satnet-data.txt file) 5mins

		$statusURL = "http://usa-s1.vatsim.net/data/status.txt";      //*change the URL incase it changes in the future
		$statusfilename = $data['tmp_upload_path'] . "status.txt";
		$satnetfilename = $data['tmp_upload_path'] . "satnet-data.txt";

		$data['vatsim_out'] = '';

		//$data['vatsim_out'] .= "<div align='center'>";

		$smoditime = filemtime($statusfilename);
		$scurrenttime = time();
		$sdiftime = $scurrenttime - $smoditime;

		if ($sdiftime > $downloadstatustime) {
			if (!copy($statusURL, $statusfilename)) {
				$data['vatsim_out'] .= "Data Caching Failed... Try A Different Server";
			}
		}

		srand((double)microtime() * 1000000);

		$lines = file($statusfilename);
		$datalinks = array();
		$i = 0;
		$l_count = count($lines);
		for ($x = 0; $x < $l_count; $x++) {
			if (substr($lines[$x], 0, 4) == 'url0') {
				$datalinks[] = substr($lines[$x], 5, strlen($lines[$x]) - 6);
				$i = $i + 1;
			}
		}

		$moditime = filemtime($satnetfilename);
		$currenttime = time();
		$diftime = $currenttime - $moditime;

		$randomdata = $datalinks[rand(0, 2)];

		if ($diftime > $downloadsatnetdatatime) {
			if (!copy(rtrim($randomdata), $satnetfilename)) {
				$data['vatsim_out'] .= "Data Caching Failed... Try A Different Server";
			}
		}

		$num = 0;
		$fp = fopen($satnetfilename, "r");
		$data['vatsim_out'] .= '<table width="200" class="borderbox">';
		$i = 0;
		while (!feof($fp)) {

			if ($i % 2 == 0) {
				$bgcolor = ' bgcolor="#e4e2fc" ';
			} else {
				$bgcolor = '';
			}
			$i++;

			$line = fgets($fp, 999);
			if (preg_match('/^(' . $VAName . ')[A-Z0-9]/', $line)) {
				list($callsign, $cid, $name, $clienttype, $frequency, $a, $b, $c, $d, $aircraft, $ias, $origin, $fl, $destination) = preg_split("/:/", $line);
				$data['vatsim_out'] .= "<tr $bgcolor>";
				$data['vatsim_out'] .= '<td height="29" align="left">';
				$data['vatsim_out'] .= '<b>' . $callsign . ' ' . substr($name, 0, strlen($name) - 4) . '</b><br />' . $origin . '-' . $destination . '<div style="float: right;">' . $fl . '</div>';
				$data['vatsim_out'] .= "</td>";
				$data['vatsim_out'] .= "</tr>";
				$num = $num + 1;
			}
		}

		if ($num == 0) {
			$data['vatsim_out'] .= "<tr>";
			$data['vatsim_out'] .= "<td align='center'>";
			$data['vatsim_out'] .= "Found no online flights";
			$data['vatsim_out'] .= "</td>";
			$data['vatsim_out'] .= "</tr>";
		}
		$data['vatsim_out'] .= "</table>";
		//$data['vatsim_out'] .= "</div>";

		$data['page_title'] = 'Information - Online Pilots';
		$this->view_fns->view('global/information/inf_online', $data);

	}

	function index($page = 'history') {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//caching
		//$this->output->cache($cache_duration_normal);

		//define page array to restrict page diplay for security

		$page_array = array(
			'history',
			'faq',
			'new',
			'whyehm',
		);

		if (!in_array($page, $page_array)) {
			$page = 'history';
		}

		$page_title = ucfirst($page);

		if ($page_title == 'Whyehm') {
			$page_title = 'Why join Euroharmony?';
		}
		if ($page_title == 'New') {
			$page_title = 'New to Virtual Airlines';
		}

		$data['page_title'] = 'Information - ' . $page_title;
		$this->view_fns->view('global/information/inf_' . $page, $data);
	}

	function propilot() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//caching
		//$this->output->cache($cache_duration_normal);

		/*
		//grab the managers form the database
		$query = $this->db->query("	SELECT 	pilots.id as id, 
											pilots.username as username,
											pilots.fname as fname,
											pilots.sname as sname,
											pilots.title as title,
											pilots.country as country_code
											
											
									FROM pilots
									
										LEFT JOIN usergroup_index
										ON usergroup_index.id = pilots.usergroup
									
									WHERE usergroup_index.management = '1'
									
									ORDER BY usergroup_index.order
											
										");
				
		$data['management_results'] =  $query->result();
		
		*/

		$data['page_title'] = 'Propilot';
		$data['no_links'] = '1';
		$this->view_fns->view('global/information/infm_propilot', $data);
	}

	function management() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//caching
		//$this->output->cache($cache_duration_normal);

		//grab the managers form the database
		$query = $this->db->query("	SELECT 	pilots.id AS id, 
											pilots.username AS username,
											pilots.signupdate AS signupdate,
											pilots.fname AS fname,
											pilots.sname AS sname,
											pilots.title AS title,
											pilots.rank AS rank_id,
											management_departments.name AS department,
											pilots.country AS country_code,
											pilots.management_pips AS pips
											
											
									FROM pilots
									
										LEFT JOIN usergroup_index
										ON usergroup_index.id = pilots.usergroup
										
										LEFT JOIN management_departments
										ON management_departments.id = pilots.department
									
									WHERE usergroup_index.management = '1'
									
									ORDER BY management_departments.order, pilots.management_pips DESC, pilots.signupdate, pilots.username
											
										");

		$data['management_results'] = $query->result();

		$data['page_title'] = 'Management team';
		$this->view_fns->view('global/information/infm_management', $data);
	}

	function pilots($all = 0, $sort = 'username', $offset = 0) {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		$this->load->library('pagination');
		$this->load->library('format_fns');

		if ($all == 1) {
			$where = "";
		} elseif ($all == "aerosoft") {
			$where = "WHERE pilots.lastflight > '$active_compare_date' 
						AND pilots.rank >= 3";
			//>= 4 equals commercial captain or higher (40 hours+)
		} else {
			$where = "WHERE pilots.lastflight > '$active_compare_date'";
		}

		$data['sort'] = $sort;
		$data['all'] = $all;

		switch ($sort) {

			case 'rank':
				$order = 'ORDER BY ranks.id, pilots.username';
				break;

			case 'fname':
				$order = 'ORDER BY pilots.fname, pilots.sname';
				break;

			case 'sname':
				$order = 'ORDER BY pilots.sname, pilots.fname';
				break;

			case 'hours':
				$order = 'ORDER BY pilots.flighthours, pilots.flightmins';
				break;

			default:
				$order = 'ORDER BY pilots.username';
				break;

		}

		//grab the managers form the database
		$query = $this->db->query("	SELECT 	pilots.id as id, 
											pilots.username as username,
											pilots.fname as fname,
											pilots.sname as sname,
											pilots.title as title,
											pilots.title as title,
											pilots.lastflight as lastflight,
											pilots.flighthours,
											pilots.flightmins,
											ranks.rank as rank_short,
											ranks.name as rank_long,
											pilots.country as country_code
											
											
									FROM pilots
									
										LEFT JOIN ranks
										ON ranks.id = pilots.rank
									
									$where
									
									$order
											
										");

		$data['pilot_data'] = $query->result();
		$data['num_pilots'] = $query->num_rows();

		$data['pilots_menu_array'] = array(
			'0' => 'Active Pilots',
			'1' => 'All Pilots',
		);

		//pagination
		if ($offset == NULL || $offset == '') {
			$offset = 0;
		}

		$data['offset'] = $offset;
		$data['limit'] = '15';

		$pag_config['base_url'] = $data['base_url'] . 'information/pilots/' . $all . '/' . $sort . '/';
		$pag_config['total_rows'] = $data['num_pilots'];
		$pag_config['per_page'] = $data['limit'];
		$pag_config['uri_segment'] = 5;

		$this->pagination->initialize($pag_config);

		$data['page_title'] = 'Pilots';
		$this->view_fns->view('global/information/infm_pilots', $data);
	}

}

/* End of file */