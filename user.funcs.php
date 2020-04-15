<?php
include_once("db.php");

function getUserObjectById($id) {
	$pdo = getPDO();
	$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE u_id = :uid");
    $result = $statement->execute(array('uid' => $id));
    $user = $statement->fetch();
	return $user;
}

function createUser($name, $email, $password, $role) {
	$passwordHash = password_hash($password, PASSWORD_DEFAULT);
	$pdo = getPDO();
	$statement = $pdo->prepare("INSERT INTO tbl_user (u_name, u_email, u_password, u_rights) 
								VALUES(?, ?, ?, ?)");
	$result = $statement->execute(array($name, $email, $passwordHash, $role));
}

function deleteUser($id) {
	$pdo = getPDO();
	$statement = $pdo->prepare("DELETE FROM tbl_user WHERE u_id=:uid");
	$result = $statement->execute(array('uid' => $id));	
}

function updateUser($id, $name, $role) { 
	$pdo = getPDO();
	$statement = $pdo->prepare("UPDATE tbl_user SET u_id = :uid, u_name = :uname, u_rights = :urole WHERE u_id=:uid");
	$result = $statement->execute(array('uid' => $id, 'uname' => $name, 'urole' => $role));	
}

function updateUserPassword($newPassword, $userid=0) {
	if($userid==0) {
		$userid = $_SESSION['userid'];
	}
	$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
	$pdo = getPDO();
	$statement = $pdo->prepare("UPDATE tbl_user SET u_password= :upass WHERE u_id=:uid");
	$result = $statement->execute(array('upass' => $passwordHash	, 'uid' => $userid));	
}

function passwordCorrect($currentPassword) {
	$user = getUserObjectById($_SESSION['userid']);
	return password_verify($currentPassword, $user['u_password']);
}

function getUserRoleName($roleId) {
	switch($roleId) {
		case 1:
			return "Benutzer*in";
		case 2:
			return "Manager*in";
		case 4:
			return "Supervisor*in";
	}
}

function userHasPermission($permissinNeeded) {
	if($permissinNeeded<=$_SESSION['permissions']) {
		return true;
	}
	return false;
}

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function useremailExists($useremail) {
	$pdo = getPDO();
	$statement = $pdo->prepare("SELECT COUNT(*) as existing FROM tbl_user WHERE u_email = :uemail");
	$result = $statement->execute(array('uemail' => $useremail));
    $user = $statement->fetch();
	if($user['existing']>0) {
		return true;
	}
	else {
		return false;
	}
}

function usernameExists($username) {
	$pdo = getPDO();
	$statement = $pdo->prepare("SELECT COUNT(*) as existing FROM tbl_user WHERE u_name = :uname");
	$result = $statement->execute(array('uname' => $username));
    $user = $statement->fetch();
	if($user['existing']>0) {
		return true;
	}
	else {
		return false;
	}
}

function printUserData($message="") {
    $user = getUserObjectById($_SESSION['userid']);
	
	echo "<h2>Meine Daten</h2>";
	if($message!="") {
		echo "<div class=\"error\">".$message."</div><br/>";
	}
	echo "<table>
			<tr>
				<td>ID</td>
				<td>".$user["u_id"]."</td>
				<td class=\"info\">Die ID wird vom System vergeben.</td>
			</tr>
			<tr>
				<td>Name</td>
				<td>".$user["u_name"]."</td>
				<td class=\"info\">Dieser Name wird angezeigt.</td>
			</tr>
			<tr>
				<td>E-Mail</td>
				<td>".$user["u_email"]."</td>
				<td class=\"info\">Mit dieser Adresse wird kommuniziert.</td>
			</tr>
			<tr>
				<td>Rolle</td>
				<td>".getUserRoleName($user["u_rights"])."</td>
				<td class=\"info\">Dies ist ihre Benutzerrolle.</td>
			</tr>
		  </table>
		  
		  <br/>
		  
		  <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
			  <h2>Passwort ändern</h2>
			  <table>	
				<tr>
					<td>Altes Passwort</td>
					<td><input type=\"password\" name=\"current_pw\" /></td>
					<td class=\"info\"></td>
				</tr>
				<tr>
					<td>Neues Passwort</td>
					<td><input type=\"password\" name=\"new_pw\" /></td>
					<td class=\"info\"></td>
				</tr>
				<tr>
					<td>Neues Passwort wiederholen</td>
					<td><input type=\"password\" name=\"new_pw2\" /></td>
					<td class=\"info\"></td>
				</tr>
				<tr>
					<td></td>
					<td><input type=\"submit\" name=\"confirmNewPw\" value=\"Passwort ändern\"/></td>
					<td class=\"info\"></td>
				</tr>
			  </table>
			  <input type=\"hidden\" name=\"func\" value=\"user\" />
			  <input type=\"hidden\" name=\"subfunc\" value=\"myData\" />
		  </form>";
}

function printUserTable() {
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT * FROM tbl_user ORDER BY u_name ASC');
    if($sth->execute()) {
		if($sth->rowCount() > 0) {
			echo "<div class=\"divScroll\">
			        <table class=\"resultlist\">
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>E-Mail</th>
						<th>Rolle</th>
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
				//print_r($result);
				echo "<tr".$class.">
						<td>".$result->u_id."</td>
						<td>".$result->u_name."</td>
						<td>".$result->u_email."</td>
						<td>".getUserRoleName($result->u_rights)."</td>
						<td>".getUsrOptions($result)."</td>
					  </tr>";
			}
			echo "  </table>
				  </div>";
		} else {
			echo 'there are no result';
		}
	}
	else {
		echo 'there is error';
	}
}

