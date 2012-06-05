<?php

class Dashboard extends CodonModule
{
	function Controller()
	{
		
		switch ($this->get->page)
		{
			case '':
				
				// Check to see if they're in our database. If they aren't, prompt
				// them for email and add them in 
				if (!$this->ValidateUser()) {
					return;
				}
		
				// TODO set this var
				Template::Set('previous_survey_dates',null);
				Template::Set('fname',SessionManager::GetData('first_name'));
				Template::Show('dashboard.tpl');				
				
				break;
				
			case 'emailSubmit':
				$email = Vars::POST('email');
				
				if ($email != '') {
					
					echo "<p>Validating...</p>";
					$api_uid = SessionManager::GetData('api_uid');
					$fname = SessionManager::GetData('first_name');
					$lname = SessionManager::GetData('last_name');
					UserData::AddUser($api_uid, $fname, $lname, $email);
				
					//populate session info
					$info = UserData::GetUserInfoByAPI_UID(SessionManager::GetData('api_uid'));
					$localid = $info->id; 
					SessionManager::AddData('localid',$localid);
					SessionManager::AddData('email',$email);
					
					// Add in default buckets
					AlterBucketData::AddDefaultBuckets($localid);
					
					echo "<p>All done! We've set <strong>$email</strong> as your email address. ";
					echo '<a href="'.SITE_URL.'/index.php/Dashboard" title="Continue" class="next">Click here to continue.</a></p>';
					
				} else {
					
					echo "<p>Looks like you didn't enter your email address. Please go back and ";
					echo '<a href="'.SITE_URL.'">start over.</p>';
				}
				
				break;

		}
	}
	
	/**
	 * This function checks to see if a user is already in the database
	 * by using their api_uid given by the SocialAPI. If not, they're
	 * added into the database with their particular information.
	 *
	 */
	private function ValidateUser() {
		
		// Check if they're not a user in our database already.
		if (!SessionManager::GetData('localid')) {
			
			echo "<p>Please enter your email address (the same one used in part one of this study): </p>";
			
			
			$f = new CodonForm();
			$f->StartForm(array(
					'name' => 'emailForm',
					'url' => 'Dashboard/emailSubmit',
					'ajax' => false,
					'method' => 'post'
			));
			$f->Textbox('Your email address: ','email');
			$f->Submit('Submit','Let\'s Go! &raquo;');
			$f->ShowForm();
			
			return false;
		}
		
		return true;
	
	}
}
?>
