<html>
<head>
	<style>
	.thumbnail {
		width:125px;
		height:190px;
		margin:4px;
		padding:0px;
	}
	.ListLineA {
		font-size:30px;
		height:200px;
		width:1080px;
		background-color:#f7f;
		margin:4px;
	}
	.ListLineB {
		font-size:30px;
		height:200px;
		width:1080px;
		background-color:#ef5;
		margin:4px;
	}
	body {
		background-color:#333;
		margin:0px;
		padding:0px;
	}
	.logoLine {
		height:120px;
		background-color:#333;
		width:1080px;
		margin:0px;
	}
	#big {
		width:1080px;
		height:1920px;
		margin:0px;
	}
	.bigFrame{
	  position: absolute;
	  height:100%;
	}
	.bigPost{
	  position: absolute;
	  z-index: -1;
	  height:100%;
	}
	.halfSection {
		width:1080px;
		height:960px;
		margin-left:0px;
		margin-top:0px;
	}
	.halfFrameTop{
		position: absolute;
		top: 0px;
		height:100%;
	}
	.halfFrameBottom{
		position: absolute;
		top: 960px;
		height:100%;
	}
	.halfPostTop{
		position: absolute;
		top: 0px;
		z-index: -1;
		height:100%;
	}
	.halfPostBottom{
		position: absolute;
		top: 960px;
		z-index: -1;
		height:100%;
	}
	.quarterSection {
		width:540px;
		height:960px;
		margin-right:0px;
		margin-bottom:0px;
		margin-left:0px;
		margin-top:0px;
	}
	.quarterFrameTopLeft{
		position: absolute;
		height:100%;
	}
	.quarterFrameTopRight{
		position: absolute;
		left: 540px;
		height:100%;
	}
	.quarterFrameBottomLeft{
		position: absolute;
		top: 960px;
		height:100%;
	}
	.quarterFrameBottomRight{
		position: absolute;
		top: 960px;
		left: 540px;
		height:100%;
	}
	.quarterPostTopLeft{
		position: absolute;
		z-index: -1;
		height:100%;
	}
	.quarterPostTopRight{
		position: absolute;
		left: 540px;
		z-index: -1;
		height:100%;
	}
	.quarterPostBottomLeft{
		position: absolute;
		top: 960px;
		z-index: -1;
		height:100%;
	}
	.quarterPostBottomRight{
		position: absolute;
		top: 960px;
		left: 540px;
		z-index: -1;
		height:100%;
	}
	</style>
	<link href='https://fonts.googleapis.com/css?family=Anonymous+Pro:700' rel='stylesheet' type='text/css' />
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet" type='text/css' />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script type="text/javascript">

		var posts = new Array();
		var slotsIds;

		function addPost(title,description1,description2,description3,folderName,tnUrl) {
			posts.push({
				title: title,
				description1: description1,
				description2: description2,
				description3: description3,
				folderName: folderName,
				tnUrl: tnUrl
			});
		}

		$(document).ready(function() {

			<?php

				function getXMLVariable($doc,$name) {
					foreach ($doc->getElementsByTagName('var') as $el) if ($el->getAttribute("name")==$name) return $el->getAttribute("value");
					return null;
				}

				$baseXml = 'posts.xml';
				$doc = new DOMDocument();
				$doc->Load($baseXml);

				$posts = $doc->getElementsByTagName('post');
				for ($i=0 ; $i < $posts->length ; $i++) {
					$elements = $posts->item($i)->getElementsByTagName('element');
					$title="";
					$description1="";
					$description2="";
					$description3="";
					$folderName="";
					$tnUrl="";
					for ($j=0 ; $j < $elements->length ; $j++) {
						if ($elements->item($j)->getAttribute("name")=="title") $title = $elements->item($j)->getAttribute("value");
						if ($elements->item($j)->getAttribute("name")=="description1") $description1 = $elements->item($j)->getAttribute("value");
						if ($elements->item($j)->getAttribute("name")=="description2") $description2 = $elements->item($j)->getAttribute("value");
						if ($elements->item($j)->getAttribute("name")=="description3") $description3 = $elements->item($j)->getAttribute("value");
						if ($elements->item($j)->getAttribute("name")=="thumbnail") $tnUrl = $elements->item($j)->getAttribute("value");
						if ($elements->item($j)->getAttribute("name")=="folderName") $folderName = $elements->item($j)->getAttribute("value");
					}
					echo 'addPost("'.$title.'","'.$description1.'","'.$description2.'","'.$description3.'","'.$folderName.'","'.$tnUrl.'");';
				}

				$slotsIds = array();
				$currentWeek=getXMLVariable($doc,"currentWeek");
				$slotsIdNodes = $doc->getElementsByTagName('slots')->item(0)->getElementsByTagName('week')->item($currentWeek)->getElementsByTagName('format');
				for ($i=0 ; $i < $slotsIdNodes->length ; $i++) {
					$slotsIds[$i]=array();
					for ($j=0 ; $j < $slotsIdNodes->item($i)->getElementsByTagName('slot')->length ; $j++) {
						$thisSlot = $slotsIdNodes->item($i)->getElementsByTagName('slot')->item($j);
						if ($thisSlot->getAttribute("targetid")!=-1) {
							$slotsIds[$i][]=$thisSlot->getAttribute("targetid");
						}
					}
				}

				echo "slotsIds = ". json_encode($slotsIds) . ";";

			?>

			updateSlots();
			$("#displayStyle0").show();
			switchDisplay();

		});

		var currentD = -1;
		<?php
			$displayNodes = $doc->getElementsByTagName('slider')->item(0)->getElementsByTagName('slide');
			echo 'var displayTypesList = new Array(';
			for ($i=0 ; $i < $displayNodes->length ; $i++) {
				if ($i>0) echo ",";
				echo $displayNodes->item($i)->getAttribute('format');
			}
			echo ');';
			echo 'var timeoutForType = new Array(';
			for ($i=0 ; $i < $displayNodes->length ; $i++) {
				if ($i>0) echo ",";
				echo $displayNodes->item($i)->getAttribute('delay');
			}
			echo ');';
		?>
		function switchDisplay() {

			// change current display
			currentD=(currentD+1)%displayTypesList.length;

			var currentType = displayTypesList[currentD];

			// hide everything first
			$("#displayStyle0").hide();
			$("#displayStyle1").hide();
			$("#displayStyle2").hide();
			$("#displayStyle3").hide();

			// shift indexes
			currentF[currentType]=(currentF[currentType]+grouppingPerF[currentType])%slotsIds[currentType].length;

			// update slots
			updateSlots();

			// display the right presentation
			$("#displayStyle"+(displayTypesList[currentD])).show();

			// schedule the next display switch
			setTimeout(switchDisplay, timeoutForType[currentD]);

		}

		var currentF = new Array(0,0,0,0);
		var grouppingPerF = new Array(9,1,4,2);
		function updateSlots() {
			for (var i=0 ; i<grouppingPerF[0] ; i++) {
				var thisPost = posts[slotsIds[0][(currentF[0]+i)%slotsIds[0].length]];
				var $thisImg = $("<img>").attr("src",thisPost.folderName+'/'+thisPost.tnUrl).addClass("thumbnail").attr("id","lineThumb"+i);
				var $thisTitle = $("<div>").html(thisPost.title).css("font-family","Source Sans Pro").css("font-size","40px").css("font-weight","bold");
				var $thisDescription = $("<div>").html(thisPost.description1).append('<br>').append(thisPost.description2).append('<br>').append(thisPost.description3).css("font-family","Anonymous Pro").css("font-size","30px");
				var $imgCol = $("<td>").append($thisImg);
				var $textCol = $("<td>").append($thisTitle).append($thisDescription).css("vertical-align","top").css("padding-top","10px").css("padding-left","10px").css("word-wrap","break-word");
				var $table = $("<table>").css("border-spacing","0px").append($imgCol).append($textCol);
				$("#line"+i).append($table);
			}
			$("#big").attr("src",posts[slotsIds[1][currentF[1]]].folderName+'/'+posts[slotsIds[1][currentF[1]]].tnUrl);
			$("#quarter1").attr("src",posts[slotsIds[2][(currentF[2]+0)%slotsIds[2].length]].folderName+'/'+posts[slotsIds[2][(currentF[2]+0)%slotsIds[2].length]].tnUrl);
			$("#quarter2").attr("src",posts[slotsIds[2][(currentF[2]+1)%slotsIds[2].length]].folderName+'/'+posts[slotsIds[2][(currentF[2]+1)%slotsIds[2].length]].tnUrl);
			$("#quarter3").attr("src",posts[slotsIds[2][(currentF[2]+2)%slotsIds[2].length]].folderName+'/'+posts[slotsIds[2][(currentF[2]+2)%slotsIds[2].length]].tnUrl);
			$("#quarter4").attr("src",posts[slotsIds[2][(currentF[2]+3)%slotsIds[2].length]].folderName+'/'+posts[slotsIds[2][(currentF[2]+3)%slotsIds[2].length]].tnUrl);
			$("#half1").attr("src",posts[slotsIds[3][(currentF[3]+0)%slotsIds[3].length]].folderName+'/'+posts[slotsIds[3][(currentF[3]+0)%slotsIds[3].length]].tnUrl);
			$("#half2").attr("src",posts[slotsIds[3][(currentF[3]+1)%slotsIds[3].length]].folderName+'/'+posts[slotsIds[3][(currentF[3]+1)%slotsIds[3].length]].tnUrl);
		}

	</script>
	<meta http-equiv="refresh" content="43200; URL=./">
	<link rel="icon" type="image/png" href="favicon.png" />
	<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="favicon.ico" /><![endif]-->
