<?php

/**
 * Class AlterBucketData
 * 
 * Contains functions for accessing and manipulating alter and bucket data in the database.
 *
 */

require_once(COMMON_PATH . '/SocialAPIInterface.class.php');
require_once(COMMON_PATH . '/FacebookAPI.class.php');
require_once(COMMON_PATH . '/facebookAPI/facebook.php');

class AlterBucketData {
	
	// ===================== ALTER FUNCTIONS =========================================
	
	/**
	 * Add an alter to the database.
	 *
	 * @param integer $user_id -- local id of the user that this alter is friends with 
	 * @param string $name -- an identifying name for the alter.
	 * @param integer $api_uid -- optional. if the alter is a friend from a SocialAPI, this is their api_uid
	 */
	public static function AddAlter($user_id, $name, $api_uid = 'NULL', $img_url = 'NULL') {
		$user_id = DB::escape($user_id);
		$api_uid = DB::escape($api_uid);
		$name = DB::escape($name);
		
		$query = "INSERT INTO alters SET user_id = $user_id, name = '$name', api_uid = $api_uid, img_url = '$img_url', timestamp = NOW()";
		return DB::query($query);		
	}
	
	/**
	 * Get information in the db on a specific alter.
	 *
	 * @param integer $alter_id the id of the alter you want info for
	 * @return object row of information from the db
	 */
	public static function GetAlter($alter_id) {
		$alter_id = DB::escape($alter_id);
		
		$query = "SELECT * FROM alters WHERE id = $alter_id";
		return DB::get_row($query);
	}
	
	/**
	 * This function adds alter_id2 to alter_id1's network in the database
	 * (`network` field, of the alters table)
	 *
	 * @param unknown_type $alter_id1
	 * @param unknown_type $alter_id2
	 */
	public static function AddToAlterNetwork($alter_id1, $alter_id2) {
		$alter_id1 = DB::escape($alter_id1);
		$alter_id2 = DB::escape($alter_id2);
		
		$network = DB::get_var("SELECT network FROM alters WHERE id = $alter_id1");
		$network .= "$alter_id2,";
		
		$query = "UPDATE alters SET network = '$network' WHERE id = $alter_id1";
		DB::query($query);
		
		return true;
	}


	/**
	 * Merge an external alter with an alter from a SocialAPI. This is performed when
	 * a user has an external friend/alter, but then finds that same friend/alter
	 * is in their PASN friends list because it was grabbed from the SocialAPI.
	 * 
	 * This function will (1) delete the version of the alter imported from
	 * the SocialAPI (the one with an api_uid) and (2) keep the 'external alter' 
	 * row in the database, but convert it to an 'internal alter' by updating its
	 * name and api_uid fields to information given from the SocialAPI. 
	 *
	 * @param integer $api_uid -- the id of the alter in question from the SocialAPI
	 * @param string $api_name -- the name of the alter in question from the SocialAPI
	 * @param integer $external_alter_id -- the local id of the alter in the alters table which correlates to the 'external friend' (i.e.: doesn't have a api_uid from a SocialAPI)
	 * @param integer $internal_alter_id -- the local id of the alter in the alters table which correlates to the friend imported from the SocialAPI. the row in the alters table with this id has an api_uid in its api_uid field. 
	 */
	public static function MergeAlter($api_uid, $api_name, $external_alter_id, $internal_alter_id) {
		$api_uid = DB::escape($api_uid);
		$api_name = DB::escape($api_name);
		$external_alter_id = DB::escape($external_alter_id);
		$internal_alter_id = DB::escape($internal_alter_id);
		
		// Update the 'external alter' to an 'internal alter' (an alter imported from SocialAPI)
		$query1 = "UPDATE alters SET name = '$api_name', api_uid = $api_uid WHERE id = $external_alter_id";
		DB::query($query1);
		
		// Delete the old 'internal alter'
		$query2 = "DELETE FROM alters WHERE id = $internal_alter_id";
		DB::query($query2);

		return true;
	}
	
