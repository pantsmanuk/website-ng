<br /><br />
<div style="font-size: 16px; text-align:center; font-weight:bold;">
You have selected to remove the following flight from the database:
<br /><br />
<span style="font-size: 14px; text-align:center; font-weight:normal;">
<?php echo '<td>Flight '.$flightnumber.' from '.$dep_icao.' '.$dep_name.' to '.$arr_icao.' '.$arr_name.'</td>'; ?>
</span>
<br /><br />
This step is irreversible. Please confirm that you wish to do this.
<br /><br />
<?php
$hidden['valid'] = 'true';
echo form_open('acp_timetables/delete/'.$timetable_id, '', $hidden);
echo form_submit('Submit', 'Confirm delete');
echo form_close();
?>
</div>