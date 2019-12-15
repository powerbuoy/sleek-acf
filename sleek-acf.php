<?php
namespace Sleek\Acf;

################################
# Generates a key field for each
# element in array that has a name field
function generate_keys ($fields, $prefix) {
	foreach ($fields as $k => $v) {
		if (is_array($v)) {
			$newPrefix = isset($fields['name']) ? $prefix . '_' . $fields['name'] : $prefix;
			$fields[$k] = generate_keys($v, $newPrefix);
		}
		elseif ($k === 'name' and !isset($fields['key'])) {
			$fields['key'] = $prefix . '_' . $fields[$k];
		}
	}

	return $fields;
}

#####################
# Hide ACF from admin
# to prevent users from adding ACF from there
if (get_theme_support('sleek-hide-acf-admin')) {
	add_filter('acf/settings/show_admin', '__return_false');
}

#########################################
# Include more info in relationship field
add_filter('acf/fields/relationship/result', function ($title, $post, $field, $postId) {
	$postType = get_post_type($post->ID);
	$postTypeObj = get_post_type_object($postType);
	$postTypeLabel = $postTypeObj->labels->singular_name;
	$postTitle = get_the_title($post->ID);
	$excerpt = get_the_excerpt($post->ID);
	$image = has_post_thumbnail($post->ID) ? get_the_post_thumbnail($post->ID, 'post-thumbnail', ['style' => 'width: 16px; height: 16px; vertical-align: middle; margin-right: 8px;']) : '';

	return "<strong>$image$postTitle</strong> ($postTypeLabel)<br><small style=\"display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;\">$excerpt</small>";
}, 10, 4);

################################################
# Hide taxonomy fields on the main taxonomy page
# https://support.advancedcustomfields.com/forums/topic/hide-taxonomy-term-fields-on-the-main-category-page/
add_filter('acf/location/rule_match/taxonomy', function ($match, $rule, $options) {
	if ($rule['param'] === 'taxonomy' and !isset($_GET['tag_ID'])) {
		return false;
	}

	return $match;
}, 20, 3);

##############################
# Nice flexible content titles
add_filter('acf/fields/flexible_content/layout_title', function ($title, $field, $layout, $i) {
	$newTitle = '<strong>' . $title . '</strong>';

	# See if it has a "title" field
	if ($t = get_sub_field($layout['key'] . '_title')) {
		$newTitle .= strip_tags(": \"$t\"");
	}

	# Or template
	if ($t = get_sub_field($layout['key'] . '_template')) {
		if ($t === 'SLEEK_ACF_HIDDEN_TEMPLATE') {
			$newTitle .= '(' . __('Hidden', 'sleek') . ')';
		}
		else {
			$newTitle .= ' (' . sprintf(__('Template: "%s"', 'sleek'), \Sleek\Utils\convert_case($t, 'title')) . ')';
		}
	}

	return $newTitle;
}, 10, 4);

###############################################
# Collapse flexible content fields on page load
add_action('acf/input/admin_head', function () {
	?>
	<script>
		(function ($) {
			$(window).load(function () {
				// Collapse all flexible content modules
				$('a[data-name="collapse-layout"]').filter(function () {
					return !$(this).parents('.-collapsed').length && !$(this).parents('.acf-clone').length;
				}).click();
			});
		})(jQuery);
	</script>
	<?php
});

###########################################
# Add nav_menu_item_depth location ♥️ Simon
add_filter('acf/location/rule_match/nav_menu_item_depth', function ($match, $rule, $options, $field_group) {
	if ($rule['operator'] === '==') {
		$match = ($options['nav_menu_item_depth'] == $rule['value']);
	}

	if ($rule['operator'] === '!=') {
		$match = ($options['nav_menu_item_depth'] != $rule['value']);
	}

	return $match;
}, 10, 4);
