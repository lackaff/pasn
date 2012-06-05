<?php
/**
 *
 * This PHP file can contain any initialization or
 * startup tasks that need to be done
 *
 */

/**
 * Do something before the engine loads any modules
 *
 */
function pre_module_load()
{
	
}

/**
 * Do something after engine loads
 */
function post_engine_load() {
	
	// ----------- API MODULE: Authentication and session-data population -------------------
	
	// Check if we need to be authenticated via API for requested module
	if (in_array(Vars::Get('module'),Config::Get('AUTH_REQUIRED_MODULES'))) {

		// Instantiate our API Object
		$apimodule = Config::Get('SOCIAL_API_MODULE');
		$fb = new ${apimodule}(SOCIAL_API_KEY, SOCIAL_API_SECRET_KEY);
		$fb->Authenticate();
		
		$userinfo = UserData::GetUserInfoByAPI_UID($fb->GetAPIUserID());
		$email = $userinfo->email;

		// Store information from api in the session
		SessionManager::AddData('session_started',true);
		SessionManager::AddData('api_uid',$fb->GetAPIUserID());
		SessionManager::AddData('first_name',$fb->GetUsersFirstName());
		SessionManager::AddData('last_name',$fb->GetUsersLastName());
		SessionManager::AddData('email',$email);
		SessionManager::AddData('friends',$fb->GetUsersFriends());
		// SessionManager::AddData('edge_list',$fb->GetUsersEdgelist());
		SessionManager::AddData('question_rules_completed', array());
		SessionManager::AddData('answered_questions',array());
		// SessionManager::AddData('alters_are_cached');
		// SessionManager::AddData('cached_alters');
		// SessionManager::AddData('survey_processed')
		// SessionManager::AddData('finished_network_qs')
		// SessionManager::AddData('processed_ext_alters')
		
		// find and store local id in session. if it's not stored then it
		// tells Dashboard.ValidateUser that we're a new user and it'll prompt
		// for the user's email
		$info = UserData::GetUserInfoByAPI_UID(SessionManager::GetData('api_uid'));
		if ($info) {
			$localid = $info->id; 
			SessionManager::AddData('localid',$localid);
		}		

	}
	
}

/**
 * Do something after they have been loaded
 *
 */
function post_module_load()
{
	// DEBUG
	echo "<!-- Debug: session information \n"; 
	// print_r(SessionManager::GetAllData());
	echo "-->";		
}
?>
