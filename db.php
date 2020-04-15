<?php
include_once("globals.php");

function getPDO() {
	$db = new PDO("mysql:host=".$GLOBALS["db_host"].";dbname=".$GLOBALS["db_name"], $GLOBALS["db_user"], $GLOBALS["db_pw"]);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	return $db;
}

function getStepX($step) {
	if($step>=0 && $step<8) {		// Daten werden gesammelt
		return 0;
	}
	if($step==8 || $step==9) {		// Sammelvorgang abgeschlossen
		return 1;
	}
	if($step==20) {					// Text eingelesen
		return 2;
	}
	if($step==50) {					// Im Korpus aktiv
		return 3;
	}
	if($step==99) {					// Abgelehnt
		return 4;
	}
}

function getStepLabel($step) {
	$labels = array("Metadaten eingeben","Bereit zum einlesen","Eingelesen","Aktiv", "Abgelehnt");
	$color = getStepColor($step);
	
	return "<span style=\"background: ".$color."\">".$labels[getStepX($step)]."</span>";
}

function getStepColor($step) {
	$labels = array("#bae1ff","#ffffba","#ffdfba","#baffc9", "#ffb3ba");
	return $labels[getStepX($step)];
}

function getDocumentTypeLabel($value) {
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT dt_name FROM tbl_document_type WHERE dt_id= :dtid');
	$result = $sth->execute(array('dtid' => $value));
    $document = $sth->fetch();
	return $document["dt_name"];
}

function getLanguageLabel($value) {
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT l_name FROM tbl_language WHERE l_id= :lid');
	$result = $sth->execute(array('lid' => $value));
    $document = $sth->fetch();
	return $document["l_name"];
}

function getUniversityLabel($value) {
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT u_name FROM tbl_university WHERE u_id= :uid');
	$result = $sth->execute(array('uid' => $value));
    $document = $sth->fetch();
	return $document["u_name"];
}

function getGenderLabel($value) {
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT g_name FROM tbl_gender WHERE g_id= :gid');
	$result = $sth->execute(array('gid' => $value));
    $document = $sth->fetch();
	return $document["g_name"];
}

function getEditToolLabel($value) {
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT e_name FROM tbl_edittool WHERE e_id= :eid');
	$result = $sth->execute(array('eid' => $value));
    $document = $sth->fetch();
	return $document["e_name"];
}

function getBaMaLabel($value) {
	switch($value) {
		case 0: return "Anderes";
		case 1: return "Bachelor";
		case 2: return "Master";
	}
	return "";
}

function getSubjectsLabel($value) {
	
	$retVal = "";
	for($n=0;$n<8;$n++) {
		if( $value & (1 << $n) ) {
			$retVal .= $GLOBALS["subjectLabels"][pow(2,$n)].", ";
		}
	}
	if(strlen($retVal)>2) {
		$retVal = substr($retVal, 0, strlen($retVal)-2);
	}
	
	return $retVal;
}

function getDocumentTypeSelect($name, $selected="", $class="") {
	if($class!="") {
		$class = "class=\"".$class."\"";
	}
	$retVal = "<select name=\"".$name."\" ".$class.">";
	
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT * FROM tbl_document_type ORDER BY dt_name ASC');
    if($sth->execute()) {
		if($sth->rowCount() > 0) {
			while($result = $sth->fetchObject()) {
				if($selected==$result->dt_id) {
					$sel = "selected=\"selected\"";
				}
				else {
					$sel = "";
				}
				$retVal .= "<option $sel value=\"".$result->dt_id."\">".$result->dt_name."</option>";
			}
		}
	}
	$retVal .= "</select>";
	return $retVal;
}

