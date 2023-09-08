<?php
/**
* Pagelist Plugin / Extra Functions
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

function cot_geturlparams() {
  if (defined('COT_LIST')) {
    global $list_url_path;
    $out = $list_url_path;
  }
  elseif (defined('COT_PAGES')) {
    global $urlParams;
    $out = $urlParams;
  }
  elseif (defined('COT_USERS')) {
    global $m;
    $out = empty($m) ? array() : array('m' => $m);
  }
  elseif (defined('COT_ADMIN')) {
    global $m, $p, $a;
    $out = array('m' => $m, 'p' => $p, 'a' => $a);
  }
  else
    $out = array();
  return $out;
}

function &array_shift2(&$array) {
  if (count($array) > 0) {
    $key = key($array);
    $first = &$array[$key];
  }
  else
    $first = null;
  array_shift($array);
  return $first;
}

/**
 * Encrypts or decrypts string
 *
 * @param		string	$action	01.	Action (encrypt || decrypt)
 * @param		string	$string	02.	String to encrypt / decrypt
 * @param		string	$key		03. Secret key
 * @param		string	$iv			04. Initialization vector
 * @param		string	$method	05. Encryption method (optional)
 * @return	string					Encrypted / decrypted string
 */
 if (!function_exists('cot_encrypt_decrypt')) {
	function cot_encrypt_decrypt($action, $string, $key, $iv, $method = '') {
		$method = empty($method) ? 'AES-256-CBC' : $method;
		$key = hash('sha256', $key);
		$iv = substr(hash('sha256', $iv), 0, 16);

		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		elseif ($action == 'decrypt') {
			$output = base64_decode($string);
			$output = openssl_decrypt($output, $method, $key, 0, $iv);
		}
		return $output;
	}
 }

/**
 * Converts multidimensional array to string
 *
 * @param		string	$glue		01. Separator
 * @param		string	$array	02. Array
 * @return	string					List of array(s) values
 */
function cot_implode_all($glue, $array) {
	for ($i = 0; $i < count($array); $i++) {
		if (@is_array($array[$i]))
		  $array[$i] = cot_implode_all($glue, $array[$i]);
	}
	return implode($glue, $array);
}

/**
 * Returns condition as SQL string
 *
 * @param		string	$cc_mode	01. Selection mode: single, array, black or white
 * @param		string	$cc_cats	02. Category (or categories in double quotes, comma separated)
 * @param		bool		$cc_subs	03. Include subcategories
 * @return	string						Condition as SQL string
 */
function cot_compilecats($cc_mode, $cc_cats, $cc_subs) {

	if (!empty($cc_cats) && ($cc_mode == 'single' || $cc_mode == 'array_white' || $cc_mode == 'array_black' || $cc_mode == 'white' || $cc_mode == 'black')) {
		$cc_cats = str_replace(' ', '', $cc_cats);

		if ($cc_mode == 'single') {
			if ($cc_subs == false) {
				$cc_where = "page_cat = " . Cot::$db->quote($cc_cats);
			}
			else {
				$cc_cats = cot_structure_children('page', $cc_cats, $cc_subs);
				$cc_where = ($cc_cats > 1) ? "page_cat IN ('" . implode("','", $cc_cats) . "')" : "AND page_cat = " . Cot::$db->quote($cc_cats[0]);
			}
		}
    elseif (($cc_mode == 'array_white') || $cc_mode == 'array_black') {
      $what = ($cc_mode == 'array_black') ? "NOT" : "";
      if ($cc_subs == false) {
				$cc_cats = '"'.implode('","', $cc_cats).'"';
	      $cc_where = "page_cat " . $what . " IN ($cc_cats)";
			}
			else {
				$tempcats = array();
				foreach ($cc_cats as $value) {
					$tempcats[] = cot_structure_children('page', $value, true);
				}
				$cc_where = "page_cat " . $what . " IN ('" . cot_implode_all("','", $tempcats) . "')";
			}
    }
		elseif (($cc_mode == 'white') || $cc_mode == 'black') {
			$what = ($cc_mode == 'black') ? "NOT" : "";
			$cc_cats = explode(';', $cc_cats);
			if ($cc_subs == false) {
				$cc_where = "page_cat " . $what . " IN ('" . implode("','", $cc_cats) . "')";
			}
			else {
				$tempcats = array();
				foreach ($cc_cats as $value) {
					$tempcats[] = cot_structure_children('page', $value, true);
				}
				$cc_where = "page_cat " . $what . " IN ('" . cot_implode_all("','", $tempcats) . "')";
			}
		}
	}
	else
		$cc_where = '';

	return $cc_where;
}
