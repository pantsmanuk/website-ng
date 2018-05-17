<div class="menu_sub">
	<?php

	echo '<a href="' . $base_url . 'fleet/aircraft/A">Current</a>';
	echo ' | ';
	echo '<a href="' . $base_url . 'fleet/aircraft/H">Historical</a>';

	$i = 2;
	foreach ($division_array as $row) {

		if ($i > 0) {
			echo ' | ';
		}

		echo '<a href="' . $base_url . 'fleet/aircraft/' . $row['id'] . '">' . $row['longname'] . '</a>';
		$i++;
	}

	?>
</div>
<br/><br/><br/>
<?php

echo '<div style="float: right;">';
echo '<img src="' . $tmpl_image_url . 'fleet/class' . $clss . '.gif" alt="Class ' . $clss . '" />';
echo '</div>';
?>


<?php

//handle phrase
if ($historical_set == 1) {
	$select_type = 'Historical Aircraft';
} elseif ($division_set == 1) {
	$select_type = $division_name . ' Aircraft';
} else {
	$select_type = 'Current Aircraft';
}

$js = 'onchange="this.form.submit();"';
echo form_open('fleet/aircraft/' . $selected_division . '/' . $selected_aircraft);
echo form_dropdown('selected_aircraft', $aircraft_array, $selected_aircraft, $js);
echo form_submit('Submit', 'Select ' . $select_type);
echo form_close();
?>


<br/>
<br/>


<?php

//determine if image exists for aircraft
$aircraft_path = $assets_path . 'uploads/aircraft/' . $id . '/aircraft.jpg';

if (file_exists($aircraft_path)) {
	$aircraft_url = $assets_url . 'uploads/aircraft/' . $id . '/aircraft.jpg';
} else {
	$aircraft_url = $assets_url . 'uploads/aircraft/no-image.png';
}

echo '<img src="' . $aircraft_url . '" alt="' . $name . '" width="200" height="350" style="float: left; padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/>';
?>


<div style="float: right; width: 270px;">

    <table border="0" cellspacing="5" width="270">
        <tr>
            <td align="center">
				<?php
				$img_url = $assets_url . 'uploads/aircraft/common/charts/' . $clss . '/climb' . $clss . '.jpg';
				if (is_array(@getimagesize($img_url))) {
					echo '<a href="' . $img_url . '" rel="lightbox[charts]"  title="' . $name . ' Climb Chart">';
					echo '<img src="' . $assets_url . 'uploads/aircraft/common/graph1.jpg" alt="Climb Chart" />';
					?>
                    <br/>
                    Climb Chart
                    </a>
					<?php
				}
				?>
            </td>
            <td align="center">
				<?php
				$img_url = $assets_url . 'uploads/aircraft/common/charts/' . $clss . '/descend' . $clss . '.jpg';
				if (is_array(@getimagesize($img_url))) {
					echo '<a href="' . $img_url . '" rel="lightbox[charts]" title="' . $name . ' Descent Chart">';
					echo '<img src="' . $assets_url . 'uploads/aircraft/common/graph2.jpg" alt="Descent Chart" />';
					?>
                    <br/>
                    Descent Chart
                    </a>
					<?php
				}
				?>
            </td>
        </tr>
    </table>

    <div align="center"
         style="font-weight: bold; font-size: 1.6em; line-height: 1.6em; background-color: #<?php echo $division_colour; ?>; color: #<?php echo $division_text_colour; ?>;">
		<?php
		foreach ($aircraft_numbers as $row) {
			echo $row->num_aircraft . ' ' . $row->state . '<br />';
		}

		?>
		<?php if ($engine_manufacturer != '') {
			echo $engine_manufacturer;
		} else {
			echo '?';
		} ?> engines<br/>
		<?php if ($range_mload != '') {
			echo $range_mload;
		} else {
			echo '?';
		} ?> nm range<br/>
		<?php if ($pax != '') {
			echo number_format($pax);
		} else {
			echo '?';
		} ?> pax,
		<?php if ($cargo != '') {
			if ($this->format_fns->lbs_tonnes($cargo) < 1) {
				echo number_format($cargo) . ' lbs';
			} else {
				echo $this->format_fns->lbs_tonnes($cargo) . ' t';
			}
		} else {
			echo '?';
		} ?> cargo capacity
    </div>

    <br/>

	<?php

	echo nl2br(htmlspecialchars($description));
	?>
    <br/><br/>

</div>

