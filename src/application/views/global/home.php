<?php
if ($this->session->userdata('logged_in') != '1') {
	$limit = 4;
} else {
	$limit = 4;
}

$img_url = $image_url;
?>
<center>


	<?php

	if ($pp_event_id != '' && is_numeric($pp_event_id)) {

		//event advertisement ***************************************************************

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
		echo '<p class="description_content"><a href="' . $base_url . 'events/details/' . $pp_event_id . '">Propilot Event: ' . $pp_event_name . ' (Starts ' . gmdate('d/m/Y', strtotime($pp_event_start_date)) . ')</a></p>';
		echo '</div>';
		echo '</div>';
		echo '<br /><br />';

	}

	?>

	<?php
	/* Flash embed for the selected movie
	<object type="application/x-shockwave-flash" style="width:600px; height:365px;" data="http://www.youtube.com/v/qeh4mDmCPqw?fs=1&amp;hl=en_GB&amp;rel=0">
	<param name="movie" value="http://www.youtube.com/v/qeh4mDmCPqw?fs=1&amp;hl=en_GB&amp;rel=0" />
	<param value="transparent" name="wmode"></param>
	<param name="allowscriptaccess" value="always"></param>
	<param name="allowFullScreen" value="true"></param>
	</object>
	</center>

	<div id="ehmvideo">You need Flash v10 or higher to see this content. <a href="http://get.adobe.com/flashplayer/">Please visit the Flash website</a>.</div>
	<script type="text/javascript">
		var so = new SWFObject('http://www.youtube.com/v/qeh4mDmCPqw','flashContent','600','365','9');
		so.addParam('movie','http://www.youtube.com/v/qeh4mDmCPqw');
		so.addParam('allowfullscreen','true');
		so.addParam('allowscriptaccess','always');
		so.addParam('transparent','wmode');
		so.addVariable('file', 'http://www.youtube.com/v/qeh4mDmCPqw');
		so.write('ehmvideo');
	</script>


		so.addParam('movie','http://www.youtube.com/v/qeh4mDmCPqw');
		so.addParam('allowfullscreen','true');
		so.addParam('allowscriptaccess','always');
		so.addParam('transparent','wmode');
		so.addVariable('file', 'http://www.youtube.com/v/qeh4mDmCPqw');

	<iframe class="youtube-player" type="text/html" width="600" height="365" src="http://www.youtube.com/v/qeh4mDmCPqw" frameborder="0">
	</iframe>

	*/
	?>

	<?php

	//to show video or images - 0 video, 1 images
	if ($vidorimage == '1' || empty($featured_video)) {

		//display the vid to image switcher
		echo '<div style="text-align: left; margin-left: 10px;">
        <a href="' . $this->config->item('base_url') . 'index.php/ehm/index/image/" class="greybutton">Images</a>
        <a href="' . $this->config->item('base_url') . 'index.php/ehm/index/video/" class="greybutton">Video</a>
        </div>';

		?>


        <div id="slideshow">
            <ul id="nav">
                <li id="prev"><a href="#">Previous</a></li>
                <li id="next"><a href="#">Next</a></li>
            </ul>

            <ul id="slides">
				<?php
				//echo the images
				$i = 0;
				foreach ($cycleimages as $img_uri) {
					//if($i < 4){
					echo '<li><img src="' . $img_uri . '" alt="EHM Image" width="600" height="365" /></li>';
					//}
					$i++;
				}

				?>
            </ul>
        </div>

		<?php
	} else {

		//display the vid to image switcher
		echo '<div style="text-align: left; margin-left: 10px;">
    <a href="' . $this->config->item('base_url') . 'index.php/ehm/index/image/" class="greybutton">Images</a>
    <a href="' . $this->config->item('base_url') . 'index.php/ehm/index/video/" class="greybutton">Video</a>
    </div>';

		?>

        <center>

            <iframe class="youtube-player" type="text/html" width="620" height="385"
                    src="<?php echo $featured_video; ?>&rel=0" frameborder="0" allowfullscreen>
            </iframe>

        </center>

		<?php
	}
	?>


</center>


<?php
/*
<table border="0" width="100%">
<tr>
<td>
<img src="<?php echo $tmpl_image_url; ?>home/welcomeheader.gif" alt="Welcome" /><br />

<div class="container">
    EuroHarmony is one of the leading Virtual Airlines in existance today. At EuroHarmony, you'll find a great pilots community to support and enhance your simulation experience.
    <br />
	<br />
    EuroHarmony VA began operations in March 2001 and has grown since then to operate eight HUBS (London,
    Stockholm, Amsterdam, Athens, Lisbon, Zurich, Atlanta and Singapore) with a fleet of over 40 aircraft servicing a timetable of more
    than 6,000 flights across five divisions: EuroHarmony, EuroBusiness, EuroHolidays, EuroCargo and Wild!
    <br />
	<br />
    To further enhance your flight simulation experience here at EuroHarmony, we have tours awarding completion merits,
    regular online group flights and community events. Everyone with basic flying skills and a general aviation flight simulator (MSFS, X-plane etc) can join us, and if you need a bit of extra training
    then we can help you with that too!
    <br />
	<br />
    Join now, and experience a community of flight simulation enthusiasm as it should be!

</div>
</td>
<td>



</td>
</tr>
</table>
*/
?>
<br/>
<br/>


<?php

