<?php
$this->load->view('global/profile/profile_topbar');
?>

<span style="font-size:16px; font-weight:bold;"><?php echo $num_flights; ?> flights logged</span>
<br/>
<?php
/*
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
<td>Aircraft</td>
<td>Departed</td>
<td>Arrived</td>
<td>Passengers</td>
<td>Cargo</td>
<td>Submitted</td>
<td>Duration</td>
<td>Online/offline</td>
<td>Route</td>
<td>Sim</td>
</tr>
</table>
*/
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr>
		<?php
		$colspan = 9;
		if ($this->session->userdata('admin_cp') == '1') {
			$colspan = 10;
		}
		echo '<span style="float: right;"><p>' . $this->pagination->create_links() . '</p></span>';
		?>
    </tr>
	<?php

	$i = 0;
	$j = 1;
	foreach ($timetable_flights as $row) {
		if ($j > $offset && $j <= ($offset + $limit)) {
			if ($i % 2 == 0) {
				$bgcol = 'bgcolor="#f2f2f2"';
			} else {
				$bgcol = '';
			}

			//caluclate distabnce between airports
			$gcd_km = $this->geocalc_fns->GCDistance($row->dep_lat, $row->dep_long, $row->arr_lat, $row->arr_long);
			$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

			echo '<tr ' . $bgcol . '>';
			echo '<td width="60"><b>' . gmdate('d/m/Y', strtotime($row->departure_time)) . '</b></td>';
			echo '<td width="140"><b>' . $row->aircraft . '</b>';
			echo '</td>';
			echo '<td><b>' . $row->start_icao . ' ' . $row->start_name . '</b></td>';
			echo '<td width="10"><b>-></b></td>';
			echo '<td><b>' . $row->end_icao . ' ' . $row->end_name . '</b></td>';
			echo '<td width="60" align="right"><b>' . number_format($gcd_nm, 0) . ' nm</b></td>';
			echo '<td width="16"><a href="http://www.gcmap.com/map?P=' . strtolower($row->start_icao) . '-' . strtolower($row->end_icao) . '&MS=bm&PM=*" 
	rel="lightbox" title="Map of the route ' . strtoupper($row->start_icao) . '-' . strtoupper($row->end_icao) . '">
	<img src="' . $image_url . 'icons/application/map.png" alt="Map" /></a></td>';
			echo '<td width="16"><a href="' . $base_url . 'profile/flightdata_print/' . $row->id . '" target="_popup" onClick="wopen(\'' . $base_url . 'profile/flightdata_print/' . $row->id . '\', \'_popup\', 835, 425); return false;">
	<img src="' . $image_url . 'icons/application/printer.png" alt="Print" /></a></td>';
			if ($this->session->userdata('admin_cp') == '1') {
				echo '<td width="16"><a href="' . $base_url . 'acp_pilots/flightdelete/' . $row->id . '/' . $current_pilot_id . '" >
	<img src="' . $image_url . 'icons/application/database_delete.png" alt="Delete" /></a></td>';
			}
			echo '<td width="50" align="center"><label for="' . $row->id . '" class="toggle">+Details</label>';
			echo '</tr>';

			//$duration = floor((strtotime($row->engine_stop_time) - strtotime($row->engine_start_time))/60);
			//$duration = $duration - $row->pausetime_mins;
			$duration = $row->blocktime_mins;
			$duration_h = floor($duration / 60);
			$duration_m = $duration - ($duration_h * 60);

			//$pausetime = $duration - $row->blocktime_mins;

			echo '<tr ' . $bgcol . '>';
			echo '<td colspan="' . $colspan . '">';
			echo '<input id="' . $row->id . '" checked="checked" class="toggle" type="checkbox"><div>';
			echo '<span class="inlineblock">';
			echo '<b>Duration:</b> ' . $duration_h . 'h ' . $duration_m . 'm';
			echo '</span>';
			echo '<span class="inlineblock">';
			echo '<b>Departed:</b> ' . gmdate('H:i', strtotime($row->departure_time));
			echo '</span>';
			echo '<span class="inlineblock">';
			echo '<b>Arrived:</b> ' . gmdate('H:i', strtotime($row->landing_time));
			echo '</span>';
			echo '<span class="inlineblock">';
			echo '<b>Passengers:</b> ' . number_format($row->passengers);
			echo '</span>';
			echo '<span class="inlineblock">';
			echo '<b>Cargo:</b> ' . number_format($row->cargo) . ' lbs';
			echo '</span>';

			echo '<span class="inlineblock">';
			if ($row->propilot_flight == '1') {
				$pp = 'Yes';
			} else {
				$pp = 'No';
			}
			echo '<b>ProPilot:</b> ' . $pp;
			echo '</span>';

			$pp_score = $row->pp_score;
			$pp_score_ng = $row->pp_score_ng;

			if (!empty($pp_score_ng)) {
				$pp_score_out = number_format($pp_score_ng);
			} else {
				$pp_score_out = number_format($pp_score);
			}

			if ($row->propilot_flight != '1') {

				$pp_score_out = 'N/A';
			}

			echo '<span class="inlineblock">';

			echo '<b>PP Score:</b> ' . $pp_score_out;
			echo '</span>';

			echo '<span class="inlineblock">';
			echo '<b>Network:</b> ' . $row->onoffline;
			echo '</span>';
			echo '<span class="inlineblock">';
			echo '<b>Cruise Alt:</b> ' . $row->cruisealt . 'ft';
			echo '</span>';
			echo '<span class="inlineblock">';
			echo '<b>Cruise Spd:</b> ' . $row->cruisespd;
			echo '</span>';

			echo '<hr />';
			echo '<b>Comments:</b> <br />' . nl2br(htmlspecialchars($row->comments));

			echo '</div>';
			echo '</td>';
			echo '</tr>';

			/*
			echo '<td>'.$row->onoffline.'</td>';
			echo '<td></td>';
			echo '<td>'.$row->fl_version.'</td>';
			echo '</tr>';
			*/

			$i++;
		}
		$j++;
	}
	?>

</table>


<div id="gcmattrib">Maps generated by the <a href="http://www.gcmap.com/">Great Circle Mapper</a>&nbsp;- copyright
    &#169; <a href="http://www.kls2.com/~karl/">Karl L. Swartz</a>.
</div>