<?php
// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

$yourEmail = ""; // the email address you wish to receive these mails through
$yourWebsite = "WEBSITE NAME"; // the name of your website
$thanksPage = ''; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
$maxPoints = 4; // max points a person can hit before it refuses to submit - recommend 4
$requiredFields = "name,email,comments"; // names of the fields you'd like to be required as a minimum, separate each field with a comma


// DO NOT EDIT BELOW HERE
$error_msg = array();
$result = null;

$requiredFields = explode(",", $requiredFields);

function clean($data) {
	$data = trim(stripslashes(strip_tags($data)));
	return $data;
}
function isBot() {
	$bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

	foreach ($bots as $bot)
		if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
			return true;

	if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
		return true;
	
	return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (isBot() !== false)
		$error_msg[] = "No bots please! UA reported as: ".$_SERVER['HTTP_USER_AGENT'];
		
	// lets check a few things - not enough to trigger an error on their own, but worth assigning a spam score.. 
	// score quickly adds up therefore allowing genuine users with 'accidental' score through but cutting out real spam :)
	$points = (int)0;
	
	$badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript", "guest", "dick", "seo", "lbs");

	foreach ($badwords as $word)
		if (
			strpos(strtolower($_POST['comments']), $word) !== false || 
			strpos(strtolower($_POST['name']), $word) !== false
		)
			$points += 3;
	
	if (strpos($_POST['comments'], "http://") !== false || strpos($_POST['comments'], "www.") !== false)
		$points += 2;
	if (isset($_POST['nojs']))
		$points += 5;
	if (preg_match("/(<.*>)/i", $_POST['comments']))
		$points += 2;
	if (strlen($_POST['name']) < 3)
		$points += 1;
	if (strlen($_POST['comments']) < 15 || strlen($_POST['comments']) > 1500)
		$points += 2;
	if (preg_match("/[bcdfghjklmnpqrstvwxyz]{7,}/i", $_POST['comments']))
		$points += 1;
	// end score assignments

	if ( !empty( $requiredFields ) ) {
		foreach($requiredFields as $field) {
			trim($_POST[$field]);
			
			if (!isset($_POST[$field]) || empty($_POST[$field]) && array_pop($error_msg) != "Please fill in all the required fields and submit again.\r\n")
				$error_msg[] = "Please fill in all the required fields and submit again.";
		}
	}

	if (!empty($_POST['name']) && !preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['name'])))
		$error_msg[] = "The name field must not contain special characters.\r\n";
	if (!empty($_POST['email']) && !preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email'])))
		$error_msg[] = "That is not a valid e-mail address.\r\n";
	if (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['url']))
		$error_msg[] = "Invalid website url.\r\n";
	
	if ($error_msg == NULL && $points <= $maxPoints) {
		$subject = "Website Feedback";
		
		$message = "The following e-mail message was received through your website: \n\n";
		foreach ($_POST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subval) {
					$message .= ucwords($key) . ": " . clean($subval) . "\r\n";
				}
			} else {
				$message .= ucwords($key) . ": " . clean($val) . "\r\n";
			}
		}
		$message .= "\r\n";
		$message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
		$message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
		$message .= 'Points: '.$points;

		if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
			$headers   = "From: {$_POST['email']}\r\n";
		} else {
			$headers   = "From: {$_POST['name']} <{$_POST['email']}>\r\n";	
		}
		$headers  .= "Reply-To: {$_POST['name']} <{$_POST['email']}>\r\n";

		if (mail($yourEmail,$subject,$message,$headers)) {
			if (!empty($thanksPage)) {
				header("Location: $thanksPage");
				exit;
			} else {
				$result = 'Your mail was successfully sent.';
				$disable = true;
			}
		} else {
			$error_msg[] = 'Your mail could not be sent this time. ['.$points.']';
		}
	} else {
		if (empty($error_msg))
			$error_msg[] = 'Your mail looks too much like spam, and could not be sent this time. ['.$points.']';
	}
}
function get_data($var) {
	if (isset($_POST[$var]))
		echo htmlspecialchars($_POST[$var]);
}
?>
<!DOCTYPE html>
<html lang="en-au">
<head>
	<title>Contact Form</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="HandheldFriendly" content="true">
	<!--Free PHP Mail Form v2.4.5. Copyright (c) Jem Turner 2007-2017. http://jemsmailform.com/-->
	<noscript>
		<style>
			.nojavascript {
				border: solid 2px red;
				border-radius: 6px;
				display: flex;
				flex-flow: column;
				background-color: red;
				margin: 2em 0 2em 0;
				font-size: 1em !important;
			}
			.nojavascript h3 {
				margin: 0 15px 0 15px;
				font-size: 1.3em;
			}
			.nojavascript p {
				margin: 0 15px 0 15px;
			}
			#submit {
				display: none;
			}
			#submit-nojs {
				opacity:0.2;
				cursor:no-drop;
			}
			.contact-sm-icon {
				max-width: 10vw;
			}
		</style>
	</noscript>
	<style>
	* {
		margin:0;
		padding:0;
		box-sizing:border-box; }

	*:before, *:after {
	  box-sizing: inherit;
	}
	html { 
		font-size:100%;
		scroll-behavior: smooth;
		--maxWidth: 1284px;
	}
	body {
		padding: 1%;
	}
	form {
		margin: 0.5em 0 0.5em 0;
	}
	.error {
		border: solid 2px orange;
		border-radius: 6px;
		display: flex;
		flex-flow: column;
		background-color: orange;
		margin: 2em 0 2em 0;
		padding: 0.5em;
		font-size: 1em !important;
	}
	.success {
		border: solid 2px green;
		border-radius: 6px;
		display: flex;
		flex-flow: column;
		background-color: green;
		margin: 2em 0 2em 0;
		padding: 0.5em;
		font-size: 1em !important;
	}
	.form-grid-container {
  		display: grid;
  		grid-template-columns: 35% 65%; 	
  	}
  	.form-grid-item {
	margin: 0.1em;
	justify-content: center;
	}
	.required {
		color: red;
		margin: 0 0.2em 0 0.2em;
	}
	input, textarea {
		font-size: 1em;
		font-family: 'Neuton', serif;
		padding: 0.2em;
	}
	textarea {
		height: 8em;
		width: 100%;
	}
	input[type="text"] {
		width: 100%;
	}
	input[type="submit"] {
		width: auto;
		padding: 0.2em;
		cursor: pointer;
	}
	@media (min-width: 200px) and (max-width: 500px) { 
		.form-grid-container {
			grid-template-columns: 100%;
		}
	}
	</style>
