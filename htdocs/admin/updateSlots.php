<html>
<head>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
	<?php
	
		$baseXml = '../posts.xml';
		$doc = new DOMDocument();
		$doc->Load($baseXml);
		
		$weeks = $doc->getElementsByTagName('slots')->item(0)->getElementsByTagName('week');
		foreach ($weeks as $week) {
			foreach ($week->getElementsByTagName("format") as $format) {
				foreach ($format->getElementsByTagName("slot") as $slot) {
					$slot->setAttribute("targetid",$_POST['format'.$format->getAttribute("type").'slot'.$slot->getAttribute("number")]);
					// then clean by checking if the id still exists
					$found=false;
					foreach ($doc->getElementsByTagName('post') as $post) {
						if ($post->getAttribute("id") == $slot->getAttribute("targetid")) $found=true;
					}
					if (!$found) {
						$slot->setAttribute("targetid",-1);
					}
				}
			}
		}

		$doc->formatOutput = true;
		$doc->saveXML();
		$doc->save($baseXml);
		
		echo '<a href="./">back to admin</a>';
		
	?>
</body>
</html>
