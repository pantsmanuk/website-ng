<br/><br/>
<div style="font-size: 16px; text-align:center; font-weight:bold;">
    You have selected to remove the following pilot<br/>and any related pireps, awards, assigned flights and locked
    propilot aircraft:
    <br/><br/>
    <span style="font-size: 14px; text-align:center; font-weight:normal;">
<?php echo '<td>EHM-' . $username . ' ' . $fname . ' ' . $sname . '<br />Member since: ' . date('d/m/Y', strtotime($signupdate)) . '</td>'; ?>
</span>
    <br/><br/>
    This step is irreversible. Please confirm that you wish to do this.
    <br/><br/>
	<?php
	$hidden['valid'] = 'true';
	echo form_open('acp_pilots/delete/' . $pilot_id, '', $hidden);
	echo form_submit('Submit', 'Confirm delete');
	echo form_close();
	?>
</div>