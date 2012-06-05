<?php

require_once(COMMON_PATH . '/SocialAPIInterface.class.php');
require_once(COMMON_PATH . '/facebookAPI/facebook.php');

class FacebookAPI implements SocialAPIInterface {
	
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
	
	/**
	 * This function will ensure the user is authenticated to
	 * whatever Social API you are using to gather egocentric
	 * network data. If they are not auth'd, it will prompt
	 * them to log in.
	 * 
	 * @return void. User should be redirected to index.php/Dashboard upon successful first login; access denied message shown upon fail. if the user is already logged in, do nothing.
	 *
	 */
	public function Authenticate() {
		
		if ($this->user === false) {
			$this->user = $this->fb->require_login();
		}
		
	}
	
	/**
	 * @return an integer user id from the Social API's database.
	 *
	 */
	public function GetAPIUserID() {
		$this->Authenticate();
		
		// Alternatively, use $this->fb->api_client->users_getLoggedInUser()
		return $this->user;
		
	}
	
	/**
	 * Gather friends of the currently logged in user -- that is,
	 * the egocentric network of alters. 
	 * 
	 * @return array of friend objects, with member variables $friend->uid, $friend->name, $friend->pic.
	 */
	public function GetUsersFriends() {
		
		$this->Authenticate();
		$friends_imploded = implode(',',$this->fb->api_client->friends_get());
		$friends = $this->fb->api_client->users_getInfo($friends_imploded,array('name','pic_square'));
		
		$return = array();
		foreach ($friends as $friend) {
			$friend_object = new stdClass;
			$friend_object->uid = $friend['uid'];
			$friend_object->name = $friend['name'];
			$friend_object->pic = $friend['pic_square'];
			
			$return[] = $friend_object;	
		}
		
		return $return;
	}
	
	/**
	 * @return string - The user's first name
	 */
	public function GetUsersFirstName() {
		$this->Authenticate();
		$info = $this->fb->api_client->users_getInfo(array("$this->user"),array('first_name'));
		$res = $info[0];
		return $res['first_name'];
	}
	
	/**
	 * @return string - The user's last name.
	 */
	public function GetUsersLastName() {
		$this->Authenticate();
		$info = $this->fb->api_client->users_getInfo(array("$this->user"),array('last_name'));
		$res = $info[0];
		return $res['last_name'];
	}

	public function GetUsersEdgelist() {
		$this->Authenticate();
		$edgelist = $this->fb->api_client->fql_query("SELECT uid1, uid2 FROM friend WHERE uid1 IN (SELECT uid2 FROM friend WHERE uid1 = 15717048) and uid2 IN (SELECT uid1 FROM friend WHERE uid2 = 15717048");
		return $edgelist;
	}

	
}

?>
