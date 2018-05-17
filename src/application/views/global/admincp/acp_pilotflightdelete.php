<?php
if ($num_flights > 0) {

	$aircraft = $timetable_flights[0]['aircraft'];
	$dep_icao = $timetable_flights[0]['start_icao'];
	$dep_name = $timetable_flights[0]['start_name'];
	$arr_icao = $timetable_flights[0]['end_icao'];
	$arr_name = $timetable_flights[0]['end_name'];
	$date = gmdate('d/m/Y', strtotime($timetable_flights[0]['landing_time']));
	$pilot_data = '[EHM-' . $timetable_flights[0]['pilot_username'] . '] ' . $timetable_flights[0]['pilot_fname'] . ' ' . $timetable_flights[0]['pilot_sname'];
	?>


    <br/><br/>
    <div style="font-size: 16px; text-align:center; font-weight:bold;">
        You have selected to remove the following flight from the database:
        <br/><br/>
        <span style="font-size: 14px; text-align:center; font-weight:normal;">
<?php echo '<td>' . $pilot_data . '<br /><br />' . $aircraft . ' from ' . $dep_icao . ' ' . $dep_name . ' to ' . $arr_icao . ' ' . $arr_name . ' on ' . $date . '</td>'; ?>
</span>
        <br/><br/>
        This step is irreversible and will result in recalculation of pilot hours and rank. Please confirm that you wish
        to do this.
        <br/><br/>
		<?php
		$hidden['valid'] = 'true';
		$hidden['confirm'] = '1';
		$hidden['flight_id'] = $flight_id;
		echo form_open('acp_pilots/flightdelete/' . $flight_id . '/' . $pilot_id, '', $hidden);
		echo form_submit('Submit', 'Confirm delete');
		echo form_close();
		?>
    </div>

	<?php
} else {
	echo '<div style="font-size: 16px; text-align:center; font-weight:bold;">No such flight could be located</div>';
}
?>