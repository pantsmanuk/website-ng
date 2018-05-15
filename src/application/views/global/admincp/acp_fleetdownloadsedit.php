<div class="container">
<table width="100%" cellspacing="5">
<tr>
<td colspan="3"><h1>Liveries and Downloads</h1></td>
</tr>
<tr>
<td colspan="3">
<?php 

	if($download_id == 0){
		$legend = 'Add new Download';
	}
	else{
		$legend = 'Edit Download';
	}
	
	
	
	//determine if image exists for aircraft
	$aircraft_path = $assets_path.'aircraft/'.$aircraft_id.'/'.strtolower($type_name).'-'.$download_id;

	if (file_exists($aircraft_path.'.jpg')) {
		$aircraft_url = $assets_url.'uploads/aircraft/'.$row->aircraft_id.'/'.strtolower($row->type).'-'.$row->id.'.jpg';
		$aircraft_path = $aircraft_path.'.jpg';
		
	}
	elseif(file_exists($aircraft_path.'.png')) {
		$aircraft_url = $assets_url.'uploads/aircraft/'.$row->aircraft_id.'/'.strtolower($row->type).'-'.$row->id.'.png';
		$aircraft_path = $aircraft_path.'.png';
	}
	elseif(file_exists($aircraft_path.'.gif')) {
		$aircraft_url = $assets_url.'uploads/aircraft/'.$row->aircraft_id.'/'.strtolower($row->type).'-'.$row->id.'.gif';
		$aircraft_path = $aircraft_path.'.gif';
	}
	else{
		$aircraft_url = $assets_url.'uploads/aircraft/file-no-image.jpg'; 
		$aircraft_path = $assets_path.'uploads/aircraft/file-no-image.jpg';
	}


	//recalculate width and height to be no more than 80 tall.
	
	list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($aircraft_path);
	
	
	$height_new = 80;
	$width_new = $width_orig / $height_orig * $height_new;
	
	
	

	$hidden = array('valid' => 'true');
	echo form_open_multipart('acp_fleet/downloads_edit/'.$aircraft_id.'/'.$download_id,'',$hidden);

	echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
	echo '<legend>'.$legend.'</legend>'; 
	echo '<label for="type">Type</label>'.form_dropdown('type', $type_array, $type).'<br />';
	echo '<label for="flight_sim_id">Flight Sim</label>'.form_dropdown('flight_sim_id', $sim_array, $flight_sim_id).'<br />';
	echo '<label for="model">Model</label>'.form_input($model).' eg: Aerosoft (optional)<br />';
	echo '<label for="location">Location</label>'.form_input($location).' (url to file)<br />';
	echo '<label for="payware">Payware</label>'.form_dropdown('payware', $payware_array, $payware).'<br />';
	echo '<center><img src="'.$aircraft_url.'" alt="Image" width="'.$width_new.'" height="'.$height_new.'" style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></center><br />';
	echo '<label for="userfile">Image</label><input type="file" name="userfile" size="20" /> '.$allowed_types.' (max: '.$max_width.'x'.$max_height.' '.$max_size.'k)';
	echo '<label for="description">Description</label>'.form_textarea($description).'(optional)<br />';
	echo '</fieldset>';
	
	echo '<center>'.form_submit('submit', 'Submit').'</center>';
	echo form_close();
	
?>
</td>
</tr>
</table>
</div>