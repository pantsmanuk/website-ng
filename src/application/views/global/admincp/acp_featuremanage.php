<center>
<table border="0">
<tr>  
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png" alt="Edit" /></td>
<td align="left">Edit</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_delete.png" alt="Edit" /></td>
<td align="left">Delete</td>
<td width="25" align="center"><a href="<?php echo $base_url; ?>acp_feature/edit/0"><img src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url; ?>acp_feature/edit/0">Add New</a></td>
</tr>
</table>     



<br />
<table border="0" width="100%">
<tr>  

<td align="right">
<?php
$hidden['valid'] = 'true';
$js = 'onchange="this.form.submit();"';
echo form_open('acp_feature/manage/'.$system_restrict, '', $hidden);
echo form_dropdown('system_restrict', $system_array, $system_restrict, $js);
echo form_submit('Submit', 'Select');
echo form_close();
?>
</td>
</tr>
</table>      

  
<table class="boxed" width="100%">
<tr>
<td colspan="11"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span></td>
</tr>
<tr>
<?php //<th>id</th> ?>
<th>type</th>
<th>url</th>
<th>enabled</th>
<th>order</th>

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
			if($row->type == 'image'){
				echo '<td align="center" width="25"><img src="'.$image_url.'icons/application/image.png" alt="Image" /></td>';
			}
			elseif($row->type == 'video'){
				echo '<td align="center" width="25"><img src="'.$image_url.'icons/application/monitor.png" alt="Video" /></td>';
			}
			else{
				echo '<td align="left" width="45">'.$row->type.'</td>';
			}
			
			
			echo '<td align="left">'.$row->uri.'</td>';
			if($row->enabled == '1'){
				echo '<td align="center" width="5">Y</td>';
			}
			else{
				echo '<td align="center" width="5"><font color="red">N</font></td>';
			}
			
			
			echo '<td align="center" width="5">'.$row->order.'</td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_feature/edit/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_feature/delete/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></a></td>';
			
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
