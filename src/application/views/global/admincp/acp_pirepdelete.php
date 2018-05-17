<br/><br/>
<div style="font-size: 16px; text-align:center; font-weight:bold;">
    You have selected to remove the following submitted pilot report before approval:
    <br/><br/>
    <span style="font-size: 14px; text-align:center; font-weight:normal;">
<?php echo '<td>EHM-' . $username . '<br />' . $aircraft . ' from ' . $start_icao . ' ' . $dep_name . ' to ' . $end_icao . ' ' . $arr_name .
	' (' . $passengers . ' pax, ' . $cargo . 'lbs cargo)<br />submitted ' . date('d/m/Y H:i:s', strtotime($submitdate)) . '</td>'; ?>
</span>
    <br/><br/>
    This step is irreversible. Please confirm that you wish to do this.
    <br/><br/>
	<?php
	$hidden['valid'] = 'true';
	echo form_open('acp_pireps/delete/' . $pirep_id, '', $hidden);
	echo form_submit('Submit', 'Confirm delete');
	echo form_close();
	?>
</div>