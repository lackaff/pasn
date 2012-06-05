<h3>Survey Submission Information</h3>

<p>Thank you very much for your participation in the Social Networks and Social Support Project this semester. Your efforts have greatly contributed to our knowledge of the college experience.</p>

<p>Please print this page and submit it to your instructor or TA, and they will sign your research card.</p>

<p>Participation Summary: </p>
<p>Name: <?=$name?> </p>
<p>Surveys completed: <?php echo count($submit_info); ?></p>
<table width="500">
	<tr><th>Submission date</th><th>Survey ID</th></tr>
	<?php 
	$x = 0;
	foreach ($submit_info as $i) {
		// limit to 4...
		$x++; if ($x >= 5) { break; }
		echo "<tr><td>$i->submit_timestamp</td><td>$i->id</td></tr>";
	}
	?>
</table>