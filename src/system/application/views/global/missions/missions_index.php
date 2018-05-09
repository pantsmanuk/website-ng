<div class="menu_sub">
<?php
$i = 0;
foreach($division_array as $row){

if($i > 0){ echo ' | '; }
	if($row['missions'] == 1){
		echo '<a href="'.$base_url.'missions/index/'.$row['id'].'">'.$row['longname'].'</a>'; 
	}
	else{
		echo '<a href="'.$base_url.'divisions/index/'.$row['id'].'">'.$row['longname'].'</a>'; 
	}
	
$i++;
}

?>
</div>
<br /><br /><br />

<div class="container">

<?php



		//determine if image exists for award
		$image_path = $assets_path.'uploads/divisions/'.$division_array[$selected_division_id]['id'].'/logo';
	
		if (file_exists($image_path.'.png')) {
			$image_url = $assets_url.'uploads/divisions/'.$division_array[$selected_division_id]['id'].'/logo'.'.png';
			$image_path = $assets_path.'uploads/divisions/'.$division_array[$selected_division_id]['id'].'/logo'.'.png';
		}
		elseif (file_exists($image_path.'.gif')) {
			$image_url = $assets_url.'uploads/divisions/'.$division_array[$selected_division_id]['id'].'/logo'.'.gif';
			$image_path = $assets_path.'uploads/divisions/'.$division_array[$selected_division_id]['id'].'/logo'.'.gif';
		}
		elseif (file_exists($image_path.'.jpg')) {
			$image_url = $assets_url.'uploads/divisions/'.$division_array[$selected_division_id]['id'].'/logo'.'.jpg';
			$image_path = $assets_path.'uploads/divisions/'.$division_array[$selected_division_id]['id'].'/logo'.'.jpg';
		}
		else{
			$image_url = $assets_url.'uploads/divisions/no-image.jpg'; 
			$image_path = $assets_path.'uploads/divisions/no-image.jpg';
		}


echo '<center><img src="'.$image_url.'" alt="'.$division_array[$selected_division_id]['longname'].' Logo" /></center><br />';
?>

<br /><br />



<div style="width: 100px; float:right;">
<?php
echo '<font size="3"><b>Resources</b></font>';
echo '<br /><br /><a href="'.$base_url.'divisions/index/'.$selected_division_id.'">Main</a>';
echo '<br /><a href="'.$base_url.'fleet/aircraft/'.$selected_division_id.'">Fleet</a>';
if($this->session->userdata('logged_in') == '1' && $division_array[$selected_division_id]['prim'] == 1){
echo '<br /><a href="'.$base_url.'dispatch/timetable/'.$selected_division_id.'/EGLL/3">Timetable</a>';
}
echo '<br />Missions';
?>
</div>

<div style="width: 500px; margin-left: 25px;">
<?php
echo '<font size="4"><b>'.$division_array[$selected_division_id]['longname'].' Missions</b></font>';
echo '<br /><font color="#cccccc">Special Missions';
$i = 0;
/*
foreach($class_array as $class_id => $class_name){

	//prepend if not first
	if($i > 0){
		echo ' | ';
	}

	echo '<a href="'.$base_url.'missions/index/'.$selected_division_id.'/'.$class_id.'">'.$class_name.'</a>';
$i++;
}
*/
echo '</font><br /><br />';


$i = 0;
foreach($mission_array as $clss => $row){

	echo '<h2>Class '.$clss.'</h2>';

	foreach($row as $mission){
	
	
	
		echo '<div style="border: #cccccc 1px solid; padding: 5px;">';
		echo '<div style="float:right; color: #bbbbbb;">Ends: '.date('d/m/Y', strtotime($mission['end_date'])).'</div>';
		echo '<b>'.$mission['title'].'</b>';
		echo '<br />';
		echo $mission['description'];
		
		echo '<br />';
		echo '<br />';
		echo '<b>Flight Details</b>';
		echo '<table border="0" cellpadding="0" cellspacing="0">';
		echo '<tr valign="top"><td width="120" align="right">Aircraft: </td><td><font color="#bbbbbb"><b>'.$mission['aircraft'].'</b></font></td>';
		echo '<td width="120" align="right">Flight Route: </td><td><font color="#bbbbbb"><b>'.$mission['start_icao'].' to '.$mission['end_icao'].'</b></font></td></tr>';
		
		echo '<tr valign="top"><td width="120" align="right">Departure Weather: </td><td colspan="3"><font color="#bbbbbb"><b>'.$mission['dep_weather'].'</b></font></td></tr>';
		echo '<tr valign="top"><td width="120" align="right">Arrival Weather: </td><td colspan="3">';
		echo '<font color="#bbbbbb"><b>'.$mission['arr_weather'].'</b></font>';
		
		echo '</td></tr>';
		echo '</table>';
		if($this->session->userdata('logged_in') == '1'){
			//display the assign flight button
			echo '<div style="float:right;"><a href="'.$base_url.'missions/assign/'.$mission['id'].'">Assign Flight</a></div><div class="clear"></div>';
		}
		echo '</div>';
		echo '<br />';
 
	
	}
	

$i++;
}

if($i == 0){
	echo 'There are either no currently active missions for this division or you have already assigned all available missions to your dispatch log.<br /><br />Please check back later, or notify management if you believe this to be an error.';
}

?>
</div>

<div class="clear"><!-- --></div>

</div>
<br />

