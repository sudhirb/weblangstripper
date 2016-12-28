<?php
header("content-type: text/html; charset=UTF-8");
$urlOrText = $_REQUEST['url_text'];
$lang_code = $_REQUEST["language"];
$matches=FALSE;
if($urlOrText){
	$regex = "#^\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))$#";
	$text = FALSE;

	if(preg_match($regex, $urlOrText)){
		// we need to fetch the text from the url
		$url = $urlOrText;
		// first check if there is a scheme in the url
		$scheme = parse_url($urlOrText, PHP_URL_SCHEME);
		if(!$scheme){
			// there is no scheme prepend http://
			$url = "http://".$urlOrText;
		}
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		$output = curl_exec($curl);
		curl_close($curl);
	
		if($output === FALSE){
			echo "Invalid URL";
		}
		else{
			$text = $output;
		}
	}
	else{
		// this the correct text.
		$text = $urlOrText;
	}
	
	if(!($text === FALSE)){
		// we now have the text loaded
		$regex_array = array(
			"hi" => "#[\x{0900}-\x{097F}]+#u",
			"ta" => "#[\x{0B80}-\x{0BFF}]+#u",
			"ml" => "#[\x{0D00}-\x{0D7F}]+#u",
			"te" => "#[\x{0C00}-\x{0C7F}]+#u",
			"kn" => "#[\x{0C80}-\x{0CFF}]+#u",
			"mr" => "#[\x{0900}-\x{097F}]+#u",
			"or" => "#[\x{0B00}-\x{0B7F}]+#u",
			"gu" => "#[\x{0A80}-\x{0AFF}]+#u",
			"bn" => "#[\x{0980}-\x{09FF}]+#u",
			"as" => "#[\x{0980}-\x{09FF}]+#u",
			"ur" => "#[\x{0600}-\x{097F}]+#u",
			"pa" => "#[\x{0A00}-\x{0A7F}]+#u"
		);
		$regex = $regex_array[$lang_code];
		
		// grep for words and word count
		//$matches = array();
		$result = preg_match_all($regex, $text, $temp_matches);
		// dump all temp_matches into the main matches
		if($result > 0){
			$matches = array_count_values($temp_matches[0]);
			arsort($matches);
		}
		//print_r($matches);
	}
} 
?>
<html>
<body>
<div style="text-align:center; width:100%">
<form method="post">
Enter the url or any string:<br/><br/>
<input type="text" name="url_text" style="width:80%" value="<?php echo ($urlOrText ? $urlOrText : '');?>"/>
<br/><br/>
Select Language
<br/>
<input type="radio" name="language" value="hi" <?php if($lang_code == "hi" || !isset($lang_code)) echo "checked";?>> Hindi </input>
<input type="radio" name="language" value="ta" <?php if($lang_code == "ta") echo "checked";?>> Tamil </input>
<input type="radio" name="language" value="ml" <?php if($lang_code == "ml") echo "checked";?>> Malayalam </input>
<input type="radio" name="language" value="te" <?php if($lang_code == "te") echo "checked";?>> Telugu </input>
<input type="radio" name="language" value="kn" <?php if($lang_code == "kn") echo "checked";?>> Kannada </input>
<input type="radio" name="language" value="mr" <?php if($lang_code == "mr") echo "checked";?>> Marathi </input>
<input type="radio" name="language" value="or" <?php if($lang_code == "or") echo "checked";?>> Oriya </input>
<input type="radio" name="language" value="gu" <?php if($lang_code == "gu") echo "checked";?>> Gujarati </input>
<input type="radio" name="language" value="bn" <?php if($lang_code == "bn") echo "checked";?>> Bengali </input>
<input type="radio" name="language" value="as" <?php if($lang_code == "as") echo "checked";?>> Assamese </input>
<input type="radio" name="language" value="ur" <?php if($lang_code == "ur") echo "checked";?>> Urdu </input>
<input type="radio" name="language" value="pa" <?php if($lang_code == "pa") echo "checked";?>> Punjabi </input>
<br/><br/>
<input type="submit" name="submit_btn"/>
</input>
<form>
</div>
<?php
if(!($matches === FALSE)){
	?>
	<div style="display: table; width:100%;table-layout: fixed;">
	<?php
	foreach($matches as $key => $value){
		?>
		<div style="display: table-row;">
		<div style="display: table-cell;text-align:right;"><?php echo $key?></div>
		<div style="display: table-cell;width:10%;">&nbsp;</div>
		<div style="display: table-cell;text-align:left;"><?php echo $value?></div>
		</div>
		<?php
	}

	//print_r($matches);
	?>
	</div>
	<?php
}
?>
</body>
</html>