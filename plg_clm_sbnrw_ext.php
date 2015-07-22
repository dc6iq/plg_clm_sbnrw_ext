<?php
// kein direkter Zugriff über eine Url sondern nur über's Joomla-Framework
defined('_JEXEC') or die('Unerlaubter Zugriff');
// lade die JPlugin-Klasse, von der unsere eigene Plugin-Klasse abgeleitet wird
jimport('joomla.plugin.plugin');
// Rumpf unserer Plugin-Klasse
class plgContentPlg_clm_sbnrw_ext extends JPlugin {
	function plgContentPlg_clm_sbnrw_ext(&$subject, $my_config) {
		if(!defined("DS")){define('DS', DIRECTORY_SEPARATOR);} // fix for Joomla 3.2
		parent::__construct($subject, $my_config);
	}
	protected function get_css_style($style) {
		switch ($style) {
			case 1:
				return $style_css = "width:100%;";
			case 2:
				return $style_css = "width:auto; margin:0 auto 0 0;";
			case 3:
				return $style_css = "width:auto; margin:0 auto;";
			case 4:
				return $style_css = "width:auto; margin:0 0 0 auto;";
			case 5:
				return $style_css = "width:auto; float:left;";
			case 6:
				return $style_css = "width:auto; float:right;";
			default:
				return $style_css = "width:auto;";
		}
	}
	protected function check_highlighting($string) {
		if ($string == "") {
			return array(true, array());
		}
		$what = explode("!", $string);
		for ($i = 0;$i < count($what);$i++) {
			$what[$i] = explode("?", $what[$i]);
			if ((count($what[$i]) < 2) || (count($what[$i]) > 4) || $what[$i][0] == "" || !is_numeric($what[$i][1]) || $what[$i][1] < 0 || $what[$i][1] > 2) {
				return array(false);
			}
		}
		return array(true, $what);
	}
	protected function get_old_config($in) {
		$new = array();
		$old = explode(" ", $in);
		if (count($old) > 1) {
			$new[0] = $old[1]; // ID ist nun vorne
			$new[1] = $old[0]; // Jahr ist nun weiter hinten
			$new[2] = 0; // Style immer mit width:auto
			if (count($old) > 2) {
				$new[3] = $old[2]; // max_Aufsteiger
				if (count($old) > 3) {
					$new[4] = $old[3]; // min Aufsteiger
					
				}
			}
		} else {
			return "";
		}
		$out = "";
		for ($i = 0;$i < count($new);$i++) {
			if ($i != 0) {
				$out.= ":";
			}
			$out.= $new[$i];
		}
		return $out;
	}
	protected function mm_findItemWithArg($zeile, $tag) {
		$number = 0;
		$in = "";
		$before = strpos($zeile, '[' . $tag);
		if ($before == null) {
			return $zeile;
		}
		$tag_length = strlen($tag);
		$stop = false;
		do {
			$next = strpos($zeile, '[' . $tag, $before + $tag + 2);
			if ($next == null) {
				$stop = true;
			}
			$after = strpos($zeile, ']', $before + $tag + 2);
			if ($after != null && ($stop || $after < $next)) {
				$my_config = substr($zeile, ($before + $tag_length + 2), $after - ($before + $tag_length + 2));
				$first = substr($zeile, 0, $before);
				$config_char = $zeile[$before + $tag_length + 1];
				if ($config_char == '_') {
					$zeile[$before + $tag_length + 1] = ' ';
				} else if ($config_char == '-') {
					$zeile[$before + $tag_length + 1] = ':';
				} else if (($config_char == ':') || ($config_char == ' ')) {
					$out = $this->mm_nrwligen_print($my_config, $config_char == ' ', $number);
					$number++;
					if ($out[0]) { // Falls durch den Tag größere Abschnitte eingeschlossen sind werden diese wieder eingebunden
						$in.= $first . $out[1];
					} else {
						$in.= $first . $out[1] . $my_config;
					}
					$zeile = substr($zeile, $after + 1);
					if (!$stop) {
						$next = $next - ($after + 1);
					} // Position auf neuen String übertragen.
					
				}
			}
			$before = $next;
		}
		while (!$stop);
		return '<div id="clm"><div id="rangliste">' . $in . $zeile . '</div></div>';
	}
	protected function mm_nrwligen_print($my_config, $old, $number) {
		if (!ini_get('allow_url_fopen')) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_FOPEN") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		if ($old) {
			$my_config = plgContentPlg_clm_sbnrw_ext::get_old_config($my_config);
		}
		if (!is_numeric($my_config)) {
			$my_config = explode(":", $my_config);
		} else {
			$my_config = array($my_config);
		}
		if (count($my_config) > 8 || count($my_config) < 1) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_TAG") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		if (!is_numeric($my_config[0])) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_ID") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		if (count($my_config) > 1 && ($my_config[1] != "")) {
			$season = htmlentities($my_config[1]);
		} else {
			$season = "";
		}
		if (count($my_config) > 2) {
			if (!is_numeric($my_config[2]) || ($my_config[2] < 0) || ($my_config[2] > 6)) {
				$lang = JFactory::getLanguage();
				$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
				return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_STYLE") . "&lt;/plg_clm_sbnrw_ext&gt;");
			}
			$style = $my_config[2];
		} else {
			$style = 0;
		}
		if (count($my_config) > 3) {
			if (!is_numeric($my_config[3])) {
				$lang = JFactory::getLanguage();
				$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
				return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_UP") . "&lt;/plg_clm_sbnrw_ext&gt;");
			}
			$min_auf = $my_config[3];
		} else {
			$min_auf = 0;
		}
		if (count($my_config) > 4) {
			if (!is_numeric($my_config[4])) {
				$lang = JFactory::getLanguage();
				$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
				return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_UP") . "&lt;/plg_clm_sbnrw_ext&gt;");
			}
			$min_ab = $my_config[4];
		} else {
			$min_ab = 0;
		}
		if (count($my_config) > 5) {
			if (!is_numeric($my_config[5])) {
				$lang = JFactory::getLanguage();
				$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
				return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_UP") . "&lt;/plg_clm_sbnrw_ext&gt;");
			}
			$max_auf = $my_config[5];
		} else {
			$max_auf = $min_auf;
		}
		if (count($my_config) > 6) {
			if (!is_numeric($my_config[6])) {
				$lang = JFactory::getLanguage();
				$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
				return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_UP") . "&lt;/plg_clm_sbnrw_ext&gt;");
			}
			$max_ab = $my_config[6];
		} else {
			$max_ab = $min_ab;
		}
		if (count($my_config) > 7) {
			$highlighting = $this->check_highlighting($my_config[7]);
			if (!($highlighting[0])) {
				$lang = JFactory::getLanguage();
				$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
				return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_HIGHLIGHTING") . "&lt;/plg_clm_sbnrw_ext&gt;");
			}
		} else {
			$highlighting[0] = false;
		}
		if ($min_auf > $max_auf) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_MIN") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		if ($min_ab > $max_ab) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_MAX") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		$url = 'http://nrw.svw.info/tools/export/tabelle.php?tid=' . $my_config[0];
		if (!$html = file_get_contents($url)) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_CONNECTION") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		if ($html == "kein Turnier") {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_TABLE") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		require_once (JPATH_SITE . DS . 'components/com_clm/includes' . DS . 'css_path.php');
		$xml = new SimpleXMLElement($html);
		$html = '
