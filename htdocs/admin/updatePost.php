<html>
<head>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
	<?php
	
		$baseXml = '../posts.xml';
		$doc = new DOMDocument();
		$doc->Load($baseXml);

		$formatsNames = array('full','half','quarter','thumbnail');
		$tabExt = array('jpg','gif','png','jpeg');
		
		echo $_POST['postId'] . "<br/>";
		echo $_POST['folderName'] . "<br/>";
		echo $_POST['title'] . "<br/>";
		echo $_POST['description1'] . "<br/>";
		echo $_POST['description2'] . "<br/>";
		echo $_POST['description3'] . "<br/>";
		if (isset($_POST['full'])) echo $_POST['full'] . "<br/>";
		if (isset($_POST['half'])) echo $_POST['half'] . "<br/>";
		if (isset($_POST['quarter'])) echo $_POST['quarter'] . "<br/>";
		if (isset($_POST['thumbnail'])) echo $_POST['thumbnail'] . "<br/>";
		
		$posts = $doc->getElementsByTagName('posts')->item(0)->getElementsByTagName('post');
		$thisPost = null;
		foreach ($posts as $post) {
			if ($_POST['postId']==$post->getAttribute('id')) {
				$thisPost = $post;
			}
		}
		
		if ($thisPost!=null) {// if the post exists
			
			foreach ($thisPost->getElementsByTagName("element") as $element) {
				$thisName = $element->getAttribute("name");
				if (!in_array($thisName,$formatsNames)) {
					$element->setAttribute("value",$_POST[$thisName]);
				}
			}
			
			for ($i=0;$i<4;$i++) { // update images files
				echo $formatsNames[$i] . " : ";
				if(!empty($_POST)) {
					if( !empty($_FILES[$formatsNames[$i]]['name']) ) {
						$extension  = pathinfo($_FILES[$formatsNames[$i]]['name'], PATHINFO_EXTENSION);
						if(in_array(strtolower($extension),$tabExt)) {
							$infosImg = getimagesize($_FILES[$formatsNames[$i]]['tmp_name']);
							if($infosImg[2] >= 1 && $infosImg[2] <= 14) {
								if(($infosImg[0] > 0) && ($infosImg[1] > 0) && (filesize($_FILES[$formatsNames[$i]]['tmp_name']) > 0)) {
									if(isset($_FILES[$formatsNames[$i]]['error']) && UPLOAD_ERR_OK === $_FILES[$formatsNames[$i]]['error']) {
										$newName = uniqid() .'.'. $extension;
										if (move_uploaded_file($_FILES[$formatsNames[$i]]['tmp_name'], '../' . $_POST['folderName'].'/'.$newName)) {
											foreach ($thisPost->getElementsByTagName("element") as $element) {
													if ($element->getAttribute("name")==$formatsNames[$i]) {
														$element->setAttribute("value",$newName);
													}
												}
											echo 'upload done';
										} else echo 'Upload failed';
									} else echo 'file has an error';
								} else echo 'bad size';
							} else echo 'not a valid image';
						} else echo 'bad extension';
					} else echo 'no image uploaded';
				} else echo 'empty post';
				echo "<br/>";
			}
			
			// fill blank images
			$foundUrl="";
			foreach ($thisPost->getElementsByTagName("element") as $element) {
				$thisName = $element->getAttribute("name");
				if ($thisName=="thumbnail"&&$element->hasAttribute("value")) $foundUrl = $element->getAttribute("value");
				if ($thisName=="quarter"&&$element->hasAttribute("value")) $foundUrl = $element->getAttribute("value");
				if ($thisName=="half"&&$element->hasAttribute("value")) $foundUrl = $element->getAttribute("value");
				if ($thisName=="full"&&$element->hasAttribute("value")) $foundUrl = $element->getAttribute("value");
			}
			if ($foundUrl!="") {
				foreach ($thisPost->getElementsByTagName("element") as $element) {
					if ($element->getAttribute("value")=="") {
						$thisName = $element->getAttribute("name");
						if ($thisName=="thumbnail") $element->setAttribute("value",$foundUrl);
						if ($thisName=="quarter") $element->setAttribute("value",$foundUrl);
						if ($thisName=="half") $element->setAttribute("value",$foundUrl);
						if ($thisName=="full") $element->setAttribute("value",$foundUrl);
					}
				}
			}

			$doc->formatOutput = true;		
			$doc->saveXML();
			$doc->save($baseXml);
		
		} else {
			echo "this post can't be found<br/>";
		}

		echo '<a href="./">back to admin</a>';
		
	?>
</body>
</html>
