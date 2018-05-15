<div class="rightbar">
    <center>
    <?php
    if($this->session->userdata('logged_in') != '1'){
    	echo '<a href="'.$base_url.'join/"><img src="'.$tmpl_image_url.'home/join.jpg" alt="Join" /></a><br />';
    }
    else{
		echo '<br />';
	
		$pips_val = '';
		if($this->session->userdata('pips') != ''){
			$pips_val = '_'.$this->session->userdata('pips');
		}
	
    	echo '<img src="'.$image_url.'ranks/'.$this->session->userdata('rank_id').$pips_val.'.png" alt="'.$this->session->userdata('rank_long').'" />';
		echo '<br />';
		echo $this->session->userdata('rank_long');
    }
	?>
    </center>
    
    
    <br />
    <div style="margin-left: 5px; margin-right: 5px;">
	<span class="barhead">Quick Links</span>
    <br /><a href="<?php echo $ops_manual_link; ?>">Operations Manual</a>
    <br /><a href="<?php echo $base_url_minimal; ?>assets/files/manuals/ehm_online_manual.pdf">Online Flying Manual</a>
    <br /><a href="<?php echo $base_url_minimal; ?>assets/files/manuals/NG_Website_User_Guide.pdf">NG Website Manual</a>
    <br /><a href="<?php echo $base_url; ?>information/latest_flights/">Latest flights</a>
    <br /><a href="https://www.fly-euroharmony.com/forum/index.php">EHM Forum</a>
    <br /><a href="https://www.fly-euroharmony.com/forum/index.php?action=unreadreplies">EHM Forum Latest</a>
    <hr />
    <span class="barhead">Software</span>
    <br /><a href="<?php echo $flogger_latest; ?>">Flight Logger <?php echo $flogger_version; ?></a>
    <br /><a href="http://www.ivao.aero/softdev/">IVAP</a>
    <br /><a href="http://squawkbox.ca/news/">Squawkbox</a>
    <br /><a href="http://www.schiratti.com/files/dowson/FSUIPC.zip?timestamp=180204">FSUIPC</a>
    <br /><a href="http://www.tosi-online.de/XPUIPC/XPUIPC.html">XPUIPC</a>
    
    <hr /> 
    </div>
    
    <?php
    
    if(isset($home_members) && is_array($home_members)){
    	//output the stats for the members
    	$total_members = count($home_members);
    	$active_members = 0;
    	
    	foreach($home_members as $row){
    		//increment for active pilots
    		if(strtotime($row->lastflight) >= strtotime($active_compare_datetime)){
    			$active_members++;
    		}
    	}
    	
    	//today
    	$day_compare_date = gmdate('Y-m-d',strtotime('-1 day'));
    	
    	//week
    	$week_compare_date = gmdate('Y-m-d',strtotime('-1 week'));
    	
    	$month_flights = count($home_flights);
    	$month_pax = 0;
    	$month_cargo = 0;
    	$week_flights = 0;
    	$week_pax = 0;
    	$week_cargo = 0;
    	$day_flights = 0;
    	$day_pax = 0;
    	$day_cargo = 0;
    	
    	foreach($home_flights as $row){
    	
    		//pax and cargo month
    		$month_pax += $row->passengers;
    		$month_cargo += $row->cargo;
    	
    		//increment for week pireps
    		if(strtotime($row->submitdate) >= strtotime($week_compare_date)){
    			$week_flights++;
    			$week_pax += $row->passengers;
    			$week_cargo += $row->cargo;
    		}
    		
    		//increment for day pireps
    		if(strtotime($row->submitdate) >= strtotime($day_compare_date)){
    			$day_flights++;
    			$day_pax += $row->passengers;
    			$day_cargo += $row->cargo;
    		}
    		
    	}
    	
    	
    	//echo '<br />';
    	echo '<div style="margin-left: 5px; margin-right: 5px;">';
		echo '<span class="barhead"><b>Quick Stats</b></span>';
		echo '<table width="100%" border="0" cellpadding="1" cellspacing="0">';
		echo '<tr><td align="right">Total pilots:</td><td align="left">'.$total_members.'</td></tr>';
		echo '<tr><td align="right">Active pilots:</td><td align="left">'.$active_members.'</td></tr>';
		echo '<tr><td colspan="2"><hr /><span class="barsub">'.gmdate('F',time()).'</span></td></tr>';
		echo '<tr><td align="right">Flights:</td><td align="left">'.$month_flights.'</td></tr>';
		echo '<tr><td align="right">Pax:</td><td align="left">'.number_format($month_pax, 0).'</td></tr>';
		echo '<tr><td align="right">Cargo:</td><td align="left">'.number_format($this->format_fns->lbs_tonnes($month_cargo), 0).' t</td></tr>';
		echo '<tr><td colspan="2"><hr /><span class="barsub">This week</span></td></tr>';
		echo '<tr><td align="right">Flights:</td><td align="left">'.$week_flights.'</td></tr>';
		echo '<tr><td align="right">Pax:</td><td align="left">'.number_format($week_pax, 0).'</td></tr>';
		echo '<tr><td align="right">Cargo:</td><td align="left">'.number_format($this->format_fns->lbs_tonnes($week_cargo), 0).' t</td></tr>';
		echo '<tr><td colspan="2"><hr /><span class="barsub">Today</span></td></tr>';
		echo '<tr><td align="right">Flights:</td><td align="left">'.$day_flights.'</td></tr>';
		echo '<tr><td align="right">Pax:</td><td align="left">'.number_format($day_pax, 0).'</td></tr>';
		echo '<tr><td align="right">Cargo:</td><td align="left">'.number_format($this->format_fns->lbs_tonnes($day_cargo), 0).' t</td></tr>';
		echo '</table>';
		echo '</div>';
		echo '<br />';
    	
    }
    
    
    
    
    // simulator stats
    if(isset($sim_stats)){
    
    	//iterate through
    	$sim_tot = 0;
    	$sub = 0;
    	$sim_array = array();
    	foreach($sim_stats as $row){
    		//count up each and place in array
    		if(!array_key_exists($row->version_name, $sim_array)){ $sim_array[$row->version_name] = 0; }
    		$sim_array[$row->version_name]++;
    		$sim_tot++;
    	}
    	
    	//output data
    	if($sim_tot > 0){
	    	//order the array, with largest totals first
	    	arsort($sim_array);
    		
    		echo '<div style="margin-left: 5px; margin-right: 5px;">';
    		echo '<table width="100%" border="0" cellpadding="1" cellspacing="0">';
    		echo '<tr><td colspan="2"><hr /><span class="barsub">Active Flight Sims</span></td></tr>';
    		
    		//output the top 4 sims
    		$i = 0;
    		foreach($sim_array as $key => $value){
    		
    			if($i < 4 && $key != ''){
    				echo '<tr><td align="right">'.$key.':</td><td align="left">'.round($value/$sim_tot*100).'%</td></tr>';
    				$sub += $value;
    			}
    		if($key != ''){	$i++; }
    		}
    		
    		
    		echo '<tr><td align="right">Unknown:</td><td align="left">'.round($sim_array['']/$sim_tot*100).'%</td></tr>';
    		
    		$sub += $sim_array[''];
    		echo '<tr><td align="right">Other:</td><td align="left">'.round(($sim_tot-$sub)/$sim_tot*100).'%</td></tr>';
    		
    		
			echo '</table>';
			echo '</div>';
			echo '<br />';
    	}
    }
    
    ?>
    
    
    
    <div style="margin-left: 5px; margin-right: 5px;">
    
     <a href="<?php echo $base_url.'information/latest_flights/'; ?>"><img src="<?php echo $image_url; ?>content/partners/latest_flights.jpg" alt="Latest Flights" /></a>
    <br /><br />
    </div>
    
    
    <div style="margin-left: 5px; margin-right: 5px;">
	<span class="barhead">Partners</span><br /><br />
    <a href="<?php echo $base_url.'information/online/'; ?>"><img src="<?php echo $image_url; ?>content/partners/ivao_logo_sm.png" alt="IVAO" /></a>
    <br /><br />
    <a href="<?php echo $base_url.'information/online/'; ?>"><img src="<?php echo $image_url; ?>content/partners/vatsim_logo_sm.png" alt="VATSIM" /></a>
    <br /><br /><hr /><br />    
    <a href="<?php echo $base_url.'information/discounts/'; ?>"><img src="<?php echo $image_url; ?>content/partners/aerosoft.png" alt="Aerosoft" /></a>
    <br /><br />
    <a href="<?php echo $base_url.'information/discounts/'; ?>"><img src="<?php echo $image_url; ?>content/partners/fs2crew.png" alt="FS2Crew" /></a>
    <br /><br />
    <a href="<?php echo $base_url.'information/discounts/'; ?>"><img src="<?php echo $image_url; ?>content/partners/oryxsim.png" alt="Oryxsim" /></a>
    <br /><br />
    
    </div>
    
</div>

<br />

<?php

if(isset($page_title) && $page_title != ''){
	echo '	<div class="divblue" style="height: 25px;">
			<div style="margin-right: 130px">
			<font size="4"><b>'.$page_title.'</b></font>
			</div>
			</div>
';
}
else{
	echo '	<div class="divblue">
			<div style="margin-right: 130px">
			<div id="jspilotnews"> </div>
			</div>
			</div>
';
}
?>

<br />
