<?php

class Flogger_pull extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function test($urlval = 'aircraft') {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models

		$data['urlval'] = $data['base_url'] . 'flogger_pull/' . $urlval;

		$data['page_title'] = 'Test Post';
		$data['no_links'] = '1';
		$this->view_fns->view('global/flogger/testpost.php', $data);

	}

	function auth() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models
		$this->load->library('Auth_fns');

		//get post data
		$post_username = $this->security->sanitize_filename($this->input->post('username'));
		$post_password = $this->security->sanitize_filename($this->input->post('password'));

		if ($post_username != '' && $post_password != '') {

			$query = $this->db->query("	SELECT 	pilots.id AS id,
													pilots.username AS username,
													pilots.usergroup AS usergroup,
													pilots.management_pips AS pips,
													pilots.email_confirmed AS email_confirmed,
													pilots.flighthours AS flight_hours,
													pilots.flightmins AS flight_mins,
													pilots.fname AS fname,
													pilots.sname AS sname,
													pilots.country AS country,
													pilots.status AS status,
													pilots.password AS password,
													hub.hub_icao AS hub,
													hub.id AS hub_id,
													ranks.rank AS rank,
													ranks.name AS rank_name,
													ranks.id AS rank_id,
													usergroup_index.admin_cp AS admin_cp

											FROM pilots

												LEFT JOIN ranks
												ON ranks.id = pilots.rank

												LEFT JOIN hub
												ON hub.id = pilots.hub

												LEFT JOIN usergroup_index
												ON usergroup_index.id = pilots.usergroup

											WHERE 	username = '" . $post_username . "'
										");

			$result = $query->result_array();
			$num_rows = $query->num_rows();

		} else {
			$num_rows = 0;
		}

		//if we got a hit back on the username, check the password
		if ($num_rows == 1) {

			//can test the password
			if ($result['0']['password'] == $this->auth_fns->hash_password($result['0']['id'], $post_password)) {

				//if we aren't banned or frozen or email unconfirmed
				if ($result['0']['status'] != '5' && $result['0']['status'] != '4' && $result['0']['email_confirmed'] == '1') {
					//define data
					$user_id = $result['0']['id'];
					$username = $post_username;
					$usergroup = $result['0']['usergroup'];
					$pips = $result['0']['pips'];
					$email_confirmed = $result['0']['email_confirmed'];
					$flight_hours = $result['0']['flight_hours'];
					$flight_mins = $result['0']['flight_mins'];
					$fname = $result['0']['fname'];
					$sname = $result['0']['sname'];
					$rank_short = $result['0']['rank'];
					$rank_long = $result['0']['rank_name'];
					$rank_id = $result['0']['rank_id'];
					$hub = $result['0']['hub'];
					$hub_id = $result['0']['hub_id'];
					$country = $result['0']['country'];
					$admin_cp = $result['0']['admin_cp'];
					$logged_in = '1';

					//output response
					echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
					//root element header
					echo '<response>' . "\n";
					echo '	<header>' . "\n";
					echo '		<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
					//handle return codes
					echo '		<errcode>authSuccess</errcode>' . "\n";
					echo '		<errmessage>User successfully authenticated</errmessage>' . "\n";
					echo '	</header>' . "\n";
					echo '	<data>' . "\n";

					echo '		<user>' . "\n";
					echo '			<username>' . $username . '</username>' . "\n";
					echo '			<fname>' . $fname . '</fname>' . "\n";
					echo '			<sname>' . $sname . '</sname>' . "\n";
					echo '			<pilotname>' . $fname . ' ' . $sname . '</pilotname>' . "\n";
					echo '			<country>' . $country . '</country>' . "\n";
					echo '			<flight_hours>' . $flight_hours . '</flight_hours>' . "\n";
					echo '			<flight_mins>' . $flight_mins . '</flight_mins>' . "\n";
					echo '			<rank_short>' . $rank_short . '</rank_short>' . "\n";
					echo '			<rank_long>' . $rank_long . '</rank_long>' . "\n";
					echo '			<rank_id>' . $rank_id . '</rank_id>' . "\n";
					echo '			<hub>' . $hub . '</hub>' . "\n";
					echo '			<hub_id>' . $hub_id . '</hub_id>' . "\n";
					echo '		</user>' . "\n";

					echo '	</data>' . "\n";
					echo '</response>' . "\n";

				} //if banned or frozen or email unconfirmed
				else {

					//output response
					echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
					//root element header
					echo '<response>' . "\n";
					echo '	<header>' . "\n";
					echo '		<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
					//handle return codes
					if ($result['0']['status'] == '5') {
						//banned
						echo '		<errcode>authDenied</errcode>' . "\n";
						echo '		<errmessage>User is currently banned</errmessage>' . "\n";
					} elseif ($result['0']['status'] == '4') {
						//account frozen
						echo '		<errcode>authDenied</errcode>' . "\n";
						echo '		<errmessage>User is currently frozen</errmessage>' . "\n";
					} elseif ($result['0']['email_confirmed'] != '1') {
						//email not confirmed
						echo '		<errcode>authDenied</errcode>' . "\n";
						echo '		<errmessage>User\'s email is not confirmed</errmessage>' . "\n";
					}
					echo '	</header>' . "\n";
					echo '	<data />' . "\n";
					echo '</response>' . "\n";

				}

			} //if password failed
			else {
				//output response
				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				//root element header
				echo '<response>' . "\n";
				echo '	<header>' . "\n";
				echo '		<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				//handle return codes
				//failed to authenticate
				echo '		<errcode>authFail</errcode>' . "\n";
				echo '		<errmessage>Username or Password incorrect</errmessage>' . "\n";

				echo '	</header>' . "\n";
				echo '	<data />' . "\n";
				echo '</response>' . "\n";
			}

		} //no credentials supplied
		else {
			//output response
			echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			//root element header
			echo '<response>' . "\n";
			echo '	<header>' . "\n";
			echo '		<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
			//handle return codes
			//failed to authenticate
			echo '		<errcode>authFail</errcode>' . "\n";
			echo '		<errmessage>Username or Password incorrect</errmessage>' . "\n";

			echo '	</header>' . "\n";
			echo '	<data />' . "\n";
			echo '</response>' . "\n";
		}

	}

	function airport_alias() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models

		//get post
		$post_timestamp = $this->security->sanitize_filename($this->input->post('timestamp'));
		$post_flightsim = $this->security->sanitize_filename($this->input->post('flightsimID'));
		//$post_timestamp = $this->input->post('timestamp');

		//handle empty submission
		if (is_null($post_timestamp) || $post_timestamp == '') {
			$post_timestamp = '0000-00-00 00:00:00';
		}

		if (is_null($post_flightsim) || $post_flightsim == '' || !is_numeric($post_flightsim)) {
			$post_flightsim = 'ALL';
		}

		//testing value FSX
		//$post_flightsim = '3';

		$check_date_day = gmdate('d', strtotime($post_timestamp));
		$check_date_month = gmdate('m', strtotime($post_timestamp));
		$check_date_year = gmdate('Y', strtotime($post_timestamp));

		//check date is valid
		if (is_numeric($check_date_day) && is_numeric($check_date_month) && is_numeric($check_date_year)
			&& checkdate($check_date_month, $check_date_day, $check_date_year)) {
			$valid_timestamp = '1';
		}

		//check that date is not in the future
		if (strtotime($post_timestamp) > time()) {
			//future date!
			$valid_timestamp = '0';
		}

		//handle return codes
		if ($valid_timestamp != '1') {
			$post_timestamp = '0000-00-00 00:00:00';
		}

		//$post_timestamp = '2013-03-16';

		$restrict_val = "WHERE icao_airports_sim_fix.disabled = '0'";

		/*
		if($post_timestamp == '0000-00-00 00:00:00'){

			if($post_flightsim != 'ALL' && is_numeric($post_flightsim)){

					$restrict_val .= " AND icao_airports_sim_fix.flight_sim_id = '$post_flightsim'";
			}
		}
		else{
			$restrict_val = "AND icao_airports_sim_fix.modified > '$post_timestamp' ";
			//$restrict_val = "";
			if($post_flightsim != 'ALL' && is_numeric($post_flightsim)){

					$restrict_val .= " AND icao_airports_sim_fix.flight_sim_id = '$post_flightsim'";
			}
		}
		*/

		if ($post_flightsim != 'ALL' && is_numeric($post_flightsim)) {

			$restrict_val .= " AND icao_airports_sim_fix.flight_sim_id = '$post_flightsim'";
		}

		//grab all the airport_alias from the db
		$query = $this->db->query("	SELECT
											icao_airports_sim_fix.icao_code,
											icao_airports_sim_fix.flight_sim_id,
											icao_airports_sim_fix.flight_sim_code,
											icao_airports_sim_fix.disabled,
											icao_airports_sim_fix.modified,
											flight_sim_versions.version_name,
											flight_sim_versions.flogger_name



									FROM icao_airports_sim_fix

									LEFT JOIN flight_sim_versions
									ON flight_sim_versions.id = icao_airports_sim_fix.flight_sim_id

									$restrict_val

										");

		$list = $query->result();
		$num_rows = $query->num_rows();

		echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		//root element header
		echo '<response>' . "\n";
		echo '<header>' . "\n";
		echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
		//handle return codes
		if ($valid_timestamp != '1') {
			echo '	<errcode>errTimestamp</errcode>' . "\n";
			echo '	<errmessage>Timestamp supplied is not valid</errmessage>' . "\n";
		} elseif ($num_rows == 0) {
			echo '	<errcode>okNoData</errcode>' . "\n";
			echo '	<errmessage>No alias updates found</errmessage>' . "\n";
		} elseif ($num_rows > 0 && $valid_timestamp == '1') {
			echo '	<errcode>okDownload</errcode>' . "\n";
			echo '	<errmessage>Alias updates found</errmessage>' . "\n";
		} else {
			//can't occur - unknown error will kill data return
			echo '	<errcode>errUnknown</errcode>' . "\n";
			echo '	<errcode>An unknown error has occurred on the server</errcode>' . "\n";
		}

		echo '	<records>' . $num_rows . '</records>' . "\n";

		echo '</header>' . "\n";

		if ($num_rows > 0) {
			echo '<data>' . "\n";
			echo '	<airport-aliases>' . "\n";
			foreach ($list as $alias) {

				//we now output every row individually
				echo '		<airport-alias>' . "\n";
				echo '			<fs-id>' . $alias->flight_sim_id . '</fs-id>' . "\n";
				echo '			<fs-version>' . $alias->flogger_name . '</fs-version>' . "\n";
				echo '			<ehm-code>' . $alias->icao_code . '</ehm-code>' . "\n";
				echo '			<fs-code>' . $alias->flight_sim_code . '</fs-code>' . "\n";
				echo '		</airport-alias>' . "\n";

				/*
		echo '			<icao_code>'.$alias->icao_code.'</icao_code>'."\n";
		echo '			<ehm_code>'.$alias->ehm_code.'</ehm_code>'."\n";
		echo '			<flight_sim_id>'.$alias->flight_sim_id.'</flight_sim_id>'."\n";
		echo '			<flight_sim_code>'.$alias->flight_sim_code.'</flight_sim_code>'."\n";
		echo '			<fs2004_code>'.$alias->fs2004_code.'</fs2004_code>'."\n";
		echo '			<fsx_code>'.$alias->fsx_code.'</fsx_code>'."\n";
		echo '			<city>'.$alias->city.'</city>'."\n";
		echo '			<country>'.$alias->country.'</country>'."\n";
		echo '			<deleted>'.$alias->deleted.'</deleted>'."\n";
		echo '			<modified>'.$alias->modified.'</modified>'."\n";
	*/

				/*

			if($alias->fs2004_code != $alias->ehm_code){
				echo '		<airport-alias>'."\n";
					echo '			<fs-version>FS9</fs-version>'."\n";
					echo '			<ehm-code>'.$alias->ehm_code.'</ehm-code>'."\n";
					echo '			<fs-code>'.$alias->fs2004_code.'</fs-code>'."\n";
				echo '		</airport-alias>'."\n";
			}

			if($alias->fsx_code != $alias->ehm_code){
				echo '		<airport-alias>'."\n";
					echo '			<fs-version>FSX</fs-version>'."\n";
					echo '			<ehm-code>'.$alias->ehm_code.'</ehm-code>'."\n";
					echo '			<fs-code>'.$alias->fsx_code.'</fs-code>'."\n";
				echo '		</airport-alias>'."\n";
			}
			/*
			/*
			for x-plane when needed
			if($alias->fs2004_code != $alias->ehm_code){
				echo '		<airport-alias>'."\n";
					echo '			<fs-version>FS9</fs-version>'."\n";
					echo '			<ehm-code>'.$alias->ehm_code.'</ehm-code>'."\n";
					echo '			<fs-code>'.$alias->fs2004_code.'</fs-code>'."\n";
				echo '		</airport-alias>'."\n";
			}
			*/

			}
			echo '	</airport-aliases>' . "\n";
			echo '</data>' . "\n";
		} else {
			echo '<data />' . "\n";
		}
		echo '</response>' . "\n";

		if ($num_rows > 0) {

		}

	}

	function aircraft() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models

		//this displays xml data of aircraft. Timestamp restricts to only those updated since that time

		//get post
		$post_timestamp = $this->security->sanitize_filename($this->input->post('timestamp'));
		//$post_timestamp = $this->input->post('timestamp');

		$valid_timestamp = '0';

		//handle empty submission
		if (is_null($post_timestamp) || $post_timestamp == '') {
			$post_timestamp = '0000-00-00 00:00:00';
		}

		$check_date_day = gmdate('d', strtotime($post_timestamp));
		$check_date_month = gmdate('m', strtotime($post_timestamp));
		$check_date_year = gmdate('Y', strtotime($post_timestamp));

		//check date is valid
		if (is_numeric($check_date_day) && is_numeric($check_date_month) && is_numeric($check_date_year)
			&& checkdate($check_date_month, $check_date_day, $check_date_year)) {
			$valid_timestamp = '1';
		}

		//check that date is not in the future
		if (strtotime($post_timestamp) > time()) {
			//future date!
			//$valid_timestamp = '0';
		}

		//handle return codes
		if ($valid_timestamp != '1') {
			$post_timestamp = '0000-00-00 00:00:00';
		}

		/*
		if($post_timestamp == '0000-00-00 00:00:00'){
			$restrict_val = "WHERE aircraft.enabled = '1'";
		}
		else{
			$restrict_val = "WHERE aircraft.modified > '$post_timestamp'
									AND aircraft.enabled = '1'";
		}
		*/
		$restrict_val = "WHERE aircraft.enabled = '1'";

		//query to get all aircraft Will pull all enabled aircraft, including historical for normal flogger flight
		$query = $this->db->query("	SELECT
											aircraft.id,
											aircraft.name,
											aircraft.clss,
											aircraft.icao_code,
											aircraft.variant,
											aircraft.in_fleet,
											aircraft.division,
											aircraft.pax,
											aircraft.cargo,
											aircraft.cruise_speed,
											aircraft.maximum_climb_rate,
											aircraft.maximum_desc_rate,
											aircraft.modified,
											divisions.tours,
											divisions.primary,
											divisions.events


									FROM aircraft

										LEFT JOIN divisions
										ON divisions.id = aircraft.division

									$restrict_val

										");

		$list = $query->result();
		$num_rows = $query->num_rows();

		$float_date = $post_timestamp;

		$num_rows = 0;

		foreach ($list as $row) {

			if (strtotime($row->modified) > strtotime($float_date)) {
				$num_rows++;
			}

		}

		echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		//root element header
		echo '<response>' . "\n";
		echo '<header>' . "\n";
		echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
		echo '	<post_timestamp>' . $post_timestamp . '</post_timestamp>' . "\n";

		//handle return codes
		if ($valid_timestamp != '1') {
			echo '	<errcode>errTimestamp</errcode>' . "\n";
			echo '	<errmessage>Timestamp supplied is not valid</errmessage>' . "\n";
		} elseif ($num_rows == 0) {
			echo '	<errcode>okNoData</errcode>' . "\n";
			echo '	<errmessage>No aircraft updates found</errmessage>' . "\n";
		} elseif ($num_rows > 0 && $valid_timestamp == '1') {
			echo '	<errcode>okDownload</errcode>' . "\n";
			echo '	<errmessage>Aircraft updates found</errmessage>' . "\n";
		} else {
			//can't occur - unknown error will kill data return
			echo '	<errcode>errUnknown</errcode>' . "\n";
			echo '	<errcode>An unknown error has occurred on the server</errcode>' . "\n";
		}

		echo '</header>' . "\n";

		echo '<data>' . "\n";
		//iterate through all returned aircraft
		if ($num_rows > 0) {
			//root element aircraft-list
			echo '<aircraft-list>' . "\n";
			foreach ($list as $aircraft) {

				echo '	<aircraft>' . "\n";
				echo '		<id>' . $aircraft->id . '</id>' . "\n";
				echo '		<name>' . $aircraft->name . '</name>' . "\n";
				echo '		<icao>' . $aircraft->icao_code . '</icao>' . "\n";

				//handle variant
				if ($aircraft->variant != '') {
					echo '		<variant>' . $aircraft->variant . '</variant>' . "\n";
				} else {
					echo '		<variant />' . "\n";
				}

				//handle division and origin
				if ($aircraft->tours == '1') {
					echo '		<origin>G</origin>' . "\n";
					echo '		<division>0</division>' . "\n";
				} elseif ($aircraft->in_fleet == '1' && $aircraft->primary == '1') {
					echo '		<origin>D</origin>' . "\n";
					echo '		<division>' . $aircraft->division . '</division>' . "\n";
				}
				//elseif($aircraft->primary == '1'){
				//	echo '		<origin>H</origin>'."\n";
				//	echo '		<division>'.$aircraft->division.'</division>'."\n";
				//}
				else {
					echo '		<origin>O</origin>' . "\n";
					echo '		<division>0</division>' . "\n";
				}

				echo '		<level>' . $aircraft->clss . '</level>' . "\n";
				echo '		<pax>' . $aircraft->pax . '</pax>' . "\n";
				echo '		<cargo>' . $aircraft->cargo . '</cargo>' . "\n";

				echo '		<std-speed>' . $aircraft->cruise_speed . '</std-speed>' . "\n";
				echo '		<max-climb-rate>' . $aircraft->maximum_climb_rate . '</max-climb-rate>' . "\n";
				echo '		<max-desc-rate>' . $aircraft->maximum_desc_rate . '</max-desc-rate>' . "\n";

				echo '		<timestamp>' . $aircraft->modified . '</timestamp>' . "\n";
				echo '	</aircraft>' . "\n";
			}

			echo '</aircraft-list>' . "\n";
			echo '</data>' . "\n";
		} else {
			//self closing tag if empty
			echo '<aircraft-list />' . "\n";
			echo '</data>' . "\n";
		}

		echo '</response>' . "\n";

		//close aircraft
	}

	function version() {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models

		//this checks the client flogger version against the latest version

		$version_match = 0;
		$version_compatible = 0;

		//get post
		$post_major = $this->security->sanitize_filename($this->input->post('major'));
		$post_minor = $this->security->sanitize_filename($this->input->post('minor'));
		$post_revision = $this->security->sanitize_filename($this->input->post('revision'));
		$post_build = $this->security->sanitize_filename($this->input->post('build'));

		//test data
		/*
		$post_major = '4';
		$post_minor = '1';
		$post_revision = '';
		$post_build = '';
		*/

		//query to get the current flogger version
		$query = $this->db->query("	SELECT
											config_codesets.code_id,
											config_codesets.code_description

									FROM config_codesets

									WHERE config_codesets.type = 'flogger'

										");

		$list = $query->result();
		$num_rows = $query->num_rows();

		//get the data

		$major = '-';
		$minor = '-';
		$revision = '-';
		$build = '-';
		$url = '';

		foreach ($list as $row) {
			switch ($row->code_id) {

				case 'major':
					$major = $row->code_description;
					break;

				case 'minor':
					$minor = $row->code_description;
					break;

				case 'revision':
					$revision = $row->code_description;
					break;

				case 'build':
					$build = $row->code_description;
					break;

				case 'url':
					$url = $row->code_description;
					break;

				default:
					break;

			}

		}

		if (
			($major <= $post_major && $minor <= $post_minor && $revision <= $post_revision && $build <= $post_build) //version match or all parts larger
			|| ($major <= $post_major && $minor <= $post_minor && $revision < $post_revision) // revision increment
			|| ($major <= $post_major && $minor < $post_minor) //minor increment
			|| ($major < $post_major) //major increment
		) {
			//match
			$version_compatible = 1;
		}

		if ($major == $post_major && $minor == $post_minor && $revision == $post_revision && $build == $post_build) {
			//match
			$version_match = 1;
		}

		echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		//root element header
		echo '<response>' . "\n";
		echo '<header>' . "\n";

		//handle return codes
		if ($version_match == '1') {
			echo '	<errcode>okCompatible</errcode>' . "\n";
			echo '	<errcode>Client is the latest version</errcode>' . "\n";

		} elseif ($version_compatible == 1) {
			echo '	<errcode>okCompatible</errcode>' . "\n";
			echo '	<errcode>Client is compatible with the latest version</errcode>' . "\n";
		} else {
			echo '	<errcode>errCompatible</errcode>' . "\n";
			echo '	<errcode>There is a newer client version</errcode>' . "\n";
		}
		echo '</header>' . "\n";
		echo '<data>' . "\n";
		echo '<version>' . "\n";
		echo '	<major>' . $major . '</major>' . "\n";
		echo '	<minor>' . $minor . '</minor>' . "\n";
		echo '	<revision>' . $revision . '</revision>' . "\n";
		echo '	<build>' . $build . '</build>' . "\n";

		echo '	<download>' . $url . '</download>' . "\n";

		echo '	<clientmajor>' . $post_major . '</clientmajor>' . "\n";
		echo '	<clientminor>' . $post_minor . '</clientminor>' . "\n";
		echo '	<clientrevision>' . $post_revision . '</clientrevision>' . "\n";
		echo '	<clientbuild>' . $post_build . '</clientbuild>' . "\n";
		echo '</version>' . "\n";
		echo '</data>' . "\n";
		echo '</response>' . "\n";

		//close version
	}

	function flights($type = 'assigned') {
		//grab global initialisation
		include_once($this->config->item('full_base_path') . 'application/controllers/init/initialise.php');
		//load libraries and models

		//this checks the client flogger version against the latest version

		$valid_timestamp = 0;

		//get post
		$post_timestamp = $this->security->sanitize_filename($this->input->post('timestamp'));
		$post_username = $this->security->sanitize_filename($this->input->post('username'));

		//if(empty($post_username)){
		//$post_username = '1997';
		//}

		//$post_timestamp = '2011-03-19 16:48:56';

		//if no data supplied ot not a valid date, set zero
		if ($post_timestamp == '') {
			$post_timestamp = '0000-00-00 00:00:00';
		}

		$check_date_day = gmdate('d', strtotime($post_timestamp));
		$check_date_month = gmdate('m', strtotime($post_timestamp));
		$check_date_year = gmdate('Y', strtotime($post_timestamp));

		//check date is valid
		if (is_numeric($check_date_day) && is_numeric($check_date_month) && is_numeric($check_date_year)
			&& checkdate($check_date_month, $check_date_day, $check_date_year)) {
			$valid_timestamp = '1';
		} else {
			$post_timestamp = '000-00-00 00:00:00';
		}

		if ($type == 'missions') {
			//always return all current missions

			$today = gmdate('Y-m-d', time());

			//grab all missions
			$query = $this->db->query("	SELECT
										mission_index.id as id,
										mission_index.title as title,
										mission_index.description as description,
										mission_index.start_date as start_date,
										mission_index.end_date as end_date,
										mission_index.dep_weather as dep_weather,
										mission_index.arr_weather as arr_weather,
										mission_index.division as division,
										mission_index.class as clss,
										mission_index.start_icao as start_icao,
										mission_index.end_icao as end_icao,
										aircraft.name as aircraft

									FROM mission_index

										LEFT JOIN aircraft
										on aircraft.id = mission_index.aircraft_id

									WHERE mission_index.start_date <= '$today'
									AND mission_index.end_date >= '$today'
									AND mission_index.aircraft_id  IS NOT NULL
									AND mission_index.start_icao  IS NOT NULL
									AND mission_index.end_icao  IS NOT NULL

									ORDER BY mission_index.class, mission_index.division, mission_index.id

										");

			$missions = $query->result();
			$num_rows = $query->num_rows();

			if ($num_rows > 0) {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<post_timestamp>' . $post_timestamp . '</post_timestamp>' . "\n";
				echo '	<errcode>okDownload</errcode>' . "\n";
				echo '	<errmessage>Flight updates found</errmessage>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list timestamp="' . $data['gmt_mysql_datetime'] . '">' . "\n";
				foreach ($missions as $flight) {

					//set values
					$group = '2';

					echo '	<flight>' . "\n";
					echo '		<timetable-id>' . $flight->id . '</timetable-id>' . "\n";
					echo '		<number />' . "\n";
					echo '		<dep-ap>' . $flight->start_icao . '</dep-ap>' . "\n";
					echo '		<arr-ap>' . $flight->end_icao . '</arr-ap>' . "\n";
					echo '		<dep-time />' . "\n";
					echo '		<arr-time />' . "\n";
					echo '		<days />' . "\n";
					echo '		<season-month-start />' . "\n";
					echo '		<season-month-end />' . "\n";
					echo '		<clss>' . $flight->clss . '</clss>' . "\n";
					echo '		<division>' . $flight->division . '</division>' . "\n";
					echo '		<group>' . $group . '</group>' . "\n";
					echo '		<aircraft_id />' . "\n";
					echo '	</flight>' . "\n";
				}
				echo '</flight-list>' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";

			} //otherwise no data to return
			else {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				//root element header
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<errcode>okNoData</errcode>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list />' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";
			}

		} elseif ($type == 'timetable') {
			//query to get the current timetable timestamp
			$query = $this->db->query("	SELECT
												config_codesets.code_id,
												config_codesets.code_description

										FROM config_codesets

										WHERE config_codesets.type = 'timetable'
										AND config_codesets.code_id = 'last_update'

										LIMIT 1

											");

			$list = $query->result_array();
			$num_rows = $query->num_rows();

			$last_updated = '0000-00-00 00:00:00';

			//if we got a hit from the db, set the last updated value
			if ($num_rows > 0) {
				$last_updated = $list[0]['code_description'];
			}

			//if updates since last pull, get timetable data since the timestamp

			//net to output data in 'flight groups' where:
			/*
			1 => scheduled (from timetable)
			2 => mission (split out of timetables - in mission index)
			3 => tour (split out of timetables - now in tour legs) - however cannot be flown out of assigned flights (or sequence problems). Return only tours that pilot has
			4 => DGF (currently not implemented, but will be reimplemented in separate table)
			5 => Event (not sure of the point of this one as only latest propilot flight will be downloaded.
			6 => Assigned flights (new)
			*/

			//query database for all scheduled (timetable flights) only if timstamp is newer on server than client (or we have a 0 value from post

			if (strtotime($last_updated) > strtotime($post_timestamp)
				|| $post_timestamp == '0000-00-00 00:00:00') {

				//query to get the timetable data
				$query = $this->db->query("	SELECT
													timetable.id,
													timetable.flightnumber,
													timetable.dep_airport,
													timetable.arr_airport,
													timetable.dep_time,
													timetable.arr_time,
													timetable.hub,
													timetable.sun,
													timetable.mon,
													timetable.tue,
													timetable.wed,
													timetable.thu,
													timetable.fri,
													timetable.sat,
													timetable.season_month_start,
													timetable.season_month_end,
													timetable.class AS clss,
													timetable.division,
													timetable.active

											FROM timetable

											WHERE timetable.active = '1'
											AND timetable.division != '5'
											AND timetable.division != '6'
											AND timetable.division != '7'

												");

				$list = $query->result();
				$num_rows = $query->num_rows();

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<post_timestamp>' . $post_timestamp . '</post_timestamp>' . "\n";
				echo '	<errcode>okDownload</errcode>' . "\n";
				echo '	<errmessage>Flight updates found</errmessage>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list timestamp="' . $data['gmt_mysql_datetime'] . '">' . "\n";
				foreach ($list as $flight) {

					//set values
					$group = '1';

					$days = '';

					//combine days
					if ($flight->mon == '1') {
						$days .= '1';
					} else {
						$days .= '-';
					}
					if ($flight->tue == '1') {
						$days .= '2';
					} else {
						$days .= '-';
					}
					if ($flight->wed == '1') {
						$days .= '3';
					} else {
						$days .= '-';
					}
					if ($flight->mon == '1') {
						$days .= '4';
					} else {
						$days .= '-';
					}
					if ($flight->mon == '1') {
						$days .= '5';
					} else {
						$days .= '-';
					}
					if ($flight->mon == '1') {
						$days .= '6';
					} else {
						$days .= '-';
					}
					if ($flight->mon == '1') {
						$days .= '7';
					} else {
						$days .= '-';
					}

					//use the row id. Preceed with T to know that this is from the main timetable
					//$flight_number = 'T'.str_pad($flight->id, 5, "0", STR_PAD_LEFT);
					$flightnumber = $flight->flightnumber;

					echo '	<flight>' . "\n";
					echo '		<timetable-id>' . $flight->id . '</timetable-id>' . "\n";
					echo '		<number>' . $flightnumber . '</number>' . "\n";
					echo '		<dep-ap>' . $flight->dep_airport . '</dep-ap>' . "\n";
					echo '		<arr-ap>' . $flight->arr_airport . '</arr-ap>' . "\n";
					echo '		<dep-time>' . $flight->dep_time . '</dep-time>' . "\n";
					echo '		<arr-time>' . $flight->arr_time . '</arr-time>' . "\n";
					echo '		<days>' . $days . '</days>' . "\n";
					echo '		<season-month-start>' . $flight->season_month_start . '</season-month-start>' . "\n";
					echo '		<season-month-end>' . $flight->season_month_end . '</season-month-end>' . "\n";
					echo '		<clss>' . $flight->clss . '</clss>' . "\n";
					echo '		<division>' . $flight->division . '</division>' . "\n";
					echo '		<group>' . $group . '</group>' . "\n";
					echo '		<aircraft_id />' . "\n";
					echo '	</flight>' . "\n";
				}
				echo '</flight-list>' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";

			} //otherwise no data to return
			else {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				//root element header
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<errcode>okNoData</errcode>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list />' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";
			}
			//close timetable
		} elseif ($type == 'assigned') {

			//verify user exists and pull data
			$query = $this->db->query("	SELECT
													pilots.id,
													pilots.username

										FROM pilots

										WHERE pilots.username = '$post_username'

										LIMIT 1

									");

			$pilot_data = $query->result_array();
			$pilot_rows = $query->num_rows();

			$post_user_id = '';

			if ($pilot_rows > 0) {

				$post_user_id = $pilot_data['0']['id'];

				//query to get the assigned data
				$query = $this->db->query("	SELECT
													pirep_assigned.id,
													pirep_assigned.start_icao,
													pirep_assigned.end_icao,
													pirep_assigned.gcd,
													pirep_assigned.aircraft_id,
													pirep_assigned.passengers,
													pirep_assigned.cargo,
													pirep_assigned.group_id,
													pirep_assigned.tour_id,
													pirep_assigned.tour_leg_id,
													pirep_assigned.event_id,
													pirep_assigned.event_leg_id,
													pirep_assigned.mission_id,
													pirep_assigned.fs_version,
													pirep_assigned.group_order,
													pirep_assigned.created,
													pirep_assigned.award_completion,
													pirep_assigned.award_id,
													tour_index.name as tour_name,
													mission_index.title as mission_name


											FROM pirep_assigned

												LEFT JOIN tour_index
												ON tour_index.id = pirep_assigned.tour_id

												LEFT JOIN mission_index
												ON mission_index.id = pirep_assigned.mission_id

											WHERE pirep_assigned.user_id = '$post_user_id'
											AND (pirep_assigned.event_id IS NULL OR pirep_assigned.event_id = '0')

											ORDER BY pirep_assigned.group_id, pirep_assigned.group_order, pirep_assigned.created

												");

				$list = $query->result();
				$num_rows = $query->num_rows();

			} else {
				$num_rows = 0;
				$list = array();
			}

			$tour_val = 0;
			$route_val = 0;
			$flight_val = 0;

			$i = 0;

			$tour_data = array();
			$route_data = array();
			$flight_data = array();

			//separate out
			foreach ($list as $row) {

				if ($row->tour_id != '') {
					//all the tours
					if ($tour_val === 0 || $row->group_order < $tour_data['group_order']) {
						$tour_val = 1;
						$tour_data['id'] = $row->id;
						$tour_data['start_icao'] = $row->start_icao;
						$tour_data['end_icao'] = $row->end_icao;
						$tour_data['gcd'] = $row->gcd;
						$tour_data['aircraft_id'] = $row->aircraft_id;
						$tour_data['passengers'] = $row->passengers;
						$tour_data['cargo'] = $row->cargo;
						$tour_data['group_id'] = $row->group_id;
						$tour_data['group_name'] = $row->tour_name;
						$tour_data['tour_id'] = $row->tour_id;
						$tour_data['tour_leg_id'] = $row->tour_leg_id;
						$tour_data['event_id'] = $row->event_id;
						$tour_data['event_leg_id'] = $row->event_leg_id;
						$tour_data['mission_id'] = $row->mission_id;
						$tour_data['fs_version'] = $row->fs_version;
						$tour_data['group_order'] = $row->group_order;
						$tour_data['created'] = $row->created;
						$tour_data['award_completion'] = $row->award_completion;
						$tour_data['award_id'] = $row->award_id;
					}

				} elseif ($row->group_id != '') {
					//routes
					if ($route_val === 0 || !array_key_exists($row->group_id, $route_data) || ($row->group_order < $route_data[$row->group_id]['group_order'])) {
						$route_val = 1;
						$route_data[$row->group_id]['id'] = $row->id;
						$route_data[$row->group_id]['start_icao'] = $row->start_icao;
						$route_data[$row->group_id]['end_icao'] = $row->end_icao;
						$route_data[$row->group_id]['gcd'] = $row->gcd;
						$route_data[$row->group_id]['aircraft_id'] = $row->aircraft_id;
						$route_data[$row->group_id]['passengers'] = $row->passengers;
						$route_data[$row->group_id]['cargo'] = $row->cargo;
						$route_data[$row->group_id]['group_id'] = $row->group_id;
						$route_data[$row->group_id]['group_name'] = 'Route: ' . $row->start_icao . '-' . $row->end_icao;
						$route_data[$row->group_id]['tour_id'] = $row->tour_id;
						$route_data[$row->group_id]['tour_leg_id'] = $row->tour_leg_id;
						$route_data[$row->group_id]['event_id'] = $row->event_id;
						$route_data[$row->group_id]['event_leg_id'] = $row->event_leg_id;
						$route_data[$row->group_id]['mission_id'] = $row->mission_id;
						$route_data[$row->group_id]['fs_version'] = $row->fs_version;
						$route_data[$row->group_id]['group_order'] = $row->group_order;
						$route_data[$row->group_id]['created'] = $row->created;
						$route_data[$row->group_id]['award_completion'] = $row->award_completion;
						$route_data[$row->group_id]['award_id'] = $row->award_id;
					} else {
						$route_data[$row->group_id]['group_name'] .= '-' . $row->end_icao;
					}

				} else {
					$flight_val = 1;
					//normal assigned flights
					$flight_data[$i]['id'] = $row->id;
					$flight_data[$i]['aircraft_id'] = $row->aircraft_id;
					$flight_data[$i]['start_icao'] = $row->start_icao;
					$flight_data[$i]['end_icao'] = $row->end_icao;
					$flight_data[$i]['passengers'] = $row->passengers;
					$flight_data[$i]['cargo'] = $row->cargo;
					$flight_data[$i]['group_id'] = $row->group_id;
					$flight_data[$i]['group_name'] = $row->mission_name;
					$flight_data[$i]['tour_id'] = $row->tour_id;
					$flight_data[$i]['tour_leg_id'] = $row->tour_leg_id;
					$flight_data[$i]['event_id'] = $row->event_id;
					$flight_data[$i]['event_leg_id'] = $row->event_leg_id;
					$flight_data[$i]['mission_id'] = $row->mission_id;
					$flight_data[$i]['fs_version'] = $row->fs_version;
					$flight_data[$i]['group_order'] = $row->group_order;
					$flight_data[$i]['created'] = $row->created;
					$flight_data[$i]['award_completion'] = $row->award_completion;
					$flight_data[$i]['award_id'] = $row->award_id;
					$i++;
				}

			}

			if ($num_rows > 0) {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<post_user_id>' . $post_user_id . '</post_user_id>' . "\n";
				echo '	<errcode>okDownload</errcode>' . "\n";
				echo '	<errmessage>Flight updates found</errmessage>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list timestamp="' . $data['gmt_mysql_datetime'] . '">' . "\n";
				//tours

				if ($tour_val == '1') {
					echo '	<flight>' . "\n";
					echo '		<assigned-id>' . $tour_data['id'] . '</assigned-id>' . "\n";
					echo '		<aircraft-id>' . $tour_data['aircraft_id'] . '</aircraft-id>' . "\n";
					echo '		<dep-ap>' . $tour_data['start_icao'] . '</dep-ap>' . "\n";
					echo '		<arr-ap>' . $tour_data['end_icao'] . '</arr-ap>' . "\n";
					echo '		<passengers>' . $tour_data['passengers'] . '</passengers>' . "\n";
					echo '		<cargo>' . $tour_data['cargo'] . '</cargo>' . "\n";
					echo '		<group-id>' . $tour_data['group_id'] . '</group-id>' . "\n";
					echo '		<group-order>' . $tour_data['id'] . '</group-order>' . "\n";
					echo '		<group-name>' . $tour_data['group_name'] . '</group-name>' . "\n";
					echo '	</flight>' . "\n";

				}

				if ($route_val == '1') {

					foreach ($route_data as $row) {

						echo '	<flight>' . "\n";
						echo '		<assigned-id>' . $row['id'] . '</assigned-id>' . "\n";
						echo '		<aircraft-id>' . $row['aircraft_id'] . '</aircraft-id>' . "\n";
						echo '		<dep-ap>' . $row['start_icao'] . '</dep-ap>' . "\n";
						echo '		<arr-ap>' . $row['end_icao'] . '</arr-ap>' . "\n";
						echo '		<passengers>' . $row['passengers'] . '</passengers>' . "\n";
						echo '		<cargo>' . $row['cargo'] . '</cargo>' . "\n";
						echo '		<group-id>' . $row['group_id'] . '</group-id>' . "\n";
						echo '		<group-order>' . $row['id'] . '</group-order>' . "\n";
						echo '		<group-name>' . $row['group_name'] . '</group-name>' . "\n";
						echo '	</flight>' . "\n";

					}

				}

				if ($flight_val == '1') {

					foreach ($flight_data as $row) {

						echo '	<flight>' . "\n";
						echo '		<assigned-id>' . $row['id'] . '</assigned-id>' . "\n";
						echo '		<aircraft-id>' . $row['aircraft_id'] . '</aircraft-id>' . "\n";
						echo '		<dep-ap>' . $row['start_icao'] . '</dep-ap>' . "\n";
						echo '		<arr-ap>' . $row['end_icao'] . '</arr-ap>' . "\n";
						echo '		<passengers>' . $row['passengers'] . '</passengers>' . "\n";
						echo '		<cargo>' . $row['cargo'] . '</cargo>' . "\n";
						echo '		<group-id>' . $row['group_id'] . '</group-id>' . "\n";
						echo '		<group-order>' . $row['id'] . '</group-order>' . "\n";
						echo '		<group-name>' . $row['group_name'] . '</group-name>' . "\n";
						echo '	</flight>' . "\n";

					}

				}

				echo '</flight-list>' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";

			} //otherwise no data to return
			elseif ($post_user_id == '') {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				//root element header
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<errcode>errNoUser</errcode>' . "\n";
				echo '	<errmessage>No valid user supplied</errmessage>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list />' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";
			} elseif ($pilot_rows > 0) {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				//root element header
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<errcode>okNoData</errcode>' . "\n";
				echo '	<errmessage>No assigned flights found</errmessage>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list />' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";
			} else {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				//root element header
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<errcode>errUnknown</errcode>' . "\n";
				echo '	<errmessage>Unknown error</errmessage>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list />' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";
			}
			//close assigned
		} elseif ($type == 'propilot') {

			//$post_username = '2097';

			//verify user exists and pull data
			$query = $this->db->query("	SELECT
													pilots.id,
													pilots.username

										FROM pilots

										WHERE pilots.username = '$post_username'

										LIMIT 1

									");

			$pilot_data = $query->result_array();
			$pilot_rows = $query->num_rows();

			$post_user_id = '';

			if ($pilot_rows > 0) {

				$post_user_id = $pilot_data['0']['id'];

				//query to get propilot locked flight
				$query = $this->db->query("	SELECT
													propilot_aircraft.id as tail_id,
													propilot_aircraft.aircraft_id as aircraft_id,
													propilot_aircraft.reserved_by as user_id,
													aircraft.name as aircraft,
													propilot_aircraft.tail_id as tail_number,
													propilot_aircraft.pax as passengers,
													propilot_aircraft.cargo as cargo,
													propilot_aircraft.location as start_icao,
													propilot_aircraft.destination as end_icao,
													propilot_aircraft.gcd as gcd,
													propilot_aircraft.last_flown as last_flown,
													dep_icao.name as dep_name,
													arr_icao.name as arr_name

											FROM propilot_aircraft

												LEFT JOIN aircraft
												ON aircraft.id = propilot_aircraft.aircraft_id

												LEFT JOIN airports_data as dep_icao
												ON dep_icao.ICAO = propilot_aircraft.location

												LEFT JOIN airports_data as arr_icao
												ON arr_icao.ICAO = propilot_aircraft.destination

												LEFT JOIN pilots
												ON propilot_aircraft.reserved_by = pilots.id

											WHERE propilot_aircraft.reserved_by = '$post_user_id'
											AND (propilot_aircraft.reserved IS NOT NULL
												AND propilot_aircraft.reserved != ''
												AND propilot_aircraft.reserved != '0000-00-00 00:00:00'
												AND propilot_aircraft.reserved >= '$pp_compare_date')
											AND propilot_aircraft.state_id = '1'
											AND pilots.pp_location = propilot_aircraft.location

											ORDER BY propilot_aircraft.reserved

											LIMIT 1

												");

				$propilot_flights = $query->result_array();
				$propilot_num = $query->num_rows();

				if ($propilot_num > 0) {

					$assigned_id = '0';
					$aircraft_id = $propilot_flights['0']['aircraft_id'];
					$tail_id = $propilot_flights['0']['tail_id'];
					$tail_number = $propilot_flights['0']['tail_number'];
					$start_icao = $propilot_flights['0']['start_icao'];
					$end_icao = $propilot_flights['0']['end_icao'];
					$passengers = $propilot_flights['0']['passengers'];
					$cargo = $propilot_flights['0']['cargo'];
					$group_id = '';
					$group_order = '';
					$group_name = '';

				} else {
					//if no locked flight exists, query to get assigned propilot flight
					//query to get the propilot event data
					$query = $this->db->query("	SELECT
															pirep_assigned.id,
															pirep_assigned.start_icao,
															pirep_assigned.end_icao,
															pirep_assigned.gcd,
															pirep_assigned.aircraft_id,
															pirep_assigned.passengers,
															pirep_assigned.cargo,
															pirep_assigned.group_id,
															pirep_assigned.tour_id,
															pirep_assigned.tour_leg_id,
															pirep_assigned.event_id,
															pirep_assigned.event_leg_id,
															pirep_assigned.mission_id,
															pirep_assigned.fs_version,
															pirep_assigned.group_order,
															pirep_assigned.created,
															pirep_assigned.award_completion,
															pirep_assigned.award_id,
															tour_index.name as tour_name,
															mission_index.title as mission_name,
															propilot_event_legs.start_date as start_date,
															propilot_event_legs.end_date as end_date,
															pilots.pp_location


													FROM pirep_assigned

														LEFT JOIN tour_index
														ON tour_index.id = pirep_assigned.tour_id

														LEFT JOIN mission_index
														ON mission_index.id = pirep_assigned.mission_id

														LEFT JOIN propilot_event_legs
														ON propilot_event_legs.id = pirep_assigned.event_leg_id

														LEFT JOIN pilots
														ON pirep_assigned.user_id = pilots.id

													WHERE pirep_assigned.user_id = '$post_user_id'
													AND pirep_assigned.event_id IS NOT NULL
													AND pirep_assigned.event_id != '0'

													ORDER BY pirep_assigned.event_leg_id, pirep_assigned.group_id, pirep_assigned.group_order ASC, pirep_assigned.created

													LIMIT 1

														");

					$propilot_flights = $query->result_array();
					$propilot_num = $query->num_rows();

					if ($propilot_num > 0) {

						//$current_time = time();
						$current_time = strtotime(gmdate('Y-m-d'));

						//handle case where event dates have passed
						if (strtotime($propilot_flights['0']['start_date']) <= $current_time
							&& strtotime($propilot_flights['0']['end_date']) >= $current_time
							&& $propilot_flights['0']['start_icao'] == $propilot_flights['0']['pp_location']
						) {

							$assigned_id = $propilot_flights['0']['id'];
							$aircraft_id = $propilot_flights['0']['aircraft_id'];
							$tail_id = '';
							$tail_number = 'E-VENT';
							$start_icao = $propilot_flights['0']['start_icao'];
							$end_icao = $propilot_flights['0']['end_icao'];
							$passengers = $propilot_flights['0']['passengers'];
							$cargo = $propilot_flights['0']['cargo'];
							$group_id = '';
							$group_order = '';
							$group_name = '';
						} else {
							$propilot_num = 0;
						}

					} else {
						$propilot_num = 0;
					}
				}

				if ($propilot_num > 0) {

					echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
					echo '<response>' . "\n";
					echo '<header>' . "\n";
					echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
					echo '	<post_user_id>' . $post_user_id . '</post_user_id>' . "\n";
					echo '	<errcode>okDownload</errcode>' . "\n";
					echo '	<errmessage>Flight updates found</errmessage>' . "\n";
					echo '</header>' . "\n";
					echo '<data>' . "\n";
					echo '<flight-list timestamp="' . $data['gmt_mysql_datetime'] . '">' . "\n";

					if ($propilot_num > 0) {

						echo '	<flight>' . "\n";
						echo '		<assigned-id>' . $assigned_id . '</assigned-id>' . "\n";
						echo '		<aircraft-id>' . $aircraft_id . '</aircraft-id>' . "\n";
						echo '		<tail-id>' . $tail_id . '</tail-id>' . "\n";
						echo '		<tail-number>' . $tail_number . '</tail-number>' . "\n";
						echo '		<dep-ap>' . $start_icao . '</dep-ap>' . "\n";
						echo '		<arr-ap>' . $end_icao . '</arr-ap>' . "\n";
						echo '		<passengers>' . $passengers . '</passengers>' . "\n";
						echo '		<cargo>' . $cargo . '</cargo>' . "\n";
						echo '		<group-id>' . $group_id . '</group-id>' . "\n";
						echo '		<group-order>' . $group_order . '</group-order>' . "\n";
						echo '		<group-name>' . $group_name . '</group-name>' . "\n";
						echo '	</flight>' . "\n";

					}

					echo '</flight-list>' . "\n";
					echo '</data>' . "\n";
					echo '</response>' . "\n";

				} //otherwise no data to return
				elseif ($post_user_id == '') {

					echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
					//root element header
					echo '<response>' . "\n";
					echo '<header>' . "\n";
					echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
					echo '	<errcode>errNoUser</errcode>' . "\n";
					echo '	<errmessage>No valid user supplied</errmessage>' . "\n";
					echo '</header>' . "\n";
					echo '<data>' . "\n";
					echo '<flight-list />' . "\n";
					echo '</data>' . "\n";
					echo '</response>' . "\n";
				} elseif ($pilot_rows > 0) {

					echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
					//root element header
					echo '<response>' . "\n";
					echo '<header>' . "\n";
					echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
					echo '	<errcode>okNoData</errcode>' . "\n";
					echo '	<errmessage>No assigned flights found</errmessage>' . "\n";
					echo '</header>' . "\n";
					echo '<data>' . "\n";
					echo '<flight-list />' . "\n";
					echo '</data>' . "\n";
					echo '</response>' . "\n";
				} else {

					echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
					//root element header
					echo '<response>' . "\n";
					echo '<header>' . "\n";
					echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
					echo '	<errcode>errUnknown</errcode>' . "\n";
					echo '	<errmessage>Unknown error</errmessage>' . "\n";
					echo '</header>' . "\n";
					echo '<data>' . "\n";
					echo '<flight-list />' . "\n";
					echo '</data>' . "\n";
					echo '</response>' . "\n";
				}
				//close if pilot_rows
			} else {

				echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				//root element header
				echo '<response>' . "\n";
				echo '<header>' . "\n";
				echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
				echo '	<errcode>errNoUser</errcode>' . "\n";
				echo '	<errmessage>No valid user supplied</errmessage>' . "\n";
				echo '</header>' . "\n";
				echo '<data>' . "\n";
				echo '<flight-list />' . "\n";
				echo '</data>' . "\n";
				echo '</response>' . "\n";

			}
			//close propilot
		} else {
			echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			//root element header
			echo '<response>' . "\n";
			echo '<header>' . "\n";
			echo '	<timestamp>' . $data['gmt_mysql_datetime'] . '</timestamp>' . "\n";
			echo '	<errcode>invalidUrl</errcode>' . "\n";
			echo '	<errmessage>Not a valid url</errmessage>' . "\n";
			echo '</header>' . "\n";
			echo '</response>' . "\n";
		}

		//close flights
	}

}

/* End of file */