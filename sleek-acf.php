<?php
namespace Sleek\Acf;

require_once __DIR__ . '/cleanup.php';
require_once __DIR__ . '/enhancements.php';
require_once __DIR__ . '/fields.php';
require_once __DIR__ . '/polylang.php';

################################
# Generates a key field for each
# element in array that has a name field
function generate_keys ($fields, $prefix, $origPrefix = null) {
	if ($origPrefix === null) {
		$origPrefix = $prefix;
	}

	foreach ($fields as $k => $v) {
		if (is_array($v)) {
			$newPrefix = isset($fields['name']) ? $prefix . '_' . $fields['name'] : $prefix;
			$fields[$k] = generate_keys($v, $newPrefix, $origPrefix);
		}
		elseif ($k === 'name' and !isset($fields['key'])) {
			$fields['key'] = $prefix . '_' . $fields[$k];
		}
		elseif (is_string($v)) {
			$fields[$k] = str_replace('{acf_key}', $origPrefix, $v);
		}
	}

	return $fields;
}

# ChatGPT optimized version (pretty much same speed and a little more complex, so using the above instead, but kept for reference)
function _generate_keys ($data, string $currentPrefix, ?string $origPrefix = null) {
	# NOTE: Sometimes $data is a string for some reason, not sure why :P
	# Had to remove array $data and : array from function :/
	if (!is_array($data)) {
		return $data;
	}

	if ($origPrefix === null) {
		$origPrefix = $currentPrefix;
	}

	// Om arrayen är en lista (numeriskt indexerad) bearbetar vi varje element med samma prefix
	if (array_keys($data) === range(0, count($data) - 1)) {
		foreach ($data as $i => $item) {
			$data[$i] = generate_keys($item, $currentPrefix, $origPrefix);
		}
		return $data;
	}

	// Om vi har en associativ array och den innehåller en "name" uppdaterar vi vårt cumulativa prefix
	if (isset($data['name'])) {
		$myPrefix = $currentPrefix . '_' . $data['name'];
		if (!isset($data['key'])) {
			$data['key'] = $myPrefix;
		}
	} else {
		$myPrefix = $currentPrefix;
	}

	// Gå igenom varje nyckel i arrayen
	foreach ($data as $key => $value) {
		if (is_string($value)) {
			// Ersätt alltid "{acf_key}" med det ursprungliga prefixet
			$data[$key] = str_replace('{acf_key}', $origPrefix, $value);
		} elseif (is_array($value)) {
			// Recursera in i alla arrayer med det uppdaterade (cumulativa) prefixet
			$data[$key] = generate_keys($value, $myPrefix, $origPrefix);
		}
	}

	return $data;
}

# NOTE: Old version kept for reference
function __generate_keys ($fields, $prefix) {
	return \Sleek\Utils\str_replace_in_array('{acf_key}', $prefix, generate_keys_recursive($fields, $prefix));

	// NOTE: These transients became HUGE and slowed the database down so commented for now
	// $key = 'sleek_generate_keys_' . md5(json_encode($fields) . $prefix);

	// if ($val = get_transient($key)) {
	// 	return $val;
	// }
	// else {
	// 	$val = \Sleek\Utils\str_replace_in_array('{acf_key}', $prefix, generate_keys_recursive($fields, $prefix));

	// 	set_transient($key, $val);

	// 	return $val;
	// }
}

###################################
# Helper function for generate_keys
function generate_keys_recursive ($fields, $prefix) {
	foreach ($fields as $k => $v) {
		if (is_array($v)) {
			$newPrefix = isset($fields['name']) ? $prefix . '_' . $fields['name'] : $prefix;
			$fields[$k] = generate_keys_recursive($v, $newPrefix);
		}
		elseif ($k === 'name' and !isset($fields['key'])) {
			$fields['key'] = $prefix . '_' . $fields[$k];
		}
	}

	return $fields;
}
