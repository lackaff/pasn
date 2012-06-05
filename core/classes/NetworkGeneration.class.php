<?php

/**
 * Class NetworkGeneration
 * 
 * Contains functions for accessing and manipulating alter and bucket data in the database.
 *
 */

require_once(COMMON_PATH . '/SocialAPIInterface.class.php');
require_once(COMMON_PATH . '/facebookAPI/facebook.php');

class NetworkGeneration extends FacebookAPI {

	var $apikey;
	var $apisecretkey;
	var $fb;
	var $user;
	
	public function __construct($apikey, $apisecretkey) {
		$this->apikey = $apikey;
		$this->apisecretkey = $apisecretkey;
		$this->user = false;
		
		$this->fb = new Facebook($this->apikey, $this->apisecretkey);
	}


public function ProcessAPINetwork($uid) {
		$APIuid = FacebookAPI->fb->api_client->users_getLoggedInUser();
		$edge_list = $this->fb->api_client->fql_query("SELECT uid1, uid2 FROM friend WHERE uid1 IN (SELECT uid2 FROM friend WHERE uid1 = $APIuid) and uid2 IN (SELECT uid1 FROM friend WHERE uid2 = $APIuid)");
	

		foreach ($edge_list as $edge) {
			
			$uid1 = $edge[uid1];
			$uid2 = $edge[uid2];

			$alter_id1 = DB::get_var("SELECT id from alters WHERE api_uid = $uid1 AND user_id = $uid");
			$alter_id2 = DB::get_var("SELECT id from alters WHERE api_uid = $uid2 AND user_id = $uid");
		
			$network = DB::get_var("SELECT network FROM alters WHERE id = $alter_id1");
			$network .= "$alter_id2,";
		
			$query = "UPDATE alters SET network = '$network' WHERE id = $alter_id1";
			DB::query($query);
		}
	}

}
?>

