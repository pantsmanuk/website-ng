<div align="center">

<table  width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td>

<div class="menu_sub">
<?php
$i = 0;
foreach($hub_list as $icao => $name){

	if($i > 0){
		echo ' | ';
	}

	echo '<a href="'.$base_url.'hubs/index/'.$icao.'">'.$icao.'</a>';
	

	$i++;
}
?>
</div>
<br />
<br />
<br />

<table>
<tr valign="top">
<td>

<img src="<?php echo $tmpl_image_url; ?>structure/hubs/hub_<?php echo strtolower($selected_hub_icao); ?>.jpg" alt="<?php echo $selected_hub_icao; ?> hub" style="float: left; padding: 3px; margin-right: 3px; border: #bbbbbb 1px solid;" />

</td>
<td>

<div class="container">
<font color="#999999">
<?php
echo '<span style="float: right;">Opened: '.date('M Y', strtotime($hub_array[$selected_hub_icao]['hub_opened'])).'</span>';
echo 'Hub Manager: '.$hub_array[$selected_hub_icao]['hub_captain'];
?>
</font>
<br /><br />

<?php
echo '<div align="left">'.$hub_array[$selected_hub_icao]['hub_description'].'</div>';
?>

</div>

</td>
</tr>
</table>

<div class="clear"><!-- --></div>

<br />
<br />

<?php
if($hub_array[$selected_hub_icao]['connection_centre'] == '0'){
?>

<table class="boxed" width="100%">
<tr>
<th></th>
<th></th>
<th>Rank</th>
<th>Pilot</th>
<th>Flight time</th>
<th>Location</th>
<th>Sim</th>
<th>Last Flight</th>
<?php /*<th>Last Active</th> */ ?>
</tr>
<?php
$i = 1;
if(array_key_exists($selected_hub_icao, $hub_array)){

	

	foreach($hub_array[$selected_hub_icao]['pilots'] as $row){
	
		//only output if pilot exists
		if($row['pilot_id'] != ''){
			if($i%2 != 0){
				$bgcol = 'bgcolor="#f2f2f2"';
			}
			else{
				$bgcol = '';
			}
			
				
			
				//handle location - pp location if the pp flight is 'locked state', or is more recent than a non-pp flight
				if($row['pp_location'] != '' && (strtotime($row['pp_lastflight']) == strtotime($row['lastflight']) || strtotime($row['pp_lastflight']) >= strtotime($pp_compare_date)  )){
					$location = $row['pp_location'].' (pp)';
				}
				else{
					$location = $row['curr_location'];
				}
				
			
			
				if($row['name'] != ''){
				echo '<tr '.$bgcol.'>';
				echo '<td>'.$i.'</td>';
				echo '<td align="center"><a href="#" class="tt">
				<img src="'.$image_url.'icons/flags/'.$row['country_code'].'.gif" alt="'.$row['country_code'].'" width="30" height="17" />'
				.'<span class="tooltip"><span class="top"></span><span class="middle">'
				.$row['country']
				.'</span><span class="bottom"></span></span>'
				.'</a></td>';
				echo '<td>'.$row['rank_long'].'</td>';
				echo '<td>'.$row['name'].'</td>';
				echo '<td align="center">'.$row['flighthours'].'h '.$row['flightmins'].'m</td>';
				
				echo '<td align="center">'.$location.'</td>';
				echo '<td align="center">'.$row['flight_sim'].'</td>';
				echo '<td align="center">'.date('d/m/Y', strtotime($row['lastflight'])).'</td>';
				//echo '<td align="center">'.date('d/m/Y', strtotime($row['lastactive'])).'</td>';
				
				
				echo '</tr>';
				}
			$i++;
		}
	}
}

?>

</table>

<?php
}
else{

echo 'This hub is currently a connection centre to allow us a refuel stop and link to other destinations.<br /><br />Pilots cannot be assigned to this hub until it grows to reach full hub status.';

}
?>

</td>
</tr>
</table>
</div>
