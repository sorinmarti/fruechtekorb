<?php
session_start();
include_once("functions.php");
pageStart($_SERVER['PHP_SELF']);

$errormsg = "";
if(isset($_GET['errormsg'])) {
	$errormsg = "<div class=\"error\">".$_GET['errormsg']."</div><br/>";
}

echo "<table>
		<tr>
			<td>
				<img src=\"images/index.png\" />
			</td>
			<td valign=\"top\" width=\"100%\">
				".$errormsg."
				<b>Willkommen bei ".$GLOBALS['project_name']."!</b><br/><br/>
				".$GLOBALS['project_name']." ist ein Textkorpus mit studentischen Dokumenten der germanistischen Linguistik der Universität Basel. Das Korpus enthält (Pro-)Seminararbeiten, Handouts, Essays und ähnliche Dokumente von Student*innen.<br/><br/>
				Das Korpus wird laufend ausgebaut. Momentan enthält es <b>X</b> Texte mit einer Gesamtzahl von <b>Y</b> Tokens.
				<br/>
				
			</td>
		</tr>
	  </table>";
	  
pageEnd();
?>