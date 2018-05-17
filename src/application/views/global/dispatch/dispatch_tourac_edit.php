<?php

echo '<h2>' . $tour_name . '</h2>';
echo 'Change the aircraft for this tour leg<br /><br />';

echo '<center>( ' . $start_icao . ' ' . $dep_name . ' to ' . $end_icao . ' ' . $arr_name . ' )</center>';

echo '<br />';

$hidden = array('valid' => 'true');
echo form_open('dispatch/tourcraft/' . $assigned_id, '', $hidden);

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Select aircraft</legend>';
//output dropdown
echo '<label for="enabled">Aircraft</label>' . form_dropdown('aircraft_id', $aircraft_array, $aircraft_id) . '<br />';
echo '</fieldset>';
echo '<br />';
echo '<center>' . form_submit('submit', 'Set aircraft') . '</center>';
echo form_close();

?>