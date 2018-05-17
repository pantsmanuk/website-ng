<?php

$disc_array = array(

	'Aerosoft' => array('url' => 'http://en.shop.aerosoft.com/eshop.php?action=home&shopfilter_category=Flight+Simulation&s_design=DEFAULT&s_language=english',
		'image' => $image_url . 'content/partners/aerosoft.png',
		'description' => 'You can purchase any of the VA edition products. You must leave your name and VA ID in the comments field during purchase so they can see you are on our roster. They then process the order manually when they see it, normally this is done in a matter of a few hours but within 24, please do not write to them regarding serial keys until at least 24 hours after ordering as this just causes them un-necessary work.',

	),

	'FS2Crew' => array('url' => 'http://www.fs2crew.com/cart/',
		'image' => $image_url . 'content/partners/fs2crew.png',
		'description' => 'At checkout, use the code<br /><br />

MHG488761OMV
<br /><br />
which gives a 30% discount on their products.',

	),

	'OryxSim' => array('url' => 'http://www.oryxsimstore.eu',
		'image' => $image_url . 'content/partners/oryxsim.png',
		'description' => 'The code below should give 20% at the OryxSim store, valid on all products. 
						<br /><br />
						This code can be used three times in total per user, and is valid till January 1st 2014.
<br /><br />
The code is: EHVA01',

	),

);

if ($this->session->userdata('logged_in') == '1' && $this->session->userdata('rank_id') >= 3) {
	//echo discounts
	foreach ($disc_array as $partner => $data) {
		echo '<h3><a href="' . $data['url'] . '" target="_new"><img src="' . $data['image'] . '" alt="' . $partner . '" /><br />' . $partner . ' Store</a></h3>';
		echo $data['description'];
		echo '<br /><br /><hr />';
	}
} else {
	//echo no dice
	echo '<br /><br />In order to be eligible for discounts, you must be at least Flight Captain. If you are a Flight Captain, please log in to see available payware discounts, otherwise please return to this page when you have logged a few more hours.';
}

?>