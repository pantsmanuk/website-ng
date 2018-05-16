<?php
$this->load->view('global/profile/profile_topbar_edit');
?>


<?php 
	$hidden = array('valid' => 'true');
	
	//Start form output	
	echo form_open('profile/email','',$hidden);

if(isset($exception)){ 
	echo '<br /><center><div class="exception">'.$exception.'</div></center>'; 
}
else{ 
	echo "<br />"; 
}  
?>
<br />

<div align="center">

<table class="statbox" width="400">
<tr>
<th colspan="2">Email Address Change</th>

</tr><tr>

<td width="250"><div align="right">Current Password:</div></td> <td width="150"><div align="left"><?php echo form_password($oldpassword); ?></div></td>

</tr><tr>
<?php
$js = 'onKeyUp="checkMatch(\'Email Addresses match\', \'Email Addresses do not match\')"';
?>
<td width="250"><div align="right">New Email:</div></td> <td width="150"><div align="left"><?php echo form_input($checkfield1, '', $js); ?></div></td>

</tr><tr>

<td width="250"><div align="right">New Email (again):</div></td> <td width="150"><div align="left"><?php echo form_input($checkfield2, '', $js); ?></div></td>

</tr><tr>
<td colspan="2" align="center">
<div id="credmatch" style="background: green; font-weight: bold; color: #FFF; line-height: 20px;">Email Addresses Match</div>
</td>
</tr>

<tr>
<td colspan="2">
<span>If you change your email address, you will need to re-validate it and cannot fly for the VA until the address is confirmed.</span>
<span>If you need to resend your confirmation email, re-enter your email address here.</span>
</td>
</tr>

</table>
<br />
<br />
<table border="0" cellpadding="0" align="center" width="770">
<tr>
<td align="center">
<input type="submit" class="form_button" value="Submit" />
</td>

</tr>
</table>
</div>