function getYearSelect($name, $selected="", $class="") {
	if($class!="") {
		$class = "class=\"".$class."\"";
	}
	$retVal = "<select name=\"".$name."\" ".$class.">";

	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT MIN(yr) AS MinYear, MAX(yr) AS MaxYear FROM (SELECT YEAR(d_date) as yr FROM tbl_document) tmp');
    if($sth->execute()) {
		$result = $sth->fetchObject();
		$min = $result->MinYear;
		$max = $result->MaxYear;
		for($i=$min;$i<=$max;$i++) {
			$retVal .= "<option value=\"".$i."\">".$i."</option>";
		}
	}

	//TODO
	$retVal .= "</select>";
	return $retVal;
}

function getAgeSelect($name, $selected="", $class="") {
	if($class!="") {
		$class = "class=\"".$class."\"";
	}
	//TODO
	$retVal = "<select name=\"".$name."\" ".$class.">";
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT MIN(age) AS MinAge, MAX(age) AS MaxAge FROM (SELECT d_author_age as age FROM tbl_document) tmp');
    if($sth->execute()) {
		$result = $sth->fetchObject();
		$min = $result->MinAge;
		$max = $result->MaxAge;
		for($i=$min;$i<=$max;$i++) {
			$retVal .= "<option value=\"".$i."\">".$i."</option>";
		}
	}
	$retVal .= "</select>";
	return $retVal;
}

function getLanguageSelect($name, $selected="", $class="") {
	if($class!="") {
		$class = "class=\"".$class."\"";
	}
	$retVal = "<select name=\"".$name."\" ".$class.">";
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT * FROM tbl_language ORDER BY l_name ASC');
    if($sth->execute()) {
		if($sth->rowCount() > 0) {
			while($result = $sth->fetchObject()) {
				if($selected==$result->l_id) {
					$sel = "selected=\"selected\"";
				}
				else {
					$sel = "";
				}
				$retVal .= "<option $sel value=\"".$result->l_id."\">".$result->l_name."</option>";
			}
		}
	}
	$retVal .= "</select>";
	return $retVal;
}

function getEditToolSelect($name, $selected="", $class="") {
	if($class!="") {
		$class = "class=\"".$class."\"";
	}
	$retVal = "<select name=\"".$name."\" ".$class.">";
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT * FROM tbl_edittool ORDER BY e_name ASC');
    if($sth->execute()) {
		if($sth->rowCount() > 0) {
			while($result = $sth->fetchObject()) {
				if($selected==$result->e_id) {
					$sel = "selected=\"selected\"";
				}
				else {
					$sel = "";
				}
				$retVal .= "<option $sel value=\"".$result->e_id."\">".$result->e_name."</option>";
			}
		}
	}
	$retVal .= "</select>";
	return $retVal;
}

function getGenderSelect($name, $selected="", $class="") {
	if($class!="") {
		$class = "class=\"".$class."\"";
	}
	$retVal = "<select name=\"".$name."\" ".$class.">";
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT * FROM tbl_gender ORDER BY g_name ASC');
    if($sth->execute()) {
		if($sth->rowCount() > 0) {
			while($result = $sth->fetchObject()) {
				if($selected==$result->g_id) {
					$sel = "selected=\"selected\"";
				}
				else {
					$sel = "";
				}
				$retVal .= "<option $sel value=\"".$result->g_id."\">".$result->g_name."</option>";
			}
		}
	}
	$retVal .= "</select>";
	return $retVal;
}

function getUniversitySelect($name, $selected="", $class="") {
	if($class!="") {
		$class = "class=\"".$class."\"";
	}
	$retVal = "<select name=\"".$name."\" ".$class.">";
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT * FROM tbl_university ORDER BY u_name ASC');
    if($sth->execute()) {
		if($sth->rowCount() > 0) {
			while($result = $sth->fetchObject()) {
				if($selected==$result->u_id) {
					$sel = "selected=\"selected\"";
				}
				else {
					$sel = "";
				}
				$retVal .= "<option $sel value=\"".$result->u_id."\">".$result->u_name."</option>";
			}
		}
	}
	$retVal .= "</select>";
	return $retVal;
}
?>