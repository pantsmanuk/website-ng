<?php
//array for enabled disabled
$yesno_array = array('0' => 'N', '1' => 'Y');



?>
<center>
<table border="0">
<tr>  
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png" alt="Edit" /></td>
<td align="left">Edit</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_delete.png" alt="Delete" /></td>
<td align="left">Delete</td>
<td width="25" align="center"><a href="<?php echo $base_url.'acp_timetables/edit/0'; ?>"><img src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url.'acp_timetables/edit/0'; ?>">Add New</a></td>
</tr>
</table>     

<br />
<table border="0" width="100%">
<tr>  
<td align="left">
<?php
$hidden['valid'] = 'true';
echo form_open('acp_timetables/manage/'.$system_restrict.'/'.$division.'/'.$hub_restrict, '', $hidden);
echo form_input($search);
echo form_submit('Submit', 'Search');
echo form_close();
?>
</td>

<td align="right">
<?php
echo form_open('acp_timetables/manage/'.$system_restrict.'/'.$division.'/'.$hub_restrict, '', $hidden);
echo form_dropdown('division', $division_array, $division);
echo form_dropdown('system_restrict', $system_array, $system_restrict);
echo form_dropdown('hub_restrict', $hub_array, $hub_restrict);
echo form_submit('Submit', 'Select');
echo form_close();
?>
</td>
</tr>
</table>      

  
<table class="boxed" width="100%">
<tr>
<td colspan="12"><span style="float: right;"><?php echo $this->pagination->create_links(); ?></span></td>
</tr>

<tr>
<?php //<th>id</th> ?>
<th colspan="7"></th>
<th colspan="2">Season</th>
<th colspan="3">&nbsp;</th>
</tr>

<tr>
<?php //<th>id</th> ?>
<th>Flight #</th>
<th>Hub</th>
<th>Origin</th>
<th>Dest</th>
<th>Departs</th>
<th>Arrives</th>
<th>Division</th>
<th>Start</th>
<th>End</th>
<th>Active</th>

<th>&nbsp;</th>
</tr>
<?php
$i = 0;
$csv = 'Flight #,Hub,origin,Dest,Departs,Arrives,Division,Season Start,Season End,Active'."\n";
foreach($result as $row){

//add all to the csv dump

	$csv .= '#'.$row->flightnumber;
	$csv .= ','.$row->hub;
	$csv .= ','.$row->dep_airport;
	$csv .= ','.$row->arr_airport;
	$csv .= ','.substr($row->dep_time,0,5);
	$csv .= ','.substr($row->arr_time,0,5);
	$csv .= ','.$row->division_shortname;
	//season start
	if($row->season_month_start != ''){
		$csv .= ','.$season_month_array[$row->season_month_start];
	}
	else{
		$csv .= ',-';
	}
	//season end
	if($row->season_month_end != ''){
		$csv .= ','.$season_month_array[$row->season_month_end];
	}
	else{
		$csv .= ',-';
	}
	//active
	if($row->active == 1){
		$csv .= ',Yes';
	}
	else{
		$csv .= ',-';
	}
	$csv .= "\n";
	

	if(is_numeric($offset) 
	&& $i >= $offset 
	&& $i < ($offset+$limit)
	){
		
		if($i%2 != 0){
			$bgcol = 'bgcolor="#f2f2f2"';
		}
		else{
			$bgcol = '';
		}
		
		//handle missing location data
		if($row->dep_lat == '' || $row->dep_lon == ''
		|| $row->arr_lat == '' || $row->arr_lon == ''){
			$bgcol = 'bgcolor="#760606" style="color:#ffffff;"';
		}
		
		
		echo '<tr '.$bgcol.'>';
			//echo '<td width="20" align="center">'.$row->id.'</td>';
			echo '<td align="left" width="5">'.$row->flightnumber.'</td>';
			if($row->hub != ''){
				echo '<td align="center" width="5">'.$row->hub.'</td>';
			}
			else{
				echo '<td align="center" width="5">-</td>';
			}
			echo '<td align="center" width="5">'.$row->dep_airport.'</td>';
			echo '<td align="center" width="5">'.$row->arr_airport.'</td>';
			echo '<td align="center" width="5">'.substr($row->dep_time,0,5).'</td>';
		echo '<td align="center" width="5">'.substr($row->arr_time,0,5).'</td>';
			echo '<td align="center" width="5">'.$row->division_shortname.'</td>';
			
			//season start
			if($row->season_month_start != ''){
				echo '<td align="center" width="5">'.$season_month_array[$row->season_month_start].'</td>';
			}
			else{
				echo '<td align="center" width="5">-</td>';
			}
			
			//season end
			if($row->season_month_end != ''){
				echo '<td align="center" width="5">'.$season_month_array[$row->season_month_end].'</td>';
			}
			else{
				echo '<td align="center" width="5">-</td>';
			}
			
			//active
			if($row->active == 1){
				echo '<td align="center" width="5">Yes</td>';
			}
			else{
				echo '<td align="center" width="5">-</td>';
			}
			//echo '<td align="center" width="5">'.$yesno_array[$row->in_fleet].'</td>';
			//echo '<td align="center" width="5">'.$yesno_array[$row->enabled].'</td>';
			//echo '<td align="center" width="5">'.$row->rank.'</td>';
			
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_timetables/edit/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_timetables/delete/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></a></td>';
			
			
			
			
		echo '</tr>';
	}
$i++;
}

?>
<tr>
<td colspan="12"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span></td>
</tr>
</table>

<br />
<div align="right">
<?php
$hidden = array(
	'file_name' => 'ehm_timetable_dump_'.date('Y_m_d', time()).'.csv',
	'file_contents' => $csv,
);
echo form_open('documents/save/','',$hidden);
echo form_submit('submit', 'Save .csv');
echo form_close();
?>
</div>

</center>
