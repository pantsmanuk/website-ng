<?php
$hidden = array(
	'valid' => 'true',
);
echo form_open('acp_propilot/event_legs_edit/' . $event_id . '/' . $leg_id . '/' . $sequence, '', $hidden);

echo $highlight1 . $error . $highlight2;

//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>';
echo '<label for="sequence">Sequence</label>' . $sequence . '<br /><br />';

echo '<label for="start_icao">Start ICAO</label>' . form_input($start_icao) . '<br />';
echo '<label for="end_icao">End ICAO</label>' . form_input($end_icao) . '<br />';
echo '<label for="start">Start Date</label>' . form_dropdown('start_day', $day_array, $start_day)
	. form_dropdown('start_month', $month_array, $start_month)
	. form_dropdown('start_year', $year_array, $start_year)
	. '<br />';

echo '<label for="end">End Date</label>' . form_dropdown('end_day', $day_array, $end_day)
	. form_dropdown('end_month', $month_array, $end_month)
	. form_dropdown('end_year', $year_array, $end_year)
	. '<br />';

echo '<label for="award">Award</label>' . form_dropdown('award_id', $award_array, $award_id) . '<br />';
echo '</fieldset>';

echo '<center>' . form_submit('submit', 'Submit') . '</center>';
echo form_close();
?>