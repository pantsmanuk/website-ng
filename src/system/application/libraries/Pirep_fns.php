<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


class Pirep_fns {


	function get_loadout($max_pax = NULL, $max_cargo = NULL){

		if($max_pax == NULL || $max_pax == 0){
			$data['pax'] = 0;
		}
		else{
			$max_sub = floor($max_pax / 35);
			if($max_sub <= 5){
				$max_sub = 5;
			}
			if($max_sub >= $max_pax){
				$max_sub = $max_pax;
			}

			$pax_sub = rand(0, $max_sub);
			$data['pax'] = $max_pax - $pax_sub;
		}

		if($max_cargo == NULL || $max_cargo == 0){
			$data['cargo'] = 0;
		}
		else{
			$max_sub = floor($max_cargo / 35);
			if($max_sub <= 5){
				$max_sub = 5;
			}
			if($max_sub >= $max_cargo){
				$max_sub = $max_cargo;
			}

			$cargo_sub = rand(0, $max_sub);
			$data['cargo'] = $max_cargo - $cargo_sub;
		}

		return $data;
	}

	function calculate_blocktime_minutes($enginestart_hh = NULL, $enginestart_mm = NULL, $engineoff_hh = NULL, $engineoff_mm = NULL){

		$blocktime = 0;

		//verify that all required parameters received
		if(		$enginestart_hh != NULL
			&&	$enginestart_mm != NULL
			&&	$engineoff_hh != NULL
			&&	$engineoff_mm != NULL){

			//convert start to minutes
			$start_mins = (60*$enginestart_hh) + $enginestart_mm;

			//convert off to minutes
			$off_mins = (60*$engineoff_hh) + $engineoff_mm;

			//boundary check
			if($off_mins <= $start_mins){
				//must have crossed midnight **********************
				//difference to midnight
				$to_midnight = (24*60) - $start_mins;

				//difference from midnight is the value of offmins;
				//sum differences
				$blocktime = $to_midnight + $off_mins;

			}
			else{
				//normal difference
				$blocktime = $off_mins - $start_mins;
			}

		}

		return $blocktime;
	}


	function calculate_flightdates(	$enginestart_hh = NULL, $enginestart_mm = NULL,
									$takeoff_hh = NULL, $takeoff_mm = NULL,
									$landing_hh = NULL, $landing_mm = NULL,
									$engineoff_hh = NULL, $engineoff_mm = NULL, $flightdate = NULL){


		if($enginestart_hh == NULL || $enginestart_mm == NULL
		 ||$takeoff_hh == NULL || $takeoff_mm == NULL
		 ||$landing_hh == NULL || $landing_mm == NULL
		 ||$engineoff_hh == NULL || $engineoff_mm == NULL || $flightdate == NULL){

		return FALSE;

		}
		else{


			$start_mins = (60*$enginestart_hh) + $enginestart_mm; //convert start to minutes
			$take_mins = (60*$takeoff_hh) + $takeoff_mm; //convert takeoff to minutes
			$land_mins = (60*$landing_hh) + $landing_mm; //convert landing to minutes
			$off_mins = (60*$engineoff_hh) + $engineoff_mm; //convert off to minutes

			//first check to see if midnight was crossed (boundary check)
			if($off_mins <= $start_mins){

				//get the day before flightdate
				$flightdate_prev = date('Y-m-d', (strtotime($flightdate) - (24 * 60 * 60)));

				//must have crossed midnight, need to find out where
				if($off_mins < $land_mins){
					//was after landing
					$start_date = $flightdate_prev;
					$take_date = $flightdate_prev;
					$land_date = $flightdate_prev;
					$off_date = $flightdate;
				}
				elseif($land_mins < $take_mins){
					//was after takeoff
					$start_date = $flightdate_prev;
					$take_date = $flightdate_prev;
					$land_date = $flightdate;
					$off_date = $flightdate;
				}
				elseif($take_mins < $start_mins){
					//was after start
					$start_date = $flightdate_prev;
					$take_date = $flightdate;
					$land_date = $flightdate;
					$off_date = $flightdate;
				}
			}
			else{
				//did not cross midnight
				$start_date = $flightdate;
				$take_date = $flightdate;
				$land_date = $flightdate;
				$off_date = $flightdate;

			}


			$data['start_date'] = $start_date.' '.$enginestart_hh.':'.$enginestart_mm.':00';
			$data['take_date'] = $take_date.' '.$takeoff_hh.':'.$takeoff_mm.':00';
			$data['land_date'] = $land_date.' '.$landing_hh.':'.$landing_mm.':00';
			$data['off_date'] = $off_date.' '.$engineoff_hh.':'.$engineoff_mm.':00';

			return $data;

		}


	}


	function verify_email($email = NULL){

		//set bool as false
		$bool = FALSE;

		//handle no var case
		if($email == NULL || $email == ''){
			$bool = FALSE;
		}
		else{
			//check there is at least one @
			if(substr_count($email, '@') > 0){
				//split the email on the @
				list($userName, $mailDomain) = explode("@", $email);
				if (!checkdnsrr($mailDomain, "MX")) {
					$bool = FALSE;
				}
				else{
					$bool = TRUE;
				}
			}
		}

	return $bool;
	}

}
/* End of file */