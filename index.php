<html>
	<head>
		<title>Popeens Youtube->Emby File Generator</title>
	</head>
	<body>
		<form method="POST" action="download.php" target="log">
			<input type="text" name="ch" placeholder="Youtube username">
			<!-- The API should take a youtube video id (?v=YOUTUBE_VIDEO_ID) and redirect to a directlink -->
			<input type="text" name="api_url" placeholder="Youtube directlink API" value="http://popeen.com/api/youtube/direct?v=">
			<input type="checkbox" name="reset" id="reset" value="1" checked="checked"> <label for="reset">Reset</label>
			<input type="submit" value="Generate Emby files">
		</form>
		<iframe name="log" src="download.php" style="width:700px; height:250px;"></iframe>
	</body>
</html>