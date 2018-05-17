Propilot events are community oriented activities similar to the tours in that they contain a series of legs that must be flown in order. However, all event flights must be flown on Propilot using the Euroharmony Flight Logger and each leg can only be flown between the specified dates - this makes each leg of the event well suited to being flown on one of the online networks (if that is your preference, it is not a requirement).
<br/><br/>
When you sign up for an Event, you will have your own aircraft, with a unique tail number, assigned to you for the duration of the tour. Careful! If you crash it, once it's gone, it's gone and your event ends there. Successful completion of an event usually results in a completion achievement award.
<br/><br/>
Events usually add something special to your flying experience at Euroharmony and will range from vintage aircraft runs such as a DC-3 or Ju-52 through modern props such as the venerable B1900D to monsters of the Sky; 747 and A380. Some events are topical - such as cargo transport for the F1 Grand Prix while others recreate historical events - such as a mail run. Whatever your preference, there should be an event for everyone. Check back regularly.
<br/><br/>
<h2>Upcoming/Ongoing Events</h2>

<?php

if ($num_results < 1) {
	echo '<h3>There are no upcoming events. Check back soon!</h3>';
} else {

	//echo '<table width="100%">';
	foreach ($result as $row) {

		$pp_event_id = $row->id;

		//determine if image exists for image
		$image_path = $assets_path . 'uploads/events/' . $pp_event_id;

		if (file_exists($image_path . '.gif')) {
			$image_url = $assets_url . 'uploads/events/' . $pp_event_id . '.gif';
			$image_path = $assets_path . 'uploads/events/' . $pp_event_id . '.gif';
		} elseif (file_exists($image_path . '.png')) {
			$image_url = $assets_url . 'uploads/events/' . $pp_event_id . '.png';
			$image_path = $assets_path . 'uploads/events/' . $pp_event_id . '.png';
		} elseif (file_exists($image_path . '.jpg')) {
			$image_url = $assets_url . 'uploads/events/' . $pp_event_id . '.jpg';
			$image_path = $assets_path . 'uploads/events/' . $pp_event_id . '.jpg';
		} else {
			$image_url = $assets_url . 'uploads/events/no-image.png';
			$image_path = $assets_path . 'uploads/events/no-image.png';
		}

		//recalculate width and height

		list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);

		//style="padding: 3px; margin-right: 3px; border: 1px solid rgb(187, 187, 187);"
		$height_new = 80;
		$width_new = $width_orig / $height_orig * $height_new;

		echo '<div class="wrapper">';
		echo '<a href="' . $base_url . 'events/details/' . $pp_event_id . '"><img src="' . $image_url . '" alt="Image" width="' . $width_new . '" height="' . $height_new . '" /></a>';
		echo '<div class="description">';
		echo '<p class="description_content"><a href="' . $base_url . 'events/details/' . $pp_event_id . '">Propilot Event: ' . $row->name . ' (Starts ' . gmdate('d/m/Y', strtotime($row->start_date)) . ')</a></p>';
		echo '</div>';
		echo '</div>';
		echo '<br />';

		/*

			echo '<tr>';
			echo '<td><a href="'.$base_url.'events/details/'.$pp_event_id.'">';
			echo '<img src="'.$image_url.'" alt="Image" width="'.$width_new.'" height="'.$height_new.'" /></a>';
			echo '</td>';
			echo '<td>'.$row->name.'</td>';
			echo '<td width="75">'.gmdate('d/m/Y', strtotime($row->start_date)).'</td>';
			echo '</tr>';
		*/

	}
	//echo '</table>';
}

?>