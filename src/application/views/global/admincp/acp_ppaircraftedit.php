<?php
$hidden = array(
	'valid' => 'true',
);
echo form_open('acp_propilot/aircraft_edit/' . $pp_aircraft_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>';
echo '<label for="tail_id">Tail id</label>' . form_input($tail_id) . ' eg: G-EACB<br />';
echo '<label for="aircraft_id">Aircraft</label>' . form_dropdown('aircraft_id', $aircraft_array, $aircraft_id) . '<br />';
echo '<label for="state_id">Aircraft State</label>' . form_dropdown('state_id', $state_array, $state_id) . '<br />';
echo '<label for="location">Location</label>' . form_dropdown('location', $airfield_array, $location) . '<br />';
echo '<label for="owner">Owner</label>' . form_dropdown('owner', $pilot_array, $owner) . '(Active + email confirmed only)<br />';
echo '</fieldset>';

echo '<center>' . form_submit('submit', 'Submit') . '</center>';
echo form_close();
?>