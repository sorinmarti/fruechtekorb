<?php
include_once("functions.php");
include_once("document.funcs.php");
include_once("user.funcs.php");
include_once("stats.funcs.php");
include_once("mail.functions.php");

session_start();
if(!isset($_SESSION['userid'])) {
    header("Location: intern.php?errormsg=Bitte zuerst einloggen!");
}
else {
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
		// last request was more than 30 minutes ago
		session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();   // destroy session data in storage
		header("Location: intern.php?errormsg=Session abgelaufen.");
	}
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
}
if(isset($_REQUEST['func']) && $_REQUEST['func']=="logout") {
	header("Location: logout.php");
}

// Check the function
$includeStr = "";
if(!isset($_REQUEST['func'])) {
	$func = "show";
	$subfunc = "";
}
else {
	$func = $_REQUEST['func'];
	if(!isset($_REQUEST['subfunc'])) {
		$subfunc = "";
	}
	else {
		$subfunc = $_REQUEST['subfunc'];
	}
}

if($func=="stats") {
	switch($subfunc) {
		case 'stats_2':
			$includeStr = "pieChart";
			break;
		case 'stats_3':
			$includeStr = "wordCloud";
			break;
	}
}
if($func=="doc2" && $subfunc=="read") {
	$includeStr = "markdown";
}

pageStart("/korpus/intern.php", true, $includeStr);
$subnav = array("doc1"  => "Dokumente anzeigen",
				"doc2"   => "Dokumente verwalten",
				"stats" => "Statistik",
				"user"  => "Benutzer",
				"logout"=> "Logout (".$_SESSION['username'].")");



echo "<h2>Interner Bereich</h2>";

echo "<table class=\"subnav\">
		<tr>";
foreach($subnav as $funcn => $text ) {
	$snclass = "";
	if($func==$funcn) {
		$snclass = "class=\"subnav_sel\" ";
	}
	echo "<td ".$snclass."width=\"".(100/sizeof($subnav))."%\"><a href=\"?func=".$funcn."\">".$text."</a></td>\n";
}

echo "	</tr>
	  </table>";


