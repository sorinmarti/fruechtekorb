<?php
include_once("db.php");

// TODO This doesn't work as needed. UMLAUT problems.
function file_get_contents_utf8($fn) {
	$content = file_get_contents($fn);
	//echo "ENC=".mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true);
    return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

function printCorpusStats() {
	echo "<h2>Korpusumfang</h2>
		  <p>Dieser Teil ist noch nicht implementiert.</p>";
}

function printPieCharts() {
	echo "<h2>Metadaten</h2>
		  <div style=\"display:inline-block; margin: auto;\">
			<div style=\"display:inline-block; float:left; height:300px; width: 300px;\">
				<h3>Dokumenttypen</h3>
				<pie-chart id=\"pieChart1\">\n";
    
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT dt.dt_id, dt.dt_name, COUNT(*) AS type_count FROM tbl_document d, tbl_document_type dt WHERE dt.dt_id = d.d_document_type GROUP BY dt.dt_id');
	 if($sth->execute()) {
		if($sth->rowCount() > 0) {
			$col = 0;
			while($result = $sth->fetchObject()) {
				$col++;
				echo "		<pchart-element name=\"".$result->dt_name."\" value=\"".$result->type_count."\" colour=\"".getColor($col)."\" />\n";
			}
		}
	}
			
    echo "		</pie-chart>
			</div>";
			
	echo "	<div style=\"display:inline-block; float:right; height:300px; width: 300px;\">
				<h3>Erstellungstools</h3>
				<pie-chart id=\"pieChart2\">\n";
		
    $pdo = getPDO();
	$sth = $pdo->prepare('SELECT g.g_id, g.g_name, COUNT(*) AS gender_count FROM tbl_document d, tbl_gender g WHERE g.g_id = d.d_author_gender GROUP BY g.g_id');
	 if($sth->execute()) {
		if($sth->rowCount() > 0) {
			$col = 0;
			while($result = $sth->fetchObject()) {
				$col++;
				echo "<pchart-element name=\"".$result->g_name."\" value=\"".$result->gender_count."\" colour=\"".getColor($col)."\">\n";
			}
		}
	}
			
    echo "		</pie-chart>
			</div>
		  </div>
		  
		  <div style=\"display:inline-block; margin: auto;\">
			<div style=\"display:inline-block; float:left; height:300px; width: 300px;\">
				<h3>Gender</h3>
				<pie-chart id=\"pieChart3\">\n";
    
	$pdo = getPDO();
	$sth = $pdo->prepare('SELECT dt.dt_id, dt.dt_name, COUNT(*) AS type_count FROM tbl_document d, tbl_document_type dt WHERE dt.dt_id = d.d_document_type GROUP BY dt.dt_id');
	 if($sth->execute()) {
		if($sth->rowCount() > 0) {
			$col = 0;
			while($result = $sth->fetchObject()) {
				$col++;
				echo "		<pchart-element name=\"".$result->dt_name."\" value=\"".$result->type_count."\" colour=\"".getColor($col)."\" />\n";
			}
		}
	}
			
    echo "		</pie-chart>
			</div>";
			
	echo "	<div style=\"display:inline-block; float:right; height:300px; width: 300px;\">
				<h3>Muttersprache</h3>
				<pie-chart id=\"pieChart4\">\n";
		
    $pdo = getPDO();
	$sth = $pdo->prepare('SELECT g.g_id, g.g_name, COUNT(*) AS gender_count FROM tbl_document d, tbl_gender g WHERE g.g_id = d.d_author_gender GROUP BY g.g_id');
	 if($sth->execute()) {
		if($sth->rowCount() > 0) {
			$col = 0;
			while($result = $sth->fetchObject()) {
				$col++;
				echo "<pchart-element name=\"".$result->g_name."\" value=\"".$result->gender_count."\" colour=\"".getColor($col)."\">\n";
			}
		}
	}
			
    echo "		</pie-chart>
			</div>
		  </div>";
}

function getColor($colorIdx) {
	$colors = array("#88e199","#aabb99","#88abba","#baffc9", "#ffb3ba");
	return $colors[$colorIdx];
}

function printWordcloud() {
	/*
	echo "  <script type=\"text/javascript\">
				/ * <![CDATA[ * /
					var words = ";						
						require_once('data/commonwords.php');
						
						$content = file_get_contents_utf8('data/wordclouds.txt');
						
						$wordsList = array();
						$words = array();
						preg_match_all('/[a-zA-ZöäüÖÄÜß]{3,}/', strtolower($content), $wordsList);
						
						foreach($wordsList[0] as $word) {
							if(!isCommon($word)) {
								if(isset($words[$word])) {
									$words[$word]++;
								}
								else {
									$words[$word] = 1;
								}
							}
						}
						
						$newWords = array();
						foreach($words as $word => $count) {
							$newWords[] = array('text' => $word, 'size' => $count);
						}
						//print_r($newWords);
						
						//$test = array(array("x"=>"a", "y"=>"b"), array("c"));
						//echo "JSON= ".json_encode($test);
						
						echo json_encode($newWords);
						//echo json_last_error() == JSON_ERROR_UTF8;
						
	echo "		/ * ]> * /
			</script>";
			*/
	echo "<h2>Wordcloud</h2>
		  <p>Dieser Teil ist noch nicht implementiert.</p>";
} 


?>