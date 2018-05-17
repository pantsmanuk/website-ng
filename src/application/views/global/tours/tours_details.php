<center>
	<?php

	//drop the header image
	$img_url = $assets_url . 'images/tours/head/' . $id . '_head.jpg';
	if (is_array(@getimagesize($img_url))) {
		echo '<img src="' . $img_url . '" alt="' . $page_title . '" /><br /><br />';
	}
	?>
</center>
<?php
//first output the detailed description
echo '<h2>' . $name . '</h2>';
echo nl2br($detail_info);
echo '<br /><br />';

//output legs

//build and output menu
echo '<div class="menu_sub">';
$j = 0;
foreach ($versions as $key => $row) {

	if ($j > 0) {
		echo ' | ';
	}

	echo '<a href="' . $base_url . 'tours/details/' . $id . '/' . $key . '">' . $row . '</a>';
	$j++;
}
echo '</div>';

//output selected
echo '<h2>Legs: ' . $versions[$selected_version] . '</h2>';

?>

<center>
    <table border="0" cellpadding="4" cellspacing="0" style="border-collapse: collapse;" bordercolor="#111111"
           width="100%">
        <tbody>
        <tr>
            <th>Leg</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Radial</th>
            <th>Distance</th>
            <th>Altitude</th>
        </tr>

		<?php

		$i = 0;
		foreach ($flight_array as $row) {

			if ($i % 2 == 0) {

				//grey the background
				$bgstyle = 'style="background: #f2f2f2;"';

			} else {
				$bgstyle = '';
			}

			if (array_key_exists('sequence', $row)) {

				echo '<tr ' . $bgstyle . '>';

				echo '<td>' . $row['sequence'] . '</td>';
				echo '<td>' . $row['start_icao'] . ' ' . $row['start_name'] . '</td>';
				echo '<td>' . $row['end_icao'] . ' ' . $row['end_name'] . '</td>';
				echo '<td align="center">' . $row['gc_bearing'] . ' deg</td>';
				echo '<td align="center">' . number_format($row['gcd_nm'], 0) . ' nm</td>';
				echo '<td align="center">' . number_format($row['altitude'], 0) . ' ft</td>';

				echo '</tr>';
			}

			$i++;
		}

		?>

        </tbody>
    </table>

	<?php
	//check if able to fly to display submit code
	if ($pilot_rank >= $rank_id && count($aircraft_array) > 0) {

		echo '<br /><br />';

		$hidden = array('valid' => 'true');
		//$hidden = array('tour_id' => $id);
		//$hidden = array('fs_version' => $selected_version);
		//Start form output
		echo form_open('tours/details/' . $id . '/' . $selected_version, '', $hidden);

		echo form_dropdown('aircraft_id', $aircraft_array, $aircraft_id);
		echo '<input type="submit" class="form_button" value="Assign Tour" />';
		echo form_close();
	} elseif (count($aircraft_array) < 1) {

		echo '<br /><br />';
		echo '<div style="color: red; font-weight: bold;">No aircraft have been allowed for this tour. Please inform Management.</div>';

	} elseif ($this->session->userdata('logged_in') == 1) {

		echo '<br /><br />';
		echo '<div style="color: red; font-weight: bold;">You are not sufficient rank to fly this tour yet. please come back later.</div>';

	} else {

		echo '<br /><br />';
		echo '<div style="color: red; font-weight: bold;">You must be logged in to assign this tour.</div>';

	}

	echo '<br /><br />';

	echo '<div style="color: red; font-weight: bold;">' . $exception . '</div>';

	?>

</center>
<?php
//output requirements
echo '<h2>Requirements</h2>';
echo nl2br($requirements);
echo '<br /><br />';

?>



