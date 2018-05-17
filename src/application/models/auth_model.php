<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model {

	function write_passchange_internal($id, $hashed_password) {

		//WRITE NEW PASSWORD TO DB
		$pass_data = array('password' => $hashed_password);
		$this->db->where('id', $id);
		$this->db->update('pilots', $this->db->escape($pass_data));

		if ($this->db->affected_rows() > 0) {
			$data['exception'] = 'Password Changed.';
		} else {
			$data['exception'] = 'Password not changed.<br /><br />You may have tried to change the password to itself.';
		}

		return $data;

		//close write_passchange_internal
	}

	function get_passchange_output($user_id) {

		//grab configuration data from the database

		$query = $this->db->query("SELECT 	id, 
													password, 
													username, 
													fname, 
													sname 
				FROM pilots 
				WHERE id = '$user_id'");

		//grab result
		$result = $query->result();
		$num_rows = $query->num_rows();

		//ensure data clear
		$dbpassword = '';

		if ($num_rows > 0) {
			//
			foreach ($result as $row) {
				if ($row->password) {
					$dbpassword = $row->password;
					$id = $row->id;
					$username = $row->username;
					$fname = $row->fname;
					$sname = $row->sname;
				} else {
					$dbpassword = '';
					$id = $row->id;
					$username = $row->username;
					$fname = $row->fname;
					$sname = $row->sname;
				}
			}
		} else {
			$fname = '';
			$sname = '';
			$id = '';
		}

		$data['fname'] = $fname;
		$data['sname'] = $sname;
		$data['user_id'] = $id;

		return $data;

		//close get_passchange_output
	}

//close class
}

?>