<div class="plg_clm_sbnrw_ext">
<table cellpadding="0" cellspacing="0" class="rangliste" style="' . $this->get_css_style($style) . '">
<tr>
	<th class="rang"><div>Rg</div></th>
	';
		if ($season != "") {
			$html.= '<th class="team"><div><a target="_blank" href="http://nrw.svw.info/ergebnisse/show/42/' . $my_config[0] . '/tabelle/">' . $xml->tname . " - " . $season . '</a></div></th>';
		} else {
			$html.= '<th class="team"><div><a target="_blank" href="http://nrw.svw.info/ergebnisse/show/42/' . $my_config[0] . '/tabelle/">' . $xml->tname . '</a></div></th>';
		}
		$team = 0;
		foreach ($xml->kreuzHeader->eH as $eH) {
			$html.= '<th class="rnd"><div>' . $eH . '</div></th>';
			$team++;
		}
		// check if there are enough teams
		if ($team == 0) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_BAD") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		if ($team < $max_ab + $max_auf) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext', JPATH_ADMINISTRATOR);
			return array(false, "&lt;plg_clm_sbnrw_ext&gt;" . JText::_("PLG_CLM_SBNRW_ERR_COUNT") . "&lt;/plg_clm_sbnrw_ext&gt;");
		}
		$html.= '			
			<th class="mp"><div>MP</div></th>
			<th class="bp"><div>BP</div></th>
					</tr>
