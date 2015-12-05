<?php include('PHP-Snippets/include_functions.php'); ?>
<html>
	<head>
		<title>Youtube->Emby File Generator</title>
		<style>
			body{
				background: #eee;
			}
			.os{
				color: #777;
			}
			.os a{
				color: #777;
			}
			.terminal{
				margin-bottom:5px;
			}
		</style>
	</head>
	<body>
		<form method="POST" action="download.php" target="log">
			<input type="text" name="ch" placeholder="Youtube username">
			<!-- The API should take a youtube video id (?v=YOUTUBE_VIDEO_ID) and redirect to a directlink -->
			<?php
				
				/**
				
				If you for some reason don't want to use the internal API an easy 
				way of finding external APIs is by going to this google search
				https://www.google.com/search?q=youtube+downloader+Put+in+just+the+ID+bit,+the+part+after+v%3D.
				and add getvideo.php?videoid= to the url. Make sure to test that it
				works before using it. Still its recommended to use the internal API
				because then you are the only one that controlls if the API stays up.
				
				**/
			
			?>
			<select name="api_url">
				<option value="<?php echo get_url_directory() .'YouTube-Downloader/getvideo.php?videoid='; ?>">Internal API (Recommended)</option>
				<option value="http://popeen.com/api/youtube/direct?v=">popeen.com</option>
			</select>
			<input type="checkbox" name="reset" id="reset" value="1" checked="checked"> <label for="reset">Reset</label>
			<input type="submit" value="Generate Emby files">
		</form>
		<iframe name="log" src="download.php" class="terminal" style="width:700px; height:250px;"></iframe><br/>
		<span class="os">
			Open Source under the 
			<a href="https://github.com/Kakadua/Youtube-Emby-generator/blob/master/LICENSE">MIT License</a>. 
			Get it at <a href="https://github.com/Kakadua/Youtube-Emby-generator">GitHub</a>.
			The API is under <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html">GPL</a>
			and also availiable on <a href="https://github.com/jeckman/YouTube-Downloader">GitHub</a>.
		</span>
	</body>
</html>