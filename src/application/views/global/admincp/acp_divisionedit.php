<?php
$hidden = array('valid' => 'true');
echo form_open_multipart('acp_divisions/edit/'.$division_id,'',$hidden);


echo $highlight1.$error.$highlight2;


//determine if image exists for image
$image_path = $assets_path.'uploads/divisions/'.$division_id.'/'.'logo';

if (file_exists($image_path.'.gif')) {
	$image_url = $assets_url.'uploads/divisions/'.$division_id.'/'.'logo.gif';
	$image_path = $assets_path.'uploads/divisions/'.$division_id.'/'.'logo.gif';
}
elseif (file_exists($image_path.'.png')) {
	$image_url = $assets_url.'uploads/divisions/'.$division_id.'/'.'logo.png';
	$image_path = $assets_path.'uploads/divisions/'.$division_id.'/'.'logo.png';
}
elseif (file_exists($image_path.'.jpg')) {
	$image_url = $assets_url.'uploads/divisions/'.$division_id.'/'.'logo.jpg';
	$image_path = $assets_path.'uploads/divisions/'.$division_id.'/'.'logo.jpg';
}
else{
	$image_url = $assets_url.'uploads/divisions/no-image.jpg'; 
	$image_path = $assets_path.'uploads/divisions/no-image.jpg';
}


//recalculate width and height

list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);


$height_new = 100;
$width_new = $width_orig / $height_orig * $height_new;


//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>'; 
echo '<label for="division_shortname">Name (short)</label>'.form_input($division_shortname).' eg: Business<br />';
echo '<label for="division_longname">Name (full)</label>'.form_input($division_longname).' eg: Eurobusiness<br />';
echo '<label for="colour">Background colour</label>'.form_input($colour).' eg: 000000 (hex minus #)<br />';
echo '<label for="text">Text colour</label>'.form_input($text).' eg: FFFFFF (hex minus #)<br />';
echo '<label for="prefix">Prefix</label>'.form_input($prefix).' eg: M (Unique)<br />';
echo '<label for="primary">Primary</label>'.form_dropdown('primary', $bool_array, $primary).' (Has timetable, like main unlike tours)<br />';
echo '<label for="public">Public</label>'.form_dropdown('public', $bool_array, $public).' (Display division on site)<br />';
echo '<label for="blurb">Description</label>'.form_textarea($blurb).' <br /><center>shown on division page</center><br />';
echo '<center><img src="'.$image_url.'" alt="Image" width="'.$width_new.'" height="'.$height_new.'" style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></center><br />';
echo '<label for="userfile">Image</label><input type="file" name="userfile" size="20" /> <br /><center>'.$allowed_types.' (max: '.$max_width.'x'.$max_height.' '.$max_size.'k)</center>';
echo '<label for="description">Notes</label>'.form_textarea($description).' <br /><center>(optional)</center><br />';
echo '</fieldset>';


echo '<center>'.form_submit('submit', 'Submit').'</center>';
echo form_close();
?>