';
		$where = 0;
		$min_ab = $team - $min_ab;
		$max_ab = $team - $max_ab;
		foreach ($xml->rangliste->teams as $oneTeam) {
		$oneTeam->team = str_replace("  ", " ", $oneTeam->team); // fix, nrw use bad strings
			if ($where % 2 == 0) {
				$html.= '<tr class="zeile1">';
			} else {
				$html.= '<tr class="zeile2">';
			}
			if ($where < $min_auf) {
				$html.= '<td class="rang_auf"> ';
			} else if ($where < $max_auf) {
				$html.= '<td class="rang_auf_evtl"> ';
			} else if ($where >= $min_ab) {
				$html.= '<td class="rang_ab"> ';
			} else if ($where >= $max_ab) {
				$html.= '<td class="rang_ab_evtl"> ';
			} else {
				$html.= '<td class="rang"> ';
			}
			$html.= $oneTeam->platz . '</td>
			';
			$exist = false;
			if ($highlighting[0]) {
				for ($i = 0;$i < count($highlighting[1]);$i++) {
					if ($highlighting[1][$i][0] == $oneTeam->team) {
						if (count($highlighting[1][$i]) == 3) {
							$html.= '<td style="color:' . htmlentities($highlighting[1][$i][2], ENT_QUOTES, "UTF-8") . ';" class="team">';
						} else {
							$html.= '<td class="team">';
						}
						if ($highlighting[1][$i][1] == 1) {
							$html.= '<b>' . $oneTeam->team . '</b></td>
';
						} else {
							$html.= $oneTeam->team . '</td>
';
						}
						$exist = true;
						break;
					}
				}
			}
			if (!$exist) {
				$html.= '<td class="team">' . $oneTeam->team . '</td>';
			}
			foreach ($oneTeam->kreuzBody->e as $e) {
				if ($e == "**") {
					$html.= '<td class="trenner">X</td>';
				} else {
					$html.= '<td>' . $e . '</td>';
				}
			}
			$html.= '<td class="mp">' . $oneTeam->mp . '</td>
<td class="bp">' . $oneTeam->bp . '</td>
</tr>';
			$where++;
		}
		if ($this->params->get('ajax', 1) > 0) {
			$ajax = "";

			// fix für eine ungerade Anzahl an Runden
			if($team%2==0) {
				$rounds=$team;
			} else {
				$rounds=$team+1;
			}

			for ($i = 1;$i < $rounds;$i++) {
				$lang = JFactory::getLanguage();
				$ajax.= '<th class="rnd"><a onclick="plg_clm_sbnrw_modal_load(' . $my_config[0] . ',' . $i . ',\'' . JURI::base(true) . '\',\'' . $lang->getTag() . '\',\'' . $number . '\')" href="javascript:void(0)">' . $i . '</a></th>';
			}
			$lang = JFactory::getLanguage();
			$lang->load('plg_content_plg_clm_sbnrw_ext_round', JPATH_ADMINISTRATOR);
			if($team%2==0) {
				$html.= "<tr><th colspan='2'>" . JText::_("PLG_CLM_SBNRW_ROUND") . "</th>" . $ajax . "<th></th><th></th><th></th></tr>";
			} else {
				$html.= "<tr><th colspan='2'>" . JText::_("PLG_CLM_SBNRW_ROUND") . "</th>" . $ajax . "<th></th><th></th></tr>";
			}
			if ($this->params->get('ajax', 1) == 2) {
				ob_start();
				require_once ("modal.php");
				$html = ob_get_contents() . $html;
				ob_end_clean();
			} else if ($this->params->get('ajax', 1) == 1) {
				ob_start();
				require_once ("under.php");
				$html = ob_get_contents() . $html;
				ob_end_clean();
			}
		}
		$html.= '
</table>';
		if ($this->params->get('ajax', 1) == 1) {
			$html.= '<div style="' . $this->get_css_style($style) . '" id="plg_clm_sbnrw_' . $number . '"></div>';
		}
		$html.= '
</div>
';
		return array(true, $html);
	}
	function onContentPrepare($context, $row, $params, $page = 0) {
		$this->renderTS($row, $params, $page = 0);
	}
	function renderTS($article, $params, $limitstart) {
		$article->text = $this->mm_findItemWithArg($article->text, "showNRWLiga");
		return true;
	}
}
