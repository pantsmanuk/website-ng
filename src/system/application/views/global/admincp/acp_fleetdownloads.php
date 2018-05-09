<div class="container">
<table width="100%" cellspacing="5">
<tr>
<td colspan="3"><h1>Liveries and Downloads</h1></td>
</tr>
<tr>
<td colspan="3">
<center>
<table border="0">
<tr>  
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png" alt="Edit" /></td>
<td align="left">Edit</td>
<td width="25" align="center"><a href="<?php echo $base_url.'acp_fleet/downloads_edit/'.$aircraft_id.'/0/'; ?>"><img src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url.'acp_fleet/downloads_edit/'.$aircraft_id.'/0/'; ?>">Add New</a></td>
</tr>
</table> 
</center>    
</td>
</tr>
<?php
//downloads
if($num_downloads > 0){

$i=0;
$last_sim = '';
foreach($downloads_data as $row){

//determine if image exists for aircraft
$aircraft_path = $assets_path.'uploads/aircraft/'.$row->aircraft_id.'/'.strtolower($row->type).'-'.$row->id;

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
	


	//if first, or a new set, close off last and output new flight sim section
	if($i == 0 || ($last_sim != '' && $last_sim != $row->series_name)){
		echo '<tr><td colspan="3"><h3>'.$row->series_name.'</h3><hr /></td></tr>';
	}
	echo '<tr valign="middle">';
	echo '<td><img src="'.$aircraft_url.'" alt="'.$row->name.'" width="'.$width_new.'" height="'.$height_new.'" style="float: left; padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"/></td>';
	echo '<td>'.$row->description.'</td>';
	if($row->model != ''){ $row->model = '('.$row->model.')'; }
	if($row->payware == 1){ $payware = '<br />Payware'; }else{ $payware = ''; }
	echo '<td align="center" width="100"><span style="float:right;">
	<a href="'.$base_url.'acp_fleet/downloads_edit/'.$aircraft_id.'/'.$row->id.'/">
	<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></span>'
	.$row->version_name.' '.$row->model.$payware.'<br /><a href="'.$row->location.'"><img src="'.$image_url.'icons/diskette.jpg" /></a></td>';
	echo '</tr>';
	//determine if there is an image
	$last_sim = $row->series_name;
	$i++;
	}
}
else{

	echo '<tr valign="middle">';
	echo '<td align="center"><h1>No downloads available</h1></td>';
	echo '</tr>';

}

?>

</table>
</div>