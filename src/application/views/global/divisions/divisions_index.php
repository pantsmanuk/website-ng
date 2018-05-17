<div class="menu_sub">
	<?php
	$i = 0;
	foreach ($division_array as $row) {

		if ($i > 0) {
			echo ' | ';
		}

		echo '<a href="' . $base_url . 'divisions/index/' . $row['id'] . '">' . $row['longname'] . '</a>';
		$i++;
	}

	//determine if image exists for award
	$image_path = $assets_path . 'uploads/divisions/' . $division_array[$selected_division_id]['id'] . '/logo';

	if (file_exists($image_path . '.png')) {
		$image_url = $assets_url . 'uploads/divisions/' . $division_array[$selected_division_id]['id'] . '/logo' . '.png';
		$image_path = $assets_path . 'uploads/divisions/' . $division_array[$selected_division_id]['id'] . '/logo' . '.png';
	} elseif (file_exists($image_path . '.gif')) {
		$image_url = $assets_url . 'uploads/divisions/' . $division_array[$selected_division_id]['id'] . '/logo' . '.gif';
		$image_path = $assets_path . 'uploads/divisions/' . $division_array[$selected_division_id]['id'] . '/logo' . '.gif';
	} elseif (file_exists($image_path . '.jpg')) {
		$image_url = $assets_url . 'uploads/divisions/' . $division_array[$selected_division_id]['id'] . '/logo' . '.jpg';
		$image_path = $assets_path . 'uploads/divisions/' . $division_array[$selected_division_id]['id'] . '/logo' . '.jpg';
	} else {
		$image_url = $assets_url . 'uploads/divisions/no-image.jpg';
		$image_path = $assets_path . 'uploads/divisions/no-image.jpg';
	}

	?>
</div>
<br/><br/><br/>

<div class="container">

	<?php
	//echo '<center><img src="'.$assets_url.'divisions/'.$division_array[$selected_division_id]['id'].'/logo.jpg" alt="'.$division_array[$selected_division_id]['longname'].' Logo" /></center><br />';
	echo '<center><img src="' . $image_url . '" alt="' . $division_array[$selected_division_id]['longname'] . ' Logo" /></center><br />';
	?>

    <br/><br/>


    <div style="width: 100px; float:right;">
		<?php
		echo '<font size="3"><b>Resources</b></font>';
		echo '<br /><br />Main';
		echo '<br /><a href="' . $base_url . 'fleet/aircraft/' . $selected_division_id . '">Fleet</a>';
		if ($this->session->userdata('logged_in') == '1' && $this->session->userdata('rank_id') >= 2 && $division_array[$selected_division_id]['prim'] == 1) {
			echo '<br /><a href="' . $base_url . 'dispatch/timetable/' . $selected_division_id . '/EGLL/3">Timetable</a>';
		}
		if ($division_array[$selected_division_id]['missions'] == 1) {
			echo '<br /><a href="' . $base_url . 'missions/index/' . $selected_division_id . '">Missions</a>';
		}
		if ($division_array[$selected_division_id]['tours'] == 1) {
			echo '<br /><a href="' . $base_url . 'tours/">Tours</a>';
		}
		?>
    </div>

    <div style="width: 500px; margin-left: 25px;">
		<?php
		echo '<font size="3"><b>Welcome to ' . $division_array[$selected_division_id]['longname'] . '</b></font>';
		echo '<br /><br />' . nl2br($division_array[$selected_division_id]['blurb']) . '<br />';
		?>
        <br/>
    </div>

    <div class="clear"><!-- --></div>

</div>
<br/>