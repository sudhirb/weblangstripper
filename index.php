<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("content-type: text/html; charset=UTF-8");
require_once("lang_constants.php");


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
			$text = html_entity_decode($output);
		}
	}
	else{
		// this the correct text.
		$text = $urlOrText;
	}
	
	echo $urlOrText;
	echo $text;
	
	if(!($text === FALSE)){
		// we now have the text loaded
		$regex_array = array(
			"hi" => "#[" . hi_CHARS . "]+[" . colon_CHARS . hi_CHARS . "]#u",
			"ta" => "#[" . ta_CHARS . "]+#u",
			"ml" => "#[" . ml_CHARS . "]+#u",
			"te" => "#[" . te_CHARS . "]+#u",
			"kn" => "#[" . kn_CHARS . "]+#u",
			"mr" => "#[" . mr_CHARS . "]+#u",
			"or" => "#[" . or_CHARS . "]+[" . or_CHARS . zero_width_joiner_CHARS . "]*[" . or_CHARS . "]+#u",
			"gu" => "#[" . gu_CHARS . "]+#u",
			"bn" => "#[" . bn_CHARS . "]+[" . apostrophe_CHARS . "]?[" . bn_CHARS . "]*#u",
			"as" => "#[" . as_CHARS . "]+[" . apostrophe_CHARS . bn_CHARS . "]*#u",
			"ur" => "#[" . ur_CHARS . "]+#u",
			"pa" => "#[" . pa_CHARS . "]+#u"
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
		print_r($matches);
	}
} 
?>
<html>
<head>
<style>
table {
    width: 100%;
    border-collapse: collapse;
}
table, th, td {
   border: 1px solid black;
}
th {
    height: 50px;
}
th, tr{
	text-align: center;
}
</style>
</head>
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
	<button id="copy-text">Copy</button>
    <span id="copied-text" style="display: none;">Copied!</span>
	<table id="text-to-copy" style="width:100%;">
	<tr>
	<th>Word</td>
	<th>Occurences</td>
	</tr>
	<?php
	foreach($matches as $key => $value){
		?>
		<tr>
		<td><?php echo $key?></td>
		<td><?php echo $value?></td>
		</tr>
		<?php
	}

	print_r($matches);
	?>
	</table>
	<script lang="text/javascript">
	
	function selectElementContents(el) {
        var body = document.body, range, sel;
        if (document.createRange && window.getSelection) {
            range = document.createRange();
            sel = window.getSelection();
            sel.removeAllRanges();
            try {
                range.selectNodeContents(el);
                sel.addRange(range);
            } catch (e) {
                range.selectNode(el);
                sel.addRange(range);
            }
        } else if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
        }
    }
	
	// Add click event
	document.getElementById('copy-text').addEventListener('click', function(e){
	  e.preventDefault();
  
	  // Select the text
	  selectElementContents(document.getElementById('text-to-copy'));
  
	  var copied;
  
	  try
	  {
		  // Copy the text
		  copied = document.execCommand('copy');
	  } 
	  catch (ex)
	  {
		  copied = false;  
	  }
  
	  if(copied)
	  {
		// Display the copied text message
		document.getElementById('copied-text').style.display = 'block';    
	  }
  
	});
	</script>
	<?php
}
?>
</body>
</html>