// SHOW DOCUMENTS
if($func=="doc1") {
	if($subfunc=="") {
		$subfunc = "showActive";
	}
	$subsubfunc = array("showAll"      => array("Alle Dokumente", $GLOBALS['user_perm_manager']),
						"showActive"   => array("Aktive Dokumente", $GLOBALS['user_perm_user']),
						"showPending"  => array("Offene Dokumente", $GLOBALS['user_perm_manager']),
						"showRejected" => array("Abgelehnte Dokumente", $GLOBALS['user_perm_supervisor'])
						);
	echo createSubSubNav($subsubfunc, $func, $subfunc);
	if($subfunc=="showAll") {
		showDocumentTable();
	}
	else if($subfunc=="showActive") {
		showDocumentTable("active");
	}
	else if($subfunc=="showPending") {
		showDocumentTable("pending");
	}
	else if($subfunc=="showRejected") {
		showDocumentTable("rejected");
	}
}
//////////////////////////////////////////////////////////////////////////
// MANAGE DOCUMENTS
else if($func=="doc2") {
	$subsubfunc = array("add"     => array("Neues Dokument", $GLOBALS['user_perm_supervisor']));
	echo createSubSubNav($subsubfunc, $func, $subfunc);
	
	/** TRY TO ADD DOCUMENT **/
	$errorMessage = "";
	if(isset($_REQUEST['addDocument_1'])) {
		$err = false;
		if($_REQUEST['d_title']=="") {
			$errorMessage = "Der Titel ist leer.";
			$err = true;
		}
		if($_REQUEST['d_date']=="") {
			$errorMessage = "Das Abgabedatum ist leer";
			$err = true;
		}
		if($_REQUEST['d_author_email']=="") {
			$errorMessage = "Die E-Mail ist leer.";
			$err = true;
		}
		
		if(!$err) {
			$subfunc = "add2";
			
		}
		else {
			$errorMessage = "<div class=\"error\">".$errorMessage."</div><br/>";
		}
		
	}
	/** ADD DOCUMENT **/
	if(isset($_REQUEST['addDocument_2'])) {
		// generate token
		$token = md5(uniqid($_REQUEST['d_title'].$_REQUEST['d_date'].$_REQUEST['d_author_email'], true));
		
		// Save to database
		createDocument($token, $_REQUEST['d_title'], $_REQUEST['d_date'], $_REQUEST['d_document_type'], $_REQUEST['d_author_email']);
		
		// Send mail
		sendMail($_REQUEST['d_author_email'], "Dürfen wir Ihre Arbeit verwenden?", "Wenn ja: bitte: http://localhost/korpus/confirm.php?confId=".$token."\n Vielen Dank!");
		$subfunc = "add3";
	}
	
	if($subfunc=="add") {
		showDocumentAdd($_REQUEST, $errorMessage);
	}
	if($subfunc=="add2") {
		showDocumentAddConfirm($_REQUEST, $errorMessage);
	}
	if($subfunc=="add3") {
		echo "<h2>Dokument hinzugefügt</h2>
				<p>Das Dokument wurde in die Datenbank aufgenommen und die Autor*in benachrichtigt.";
	}
	
	/** DELETE DOCUMENT **/
	if($subfunc=="delete") {
		if(isset($_REQUEST['doc'])) {
			if(isset($_REQUEST['deleteConfirm'])) {
				deleteDocument($_REQUEST['doc']);
				echo "<h2>Dokument gelöscht</h2>
					  <p>Das Dokument wurde aus der Datenbank entdernt.</p>";
				
			}
			else {
				echo "<h2>Dokument löschen</h2>
					  <p>Wollen Sie das Dokument wirklich löschen?</p>
					  <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
						<input type=\"submit\" name=\"deleteConfirm\" value=\"Ja, Dokument löschen\" />
						<input type=\"hidden\" name=\"func\" value=\"doc2\" />
						<input type=\"hidden\" name=\"subfunc\" value=\"delete\" />
						<input type=\"hidden\" name=\"doc\" value=\"".$_REQUEST['doc']."\" />
					  </form>";
			}
		}
	}
	/*READ TEXT OF DOCUMENT */
	if($subfunc=="read") {
		if(isset($_REQUEST['doc'])) {
			showReadDocument($_REQUEST['doc']);
		}
	}
	/* ACTIVATE DOCUMENT */
	if($subfunc=="activate") {
		if(isset($_REQUEST['doc'])) {
			echo "<h2>Dokument freigeben</h2>
					  <p>Wollen Sie das Dokument freigeben?</p>
					  <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
						<input type=\"submit\" value=\"Ja, Dokument freigeben\" />
						<input type=\"hidden\" name=\"func\" value=\"doc2\" />
						<input type=\"hidden\" name=\"subfunc\" value=\"activateConfirm\" />
						<input type=\"hidden\" name=\"doc\" value=\"".$_REQUEST['doc']."\" />
					  </form>";
		}
	}
	if($subfunc=="activateConfirm") {
		activateDocument($_REQUEST['doc']);
	}
	/* EDIT DOCUMENT */
	if($subfunc=="edit") {
		$complete = true;
		$message = "";
		if(isset($_REQUEST['action']) && $_REQUEST['action']=="editConfirm") {
			// TODO Check for:
			// Title
			// Daten
			// Typ
			// Edittool
			// Doc language
			// Tags
			// E-Mail Autor
			// Muttersprache
			// Alter
			// Gender
			// Universität
			// Semester
			$document = getDocumentObjectById($_REQUEST['doc']);
			$document['d_title']              = $_REQUEST['d_title'];
			$document['d_date']               = $_REQUEST['d_date'];
			$document['d_document_tool']      = $_REQUEST['d_document_tool'];
			$document['d_document_lang']      = $_REQUEST['d_document_lang'];
			$document['d_document_type']      = $_REQUEST['d_document_type'];
			$document['d_document_tags']      = $_REQUEST['d_document_tags'];
			$document['d_author_email']       = $_REQUEST['d_author_email'];
			$document['d_author_native_lang'] = $_REQUEST['d_author_native_lang'];
			$document['d_author_gender']      = $_REQUEST['d_author_gender'];
			$document['d_author_uni']         = $_REQUEST['d_author_uni'];
			$document['d_author_semesters']   = $_REQUEST['d_author_semesters'];
			$document['d_author_age']         = $_REQUEST['d_author_age'];
			updateDocument($document);
			echo "<h2>Benutzer*in gespeichert.</h2>
				  <p>Die Änderungen wurden gespeichert.</p>";
		}
		else {
			showDocumentEdit($_REQUEST['doc']);
		}
	}
	
}
//////////////////////////////////////////////////////////////////////////
// STATS
else if($func=="stats"){
	if($subfunc=="") {
		$subfunc = "stats_1";
	}
	$subsubfunc = array("stats_1" => array("Korpusumfang", $GLOBALS['user_perm_user']),
						"stats_2" => array("Metadaten", $GLOBALS['user_perm_manager']),
						"stats_3" => array("Wordcloud", $GLOBALS['user_perm_manager']));
	echo createSubSubNav($subsubfunc, $func, $subfunc);
	if($subfunc=="stats_1") {
		printCorpusStats();
	}
	else if($subfunc=="stats_2") {
		printPieCharts();
	}
	else if($subfunc=="stats_3") {
		printWordcloud();
	}
}
//////////////////////////////////////////////////////////////////////////
//USER
else if($func=="user"){
	if($subfunc=="") {
		$subfunc = "myData";
	}
	$subsubfunc = array("myData"    => array("Meine Daten", $GLOBALS['user_perm_user']),
						"showUsers" => array("Alle Benutzer*innen", $GLOBALS['user_perm_manager']),
						"newUser"  => array("Neue Benutzer*in", $GLOBALS['user_perm_supervisor']));
	echo createSubSubNav($subsubfunc, $func, $subfunc);
	// My User Data
	if($subfunc=="myData") {
		if(isset($_REQUEST['confirmNewPw'])) {
			$complete = true;
			$message = "";
			// Check if complete
			if($_REQUEST['current_pw']=="") {
				$complete = false;
				$message = "Geben Sie Ihr bisheriges Passwort ein.";
			}
			else {
				if(!passwordCorrect($_REQUEST['current_pw'])) {
					$complete = false;
					$message = "Ihr bisheriges Passwort ist falsch.";
				}
			}
			if($_REQUEST['new_pw']=="") {
				$complete = false;
				$message = "Geben Sie ein neues Passwort ein.";
			}
			else {
				if($_REQUEST['new_pw2']=="") {
					$complete = false;
					$message = "Wiederholen Sie ihr neues Passwort.";
				}
				else {
					// Both new pw fields are NOT EMPTY
					if($_REQUEST['new_pw']!=$_REQUEST['new_pw2']) {
						$complete = false;
						$message = "Die neuen Passwörter stimmen nicht überein.";
					}
				}
			}
			if($complete) {
				// Actually change the password
				updateUserPassword($_REQUEST['new_pw']);
				echo "<h2>Passwort geändert</h2>
					  <p>Ihr Passwort wurde geändert.</p>";
			}
			else {
				printUserData($message);
			}
		}
		else {
			printUserData();
		}
	}
	// Show user Table
	else if($subfunc=="showUsers") {
		// An action is set
		if(isset($_REQUEST['action'])) {
			// Action: delete
			if($_REQUEST['action']=="delete"){
				$user = getUserObjectById($_REQUEST['uid']);
				echo "<h2>Benutzer*in löschen</h2>
					  <p>Wollen Sie die Benutzer*in <b>".$user['u_name']."</b> wirklich löschen?</p><br/>
					  <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
						<input type=\"submit\" value=\"Benutzer*in löschen\" />
						<input type=\"hidden\" name=\"func\" value=\"user\" />
						<input type=\"hidden\" name=\"subfunc\" value=\"showUsers\" />
						<input type=\"hidden\" name=\"action\" value=\"deleteUserConfirm\" />
						<input type=\"hidden\" name=\"uid\" value=\"".$_REQUEST['uid']."\" />
					  </form>";
			}
			if($_REQUEST['action']=="deleteUserConfirm"){
				deleteUser($_REQUEST['uid']);
				echo "<h2>Benutzer*in gelöscht</h2>
					  <p>Die Benutzer*in wurde gelöscht.</p>";
			}
			
			if($_REQUEST['action']=="edit"){
				printEditUser($_REQUEST['uid']);
			}
			if($_REQUEST['action']=="updateUserConfirm"){
				if($_REQUEST['u_name']=="") {
					$message = "Der Benutzer*innen-Name darf nicht leer sein.";
					printEditUser($_REQUEST['uid'], $message);
				}
				else {
					updateUser($_REQUEST['u_id'], $_REQUEST['u_name'], $_REQUEST['u_role']);
				}
			}
			
			if($_REQUEST['action']=="pwreset"){
				$user = getUserObjectById($_REQUEST['uid']);
				echo "<h2>Passwort zurücksetzen</h2>
					  <p>Wollen Sie das Passwort der Benutzer*in <b>".$user['u_name']."</b> wirklich zurücksetzen? Die Benutzer*in erhält eine E-Mail mit dem neuen Passwort.</p><br/>
					  <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
						<input type=\"submit\" value=\"Passwort zurücksetzen\" />
						<input type=\"hidden\" name=\"func\" value=\"user\" />
						<input type=\"hidden\" name=\"subfunc\" value=\"showUsers\" />
						<input type=\"hidden\" name=\"action\" value=\"resetUserConfirm\" />
						<input type=\"hidden\" name=\"uid\" value=\"".$_REQUEST['uid']."\" />
					  </form>";
			}
			if($_REQUEST['action']=="resetUserConfirm"){
				$password = randomPassword();
				updateUserPassword($password, $_REQUEST['uid']);
				$user = getUserObjectById($_REQUEST['uid']);
				sendPasswordUpdateMail($user['u_name'], $email, $password);
				echo "<h2>Passwort zurückgesetzt</h2>
					  <p>Das Passwort wurde neu generiert und die Benutzer*in benachrichtigt.</p>";
			}
		}
		else {
			printUserTable();
		}
	}
	else if($subfunc=="newUser") {
		if(isset($_REQUEST['newUserConfirm'])) {
			$complete = true;
			$message = "";
			
			// Check if complete
			if($_REQUEST['newUserEmail']=="") {
				$complete = false;
				$message = "Die E-Mail-Adresse darf nicht leer sein.";
			}
			else {
				if(useremailExists($_REQUEST['newUserEmail'])) {
					$complete = false;
					$message = "Die E-Mail-Adresse existiert bereits in der Datenbank.";
				}
			}
			if($_REQUEST['newUserName']=="") {
				$complete = false;
				$message = "Der Name darf nicht leer sein.";
			}
			else {
				if(usernameExists($_REQUEST['newUserName'])) {
					$complete = false;
					$message = "Der Name existiert bereits in der Datenbank.";
				}
			}
			
			// if complete: Add user and print success message
			if($complete) {
				// Add USER:
				// -create a password
				$password = randomPassword();
				// -add the entry to the database
				createUser($_REQUEST['newUserName'], $_REQUEST['newUserEmail'], $password, $_REQUEST['u_role']);
				// -notify the user via email
				sendNewUserMail($_REQUEST['newUserName'], $_REQUEST['newUserEmail'], $password);
				
				printUserAdded();
			}
			// if not complete: show form again
			else {
				printAddUser($_REQUEST, $message);
			}
		}
		else {
			printAddUser($_REQUEST);
		}
	}
	
}
pageEnd();

function createSubSubNav($subsubfunc, $funcn, $selected="") {
	$divider =  "&nbsp;|&nbsp;";
	$ret = "Bitte auswählen: ";
	$numOptions = 0;
	foreach($subsubfunc as $key => $func) {
		$sel = "class=\"subsubnav\" ";
		if($selected==$key) {
			$sel = "class=\"subsubnav_sel\" ";
		}
		if($func[1]<=$_SESSION['permissions']) {
			$ret .= "<a href=\"?func=".$funcn."&subfunc=".$key."\" $sel>".$func[0]."</a>".$divider;
			$numOptions++;
		}
	}
	if($numOptions>0) {
		$ret = substr($ret, 0, strlen($ret)-strlen($divider) );
	}
	else {
		return "<i>(Sie haben keine weiteren Optionen.)</i>";
	}
	$ret .= "<br/><br/>";
	return $ret;
}
?>