	/**
	 * Get all the alters for a given user id.
	 *
	 * @param integer $uid -- the user id whose alters you want.
	 * @param boolean $externalOnly true if you want it to return external alters only
	 * @return array -- an array of alters from the database and all corresponding fields of information for them 
	 */
	public static function GetAlters($uid, $externalOnly = false) {
		
		// Check if alters are cached in session or not. if they are, give them the cached alters
		//if (SessionManager::GetData('alters_are_cached') == false || SessionManager::GetData('alters_are_cached') == '' ) {		
			
			$uid = DB::escape($uid);
			$query = "SELECT alters.id, alters.name, alters.img_url FROM alters, alters_buckets, buckets WHERE alters.user_id = $uid";
			// edited below to include ALL alters in buckets
			if ($externalOnly) { $query .= " AND alters_buckets.alter_id = alters.id AND alters_buckets.bucket_id = buckets.id AND buckets.name != 'family' AND buckets.name NOT LIKE 'unsorted'"; }
			$query .= " ORDER BY alters.name";	
			$res = DB::get_results($query);
		//	SessionManager::AddData('cached_alters', $res);
		//	SessionManager::AddData('alters_are_cached', true);
			
		//} else {
		//	$res = SessionManager::GetData('cached_alters');
		//}
				
		return $res;
	}
	
	
	/**
	 * Return an array of alter objects; each alter with member variables $alter->uid, $alter->name, $alter->pic
	 *
	 * @param integer $uid the user's local id
	 * @param array $alters an array of alter objects to scan for new alters
	 * @return an array of alter objects of alters which are new and need to be inserted into the db.
	 */
	public static function GetNewAlters($uid,$alters=array()) {
		// Get the alters we know about
		$res = self::GetAlters($uid);
		if (!$res) { $res = array(); } // If they have no alters.
		$registered_alters_uids = array();
		foreach ($res as $row) {
			$registered_alters_uids[] = $row->api_uid;
		}
		
		// Find which are new and add them to an array
		$new_alters = array();
		foreach ($alters as $alter) {
			if (!in_array($alter->uid,$registered_alters_uids)) {
				$new_alters[] = $alter;
			}
		}
		
		return $new_alters;
	}
	
	/**
	 * Function for processing alters
	 *
	 * @param integer $uid localid of the user whose alters we're processing
	 * @param array $currentAlters an array of the users' current alters from SocialAPI,with members alter->uid (api_uid), alter->name, alter->pic. 
	 * @return array array of new alter objects with members alter->id (localid), alter->name, alter->pic. 
	 */
	public static function ProcessNewAlters($uid,$currentAlters) {
		$dbAlters = self::GetAlters($uid);
		if (count($dbAlters) == 0) { $dbAlters = array(); }
		$dbAltersAPI_UIDList = array();
		// Put together a list of alters' api_uid's (alters from DB)
		foreach ($dbAlters as $alter) {
			array_push($dbAltersAPI_UIDList,$alter->api_uid);
		}
		
		// Put together a list of current alters' api_uid's
		$currentAltersAPI_UIDList = array();
		if (count($currentAlters) == 0) { $currentAlters = array(); }
		foreach ($currentAlters as $alter) {
			array_push($currentAltersAPI_UIDList,$alter->uid);
		}
		
		// Keep PHP from turning a 1-element array into just a single variable.
		if (count($dbAltersAPI_UIDList) == 1) { $dbAltersAPI_UIDList = array(0 => $dbAltersAPI_UIDList); }
		if (count($currentAltersAPI_UIDList) == 1) { $currentAltersAPI_UIDList = array(0 => $currentAltersAPI_UIDList); }
		
		// $new_alters is a list of API_UID's of new alters only.
		//$new_alters = $currentAltersAPI_UIDList,$dbAltersAPI_UIDList);
		$new_alters = $currentAltersAPI_UIDList; //need them all for 1shot survey
				
		// For each of the users' SocialAPI alters, if they're new, insert them into the DB -- constructing alter objects for return along the way.
		$ret = array();
		foreach ($currentAlters as $alter) {
			if (in_array($alter->uid, $new_alters)) {
								
				self::AddAlter($uid,$alter->name,$alter->uid,$alter->pic);
				$temp = new stdClass;
				$temp = $alter;
				$temp->id = DB::$insert_id;
				
				array_push($ret,$temp);
			}
		}
		
		return $ret;
	}

	
	/**
	 * This function finds all alters not assigned to any buckets
	 * and assigns them to the 'Unsorted Contacts' bucket.
	 *
	 */
	function OrganizeOrphanAlters($user_id) {
		if ($user_id == '') return false;
				
		// Select all of user's alters WHERE they are not in all the user's alters assigned to buckets.
		$sql = "SELECT id FROM alters a
  				WHERE
    				a.user_id = $user_id
					AND a.id NOT IN
    				(
      					SELECT ab.alter_id
      					FROM alters_buckets ab, alters a
						WHERE
        					a.user_id = $user_id
        				AND ab.alter_id = a.id
    				)
				";
    	$res = DB::get_col($sql);
    	
    	if (count($res) == 0) {
    		return false;
    	} else {
    		// Find the unsorted alters bucket
    		$misc_bucket_id = DB::get_row("SELECT id FROM buckets WHERE user_id = $user_id AND name LIKE 'unsorted'");
    		$misc_bucket_id = $misc_bucket_id->id;
    		
    		if ($misc_bucket_id == '') {
    			return false;
    		}
    		
    		// Add them all into the unsorted alters bucket
    		foreach ($res as $id) {
    			self::AddAlterToBucket($id, $misc_bucket_id);
    		}
    		
    		return true;
    	}
		
	}
	// ===================== BUCKET FUNCTIONS =========================================
	
