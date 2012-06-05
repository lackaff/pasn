<?php

class EdgeFinder extends CodonModule
{
	function Controller()
	{
		
		switch ($this->get->page)
		{
			case '':
				
				$uid = SessionManager::GetData('localid');
				
				// Get alters from SocialAPI and from our DB so we can compare which alters are new
				//$SocialAPIFriendsList = SessionManager::GetData('friends');
				
				// Insert new alters and return their infos
				//$newfriends = AlterBucketData::ProcessNewAlters($uid,$SocialAPIFriendsList);
				
				// Find the users' available buckets, and display those too
				//$buckets = AlterBucketData::GetUsersBuckets($uid);

				$processalters = NetworkGeneration::ProcessAPINetwork();

				Template::Set('localid',$localid);
				//Template::Set('newfriends_statusmessage', count($newfriends) . ' out of your ' . count($SocialAPIFriendsList) . 'contacts have not been added yet.');
				//Template::Set('newfriends',$newfriends);
				Template::Show('edgefinder.tpl');
				//Template::Show('internalfriendmanager_bucketsidebar.tpl');
				
				break;
				
		}
	}
}
?>
