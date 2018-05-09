<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


class Profile_fns {


	function get_age($dob = NULL){

		if($dob == NULL){
			$age = '-';
		}
		else{
			//caluclate years difference between today and dob
			$age = floor((time() - strtotime($dob))/31556926);
		}


		return $age;
	}

}
/* End of file */