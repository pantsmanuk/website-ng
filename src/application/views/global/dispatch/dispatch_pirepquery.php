<?php
$flight_time = strtotime($landing_time) - strtotime($departure_time);
$block_time = strtotime($engine_stop_time) - strtotime($engine_start_time);

$flight_text = $this->format_fns->time_duration($flight_time, NULL, FALSE, TRUE);
$block_text = $this->format_fns->time_duration($block_time, NULL, FALSE, TRUE);
?>
<div style="font-size: 16px; text-align:left; font-weight:bold;">
    Pirep details
</div>
<br/>

<table width="100%">
    <tr>
        <th align="center">Pilot</th>
        <th align="center">Aircraft</th>
        <th align="center">From</th>
        <th align="center">To</th>
        <th align="center">Network</th>
        <th align="center">Pax</th>
        <th align="center">Cargo</th>
    </tr>

    <tr>
        <td align="center">EHM-<?php echo $username; ?></td>
        <td align="center"><?php echo $aircraft; ?></td>
        <td align="center"><?php echo $start_icao . ' ' . $dep_name; ?></td>
        <td align="center"><?php echo $end_icao . ' ' . $arr_name; ?></td>
        <td align="center"><?php echo $onoffline; ?></td>
        <td align="center"><?php echo $passengers; ?></td>
        <td align="center"><?php echo $cargo; ?>lbs</td>
    </tr>
</table>


<table width="100%">
    <tr>
        <th align="center">Engine Start</th>
        <th align="center">Takeoff</th>
        <th align="center">Landing</th>
        <th align="center">Engine Stop</th>
        <th align="center">Flight time</th>
        <th align="center">Block Time</th>
    </tr>

    <tr>
        <td align="center"><?php echo date('d/m/Y H:i', strtotime($engine_start_time)); ?></td>
        <td align="center"><?php echo date('d/m/Y H:i', strtotime($departure_time)); ?></td>
        <td align="center"><?php echo date('d/m/Y H:i', strtotime($landing_time)); ?></td>
        <td align="center"><?php echo date('d/m/Y H:i', strtotime($engine_stop_time)); ?></td>
        <td align="center"><?php echo $flight_text; ?></td>
        <td align="center"><?php echo $block_text; ?></td>
    </tr>
</table>

<br/>
<center>
    <img src="http://www.gcmap.com/map?P=<?php echo $start_icao . '-' . $end_icao; ?>&MS=bm&MX=720x180&PM=*"
         alt="Pirep: <?php echo $start_icao . '-' . $end_icao; ?>"/>
</center>

<hr/>

<?php
if (count($messages) > 0) {

	?>
    <div style="font-size: 16px; text-align:left; font-weight:bold;">
        Messages
    </div>

    <table width="100%" class="boxed">
        <tr>
            <th width="270">Sent / User</th>
            <th>comment</th>
        </tr>
		<?
		$i = 1;
		foreach ($messages as $row) {

			//colour rows
			if ($i % 2 != 0) {
				$bgcol = 'bgcolor="#f2f2f2"';
			} else {
				$bgcol = '';
			}

			//colour text based on pilot / admin
			if ($row->from_pilot == 1) {
				$style = 'style="color: #2200dd;"';
			} else {
				$style = 'style="color: #ff2200;"';
			}

			echo '<tr ' . $bgcol . ' ' . $style . '>';
			echo '<td>' . date('d/m/Y H:i:s', strtotime($row->submitted)) . ' (EHM-' . $row->username . ' ' . $row->fname . ')</td>';
			echo '<td>' . $row->comment . '</td>';
			echo '</tr>';
			$i++;
		}
		?>
        </tr>
    </table><br/>
	<?php
}
?>


<br/>
<center>
    <table border="0">
        <tr>
            <th align="right">Add Comment:</th>
            <th>
                Leave blank to avoid writing data<br/>
				<?php
				$hidden['valid'] = 'true';
				echo form_open('dispatch/pirepquery/' . $pirep_id, '', $hidden);
				echo form_textarea($pilot_comment);

				?>
            </th>
        </tr>
    </table>


    <br/>
	<?php

	echo form_submit('Submit', 'Submit');
	echo form_close();

	?>

</center>