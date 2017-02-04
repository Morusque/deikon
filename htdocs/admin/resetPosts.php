<html>
<head>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
	<?php
	
		copy("posts.xml", "postsBackup.xml");
		copy("postsTemplate.xml", "posts.xml");
		echo '<a href="./">back to admin</a>';
		
	?>
</body>
</html>
