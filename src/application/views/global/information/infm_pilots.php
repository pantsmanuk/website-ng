<div class="menu_sub">
	<?php

	$url = $base_url . 'information/pilots/';

	$i = 0;
	foreach ($pilots_menu_array as $key => $value) {

		if ($i > 0) {
			echo ' | ';
		}

		echo '<a href="' . $url . $key . '">' . $value . '</a>';
		$i++;
	}

	?>
</div>
<br/><br/>

<span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span>

<br/>


<?php

echo '<table width="100%" class="pilots">';
echo '<tr>';
echo '<th width="150"><a href="' . $url . $all . '/rank">Rank</a></th>';
echo '<th><a href="' . $url . $all . '/username">ID</a></th>';
echo '<th><a href="' . $url . $all . '/fname">Firstname</a></th>';
echo '<th><a href="' . $url . $all . '/sname">Surname</a></th>';
echo '<th><a href="' . $url . $all . '/hours">Hours</a></th>';
echo '<th><a href="' . $url . $all . '/active">Last Active</a></th>';
echo '<th width="38">&nbsp;</th>';
echo '</tr>';

$i = 0;
foreach ($pilot_data as $row) {

	if (is_numeric($offset)
		&& $i >= $offset
		&& $i < ($offset + $limit)
	) {

		if ($i > 0) {
			echo '<tr>';
			echo '<td colspan="7"><hr /></td>';
			echo '</tr>';
		}

		/*
		$mugshot_path = $image_path.'content/mugshots/'.$row->id.'.jpg';
	
		if (file_exists($mugshot_path)) {
			$mugshot_url = $image_url."content/mugshots/".$row->id.'.jpg';
		}
		else{
			$mugshot_url = $image_url.'content/mugshots/missing.jpg';
		}
	*/

		$flight_seconds = ($row->flighthours * 60 * 60) + ($row->flightmins * 60);

		$hours_text = '';
		$min_text = '';

		if ($row->flighthours != '') {
			$hours_text = number_format($row->flighthours, 0) . 'h';
		}

		if ($row->flighthours != '') {
			$min_text = $row->flightmins . 'm';
		}

		$flight_time = $hours_text . ' ' . $min_text;

		echo '<tr>';
		echo '<td align="left">' . $row->rank_long . '</td>';
		echo '<td align="left">[EHM-' . $row->username . ']</td>';
		echo '<td align="left">' . $row->fname . '</td>';
		echo '<td align="left">' . $row->sname . '</td>';
		//echo '<td align="left">'.$this->format_fns->format_seconds_hhmm_verbose($flight_seconds).'</td>';
		echo '<td align="left">' . $flight_time . '</td>';
		echo '<td align="left">' . date('d/m/Y', strtotime($row->lastflight)) . '</td>';
		echo '<td align="center"><img src="' . $image_url . 'icons/flags/' . $row->country_code . '.gif" 
		alt="' . $row->country_code . '" width="30" height="17" /></td>';
		echo '</tr>';

	}
	$i++;

}

echo '</table>';

?>

<br/>

<span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span>