<center>
    <table border="0">
        <tr>
            <td width="25" align="center"><img
                        src="<?php echo $image_url; ?>icons/application/application_view_detail.png"
                        alt="View Flights"/></td>
            <td align="left">View Flights</td>
            <td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_error.png"
                                               alt="Query"/></td>
            <td align="left">Query</td>
            <td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_add.png"
                                               alt="Approve"/></td>
            <td align="left">Approve</td>
            <td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png"
                                               alt="Delete"/></td>
            <td align="left">Edit</td>
            <td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_delete.png"
                                               alt="Delete"/></td>
            <td align="left">Delete</td>
        </tr>
    </table>

    <table class="boxed" width="100%">
        <tr>
            <td colspan="11"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span>
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <th>Submitted</th>
            <th>Pilot</th>
            <th>Aircraft</th>
            <th>From</th>
            <th>To</th>
            <th>Block time</th>
            <th>GCD</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
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

				$status = 'Unchecked';
				if ($row->checked == '3') {
					$bgcol = 'bgcolor="#ffffbb"';
					$status = 'Queried';
				} elseif ($row->checked == '4') {
					$bgcol = 'bgcolor="#ffbb99"';
					$status = 'Responded';
				}

				echo '<tr ' . $bgcol . '>';
				echo '<td>' . $status . '</td>';
				echo '<td>' . gmdate('d/m/Y (H:i)', strtotime($row->submitdate)) . '</td>';
				echo '<td>EHM-' . $row->username . ' ' . $row->fname . ' ' . $row->sname . '</td>';
				echo '<td>' . $row->aircraft . '</td>';
				echo '<td>' . $row->start_icao . '</td>';
				echo '<td>' . $row->end_icao . '</td>';
				$length = 50;
				$dotdotdot = '';
				if (strlen($row->comments) > $length) {
					$dotdotdot = '...';
				}
				//echo '<td>'.substr($row->comments, 0, $length).$dotdotdot.'</td>';

				$flight_time = strtotime($row->landing_time) - strtotime($row->departure_time);
				$block_time = strtotime($row->engine_stop_time) - strtotime($row->engine_start_time);

				$flight_text = $this->format_fns->time_duration($flight_time, NULL, FALSE, TRUE);
				$block_text = $this->format_fns->time_duration($block_time, NULL, FALSE, TRUE);

				//echo '<td>'.$flight_text.'</td>';
				echo '<td>' . $block_text . '</td>';
				echo '<td>' . $row->gcd . '</td>';

				echo '<td align="center"><a href="' . $base_url . 'profile/flightlog/' . $row->pilot_id . '/e">
			<img src="' . $image_url . 'icons/application/application_view_detail.png" alt="View Flights" /></a></td>';
				echo '<td align="center"><a href="' . $base_url . 'acp_pireps/query/' . $row->id . '">
			<img src="' . $image_url . 'icons/application/database_error.png" alt="Query" /></a></td>';
				echo '<td align="center"><a href="' . $base_url . 'acp_pireps/approve/' . $row->id . '">
			<img src="' . $image_url . 'icons/application/database_add.png" alt="Approve" /></a></td>';
				echo '<td align="center"><a href="' . $base_url . 'acp_pireps/edit/' . $row->id . '">
			<img src="' . $image_url . 'icons/application/database_edit.png" alt="Edit" /></a></td>';
				echo '<td align="center"><a href="' . $base_url . 'acp_pireps/delete/' . $row->id . '">
			<img src="' . $image_url . 'icons/application/database_delete.png" alt="Delete" /></a></td>';

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
