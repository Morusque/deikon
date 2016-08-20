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
		width:1126px;
		background-color:#DD8;
		margin:4px;
	}
	.ListLineB {
		font-size:30px;
		height:200px;
		width:1126px;
		background-color:#8DD;
		margin:4px;
	}
	body {
		background-color:black;
		margin:0px;
		padding:0px;
	}
	
	</style>
	<link href='https://fonts.googleapis.com/css?family=Anonymous+Pro:700' rel='stylesheet' type='text/css'>
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
			
				$baseXml = './admin/posts.xml';
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
		var grouppingPerF = new Array(8,1,4,2);
		function updateSlots() {
			for (var i=0 ; i<grouppingPerF[0] ; i++) {
				var thisPost = posts[slotsIds[0][(currentF[0]+i)%slotsIds[0].length]];
				$("#line"+i).html("");
				var $thisImg = $("<img>").attr("src",thisPost.folderName+'/'+thisPost.tnUrl).addClass("thumbnail").attr("id","lineThumb"+i);
				var $thisTitle = $("<div>").html(thisPost.title).css("font-family","Anonymous Pro").css("font-size","40px").css("font-weight","bold");
				var $thisDescription = $("<div>").html(thisPost.description1).append('<br>').append(thisPost.description2).append('<br>').append(thisPost.description3).css("font-family","Anonymous Pro").css("font-size","30px").css("font-weight","bold");
				var $imgCol = $("<td>").append($thisImg);
				var $textCol = $("<td>").append($thisTitle).append($thisDescription).css("vertical-align","top").css("padding-top","10px").css("padding-left","10px");
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
</head>
<body>
	<?php // echo "// ".var_dump($slotsIds); ?>
	<div id="displayStyle0" style="display:none;"><!--thumbnails-->
		<?php
			for ($i=0;$i<8;$i++) {
				if ($i%2==0) echo '<div class="ListLineA" id="line'.$i.'">';
				else		 echo '<div class="ListLineB" id="line'.$i.'">';
				echo '</div>';
			}
			echo '<div style="height:60px;background-color:#FFF;width:1126px;margin:4px;">DEIKON</div>';
		?>
	</div>
	<div id="displayStyle1" style="display:none;"><!--full-->
		<img id="big" style="width:1126px;height:1692px;margin:4px;"></img>
	</div>
	<div id="displayStyle2" style="display:none;"><!--quarter-->
		<img id="quarter1" style="width:545px;height:838px;margin-right:0px;margin-bottom:0px;margin-left:4px;margin-top:4px;"></img><img id="quarter2" style="width:545px;height:838px;margin-right:0px;margin-bottom:0px;margin-left:4px;margin-top:4px;"></img><br/>
		<img id="quarter3" style="width:545px;height:838px;margin-right:0px;margin-bottom:0px;margin-left:4px;margin-top:4px;"></img><img id="quarter4" style="width:545px;height:838px;margin-right:0px;margin-bottom:0px;margin-left:4px;margin-top:4px;"></img>
	</div>
	<div id="displayStyle3" style="display:none;"><!--half-->
		<img id="half1" style="width:1126px;height:838px;margin-left:4px;margin-top:4px;"></img><br/>
		<img id="half2" style="width:1126px;height:838px;margin-left:4px;margin-top:4px;"></img>
	</div>
</body>
</html>
