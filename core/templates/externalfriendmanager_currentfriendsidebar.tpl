<div id="sidebar">
	<h3>Your External Friends:</h3>
	<ul>
 	<?php
 	if (count($currentExtFriends) == 0) {
		 	
	} else {

 		foreach ($currentExtFriends as $friend) {
 			echo "<li>$friend->name</li>";
	 	}

	}
	?>
	</ul>
</div>