<table border="0" cellpadding="0" cellspacing="0" width="300">
    <tr>
        <td colspan="2" align="center" bgcolor="#<?php echo $division_colour; ?>" valign="top">
            <font color="#ffffff" face="Verdana" size="2">
                <b>
                    <center>General Information</center>
                </b>
            </font></td>


    </tr>
    <tr>
        <td align="left">Length</td>
        <td align="left"><?php if ($length > 0) {
				echo ($length / 100) . ' m';
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Wingspan</td>
        <td align="left"><?php if ($wingspan > 0) {
				echo ($wingspan / 100) . ' m';
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Height</td>
        <td align="left"><?php if ($height > 0) {
				echo ($height / 100) . ' m';
			} else {
				echo '?';
			} ?></td>
    </tr>

    <tr>
        <td align="left">Engine</td>
        <td align="left"><?php if ($engine != '') {
				echo $engine;
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Cruise Speed</td>
        <td align="left"><?php if ($cruise_speed != '') {
				echo $cruise_speed . ' kts';
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Service Ceiling</td>
        <td align="left"><?php if ($service_ceiling != '') {
				echo number_format($service_ceiling) . ' ft';
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Gross Weight</td>
        <td align="left"><?php if ($gross_weight > 0) {
				echo ($gross_weight / 100) . ' t';
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Capacity</td>
        <td align="left"><?php if ($pax != '') {
				echo $pax . ' pax | ';
			} else {
				echo '? | ';
			} ?><?php if ($cargo != '') {
				if ($this->format_fns->lbs_tonnes($cargo) < 1) {
					echo $cargo . ' lbs';
				} else {
					echo $this->format_fns->lbs_tonnes($cargo) . ' t';
				}
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Crew</td>
        <td align="left"><?php if ($crew != '') {
				echo $crew;
			} else {
				echo '?';
			} ?></td>
    </tr>

    <tr>
        <td align="left">Price</td>
        <td align="left"><?php if ($price > 0) {
				echo round(($price / 1000000), 1) . 'M EUR';
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Manufacturer</td>
        <td align="left"><?php if ($manufacturer != '') {
				echo $manufacturer;
			} else {
				echo '?';
			} ?></td>
    </tr>
    <tr>
        <td align="left">Pictures</td>
        <td align="left"><a href="http://www.airliners.net/search/photo.search?search_active=1&q=<?php echo $name; ?>"
                            target="top"
                            style="color: rgb(100, 132, 203); font-family: verdana; font-size: 11px; text-decoration: none;"><b>Airliners.net</b></a>
        </td>
    </tr>

    <tr>
        <td colspan="2" align="center" bgcolor="#<?php echo $division_colour; ?>" valign="top">
            <font color="#ffffff" face="Verdana" size="2">
                <b>
                    <center>Operating Information</center>
                </b>
            </font></td>
    </tr>
    <td align="left" valign="top">

        <tr>
            <td align="left">OEW</td>
            <td align="left"><?php if ($oew > 0) {
					echo number_format($oew) . ' lbs';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">MTOW</td>
            <td align="left"><?php if ($mtow > 0) {
					echo number_format($mtow) . ' lbs';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Fuel Capacity</td>
            <td align="left"><?php if ($fuel_capacity > 0) {
					echo number_format($fuel_capacity) . ' gal';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Fuel Weight</td>
            <td align="left"><?php if ($fuel_weight > 0) {
					echo number_format($fuel_weight) . ' lbs';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Long Range Altitude</td>
            <td align="left"><?php if ($long_range_altitude != '') {
					echo $long_range_altitude;
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Long Range Speed</td>
            <td align="left"><?php if ($long_range_speed > 0) {
					echo $long_range_speed . ' kts';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Max Speed</td>
            <td align="left"><?php if ($max_speed > 0) {
					echo $max_speed . ' kts';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Range (MLoad...MFuel)</td>
            <td align="left">    <?php if ($range_mload > 0) {
					echo number_format($range_mload);
				} else {
					echo '?';
				} ?>...
				<?php if ($range_mfuel > 0) {
					echo number_format($range_mfuel);
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Engine Thrust</td>
            <td align="left"><?php if ($engine_thrust != '') {
					echo $engine_thrust;
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Takeoff RWY Length (Min)</td>
            <td align="left"><?php if ($to_rwy_length_min > 0) {
					echo number_format($to_rwy_length_min) . ' ft';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Takeoff RWY Length (Max)</td>
            <td align="left"><?php if ($to_rwy_length_max > 0) {
					echo number_format($to_rwy_length_max) . ' ft';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">Landing RWY Length</td>
            <td align="left"><?php if ($land_rwy_length > 0) {
					echo number_format($land_rwy_length) . ' ft';
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">V Rotate / Flaps</td>
            <td align="left"><?php if ($v_rotate != '') {
					echo $v_rotate;
				} else {
					echo '?';
				} ?>kts / <?php if ($flaps_rotate != '') {
					echo $flaps_rotate;
				} else {
					echo '?';
				} ?></td>
        </tr>
        <tr>
            <td align="left">V Approach / Flaps</td>
            <td align="left"><?php if ($v_approach != '') {
					echo $v_approach;
				} else {
					echo '?';
				} ?>kts / <?php if ($flaps_approach != '') {
					echo $flaps_approach;
				} else {
					echo '?';
				} ?></td>
        </tr>

        <tr>
            <td align="left">Max climb rate</td>
            <td align="left"><?php if ($maximum_climb_rate != '') {
					echo $maximum_climb_rate;
				} else {
					echo '?';
				} ?> ft/min
            </td>
        </tr>
        <tr>
            <td align="left">Max descent rate</td>
            <td align="left"><?php if ($maximum_desc_rate != '') {
					echo '-' . $maximum_desc_rate;
				} else {
					echo '?';
				} ?> ft/min
            </td>
        </tr>

</table>


<div style="page-break-before: always;">&nbsp;</div>
<?php

$current_class = $this->session->userdata('rank_id') + 1;

if ($clss > $current_class) {
	echo 'You will be able to fly this aircraft in ' . ($clss - $current_class) . ' promotions<br /><br />';
}

//restrict downloads to current class+1

?>

<br/><br/>
<div class="container">
    <table width="100%" cellspacing="5">
        <tr>
            <td colspan="3"><h1>Liveries and Downloads</h1></td>
        </tr>

		<?php
		//downloads
		if ($num_downloads > 0) {

			$i = 0;
			$last_sim = '';
			foreach ($downloads_data as $row) {

//determine if image exists for aircraft
				$aircraft_path = $assets_path . 'uploads/aircraft/' . $row->aircraft_id . '/' . strtolower($row->type) . '-' . $row->id;

				if (file_exists($aircraft_path . '.jpg')) {
					$aircraft_url = $assets_url . 'uploads/aircraft/' . $row->aircraft_id . '/' . strtolower($row->type) . '-' . $row->id . '.jpg';
					$aircraft_path = $aircraft_path . '.jpg';

				} elseif (file_exists($aircraft_path . '.png')) {
					$aircraft_url = $assets_url . 'uploads/aircraft/' . $row->aircraft_id . '/' . strtolower($row->type) . '-' . $row->id . '.png';
					$aircraft_path = $aircraft_path . '.png';
				} elseif (file_exists($aircraft_path . '.gif')) {
					$aircraft_url = $assets_url . 'uploads/aircraft/' . $row->aircraft_id . '/' . strtolower($row->type) . '-' . $row->id . '.gif';
					$aircraft_path = $aircraft_path . '.gif';
				} else {
					$aircraft_url = $assets_url . 'uploads/aircraft/file-no-image.jpg';
					$aircraft_path = $assets_path . 'uploads/aircraft/file-no-image.jpg';
				}

				//recalculate width and height to be no more than 80 tall.

				list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($aircraft_path);

				//$height_new = 80;
				//$width_new = $width_orig / $height_orig * $height_new;

				$width_new = 250;
				$height_new = $height_orig / $width_orig * $width_new;

				//if first, or a new set, close off last and output new flight sim section
				if ($i == 0 || ($last_sim != '' && $last_sim != $row->version_name)) {
					echo '<tr><td colspan="3"><h3>' . $row->version_name . '</h3><hr /></td></tr>';
				}
				echo '<tr valign="top">';
				echo '<td><a href="' . $aircraft_url . '" rel="lightbox[' . $row->version_name . ']"  title="' . $name . ' ' . $row->type . ' for ' . $row->series_name . '"><img src="' . $aircraft_url . '" alt="' . $name . '" width="' . $width_new . '" height="' . $height_new . ' style="float: left; padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></a></td>';
				echo '<td>' . $row->description . '</td>';
				if ($row->model != '') {
					$row->model = '(' . $row->model . ')';
				}
				if ($row->payware == 1) {
					$payware = '<br />Payware';
				} else {
					$payware = '';
				}
				echo '<td align="center" width="150">';
				if ($this->session->userdata('admin_cp') == 1) {
					echo '<span style="float:right;"><a href="' . $base_url . 'acp_fleet/downloads_edit/' . $selected_aircraft . '/' . $row->id . '/">
	<img src="' . $image_url . 'icons/application/database_edit.png" alt="Edit" /></a></span>';
				}
				echo $row->version_name . ' ' . $row->model . $payware . '<br /><a href="' . $row->location . '"><img src="' . $image_url . 'icons/diskette.jpg" /></a></td>';
				echo '</tr>';
				//determine if there is an image
				$last_sim = $row->version_name;
				$i++;
			}
		} else {

			echo '<tr valign="middle">';
			echo '<td align="center"><h1>No downloads available</h1></td>';
			echo '</tr>';

		}

		?>

    </table>
</div>

