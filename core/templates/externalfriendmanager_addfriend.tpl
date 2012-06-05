<h1>Manage Your External Friends</h1>
<h3>Here you'll be able to view and add 'external' friends: friends who you wish to use to answer survey questions but are not on Facebook.</h3>
<hr />

<div id="leftcontent">
	<h3>Add an External Friend</h3>
	<?php
	
		$f = new CodonForm();
		$f->StartForm(array(
				'name' => 'addExternalFriend',
				'url' => 'AJAXFunctions/addExternalFriend',
				'ajax' => true,
				'method' => 'post',
				'updatediv' => '#message'
		));
		$f->Textbox('External Friend\'s Name: ','name');
		$f->Submit('Submit_externalFriend','Add Friend! &raquo;','','id="extFriendSubmit"');
		$f->ShowForm();

	?>	 
</div>