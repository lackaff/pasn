<?php

class SurveySubmissionInfo extends CodonModule
{
	function Controller()
	{
		
		switch ($this->get->page)
		{
			case '':
				
				$submit_info = UserData::GetUserSurveyInfo(SessionManager::GetData('localid'));
				$name = SessionManager::GetData('first_name') . ' ' . SessionManager::GetData('last_name');
				Template::Set('name',$name);
				Template::Set('submit_info',$submit_info);
				Template::Set('submit_id',SessionManager::GetData('survey_processed_id'));
				Template::Show('survey_submission_info.tpl');
				
				break;
		}
	}
	
}
?>