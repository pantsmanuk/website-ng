<?php
$hidden = array(
	'valid' => 'true',
);
echo form_open_multipart('acp_propilot/event_edit/' . $event_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

//determine if image exists for image
$image_path = $assets_path . 'uploads/events/' . $event_id;

if (file_exists($image_path . '.gif')) {
	$image_url = $assets_url . 'uploads/events/' . $event_id . '.gif';
	$image_path = $assets_path . 'uploads/events/' . $event_id . '.gif';
} elseif (file_exists($image_path . '.png')) {
	$image_url = $assets_url . 'uploads/events/' . $event_id . '.png';
	$image_path = $assets_path . 'uploads/events/' . $event_id . '.png';
} elseif (file_exists($image_path . '.jpg')) {
	$image_url = $assets_url . 'uploads/events/' . $event_id . '.jpg';
	$image_path = $assets_path . 'uploads/events/' . $event_id . '.jpg';
} else {
	$image_url = $assets_url . 'uploads/events/no-image.png';
	$image_path = $assets_path . 'uploads/events/no-image.png';
}

//recalculate width and height

list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);

$height_new = 80;
$width_new = $width_orig / $height_orig * $height_new;

//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>';
echo '<label for="name">Name</label>' . form_input($name) . '<br />';
echo '<label for="clss">Aircraft</label>' . form_dropdown('aircraft_id', $aircraft_array, $aircraft_id) . '<br />';
echo '<label for="start">Start Date</label>' . form_dropdown('start_day', $day_array, $start_day)
	. form_dropdown('start_month', $month_array, $start_month)
	. form_dropdown('start_year', $year_array, $start_year)
	. '<br />';
echo '<center><img src="' . $image_url . '" alt="Image" width="' . $width_new . '" height="' . $height_new . '" style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></center><br />';
echo '<label for="userfile">Image</label><input type="file" name="userfile" size="20" /> <br /><center>' . $allowed_types . ' (max: ' . $max_width . 'x' . $max_height . ' ' . $max_size . 'k)</center>';
echo '<label for="difficulty">Difficulty</label>' . form_textarea($difficulty) . '<br />';
echo '<label for="description">Description short</label>' . form_textarea($description) . '<br />';
echo '<label for="clss">Active</label>' . form_dropdown('active', $active_array, $active) . '<br />';

echo '</fieldset>';

echo '<center>' . form_submit('submit', 'Submit') . '</center>';
echo form_close();
?>