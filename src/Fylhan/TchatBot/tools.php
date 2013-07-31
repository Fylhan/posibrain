<?php

namespace Fylhan\TchatBot;

function parserI($int) {
	return intval(trim($int));
}
function parserF($decimal) {
	return floatval(trim($decimal));
}

function parserS($str) {
	if(!get_magic_quotes_gpc()) {
		return addslashes(trim($str));
	}
	return trim($str);
}
function deparserS($str) {
	return stripslashes(trim($str));
}

/**
 * Test si un email est valide
 * @param $email Email à tester
 * @return true si c'est un email
 * @return false sinon
 */
function isEmailValide($email) {
	return preg_match('![a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}!i', $email);
}
/**
 * Test si un tel est valide
 * @param $tel tel à tester
 * @return true si c'est un tel
 * @return false sinon
 */
function isTelValide($tel) {
	return preg_match('!^0[1-9]([-. ]?[0-9]{2}){4}$!i', $tel);
}

// Transforme le numéro du mois en son nom en français
function nomMois($numMois)
{
	switch ($numMois) {
		case 1: return 'janvier'; break;
		case 2: return 'février'; break;
		case 3: return 'mars'; break;
		case 4: return 'avril'; break;
		case 5: return 'mai'; break;
		case 6: return 'juin'; break;
		case 7: return 'juillet'; break;
		case 8: return 'août'; break;
		case 9: return 'septembre'; break;
		case 10: return 'octobre'; break;
		case 11: return 'novembre'; break;
		default: return 'décembre';
	}
}

function approximeMinute($minute)
{
	if ($minute > 00 && $minute <= 05)
		$minute = 05;
	else if ($minute > 05 && $minute <= 10)
		$minute = 10;
	else if ($minute > 10 && $minute <= 15)
		$minute = 15;
	else if ($minute > 15 && $minute <= 20)
		$minute = 20;
	else if ($minute > 20 && $minute <= 25)
		$minute = 25;
	else if ($minute > 25 && $minute <= 30)
		$minute = 30;
	else if ($minute > 30 && $minute <= 35)
		$minute = 35;
	else if ($minute > 35 && $minute <= 40)
		$minute = 40;
	else if ($minute > 40 && $minute <= 45)
		$minute = 45;
	else if ($minute > 45 && $minute <= 50)
		$minute = 50;
	else
		$minute = 55;
	return $minute;
}

function filesizeHuman($filepath, $precision=2) {
	$unites = array('octets','ko','mo','go','to');
	$taille = filesize($filepath);
	$div = floor(log($taille, 1024));
	$taille = round($taille/pow(1024, $div), $precision);
	return str_replace('.',',',$taille).' '.$unites[$div];
}

function filetypeHuman($filepath) {
	return substr(strrchr($filepath, '.'), 1);
}

function filedateHuman($filepath) {
	return dateFr(filemtime($filepath));
}

/** 
 * Formate une date en fonction de la date actuelle
*/
function dateFr($timestamp, $heure=false, $le=true) {
    $timestampCourant = time();
    if (date('Y', $timestamp) == date('Y', $timestampCourant)) {
        if ($heure && date('z', $timestamp) == date('z', $timestampCourant)) { // Le même jour
            $s_date = 'aujourd\'hui à '.date('G\hi', $timestamp);
        } elseif (date('z', $timestamp) == date('z', $timestampCourant) - 1) { // La veille
            $s_date = 'hier'.($heure ? ' à '.date('G\hi', $timestamp) : '');
        } else { // La même année
            $s_date = ($le ? 'le ' : '').(date('d', $timestamp)+0).' '.nomMois(date('n', $timestamp)).($heure ? ' à '.date('G\hi', 
$timestamp) : '');
        }
    } else { // Une année différente
        $s_date = ($le ? 'le ' : '').(date('d', $timestamp)+0).' '.nomMois(date('n', $timestamp)).date(' Y', $timestamp).($heure ? ' à '.
date('G\hi', $timestamp) : '');
    }
    return $s_date;
}

