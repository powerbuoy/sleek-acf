<?php
namespace Sleek\Acf;

require_once __DIR__ . '/cleanup.php';
require_once __DIR__ . '/enhancements.php';
require_once __DIR__ . '/fields.php';
require_once __DIR__ . '/polylang.php';

################################
# Generates a key field for each
# element in array that has a name field
function generate_keys ($fields, $prefix) {
	return \Sleek\Utils\str_replace_in_array('{acf_key}', $prefix, generate_keys_recursive($fields, $prefix));
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
