<?php

class ExternalFriendManager extends CodonModule
{
	function Controller()
	{		
		switch ($this->get->page)
		{
			case '':
				$localid = SessionManager::GetData('localid');
				
				Template::Show("externalfriendmanager_addfriend.tpl");
				
			// TODO show current external friends
				$currentExtFriends = AlterBucketData::GetAlters($localid, true);
				Template::Set('currentExtFriends',$currentExtFriends);
				Template::Show('externalfriendmanager_currentfriendsidebar.tpl');
				
				break;	

			case 'nameGenerator1':
				// Ensure all the users' alters are in a bucket.
				// 		We put this here because they should be loading this page after
				//		Doing their internal friend import, so there may be unsorted
				//		alters at this point. We run this to clean that up.
				$localid = SessionManager::GetData('localid');
				if (AlterBucketData::OrganizeOrphanAlters($localid) === true) {
			//		echo "<p><strong>NOTE:</strong> We've noticed that some of your friends weren't put into buckets, so we put those friends into a bucket called \"Unsorted Contacts\" for you.</p>";
				}
				
				Template::Show('externalfriendmanager_namegenerator1.tpl');
								
				
				break;

				case 'nameGenerator2':
				
				Template::Show('externalfriendmanager_namegenerator2.tpl');
								
				
				break;

				case 'nameGenerator3':
				
				Template::Show('externalfriendmanager_namegenerator3.tpl');
								
				
				break;

				case 'nameGenerator4':
				
				Template::Show('externalfriendmanager_namegenerator4.tpl');
								
				
				break;

				case 'nameGenerator5':
				
				Template::Show('externalfriendmanager_namegenerator5.tpl');
								
				
				break;

				case 'nameGenerator6':
			
				
				Template::Show('externalfriendmanager_namegenerator6.tpl');
								
				
				break;

				case 'nameGenerator7':
			
				
				Template::Show('externalfriendmanager_namegenerator7.tpl');
								
				
				break;
		}
	}
}
?>
