<p>From time to time, most people discuss important personal matters with other people, for instance if they have problems at work, at school, with their romantic partner, parents, or other similar situations.</p>
<p><strong>Now you can add those important people in your life who aren't Facebook Friends. Add the names of these important people one at a time -- be sure to press the "Add..." button (or press "Enter") after each individual name.</strong></p>
<?php

		echo "<h3>With which high school friends do you discuss personal matters? (Question 2 of 6)</h3>";
		
		$f = new CodonForm();
		$f->StartForm(array(
				'name' => 'addExternalFriendTo' . 'high_school',
				'url' => 'AJAXFunctions/addExternalFriendToBucket',
				'ajax' => true,
				'method' => 'post',
				'updatediv' => '#message'
		));
		$f->Hidden('bucket_name','high_school');
		$f->Textbox("Name: ",'name','','class="extaltername"');
		$f->Submit('Submit_externalFriend','Add this person! &raquo;','','class="nameGeneratorSubmit"');
		$f->ShowForm();
?>
<div class="message" id="message"></div>
<hr />
<p>When you have added everyone you can remember, please <a href="<?=LINK_BASE?>/ExternalFriendManager/nameGenerator3" class="next">continue</a></p>
