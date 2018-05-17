<?php
$hidden = array('valid' => 'true');
echo form_open('acp_pireps/edit/' . $pirep_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Flight</legend>';
echo '<b>Route: </b>' . $start_icao . ' ' . $dep_name . ' to ' . $end_icao . ' ' . $arr_name;
echo '<br /><b>Aircraft: </b>' . $aircraft;
echo '<br /><b>Passengers: </b>' . $passengers;
echo '<br /><b>Cargo: </b>' . $cargo . ' lbs';
echo '</fieldset>';

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Flight details</legend>';
echo '<label for="onoff">Online/Offline</label>' . form_dropdown('onlineoffline', $onlineoffline_array, $onlineoffline) . '<br />';
echo '<label for="engstart">Engine Start Time</label>'
	. form_dropdown('enginestart_dd', $dobday_array, $enginestart_dd)
	. form_dropdown('enginestart_mo', $dobmonth_array, $enginestart_mo)
	. form_dropdown('enginestart_yy', $pirepyear_array, $enginestart_yy) . ' (d/m/y) '
	. form_input($enginestart_hh) . ':' . form_input($enginestart_mm) . ' (hh:mm)<br />';
echo '<label for="to">Takeoff Time</label>'
	. form_dropdown('takeoff_dd', $dobday_array, $takeoff_dd)
	. form_dropdown('takeoff_mo', $dobmonth_array, $takeoff_mo)
	. form_dropdown('takeoff_yy', $pirepyear_array, $takeoff_yy) . ' (d/m/y) '
	. form_input($takeoff_hh) . ':' . form_input($takeoff_mm) . ' (hh:mm)<br />';
echo '<label for="land">Landing Time</label>'
	. form_dropdown('landing_dd', $dobday_array, $landing_dd)
	. form_dropdown('landing_mo', $dobmonth_array, $landing_mo)
	. form_dropdown('landing_yy', $pirepyear_array, $landing_yy) . ' (d/m/y) '
	. form_input($landing_hh) . ':' . form_input($landing_mm) . ' (hh:mm)<br />';
echo '<label for="engoff">Engine Shutdown Time</label>'
	. form_dropdown('engineoff_dd', $dobday_array, $engineoff_dd)
	. form_dropdown('engineoff_mo', $dobmonth_array, $engineoff_mo)
	. form_dropdown('engineoff_yy', $pirepyear_array, $engineoff_yy) . ' (d/m/y) '
	. form_input($engineoff_hh) . ':' . form_input($engineoff_mm) . ' (hh:mm)<br />';
echo '</fieldset>';

echo '<fieldset style="border: 1px dotted #cccccc; padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend><font color="#cccccc">Optional Information</font></legend>';
echo '<label for="cruisealt"><font color="#cccccc">Cruise Altitude</font></label>' . form_input($altitude) . '<br />';
echo '<label for="cruisespeed"><font color="#cccccc">Cruise Speed</font></label>' . form_input($speed) . '<br />';
echo '<label for="app"><font color="#cccccc">Approach</font></label>' . form_dropdown('approach', $approach_array, $approach) . '<br />';
echo '<label for="fuelburn"><font color="#cccccc">Fuel Burnt</font></label>' . form_input($fuelburnt) . '<br />';
echo '<label for="comments"><font color="#cccccc">Comments</font></label>' . form_textarea($comments);
echo '</fieldset>';

echo '<center>' . form_submit('joinsubmit', 'Edit Report') . '</center>';
echo form_close();
?>