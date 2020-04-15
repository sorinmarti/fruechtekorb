<?php
$GLOBALS["db_host"] = "localhost";
$GLOBALS["db_name"] = "korpus";
$GLOBALS["db_user"] = "root";
$GLOBALS["db_pw"]   = "";

$GLOBALS['site_email'] = "sorin.marti@unibas.ch";
$GLOBALS['contact_email'] = "sorin.marti@unibas.ch";

$GLOBALS['project_name'] = "FrueCHTeKorB";	// Frei recherchier- und erweiterbares CH-TextKorpus Basel
//$GLOBALS['project_name'] = "Fichtenlaub";	// Frei interpretierbares CH-Textkorpus der linguistischen Abteilung der Universität Basel
//$GLOBALS['project_name'] = "Tiefblau";	// Textkorpus interner Eingaben für die basler linguistische Abteilung d. Universität
//$GLOBALS['project_name'] = "??";	//

$GLOBALS['user_perm_user']       = 1;
$GLOBALS['user_perm_manager']    = 2;
$GLOBALS['user_perm_supervisor'] = 4;

$GLOBALS['login_page'] = "http://www.sorinmarti.com/korpus/intern.php";

$GLOBALS["subjectLabels"] = array(1     => "Deutsche Philologie", 
								  2     => "Geschichte", 
								  4     => "Englisch",
								  8     => "Französistik", 
								  16    => "Medienwissenschaft",
								  32    => "Geschlechterforschung",
								  64    => "Hispanistik",
								  128   => "Italianistik",
								  256   => "Kulturanthropologie",
								  512   => "Kunstgeschichte",
								  1024  => "Nordistik",
								  2048  => "Soziologie",
								  4096  => "Religionswissenschaft",
								  8192  => "Philosophie",
								  16348 => "Politikwissenschaft",
								  32768 => "Nicht aufgeführt");
?>