	/**
	 * Add a bucket for a user
	 *
	 * @param integer $uid -- the user who for which this bucket is created
	 * @param string $name -- the name for the bucket
	 */
	public static function AddBucket($uid,$name) {
		$uid = DB::escape($uid);
		$name = DB::escape($name);
		
		return DB::query("INSERT INTO buckets SET user_id = $uid, name = '$name'");
	}
	
	/**
	 * Get information on a certain bucket from the database.
	 *
	 * @param integer $bucket_id the id of the bucket you want info on 
	 * @return object the row of info from the database
	 */
	public static function GetBucket($bucket_id) {
		$bucket_id = DB::escape($bucket_id);
		$query = "SELECT * FROM buckets WHERE id = $bucket_id";
		return DB::get_row($query);
	}
	
	/**
	 * Add the Default Buckets for a user when that user is created.
	 *
	 * @param integer $uid user id whom the buckets belong to
	 * @return true
	 */
	public static function AddDefaultBuckets($uid) {
		$default_buckets = Config::Get('DEFAULT_BUCKETS');
		$query = "INSERT INTO buckets (`user_id`,`name`) VALUES ";
		foreach ($default_buckets as $bucket) {
			$query .= "(" . $uid . ",'" . $bucket . "'),";
		}
		
		// Get rid of that trailing comma!
		$query = substr($query,0,-1);
		DB::query($query);
		
		return true;		
	}
	
	/**
	 * Assign an alter to a bucket
	 *
	 * @param integer $alter_id -- local id of the alter to assign to the bucket
	 * @param integer $bucket_id -- local id of the bucket to assign the alter to
	 */
	public static function AddAlterToBucket($alter_id, $bucket_id) {
		$alter_id = DB::escape($alter_id);
		$bucket_id = DB::escape($bucket_id);
		
		return DB::query("INSERT INTO alters_buckets SET alter_id = $alter_id, bucket_id = $bucket_id");
	}
	
	/**
	 * Get all the buckets for a certain user id.
	 *
	 * @param integer $uid -- local id of the user whose buckets you want
	 * @return array -- array of the buckets including names and bucket id's
	 */
	public static function GetUsersBuckets($uid) {
		$uid = DB::escape($uid);
		
		return DB::get_results("SELECT id,name FROM buckets WHERE user_id = $uid and name NOT LIKE 'unsorted'");
	}
	
	/**
	 * Get all the alters assigned to a certain bucket for a certain user
	 *
	 * @param integer $bucket_id -- local id of the bucket whose alters you want
	 * @return unknown
	 */
	public static function GetAltersInBucket($bucket_id) {
		$bucket_id = DB::escape($bucket_id);
		
		$query = "SELECT * FROM alters a
					LEFT JOIN alters_buckets ab ON
					  (a.id = ab.alter_id)
					WHERE
					  bucket_id = $bucket_id
					AND network IS NULL
					ORDER BY name ASC";
		
		return DB::get_results($query);
		// this query took long.
		// return DB::get_results("SELECT * FROM alters WHERE id IN (SELECT alter_id FROM alters_buckets WHERE bucket_id = $bucket_id) ORDER BY name ASC");
	}
	
	
}


?>
