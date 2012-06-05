<div id="sidebar">
<h3>Important Contacts Categories:</h3>
	<ul id="bucket_list">
	<?php echo "<li class=\"bucket_label\" bucket_id=\"".$buckets[0]->id."\">Family Members</li>";
		echo "<li class=\"bucket_label\" bucket_id=\"".$buckets[1]->id."\">High School Contacts</li>";
		echo "<li class=\"bucket_label\" bucket_id=\"".$buckets[2]->id."\">College Contacts</li>";
		echo "<li class=\"bucket_label\" bucket_id=\"".$buckets[3]->id."\">Work Contacts</li>";
		echo "<li class=\"bucket_label\" bucket_id=\"".$buckets[4]->id."\">Club / Activity Contacts</li>";
		echo "<li class=\"bucket_label\" bucket_id=\"".$buckets[5]->id."\">Other Contacts</li>"; 
	?>
	</ul>

<p>Once you've added all of your importnat Facebook Friends to the appropriate categories, <br /> 
<a href="<?=LINK_BASE?>/ExternalFriendManager/nameGenerator1" class="next">click here to continue</a> to the next part of the interview.</p> 

<div class="message" id="message"></div>
</div>

