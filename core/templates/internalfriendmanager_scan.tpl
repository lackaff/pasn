<div id="leftcontent">
<?php
	if (count($newfriends) == 0) {
		echo '<p>You don\'t have any new Facebook Friends to import!</p>';
	} else {
		echo "<h1>Part 1: Identify Important Facebook Friends</h1>";
		echo "<p>From time to time, most people discuss important personal matters with other people, for instance if they have problems at work, at school, with their romantic partner, parents, or other similar situations. To select these people from your Facebook network, just click and drag their picture onto one of the boxes along the right side of the screen. If someone fits into more than one category, choose the most relevant one. If you have trouble thinking of a category for someone, you can put them in the \"Other Contacts\" group.</p>";
		echo "<p>Drag and drop your important contacts from this list to the appropriate category on the right. You don't need to sort all of these contacts, only those you discuss important matters with.</p>";
		echo "<ul id=\"new_friend_list\">";

		foreach ($newfriends as $newfriend) {
			
			echo '<li class="new_friend_entry" alter_id="'.$newfriend->id.'">';
			if ($newfriend->pic != '') {
				echo '<img src="'.$newfriend->pic.'" alt="Photo of '.$newfriend->name.'" width="50px" height="50px" /><br />';
			}
			else echo '<img src="http://HARDCODED/lib/images/Face-smile.png" alt="No photo" /><br />';
			echo $newfriend->name.'</li>';
		}
	}
?>

</div>
