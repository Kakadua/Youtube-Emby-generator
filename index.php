<html>
	<head>
		<title>Popeens Youtube->Emby File Generator</title>
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
			<input type="text" name="api_url" placeholder="Youtube directlink API" value="http://popeen.com/api/youtube/direct?v=">
			<input type="checkbox" name="reset" id="reset" value="1" checked="checked"> <label for="reset">Reset</label>
			<input type="submit" value="Generate Emby files">
		</form>
		<iframe name="log" src="download.php" class="terminal" style="width:700px; height:250px;"></iframe><br/>
		<span class="os">
			Open Source under the 
			<a href="https://github.com/popeen/Youtube--Emby-generator/blob/master/LICENSE">MIT License</a>. 
			Get it at <a href="https://github.com/popeen/Youtube--Emby-generator">GitHub</a>
		</span>
	</body>
</html>