if (isset($news) && count($news) > 0) {

	echo '<img src="' . $tmpl_image_url . 'home/latestforummessages.gif" alt="Forum threads" /><br />';
	echo '<div class="container">';

	echo '<table width="100%">';

	$i = 1;
	$j = 1;
	foreach ($news as $news_item) {

		if ($j % 2 != 0) {
			$bgcolor = 'style="background: #f2f2f2;"';
		} else {
			$bgcolor = '';
		}

		if ($i % 2 != 0) {

			echo "<tr  $bgcolor>";
			$j++;
		}

		echo '<td width="50%">' . anchor($news_item->link, $news_item->title) . '</td>';
		//echo '<p>';
		//        if(isset($news_item->enclosure)){
		//            echo img($news_item->enclosure->attributes()->url);
		//        }
		//echo $news_item->description;
		//echo '</p>';

		//close row
		if ($i % 2 == 0) {
			echo '</tr>';
		}
		$i++;
	}

	//determine if end needed
	if (count($news) % 2 != 0) {
		//need to end
		echo '<td>&nbsp;</td>';
		echo '</tr>';
	}

	echo '</table>';
	echo '</div>';

}
?>
<br/>
<br/>


<table width="100%">
    <td width="33.33333333333%">
        <center>
            <a href="http://twitter.com/intent/user?screen_name=euroharmony" target="_new"><img
                        src="<?= $img_url; ?>icons/twitter.png"/></a>
        </center>

    </td>
    <td width="33.33333333333%">

        <center>
            <a href="https://www.facebook.com/pages/Euroharmony-VA/240335449356693" target="_new"><img
                        src="<?= $img_url; ?>icons/facebook.png"/></a>
        </center>

    </td>
    <td width="33.33333333333%">

        <center>
            <a href="https://plus.google.com/b/118357379399235353400/" target="_new"><img
                        src="<?= $img_url; ?>icons/googleplus.png"/></a>
        </center>

    </td>
</table>


<br/>
<br/>

<img src="<?php echo $tmpl_image_url; ?>home/euroharmonynews.gif" alt="News"/><br/>
<div class="container">
    <table width="100%">
        <tr valign="top">
            <td width="50%">
                <table class="tbl_news" width="100%">
					<?php
					$i = 0;
					foreach ($news_items as $row) {
						if ($i < $limit) {

							//determine if image exists
							$image_path = $assets_path . 'uploads/news/' . $row->id;

							if (file_exists($image_path . '.png')) {
								$image_url = $assets_url . 'uploads/news/' . $row->id . '.png';
								$image_path = $assets_path . 'uploads/news/' . $row->id . '.png';
							} elseif (file_exists($image_path . '.gif')) {
								$image_url = $assets_url . 'uploads/news/' . $row->id . '.gif';
								$image_path = $assets_path . 'uploads/news/' . $row->id . '.gif';
							} elseif (file_exists($image_path . '.jpg')) {
								$image_url = $assets_url . 'uploads/news/' . $row->id . '.jpg';
								$image_path = $assets_path . 'uploads/news/' . $row->id . '.jpg';
							} else {
								$image_url = $assets_url . 'uploads/news/no-image.jpg';
								$image_path = $assets_path . 'uploads/news/no-image.jpg';
							}

							//recalculate width and height

							list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);

							//$height_new = 70;
							//$width_new = $width_orig / $height_orig * $height_new;

							$width_new = 80;
							$height_new = $height_orig / $width_orig * $width_new;

							if ($i > 0) {
								echo '<tr><td colspan="2"><hr /></td></tr>';
							}

							echo '<tr valign="top"><td width="90">';

							echo '<img src="' . $image_url . '" width="' . $width_new . 'px" height="' . $height_new . 'px" alt="' . htmlspecialchars($row->news_title) . '" />';

							echo '</td>';
							echo '<td valign="top">';

							echo '<strong> <font color="#000000">' . htmlspecialchars($row->news_title) . '</font></strong> <span class="smallgrey" style="float:right;">' . $row->submitted . '</span><br />';
							//echo "<font face='Verdana' size='1'>" . stripslashes(substr($row['news_text'],0,400)) . "&nbsp;<a target='_new' href='viewNewsBlock.php?id={$row['id']}'>...</a></font>";
							echo parse_bbcode(nl2br(htmlspecialchars($row->news_text)));
							echo '</td></tr>';
						}
						$i++;
					}
					?>
                </table>

            </td>
            <td width="50%">

                <a class="twitter-timeline" data-dnt="true" href="https://twitter.com/euroharmony"
                   data-widget-id="448916639652651008">Tweets by @euroharmony</a>
                <script>!function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = p + "://platform.twitter.com/widgets.js";
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, "script", "twitter-wjs");</script>


            </td>
        </tr>
    </table>

</div>

<br/>


<?php
/*
<center>
<img src="<?php echo $assets_url; ?>images/content/banners/thankyou.jpg" alt="Thank You" />
</center>

<br />
<img src="<?php echo $tmpl_image_url; ?>home/partners.gif" alt="Partners" /><br />
<div class="container">
<table width="100%">
<tr>
<td width="50%" align="center"><a href="http://www.ivao.aero/"><img src="<?php echo $image_url; ?>content/partners/ivao_logo.gif" alt="IVAO" /></a></td>
<td width="50%" align="center"><a href="http://www.vatsim.net/"><img src="<?php echo $image_url; ?>content/partners/vatsim_logo.gif" alt="VATSIM" /></a></td>
</tr>
</table>
</div>



<script type="text/javascript">
	var so = new SWFObject('<?php echo $featured_video; ?>','harmonyvid','600','365','9');
	so.addParam('allowfullscreen','true');
	so.addParam('allowscriptaccess','always');
	so.write('ehmvideo');
</script>
*/

?>
