<h1>Part 2: Who else is imporant in your network?</h1>
<p>From time to time, most people discuss important personal matters with other people, for instance if they have problems at work, at school, with their romantic partner, parents, or other similar situations.</p>
<p>Now you can add those <strong>important people in your life who aren't Facebook Friends</strong>. Add the names of these important people one at a time -- be sure to press the "Add..." button (or press "Enter") after each individual name.</p>
<?php

		echo "<h3>With which family members do you discuss personal matters? (Question 1 of 6)</h3>";
		
		$f = new CodonForm();
		$f->StartForm(array(
				'name' => 'addExternalFriendTo' . 'family',
				'url' => 'AJAXFunctions/addExternalFriendToBucket',
				'ajax' => true,
				'method' => 'post',
				'updatediv' => '#message'
		));
		$f->Hidden('bucket_name','family');
		$f->Textbox("Name: ",'name','','class="extaltername"');
		$f->Submit('Submit_externalFriend','Add this person! &raquo;',"this.form.submit(); this.form.name.value=''",'class="nameGeneratorSubmit"');
		$f->ShowForm();
?>
<div class="message" id="message"></div>
<hr />
<p>When you have added everyone you can remember, please <a href="<?=LINK_BASE?>/ExternalFriendManager/nameGenerator2" class="next">continue</a></p>
