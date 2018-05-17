<br/><br/>
<div style="font-size: 16px; text-align:center; font-weight:bold;">
    You have selected to remove the following flight assignment:
    <br/><br/>
    <span style="font-size: 14px; text-align:center; font-weight:normal;">
<?php echo '<td>' . $aircraft . ' from ' . $start_icao . ' ' . $dep_name . ' to ' . $end_icao . ' ' . $arr_name . '</td>'; ?>
</span>
    <br/><br/>
    Please confirm that you wish to do this.
    <br/><br/>
	<?php
	$hidden['valid'] = 'true';
	echo form_open('dispatch/unassign/' . $assigned_id, '', $hidden);
	echo form_submit('Submit', 'Confirm delete');
	echo form_close();
	?>
</div>