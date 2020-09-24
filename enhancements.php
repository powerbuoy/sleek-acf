<?php
namespace Sleek\Acf;

###########################################
# Add nav_menu_item_depth location ♥️ Simon
add_filter('acf/location/rule_match/nav_menu_item_depth', function ($match, $rule, $options, $field_group) {
	if (isset($options['nav_menu_item_depth']) and isset($rule['operator']) and isset($rule['value'])) {
		if ($rule['operator'] === '==') {
			$match = ($options['nav_menu_item_depth'] == $rule['value']);
		}
		elseif ($rule['operator'] === '!=') {
			$match = ($options['nav_menu_item_depth'] != $rule['value']);
		}
		elseif ($rule['operator'] === '>') {
			$match = ($options['nav_menu_item_depth'] > $rule['value']);
		}
		elseif ($rule['operator'] === '<') {
			$match = ($options['nav_menu_item_depth'] < $rule['value']);
		}
		elseif ($rule['operator'] === '>=') {
			$match = ($options['nav_menu_item_depth'] >= $rule['value']);
		}
		elseif ($rule['operator'] === '<=') {
			$match = ($options['nav_menu_item_depth'] <= $rule['value']);
		}

		return $match;
	}

	return false;
}, 10, 4);

##########################################################
# Add 'return_format' => 'array' to textarea-field ♥️ Simon
add_filter('acf/format_value/type=textarea', function($value, $post_id, $field) {
	if (isset($field['return_format']) and $field['return_format'] === 'array' and !empty($value)) {
		$value = explode("\n", $value);
		$value = array_map('trim', $value);
	}

	return $value;
}, 10, 3);

################################
# Simple WYSIWYG toolbar ♥️ Simon
add_filter('acf/fields/wysiwyg/toolbars' , function ($toolbars) {
	$toolbars['Simple'][1] = ['bold', 'italic', 'underline', 'link', 'undo', 'redo'];

	return $toolbars;
});