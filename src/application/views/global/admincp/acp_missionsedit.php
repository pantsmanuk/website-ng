<?php
$hidden = array(
	'valid' => 'true'
);
echo form_open('acp_missions/edit/'.$mission_id,'',$hidden);


echo $highlight1.$error.$highlight2;


//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>'; 
echo '<label for="title">Title</label>'.form_input($title).' eg: Death run<br />';


echo '<label for="dep_airport">Departure airport</label>'.form_dropdown('start_icao', $airfield_array, $start_icao).' eg: EGLL<br />';
echo '<label for="arr_airport">Arrival airport</label>'.form_dropdown('end_icao', $airfield_array, $end_icao).' eg: EGLL<br /><br />';

echo '<label for="division">Division</label>'.form_dropdown('division', $divisions_array, $division).' eg: Euroharmony<br />';

echo '<label for="aircraft_id">Aircraft</label>'.form_dropdown('aircraft_id', $aircraft_array, $aircraft_id).' eg: Aircraft Name [Class]<br />';

echo '<label for="clss">Class</label>'.form_dropdown('clss', $clss_array, $clss).' eg: Class 1<br /><br />';

echo '<label for="description">Description</label>'.form_textarea($description).'<br /><br />';

echo '<label for="start">Start Date</label>'	.form_dropdown('start_day', $day_array, $start_day)
										.form_dropdown('start_month', $month_array, $start_month)
										.form_dropdown('start_year', $year_array, $start_year)
										.'<br />';
										
echo '<label for="start">End Date</label>'	.form_dropdown('end_day', $day_array, $end_day)
										.form_dropdown('end_month', $month_array, $end_month)
										.form_dropdown('end_year', $year_array, $end_year)
										.' <br />';

echo '</fieldset>';

//optional set
echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Optional</legend>'; 

echo '<label for="dep_time">Departure time</label>'.form_input($dep_time).' eg: 09:00<br />';
echo '<label for="arr_time">Arrival time</label>'.form_input($arr_time).' eg: 14:05<br />';

echo '<label for="dep_weather">Departure Weather</label>'.form_textarea($dep_weather).'<br />';

echo '<label for="arr_weather">Arrival Weather</label>'.form_textarea($arr_weather).'<br />';

echo '</fieldset>';

echo '<center>'.form_submit('submit', 'Submit').'</center>';
echo form_close();
?>