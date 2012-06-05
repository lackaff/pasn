<?php

interface SocialAPIInterface {
	// TODO sync this up with facebookapi.class.php
	

	/**
	 * This function will ensure the user is authenticated to
	 * whatever Social API you are using to gather egocentric
	 * network data. If they are not auth'd, it will prompt
	 * them to log in.
	 * 
	 * @return void. User should be redirected to index.php/Dashboard upon success; access denied message shown upon fail.
	 *
	 */
	public function Authenticate();
	
	/**
	 * @return an integer user id from the Social API's database.
	 *
	 */
	public function GetAPIUserID();
	
	/**
	 * Gather friends of the currently logged in user -- that is,
	 * the egocentric network of alters. 
	 * TODO: more docs on this
	 *
	 */
	public function GetUsersFriends(); 
}

?>