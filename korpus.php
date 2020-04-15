<?php
session_start();
include_once("functions.php");
include_once("db.php");
pageStart($_SERVER['PHP_SELF']);
echo "<h2>Korpusabfrage</h2>";

$searchform = printSimpleSearch();
if(isset($_REQUEST['searchform']) && $_REQUEST['searchform']=="advanced") {
	$searchform = printAdvancedSearch();
}

$result = "";
if(isset($_REQUEST['search'])) {
	// DO AN ADVANCED SEARCH
	$result = "Die Suchfunktion ist noch nicht implementiert.";
}

echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"GET\">
		".$searchform."
	  </from><br/>".
	  $result;

pageEnd();

function printSimpleSearch() {
	return "<table class=\"searchform\">
				<tr>
					<td align=\"right\">Suchbegriff:</td>
					<td><input type=\"text\" name=\"searchQuery\" class=\"searchform\" /></td>
					<td align=\"right\">
						<a href=\"?searchform=advanced\"><nobr>Erweiterte Suche</nobr></a>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type=\"submit\" name=\"search\" value=\"Korpus durchsuchen\" class=\"searchform\" />
					</td>
					<td></td>
				</tr>
			</table>
			<input type=\"hidden\" name=\"searchform\" value=\"simple\" />";
}

function printAdvancedSearch() {
	return "		<table class=\"searchform\">
						<tr>
							<td align=\"right\">Suchbegriff:</td>
							<td colspan=\"6\" width=\"100%\"><input type=\"text\" name=\"searchQuery\" class=\"searchform\" /></td>
							<td colspan=\"2\" align=\"right\">
								<a href=\"?searchform=simple\"><nobr>Einfache Suche</nobr></a>
							</td>
						</tr>
						<tr>
							<td class=\"sf_r\" rowspan=\"4\" valign=\"top\" align=\"right\">Filter:</td>
							<td class=\"sf_tr\" colspan=\"4\" bgcolor=\"#DDDDDD\" align=\"center\"><i>Dokument</i></td>
							<td class=\"sf_tr\" colspan=\"4\" bgcolor=\"#DDDDDD\" align=\"center\"><i>Autor</i></td>
						</tr>
						<tr>
							
							<td>Dokumenttyp</td>
							<td class=\"sf_r\" colspan=\"3\">".getDocumentTypeSelect("docType", "", "searchform")."</td>
							<td>Alter zw.</td>
							<td>".getAgeSelect("ageStartSelectName", "", "searchform")."</td>
							<td>und</td>
							<td class=\"sf_r\">".getAgeSelect("ageEndSelectName", "", "searchform")."</td>
						</tr>
						<tr>
							
							<td>Abgabe zw.</td>
							<td>".getYearSelect("yearStartSelectName", "", "searchform")."</td>
							<td>und</td>
							<td class=\"sf_r\">".getYearSelect("yearEndSelectName", "", "searchform")."</td>
							<td>Gender</td>
							<td class=\"sf_r\" colspan=\"3\">".getGenderSelect("genderSelectName", "", "searchform")."</td>
						</tr>
						<tr>
							
							<td class=\"sf_b\">Sprache</td>
							<td class=\"sf_br\"colspan=\"3\">".getLanguageSelect("lang", "", "searchform")."</td>
							<td class=\"sf_b\">Studium</td>
							<td class=\"sf_br\" colspan=\"3\">niy</td>
						</tr>
						<tr>
							<td></td>
							<td colspan=\"8\">
								<input type=\"submit\" name=\"search\" value=\"Korpus durchsuchen\" class=\"searchform\" />
							</td>
						</tr>
					</table>
					<input type=\"hidden\" name=\"searchform\" value=\"advanced\" />";
}
?>