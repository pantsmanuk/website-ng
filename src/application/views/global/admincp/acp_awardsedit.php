
<?php
$hidden = array(
	'valid' => 'true',
	'automatic' => $automatic,
);
echo form_open_multipart('acp_awards/edit/'.$award_id,'',$hidden);


echo $highlight1.$error.$highlight2;


//determine if image exists for award
$image_path = $assets_path.'uploads/awards/'.$award_id;

if (file_exists($image_path.'.png')) {
	$image_url = $assets_url.'uploads/awards/'.$award_id.'.png';
	$image_path = $assets_path.'uploads/awards/'.$award_id.'.png';
}
elseif (file_exists($image_path.'.gif')) {
	$image_url = $assets_url.'uploads/awards/'.$award_id.'.gif';
	$image_path = $assets_path.'uploads/awards/'.$award_id.'.gif';
}
elseif (file_exists($image_path.'.jpg')) {
	$image_url = $assets_url.'uploads/awards/'.$award_id.'.jpg';
	$image_path = $assets_path.'uploads/awards/'.$award_id.'.jpg';
}
else{
	$image_url = $assets_url.'uploads/awards/no-image.png'; 
	$image_path = $assets_path.'uploads/awards/no-image.png';
}


//recalculate width and height

list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);


$height_new = 30;
$width_new = $width_orig / $height_orig * $height_new;


//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>'; 
echo '<label for="awardtype">Award type</label>'.form_input($awardtype).' eg: dc3airmail<br />';
echo '<label for="award_name">Award name</label>'.form_input($award_name).' eg: USA Air Mail Event<br /><br />';
echo '<label for="automatic">Automatic</label>'.form_dropdown('automatic', $yesno_array, $automatic).'<br />';
echo '<label for="tour">Tour</label>'.form_dropdown('tour', $bool_array, $tour).'<br />';
echo '<label for="event">Event</label>'.form_dropdown('event', $bool_array, $event).'<br />';
echo '<label for="aggregate_award_name">aggregate_award_name</label>'.form_input($aggregate_award_name).' eg: airmail (award groupname)<br />';
echo '<label for="aggregate_award_rank">aggregate_award_rank</label>'.form_input($aggregate_award_rank).' eg: 1 (award tier. Group+rank should be unique)<br />';
echo '<label for="blurb">Description</label>'.form_textarea($description).' <br /><center>Explain the award</center><br />';
echo '<center><img src="'.$image_url.'" alt="Image" width="'.$width_new.'" height="'.$height_new.'" style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></center><br />';
echo '<label for="userfile">Image</label><input type="file" name="userfile" size="20" /> <br /><center>'.$allowed_types.' (max: '.$max_width.'x'.$max_height.' '.$max_size.'k)</center>';
echo '</fieldset>';


echo '<center>'.form_submit('submit', 'Submit').'</center>';
echo form_close();
?>