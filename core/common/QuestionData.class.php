<?php
/*
 * for Importing Questions
 * ^"([a-zA-Z_0-9]*)","([a-zA-Z_0-9]*)","([^,]*)","([^,]*)","([a-zA-Z_0-9]*)","([^,]*)"$
 * ^"([a-zA-Z_0-9]*)","([a-zA-Z_0-9]*)","([a-zA-Z_0-9!?,.]*)","([a-zA-Z_0-9!?,.]*)","([a-zA-Z_0-9]*)","([^,]*)"$
 * ^"([a-zA-Z_0-9]*)","([a-zA-Z_0-9]*)","([a-zA-Z_0-9!?,.]*)","([a-zA-Z_0-9!?,.=\>\|"]*)","([a-zA-Z_0-9]*)","([^,]*)"$
 * ^"([a-zA-Z_0-9]*)","([a-zA-Z_0-9]*)","([.]*)","([.]*)","([a-zA-Z_0-9]*)","([.]*)"$
INSERT INTO pitious_pasn.survey_questions (question_id, question_type,question_text, multichoice_choice_list, openended_textbox_type, comment)
VALUES(
'$RegEx1.1',
'$RegEx1.2',
'$RegEx1.3',
'$RegEx1.4',
'$RegEx1.5',
'$RegEx1.6',
);
 */

/**
 * Class QuestionData
 * 
 * Contains all the data accessor and manipulation methods for survey questions.
 *
 */
class QuestionData {
	static $MULTICHOICE = 'MULTICHOICE';
	static $OPENENDED = 'OPENENDED';
	static $ALTERSELECT = 'ALTERSELECT';
	static $NETWORKALTERSELECT = "NETWORKALTERSELECT";
	static $INSTRUCTIONTEXT = 'INSTRUCTIONTEXT';
	static $TEXTAREA = 'TEXTAREA';
	static $TEXTBOX = 'TEXTBOX';
	
	/**
	 * Returns a random unanswered or specific question given a question_id pattern.
	 *
	 * @param string $pattern the pattern to match against question_id. can contain * wildcards
	 * @return object row from the db of the question
	 */
	public static function GetRandomUnansweredQuestion($pattern) {
		$pattern = DB::escape($pattern);

		if (strpos($pattern, '*') === false) {
			// They're looking for a specific question_id. Return it.
			return self::GetQuestion($pattern);
		
		} else {
			// There's a wildcard. Search for unanswered questions satisfyign pattern
			
			$pattern = str_replace('*','%',$pattern);
			$pattern = str_replace('_','\_',$pattern);
			// This is used for not showing any instructional text. See below
			$pattern_instr = str_replace('%','INSTR',$pattern);
			
			// Check the session for questions that have been answered. 
			$answered_questions = SessionManager::GetData('answered_questions');
			// Double check for empty array
			
			if (count($answered_questions) == 0 || $answered_questions == '') {
				$answered_questions = array(); 
			} else { 
				$answered_questions = array_keys($answered_questions);
			}
			
			// Build a string based off of this for part of our query
			$answered_questions_exploded = '';
			if (count($answered_questions) == 0 || $answered_questions == '') {
				$answered_questions_exploded = "''";
			}
			$has_trailing_comma = false;
			foreach ($answered_questions as $q) {
				$answered_questions_exploded .= "'$q',";
				$has_trailing_comma = true;
			}
			if ($has_trailing_comma) {
				$answered_questions_exploded = substr($answered_questions_exploded, 0, -1);
			}
			
			
			// Build our query, query it, and return our question(s)
			$query = "SELECT * FROM survey_questions WHERE
  						question_id LIKE '$pattern'
  						AND question_id NOT IN ($answered_questions_exploded)
  						AND question_id NOT LIKE '$pattern_instr'
  						ORDER BY RAND()";
			$question = DB::get_results($query);
			
			return $question;			
		}
		
	}
	