/**
 * Parse une date au format DATE_RFC822
 * Il existe date(DATE_RFC822, timestamp), mais le serveur Flamb'clair ne le connait pas
 * @param int $timestamp Timestamp à parser
 */
function dateToRFC822($timestamp)
{
	return date('D\, d M Y H\:i\:s O', $timestamp);
}

/**
 * Formate la date d'un événement
 * @param $timestamp Timestamp
 * @return La date formatée
 */
function afficherDateEvenement($timestamp) {
	return 'le '.(date('d', $timestamp)+0).' '.nomMois(date('n', $timestamp)).' à '.date('G\hi', $timestamp);
}
function salut() {
	$heure = date('G\.i', time());
	if ($heure <= 7 || $heure >= 22) {
		return 'bonne nuit';
	} elseif ($heure <= 11.30) {
		return 'bonjour';
	} elseif ($heure <= 13.30) {
		return 'bon appetit';
	} elseif ($heure <= 18) {
		return 'bonne après-midi';
	} else {
		return 'bonne soirée';
	}
}

/**
 * Clean une chaine de caractères pour l'url rewriting
 * @param string $url Chaine de caractères à cleaner
 * @param boolean $elag True pour élaguer les petits mots (la, les, ...); false sinon
 * @return string La chaine de caractère rewritée
*/
function parserUrl($url, $elag=true, $strtolower=true)
{
	if ($strtolower) {
		$url = mb_strtolower($url);
	}
	

	// Elagage
	if ($elag) {
		$url = preg_replace('!(?:^|\s|[_-])(le|la|les|un|une|des|de|à|sa|son|ses|ces|s)(?:$|\s|[\'_-])!i', '-', $url);
	}
	
	// Clean accent
	$url = str_ireplace(array('à', 'â', 'ä'), 'a', $url);
	$url = str_ireplace(array('é', 'è', 'ê', 'ë'), 'e', $url);
	$url = str_ireplace(array('î', 'ï'), 'i', $url);
	$url = str_ireplace(array('ô', 'ö'), 'o', $url);
	$url = str_ireplace(array('ù', 'û', 'ü'), 'u', $url);
	$url = str_ireplace('ÿ', 'y', $url);
	$url = str_ireplace('ç', 'c', $url);
	
	// Clean caractère
	$url = preg_replace('![/@\'=_ -]!i', '-', $url);
	$url = preg_replace('![&~"#|`^()+{}[\]$£¤*µ%§\!:;\\\.,?°]!i', '', $url);
	
	// Elagage final
	$url = preg_replace('!-(d|l|m|qu|t)-!i', '-', $url);
	$url = preg_replace('!^(d|l|m|qu|t)-!i', '-', $url);
	$url = preg_replace('!-(d|l|m|qu|t)&!i', '-', $url);
	$url = preg_replace('!-{2,}!i', '-', $url);
	$url = preg_replace('!^-!i', '', $url);
	$url = preg_replace('!-$!i', '', $url);

	return $url;
}

function deparserUrl($url)
{
	$url = ucwords($url);
	$url = str_replace('-', ' ', $url);		
	return $url;
}

/**
 * Rendre un url joli (on enlève http tout ça et on remplace par www)
 */
function cleanerUrl($url) {
	return 'www.'.preg_replace('!(?:http://)?(?:www\.)?(.+)!i', '$1', $url);
}

// Met au pluriel un mot
function pluriel($a_i_nombreElements, $a_s_mot) {
	if ($a_i_nombreElements > 1) {
		$s_motAuPluriel = $a_i_nombreElements." ".$a_s_mot."s";
	} elseif ($a_i_nombreElements == '1') {
		$s_motAuPluriel = "Un ".$a_s_mot;
	} else {
		$s_motAuPluriel = 'Aucun '.$a_s_mot;
	}
    return $s_motAuPluriel;
}
function ajoutS($nbElmnt, $str) {
	if ($nbElmnt > 1) {
		return $str.'s';
	} else {
    	return $str;
	}
}

