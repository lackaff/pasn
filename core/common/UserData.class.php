<?php

/**
 * Class UserData
 * 
 * Contains functions for accessing and manipulating user data in the database.
 *
 */
class UserData {
	
	/**
	 * Returns all the information in the users table for a particular ID
	 *
	 * @param integer $uid -- id of the user to get information on
	 * @return array of fields which contain information about the user
	 */
	public static function GetUserInfoByID($uid) {
		$uid = DB::escape($uid);
		return DB::get_row("SELECT * FROM users WHERE id = $uid");
	}
	
	/**
	 * Returns all the information in the users table for a particular api_uid
	 *
	 * @param integer $api_uid -- api_uid of the user to get information on (from SocialAPI)
	 * @return array of fields which contain information about the user
	 */
	public static function GetUserInfoByAPI_UID($api_uid) {
		$api_uid = DB::escape($api_uid);
		return DB::get_row("SELECT * FROM users WHERE api_uid = $api_uid");
	}

/**
	 * Returns all the information in the users table for a particular email
	 *
	 * @param integer $api_uid -- api_uid of the user to get information on (from SocialAPI)
	 * @return array of fields which contain information about the user
	 */
	public static function GetUserInfoByEmail($email) {
		$email = DB::escape($email);
		return DB::get_row("SELECT * FROM users WHERE email = '$email'");
	}
	
	/**
	 * Adds a user into the database
	 *
	 * @param integer $api_uid api_uid for the user to add (from SocialAPI)
	 * @param string $fname user's first name
	 * @param string $lname user's last name
	 * @param string $email user's email address
	 */
	public static function AddUser($api_uid,$fname,$lname,$email) {
		$api_uid = DB::escape($api_uid);
		$fname = DB::escape($fname);
		$lname = DB::escape($lname);
		$email = DB::escape($email);
		
		
		$query = "INSERT INTO users SET 
			api_uid = $api_uid, 
			first_name = '$fname', 
			last_name = '$lname', 
			email = '$email'";
		
		return DB::query($query);
	}

	public static function GetUserSurveyInfo($uid) {
		$query = "SELECT id,submit_timestamp  
			FROM survey_submissions WHERE user_id = $uid 
			ORDER BY submit_timestamp";
		return DB::get_results($query);
	}
	
}


?>