	public static function GetQuestion($qid) {
		$qid = DB::escape($qid);
		$query = "SELECT * FROM survey_questions WHERE question_id = '$qid'";
		$ret = DB::get_row($query);
		
		return $ret;		
	}
	
	
	public static function OutputQuestionHTML($form, $question) {
				
		switch ($question->question_type) {
			
			case self::$MULTICHOICE:
				echo "<p><strong>$question->question_text</strong></p>";
				
				$answer_map = explode('|', $question->multichoice_choice_list);
				foreach ($answer_map as $answer) {
					$answer_pair = explode('=>', $answer);
					$answer_key = trim($answer_pair[0], ' \'"');
					$answer_value = trim($answer_pair[1], ' \'"');
					$answer_id = $question->question_id . '_' . $answer_key;
					
					$form->Radio($answer_value, $answer_id, $question->question_id, $answer_key, ''); 					
				}
				
				break;
				
			case self::$OPENENDED:
				
				echo "<p><strong>$question->question_text</strong></p>";
				
				if ($question->openended_textbox_type == self::$TEXTBOX) {
					$form->Textbox('&nbsp;', $question->question_id);
				} else if ($question->openended_textbox_type == self::$TEXTAREA) {
					$form->Textarea('&nbsp;', $question->question_id);					
				}
				
				break;
				
			case self::$ALTERSELECT:
				
				echo "<p><strong>$question->question_text</strong></p>";
				echo "<div class=\"alterselectsubmit\" id=\"$question->question_id\"><strong>Answer Box:</strong> Drag your chosen friends here. <br /><strong>Chosen friends so far: </strong></div>";
				
				// Show div with buckets and alters!
				echo "<p>Click below to choose from your friends!</p>";
				echo "<ul class=\"ui-accordion-container alterBucketBox\">";
				
					$buckets = AlterBucketData::GetUsersBuckets(SessionManager::GetData('localid'));
					if (count($buckets) == 0 || $buckets == '') { $buckets = array(); echo "<li>You have no buckets set!</li>"; }
					
					// Echo out a bucket with the "Nobody" user
					echo "<li><a class=\"alterBucket\" href=\"#\">Nobody</a>";
					echo "<div><p class=\"alter\" name=\"Nobody\" id=\"-1\">Nobody</div></p>";
					echo "</li>";
					
					foreach ($buckets as $bucket) {
						// Get friends in this bucket
						$friends = AlterBucketData::GetAltersInBucket($bucket->id);
						$cnt = count($friends);
						
						// Echo out bucket
						echo "<li><a class=\"alterBucket\" href=\"#\">Bucket: $bucket->name ($cnt friends)</a>";
						echo "<div>";
						if (count($friends) == 0 || $friends == '') {
							echo "<div style=\"clear: both;\"><strong>There are no friends/alters in this bucket.</strong></div>";
						} else {
							foreach ($friends as $friend) {
								$img_html = '';
								
								if ($friend->img_url != '' && $friend->img_url != 'NULL') {
									$img_html = "<img src=\"$friend->img_url\" alt=\"friend pic\" /><br />";
								} else {
									$img_html = '<img src="'.SITE_URL.'/lib/images/Face-smile.png" alt="Camera Shy" /><br />';
								}
								
								echo "<p class=\"alter\" name=\"$friend->name\" id=\"$friend->id\">$img_html $friend->name</p>";
							}
						} 
												
						echo "</div>";
						echo "</li>"; // end alterBucket li
					}
					
					// TODO Show friends who aren't in any buckets
					//echo "<div class=\"alterBucket\"><p></p>";
					//echo "</div>";
				
				echo "</ul>"; // end ul .alterBucketBox
				
				break;
				
			case self::$NETWORKALTERSELECT:
				
				//echo "<p class=\"networkQuestion\">$question->question_text</p>";
				echo "<div class=\"networkalterselectsubmit\" id=\"$question->question_id\">$question->question_text<br />Drag your selections here</em><br /><strong>Chosen so far: </strong></div>";
				
				// Show div with buckets and alters!
				echo "<p>Click on a list to open it, and drag the correct icons to the box above. If the answer is \"nobody\", then drag that icon to the box above. You don't need to add the person in the question -- we assume he/she knows himself/herself! Click the \"Submit Answer\" button when you are finished.</p>";
				echo "<ul class=\"ui-accordion-container alterBucketBox\">";
				
					$buckets = AlterBucketData::GetUsersBuckets(SessionManager::GetData('localid'));
					if (count($buckets) == 0 || $buckets == '') { $buckets = array(); echo "<li>You have no buckets set!</li>"; }
					
					// Echo out a bucket with the "Nobody" user
					echo "<li><a class=\"alterBucket\" href=\"#\">Nobody</a>";
					echo '<div><p class="alter" name="Nobody" id="-1"><img src="'.SITE_URL.'/lib/images/dialog-cancel.png" alt = "Nobody" />Nobody</div></p>';
					echo "</li>";
					
					foreach ($buckets as $bucket) {
						// Get friends in this bucket
						$friends = AlterBucketData::GetAltersInBucket($bucket->id);
						$cnt = count($friends);
						
						// Echo out bucket
						echo "<li><a class=\"alterBucket\" href=\"#\">List: $bucket->name ($cnt friends)</a>";
						echo "<div class=\"divBucket\">";
						if (count($friends) == 0 || $friends == '') {
							echo "<div class=\"emptyBucket\" style=\"clear: both;\"><strong>This list is empty.</strong></div>";
						} else {
							foreach ($friends as $friend) {
								$img_html = '';
								
								if ($friend->img_url != '' && $friend->img_url != 'NULL') {
									$img_html = "<img src=\"$friend->img_url\" alt=\"friend pic\" /><br />";
								} else {
									$img_html = '<img src="'.SITE_URL.'/lib/images/Face-smile.png" alt="Camera Shy" /><br />';
								}
								echo "<p class=\"alter\" name=\"$friend->name\" id=\"$friend->alter_id\">$img_html $friend->name</p>";
							}
						} 
												
						echo "</div>";
						echo "</li>"; // end alterBucket li
					}
					
					// TODO Show friends who aren't in any buckets
					//echo "<div class=\"alterBucket\"><p></p>";
					//echo "</div>";
				
				echo "</ul>"; // end ul .alterBucketBox
				
				break;
			
			case self::$INSTRUCTIONTEXT:
				
				echo "<h3>$question->question_text</h3>";
				
				break;
		}
		
		echo "<hr />";
		
	}

	public static function StoreAnswers($answers) {
		$user_id = SessionManager::GetData('localid');
		$now = DB::get_var("SELECT NOW()");
		
		$query = "INSERT INTO survey_submissions SET user_id = $user_id, submit_timestamp = '$now'";
		DB::query($query);
		$last_id = DB::$insert_id;
		
		foreach ($answers as $question_id => $answer) {
			self::StoreAnswer($user_id, $question_id,$answer,$now);
		}
		
		return $last_id;
		
	}
	
	private static function StoreAnswer($user_id, $question_id,$answer,$now) {
		$query = "INSERT INTO survey_answers SET user_id = $user_id, submit_timestamp = '$now', question_id = '$question_id', answer = '$answer'";
		DB::query($query);
	}
	
}


?>
