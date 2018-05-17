<center>

    <table border="0">
        <tr>
            <td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_edit.png"
                                               alt="Edit"/></td>
            <td align="left">Edit</td>
            <td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_delete.png"
                                               alt="Delete"/></td>
            <td align="left">Delete</td>
            <td width="25" align="center"><a
                        href="<?php echo $base_url . 'acp_pilots/award_edit/' . $pilot_id . '/0/'; ?>"><img
                            src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add"/></a></td>
            <td align="left"><a href="<?php echo $base_url . 'acp_pilots/award_edit/' . $pilot_id . '/0/'; ?>">Assign
                    New</a></td>
        </tr>
    </table>

    <h1><?php echo '[' . $username . '] ' . $pilot_name . ' (' . $status . ')'; ?></h1>

    <table class="boxed" width="100%">
        <tr>
            <td colspan="11"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span>
            </td>
        </tr>
        <tr>
            <th>Award</th>
            <th>Auto</th>
            <th>Assigned</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
		<?php
		$i = 0;
		foreach ($results as $row) {
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

				echo '<td align="left">' . $row->award_name . '</td>';

				if ($row->automatic == 'Y') {
					echo '<td width="20" align="center">' . $row->automatic . '</td>';
				} else {
					echo '<td width="20" align="center"><b>' . $row->automatic . '</b></td>';
				}
				if ($row->assigned_date == '0000-00-00' || $row->assigned_date == '') {
					echo '<td width="20" align="center">Unknown</td>';
				} else {
					echo '<td width="20" align="center">' . gmdate('d/m/Y', strtotime($row->assigned_date)) . '</td>';
				}

				echo '<td align="center" width="20"><a href="' . $base_url . 'acp_pilots/award_edit/' . $pilot_id . '/' . $row->id . '">
				<img src="' . $image_url . 'icons/application/database_edit.png" alt="Edit" /></a></td>';

				echo '<td align="center" width="20"><a href="' . $base_url . 'acp_pilots/award_delete/' . $pilot_id . '/' . $row->id . '">
				<img src="' . $image_url . 'icons/application/database_delete.png" alt="Delete" /></a></td>';

				echo '</tr>';
			}
			$i++;
		}

		?>
    </table>
</center>