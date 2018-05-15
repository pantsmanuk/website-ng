<?php
$hidden = array(
	'valid' => 'true'
);
echo form_open('acp_tours/legs_edit/'.$tour_id.'/'.$leg_id.'/'.$sequence.'/'.$flight_sim,'',$hidden);


echo $highlight1.$error.$highlight2;


//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>'; 
echo '<label for="sequence">Sequence</label>'.$sequence.'<br /><br />';

if($flight_sim == NULL || $flight_sim == ''){
	$flight_sim_out = 'Generic';
	echo '<label for="flight_sim">Flight Simulator</label>'.$flight_sim_out.'<br /><br />';
}
elseif($flight_sim > 0){
	$flight_sim_out = $sim_array[$flight_sim];
	echo '<label for="flight_sim">Flight Simulator</label>'.$flight_sim_out.'<br /><br />';
}
else{
	$flight_sim_out = 'Unknown';
	echo '<label for="flight_sim">Flight Simulator</label>'.form_dropdown('flight_sim', $sim_array, $flight_sim).'<br />';
}



echo '<label for="start_icao">Start ICAO</label>'.form_input($start_icao).'<br />';
echo '<label for="end_icao">End ICAO</label>'.form_input($end_icao).'<br />';
echo '<label for="altitude">Altitude</label>'.form_input($altitude).' ft (number only)<br />';
echo '<label for="award">Award</label>'.form_dropdown('award_id', $award_array, $award_id).'<br />';
echo '</fieldset>';


echo '<center>'.form_submit('submit', 'Submit').'</center>';
echo form_close();
?>