/**
 * Retourne l'url de l'image si une image existe dans ce dossier avec ce nom
 * @param string $dossierOuChercher)
 * @param string $nomImage (sans extension)
 * @return Url de l'image si elle existe, vide sinon
 */
function isImage($dossierOuChercher, $nomImage) {
	// AJout d'un slash à la fin si besoin
	if ($dossierOuChercher[(strlen($dossierOuChercher)-1)] != '/') {
		$dossierOuChercher .= '/';
	}
	// Boucle sur les extensions d'images
	$extensions = array('.jpg', '.jpeg', '.gif', '.png', '.JPG', '.JPEG', '.GIF', '.PNG');
	foreach($extensions AS $extension) {
		if (is_file($dossierOuChercher.$nomImage.$extension)) {
			return $nomImage.$extension;
		}
	}
	// Si on n'a rien trouvé, on renvoie vide
	return '';
}

/**
 * Tronque un texte pour qu'il fasse $longueur caractères et y ajoute "..." si besoins
 * @param string $txt Texte à tronquer
 * @param int $longueur La longueur du texte final
 * @return string Le texte tronqué
 */
function getExtrait($txt, $longueur=300)
{
	if (strlen($txt) > 300)
		$fin = '...';
	return substr($txt, 0, $longueur).@$fin;
}

function getUrlCourant($urlDefault)
{
	$urlCourant = explode('?', isset($_SERVER['REQUEST_URI']) ? htmlspecialchars($_SERVER['REQUEST_URI']) : $urlDefault);
	return $urlCourant[0];
	
}

function getUrlPagePrecedente($urlDefault)
{
	$urlCourant = explode('?', isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : $urlDefault);
	return $urlCourant[0];
	
}

function br2nl($str) {
	return preg_replace("!<br ?/?>!i", "\n", $str);
}

/**
 * Clean a JSON string
 * * Remove comments ("//" and "/*")
 * * Add first and last braces ("{" and "}") if missing
 * * Remove unauthorized commas
 */
function cleanJsonString($data) {
	$data = trim($data);
	$data = preg_replace('!\s*//[^"]*\n!U', '\n', $data);
	$data = preg_replace('!/\*[^"]*\*/!U', '', $data);
	$data = !startsWith('{', $data) ? '{'.$data : $data;
	$data = !endsWith('}', $data) ? $data.'}' : $data;
	$data = preg_replace('!,(\s*[}\]])!U', '$1', $data);
    return $data;
}

/**
 * Retourne la signification de la dernière erreur 
 * d'encodage / décodage json.
 * En français.
 */
function getJsonLastError() {
	switch (json_last_error()) {
		case JSON_ERROR_NONE:
			return 'Aucune erreur';
		break;
		case JSON_ERROR_DEPTH:
			return 'Profondeur maximale atteinte';
		break;
		case JSON_ERROR_STATE_MISMATCH:
			return 'Inadéquation des modes ou underflow';
		break;
		case JSON_ERROR_CTRL_CHAR:
			return 'Erreur lors du contrôle des caractères';
		break;
		case JSON_ERROR_SYNTAX:
			return 'Erreur de syntaxe ; JSON malformé';
		break;
		case JSON_ERROR_UTF8:
			return 'Caractères UTF-8 malformés, probablement une erreur d\'encodage';
		break;
	}
	return 'Erreur inconnue';
}

function echa($var) {
	echo '<pre>';print_r($var); echo '</pre><br />';
}


function startsWith($needle, $haystack) {
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($needle, $haystack) {
	$length = strlen($needle);
	if ($length == 0)
		return true;
	return (substr($haystack, -$length) === $needle);
}

function logger($str, $line=-1) {
	echo '<span style="color: red;">'.dateToRFC822(time()).': "'.$str.'" on line '.$line.'</span>';
}

?>
