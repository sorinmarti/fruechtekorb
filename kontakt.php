<?php
session_start();
include_once("functions.php");
include_once("mail.functions.php");
pageStart($_SERVER['PHP_SELF']);

$content = "";
if(isset($_REQUEST['sendContactForm'])) {
	// Check if form is complete
	$complete = true;
	$message = "";
	
	if($_REQUEST['contact_text']=="") {
		$complete = false;
		$message = "Bitte schreiben Sie eine Nachricht.";
	}
	if($_REQUEST['contact_email']=="") {
		$complete = false;
		$message = "Geben Sie eine E-Mail-Adresse an.";
	}
	if($_REQUEST['contact_name']=="") {
		$complete = false;
		$message = "Tragen Sie einen Namen ein.";
	}
	
	// Complete: send mail and show success message
	if($complete) {
		sendContactFormMail($_REQUEST['contact_email'], $_REQUEST['contact_name'], $_REQUEST['contact_text']);
		$content = getSuccessMessage($_SERVER['PHP_SELF']);
	}
	// Incomplete: SHow form again with error message
	else {
		$content = getForm($_SERVER['PHP_SELF'], $_REQUEST, $message);
	}
} else {
	$content = getForm($_SERVER['PHP_SELF'], $_REQUEST);
}

echo "<table>
		<tr>
			<td valign=\"top\">
				<img src=\"images/kontakt.png\" />
			</td>
			<td valign=\"top\">
				".$content."
			</td>
		</tr>
	  </table>";

pageEnd();

function getSuccessMessage($page) {
	$retVal = "<h2>Ihre Nachricht wurde versendet.</h2>
			   Vielen Dank, wir haben Ihre Nachricht erhalten. Wir werden Sie bald kontaktieren.<br/>";
	return $retVal;
}

function getForm($page, $request, $message="") {
	$name = "";
	$email = "";
	$text = "";
	if(isset($request['contact_name']))  { $name  = $request['contact_name']; }
	if(isset($request['contact_email'])) { $email = $request['contact_email']; }
	if(isset($request['contact_text']))  { $text  = $request['contact_text']; }
	
	$retVal = "<h2>Kontaktieren Sie uns.</h2>";
	if($message!="") {
		$retVal .= "<div class=\"error\">".$message."</div><br/>";
	}
	$retVal .= "Dieses Projekt befindet sich im Aufbau. Haben Sie Fragen oder möchten Sie die Daten für eine Arbeit verwenden? Schreiben Sie uns an <a href=\"mailto:".$GLOBALS["contact_email"]."\">".$GLOBALS["contact_email"]."</a> oder kontaktieren Sie uns mit unten stehendem Formular.<br/><br/>
				<form action=\"".$page."\" method=\"POST\">
					<table>
						<tr>
							<td width=\"10%\">Name</td>
							<td width=\"50%\"><input type=\"text\" name=\"contact_name\" value=\"".$name."\" class=\"searchform\" /></td>
						</tr>
						<tr>
							<td>E-Mail</td>
							<td><input type=\"text\" name=\"contact_email\" value=\"".$email."\" class=\"searchform\" /></td>
						</tr>
						<tr>
							<td>Nachricht</td>
							<td>
								<textarea name=\"contact_text\" class=\"searchform\" rows=\"8\">".$text."</textarea>
							</td>
						</tr>
						<tr>
							<td></td>
							<td align=\"right\"><input type=\"submit\" name=\"sendContactForm\" value=\"Nachricht senden\" /></td>
						</tr>
					</table>
				</form>";
	return $retVal;
}
?>