<?php
//array for enabled disabled
$yesno_array = array('0' => 'N', '1' => 'Y');
?>
<center>
<table border="0">
<tr>  
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png" alt="Edit" /></td>
<td align="left">Edit</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_save.png" alt="Downloads" /></td>
<td align="left">Downloads</td>
<td width="25" align="center"><a href="<?php echo $base_url.'acp_fleet/edit/0'; ?>"><img src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url.'acp_fleet/edit/0'; ?>">Add New</a></td>
</tr>
</table>     

<br />
<table border="0" width="100%">
<tr>  
<td align="left">
<?php
$hidden['valid'] = 'true';
echo form_open('acp_fleet/manage/'.$system_restrict.'/'.$division, '', $hidden);
echo form_input($search);
echo form_submit('Submit', 'Search');
echo form_close();
?>
</td>

<td align="right">
<?php
echo form_open('acp_fleet/manage/'.$system_restrict.'/'.$division, '', $hidden);
echo form_dropdown('division', $division_array, $division);
echo form_dropdown('system_restrict', $system_array, $system_restrict);
echo form_submit('Submit', 'Select');
echo form_close();
?>
</td>
</tr>
</table>      

  
<table class="boxed" width="100%">
<tr>
<td colspan="11"><span style="float: right;"><?php echo $this->pagination->create_links(); ?></span></td>
</tr>
<tr>
<?php // <th>id</th> ?>
<th>name</th>
<th>variant</th>
<th>class</th>
<th>division</th>
<th>current</th>
<th>enabled</th>
<th>charter</th>

<th>&nbsp;</th>
<th>&nbsp;</th>
</tr>
<?php
$i = 0;
foreach($result as $row){

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
		
		
		echo '<tr '.$bgcol.'>';
			//echo '<td width="20" align="center">'.$row->id.'</td>';
			echo '<td align="left">'.$row->name.'</td>';
			echo '<td align="center" width="5">'.$row->variant.'</td>';
			echo '<td align="center" width="5">'.$row->clss.'</td>';
			//echo '<td align="center" width="5">'.$row->pax.'</td>';
			//echo '<td align="center" width="5">'.$row->cargo.'</td>';
			echo '<td align="center" width="5">'.$row->division.'</td>';
			echo '<td align="center" width="5">'.$yesno_array[$row->in_fleet].'</td>';
			echo '<td align="center" width="5">'.$yesno_array[$row->enabled].'</td>';
			echo '<td align="center" width="5">'.$yesno_array[$row->charter].'</td>';
			//echo '<td align="center" width="5">'.$row->rank.'</td>';
			
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_fleet/edit/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_edit.png" alt="Query" /></a></td>';
			
			if($row->variant == ''){
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_fleet/downloads/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_save.png" alt="Downloads" /></a></td>';
			}
			
			
		echo '</tr>';
	}
$i++;
}

?>
<tr>
<td colspan="11"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span></td>
</tr>
</table>
</center>
