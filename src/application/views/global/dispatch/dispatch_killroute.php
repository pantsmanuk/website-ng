<br/><br/>
<div style="font-size: 16px; text-align:center; font-weight:bold;">
    You have selected to remove the following flight route:
    <br/><br/>
    <span style="font-size: 14px; text-align:center; font-weight:normal;">
<center>
<table border="0">
<?php
foreach ($pireps as $row) {
	echo '<tr><td>' . '[' . $row->group_order . '] ' . $row->aircraft . ' from ' . $row->start_icao . ' ' . $row->dep_name . ' to ' . $row->end_icao . ' ' . $row->arr_name . '</td></tr>';
}
?>
</table>
</center>
</span>
    <br/><br/>
    Please confirm that you wish to do this.
    <br/><br/>
	<?php
	$hidden['valid'] = 'true';
	echo form_open('dispatch/killroute/' . $route_id, '', $hidden);
	echo form_submit('Submit', 'Confirm delete');
	echo form_close();
	?>
</div>