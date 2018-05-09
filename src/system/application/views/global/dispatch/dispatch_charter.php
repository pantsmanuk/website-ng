Charter allows you to file a chartered route, using either Aeroclub, Eurobusiness or Wild division aircraft.

<br /><br />
<center>
<?php
$hidden['valid'] = 'true';
echo form_open('dispatch/charter/', '', $hidden);
?>

<table class="boxed" width="95%">
<tr>
	<th>Aircraft</th>
	<th>Start Airfield</th>
    <th>End Airfield</th>
</tr>

<tr>
	<td align="center"><?php echo form_dropdown('aircraft_id', $aircraft_array, $aircraft_id); ?></td>
    <td align="center"><?php echo form_dropdown('start_icao', $airfield_array, $start_icao); ?></td>
    <td align="center"><?php echo form_dropdown('end_icao', $airfield_array, $end_icao); ?></td>
</tr>

</table>

<br />

<?
echo form_submit('Submit', 'Select');
echo form_close();
?>


<br /><br />

<div class="error"><?php echo $exception; ?></div></center>