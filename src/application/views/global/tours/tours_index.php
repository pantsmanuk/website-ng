<center>
    <table border="0" cellpadding="4" cellspacing="0" style="border-collapse: collapse;" bordercolor="#111111"
           width="100%" id="AutoNumber1">
        <tbody>
        <tr>
            <td colspan="2" valign="top" align="left">&nbsp;<br/>
                After flying so many scheduled flights with passenger and cargo
                aircraft, now it's time to relax and to explore the most interesting
                regions of Europe with your favorite EuroHarmony general aviation
                plane, through a series of tours organized by Harmony Aeroclub. Visit
                the islands of Greece, admire the rugged coastline of Norway, fly
                challenging flights in the Alps, follow the Danube, explore the Iberian
                Peninsula - these tours will be a unique experience for you. <br/>
                &nbsp;
            </td>
        </tr>
		<?php
		foreach ($tour_array as $row) {

			//determine if image exists for award
			$image_path = $assets_path . 'uploads/tours/' . $row->id;

			if (file_exists($image_path . '.png')) {
				$image_url = $assets_url . 'uploads/tours/' . $row->id . '.png';
				$image_path = $assets_path . 'uploads/tours/' . $row->id . '.png';
			} elseif (file_exists($image_path . '.gif')) {
				$image_url = $assets_url . 'uploads/tours/' . $row->id . '.gif';
				$image_path = $assets_path . 'uploads/tours/' . $row->id . '.gif';
			} elseif (file_exists($image_path . '.jpg')) {
				$image_url = $assets_url . 'uploads/tours/' . $row->id . '.jpg';
				$image_path = $assets_path . 'uploads/tours/' . $row->id . '.jpg';
			} else {
				$image_url = $assets_url . 'uploads/tours/no-image.jpg';
				$image_path = $assets_path . 'uploads/tours/no-image.jpg';
			}

			//check preview image exists
			//$image_url = $assets_url.'uploads/tours/'.$row->id.'.gif';

			//<td style="background: #ffffff  url('.$tmpl_image_url.'fleet/locked_bg.jpg); width: 127px; height: 69px;">';

			//check if able to fly
			$pilot_rank = $this->session->userdata('rank_id');

			if ($pilot_rank < $row->rank_id) {

				//grey the background
				$bgstyle = 'style="background: #dbdbdb url(' . $tmpl_image_url . 'tours/locked_bg_solid.png) no-repeat top right;"';

			} else {
				$bgstyle = '';
			}

			echo '<tr>';
			if (is_array(@getimagesize($image_url))) {
				echo '<td><a href="' . $base_url . 'tours/details/' . $row->id . '"><img src="' . $image_url . '" alt="' . $row->rank_name . '" /></a></td>';
			} else {
				echo '<td>&nbsp;</td>';
			}
			echo '<td ' . $bgstyle . '>';
			echo '<a href="' . $base_url . 'tours/details/' . $row->id . '">' . $row->name . '</a> (' . $row->rank_name . '+)<br />';
			echo '<b>Author:</b> ' . $row->author . '<br />';
			echo '<b>Length:</b> ' . $row->length . '<br />';
			echo '<b>Difficulty:</b> ' . $row->difficulty . '<br />';
			echo $row->description;
			echo '</td>';
			echo '</tr>';

		}
		?>


        <tr>
            <td colspan="2" valign="top" align="left">
                <b>Restrictions and requirements</b> (general points):<br/>
                - Tours are restricted by rank<br/>
                - You may fly each tour as many times as you wish, but the reward will only be issued the first
                time.<br/>
                - You must complete one tour before starting another.<br/>
                - Flight hours from tours will be added to your total time at Euroharmony. Report the legs using the
                Pirep, or the flight logger.<br/>
                - After completing a tour, you should automatically receive your reward.
                - You may set the weather (if flying offline), time and date as you wish. The tours are to be flown VFR
                with GA planes (except Capitals tour), so it's recommended to fly at daytime.<br/>
                - Make sure you review all individual requirements for each tour (hours needed, plane type etc). We
                can't accept tours that are not compatible with the regulations.<br/>
                - Make full stop landings at all airports of a tour. Touch-and-gos or flybys are not sufficient.<br/>
                - Respect all other restrictions regarding EuroHarmony flights, as they are presented in the Operations
                Manual.

                Have a nice flight!
            </td>
        </tr>
        </tbody>
    </table>
</center>
