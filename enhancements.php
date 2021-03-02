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

#########################################
# Include more info in relationship field
# TODO: ACF now has support for featured_image at least... should remove this in the future?
add_filter('acf/fields/relationship/result', function ($title, $post, $field, $postId) {
	$postType = get_post_type($post->ID);
	$postTypeObj = get_post_type_object($postType);
	$postTypeLabel = $postTypeObj->labels->singular_name;
	$postTitle = get_the_title($post->ID);
	$excerpt = get_the_excerpt($post->ID);
	$image = has_post_thumbnail($post->ID) ? get_the_post_thumbnail($post->ID, 'post-thumbnail', ['style' => 'width: auto; height: 34px; float: left; vertical-align: middle; margin-right: 8px;']) : '';

	return "<div style=\"overflow: hidden\"><strong>$image$postTitle</strong> ($postTypeLabel)<br><small style=\"display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;\">$excerpt</small></div>";
}, 10, 4);
