<?php

/**
 * Class AJAXFunctions contains functions that are called via
 * action.php/AJAXFunctions/CASE. These functions are exempt from
 * SocialAPI authentication and therefore cannot be guaranteed
 * access to resources gained from that SocialAPI. 
 *
 */
class AJAXFunctions extends CodonModule
{
	function Controller()
	{		
		switch ($this->get->page)
		{
			
			// INTERNAL FRIEND MANAGER METHODS
			case 'alterBucketAssignment':
				// AJAX Call. The user has dragged an alter from the friends list to a bucket.
				
				$alter_id = $this->post->alter_id;
				$bucket_id = $this->post->bucket_id;
				
				AlterBucketData::AddAlterToBucket($alter_id, $bucket_id);
				$alterInfo = AlterBucketData::GetAlter($alter_id);
				$bucketInfo = AlterBucketData::GetBucket($bucket_id);
				echo "<p>Added $alterInfo->name to category $bucketInfo->name!</p>";				
				
				break;
				
			// EXTERNAL FRIEND MANAGER METHODS
			case 'addExternalFriend':
				// AJAX Call. The user has submitted the name of an external friend to create.
				
				$user_id = SessionManager::GetData('localid');
				if (!$user_id) { echo "<p>Error! Please clear your cookies and restart your browser.</p>"; }
				
				$name = DB::escape($this->post->name);
				$pattern = '/^[a-zA-Z ]*$/';
				
				// Check if they're trying to use a non alpha character for an alter name
				if (!preg_match($pattern, $name)) {
					// Don't process
					echo "<p>Invalid name! Please use only alphabetic characters and spaces.</p>";
				
				} else {
					
					AlterBucketData::AddAlter($user_id, $name);
					echo '<p>Added $name as an External Friend!</p>';
					
				}
				break;
				
			case 'addExternalFriendToBucket':
				// AJAX Call. The user is coming from the name generator and has submitted
				// a friend to be added into the 'X' bucket.
				
				// Get bucket and user information
				$bucket_name = DB::escape($this->post->bucket_name);
				$user_id = SessionManager::GetData('localid');
				if (!$user_id) { echo "<p>Error! Please clear your cookies and restart your browser.</p>"; }
				$buckets = AlterBucketData::GetUsersBuckets($user_id);
				foreach ($buckets as $bucket) {
					if ($bucket->name == $bucket_name) { $bucket_id = $bucket->id; }
				}
				
				$name = DB::escape($this->post->name);
				$pattern = '/^[a-zA-Z ]{3,}$/';
								
				// Check if they're trying to use a non alpha character for an alter name
				if (!preg_match($pattern, $name)) {
					// Don't process
					echo "<p>Invalid name! May only contain letters and spaces, minimum three letters.</p>";
				}
				else {
					// Add the alter into the DB
					AlterBucketData::AddAlter($user_id, $name);
					
					// Assign the alter to the correct bucket
					$alter_id = DB::$insert_id;
					AlterBucketData::AddAlterToBucket($alter_id, $bucket_id);
					
					echo "<p>Added $name to $bucket_name!</p>";
				}
				
				break;
				
			// SURVEY METHODS
			case 'alterSelectSubmit':
				// AJAX Call. The user has dragged an alter to the answer box in an alterselect question.
				
				$user_id = SessionManager::GetData('localid');
				$alter_id = $this->post->alter_id;
				$question_id = $this->post->question_id;
				
				// ---- Save this alter to this question's answer in the session. 
				$previous_answers = SessionManager::GetData('answered_questions');
				if (count($previous_answers) == 0 || $previous_answers == '') { $previous_answers = array(); }
				
				// If they've already answered this with other alters, add this alter on, else 
				// this is a new answer -- put this answer in the session
				if (in_array($question_id, array_keys($previous_answers))) {
					$previous_answers[$question_id] = $previous_answers[$question_id] . ",$alter_id"; 
				} else {
					$previous_answers[$question_id] = "$alter_id";
				}
				SessionManager::AddData('answered_questions', $previous_answers);
				
				break;
				
			case 'networkAlterSelectSubmit': 
				// AJAX Call. The user has dragged an alter to the answer box in a network alterselect question.
				
				$user_id = SessionManager::GetData('localid');
				$alter_id1 = $this->post->alter_id1;
				$alter_id2 = $this->post->alter_id2;
								
				AlterBucketData::AddToAlterNetwork($alter_id1,$alter_id2);
				DB::debug();
				
				break;
		}
	}
	
}
?>
