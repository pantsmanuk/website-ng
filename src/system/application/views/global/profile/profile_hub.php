<?php
$this->load->view('global/profile/profile_topbar_edit');
?>


<?php 

if($time_remaining <= 0){


	$hidden = array('valid' => 'true');
	
	//Start form output	
	echo form_open('profile/hub','',$hidden);

	if(isset($exception)){ 
		echo '<br /><center><div class="exception">'.$exception.'</div></center>'; 
	}
	else{ 
		echo "<br />"; 
	}  

}
?>
<br />

<div align="center">

<table class="statbox" width="400">
<tr>
<th colspan="2">Request hub transfer</th>

</tr>


<?php
if($time_remaining <= 0){
?>
<tr>
<td width="250"><div align="right">Hub:</div></td> <td width="150"><div align="left"><?php echo form_dropdown('hub', $hub_array, $hub); ?></div></td>
</tr>
<?php
}
else{

//calculate days, hours from time remaining


?>
<tr>
<td width="250"><div align="right">Able to request in:</div></td> <td width="150"><div align="center"><?php echo number_format($time_remaining/60/60/24, 0).' days'; ?></div></td>
</tr>
<?php
}
?>

<tr>
<td width="250" colspan="2"><div>Normally, your hub transfer request would be placed into a queue to be approved by the respective hub manager. However, currently, all transfers are approved immediately. You can make one transfer request a month</div></td></td>
</tr>


</table>
<br />
<br />
<table border="0" cellpadding="0" align="center" width="770">
<tr>
<td align="center">
<?php
if($time_remaining <= 0){
?>
<input type="submit" class="form_button" value="Submit" />
<?php
}
?>
</td>

</tr>
</table>
</div>
