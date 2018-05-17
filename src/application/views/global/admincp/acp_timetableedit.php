<?php
$hidden = array(
	'valid' => 'true',
);
echo form_open('acp_timetables/edit/' . $timetable_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>';
echo '<label for="flightnumber">Flight number</label>';
if ($timetable_id == '0') {
	echo 'Will be generated<br /><br />';
} else {
	echo $flightnumber . '<br /><br />';
}

echo '<label for="active">Active</label>' . form_dropdown('active', $bool_array, $active) . ' eg: Yes<br />';

echo '<label for="division">Division</label>' . form_dropdown('division', $divisions_array, $division) . ' eg: Euroharmony<br />';
echo '<label for="clss">Class</label>' . form_dropdown('clss', $clss_array, $clss) . ' eg: Class 1<br />';

echo '<label for="hub">Hub</label>' . form_dropdown('hub', $hub_array, $hub) . ' eg: EGLL<br />';

echo '<br />';

echo '<label for="dep_airport">Departure airport</label>' . form_input($dep_airport) . ' eg: EGLL<br />';
echo '<label for="arr_airport">Arrival airport</label>' . form_input($arr_airport) . ' eg: EGLL<br /><br />';
echo '<label for="days">Days</label><table class="statbox"><tr><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td></tr>'
	. '<tr><td>' . form_checkbox($sun) . '</td>
<td>' . form_checkbox($mon) . '</td>
<td>' . form_checkbox($tue) . '</td>
<td>' . form_checkbox($wed) . '</td>
<td>' . form_checkbox($thu) . '</td>
<td>' . form_checkbox($fri) . '</td>
<td>' . form_checkbox($sat) . '</td></tr></table>'
	. '<br />';
echo '<label for="dep_time">Departure time</label>' . form_input($dep_time) . ' eg: 09:00<br />';
echo '<label for="arr_time">Arrival time</label>' . form_input($arr_time) . ' eg: 14:05<br />';

echo '<br />';

echo '<label for="dep_airport">Season Start</label>' . form_dropdown('season_month_start', $season_month_array, $season_month_start) . ' eg: Jun<br />';
echo '<label for="arr_airport">Season End</label>' . form_dropdown('season_month_end', $season_month_array, $season_month_end) . ' eg: Aug<br /><br />';

if ($timetable_id == '0') {
	echo '<label for="return">Create return flight?</label>' . form_checkbox($return) . '<br />';
}
echo '</fieldset>';

echo '<center>' . form_submit('submit', 'Submit') . '</center>';
echo form_close();
?>