<?php
error_reporting(0);
if (isset($_GET["tid"]) && is_numeric($_GET["tid"]) && isset($_GET["round"]) && is_numeric($_GET["round"])) {
	$url = 'http://nrw.svw.info/tools/export/runde.php?tid=' . $_GET["tid"] . '&runde=' . $_GET["round"];
	if (!$xml = file_get_contents($url)) {
		exit(0);
	}
	$xml = new SimpleXMLElement($xml);
	$first = true;
	if (isset($_GET["lang"]) && ($_GET["lang"] == "de-DE")) {
		$lang_german = true;
	} else {
		$lang_german = false;
	}
	$date = explode(".", $xml->spieltag->datum);
	$time = explode(":", $xml->spieltag->uhrzeit);
	if (date("Y") > $date[2]) {
		$before = true;
	} else if (date("Y") < $date[2]) {
		$before = false;
	} else if (date("m") > $date[1]) {
		$before = true;
	} else if (date("m") < $date[1]) {
		$before = false;
	} else if (date("d") > $date[0]) {
		$before = true;
	} else if (date("d") < $date[0]) {
		$before = false;
	} else if (date("H") > $time[0]) {
		$before = true;
	} else if (date("H") < $time[0]) {
		$before = false;
	} else if (date("i") > $time[1]) {
		$before = true;
	} else if (date("i") < $time[1]) {
		$before = false;
	}
?>
<div class="clm">
 <div id="runde">
  <div class="nrw-extern">
   <table cellpadding="0" cellspacing="0" class="runde">
    <tr><?php
	if ($before) {
		if ($lang_german) {
?>
     <th colspan="4" class="paarung2">Runde <?php
			echo htmlentities($xml->spieltag->runde, ENT_QUOTES, "UTF-8") . " begann am " . htmlentities($xml->spieltag->datum) . " um " . htmlentities($xml->spieltag->uhrzeit, ENT_QUOTES, "UTF-8");
?> Uhr</th><?php
		} else {
?>
     <th colspan="4" class="paarung2">Round <?php
			echo htmlentities($xml->spieltag->runde, ENT_QUOTES, "UTF-8") . " has started at " . htmlentities($xml->spieltag->uhrzeit, ENT_QUOTES, "UTF-8") . " o'clock on " . htmlentities($xml->spieltag->datum, ENT_QUOTES, "UTF-8"); ?></th><?php
		}
	} else {
		if ($lang_german) {
?>
     <th colspan="4" class="paarung2">Runde <?php
			echo htmlentities($xml->spieltag->runde, ENT_QUOTES, "UTF-8") . " wird am " . htmlentities($xml->spieltag->datum, ENT_QUOTES, "UTF-8") . " um " . htmlentities($xml->spieltag->uhrzeit);
?> Uhr starten</th><?php
		} else {
?>
     <th colspan="4" class="paarung2">Round <?php
			echo htmlentities($xml->spieltag->runde, ENT_QUOTES, "UTF-8") . " will start at " . htmlentities($xml->spieltag->uhrzeit, ENT_QUOTES, "UTF-8") . " o'clock on " . htmlentities($xml->spieltag->datum, ENT_QUOTES, "UTF-8");
?></th><?php
		}
	}
?>
    </tr><?php
	foreach ($xml->begegnung as $begegnung) { ?>
    <tr>
     <td class="noborder" colspan="6"></td>
    </tr>
    <tr>
    

     <th colspan="2" class="paarung2"><?php
		echo htmlentities($begegnung->heimmannschaft->name, ENT_QUOTES, "UTF-8");
?></th>
     <th class="paarung"><?php
		echo $begegnung->ergebnis->heimergebnis . " : " . $begegnung->ergebnis->gastergebnis;
?></th>
<th class="paarung2"><?php
		echo htmlentities($begegnung->gastmannschaft->name, ENT_QUOTES, "UTF-8");
?></th>




    </tr>
    <tr><?php
		for ($i = 0;$i < count($begegnung->einzelergebnis);$i++) {
			if ((($i + 1) % 2) == 1) {
				echo '<tr class="zeile2">';
			} else {
				echo '<tr class="zeile1">';
			}
?>
     <td class="paarung"><?php
			echo htmlentities($begegnung->einzelergebnis[$i]->brettnr, ENT_QUOTES, "UTF-8");
?></td>
     <td class="paarung2"><?php
			echo htmlentities($begegnung->einzelergebnis[$i]->heimspieler->nachname, ENT_QUOTES, "UTF-8");
			if ($begegnung->einzelergebnis[$i]->heimspieler->vorname != "") {
				echo ",";
			}
			echo htmlentities($begegnung->einzelergebnis[$i]->heimspieler->vorname, ENT_QUOTES, "UTF-8");
?></td>
     <td class="paarung"><b><?php
			echo $begegnung->einzelergebnis[$i]->ergebnis->heimergebnis;
			if (($begegnung->einzelergebnis[$i]->ergebnis->heimergebnis == "+") || ($begegnung->einzelergebnis[$i]->ergebnis->heimergebnis == "-")) {
				echo " / ";
			} else {
				echo " - ";
			}
			echo $begegnung->einzelergebnis[$i]->ergebnis->gastergebnis; ?></b></td>
     <td class="paarung2"><?php
			echo htmlentities($begegnung->einzelergebnis[$i]->gastspieler->nachname, ENT_QUOTES, "UTF-8");
			if ($begegnung->einzelergebnis[$i]->gastspieler->vorname != "") {
				echo ",";
			}
			echo htmlentities($begegnung->einzelergebnis[$i]->gastspieler->vorname, ENT_QUOTES, "UTF-8");
?></td>
    </tr><?php
		}
	}
?>
   </table>
  </div>
 </div>
</div>
<?php
}
?>

