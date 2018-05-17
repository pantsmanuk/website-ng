<?php

$signup_output = '';
$signup_output .= '<center>';
//output signup if we're before start date and user hasn't assigned flights to dispatch log
if (strtotime($pp_event_start_date) > time() && $signed_up != 1) {

	if ($this->session->userdata('logged_in') == '1') {
		$hidden = array('valid' => 'true', 'signup' => '1');
		$signup_output .= form_open('events/details/' . $event_id . '/', '', $hidden);
		$signup_output .= '<input type="submit" class="form_button" value="Sign Up" />';
		$signup_output .= form_close();
	} else {
		$signup_output .= '<b>You must be logged in to signup for an event</b>';
	}
} else {

	if ($signed_up == 1) {
		//if we're already signed up
		$signup_output .= '<b>You are already signed up for this event<br />Please check your dispatch log</b>';
	} else {
		//otherwise
		$signup_output .= '<b>Signing up for this event is now closed</b>';
	}
}

$signup_output .= '</center>';

echo '<font size="4"><b>' . $pp_event_name . '</b></font><br />';
echo '<b>(' . $pp_aircraft . ')</b>';
echo '<br /><br />';

//determine if image exists for image
$image_path = $assets_path . 'uploads/events/' . $pp_event_id;

if (file_exists($image_path . '.gif')) {
	$iimage_url = $assets_url . 'uploads/events/' . $pp_event_id . '.gif';
	$image_path = $assets_path . 'uploads/events/' . $pp_event_id . '.gif';
} elseif (file_exists($image_path . '.png')) {
	$iimage_url = $assets_url . 'uploads/events/' . $pp_event_id . '.png';
	$image_path = $assets_path . 'uploads/events/' . $pp_event_id . '.png';
} elseif (file_exists($image_path . '.jpg')) {
	$iimage_url = $assets_url . 'uploads/events/' . $pp_event_id . '.jpg';
	$image_path = $assets_path . 'uploads/events/' . $pp_event_id . '.jpg';
} else {
	$iimage_url = $assets_url . 'uploads/events/no-image.png';
	$image_path = $assets_path . 'uploads/events/no-image.png';
}

//recalculate width and height

list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);

//style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"
$height_new = 80;
$width_new = $width_orig / $height_orig * $height_new;

echo '<center>';
echo '<img src="' . $iimage_url . '" alt="Image" width="' . $width_new . '" height="' . $height_new . '" style="padding: 3px; border: #bbbbbb 1px solid;"/>';
echo '</center>';
echo '<br />';

echo $signup_output;
echo '<br />';

if (count($participants) > 0) {

	//people are already signed up! tell the world.
	echo '<h2>Pilots participating</h2>';
	echo '<table width="100%">';

	$i = 0;
	$j = 1;
	foreach ($participants as $participant) {

		if ($j % 2 != 0) {
			$bgcolor = 'style="background: #f2f2f2;"';

		} else {
			$bgcolor = '';
		}

		if ($i % 3 == 0) {
			$j++;
			echo "<tr  $bgcolor>";

		}

		echo '<td width="27%">EHM-' . $participant['username'] . ' ' . $participant['fname'] . ' ' . $participant['sname'] . '</td>';
		echo '<td width="6%" align="center"><b>' . $participant['pp_location'] . '</b></td>';
		//echo '<p>';
		//        if(isset($news_item->enclosure)){
		//            echo img($news_item->enclosure->attributes()->url);
		//        }
		//echo $news_item->description;
		//echo '</p>';

		//close row
		if (($i + 1) % 3 == 0) {
			echo '</tr>';
		}
		$i++;
	}

	//determine if end needed
	if ((count($participants) + 1) % 3 == 0) {
		//need to end
		echo '<td>&nbsp;</td>';
		echo '</tr>';
	} elseif ((count($participants) + 2) % 3 == 0) {
		//need to end
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';
	}

	echo '</table>';

}

echo '<h2>Difficulty</h2>';
echo $pp_event_difficulty;

echo '<br />';

echo '<h2>Description</h2>';
echo $pp_event_description;

echo '<h2>Legs</h2>';
echo '<table border="0" cellpadding="4" cellspacing="0" style="border-collapse: collapse;" bordercolor="#111111" width="100%">';
echo '
		  <tr>
		  	<th width="20">Leg</th>
		    <th>Origin</th>
		    <th>Destination</th>
		    <th width="50">Radial</th>
		    <th width="50">Distance</th>
		    <th>Opens</th>
		    <th>Closes</th>
		    <th width="50">Award</th>
		  </tr>';
$i = 0;
foreach ($flight_array as $row) {

	if ($row['start_icao'] != '') {
		if ($i % 2 == 0) {

			//grey the background
			$bgstyle = 'style="background: #f2f2f2;"';

		} else {
			$bgstyle = '';
		}

		//if leg is 'open' change background row colour

		echo '<tr ' . $bgstyle . '>';

		echo '<td>' . $row['sequence'] . '</td>';
		echo '<td>' . $row['start_icao'] . ' ' . $row['start_name'] . '</td>';
		echo '<td>' . $row['end_icao'] . ' ' . $row['end_name'] . '</td>';
		echo '<td align="center">' . $row['gc_bearing'] . ' deg' . '</td>';
		echo '<td align="center">' . number_format($row['gcd_nm'], 0) . ' nm' . '</td>';
		echo '<td align="center">' . gmdate('d/m/Y', strtotime($row['start_date'])) . '</td>';
		echo '<td align="center">' . gmdate('d/m/Y', strtotime($row['end_date'])) . '</td>';
		if ($row['award_id'] > 0) {
			echo '<td align="center" width="15"><img src="' . $image_url . 'icons/application/asterisk_yellow.png" alt="' . $row['award_name'] . '" /></td>';
		} else {
			echo '<td align="center" width="15">-</td>';
		}

		echo '</tr>';
	}
	$i++;
}
echo '</table>';

echo '<br /><br />';
echo $signup_output;

?>