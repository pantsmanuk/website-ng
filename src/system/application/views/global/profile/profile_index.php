<?php
$this->load->view('global/profile/profile_topbar');
?>
<b>Personal Details</b>
<table width="100%" border="0" cellpadding="0" cellspacing="5">
<tr>
<td width="17%">Age:</td>
<td width="16%"><?php echo $age; ?></td>
<td width="17%">Location:</td>
<td width="16%"><?php echo '<img src="'.$image_url.'icons/flags/'.$this->session->userdata('country').'.gif" alt="'.$this->session->userdata('country').'" width="30" height="17" />' ?></td>
<td width="17%">Joined EHM:</td>
<td width="16%"><?php echo $joined; ?></td>
</tr>
<tr>
<td width="17%">Email:</td>
<td colspan="5"><?php echo $emailaddress; ?></td>
</tr>
<tr>
<td width="17%">Main Flight Sim:</td>
<td colspan="5"><?php echo $fsversion; ?></td>
</tr>


</table>

<br /><br />


<b>Status</b>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="17%">Current Hub:</td>
<td width="16%"><?php echo '<a href="'.$base_url.'hubs/index/'.$this->session->userdata('hub').'">'.$this->session->userdata('hub').'</a>'; ?></td>
<td width="17%">Last Flight:</td>
<td width="16%"><?php echo $lastflight; ?></td>
<td width="17%">Current Status:</td>
<td width="16%"><?php echo $status; ?></td>
</tr>
<tr>
<td width="17%">Number of Flights:</td>
<td width="16%"><?php echo $num_flights; ?></td>
<td width="17%">Flight Hours:</td>
<td width="16%"><?php echo $flighthours.'h '.$flightmins.'m'; ?></td>
<?php
if($next_rank != '-'){
	echo '<td width="17%">'.$next_rank.' promotion in :</td>';
	echo '<td width="16%">'.$timetorank.'</td>';
}
else{
	echo '<td colspan="2">&nbsp;</td>';
}
?>

</tr>
</table>

<br /><br />


<b>Propilot</b>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="17%">Last landed:</td>
<td width="49%" colspan="3"><?php echo $curr_location; ?></td>
<td width="17%"></td>
<td width="16%"></td>
</tr>
<tr>
<td width="17%">Propilot Location:</td>
<td width="49%" colspan="3"><?php echo $pp_location; ?></td>
<td width="17%"></td>
<td width="16%"></td>
</tr>
<tr>
<td width="17%">Average Score:</td>
<td width="16%"><?php if(is_numeric($pp_average)){ echo number_format($pp_average,0); } else{ echo '-'; } ?></td>
<td width="17%">Frequency:</td>
<td width="16%"><?php if(is_numeric($pp_average)){ echo number_format($pp_count,0); } else{ echo '-'; } ?></td>
<td width="17%">Total Score:</td>
<td width="16%"><?php if(is_numeric($pp_average)){ echo number_format($pp_sum,0); } else{ echo '-'; } ?></td>
</tr>
<?php
/*
<td width="17%">Travel Mode:</td>
<td width="16%"><?php if(strtotime($pp_lastflight) >= strtotime($pp_compare_date)){ echo 'No'; } else{ echo 'Yes'; } ?></td>
<tr>
<td width="17%"></td>
<td width="16%"></td>

</tr>
*/
?>
</table>

<br /><br />


<b>Stats</b>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="17%">Passengers:</td>
<td width="16%"><?php echo number_format($num_passengers); ?></td>
<td width="17%">Cargo:</td>
<td width="16%"><?php echo number_format($num_cargo); ?> tonnes</td>
<td width="17%"></td>
<td width="16%"></td>
</tr>
</table>