function printEditUser($id, $message="") {
	$user = getUserObjectById($_REQUEST['uid']);
	
	echo "<h2>Benutzer*in editieren</h2>";
	if($message!="") {
		echo "<div class=\"error\">".$message."</div><br/>";
	}
	echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
			<table>
				<tr>
					<td>Name</td>
					<td><input type=\"text\" name=\"u_name\" value=\"".$user['u_name']."\" /></td>
					<td class=\"info\">Anzeigename der Benutzer*in.</td>
				</tr>
				<tr>
					<td>Rolle</td>
					<td>".getUserRoleSelect($user['u_rights'])."</td>
					<td class=\"info\">Benutzer*innen können das Korpus herunterladen, die Manager*innen dürfen Dokumente erstellen, sowie erfassen. Supervisor*innen können zudem Benutzer erstellen und Dokumente löschen.</td>
				</tr>
				<tr>
					<td></td>
					<td><input type=\"submit\" value=\"Änderungen speichern\" /></td>
					<td class=\"info\">Die Benutzer*in wird nicht benachrichtigt.</td>
				</tr>
			</table>
			<input type=\"hidden\" name=\"func\" value=\"user\" />
			<input type=\"hidden\" name=\"subfunc\" value=\"showUsers\" />
			<input type=\"hidden\" name=\"action\" value=\"updateUserConfirm\" />
			<input type=\"hidden\" name=\"u_id\" value=\"".$id."\" />
		  </form>";
}

function printAddUser($request, $message="") {
	if(isset($request['newUserName']))  { $username  = $request['newUserName'];  }  else{ $username="";  }
	if(isset($request['newUserEmail'])) { $useremail = $request['newUserEmail']; }  else{ $useremail=""; }
	if(isset($request['newUserRole']))  { $userrole  = $request['newUserRole'];  }  else{ $userrole="";  }
	
	echo "<h2>Neue Benutzer*in anlegen</h2>";
	if($message!="") {
		echo "<div class=\"error\">".$message."</div><br/>";
	}
	echo "<p>
		    Legen Sie eine Benutzer*in an und wählen Sie ihre Rolle.
			Die Benutzer*in wird per E-Mail benachrichtigt um auf das Konto zuzugreifen.
		  </p>
		  <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
			<table>
				<tr>
					<td>Name</td>
					<td><input type=\"text\" name=\"newUserName\" value=\"".$username."\" /></td>
					<td class=\"info\">Anzeigename der Benutzer*in.</td>
				</tr>
				<tr>
					<td><nobr>E-Mail</nobr></td>
					<td><input type=\"text\" name=\"newUserEmail\" value=\"".$useremail."\" /></td>
					<td class=\"info\">E-Mail Adresse der Benutzer*in.</td>
				</tr>
				<tr>
					<td>Rolle</td>
					<td>".getUserRoleSelect($userrole)."</td>
					<td class=\"info\">Benutzer*innen können das Korpus herunterladen, die Manager*innen dürfen Dokumente erstellen, sowie erfassen. Supervisor*innen können zudem Benutzer erstellen und Dokumente löschen.</td>
				</tr>
				<tr>
					<td></td>
					<td><input type=\"submit\" name=\"newUserConfirm\" value=\"Benutzer*in anlegen\" /></td>
					<td class=\"info\">Die Benutzer*in erhält eine E-Mail um ihr Konto zu aktivieren.</td>
				</tr>
			</table>
			<input type=\"hidden\" name=\"func\" value=\"user\" />
			<input type=\"hidden\" name=\"subfunc\" value=\"newUser\" />
		  </form>";
}

function printUserAdded() {
	echo "<h2>Eine Benutzer*in wurde hinzugefügt.</h2>
		  Die Benutzer*in wird per E-Mail benachrichtigt. <br/>";
}

function getUserRoleSelect($selected) {
	$roles = array(1 => "Benutzer*in", 2=>"Manager*in", 4=>"Supervisor*in");
	$ret = "<select name=\"u_role\" class=\"searchform\">";
	foreach($roles as $key => $role) {
		if($selected==$key) {
			$sel = "selected=\"selected\"";
		}	
		else {
			$sel = "";
		}
		$ret .= "  <option ".$sel." value=\"".$key."\">".$role."</option>";
	}
	$ret .= "</select>";
	return $ret;
}


function getUsrOptions($result) {
	$retVal = "";
	if(userHasPermission($GLOBALS['user_perm_supervisor'])) {
		$retVal .= "<a href=\"?func=user&subfunc=showUsers&action=edit&uid=".$result->u_id."\">Editieren</a> ";
	}
	if(userHasPermission($GLOBALS['user_perm_manager'])) {
		$retVal .= "<a href=\"?func=user&subfunc=showUsers&action=pwreset&uid=".$result->u_id."\">Passwort zurücksetzen</a> ";
	}
	if(userHasPermission($GLOBALS['user_perm_supervisor']) && $result->u_id!=$_SESSION['userid']) {
		$retVal .= "<a href=\"?func=user&subfunc=showUsers&action=delete&uid=".$result->u_id."\">Löschen</a> ";
	}
	return $retVal;							
}
?>