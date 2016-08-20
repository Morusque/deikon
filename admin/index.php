<html>
<head>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
	<?php
	
		$baseXml = 'posts.xml';
		$doc = new DOMDocument();
		$doc->Load($baseXml);
		
		$posts = $doc->getElementsByTagName('post');
		$weeks = $doc->getElementsByTagName('slots')->item(0)->getElementsByTagName('week');
		
		echo 'POSTS : <br/>';
		echo '<select class="postSelector">';
		foreach ($posts as $post) echo '<option idValue="'.$post->getAttribute('id').'">'.$post->getAttribute('id').'</option>';
		echo '</select><br/>';
		
		function getPostValue($post, $attr_name) {
			foreach($post->getElementsByTagName("element") as $node) if ($node->getAttribute("name")==$attr_name) return $node->getAttribute("value");
			return "";
		}
		
		foreach ($posts as $post) {
			echo '<div class="elementList" id="elementList'.$post->getAttribute('id').'" style="display:none;">';
			echo '<form action="updatePost.php" method="post" enctype="multipart/form-data">';
			echo '<input type="hidden" name="postId" value="' . $post->getAttribute('id') . '"/>';
			foreach ($post->getElementsByTagName('element') as $element) {
				$eN = $element->getAttribute("name");
				if ($eN=="full") {
					echo '<img src="../'.getPostValue($post,"folderName").'/'.$element->getAttribute("value").'" style="width:112px;height:169px;"></img>('.$eN.')<input type="file" accept="image/*" name="'.$eN.'"/>';//1126x1692
				} else if ($eN=="half") {
					echo '<img src="../'.getPostValue($post,"folderName").'/'.$element->getAttribute("value").'" style="width:112px;height:83px;"></img>('.$eN.')<input type="file" accept="image/*" name="'.$eN.'"/>';//1126x838
				} else if ($eN=="quarter") {
					echo '<img src="../'.getPostValue($post,"folderName").'/'.$element->getAttribute("value").'" style="width:54px;height:83px;"></img>('.$eN.')<input type="file" accept="image/*" name="'.$eN.'"/>';//545x838
				} else if ($eN=="thumbnail") {
					echo '<img src="../'.getPostValue($post,"folderName").'/'.$element->getAttribute("value").'" style="width:125px;height:190px;"></img>('.$eN.')<input type="file" accept="image/*" name="'.$eN.'"/>';//125x190
				} else {
					echo $eN . ' : <input name="' . $eN . '" value="' . $element->getAttribute("value") . '"/>';
				}
				echo '<br/>';
			}
			echo '<input type="submit" name="update"/>';
			echo '</form>';
			echo '</div>';
		}
		
		echo "<br/>";

		$formatNames = ["thumbnail","half","quarter","full"];

		echo '<form action="addRemovePost.php" method="post" ><input type="hidden" name="postId" value="' . $post->getAttribute('id') . '"/><input type="submit" value="remove this post"></input></form>';
		echo '<form action="addRemovePost.php" method="post" ><input type="submit" value="add a new post"></input></form>';
		
		echo '<br/>----------------------------------------<br/><br/>';
		
		echo '<form action="updateSlots.php" method="post" >';
		foreach ($weeks as $week) {
			echo 'WEEK : '.$week->getAttribute("number").'<br/>';
			foreach ($week->getElementsByTagName("format") as $format) {
				echo '- FORMAT '.$format->getAttribute("type").' ('.$formatNames[$format->getAttribute("type")].') '.' <br/> SLOTS : ';
				foreach ($format->getElementsByTagName("slot") as $slot) {
					echo '<select name="format'.$format->getAttribute("type").'slot'.$slot->getAttribute("number").'" autocomplete="off">';
					$posts = $doc->getElementsByTagName('post');
					echo '<option value="-1" ';
					if ($slot->getAttribute("targetid")==-1) echo 'selected="true"';
					echo '>-1</option>';
					foreach ($posts as $post) {
						echo '<option value="'.$post->getAttribute('id').'"';
						if ($post->getAttribute('id')==$slot->getAttribute("targetid")) echo 'selected="true"';
						echo '>'.$post->getAttribute('id').'</option>';
					}
					echo '</select>';
				}
				echo '<br/><br/>';
			}
			echo '<br/>';
		}
		echo '<input type="submit" name="update"/>';
		echo '</form>';
		
	?>
	<script type="text/javascript">
		$(".postSelector").change(function () {
			var listName = "elementList" + $(".postSelector option:selected").attr("idValue");
			$(".elementList").each(function() {$(this).hide();});
			$("#"+listName).show();
		});
	</script>
</body>
</html>
