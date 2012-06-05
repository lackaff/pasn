<p>You will now see a series of questions designed to help you remember 
people in your personal social network who are not your Facebook Friends. When you answer these questions, 
think about people who you have personally communicated with in the last few weeks, either in person, 
on the phone, or using another method. <strong>Family members and other close relationships are most important, 
but try to add as many people as you can remember.</strong> Names should be something easy for you to remember and identify later, 
like "Mom," "Aunt Linda," "Johnny C," "the mailman," or "that cute bartender at Applebees". Try to be as thorough as possible; the more people you can add, the better.<p>
<p><strong>Add names one at a time -- be sure to press the "Add to ..." button after each individual name.</strong></p>
<?php
	
	$buckets = Config::Get('DEFAULT_BUCKETS');

	foreach ($buckets as $bucket_name) {
		echo "<p><strong>What $bucket_name do you communicate with at least twice per year?</strong></p>";
		
		$f = new CodonForm();
		$f->StartForm(array(
				'name' => 'addExternalFriendTo' . $bucket_name,
				'url' => 'AJAXFunctions/addExternalFriendToBucket',
				'ajax' => true,
				'method' => 'post',
				'updatediv' => '#message'
		));
		$f->Hidden('bucket_name',$bucket_name);
		$f->Textbox("Name: ",'name','','class="extaltername"');
		$f->Submit('Submit_externalFriend','Add this person! &raquo;','','class="nameGeneratorSubmit"');
		$f->ShowForm();
		echo "<hr />";
	}

?>	 
<p>When you have added everyone you can remember, move on and <a href="<?=LINK_BASE?>/Survey" class="next">Start the Survey!</a></p>
