<?php

class InternalFriendManager extends CodonModule
{
	function Controller()
	{
		
		switch ($this->get->page)
		{
			case '':
				
				break;
				
			case 'scan':
				$uid = SessionManager::GetData('localid');
				
				// Get alters from SocialAPI and from our DB so we can compare which alters are new
				$SocialAPIFriendsList = SessionManager::GetData('friends');
				
				// Insert new alters and return their infos
				$newfriends = AlterBucketData::ProcessNewAlters($uid,$SocialAPIFriendsList);
				
				// Find the users' available buckets, and display those too
				$buckets = AlterBucketData::GetUsersBuckets($uid);

			//	$processalters = NetworkGeneration::ProcessAPINetwork($uid);

				Template::Set('buckets',$buckets);
				Template::Set('newfriends_statusmessage', count($newfriends) . ' out of your ' . count($SocialAPIFriendsList) . 'contacts have not been added yet.');
				Template::Set('newfriends',$newfriends);
				Template::Show('internalfriendmanager_scan.tpl');
				Template::Show('internalfriendmanager_bucketsidebar.tpl');
				
				break;
				
		}
	}
}
?>
