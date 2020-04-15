<?php
include_once("globals.php");

function pageStart($pagename, $printBar=true, $include="") {
	echo "<!DOCTYPE html>\n";
	echo "<html>\n";
	echo "  <head>\n";
	/*
	echo "    <meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">\n";
	echo "    <meta charset=\"UTF-8\">\n";
	echo "    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">\n";
	echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n"; 
	echo "    <meta name=\"description\" content=\"TODO\" />\n";
	echo "    <meta name=\"keywords\" content=\"TODO\" />\n";
	echo "    <meta name=\"author\" content=\"Sorin Marti\" />\n";
	*/
	echo "    <title>FrueCHTeKorB - Korpus studentischer Arbeiten</title>\n";
	echo "    <link rel=\"shortcut icon\" href=\"images/favicon.ico\" type=\"image/x-icon\">";
	echo "    <link rel=\"icon\" href=\"images/favicon.ico\" type=\"image/x-icon\">";
	
	if($include=="pieChart") {
		echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/pieChart.css\" />\n";
		echo "    <script type=\"text/javascript\" src=\"js/pie-chart-js.js\"></script>";
		echo "  <script>
					window.onload= function() {
						var c1 = document.getElementById(\"pieChart1\");
						c1.resize();
						                                 
						var c2 = document.getElementById(\"pieChart2\");
						c2.resize();
						
						var c3 = document.getElementById(\"pieChart3\");
						c3.resize();
						
						var c4 = document.getElementById(\"pieChart4\");
						c4.resize();
					}
				</script>";
	}
	if($include=="wordCloud") {
		
	}
	if($include=="markdown") {
		echo "	<script src=\"https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js\"></script>";
		echo "	<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css\">";
	}
	
	echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/korpus.css\" />\n";
	
	
	echo "  </head>\n";

	// BODY
	echo "  <body>\n";
	echo "    <div id=\"content\">\n";
	if($printBar) {
		printTitleBar($pagename);
	}
	echo "      <div id=\"content_text\">\n";
}

function pageEnd() {
	echo "      </div>\n";
	
	echo "      <div id=\"footer\"><i>Ein Projekt der germanistischen Linguistik der Universität Basel.</i></div>\n";
	echo "    </div>\n";
	echo "  </body>\n";
	echo "</html>\n";
}

function printTitleBar($pagename) {
	$split = explode("/", $pagename);
	$pagename = $split[sizeof($split)-1];
	$pages = array(array("Startseite", "index.php"),
				   array("Korpus",     "korpus.php"),
				   array("Kontakt",    "kontakt.php"),
				   array("Intern", 	   "intern.php"));
					
	
	echo "    <div id=\"navigation1\">\n";
	echo "      <ul class=\"nav\">\n";
	foreach($pages as $page) {
		if($page[1]==$pagename) {
			$active = "class=\"active1\"";
		}
		else {
			$active = "class=\"inactive1\"";
		}
		echo "        <li class=\"nav\"><a $active href=\"".$page[1]."\">".$page[0]."</a></li>\n";
	}
	echo "	    </ul>\n";
	echo "    </div>\n";
	echo "    <div id=\"navigation2\">\n";
	echo "      <ul class=\"nav\">\n";
	foreach($pages as $page) {
		if($page[1]==$pagename) {
			$active = "class=\"active2\"";
		}
		else {
			$active = "class=\"inactive2\"";
		}
		echo "        <li class=\"nav\"><a $active href=\"".$page[1]."\">".$page[0]."</a></li>\n";
	}
	echo "	    </ul>\n";
	echo "    </div>\n";

}
?>