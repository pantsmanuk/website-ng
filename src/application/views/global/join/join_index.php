<div style="padding:5px; float: right; width: 450px;">

    <img src="<?php echo $tmpl_image_url; ?>home/first_flight.png" alt="First Flight"/><br/>

    <div class="container">

        In order to join the VA, you must now conduct and post a report of your first flight. This flight is not
        substantially different from any other flight you may fly for the VA other than it is pre-selected. Once a
        member of the VA you will choose which flights to perform and with which divisions to fly them.

        <br/><br/>
        Depart from Manchester Airport (EGCC) and Fly to Birmingham (EGBB) using any Class 1 aircraft. This is a short
        hop flight and the first in your career at Euroharmony (optional route: EGCC LISTO R101 STAFA EGBB). Once
        complete, post your flight using the form below.

        <br/><br/>
		<?php
		$hidden = array('valid' => '1');
		echo form_open('join', '', $hidden);

		echo $highlight1 . $error . $highlight2;

		//echo form_input($data);
		echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
		echo '<legend>Pilot details</legend>';
		echo '<label for="dof">Firstname</label>' . form_input($fname) . '<br />';
		echo '<label for="dof">Surname</label>' . form_input($sname) . '<br />';
		echo '<label for="dof">Email Address</label>' . form_input($email) . '<br />';
		echo '<label for="dof">Confirm Email</label>' . form_input($email2) . '<br />';
		echo '<label for="dof">Country</label>' . form_dropdown('country', $country_array, $country) . '<br />';
		echo '<label for="dob">DOB (D/M/Y)</label>' . form_dropdown('dobday', $dobday_array, $dobday) . form_dropdown('dobmonth', $dobmonth_array, $dobmonth) . form_dropdown('dobyear', $dobyear_array, $dobyear) . '<br />';
		echo '<label for="dof">Flight Simulator</label>' . form_dropdown('flightsim', $flightsim_array, $flightsim) . '<br />';
		echo '<label for="dof">Hub Choice 1</label>' . form_dropdown('hub_1', $hub_array, $hub_1) . '<br />';
		echo '<label for="dof">Hub Choice 2</label>' . form_dropdown('hub_2', $hub_array, $hub_2) . '<br />';
		echo '<label for="dof">Hub Choice 3</label>' . form_dropdown('hub_3', $hub_array, $hub_3) . '<br />';
		echo '<label for="dof">Other VA</label>' . form_dropdown('other_va', $otherva_array, $other_va) . '<br />';
		echo '</fieldset>';

		echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
		echo '<legend>Flight details</legend>';
		echo '<label for="dof">Date of flight</label>' . form_dropdown('flightdate', $flightdate_array, $flightdate) . '<br />';
		echo '<label for="dof">Aircraft used</label>' . form_dropdown('aircraft', $aircraft_array, $aircraft) . '<br />';
		echo '<label for="dof">Online/Offline</label>' . form_dropdown('onlineoffline', $onlineoffline_array, $onlineoffline) . '<br />';
		echo '<label for="dof">Engine Start Time</label>' . form_input($enginestart_hh) . ':' . form_input($enginestart_mm) . ' (hh:mm)<br />';
		echo '<label for="dof">Takeoff Time</label>' . form_input($takeoff_hh) . ':' . form_input($takeoff_mm) . ' (hh:mm)<br />';
		echo '<label for="dof">Landing Time</label>' . form_input($landing_hh) . ':' . form_input($landing_mm) . ' (hh:mm)<br />';
		echo '<label for="dof">Engine Shutdown Time</label>' . form_input($engineoff_hh) . ':' . form_input($engineoff_mm) . ' (hh:mm)<br />';
		echo '</fieldset>';

		echo '<fieldset style="border: 1px dotted #cccccc; padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
		echo '<legend><font color="#cccccc">Optional Information</font></legend>';
		echo '<label for="dof"><font color="#cccccc">Cruise Altitude</font></label>' . form_input($altitude) . form_dropdown('alt_units', $alt_unit_array, $alt_units) . '<br />';
		echo '<label for="dof"><font color="#cccccc">Cruise Speed</font></label>' . form_input($speed) . form_dropdown('speed_units', $speed_units_array, $speed_units) . '<br />';
		echo '<label for="dof"><font color="#cccccc">Approach</font></label>' . form_dropdown('approach', $approach_array, $approach) . '<br />';
		echo '<label for="dof"><font color="#cccccc">Fuel Burnt</font></label>' . form_input($fuelburnt) . form_dropdown('fuel_units', $fuel_units_array, $fuel_units) . '<br />';
		echo '<label for="dof"><font color="#cccccc">Vatsim ID</font></label>' . form_input($vatsimid) . '<br />';
		echo '<label for="dof"><font color="#cccccc">IVAO ID</font></label>' . form_input($ivaoid) . '<br />';
		echo '<label for="dof"><font color="#cccccc">Comments</font></label>' . form_textarea($comments);
		echo '</fieldset>';

		echo '<center>' . form_submit('joinsubmit', 'Submit Flight') . '</center>';
		echo form_close();
		?>


    </div>

