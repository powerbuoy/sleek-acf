<?php
namespace Sleek\Acf\Locations;

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
