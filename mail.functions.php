<?php
include_once("globals.php");

function sendMail($to, $subject, $message) {
	$header = "From: ".$GLOBALS['site_email']. "\r\n" .
              "Reply-To: ".$GLOBALS['site_email']. "\r\n" .
              "X-Mailer: PHP/" . phpversion();
	
	$success = mail( $to , $subject, $message, $header );
	
	return $success;
}

function sendContactFormMail($from, $name, $message) {
	$formattedMessage = "Sie haben eine Nachricht von ".$name." erhalten:\n\n\"".$message."\"\n\nAntworten Sie an: ".$from."\n\n(Dies ist eine automatisch generierte Nachricht. Antworten Sie nicht darauf.)";
	sendMail($GLOBALS['contact_email'], "Nachricht von ".$GLOBALS['project_name'], $formattedMessage);
}

function sendNewUserMail($name, $email, $password) {
	$formattedMessage = "Liebe/r ".$name."\n\nFür Sie wurde für das Projekt ".$GLOBALS['project_name']." ein Benutzerkonto angelegt. Sie können sich hier einloggen:\n\n ".$GLOBALS['login_page']."\n\n Ihr initiales Passwort lautet: ".$password." \n\n Bitte ändern Sie ihr Passwort nach Ihrem ersten Login.\n\n Beste Grüsse vom ".$GLOBALS['project_name']."-Team!";
	
	sendMail($email, "Benutzerkonto bei ".$GLOBALS['project_name'], $formattedMessage);
}

function sendPasswordUpdateMail($name, $email, $password) {
	$formattedMessage = "Liebe/r ".$name."\n\nDas Passwort für Ihr Benutzerkonto bei ".$GLOBALS['project_name']." wurde zurückgesetzt.\n\n Ihr neues Passwort lautet: ".$password." \n\n Bitte ändern Sie ihr Passwort nach Ihrem nächsten Login.\n\n Beste Grüsse vom ".$GLOBALS['project_name']."-Team!";
	sendMail($email, "Neues Passwort bei ".$GLOBALS['project_name'], $formattedMessage);
}
?>