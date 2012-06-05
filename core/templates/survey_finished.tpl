<h1>Nice Job! Interview Complete!</h1>

<h3>If you are completing this project for class credit, please print this page and submit it to your instructor.</h3>

<p>Thank you very much for your participation in the Social Networks and Social Support Project this semester. Your efforts have greatly contributed to our knowledge of the college experience.</p>

<p>Please print this page and submit it to your instructor or TA, and they will sign your research card.</p>

<h1>Participation Summary: </h1>
<table width="500">
	<tr><th>Name</th><th>Submission date</th><th>Survey ID</th></tr>
	<?php 
	$x = 0;
	foreach ($submit_info as $i) {
		// limit to 4...
		$x++; if ($x >= 5) { break; }
		echo "<tr><td>$name</td><td>$i->submit_timestamp</td><td>$i->id</td></tr>";
	}
	?>
</table>
