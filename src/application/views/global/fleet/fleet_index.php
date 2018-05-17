<div class="menu_sub">
	<?php
	$i = 0;
	foreach ($fleet_menu_array as $key => $value) {

		if ($i > 0) {
			echo ' | ';
		}

		echo '<a href="' . $base_url . 'fleet/index/' . $key . '/' . $flight_sim . '">' . $value . '</a>';
		$i++;
	}
	?>

</div>
<?php
$js = 'onchange="this.form.submit();"';
echo form_open('fleet/index/' . $fleet_type . '/' . $flight_sim);
echo form_dropdown('flight_sim', $flight_sim_array, $flight_sim, $js);
echo form_submit('Submit', 'See available downloads');
echo form_close();
?>

<br/><br/><br/>


<div align="center">

    <table width="675" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>


				<?php

				if ($fleet_type == 'C') {
					?>

                    <table class="boxed" width="100%">
                        <tr>
                            <th></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/1"><img src="' . $tmpl_image_url . 'fleet/ehm.jpg" alt="Euroharmony" />'; ?></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/2"><img src="' . $tmpl_image_url . 'fleet/ehb.jpg" alt="Eurobusiness" />'; ?></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/3"><img src="' . $tmpl_image_url . 'fleet/ehc.jpg" alt="Eurocargo" />'; ?></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/4"><img src="' . $tmpl_image_url . 'fleet/eho.jpg" alt="Euroholidays" />'; ?></th>
                        </tr>
						<?php

						$i = 1;
						foreach ($aircraft as $clss) {

							echo '<tr>';
							echo '<td><center><img src="' . $tmpl_image_url . 'fleet/class' . $i . '.gif" alt="Class ' . $i . '" /><br />' . $clss_detail[$i] . '</center></td>';
							$k = 1;
							foreach ($clss as $division) {

								if ($division == '') {
									//show grey background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/0_bg.jpg); width: 127px; height: 69px;">';
								} elseif ($i <= 1) {
									//show background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/' . $k . '_bg.jpg); width: 127px; height: 69px;">';
								} elseif ($i <= ($this->session->userdata('rank_id') + 1)) {
									//show background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/' . $k . '_bg.jpg); width: 127px; height: 69px;">';
								} else {
									//show locked background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/locked_bg.jpg); width: 127px; height: 69px;">';
								}

								$j = 0;
								if ($division != '') {
									foreach ($division as $plane) {
										//if the aircraft exists
										if (isset($plane['aircraft_id']) && isset($plane['name'])) {

											//determine if linked or not
											if ($plane['enabled'] == '1' && !empty($plane['aircraft_downloads_id'])) {
												echo '<center><a href="' . $base_url . 'fleet/aircraft/A/' . $plane['aircraft_id'] . '">' . $plane['name'] . '</a></center>';
											} //enabled plane, no downloads
                                            elseif ($plane['enabled'] == '1') {
												echo '<center><strike><a href="' . $base_url . 'fleet/aircraft/A/' . $plane['aircraft_id'] . '" style="font-weight: normal;">' . $plane['name'] . '</a></strike></center>';
											} //disabled plane
											else {
												echo '<center><div style="color:gray;}"><strike>' . $plane['name'] . '</strike></div></center>';
											}
										}
										$j++;
									}
								}
								echo '</td>';

								$k++;
							}

							echo '</tr>';
							$i++;
						}

						?>

                    </table>
                    <br/>
                    <hr/>
                    <br/>
                    <table class="boxed" width="100%">
						<?php

						//now we can output the alternative aircraft
						$i = 1;
						foreach ($alt_aircraft as $division) {

							$k = 1;

							$div_name = '';
							echo '<tr>';
							echo '<th width="150">';
							echo '<h3>';
							echo $division['division_longname'];
							echo '</h3>';
							echo '</th>';
							echo '<td>';
							foreach ($division['clss'] as $clss) {

								$j = 0;
								if ($clss != '') {
									foreach ($clss as $plane) {

										if ($clss != $plane['clss']) {
											echo '</td>';
											echo '<td>';
										}

										//if the aircraft exists
										if (isset($plane['aircraft_id']) && isset($plane['name'])) {
											if ($j > 0) {
												echo '<br />';
											}

											//determine if linked or not
											if ($plane['enabled'] == '1' && !empty($plane['aircraft_downloads_id'])) {
												echo '<a href="' . $base_url . 'fleet/aircraft/A/' . $plane['aircraft_id'] . '">' . $plane['name'] . '</a>';
											} //enabled plane, no downloads
                                            elseif ($plane['enabled'] == '1') {
												echo '<strike><a href="' . $base_url . 'fleet/aircraft/A/' . $plane['aircraft_id'] . '" style="font-weight: normal;">' . $plane['name'] . '</a></strike>';
											} //disabled plane
											else {
												echo '<div style="color:gray;}"><strike>' . $plane['name'] . '</strike></div>';
											}

											/*
											if($plane['enabled'] == '1'){
												echo '<a href="'.$base_url.'fleet/aircraft/A/'.$plane['aircraft_id'].'">'.$plane['name'].'</a>';
											}
											else{
												echo '<center><strike>'.$plane['name'].'</strike></center>';
											}
											*/

										}

										$clss = $plane['clss'];
										$j++;
									}
								}

							}
							echo '</td>';
							echo '</tr>';

							$i++;
						}

						?>

                    </table>
                    <br/>
					<?php
				} else {
					?>
                    <table class="boxed" width="100%">
                        <tr>
                            <th></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/1"><img src="' . $tmpl_image_url . 'fleet/ehm.jpg" alt="Euroharmony" />'; ?></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/2"><img src="' . $tmpl_image_url . 'fleet/ehb.jpg" alt="Eurobusiness" />'; ?></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/3"><img src="' . $tmpl_image_url . 'fleet/ehc.jpg" alt="Eurocargo" />'; ?></th>
                            <th><?php echo '<a href="' . $base_url . 'fleet/aircraft/4"><img src="' . $tmpl_image_url . 'fleet/eho.jpg" alt="Euroholidays" />'; ?></th>
                        </tr>
						<?php

						$i = 1;
						foreach ($historical as $clss) {

							echo '<tr>';
							echo '<td><center><img src="' . $tmpl_image_url . 'fleet/class' . $i . '.gif" alt="Class ' . $i . '" /><br />' . $clss_detail[$i] . '</center></td>';
							$k = 1;
							foreach ($clss as $division) {

								if ($division == '') {
									//show grey background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/0_bg.jpg); width: 127px; height: 69px;">';
								} elseif ($i <= 1) {
									//show background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/' . $k . '_bg.jpg); width: 127px; height: 69px;">';
								} elseif ($i <= ($this->session->userdata('rank_id') + 1)) {
									//show background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/' . $k . '_bg.jpg); width: 127px; height: 69px;">';
								} else {
									//show locked background
									echo '<td style="background: #ffffff  url(' . $tmpl_image_url . 'fleet/locked_bg.jpg); width: 127px; height: 69px;">';
								}

								$j = 0;
								if ($division != '') {
									foreach ($division as $plane) {
										//if the aircraft exists
										if (isset($plane['aircraft_id']) && isset($plane['name'])) {

											//determine if linked or not
											if ($plane['enabled'] == '1' && !empty($plane['aircraft_downloads_id'])) {
												echo '<center><a href="' . $base_url . 'fleet/aircraft/A/' . $plane['aircraft_id'] . '">' . $plane['name'] . '</a></center>';
											} //enabled plane, no downloads
                                            elseif ($plane['enabled'] == '1') {
												echo '<center><strike><a href="' . $base_url . 'fleet/aircraft/A/' . $plane['aircraft_id'] . '" style="font-weight: normal;">' . $plane['name'] . '</a></strike></center>';
											} //disabled plane
											else {
												echo '<center><div style="color:gray;}"><strike>' . $plane['name'] . '</strike></div></center>';
											}

										}
										$j++;
									}
								}
								echo '</td>';

								$k++;
							}

							echo '</tr>';
							$i++;
						}

						?>

                    </table>
                    <br/>
					<?php
				}
				?>

                <div>
                    In order to better support multiple flight-simulators and allow you more simming enjoyment, we
                    operate a relaxed policy on our aircraft types. <br/><br/>This means that while the official fleet
                    aircraft and supporting files are listed, you may fly a close variant aircraft. E.G. An MD-81 or
                    MD-82 may be flown as a substitute for the MD-83, an Airbus A300F instead of the A310-300F, a
                    747-200 instead of the 747-400 and a CRJ-200 instead of the Challenger 850. <br/><br/>Substantially
                    different types may not be flown as a substitute. A Sequioa Falco may not be flown in place of a
                    Cessna, and a 727-200 may not be flown in place of an MD-11.
                </div>

            </td>
        </tr>
    </table>
</div>

