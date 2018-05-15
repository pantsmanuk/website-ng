<?php
//javascript
//$bb_java = js_insert_bbcode('news_text');
$bb_buttons = get_bbcode_buttons();
//echo $bb_java;
$attr = array('id' => 'news');
$hidden = array('valid' => 'true');
echo form_open_multipart('acp_news/edit/'.$news_id,$attr,$hidden);


echo $highlight1.$error.$highlight2;


//determine if image exists for image
$image_path = $assets_path.'uploads/news/'.$news_id;

if (file_exists($image_path.'.gif')) {
	$image_url = $assets_url.'uploads/news/'.$news_id.'.gif';
	$image_path = $assets_path.'uploads/news/'.$news_id.'.gif';
}
elseif (file_exists($image_path.'.png')) {
	$image_url = $assets_url.'uploads/news/'.$news_id.'.png';
	$image_path = $assets_path.'uploads/news/'.$news_id.'.png';
}
elseif (file_exists($image_path.'.jpg')) {
	$image_url = $assets_url.'uploads/news/'.$news_id.'.jpg';
	$image_path = $assets_path.'uploads/news/'.$news_id.'.jpg';
}
else{
	$image_url = $assets_url.'uploads/news/no-image.jpg'; 
	$image_path = $assets_path.'uploads/news/no-image.jpg';
}


//recalculate width and height

list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);


$height_new = 70;
$width_new = $width_orig / $height_orig * $height_new;


//required set

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>'; 
echo '<label for="news_title">Title</label>'.form_input($news_title).'<br /><br />';
echo '<div align="center">';
echo '<label for="news_text">Content</label><br /><br />';
foreach($bb_buttons as $button){

	echo $button;	
	
}


echo form_textarea($news_text).'<br />';
echo '</div><br />';
echo '<label for="start">Start Date</label>'	.form_dropdown('news_start_day', $day_array, $news_start_day)
										.form_dropdown('news_start_month', $month_array, $news_start_month)
										.form_dropdown('news_start_year', $year_array, $news_start_year)
										.'<br />';
										
echo '<label for="start">End Date</label>'	.form_dropdown('news_end_day', $day_array, $news_end_day)
										.form_dropdown('news_end_month', $month_array, $news_end_month)
										.form_dropdown('news_end_year', $year_array, $news_end_year)
										.' (Leave blank to not set)<br />';


echo '<center><img src="'.$image_url.'" alt="Image" width="'.$width_new.'" height="'.$height_new.'" style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></center><br />';
echo '<label for="userfile">Image</label><input type="file" name="userfile" size="20" /> <br /><center>'.$allowed_types.' (max: '.$max_width.'x'.$max_height.' '.$max_size.'k)</center>';

echo '</fieldset>';


echo '<center>'.form_submit('submit', 'Submit').'</center>';
echo form_close();
?>