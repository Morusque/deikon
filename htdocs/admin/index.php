<html>
<head>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
	<?php
		/*
			identification HTTP Digest
			début du code
			source : https://secure.php.net/manual/fr/features.http-auth.php
		*/
		$realm = 'Restricted Area';

		//utilisateur => mot de passe
		$users = array('morusque' => 'zozo1234', 'd2air' => 'zozo1234', 'lepole' => 'zozo1234');

		if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

			die('Annulation de l\'authentification<br/><a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/">Page principale</a><br/>');
		}

		// analyse la variable PHP_AUTH_DIGEST
		if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
			!isset($users[$data['username']]))
			die('Mauvaise Pièce d\'identité!<br/><a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/">Page principale</a><br/>');

		// Génération de réponse valide
		$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
		$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

		if ($data['response'] != $valid_response)
			ie('Mauvaise Pièce d\'identité!<br/><a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/">Page principale</a><br/>');

		// ok, utilisateur & mot de passe valide
		$salutatoi = 'Vous êtes identifié en tant que : ' . $data['username'] . '<br/><br/>';

		// fonction pour analyser l'en-tête http auth
		function http_digest_parse($txt)
			{
			// protection contre les données manquantes
			$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
			$data = array();
			$keys = implode('|', array_keys($needed_parts));

			preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

			foreach ($matches as $m) {
				$data[$m[1]] = $m[3] ? $m[3] : $m[4];
				unset($needed_parts[$m[1]]);
			}

			return $needed_parts ? false : $data;
		}
		/*
			fin du code
			identification HTTP Digest
		*/

		$baseXml = '../posts.xml';
		$doc = new DOMDocument();
		$doc->Load($baseXml);

		$posts = $doc->getElementsByTagName('post');
		$weeks = $doc->getElementsByTagName('slots')->item(0)->getElementsByTagName('week');

		echo $salutatoi;
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

		$formatNames = ["thumbnail","full","quarter","half"];

		echo '<form action="addRemovePost.php" method="post" ><input type="hidden" id="postToRemove" name="postId" value="' . $post->getAttribute('id') . '"/><input type="submit" value="remove this post"></input></form>';
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
		
		var currentPost = "0";
		
		$(".postSelector").change(function () {
			currentPost = $(".postSelector option:selected").attr("idValue");
			$("#postToRemove").attr("value",currentPost);
			var listName = "elementList" + currentPost;
			$(".elementList").each(function() {$(this).hide();});
			$("#"+listName).show();
		});
	</script>
</body>
</html>
