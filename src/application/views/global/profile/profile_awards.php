<?php
$this->load->view('global/profile/profile_topbar');
?>
<br/>
<center>
    <table class="statbox" width="85%">
        <tr>
            <th>Award</th>
            <th>Description</th>
        </tr>

		<?php
		foreach ($awards as $row) {

			$award_id = $row['awards_index_id'];

			//determine if image exists for award
			$image_path = $assets_path . 'uploads/awards/' . $award_id;

			if (file_exists($image_path . '.png')) {
				$image_url = $assets_url . 'uploads/awards/' . $award_id . '.png';
				$image_path = $assets_path . 'uploads/awards/' . $award_id . '.png';
			} elseif (file_exists($image_path . '.gif')) {
				$image_url = $assets_url . 'uploads/awards/' . $award_id . '.gif';
				$image_path = $assets_path . 'uploads/awards/' . $award_id . '.gif';
			} elseif (file_exists($image_path . '.jpg')) {
				$image_url = $assets_url . 'uploads/awards/' . $award_id . '.jpg';
				$image_path = $assets_path . 'uploads/awards/' . $award_id . '.jpg';
			} else {
				$image_url = $assets_url . 'uploads/awards/no-image.png';
				$image_path = $assets_path . 'uploads/awards/no-image.png';
			}

			//recalculate width and height

			list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);

			$height_new = 30;
			$width_new = $width_orig / $height_orig * $height_new;

			echo '<tr>';
			echo '<td><img src="' . $image_url . '" width="' . $width_new . '" height="' . $height_new . '"/></td>';
			//echo '<td><img src="'.$assets_url.'images/awards/'.$row->awards_index_id.'.jpg" alt="award '.$row->awards_index_id.'" /></td>';
			echo '<td><div align="left"><b>' . $row['award_name'] . '</b> (' . $row['award_rank'] . ') ' . $row['description'] . '</div></td>';
			echo '</tr>';
		}

		?>

    </table>
</center>
