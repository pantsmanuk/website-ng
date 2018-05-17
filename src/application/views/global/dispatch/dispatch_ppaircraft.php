<center>


    <br/>
    <div align="left">
		<?php
		$hidden['valid'] = 'true';
		echo form_open('dispatch/propilot_aircraft/' . $aircraft_restrict . '/' . $location_restrict . '/' . $status_restrict . '/' . $acstatus_restrict . '/', '', $hidden);
		echo form_input($search);
		echo form_submit('Submit', 'Search');
		echo form_close();
		?>
    </div>

    <br/>

    <div align="right">
		<?php
		$hidden['acstatus_restrict'] = '1';
		echo form_open('dispatch/propilot_aircraft/' . $aircraft_restrict . '/' . $location_restrict . '/' . $status_restrict . '/' . $acstatus_restrict . '/' . $search_url, '', $hidden);
		echo form_dropdown('aircraft_restrict', $aircraft_array, $aircraft_restrict);
		//echo '<br />';
		echo form_dropdown('status_restrict', $status_array, $status_restrict);
		echo '<br />';
		echo form_dropdown('location_restrict', $airfield_array, $location_restrict);

		//echo form_dropdown('acstatus_restrict', $acstatus_array, $acstatus_restrict);
		echo form_submit('Submit', 'Select');
		echo form_close();
		?>
    </div>
    <div align="left" style="font-weight: bold;">
		<?php echo $num_rows; ?> Aircraft Found
    </div>
    <table class="boxed" width="100%">
        <tr>
            <td colspan="11"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span>
            </td>
        </tr>
        <tr>
			<?php //<th>id</th> ?>
            <th>Tail id</th>
            <th>Aircraft</th>
            <th>Status</th>
            <th>Location</th>
            <th>Flown</th>
            <th width="70">Owner</th>
            <th width="70">Reserved</th>
            <th width="160">Pilot</th>
            <th>PP</th>
        </tr>
		<?php
		$i = 0;
		foreach ($result as $row) {

			if (is_numeric($offset)
				&& $i >= $offset
				&& $i < ($offset + $limit)
			) {

				if ($i % 2 != 0) {
					$bgcol = 'bgcolor="#f2f2f2"';
				} else {
					$bgcol = '';
				}

				echo '<tr ' . $bgcol . '>';
				//echo '<td width="20" align="center">'.$row->id.'</td>';
				echo '<td align="left" width="50">' . $row->tail_id . '</td>';
				echo '<td align="left">' . $row->name . '</td>';
				echo '<td align="left">' . $row->status . '</td>';
				echo '<td align="center">' . $row->location . '</td>';
				echo '<td align="center">' . gmdate('d/m/y', strtotime($row->last_flown)) . '</td>';

				if ($row->owner != '') {
					echo '<td align="center">EHM-' . $row->owner_username . '</td>';
				} else {
					echo '<td align="center">-</td>';
				}

				//locked pp plane
				if ($row->reserved != '0000-00-00 00:00:00' && $row->reserved != '' && $row->reserved >= $pp_compare_date) {
					echo '<td align="center">' . gmdate('d/m/y', strtotime($row->reserved)) . '</td>';
					echo '<td align="left">EHM-' . $row->reserver_username . ' ' . $row->reserver_fname . ' ' . $row->reserver_sname . '</td>';
				} else {
					if ($row->reserved != '0000-00-00 00:00:00' && $row->reserved != '') {
						echo '<td align="center">' . gmdate('d/m/y', strtotime($row->reserved)) . '</td>';
					} else {
						echo '<td align="center">-</td>';
					}
					if ($row->owner != '') {
						echo '<td align="left">EHM-' . $row->owner_username . ' ' . $row->owner_fname . ' ' . $row->owner_sname . '</td>';
					} else {
						echo '<td align="center">-</td>';
					}
				}

				if ($pp_location == $row->location && $row->flightnumber != NULL) {
					echo '<td align="center" width="20"><a href="' . $base_url . 'dispatch/propilot_lock/' . $row->id . '">
				<img src="' . $image_url . 'icons/application/lock_add.png" alt="Lock" /></a></td>';
				} else {
					if ($row->flightnumber != NULL) {
						echo '<td align="center"><img src="' . $image_url . 'icons/application/accept.png" alt="/" /></td>';
					} else {
						echo '<td align="center"><img src="' . $image_url . 'icons/application/cross.png" alt="X" /></td>';
					}
				}

				/*
				echo'<td align="center" width="20"><a href="'.$base_url.'acp_propilot/aircraft_edit/'.$row->id.'">
				<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';
				*/

				echo '</tr>';
			}
			$i++;
		}

		?>
        <tr>
            <td colspan="11"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span>
            </td>
        </tr>
    </table>
</center>
