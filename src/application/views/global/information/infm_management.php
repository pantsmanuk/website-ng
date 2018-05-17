<?php

echo '<table class="boxed" width="100%">';

$i = 0;
$department = '';
foreach ($management_results as $row) {

	if ($i > 0) {
		echo '<tr>';
		echo '<td colspan="2"><hr /></td>';
		echo '</tr>';
	}

	if ($row->department != $department) {
		echo '<tr>';
		echo '<td colspan="2"><h3>' . $row->department . '</h3></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="2"><hr /></td>';
		echo '</tr>';

		$department = $row->department;
	}

	$mugshot_path = $image_path . 'content/mugshots/' . $row->id . '.jpg';

	if (file_exists($mugshot_path)) {
		$mugshot_url = $image_url . "content/mugshots/" . $row->id . '.jpg';
	} else {
		$mugshot_url = $image_url . 'content/mugshots/missing.jpg';
	}

	$pips_val = '';
	if ($row->pips != '') {
		$pips_val = '_' . $row->pips;
	}

	echo '<tr valign="top">';
	echo '<td align="center" width="80"><img src="' . $mugshot_url . '" alt="' . $row->fname . '" width="75" height="90" class="borderedimage" /></td>';
	echo '<td align="left">';
	echo '<div class="smallgrey" style="float: right;" align="right"><img src="' . $image_url . 'ranks/' . $row->rank_id . $pips_val . '.png" alt="Rank" />';
	echo '<br /><br /><br /><br /><br />Member since ' . date('d/m/Y', strtotime($row->signupdate));
	echo '</div>';
	echo '<span class="head3">[EHM-' . $row->username . '] ' . $row->fname . ' ' . $row->sname . '</span>';
	echo '<br /><span class="sub3">' . $row->title . '</span>';
	/*
	if(is_numeric($row->pips)){
	
		echo '<br />';
		$j = 1;
		while($j <= $row->pips){
			echo '*';
		$j++;
		}
	}
	*/

	//flag
	echo '<br /><br /><img src="' . $image_url . 'icons/flags/' . $row->country_code . '.gif" alt="' . $row->country_code . '" />';

	echo '<br /><br /><span class="sub3"><a href="' . $base_url . 'contact/index/' . $row->id . '">Contact ' . $row->fname . '</a></span>';

	echo '</td>';
	echo '</tr>';
	$i++;
}

echo '</table>';

?>