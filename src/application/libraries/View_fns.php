<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class View_fns {

	function view($page, $data) {
		//allow access to CI functions
		$CI =& get_instance();

		$CI->load->view('global/structure/head', $data);
		$CI->load->view('templates/' . $data['template'] . '/header', $data);
		if (!isset($data['right_bar']) || $data['right_bar'] != '0') {
			//load right_bar
			if (isset($data['no_links']) && $data['no_links'] == '1') {
				$CI->load->view('global/right_bar_nl', $data);
			} elseif (isset($data['admin_menu']) && $data['admin_menu'] == 1) {
				$CI->load->view('global/right_bar_admin', $data);
				$CI->load->view('templates/' . $data['template'] . '/content_wrap_open', $data);
			} else {
				$CI->load->view('global/right_bar', $data);
				//load content wrap
				$CI->load->view('templates/' . $data['template'] . '/content_wrap_open', $data);
			}

		} else {
			$CI->load->view('global/spacer', $data);
		}
		$CI->load->view($page, $data);
		if ((!isset($data['right_bar']) || $data['right_bar'] != '0')
			&& (!isset($data['no_links']) || $data['no_links'] != '1')) {
			$CI->load->view('templates/' . $data['template'] . '/content_wrap_close', $data);
		}
		$CI->load->view('templates/' . $data['template'] . '/footer', $data);
		$CI->load->view('global/structure/foot', $data);
	}

//end of class
}

?>