</head>
<body>
	<?php // echo "// ".var_dump($slotsIds); ?>
	<div id="displayStyle0" style="display:none;"><!--thumbnails-->
		<?php
			for ($i=0;$i<9;$i++) {
				if ($i%2==0) echo '<div class="ListLineA" id="line'.$i.'">';
				else		 echo '<div class="ListLineB" id="line'.$i.'">';
				echo '</div>';
			}
		?>
		<div class="logoLine"><img src="/images/maintheme/bandeau_1080x120_deikon.png" alt="Deikon"></div>
	</div>
	<div id="displayStyle1" style="display:none;"><!--full-->
		<div class="bigFrame">
			<img src="images/maintheme/frame_1080x1920.png" alt="" />
		</div>
		<div class="bigPost">
			<img id="big" alt="" />
		</div>
	</div>
	<div id="displayStyle2" style="display:none;"><!--quarter-->
		<div>
			<div class="quarterFrameTopLeft">
				<img src="images/maintheme/frame_540x960_top_left.png" alt="" />
			</div>
			<div class="quarterPostTopLeft">
				<img class="quarterSection" id="quarter1" />
			</div>
			<div class="quarterFrameTopRight">
				<img src="images/maintheme/frame_540x960_top_right.png" alt="" />
			</div>
			<div class="quarterPostTopRight">
				<img class="quarterSection" id="quarter2" />
			</div>
			<div class="quarterFrameBottomLeft">
				<img src="images/maintheme/frame_540x960_bottom_left.png" alt="" />
			</div>
			<div class="quarterPostBottomLeft">
				<img class="quarterSection" id="quarter3" />
			</div>
			<div class="quarterFrameBottomRight">
				<img src="images/maintheme/frame_540x960_bottom_right.png" alt="" />
			</div>
			<div class="quarterPostBottomRight">
				<img class="quarterSection" id="quarter4" />
			</div>
		</div>
	</div>
	<div id="displayStyle3" style="display:none;"><!--half-->
		<div>
			<div class="halfFrameTop">
				<img src="images/maintheme/frame_1080x960_top.png" alt="" /><br />
			</div>
			<div class="halfPostTop">
				<img class="halfSection" id="half1" />
			</div>
			<div class="halfFrameBottom">
				<img src="images/maintheme/frame_1080x960_bottom.png" alt="" />
			</div>
			<div class="halfPostBottom">
				<img class="halfSection" id="half2" />
			</div>
		</div>
	</div>
</body>
</html>
