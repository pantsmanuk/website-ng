 <br />
 <font size="4"><b>Manual PIREP (Pilot Report)</b></font>
 <br /><br /><div style="color: #777777;">Manual submissions have to be approved before showing up in your flight log. Flight Logger can automatically record, submit and approve both normal and propilot flights. <a href="<?php echo $flogger_latest; ?>">Download Flogger <?php echo $flogger_version; ?> Now.</a></div>
 <br />
 
    <?php
	$hidden = array('valid' => 'true');
	echo form_open('dispatch/pirep/'.$assigned_id,'',$hidden);

echo '<center><div class="warning">All flight times should be reported with the real world date and time (zulu), not simulation date and time.</div></center><br />';	
	
	echo $highlight1.$error.$highlight2;
	
	echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
	echo '<legend>Flight</legend>'; 
	echo '<b>Route: </b>'.$start_icao.' '.$dep_name.' to '.$end_icao.' '.$arr_name;
	echo '<br /><b>Aircraft: </b>'.$aircraft;
	echo '<br /><b>Passengers: </b>'.$passengers;
	echo '<br /><b>Cargo: </b>'.$cargo.' lbs';
	echo '</fieldset>';
	

	
	echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
	echo '<legend>Flight details</legend>'; 
	echo '<label for="dof">Date of flight</label>'.form_dropdown('flightdate', $flightdate_array, $flightdate).'<br />';
	echo '<label for="dof">Online/Offline</label>'.form_dropdown('onlineoffline', $onlineoffline_array, $onlineoffline).'<br />';
	echo '<label for="dof">Engine Start Time</label>'.form_input($enginestart_hh).':'.form_input($enginestart_mm).' (hh:mm)<br />';
	echo '<label for="dof">Takeoff Time</label>'.form_input($takeoff_hh).':'.form_input($takeoff_mm).' (hh:mm)<br />';
	echo '<label for="dof">Landing Time</label>'.form_input($landing_hh).':'.form_input($landing_mm).' (hh:mm)<br />';
	echo '<label for="dof">Engine Shutdown Time</label>'.form_input($engineoff_hh).':'.form_input($engineoff_mm).' (hh:mm)<br />';
	echo '</fieldset>';
	
	echo '<fieldset style="border: 1px dotted #cccccc; padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
	echo '<legend><font color="#cccccc">Optional Information</font></legend>'; 
	echo '<label for="dof"><font color="#cccccc">Cruise Altitude</font></label>'.form_input($altitude).form_dropdown('alt_units', $alt_unit_array, $alt_units).'<br />';
	echo '<label for="dof"><font color="#cccccc">Cruise Speed</font></label>'.form_input($speed).form_dropdown('speed_units', $speed_units_array, $speed_units).'<br />';
	echo '<label for="dof"><font color="#cccccc">Approach</font></label>'.form_dropdown('approach', $approach_array, $approach).'<br />';
	echo '<label for="dof"><font color="#cccccc">Fuel Burnt</font></label>'.form_input($fuelburnt).form_dropdown('fuel_units', $fuel_units_array, $fuel_units).'<br />';
	echo '<label for="dof"><font color="#cccccc">Comments</font></label>'.form_textarea($comments);
	echo '</fieldset>';
	
	echo '<center>'.form_submit('joinsubmit', 'Submit Report').'</center>';
	echo form_close();
	?>