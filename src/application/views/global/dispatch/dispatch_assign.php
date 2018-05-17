Flight assignment will automatically assign you a flight from the timetable. This could be from any division and any class up to your rank. This system will always assign from your current location, which is wherever you last landed. Please click the button to receive your assignment. You must then accept the assignment for it to appear in your dispatch log.
<br/><br/>

<center>

	<?php
	$hidden['valid'] = 'true';
	echo form_open('dispatch/assign/', '', $hidden);
	echo form_dropdown('selected_aircraft_id', $aircraft_array['aircraft_array'], $selected_aircraft_id);
	echo form_submit('Submit', 'Request Assignment');
	echo form_close();

	echo '<br /><br />';

	echo $err_message;

	if (isset($aircraft_name) && $aircraft_name != '') {
		echo '<table class="boxed" width="100%">';
		echo '<tr>';

		echo '<th>Aircraft</th>';
		echo '<th>Division</th>';
		echo '<th>Class</th>';
		echo '<th>Flight #</th>';
		echo '<th>Departure</th>';
		echo '<th>Take-Off </th>';
		echo '<th>Arrival</th>';
		echo '<th>Distance</th>';
		echo '<th>&nbsp;</th>';

		echo '</tr>';

		echo '<tr bgcolor="#f2f2f2">';

		echo '<td>' . $aircraft_name . '</td>';
		echo '<td align="center">' . $division_name . '</td>';
		echo '<td align="center">' . $clss . '</td>';
		echo '<td align="center">EH' . $prefix . '-' . $flightnumber . '</td>';
		echo '<td>' . $dep_airport . ' ' . $dep_name . '</td>';
		echo '<td align="center">' . substr($dep_time, 0, 5) . '</td>';
		echo '<td>' . $arr_airport . ' ' . $arr_name . '</td>';

		$gcd_km = $this->geocalc_fns->GCDistance($dep_lat, $dep_lon, $arr_lat, $arr_lon);
		$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

		echo '<td align="center">' . number_format($gcd_nm, 0) . 'nm</td>';
		echo '<td align="center"><a href="http://www.gcmap.com/map?P=' . strtolower($dep_airport) . '-' . strtolower($arr_airport) . '&MS=bm&PM=*" 
				rel="lightbox" title="Map of the route ' . strtoupper($dep_airport) . '-' . strtoupper($arr_airport) . '">
				<img src="' . $image_url . 'icons/application/map.png" alt="Map" /></a></td>';

		echo '</tr>';
		echo '</table>';

		echo '<br /><br />';

		$hidden['accept'] = 'true';
		$hidden['flightnumber'] = $flightnumber;
		$hidden['aircraft_id'] = $aircraft_id;
		echo form_open('dispatch/assign/', '', $hidden);
		echo form_submit('Submit', 'Accept Assignment');
		echo form_close();
	}

	?>


</center>