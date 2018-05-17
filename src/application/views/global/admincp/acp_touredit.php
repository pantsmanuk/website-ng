<?php
$hidden = array(
	'valid' => 'true',
);
echo form_open_multipart('acp_tours/edit/' . $tour_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

//determine if image exists for image
$image_path = $assets_path . 'uploads/tours/' . $tour_id;

if (file_exists($image_path . '.gif')) {
	$image_url = $assets_url . 'uploads/tours/' . $tour_id . '.gif';
	$image_path = $assets_path . 'uploads/tours/' . $tour_id . '.gif';
} elseif (file_exists($image_path . '.png')) {
	$image_url = $assets_url . 'uploads/tours/' . $tour_id . '.png';
	$image_path = $assets_path . 'uploads/tours/' . $tour_id . '.png';
} elseif (file_exists($image_path . '.jpg')) {
	$image_url = $assets_url . 'uploads/tours/' . $tour_id . '.jpg';
	$image_path = $assets_path . 'uploads/tours/' . $tour_id . '.jpg';
} else {
	$image_url = $assets_url . 'uploads/tours/no-image.jpg';
	$image_path = $assets_path . 'uploads/tours/no-image.jpg';
}

//recalculate width and height

list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);

$height_new = 100;
$width_new = $width_orig / $height_orig * $height_new;

//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>';
echo '<label for="enabled">Status</label>' . form_dropdown('enabled', $enabled_array, $enabled) . '<br />';
echo '<label for="name">Name</label>' . form_input($name) . '<br />';
echo '<label for="author">Author</label>' . form_input($author) . '<br />';

echo '<label for="clss">Class</label>' . form_dropdown('clss', $clss_array, $clss) . '<br />';
echo '<center><img src="' . $image_url . '" alt="Image" width="' . $width_new . '" height="' . $height_new . '" style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></center><br />';
echo '<label for="userfile">Image</label><input type="file" name="userfile" size="20" /> <br /><center>' . $allowed_types . ' (max: ' . $max_width . 'x' . $max_height . ' ' . $max_size . 'k)</center>';
echo '<label for="length">Length</label>' . form_textarea($length) . '<br />';
echo '<label for="difficulty">Difficulty</label>' . form_textarea($difficulty) . '<br />';
echo '<label for="description">Description short</label>' . form_textarea($description) . '<br />';
echo '<label for="detail_info">Description full</label>' . form_textarea($detail_info) . '<br />';
echo '<label for="requirements">Requirements</label>' . form_textarea($requirements) . '<br />';

echo '</fieldset>';

echo '<center>' . form_submit('submit', 'Submit') . '</center>';
echo form_close();
?>