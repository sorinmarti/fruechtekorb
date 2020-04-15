<?php
session_start();
include_once("functions.php");
include_once("document.funcs.php");

// END THE CONFIRMATION PROCESS
if(isset($_REQUEST['endConfirmation'])) {
	// TODO END PROCESS IN DB
	header("Location: index.php");
	die();
}


$confId = "";
if(isset($_REQUEST['confId'])) {
	$confId   = $_REQUEST['confId'];
	$document = getDocumentObjectByToken($confId);
	
	if(isset($_REQUEST['restartForm'])) {	
		$document['d_step'] = 3;
		$_REQUEST['nextStep'] = 3;
		updateDocument($document);
	}
	
	if($document['d_step']>=8) {
		header("Location: index.php?errormsg=Das Dokument wurde bereits verarbeitet!");
	}
}
else {
  header("Location: index.php");
}


pageStart($_SERVER['PHP_SELF'], false);

echo "<div id=\"confirmBox\">\n";
printStep($confId);
echo "</div>";

pageEnd();

/**
 * THIS FUNCTION PRINTS A STEP IN THE CONFIRMATION 
 * PROCESS. 
 */
function printStep($token) {
	$document = getDocumentObjectByToken($token);
	
	// If the TOKEN is not set or invalid: abort.
	if(!isset($document['d_id'])) {	
		echo "<h1>Es ist ein Fehler aufgetreten</h1>";
		echo "<div class=\"error\">Es ist ein Fehler aufgetreten. Bitte wenden Sie sich an XY.</div>";
		echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"GET\">
				<input type=\"submit\" name=\"endConfirmation\" value=\"Beenden\">
			  </form>";				
		return;
	}
	
	// If there is no NEXT_STEP set: set it to the document's step
	if(!isset($_REQUEST['nextStep'])) {
		$_REQUEST['nextStep'] = $document['d_step'];
	}
	// If the STEP is 0 (e.g. "new process started."), set it to 1.
	if($_REQUEST['nextStep']==0) {
		$_REQUEST['nextStep'] = 1;
	}
	
	// Check for errors in the previously shown page
	$errorBlock = "<div class=\"error\">%s</div>";
	$error = "";
	switch($_REQUEST['nextStep']) {
		case 4:		// Check if step 3 is complete
			$error = checkField($error, "Titel der Arbeit", "d_title");
			$error = checkDateField($error, "Abgabedatum", "d_date");
			// TODO no need for type.
			break;
		case 5:		// Check if step 4 is complete
			// TODO alternative for edit tool
			$error = checkField($error, "Schlagworte", "d_document_tags");
			break;
		case 6:		// Check if step 5 is complete
			$error = checkNumberField($error, "Alter", "d_author_age");
			$error = checkField($error, "Gender", "d_author_gender");
			// TODO alternative for gender
			$error = checkField($error, "Muttersprache", "d_author_native_lang");
			break;
		case 7:
			$error = checkField($error, "Universität", "d_author_uni");
			$error = checkField($error, "Studium", "d_author_bama");
			$error = checkNumberField($error, "Semester", "d_author_semesters");
			$error = checkArrayField($error, "Fächer", "d_author_subjects");
			
			break;
	}
	
	// If there was an error: decrement the previously incremented step
	if($error!="") {
		$_REQUEST['nextStep'] = $_REQUEST['nextStep']-1;
		echo sprintf($errorBlock, $error);
	}
	// If the previously shown page is fine:
	// save the new step
	else {
		$document['d_step'] = $_REQUEST['nextStep'];
	}
	
	// PAGE 3
	$document = setField($document, "d_title");
	$document = setField($document, "d_date");
	$document = setField($document, "d_document_type");
	// PAGE 4
	$document = setField($document, "d_document_lang");
	$document = setField($document, "d_document_tool");
	$document = setField($document, "d_document_tags");
	// PAGE 5
	$document = setField($document, "d_author_age");
	$document = setField($document, "d_author_gender");
	$document = setField($document, "d_author_native_lang");
	// PAGE 6
	$document = setField($document, "d_author_uni");
	$document = setField($document, "d_author_bama");
	$document = setField($document, "d_author_semesters");
	$document = setArrayField($document, "d_author_subjects");
	
	
	// TODO alternative for edit tool
	// TODO alternative for gender
	
	updateDocument($document);
	
	$title = "";
	$text = "";
	$form = "";
	
	//echo "iserror: ".$_REQUEST['nextStep'];
	
	switch(  $_REQUEST['nextStep'] ) {
		case 1: {		// [0]: GIVE PERMISSION
			$title = "Dürfen wir Ihre Arbeit verwenden?";
			$text = "Herzlich willkommen bei FrueCHteKorB. Sie wurden angefragt, ob ihre eingegebene Arbeit für linguistische Zwecke verwendet werden darf.<br/>
					 Bitte geben Sie Ihre Zustimmung, indem Sie das Häckchen setzen und \"Zustimmen\" drücken.";
			$form = "<input type=\"checkbox\" name=\"confirmationCheck\" value=\"confirmed\">
						  <i>Ich stimme zu, dass die Arbeit <b>\"".$document['d_title']."\"</b> in das FrueCHteKorB-Korpus aufgenommen wird und für linguistische Forschungsarbeiten verwendet werden darf.
							 Der Text meiner Arbeit wird mit den folgend anzugebenden Daten (Alter, Gender, Ausbildung) verknüpft, meine persönlichen Daten (Name, E-Mail-Adresse) werden <b>nie</b> weitergegeben.</i>
					 <br/><br/>
					 <input type=\"hidden\" name=\"nextStep\" value=\"2\" />
					 <input type=\"submit\" name=\"confirmUsage\" value=\"Zustimmen\"> 
					 <input type=\"submit\" name=\"denyUsage\" value=\"Ablehnen\"> 
					";
			$document['d_step'] = 1;	// Document process startet
			break;
		}
		case 2: {		// [2]: PERMISSION GRANTED OR DENIED
			$title = "Arbeit autorisieren";
			if(isset($_REQUEST['denyUsage'])) {
				$document['d_step'] = 99;
				updateDocument($document);
				$text = "Vielen Dank für Ihre Antwort.<br/>Wir werden Ihre Arbeit <b>nicht</b> verwenden.";
				$form = "<input type=\"submit\" name=\"endConfirmation\" value=\"Beenden\"> ";
			}
			if(isset($_REQUEST['confirmUsage'])) {
				if(!isset($_REQUEST['confirmationCheck'])) {
					$text = "Bitte aktivieren Sie die Checkbox auf der vorherigen Seite um zuzustimmen.";
					$form = "<input type=\"hidden\" name=\"nextStep\" value=\"99\" />
							 <input type=\"submit\" name=\"goBack\" value=\"Zurück\">";
				}
				else {
					$text = "Vielen Dank! Bitte ergänzen Sie die Metadaten zu ihrer Arbeit auf den folgenden Seiten.";
					$form = "<input type=\"submit\" name=\"startMetadata\" value=\"Daten eingeben\"> ";
				}
			}
			
			break;
		}
		case 3: {		// [3]: METADATA FOR PAPER
			$title = "Schritt 1: Angaben zur Arbeit";
			$text = "Bitte machen Sie folgende Angaben zu Ihrem Dokument:";
			$form = "<table>
						<tr class=\"evenline\">
							<td><nobr>Titel der Arbeit</nobr></td>
							<td><input type=\"text\" name=\"d_title\" value=\"".$document['d_title']."\" /></td>
							<td class=\"info\">Dies sollte dem Titel ihrer Arbeit entsprechen. Ändern Sie ihn nur, falls er Fehler enthalten sollte.</td>
						</tr>
						<tr>
							<td><nobr>Abgabedatum</nobr></td>
							<td><input type=\"text\" name=\"d_date\" value=\"".$document['d_date']."\" /></td>
							<td class=\"info\">Dies sollte dem Abgabedatum ihrer Arbeit entsprechen. Ändern Sie es nur, falls es Fehler enthalten sollte.</td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Dokumenttyp</nobr></td>
							<td>".getDocumentTypeSelect("d_document_type", $document['d_document_type'])."</td>
							<td class=\"info\">Dies sollte dem Typ ihrer Arbeit entsprechen. Ändern Sie ihn nur, falls er nicht stimmt.</td>
						</tr>
						<tr class=\"lastline\">
							<td></td>
							<td><input type=\"submit\" name=\"\" value=\"Weiter\" /></td>
							<td></td>
						</tr>
					 </table>";
			break;
		}
		case 4:	{		// [4]: ADDITIONAL METADATA FOR PAPER
			$title = "Schritt 2: Angaben zur Arbeit";
			$form = "<table>
						<tr class=\"evenline\">
							<td><nobr>Sprache des Dokuments</nobr></td>
							<td>".getLanguageSelect("d_document_lang", $document['d_document_lang'])."</td>
							<td class=\"info\">Sprache des Dokuments.</td>
						</tr>
						<tr>
							<td rowspan=\"2\"><nobr>Erstellung in</nobr></td>
							<td>".getEditToolSelect("d_document_tool", $document['d_document_tool'])."</td>
							<td rowspan=\"2\" class=\"info\">Wählen Sie die Software mit der die Arbeit erstellt wurde. Falls sie nicht aufgelistet ist, geben Sie einen Namen an.</td>
						</tr>
						<tr>
							<td><input type=\"text\" name=\"d_document_tool_alt\" value=\"\" /></td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Schlagworte</nobr></td>
							<td><textarea name=\"d_document_tags\">".$document['d_document_tags']."</textarea></td>
							<td class=\"info\">Geben Sie einige Schlagworte ein, die Ihre Arbeit inhaltlich umreissen. <b>Trennen Sie mit Kommas.</b></td>
						</tr>
						
						<tr class=\"lastline\">
							<td></td>
							<td><input type=\"submit\" name=\"\" value=\"Weiter\" /></td>
							<td></td>
						</tr>
					 </table>";
			break;
		}
		case 5:	{		// [5]: METADATA FOR AUTHOR
			$title = "Schritt 3: Angaben zur Autor*in";
			$form = "<table class=\"formtable\">
						<tr class=\"evenline\">
							<td><nobr>Alter</nobr></td>
							<td><input type=\"text\" name=\"d_author_age\" value=\"".$document['d_author_age']."\" /></td>
							<td class=\"info\">Geben Sie Ihr Alter in Jahren an.</td>
						</tr>
						<tr>
							<td rowspan=\"2\"><nobr>Gender</nobr></td>
							<td>".getGenderSelect("d_author_gender", $document['d_author_gender'])."</td>
							<td rowspan=\"2\" class=\"info\">Wählen Sie Ihr Gender. Falls sie die gewünschte Angabe nicht aufgelistet sehen, geben Sie sie im Textfeld ein.</td>
						</tr>
						<tr>
							<td><input type=\"text\" name=\"d_author_gender_alt\" value=\"\" /></td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Muttersprache</nobr></td>
							<td>".getLanguageSelect("d_author_native_lang", $document['d_author_native_lang'])."</td>
							<td class=\"info\">Geben Sie Ihre Erstsprache an.</td>
						</tr>
						<tr class=\"lastline\">
							<td></td>
							<td><input type=\"submit\" name=\"\" value=\"Weiter\" /></td>
							<td></td>
						</tr>
					 </table>";
			break;
		}
		case 6: {		// [6]: ADDITIONAL METADATA FOR AUTHOR
			$title = "Schritt 4: Angaben zur Autor*in";
			$form = "<table>
						<tr class=\"evenline\">
							<td><nobr>Bildungseinrichtung</nobr></td>
							<td>".getUniversitySelect("d_author_uni", $document['d_author_uni'])."</td>
							<td class=\"info\">Wählen Sie Ihre Bildungseinrichtung. Sollte sie nicht verzeichnet sein: wählen Sie \"Andere\".</td>
						</tr>
						<tr>
							<td><nobr>Studium</nobr></td>
							<td>
							  <nobr>";
								$bama = array(1 => "Bachelor", 2 => "Master", 0=> "Anderes");
								foreach($bama as $value => $label) {
									if($value==$document['d_author_bama']) {
										$selected = "checked";
									}
									else {
										$selected = "";
									}
									$form .= "<input type=\"radio\" name=\"d_author_bama\" id=\"id".$value."\" value=\"".$value."\" ".$selected.">
										  <label for=\"id".$value."\">".$label."</label> ";
								}
								
			$form .= "		  </nobr>
							</td>
							<td class=\"info\"></td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Semester</nobr></td>
							<td><input type=\"text\" name=\"d_author_semesters\" value=\"".$document['d_author_semesters']."\" /></td>
							<td class=\"info\">Wählen Sie Ihr Semester-Gesamtanzahl. (Inkl. aller BA/MA Semester).</td>
						</tr>
						<tr>
							<td><nobr>Fächer</nobr></td>
							<td colspan=\"2\">";
							
							foreach($GLOBALS["subjectLabels"] as $value => $label) {
								//echo $document['d_author_subjects']." => ".$value.": ".($document['d_author_subjects'] & $value)."<br/>";
								if (($document['d_author_subjects'] & $value) == $value) {
  									$selected = "checked";
								}
								else {
									$selected = "";
								}
								$form .= "<nobr>
											<input type=\"checkbox\" id=\"das_".$value."\" name=\"d_author_subjects[]\" value=\"".$value."\" ".$selected." />
											<label for=\"das_".$value."\">".$label."</label>
										  </nobr> ";
							}
							  
			$form .= "		</td>
						</tr>
						<tr class=\"lastline\">
							<td></td>
							<td><input type=\"submit\" name=\"\" value=\"Weiter\" /></td>
							<td></td>
						</tr>
					 </table>";
			break;
		}
		case 7:	{	// [7]: CONFIRM METADATA
			$title = "Schritt 5: Angaben eingeben";
			$text = "Sie haben folgende Angaben gemacht. Bitte überprüfen Sie sie und stimmen Sie erneut der Verwendung zu.";
			$form = "<table width=\"100%\">
					  <tr><td width=\"50%\">
					  
					 <table width=\"100%\">
						<tr>
							<th colspan=\"2\">Dokument</th>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Titel</nobr></td>
							<td>".$document['d_title']."</td>
						</tr>
						<tr>
							<td><nobr>Abgabedatum</nobr></td>
							<td>".$document['d_date']."</td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Dokumenttyp</nobr></td>
							<td>".getDocumentTypeLabel($document['d_document_type'])."</td>
						</tr>
						<tr>
							<td><nobr>Sprache</nobr></td>
							<td>".getLanguageLabel($document['d_document_lang'])."</td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Erstellung in</nobr></td>
							<td>".getEditToolLabel($document['d_document_tool'])."</td>
						</tr>
						<tr>
							<td><nobr>Schlagworte</nobr></td>
							<td>".$document['d_document_tags']."</td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>&nbsp;</nobr></td>
							<td></td>
						</tr>
					  </table>
					  
					  </td><td width=\"50%\">
					  
					  <table width=\"100%\">
						<tr>
							<th colspan=\"2\">Autor*in</th>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Alter</nobr></td>
							<td>".$document['d_author_age']."</td>
						</tr>
						<tr>
							<td><nobr>Gender</nobr></td>
							<td>".getGenderLabel($document['d_author_gender'])."</td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Muttersprache</nobr></td>
							<td>".getLanguageLabel($document['d_author_native_lang'])."</td>
						</tr>
						<tr>
							<td><nobr>Bildungseinrichtung</nobr></td>
							<td>".getUniversityLabel($document['d_author_uni'])."</td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Sudiengang</nobr></td>
							<td>".getBaMaLabel($document['d_author_bama'])."</td>
						</tr>
						<tr>
							<td><nobr>Semester</nobr></td>
							<td>".$document['d_author_semesters']."</td>
						</tr>
						<tr class=\"evenline\">
							<td><nobr>Fächer</nobr></td>
							<td>".getSubjectsLabel($document['d_author_subjects'])."</td>
						</tr>
						
					 </table>
					 
					 </td></tr>
					 
					 <tr class=\"lastline\">
							<td colspan=\"2\">
								<input type=\"submit\" name=\"saveMetadata\" value=\"Zustimmen und speichern\" />
								<input type=\"submit\" name=\"restartForm\" value=\"Eingaben ändern\" />
							</td>
						</tr>
					 
					 </table>
					 ";
			break;
		}
		case 8: {		// [7]: THANK YOU & END
			$document['d_author_email'] = NULL;
			updateDocument($document);
			$title = "Vielen Dank!";
			$text = "Sie haben die Eingabe abgeschlossen, vielen Dank. Ihre Arbeit wird in nächster Zeit in das Korpus aufgenommen.";
			$form = "<td><input type=\"submit\" name=\"endConfirmation\" value=\"Ende\" />";
			break;
		}
	}
	echo "<h1>".$title."</h1>";
	echo "<p>".$text."</p>";
	echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"GET\">
			<input type=\"hidden\" name=\"confId\" value=\"".$_REQUEST['confId']."\" />
			<input type=\"hidden\" name=\"nextStep\" value=\"".($_REQUEST['nextStep']+1)."\" /> 
			".$form."
		  </form>";
}

