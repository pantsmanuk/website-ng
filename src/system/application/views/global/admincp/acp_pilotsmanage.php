<center>
<table border="0">
<tr>  
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png" alt="Edit" /></td>
<td align="left">Edit</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_error.png" alt="Hours" /></td>
<td align="left">Recalculate hours</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/key.png" alt="Password" /></td>
<td align="left">Password</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_lightning.png" alt="Awards" /></td>
<td align="left">Awards</td>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_delete.png" alt="Delete" /></td>
<td align="left">Delete</td>
<td width="25" align="center" bgcolor="#ffffbb">&nbsp;</td>
<td align="left">Email unconfirmed</td>
</tr>
</table>     

<br />
<table border="0" width="100%">
<tr>  
<td align="left">
<?php
$hidden['valid'] = 'true';
echo form_open('acp_pilots/manage/'.$status_restrict, '', $hidden);
echo form_input($search);
echo form_submit('Submit', 'Search');
echo form_close();
?>
</td>

<td align="right">
<?php
echo form_open('acp_pilots/manage/'.$status_restrict, '', $hidden);
echo form_dropdown('status_restrict', $status_array, $status_restrict);
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
<th>rank</th>
<th>username</th>
<th>pilot</th>
<th>usergroup</th>
<th>acp</th>
<th>status</th>
<th>joined</th>
<th>last flew</th>
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
		
		//handle email confirmed
		if($row->email_confirmed != '1'){
			$bgcol = 'bgcolor="#ffffbb"';
		}
		
		/*
		$status = 'Unchecked';
		if($row->checked == '3'){
			$bgcol = 'bgcolor="#ffffbb"';
			$status = 'Queried';
		}
		elseif($row->checked == '4'){
			$bgcol = 'bgcolor="#ffbb99"';
			$status = 'Responded';
		}
		*/
		echo '<tr '.$bgcol.'>';
			//echo '<td width="20" align="center">'.$row->id.'</td>';
			echo '<td width="20" align="center">'.$row->rank.'</td>';
			echo '<td width="55">EHM-'.$row->username.'</td>';
			echo '<td>'.$row->fname.' '.$row->sname.'</td>';
			if($row->usergroup_name != ''){
				echo '<td align="center" width="50">'.$row->usergroup_name.'</td>';
			}
			else{
				echo '<td align="center" width="50">-</td>';
			}
			
			if($row->admin_cp == 1){
				echo '<td align="center" width="50">Yes</td>';
			}
			else{
				echo '<td align="center" width="50">-</td>';
			}
			echo '<td align="center" width="50">'.$row->status.'</td>';
			echo '<td width="55" align="center">'.gmdate('d/m/Y',strtotime($row->signupdate)).'</td>';
			if($row->lastflight != '0000-00-00'){
				echo '<td width="55" align="center">'.gmdate('d/m/Y',strtotime($row->lastflight)).'</td>';
			}
			else{
				echo '<td width="55" align="center"><b>Never</b></td>';
			}
			
			echo'<td align="center" width="20"><a href="'.$base_url.'/profile/flightlog/'.$row->id.'/e">
			<img src="'.$image_url.'icons/application/application_view_detail.png" alt="View Flights" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_pilots/edit/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'cron/recalculatehours/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_error.png" alt="Edit" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_pilots/credentials/'.$row->id.'">
			<img src="'.$image_url.'icons/application/key.png" alt="Password" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_pilots/awards/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_lightning.png" alt="Awards" /></a></td>';
			
			echo'<td align="center" width="20"><a href="'.$base_url.'acp_pilots/delete/'.$row->id.'">
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
