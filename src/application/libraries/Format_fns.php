<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Format_fns {

	function format_seconds_hhmm($duration, $mins = NULL) {

		if ($mins == '1') {
			//duration is in minutes
			$flightmins = $duration;
		} else {
			//duration is in seconds, get in minutes
			$flightmins = $duration / 60;
		}
		$flighthours = floor($flightmins / 60);

		$remainmins = floor($flightmins - ($flighthours * 60));;
		$time = $flighthours . ':' . str_pad((int)$remainmins, 2, "0", STR_PAD_LEFT);

		return $time;
	}

	function format_seconds_hhmm_verbose($duration, $mins = NULL) {

		if ($mins == '1') {
			//duration is in minutes
			$flightmins = $duration;
		} else {
			//duration is in seconds, get in minutes
			$flightmins = $duration / 60;
		}
		$flighthours = floor($flightmins / 60);

		$remainmins = floor($flightmins - ($flighthours * 60));

		if ($remainmins > 1) {
			$minute_text = 'mins';
		} elseif ($remainmins > 0) {
			$minute_text = 'min';
		} else {
			$minute_text = '';
			$remainmins = '';
		}

		if ($flighthours > 1) {
			$hour_text = 'hours';
		} elseif ($flighthours > 0) {
			$hour_text = 'hour';
		} else {
			$hour_text = '';
			$flighthours = '';
		}

		if ($minute_text != '' && $hour_text != '') {
			$hour_text .= ',';
		}

		$time = $flighthours . ' ' . $hour_text . ' ' . $remainmins . ' ' . $minute_text;

		return $time;
	}

	function lbs_tonnes($lbs) {

		return round($lbs * 0.00045359240000000003);

	}

	//borrowed from http://aidanlister.com/2004/04/making-time-periods-readable/
	function time_duration($seconds, $use = NULL, $zeros = FALSE, $abbreviate = FALSE) {
		// Define time periods
		if ($abbreviate == TRUE) {
			$periods = array(
				'yr' => 31556926,
				'mnths' => 2629743,
				'wks' => 604800,
				'days' => 86400,
				'hrs' => 3600,
				'mins' => 60,
				'secs' => 1,
			);
		} else {
			$periods = array(
				'years' => 31556926,
				'Months' => 2629743,
				'weeks' => 604800,
				'days' => 86400,
				'hours' => 3600,
				'minutes' => 60,
				'seconds' => 1,
			);

		}

		//use hours, if less than a day ago
		if ($use != NULL && $seconds < 86400) {
			$use = 'h';
		}

		//use minutes, if less than an hour ago
		if ($use != NULL && $seconds < 3600) {
			$use = 'm';
		}

		//use seconds if less than a minute ago
		if ($use != NULL && $seconds < 60) {
			$use = 's';
		}

		// Break into periods
		$seconds = (float)$seconds;
		$segments = array();
		foreach ($periods as $period => $value) {
			if ($use && strpos($use, $period[0]) === FALSE) {
				continue;
			}
			$count = floor($seconds / $value);
			if ($count == 0 && !$zeros) {
				continue;
			}
			$segments[strtolower($period)] = $count;
			$seconds = $seconds % $value;
		}

		// Build the string
		$string = array();
		foreach ($segments as $key => $value) {
			$segment_name = substr($key, 0, -1);
			$segment = $value . ' ' . $segment_name;
			if ($value != 1) {
				$segment .= 's';
			}
			$string[] = $segment;
		}

		$return_data = implode(', ', $string);

		if ($return_data == '') {
			$return_data = '1 second';
		}

		return $return_data;

	}

}
/* End of file */