function checkField($error, $fieldLabel, $fieldName) {
	if(isset($_REQUEST[$fieldName])) {
		if($_REQUEST[$fieldName]=="") {
			return $error." Das Feld \"".$fieldLabel."\" darf nicht leer sein.";
		}
		return $error;	// No error
	}
	return $error." Das Feld \"".$fieldLabel."\" ist nicht gesetzt";
}

function checkNumberField($error, $fieldLabel, $fieldName) {
	$error = checkField($error, $fieldLabel, $fieldName);
	if($error=="") {
		if(!is_numeric($_REQUEST[$fieldName])) {
			return $error." Das Feld \"".$fieldLabel."\" muss eine Nummer sein.";
		}
	}
	return $error;
}

function checkDateField($error, $fieldLabel, $fieldName) {
	$error = checkField($error, $fieldLabel, $fieldName);
	return $error;
}

function checkArrayField($error, $fieldLabel, $fieldName) {
	if(isset($_REQUEST[$fieldName])) {
		$valArr = $_REQUEST[$fieldName];
		if(sizeof($valArr)==0) {
			return $error." Wählen Sie mindestens einen Wert aus \"".$fieldLabel."\".";
		}
		return $error;	// No error
	}
	return $error." Das Feld \"".$fieldLabel."\" ist nicht gesetzt";
}

function setField($document, $fieldName) {
	if(isset($_REQUEST[$fieldName])) {
		$document[$fieldName] = $_REQUEST[$fieldName];
	}
	return $document;
}

function setArrayField($document, $fieldName) {
	if(isset($_REQUEST[$fieldName])) {
		$valArr = $_REQUEST[$fieldName];
		$result = 0;
		foreach($valArr as $val) {
			$result += $val;
		}
		$document[$fieldName] = $result;
	}
	return $document;
}
?>