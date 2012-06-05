<h3>Edgefinder</h3>

<p>Processing the links among your facebook friends.</p>
<p>Edgefinder summary: </p>
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
