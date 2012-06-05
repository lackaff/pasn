<?php

/**
 * Class Admin is the main controller module for the Admin panel.
 * Main tasks are split up into methods. 
 *
 */
class Admin extends CodonModule
{
	var $notices;
	var $SURVEY_START;
	var $WAVE_END_DATES;
	var $DELIMITER;
	var $NULL_OUTPUT;
	var $STRING_ENCLOSURE; 
	
	function __construct() {
		$this->SURVEY_START = '2008-08-01 00:00:00';
		$this->WAVE_END_DATES = array(
			1 => '2008-09-29 00:00:00',
			2 => '2008-10-26 00:00:00',
			3 => '2008-11-30 00:00:00',
			4 => '2008-12-30 00:00:00'
		);
		$this->DELIMITER = ',';
		$this->NULL_OUTPUT = '99';
		$this->STRING_ENCLOSURE = '"';
		
		set_time_limit(120); // Set time limit to two minutes -- this report might take a while.
	}
	
	function Controller()
	{		
		// Authenticate
		if (SessionManager::GetData('email') == '') {
			echo "<p>Error! Email not set in session; you need to log in through the main Dashboard.</p>";
			exit;
		} else if (in_array(SessionManager::GetData('email'), Config::Get('ADMIN_USERS'))) {
			// Continue on.
		} else {
			echo "<p>Error! Unauthorized user not in ADMIN_USERS. Your email address: ".SessionManager::GetData('email')."</p>";
			exit;
		}
		
		$this->ClearNotices();
		
		// Start decision tree
		switch ($this->get->page)
		{
			
			case 'harvestDataForSPSS':
				$this->harvestDataForSPSS();
				
				break;
				
			case '':
				
				echo "<h2>Admin Menu</h2>";
				echo "<p><a href=\"".LINK_BASE."/Admin/harvestDataForSPSS\">Export Data</a> for SPSS</p>";
				break;
				
		}
	}
	
	function harvestDataForSPSS() {		
		echo "Getting data...";
		echo "<pre>";
		
		// TODO output headers
		
		$users = DB::get_col("SELECT id FROM users");
		$question_list = DB::get_col("SELECT DISTINCT question_id FROM survey_questions");
		$num_questions = count($question_list);
		
		// output headers
		$this->outputAnswer("user_id");
		foreach ($this->WAVE_END_DATES as $wave => $end_date) {
			foreach ($question_list as $q) { $this->outputAnswer("W" . $wave . "_" . $q); }
		}
		echo "<br />";
						
		// Grab wave submissions for each user
		foreach ($users as $userid) {
			
			$this->outputAnswer($userid);
			
			foreach ($this->WAVE_END_DATES as $wave => $end_date) {
				
				// get info on the users' submission for this wave
				$submission_query = "SELECT * FROM survey_submissions 
											WHERE 
												user_id = $userid 
												AND submit_timestamp >= '$this->SURVEY_START'
												AND submit_timestamp <= '$end_date'
											ORDER BY submit_timestamp DESC
											LIMIT 1";
				$submission = DB::get_row($submission_query);
				if (count($submission) == 0 || $submission == '') {
					// user doesn't have this wave; output 99's to fill in the gap
					$this->OutputNulls($num_questions);
					$this->LogNotice("NOTICE: User $userid did not submit a survey for wave $wave. <br />"); continue; 
				}
				$timestamp = $submission->submit_timestamp;
				
				
				// foreach survey question, grab it, if it doesn't exist output 99, else output the answer
				foreach ($question_list as $question_id) {
					$answer_query = "SELECT a.answer,q.question_type FROM survey_answers a, survey_questions q
											WHERE
												a.question_id = q.question_id
												AND a.user_id = $userid
												AND a.submit_timestamp = '$timestamp'
												AND a.question_id = '$question_id'
											LIMIT 1";
					$answer = DB::get_row($answer_query);
					if (count($answer) == 0 || $answer->answer == '' || $answer->answer == 'NULL' || $answer->answer == 'null') { 
						// user doesn't have answer for this question, or it's invalid, or something; output 99.
						$this->OutputNulls(1);
						$this->LogNotice("NOTICE: User $userid did not submit answer for wave $wave, question $question_id. <br />"); 
					} else {
						if ($answer->question_type == 'ALTERSELECT') {
							$this->outputAlterSelect($answer->answer);
						} else {
							$this->outputAnswer($answer->answer);
						}
					}
				} // end foreach question_id
			 
			} // end big foreach wave
			echo "<br />";
		} // end big foreach user 
		
		echo "</pre>";
		
		echo "<p>...End of report!</p>";
		echo $this->GetNotices();
		
	}
	
	private function outputAnswer($a) {
		echo $this->STRING_ENCLOSURE . htmlentities($a) . $this->STRING_ENCLOSURE . $this->DELIMITER;
	}
	private function outputAlterSelect($a) {
		$arr = explode(',',$a);
		if (is_array($arr)) {
			$this->outputAnswer(count($arr));
		} else if ($a == '-1') {
			$this->outputAnswer(0);
		}
	}
	private function OutputNulls($cnt) {
		for ($x = 0; $x < $cnt; $x++) { $this->outputAnswer($this->NULL_OUTPUT); }
		return;
	}
	private function ClearNotices() { $this->notices = ''; }
	private function GetNotices() { return $this->notices; }
	private function LogNotice($str) {
		$this->notices .= $str;
	}
	
	
}
?>
