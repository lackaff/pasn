<?php

class Survey extends CodonModule
{
	function Controller()
	{
		
		switch ($this->get->page)
		{
			case '':
				echo "<!-- SESSION: \n[question_rules_completed]\n";
				print_r(SessionManager::GetData('question_rules_completed'));
				echo "\n[answered_questions]\n";
				print_r(SessionManager::GetData('answered_questions'));
				echo "-->";
				
				// If the Survey is closed, display closed message and end process
				if (Config::Get('SURVEY_CLOSED')) {
					echo "<p>".Config::Get('SURVEY_CLOSED_MESSAGE')."</p>";
					return;
				}
				
				// If a person submitted previous survey answers, update answered_questions and store answers in session.
				if (isset($this->post->Submit)) {
					foreach ($this->post as $key => $val) {
						if ($key != 'Submit') {
							$answered_questions[$key] = $val;
						}						
					}
					
					$previous_answers = SessionManager::GetData('answered_questions');
					if (count($previous_answers) == 0 || $previous_answers == '') { $previous_answers = array(); }
					if (count($answered_questions) == 0 || $answered_questions == '') { $answered_questions = array(); }
					$answered_questions = array_merge($answered_questions, $previous_answers);
					SessionManager::AddData('answered_questions', $answered_questions);					
				}
				 
				// Check if they've completed all the questions. If so, process answers and 
				// display a finished template. To do this we compare completed rules and
				// the rules defined in local.config.php
				$completed_rules = SessionManager::GetData('question_rules_completed');
				$question_rules = Config::Get('QUESTION_DISPLAY_RULES');
				
				if (count($question_rules) == count($completed_rules)) {
					
					// If the survey wasn't processed yet, submit their answers and set the processed flag
					if (!SessionManager::GetData('survey_processed')) {
						$user_answers = SessionManager::GetData('answered_questions');
						$id = QuestionData::StoreAnswers($user_answers);
						SessionManager::AddData('survey_processed_id',$id);
						SessionManager::AddData('survey_processed',true);
					}
					
					// Now move on to network info questions -- process this if it's enabled
					// and if they haven't finished all the network info questions yet
					if (Config::Get('GATHER_EXT_NETWORK_LINKS') && !SessionManager::GetData('finished_network_qs')) {
						
						// processNetworkQuestions returns false if they're not done with network questions yet 
						if (!$this->processNetworkQuestions()) {
							return true; // end execution of this controller and keep going with network q's.
						} else {
							// else continue on with survey finished info screen
							SessionManager::SetData('finished_network_qs',true);	
						}
						
					}
					
					
					// TODO ?
					$submit_info = UserData::GetUserSurveyInfo(SessionManager::GetData('localid'));
					$name = SessionManager::GetData('first_name') . ' ' . SessionManager::GetData('last_name');
					Template::Set('name',$name);
					Template::Set('submit_info',$submit_info);
					Template::Set('submit_id',SessionManager::GetData('survey_processed_id'));
					Template::Show('survey_finished.tpl');

					
					
					return true; // end execution of this controller
				}
				
				// Show the next question to be shown until we're done with all questions!
				$question_rules = Config::Get('QUESTION_DISPLAY_RULES');
				$question_amt = Config::Get('QUESTION_DISPLAY_AMT');
				
				// Start the form
				$f = new CodonForm('Survey','/Survey','post');
				$f->StartForm();
				
				// MAIN LOOP: Go through the rules and show the questions according to the rules.
				$count = 0;
				foreach ($question_rules as $rule_pattern) {
					
					
					// ---------- If we've already completed this rule on a previous page, skip it.
					$rules_completed = SessionManager::GetData('question_rules_completed');
					
					// Some checking to make sure $rules_completed is really an array
					if (count($rules_completed) == 0) { $rules_completed = array(); }
					if (count($rules_completed) == 1) { $rules_completed = array(0 => $rules_completed); }
					
					// If we've completed the rule already, skip it
					if (in_array($rule_pattern, $rules_completed)) {
						continue;
					}					
					
					// ----------- If we've shown 10 questions or more, skip showing the rest, 
					// so that the submit button is shown and the user goes on to the next page
					if ($count >= $question_amt) {
						break;								
					}

					// ----------- Get questions to show according to pattern and show them
					$questions = QuestionData::GetRandomUnansweredQuestion($rule_pattern);
					
					if (count($questions) == 1) {
						// We're showing a single question.
						$count++;
						QuestionData::OutputQuestionHTML($f, $questions);
						
						// If the question is an INSTRUCTIONTEXT, add that
						// to question_rules_completed immediately as we've displayed it
						// and therefore it is completed.
						if ($question->question_type == 'INSTRUCTIONTEXT') {
							// update session as to which rules we have completed
							$rules_completed = array();
							$rules_completed = SessionManager::GetData('question_rules_completed');
							$rules_completed[] = $rule_pattern;
							SessionManager::AddData('question_rules_completed', $rules_completed);
						}
						
						// If the question is an alter select question or an instructiontext,
						// break out since we only want to display one per page.
						if ($questions->question_type == 'ALTERSELECT') {
							
							// update session as to which rules we have completed
							$rules_completed = array();
							$rules_completed = SessionManager::GetData('question_rules_completed');
							$rules_completed[] = $rule_pattern;
							SessionManager::AddData('question_rules_completed', $rules_completed);
							
							break; // break out of foreach(question rules as rule pattern)
						}
													
					} else {
						
						// We're showing multiple questions
						if (count($questions) == 0) { 
								// BUG? -- If there were questions supposed to be here,
								// likely we got here because we broke out of master loop
								// on 10-item limit at the same boundary as completing all
								// questions in rule 
								
								//echo "Error: No questions found for rule!"; 
						} else {
							
							foreach ($questions as $question) {
								$count++;
								QuestionData::OutputQuestionHTML($f, $question);
								
								// If the question is an alter select question,
								// break out since we only want to display one per page.
								if ($question->question_type == 'ALTERSELECT') {
									break 2;
								}
								
								// If we've shown 10 questions or more, skip showing the rest, 
								// so that the submit button is shown and the user goes on to the next page
								if ($count >= $question_amt) {
									break 2; // break out of this foreach loop and the bigger foreach loop					
								}
								
							}
						}
					}
					
					// ------------ update session as to which rules we have completed
					$rules_completed = array();
					$rules_completed = SessionManager::GetData('question_rules_completed');
					if (count($rules_completed) == 0) { $rules_completed = array(); }
					// Don't add it in if it's already there. (sanity check)
					if (!in_array($rule_pattern, $rules_completed)) { $rules_completed[] = $rule_pattern; }
					SessionManager::AddData('question_rules_completed', $rules_completed);
										
				}
				
				echo "<p>";
				$f->Submit('Submit','Submit Answers &raquo;');
				echo "</p>";
				
				// Show the survey
				$f->ShowForm();
				
				break;				
			
		} // end switch
	}

		
	function processNetworkQuestions() {
		$uid = SessionManager::GetData('localid');
		$external_alters = AlterBucketData::GetAlters($uid, true);
		if (count($external_alters) == 0) { $external_alters = array(); }
		$processed_external_alters = SessionManager::GetData('processed_ext_alters');


		// Track that they have processed the previous question that they just submitted
		if (isset($this->post->Submit_extalter)) {
			
			$processed_external_alters .= $this->post->alter_id . ',';
			SessionManager::AddData('processed_ext_alters',$processed_external_alters);
			
		}
		
		$processed_external_alters = explode(',',$processed_external_alters);
		if (count($processed_external_alters) == 0) { $processed_external_alters = array(); }
		
		// Check if they're done
		// we do count($external_alters) + 1 because processed_external_alters has one more than what the real number is, due to extra comma at end.
		if (count($processed_external_alters) == count($external_alters) + 1) {
			return true;
		}
		
		// Process next alter
		foreach ($external_alters as $a) {
			if (!in_array($a->id, $processed_external_alters)) {
				
				// show form
				$this->showNetworkAlterQuestionForm($a->id, $a->name);
				
				return false; // end execution because we only want one alter per page
			}
		}
		 
	}
	
	function showNetworkAlterQuestionForm($id, $name) {
		// Start the form
		$f = new CodonForm('Survey','/Survey','post');
		$f->StartForm();
		
		$question = new stdClass; 
		$question->question_type = "NETWORKALTERSELECT";
		$question->question_text = "<h2>Which of these people does <strong>$name</strong> know?</h2>";
		$question->question_id = $id;
		
		QuestionData::OutputQuestionHTML($f, $question);
		
		$f->Hidden('alter_id',$id);

		echo "<p>";
		$f->Submit('Submit_extalter','Submit Answers &raquo;');
		echo "</p>";
		
		// Show the survey
		$f->ShowForm();
				
	}
}
?>
