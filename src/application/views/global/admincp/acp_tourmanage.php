<?php
//array for enabled disabled
$yesno_array = array('0' => 'N', '1' => 'Y');?>
<center>
<table border="0">
<tr>  
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png" alt="Edit" /></td>
<td align="left">Edit</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_table.png" alt="Legs" /></td>
<td align="left">Legs</td>
<td width="25" align="center"><a href="<?php echo $base_url.'acp_tours/edit/0'; ?>"><img src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url.'acp_tours/edit/0'; ?>">Add New</a></td>
</tr>
</table>     

<br />
<table border="0" width="100%">
<tr>  
<td align="left">
<?php
$hidden['valid'] = 'true';
echo form_open('acp_tours/manage/'.$system_restrict.'/', '', $hidden);
echo form_input($search);
echo form_submit('Submit', 'Search');
echo form_close();
?>
</td>

<td align="right">
<?php
echo form_open('acp_tours/manage/'.$system_restrict.'/', '', $hidden);
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
<th>name</th>
<th>author</th>
<th>class</th>

<th>&nbsp;</th>
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
			echo '<td align="left">'.$row->name.'</td>';
			echo '<td align="left">'.$row->author.'</td>';
			echo '<td align="center" width="5">'.$row->clss.'</td>';
			
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_tours/edit/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_tours/legs/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_table.png" alt="Legs" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_tours/aircraft/'.$row->id.'">
			<img src="'.$image_url.'icons/application/aircraft.png" alt="Aircraft" /></a></td>';
			
			
			
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