</div>


<div style="padding:5px; margin-right: 460px;">

    <img src="<?php echo $tmpl_image_url; ?>home/pilot_briefing.png" alt="Pilot Briefing"/><br/>

    <div class="container">

        <table>
            <tr>
                <td><img src="<?php echo $image_url; ?>icons/application/accept.png" alt="tick"/></td>
                <td>Rules, Regulations and SOP <a href="<?php echo $ops_manual_link; ?>">[Download]</a></td>
            </tr>
            <tr>
                <td></td>
                <td><font color="#777777">I have read and agree to the Rules and Regulations of the VA and understand
                        the standard operating procedures of the airline</font></td>
            </tr>
            <tr>
                <td><img src="<?php echo $image_url; ?>icons/application/accept.png" alt="tick"/></td>
                <td>Download Aircraft <a href="<?php echo $base_url; ?>fleet/aircraft/A/2">[Download]</a></td>
            </tr>
            <tr>
                <td></td>
                <td><font color="#777777">I have downloaded and installed the training aircraft to perform my first
                        flight</font></td>
            </tr>
            <tr>
                <td><img src="<?php echo $image_url; ?>icons/application/accept.png" alt="tick"/></td>
                <td>Download Flight Logger (optional) <a href="<?php echo $flogger_latest; ?>">[Download]</a></td>
            </tr>
            <tr>
                <td></td>
                <td><font color="#777777">I have downloaded and installed the Flight logging software to automatically
                        record and approve my flights <b>after</b> I have posted my first flight here</font></td>
            </tr>
        </table>


    </div>
    <br/>
    <br/>

    <img src="<?php echo $tmpl_image_url; ?>home/training.png" alt="Training"/><br/>

    <div class="container">
        Manchester (EGCC) is our Training hub and is the main destination and departure airport for training flights. If
        you are new to Virtual Airlines and Virtual Aviation in general, it is strongly recommended that you complete
        these training flights. If you have no difficulty performing these tasks, simply proceed to the first flight.
        These flights are performed with a fixed wing Class 1 aircraft. The B1900D is recommended. There is no
        verification on these or the checkride - simply fly at a realism level that you enjoy.
        <br/><br/>
        <b>Basic aircraft familiarisation</b>
        <br/>
        Take off from Manchester and fly a circuit under manual control. You should fly runway heading on takeoff -
        climb to 4000 feet, then make a 90 degree left turn. Once a suitable distance from the airfield, make another
        left turn and fly parallel to the runway. Once you have passed the runway by a suitable distance, perform
        another left turn and fly until you can make a turn to line up with the runway and land.

        Repeat this flight as many times as necessary to familiarise yourself with basic aircraft handling.
        <br/>
        <br/>
        <b>ILS landing</b>
        <br/>
        Similar to the previous flights, fly a circuit around the airport using the autopilot to execute heading and
        altitude holds. Expand the distance flown from the airfield, but fly longer on the parallel stretch, so that it
        is possible to make a 135 degree turn to intercept the runway line at 45 degrees. Ensure that the interecept is
        at least 4 runway lengths from the runway threshold.<br/>
        <br/>
        Tune the nav radio to the runway frequency of 109.50 and when on the 45 degree ILS intercept, hit the APP button
        (Approach). The autopilot should when intercepting the ILS, adjust heading and altitude to follow the glidepath.
        Before touchdown, disengage the autopilot and perform a manual landing. Repeat this flight as many times as
        necessary to familiarise yourself with basic aircraft handling.


    </div>

    <br/>
    <br/>


</div>



