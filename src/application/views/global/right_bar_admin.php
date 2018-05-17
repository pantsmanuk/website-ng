<div class="rightbar">
    <center>
		<?php
		if ($this->session->userdata('logged_in') != '1') {
			echo '<a href="' . $base_url . 'join/"><img src="' . $tmpl_image_url . 'home/join.jpg" alt="Join" /></a><br />';
		} else {
			echo '<br />';
			$pips_val = '';
			if ($this->session->userdata('pips') != '') {
				$pips_val = '_' . $this->session->userdata('pips');
			}

			echo '<img src="' . $image_url . 'ranks/' . $this->session->userdata('rank_id') . $pips_val . '.png" alt="' . $this->session->userdata('rank_long') . '" />';
			echo '<br />';
			echo $this->session->userdata('rank_long');
		}
		?>
    </center>
    <br/>
    <div style="margin-left: 5px; margin-right: 5px;">


		<?php

		// ADMIN MENU *************************************************************************************

		//index
		echo '
	<font size="2" color="#414b59"><b>Stats</b></font>
	<br /><a href="' . $base_url . 'admincp/">Admin Index</a>
	<hr />
	';

		//pilots
		echo '    
	<font size="2" color="#414b59"><b>Pilots</b></font>
	<br /><a href="' . $base_url . 'acp_pilots/manage">Manage Pilots</a>
    <br /><s>Hub Transfers</s>
	<br /><a href="' . $base_url . 'acp_pireps/validate">Validate Pireps</a>
	<hr />
	';

		//operations
		echo '  
	<font size="2" color="#414b59"><b>Operations</b></font>
	<br /><a href="' . $base_url . 'acp_awards/manage">Manage Awards</a>
	<br /><a href="' . $base_url . 'acp_divisions/manage">Manage Divisions</a>
    <br /><a href="' . $base_url . 'acp_fleet/manage">Manage Fleet</a>
    <br /><a href="' . $base_url . 'acp_missions/manage">Manage Missions</a>
	<br /><a href="' . $base_url . 'acp_tours/manage">Manage Tours</a>
	<br /><a href="' . $base_url . 'acp_timetables/manage">Manage Timetables</a>
	<hr />
	
	';

		//propilot
		echo '   
	<font size="2" color="#414b59"><b>Propilot</b></font>
	<br /><a href="' . $base_url . 'acp_propilot/aircraft_manage">Manage Aircraft</a>
	<br /><a href="' . $base_url . 'acp_propilot/event_manage">Manage Events</a>
    
	<hr />
	';

		//communication
		echo '    
	<font size="2" color="#414b59"><b>Communication</b></font>
    <br /><a href="' . $base_url . 'acp_news/manage">News Feed</a>
    <br /><a href="' . $base_url . 'acp_feature/manage">Featured</a>
	<br />Bulk Email
	';

		?>

    </div>

</div>

<br/>

<?php

if (isset($page_title) && $page_title != '') {
	echo '	<div class="divblue" style="height: 25px;">
			<div style="margin-right: 130px">
			<font size="4"><b>' . $page_title . '</b></font>
			</div>
			</div>
';
} else {
	echo '	<div class="divblue">
			<div style="margin-right: 130px">
			<div id="jspilotnews"> </div>
			</div>
			</div>
';
}
?>

<br/>