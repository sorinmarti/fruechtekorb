<?php
session_start();
if(isset($_SESSION['userid'])) {
    header("Location: manage.php");
}
include_once("functions.php");

$pdo = new PDO('mysql:host=localhost;dbname=korpus', 'root', '');



if(isset($_GET['login'])) {
    $email = $_REQUEST['email'];
    $passwort = $_REQUEST['passw'];
	
    $statement = $pdo->prepare("SELECT * FROM tbl_user WHERE u_email = :email");
    $result = $statement->execute(array('email' => $email));
    $user = $statement->fetch();
    
    //Überprüfung des Passworts
    if ($user !== false && password_verify($passwort, $user['u_password'])) {
        $_SESSION['userid'] = $user['u_id'];
		$_SESSION['username'] = $user['u_name'];
		$_SESSION['useremail'] = $user['u_email'];
		$_SESSION['permissions'] = $user['u_rights'];
       header("Location: manage.php?func=doc1");
    } else {
        $errorMessage = "E-Mail oder Passwort war ungültig<br>";
    }
    
}

if(isset($_GET['errormsg'])) {
	$errorMessage = $_GET['errormsg'];
}

pageStart($_SERVER['PHP_SELF']);

echo "<table width=\"90%\">
		<tr>
			<td>
				<img src=\"images/intern.png\" />
			</td>
			<td valign=\"top\">";
			if(isset($errorMessage)) {
				echo "<div class=\"error\">".$errorMessage."</div><br/>";
			}
echo "			<b>Interner Bereich</b><br/><br/>
				Loggen Sie sich ein, um die internen Funktionen zu sehen. Wenn Sie Zugang zum Korpus wünschen, <a href=\"kontakt.php\">kontaktieren</a> Sie uns..<br/><br/>
				Login:<br/>
				<form action=\"?login=1\" method=\"post\">
					<table>
					  <tr>
						<td>E-Mail:</td>
						<td><input type=\"text\" name=\"email\"/></td>
					  </tr>
					  <tr>
						<td>Passwort:</td>
						<td><input type=\"password\"  name=\"passw\"/></td>
					  </tr>
					  <tr>
						<td></td>
						<td><input type=\"submit\" value=\"Login\" /></td>
					  </tr>
					</table>
				</form>
				<br/>
				
			</td>
		</tr>
	  </table>";

pageEnd();