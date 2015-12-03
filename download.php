<?php
	set_time_limit(0);
	error_reporting(0);

	include('PHP-Snippets/include_functions.php');
	$API_KEY = file_get_contents('API_KEY.txt');
	
	//Converts youtubes time format
	function covtime($time, $format){
		$ret = new DateTime('@0');
		$ret->add(new DateInterval($time));
		return $ret->format($format);
	}
	
	//Zip folder
	function zip_folder($folder, $zipFile){
		$root = realpath($folder);
		$zip = new ZipArchive();
		$zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root), RecursiveIteratorIterator::LEAVES_ONLY);
		foreach($files as $name => $file){
			if(!$file->isDir()){
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($root) + 1 );
				$zip->addFile($filePath, $relativePath);
			}
		}
		$zip->close();
	}
	
	//Delete directory with files recursivly
	function delete_dir($dirPath) {
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				delete_dir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}
	
	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	); 

	$badCharacters = array_merge(
        array_map('chr', range(0,31)),
        array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));	
?>
<html><head><style>body{ color: #0F0; background: #000; }</style></head><body>
<?php
		
	if(isset($_POST['ch'])){
		
		
		$username = $_POST['ch'];
		
		$user = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername='.$username.'&key='.$API_KEY, false, stream_context_create($arrContextOptions)),true);
		$json = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$user['items'][0]['contentDetails']['relatedPlaylists']['uploads'].'&key='.$API_KEY.'&maxResults=50', false, stream_context_create($arrContextOptions)),true);
		$videos = array();


		$i=0;
		//Get all videos from a channel
		while(isset($json['nextPageToken'])){
			if($i==0){ echo 'Loading videos...<br/>'; }else{ echo 'Loading more videos...<br/>'; }
			echo '<script>window.scrollTo(0,document.documentElement.clientHeight)</script>';
			ob_flush(); flush();
			$videos = array_merge($videos, $json['items']);
			$json = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$user['items'][0]['contentDetails']['relatedPlaylists']['uploads'].'&key='.$API_KEY.'&maxResults=50&pageToken='.$json['nextPageToken'], false, stream_context_create($arrContextOptions)),true);
			$i++;
		}
		$videos = array_reverse(array_merge($videos, $json['items']));
		
		if($_POST['reset'] == '1'){ delete_dir($username); echo 'Deleted old files on server<br/>'; ob_flush(); flush(); } //Delete old versions before downloading
		if(!file_exists('files/'.$username)){ mkdir('files/'.$username, 0777, true);	} //Create folder if it doesn't exist
		if(!file_exists('zip')){ mkdir('zip', 0777, true); } //Create folder if it doesn't exist
		
		$yearCheck =  '';
		foreach($videos as $video){
			
			$API_DIRECTLINK = $_POST['api_url'];
			
			$print = '';

			//Video info
			$title = $video['snippet']['title'];
			$title = str_replace($badCharacters, '', $title);
			$videoId = $video['snippet']['resourceId']['videoId'];
			$date = explode('T', $video['snippet']['publishedAt'])[0];	
			$year = explode('-', $date)[0];	
			$img = $video['snippet']['thumbnails']['standard']['url'];
			if(!isset($video['snippet']['thumbnails']['standard'])){ $img = $video['snippet']['thumbnails']['medium']['url']; } //If standard image is not availiable use medium	
			$channel = $video['snippet']['channelTitle'];
			$info = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?id='.$videoId.'&part=contentDetails&key='.$API_KEY, false, stream_context_create($arrContextOptions)),true);
			$duration = $info['items'][0]['contentDetails']['duration'];
			if($year != $yearCheck){ $yearCheck = $year; $j=1; }else{ $j++; }
			if($j>99){ $j = sprintf('%03d', $j); }else{ $j = sprintf('%02d', $j); }			
			
			//The nfo files
			$xml = '
				<?xml version="1.0" encoding="utf-8" standalone="yes"?>
					<episodedetails>
						<title>'.$title.'</title>
						<year>'.$year.'</year>
						<aired>'.$date.'</aired>
						<runtime>'.covtime($duration, 's').'</runtime>
						<season>'.$year.'</season>
						<episode>'.$j.'</episode>
						<fileinfo>
							<streamdetails>
								<video>
									<duration>'.covtime($duration, 's').'</duration>
									<durationinseconds>'.covtime($duration, 's').'</durationinseconds>
								</video>
							</streamdetails>
						</fileinfo>
					</episodedetails>
				';

			$folder = 'files/'.$username.'/'.$channel.'/'.$year;
			$filename = $folder.'/[S'.$year.'E'.$j.'] '.$title;
			
			if(!file_exists($filename.'.strm')){
				
				if(!file_exists($folder)){ mkdir($folder, 0777, true); } //Create folder if it doesn't exist
				file_write($filename.'.strm', $API_DIRECTLINK.$videoId);
				file_write($filename.'.nfo', $xml);
				file_put_contents($filename.'-thumb.jpg', file_get_contents($img, false, stream_context_create($arrContextOptions)));
				$print .= 'Added: ';

			}else{ $print .= 'Skipped: '; }			
			
			$print .= '[S'.$year.'E'.$j.'] '.$title.'<br/>';
			$print .= '
						<script>
							window.scrollTo(0,document.documentElement.clientHeight);
						</script>
					';
			
			echo $print;
			
			ob_flush();
			flush();
			
		}//end foreach videos
		
		zip_folder('files/'.$username, 'zip/'.$username.'.zip');
		echo '
				<script>
					window.location.href="zip/'.$username.'.zip";
				</script>
			';
		
	}
?>
</body></html>