</head>
<body>
<h1>Contact Form</h1>
<!--
	Free PHP Mail Form v2.4.5 - Secure single-page PHP mail form for your website
	Copyright (c) Jem Turner 2007-2017
	http://jemsmailform.com/

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	To read the GNU General Public License, see http://www.gnu.org/licenses/.
-->

<?php if (!empty($error_msg)) {
	echo '<p class="error">ERROR: '. implode("<br />", $error_msg) . "</p>";
}
if ($result != NULL) {
	echo '<p class="success">'. $result . "</p>";
}?>

<noscript>
	<div class="nojavascript">
		<h3>JavaScript is turned off in your browser!</h3>
		<p>You will not be able to submit this form until you enable JavaScript. This is an anti-spam measure.</p>
	</div>
</noscript>

<form class="form-grid-container" action="<?php echo basename(__FILE__); ?>" method="post" enctype="multipart/form-data">
	<div class="form-grid-item item-01">
	<noscript>
		<input type="hidden" name="nojs" id="nojs" />
	</noscript>
	<label for="name">Your name<span class="required">*</span>:</label>
	</div>
	<div class="form-grid-item item-02">
		<input type="text" name="name" id="name" value="<?php get_data("name"); ?>" />
	</div>
	<div class="form-grid-item item-03">
		<label for="email">Your email address<span class="required">*</span>:</label>
	</div>
	<div class="form-grid-item item-04"><input type="text" name="email" id="email" value="<?php get_data("email"); ?>" />
	</div>
	<div class="form-grid-item item-05">
		<label for="comments">Your message<span class="required">*</span>:</label>
	</div>
	<div class="form-grid-item item-06">
		<textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea>
	</div>
	<div class="form-grid-item item-08">
		<input type="submit" name="submit" id="submit" value="Send Email">
		<noscript>
			<input type="submit" id="submit-nojs" value="Send Email" disabled="disabled">
		</noscript>
	</div>
</form>

<p>Powered by <a href="https://jemsmailform.com/">Jem’s PHP Mail Form</a>. Modified by <a href="https://www.adonline.id.au">Adam Dimech</a>.</p>

</body>
</html>
