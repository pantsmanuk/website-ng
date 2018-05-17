<?php
$hidden = array('valid' => 'true');
echo form_open_multipart('acp_fleet/edit/' . $aircraft_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>';
echo '<label for="name">Name</label>' . form_input($name) . '<br />';
echo '<label for="clss">Class</label>' . form_dropdown('clss', $clss_array, $clss) . '<br />';
echo '<label for="pax">Pax</label>' . form_input($pax) . '<br />';
echo '<label for="cargo">Cargo</label>' . form_input($cargo) . ' lbs<br />';
echo '<label for="division">Division</label>' . form_dropdown('division', $division_array, $division) . '<br />';
echo '<label for="in_fleet">In Fleet</label>' . form_dropdown('in_fleet', $bool_array, $in_fleet) . '<br />';
echo '<label for="enabled">Enabled</label>' . form_dropdown('enabled', $bool_array, $enabled) . '<br />';
echo '<label for="charter">Allow Charter</label>' . form_dropdown('charter', $bool_array, $charter) . '<br />';
echo '<label for="aircraft_type">Aircraft Type</label>' . form_dropdown('aircraft_type', $type_array, $aircraft_type) . '<br />';
echo '</fieldset>';

//determine if image exists for aircraft
$aircraft_path = $assets_path . 'uploads/aircraft/' . $aircraft_id . '/' . 'aircraft.jpg';

if (file_exists($aircraft_path)) {
	$aircraft_url = $assets_url . 'uploads/aircraft/' . $aircraft_id . '/aircraft.jpg';
} else {
	$aircraft_url = $assets_url . 'uploads/aircraft/no-image.png';
	$aircraft_path = $assets_path . 'uploads/aircraft/no-image.png';
}

//recalculate width and height to be no more than 80 tall.

list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($aircraft_path);

$height_new = 350;
$width_new = $width_orig / $height_orig * $height_new;

//image
echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Image</legend>';
echo '<center><img src="' . $aircraft_url . '" alt="Image" width="' . $width_new . '" height="' . $height_new . '" style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></center><br />';
echo '<label for="userfile">Image</label><input type="file" name="userfile" size="20" /> <br /><center>' . $allowed_types . ' (max: ' . $max_width . 'x' . $max_height . ' ' . $max_size . 'k)</center>';
echo '</fieldset>';

//description
echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Description</legend>';
echo '<label for="description">Description</label>' . form_textarea($description) . '<br />';
echo '</fieldset>';

//display data
echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Data</legend>';
echo '<label for="icao_code">ICAO Code</label>' . form_input($icao_code) . '<br />';
echo '<label for="variant">Variant</label>' . form_input($variant) . '<br />';
echo '<label for="crew">Crew</label>' . form_input($crew) . ' eg: 2 pilots, 1 air-host<br />';
echo '<label for="length">Length</label>' . form_input($length) . ' m<br />';
echo '<label for="wingspan">Wingspan</label>' . form_input($wingspan) . ' m<br />';
echo '<label for="height">Height</label>' . form_input($height) . ' m<br />';
echo '<label for="engine">Engine</label>' . form_input($engine) . ' eg: PW PT6A67D<br />';
echo '<label for="engine_manufacturer">Engine manufacturer</label>' . form_input($engine_manufacturer) . ' eg: Pratt & Whitney<br />';
echo '<label for="cruise_speed">Cruise speed</label>' . form_input($cruise_speed) . ' kts<br />';
echo '<label for="service_ceiling">Service ceiling</label>' . form_input($service_ceiling) . ' ft<br />';
echo '<label for="gross_weight">Gross weight</label>' . form_input($gross_weight) . ' t<br />';
echo '<label for="price">Price</label>' . form_input($price) . ' Million EUR<br />';
echo '<label for="manufacturer">Manufacturer</label>' . form_input($manufacturer) . ' eg: RAYTHEON<br />';
echo '<label for="oew">OEW</label>' . form_input($oew) . ' lbs<br />';
echo '<label for="mtow">mtow</label>' . form_input($mtow) . ' lbs<br />';
echo '<label for="fuel_capacity">Fuel capacity</label>' . form_input($fuel_capacity) . ' gal<br />';
echo '<label for="fuel_weight">Fuel weight</label>' . form_input($fuel_weight) . ' lbs<br />';
echo '<label for="long_range_altitude">Long range altitude</label>' . form_input($long_range_altitude) . ' eg: FL240<br />';
echo '<label for="long_range_speed">Long range speed</label>' . form_input($long_range_speed) . ' kts<br />';
echo '<label for="max_speed">Max speed</label>' . form_input($max_speed) . ' kts<br />';
echo '<label for="range_mload">Range mload</label>' . form_input($range_mload) . ' nm<br />';
echo '<label for="range_mfuel">Range mfuel</label>' . form_input($range_mfuel) . ' nm<br />';
echo '<label for="engine_thrust">Engine thrust</label>' . form_input($engine_thrust) . ' eg: 2x1280 HP<br />';
echo '<label for="to_rwy_length_min">to rwy length min</label>' . form_input($to_rwy_length_min) . ' ft<br />';
echo '<label for="to_rwy_length_max">to rwy length max</label>' . form_input($to_rwy_length_max) . ' ft<br />';
echo '<label for="land_rwy_length">land rwy length</label>' . form_input($land_rwy_length) . ' ft<br />';
echo '<label for="v_rotate">v_rotate</label>' . form_input($v_rotate) . ' kts<br />';
echo '<label for="v_approach">v_approach</label>' . form_input($v_approach) . ' kts<br />';
echo '<label for="flaps_rotate">flaps_rotate</label>' . form_input($flaps_rotate) . ' eg: 10 deg<br />';
echo '<label for="flaps_approach">flaps_approach</label>' . form_input($flaps_approach) . ' eg: 40 deg<br />';
echo '<label for="maximum_climb_rate">Max climb rate</label>' . form_input($maximum_climb_rate) . 'ft/min eg: 3600 (number only)<br />';
echo '<label for="maximum_desc_rate">Max desc rate</label>' . form_input($maximum_desc_rate) . 'ft/min eg: 3000 (number only)<br />';
echo '</fieldset>';

echo '<center>' . form_submit('submit', $mode . ' Aircraft') . '</center>';
echo form_close();
?>