<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth_fns {

	function hash_password($user_id, $pwd) {
		$hashed_pwd = sha1($user_id . $pwd);
		return $hashed_pwd;
	}

	function generate_password($length = 8) {
		$vowels = 'aeuy';
		$vowels .= "AEUY";
		$consonants = 'bdghjmnpqrstvz';
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
		$consonants .= '23456789';
		//$consonants .= '@#$%';

		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

	function generate_username() {

		//grab all pilot ids from database
		$CI =& get_instance();

		$today = date('Y-m-d');

		//grab the term from the database
		$query = $CI->db->query("	SELECT 	
									username
									
							FROM pilots 
							
							ORDER BY username
							ASC
						");

		$num_results = $query->num_rows();
		$results = $query->result();

		$username = '-';
		$selected = FALSE;

		$taken_usernames = array();

		if ($num_results == 0) {
			//issue first id
			$username = '0001';
		} else {
			//iterate through all the id's and assign the first unassigned id

			foreach ($results as $row) {

				$taken_usernames[$row->username] = $row->username;

			}

			//now iterate through usernames, checking each one is not in the array
			$i = 2662;
			while ($i <= 9999) {

				if (!array_key_exists(str_pad((int)$i, 4, "0", STR_PAD_LEFT), $taken_usernames)) {
					$username = $i;
					break;
				}

				$i++;
			}

		}

		//deal with the case where all ids are taken or the id is assigned out of bounds
		if ($username == '-' || $username > '9999') {
			//we didn't set the id during the run.
			//echo 'Num_results: '.$num_results;
			//echo 'Username: '.$username;
			return FALSE;
		} else {

			$username = str_pad((int)$username, 4, "0", STR_PAD_LEFT);
			//echo 'username: '.$username.'<br />';
			return $username;
		}

	}

}
/* End of file */