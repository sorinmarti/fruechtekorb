<?php
include_once("db.php");
include_once("user.funcs.php");

function getDocumentObjectById($id) {
	$pdo = getPDO();
	$statement = $pdo->prepare("SELECT * FROM tbl_document WHERE d_id = :did");
    $result = $statement->execute(array('did' => $id));
    $document = $statement->fetch();
	return $document;
}

function getDocumentObjectByToken($token) {
	$pdo = getPDO();
	$statement = $pdo->prepare("SELECT * FROM tbl_document WHERE d_token = :token");
    $result = $statement->execute(array('token' => $token));
    $document = $statement->fetch();
	return $document;
}

function createDocument($token, $title, $date, $type, $email) {
		$pdo = getPDO();
		$statement = $pdo->prepare("INSERT INTO tbl_document (d_token, d_step, d_title, d_date, d_entry_creation_date, d_entry_creation_user, d_document_type, d_author_email) 
									VALUES(?, ?, ?, ?, NOW(), ?, ?, ?)");
		$result = $statement->execute(array($token, 0, $title, $date, $_SESSION['userid'], $type, $email));
		
}

function deleteDocument($id) {
	$pdo = getPDO();
	$statement = $pdo->prepare("DELETE FROM tbl_document WHERE d_id = :did");
    $result = $statement->execute(array('did' => $id));
}

function activateDocument($id) {
	$pdo = getPDO();
	$statement = $pdo->prepare("UPDATE tbl_document SET d_step = 50 WHERE d_id = :did");
    $result = $statement->execute(array('did' => $id));
}

function updateDocument($document) {
	$pdo = getPDO();
	$statement = $pdo->prepare("UPDATE tbl_document 
								SET d_step  = :dstep, 
								d_title = :dtitle, 
								d_date  = :ddate,
								d_document_type = :dtype,
								d_document_lang = :dlang,
								d_document_tool = :dtool,
								d_document_tags = :dtags,
								d_author_age = :aage,
								d_author_gender = :agender,
								d_author_uni = :auni,
								d_author_bama = :abama,
								d_author_semesters = :asems,
								d_author_subjects = :asubs,
								d_author_native_lang = :alang,
								d_author_email = :aemail 
								WHERE d_id  = :did");
    $result = $statement->execute(array('did'     => $document['d_id'],
										'dstep'   => $document['d_step'],
										'dtitle'  => $document['d_title'],
										'ddate'   => $document['d_date'],
										'dtype'   => $document['d_document_type'],
										'dlang'   => $document['d_document_lang'],
										'dtool'   => $document['d_document_tool'],
										'dtags'   => $document['d_document_tags'],
										'aage'    => $document['d_author_age'],
										'agender' => $document['d_author_gender'],
										'auni'    => $document['d_author_uni'],
										'abama'   => $document['d_author_bama'],
										'asems'   => $document['d_author_semesters'],
										'asubs'   => $document['d_author_subjects'],
										'alang'   => $document['d_author_native_lang'],
										'aemail'  => $document['d_author_email']));
}

function showDocumentAdd($request, $errorMessage) {
	if(!isset($request['d_title'])) 		{	$request['d_title'] = "";			}
	if(!isset($request['d_date']))  		{	$request['d_date'] = "";			}
	if(!isset($request['d_document_type'])) {	$request['d_document_type'] = "";	}
	if(!isset($request['d_author_email'])) 	{	$request['d_author_email'] = "";	}
	
	echo "<h2>Dokument hinzufügen</h2>";
	echo $errorMessage;
	echo "<form>
		  <table>
			<tr>
				<td>Titel</td>
				<td><input type=\"text\" name=\"d_title\"  value=\"".$request['d_title']."\" size=\"45\" /></td>
				<td class=\"info\">Titel der abgegebenen Arbeit.</td>
			</tr>
			<tr>
				<td>Abgabedatum</td>
				<td><input type=\"text\" name=\"d_date\"  value=\"".$request['d_date']."\" />	( Jahr-Monat-Tag: ".date("Y-m-d")." )</td>
				<td class=\"info\">Auf dem Dokument angegebenes Abgabedatum.</td>
			</tr>
			<tr>
				<td>Dokumenttyp</td>
				<td>".getDocumentTypeSelect("d_document_type", $request['d_document_type'])."</td>
				<td class=\"info\"></td>
			</tr>
			<tr>
				<td>E-Mail Autor</td>
				<td><input type=\"text\" name=\"d_author_email\" value=\"".$request['d_author_email']."\" size=\"45\" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input type=\"submit\" name=\"addDocument_1\" value=\"Weiter\" /></td>
				<td class=\"info\"></td>
			</tr>
		  </table>
			<input type=\"hidden\" name=\"func\" value=\"doc2\" />
			<input type=\"hidden\" name=\"subfunc\" value=\"add\" />
		  </form>";
}

function showDocumentAddConfirm($request, $errorMessage) {
	echo "<h2>Dokument hinzufügen</h2>
		  <p>Wählen Sie den Button um den Eintrag in der Datenbank anzulegen und der Benutzer*in eine E-Mail für die Zustimmung und die Eingabe der Metadaten zu senden.</p>";
	echo "<table>
			<tr>
				<td>Titel</td>
				<td>".$request['d_title']."</td>
			</tr>
			<tr>
				<td>Abgabedatum</td>
				<td>".$request['d_date']."</td>
			</tr>
			<tr>
				<td>Dokumenttyp</td>
				<td>".getDocumentTypeLabel($request['d_document_type'])."</td>
			</tr>
			<tr>
				<td>E-Mail Autor</td>
				<td>".$request['d_author_email']."</td>
			</tr>
		  </table>
		  <form>
			<input type=\"hidden\" name=\"d_title\" value=\"".$request['d_title']."\" />
			<input type=\"hidden\" name=\"d_date\" value=\"".$request['d_date']."\" />
			<input type=\"hidden\" name=\"d_document_type\" value=\"".$request['d_document_type']."\" />
			<input type=\"hidden\" name=\"d_author_email\" value=\"".$request['d_author_email']."\" />
			
			<input type=\"submit\" name=\"addDocument_2\" value=\"E-Mail senden und Dokument anlegen\" />
			<input type=\"hidden\" name=\"func\" value=\"doc2\" />
			<input type=\"hidden\" name=\"subfunc\" value=\"add\" />
		  </form>";
	
}

function showDocumentEdit($id, $message="") {
	$document = getDocumentObjectById($id);
	// Missing: subjects, bama
	
	echo "<h2>Dokument editieren</h2>
		  <form>
		  <table>
			<tr>
				<td>Titel</td>
				<td><input type=\"text\" name=\"d_title\" value=\"".$document['d_title']."\" class=\"searchform\" /></td>
			</tr>
			<tr>
				<td>Abgabedatum</td>
				<td><input type=\"\" name=\"d_date\" value=\"".$document['d_date']."\" class=\"searchform\" /></td>
			</tr>
			<tr>
				<td>Dokumenttyp</td>
				<td>".getDocumentTypeSelect("d_document_type", $document['d_document_type'], "searchform")."</td>
			</tr>
			<tr>
				<td>Erstell-Tool</td>
				<td>".getEditToolSelect("d_document_tool", $document['d_document_tool'], "searchform")."</td>
			</tr>
			<tr>
				<td>Sprache</td>
				<td>".getLanguageSelect("d_document_lang", $document['d_document_lang'], "searchform")."</td>
			</tr>
			<tr>
				<td valign=\"top\">Schlagworte</td>
				<td><textarea name=\"d_document_tags\" class=\"searchform\" rows=\"4\">".$document['d_document_tags']."</textarea></td>
			</tr>
			<tr>
				<td>E-Mail Autor</td>
				<td><input type=\"text\" name=\"d_author_email\" value=\"".$document['d_author_email']."\" class=\"searchform\" /></td>
			</tr>
			<tr>
				<td>Muttersprache</td>
				<td>".getLanguageSelect("d_author_native_lang", $document['d_author_native_lang'], "searchform")."</td>
			</tr>
			<tr>
				<td>Alter</td>
				<td><input type=\"text\" name=\"d_author_age\" value=\"".$document['d_author_age']."\" class=\"searchform\" /></td>
			</tr>
			<tr>
				<td>Gender</td>
				<td>".getGenderSelect("d_author_gender", $document['d_author_gender'], "searchform")."</td>
			</tr>
			<tr>
				<td>Universität</td>
				<td>".getUniversitySelect("d_author_uni", $document['d_author_uni'], "searchform")."</td>
			</tr>
			<tr>
				<td>Semester</td>
				<td><input type=\"text\" name=\"d_author_semesters\" value=\"".$document['d_author_semesters']."\" class=\"searchform\" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input type=\"submit\" value=\"Angaben speichern\" class=\"searchform\" /></td>
			</tr>
		  </table>
		  <input type=\"hidden\" name=\"func\" value=\"doc2\"/>
			<input type=\"hidden\" name=\"subfunc\" value=\"edit\"/>
			<input type=\"hidden\" name=\"action\" value=\"editConfirm\"/>
			<input type=\"hidden\" name=\"doc\" value=\"".$id."\"/>
		  </form>";
	
}

function showReadDocument($id) {
	$document = getDocumentObjectById($id);
	echo "<h2>Dokument einlesen</h2>
		  <p>Sie lesen das Dokument \"".$document['d_title']."\" ein.
		  <form>
			<table>
				<tr>
					<td>Laden Sie die PDF-Datei hoch:</td>
					<td><input type=\"file\" id=\"myPDF\" name=\"pdfFile\" /></td>
					<td class=\"info\">Es muss sich um eine PDF-Datei handeln. Maximale Grösse: 50 MB</td>
				</tr>
				<tr>
					<td colspan=\"2\"><br/>Kopieren und formatieren Sie den Text:</td>
					<td class=\"info\"> </td>
				</tr>
				<tr>
					<td colspan=\"2\">
						<textarea id=\"demo1\"></textarea>
						<script>
							new SimpleMDE({
								element: document.getElementById(\"demo1\"),
								spellChecker: false,
							});
						</script>
					</td>
					<td class=\"info\">
						<b><u>Formatierungshilfe</u></b><br/>
						<br/>
						<u>Überschriften:</u><br/>
						# Kapitel-Überschrift 1 <br/>
						## Kapitel-Überschrift 2 <br/>
						### Kapitel-Überschrift 3  <br/><br/>
						<u>Auszeichnungen:</u><br/>
						**<b>fett</b>**<br/>
						*<i>kursiv</i>*<br/><br/>
						<u>Aufzählungen:</u><br/>
						- Ohne Nummerierung <br/>
						1. Oder eine Liste <br/>
						2. mit Nummerierung<br/><br/>
						<u>Optionen:</u><br/>
						Symbol \"Auge\": Voransicht des formatierten Textes<br/>
						Symbol \"Geteiltes Fenster\": Editieren mit Voransicht<br/>
						Symbol \"Vier Pfeile\": Vollbildmodus<br/><br/>
						(Es werden nur Fliesstext mit Überschriften erfasst.<br/> 
						 Titelblatt, Inhaltsverzeichnis, Fussnoten und Anhang nicht erfassen.<br/>
						 Bitte sehen Sie sich die Guidelines als <a href=\"\">PDF</a> an.)
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type=\"submit\" name=\"saveDocumentText\" value=\"Text und PDF Speichern\" /></td>
				</tr>
			</table>
			<input type=\"hidden\" name=\"func\" value=\"doc2\"/>
			<input type=\"hidden\" name=\"subfunc\" value=\"read\"/>
			<input type=\"hidden\" name=\"doc\" value=\"".$id."\"/>
		  </form>";
	// PDF
	// TXT
	//Text Area
}

function showDocumentTable($filter="") {
	$pdo = getPDO();
	$sql = "SELECT  d.*, dt.dt_name, dt.dt_shortname, 
						 et.e_name, et.e_shortname, 
						 g.g_name, g.g_shortname, 
						 l.l_name as ld_name, l.l_shortname as ld_shortname, 
                         l2.l_name as la_name, l2.l_shortname as la_shortname, 
						 u.u_name, un.u_name as un_name, un.u_shortname as un_shortname  
			FROM tbl_document d
			LEFT JOIN tbl_document_type dt 
			ON dt.dt_id=d.d_document_type 
			LEFT JOIN tbl_edittool et 
			ON et.e_id=d.d_document_tool 
			LEFT JOIN tbl_gender g 
			ON g.g_id=d.d_author_gender 
			LEFT JOIN tbl_language l 
			ON l.l_id=d.d_document_lang 
            LEFT JOIN tbl_language l2 
			ON l2.l_id=d.d_author_native_lang 
			LEFT JOIN tbl_university un
			ON un.u_id=d.d_author_uni 
			LEFT JOIN tbl_user u 
			ON u.u_id=d.d_entry_creation_user 
			%s
			ORDER BY d.d_date DESC";
	if($filter=="") {
		$sth = $pdo->prepare(sprintf($sql, ""));
	}
	else if($filter=="active") {
		$sth = $pdo->prepare(sprintf($sql, "WHERE d_step=50 "));
	}
	else if($filter=="pending") {
		$sth = $pdo->prepare(sprintf($sql, "WHERE d_step>7 AND d_step<50 "));
	}
	else if($filter=="rejected") {
		$sth = $pdo->prepare(sprintf($sql, "WHERE d_step=99 "));
	}
    if($sth->execute()) {
		if($sth->rowCount() > 0) {
			echo "<div class=\"divScroll\">
			        <table class=\"resultlist\">
					<tr>
						<th>ID</th>
						<th>Jahr</th>
						<th>Titel</th>
						<th>Dokument</th>
						<th>Autor</th>
						<th>Status</th>
						<th>Optionen</th>
					</tr>";
			$line = 1;
			while($result = $sth->fetchObject()) {
				$line++;
				if($line%2==0) {
					$class = " class=\"evenline\"";
				}
				else {
					$class = "";
				}
				
				$bgcolor = getStepColor($result->d_step);
				
				//print_r($result);
				echo "<tr".$class.">
						<td bgcolor=\"".$bgcolor."\">
							<div class=\"tooltip\">
								".$result->d_id."
								<span class=\"tooltiptext\">
									Erstellt am: ".$result->d_entry_creation_date."<br/>
									Erstellt von: ".$result->u_name."
								</span>
							</div>
						</td>
						<td>".explode("-",$result->d_date)[0]."</td>
						<td>".$result->d_title."</td>
						<td>".$result->dt_shortname.", ".$result->ld_shortname.", ".$result->e_shortname."</td>
						<td>".$result->g_shortname.", ".$result->d_author_age."j, ".$result->la_shortname.", ".$result->un_shortname.", ".$result->d_author_semesters.". S. </td>
						<td>".getStepLabel($result->d_step)."</td>
						<td>".getDocOptions($result)."</td>
					  </tr>";
			}
			echo "  </table>
				  </div>";
		} else {
			echo 'Die Tabelle ist leer.';
		}
	}
	else {
		echo 'there is an error';
	}
}

function getDocOptions($result) {
	$retVal = "";
	if(userHasPermission($GLOBALS['user_perm_manager'])) {
		$retVal .= "<a href=\"?func=doc2&subfunc=edit&doc=".$result->d_id."\">Editieren</a> ";
	}
	if(userHasPermission($GLOBALS['user_perm_manager'])  && getStepX($result->d_step)==1) {
		$retVal .= "<a href=\"?func=doc2&subfunc=read&doc=".$result->d_id."\">Einlesen</a> ";
	}
	if(userHasPermission($GLOBALS['user_perm_supervisor']) && getStepX($result->d_step)==2) {
		$retVal .= "<a href=\"?func=doc2&subfunc=activate&doc=".$result->d_id."\">Freigeben</a> ";
	}
	if(userHasPermission($GLOBALS['user_perm_supervisor'])) {
		$retVal .= "<a href=\"?func=doc2&subfunc=delete&doc=".$result->d_id."\">Löschen</a> ";
	}
	return $retVal;
}
?>