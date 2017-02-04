<html>
<head>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
	<?php
	
		function remove_children(&$node) {
			while ($node->firstChild) {
				while ($node->firstChild->firstChild) {
					remove_children($node->firstChild);
				}
				$node->removeChild($node->firstChild);
			}
		}

		function rrmdir($dir) {// recursively remove an entire folder and all its children
			if (is_dir($dir)) { 
				$objects = scandir($dir); 
				foreach ($objects as $object) { 
					if ($object != "." && $object != "..") { 
					if (is_dir($dir."/".$object))
						rrmdir($dir."/".$object);
					else
						unlink($dir."/".$object); 
					}
				}
			rmdir($dir); 
			}
		}
		
		function getPostValue($post, $attr_name) {
			foreach($post->getElementsByTagName("element") as $node) if ($node->getAttribute("name")==$attr_name) return $node->getAttribute("value");
			return "";
		}		
		
		$baseXml = '../posts.xml';
		$doc = new DOMDocument();
		$doc->Load($baseXml);

		$formatsNames = array('full','half','quarter','thumbnail');
		$tabExt = array('jpg','gif','png','jpeg');
		
		if (isset($_POST['postId'])) echo "postId = " . $_POST['postId'] . "<br/>";
		else echo "no post Id defined<br/>";
		
		if (!isset($_POST['postId'])) {//add post
			$nextId=0;
			$found=true;
			$posts = $doc->getElementsByTagName('post');
			while ($found) {
				$found=false;
				foreach ($posts as $post) {
					if ($post->getAttribute('id')==$nextId) {
						$nextId++;
						$found=true;
					}
				}
			}			
	
			$newPost = $doc->createElement('post');
			$newPost->setAttribute("id",$nextId);
			$folderName = "./posts/post".uniqid();
			$newPostFolderName = $doc->createElement('element');
			$newPostFolderName->setAttribute("name","folderName");
			$newPostFolderName->setAttribute("value",$folderName);
			$newPost->appendChild($newPostFolderName);
			
			$child =  $doc->createElement('element');
			$child->setAttribute("name","title");
			$newPost->appendChild($child);
			$child =  $doc->createElement('element');
			$child->setAttribute("name","description1");
			$newPost->appendChild($child);
			$child =  $doc->createElement('element');
			$child->setAttribute("name","description2");
			$newPost->appendChild($child);
			$child =  $doc->createElement('element');
			$child->setAttribute("name","description3");
			$newPost->appendChild($child);
			$child =  $doc->createElement('element');
			$child->setAttribute("name","full");
			$newPost->appendChild($child);
			$child =  $doc->createElement('element');
			$child->setAttribute("name","half");
			$newPost->appendChild($child);
			$child =  $doc->createElement('element');
			$child->setAttribute("name","quarter");
			$newPost->appendChild($child);
			$child =  $doc->createElement('element');
			$child->setAttribute("name","thumbnail");
			$newPost->appendChild($child);
			
			$doc->documentElement->appendChild($newPost);
		
			mkdir ('../'.$folderName);
			
		} else {// remove post with given id
			
			$posts = $doc->getElementsByTagName('post');
			foreach ($posts as $post) {
				if ($post->getAttribute('id')==$_POST['postId']) {
					rrmdir("../".getPostValue($post,"folderName"));
					remove_children($post);
					$doc->documentElement->removeChild($post);
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
