<?php
namespace Sleek\Acf;

function generate_keys ($fields, $prefix) {
	foreach ($fields as $k => $v) {
		if (is_array($v)) {
			$newPrefix = isset($fields['name']) ? $prefix . '_' . $fields['name'] : $prefix;
			$fields[$k] = generate_keys($v, $newPrefix);
		}
		elseif ($k === 'name' and !isset($group['key'])) {
			$fields['key'] = $prefix . '_' . $fields[$k];
		}
	}

	return $fields;
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
# TODO
add_filter('__acf/fields/flexible_content/layout_title', function ($title, $field, $layout, $i) {
	# Figure out the field name
	$nameBits = explode('_', $layout['name']);
	$fieldName = end($nameBits);
	$fieldName = str_replace('-', '_', $fieldName);
	$newTitle = '<strong>' . $title . '</strong>';

	# See if it has a "title" field
	if ($t = get_sub_field($fieldName . '_title')) {
		$newTitle .= strip_tags(": \"$t\"");
	}

	# Or template
	if ($t = get_sub_field($layout['key'] . '_template')) {
		if ($t === 'SLEEK_ACF_HIDDEN_TEMPLATE') {
			$newTitle .= '(' . __('Hidden', 'sleek') . ')';
		}
		else {
			$newTitle .= ' (' . sprintf(__('Template: "%s"', 'sleek'), __(ucfirst(str_replace(['-', '_'], ' ', basename($t, '.php')))), 'sleek') . ')';
		}
	}

	return $newTitle;
}, 10, 4);

###############################################
# Collapse flexible content fields on page load
# And hide template dropdowns if there's only one template
add_action('acf/input/admin_head', function () {
	?>
	<script>
		(function ($) {
			$(window).load(function () {
				// Collapse all flexible content modules
				$('a[data-name="collapse-layout"]').filter(function () {
					return !$(this).parents('.-collapsed').length && !$(this).parents('.acf-clone').length;
				}).click();

				// Hide templates if only one
				$('div[data-name="template"]').filter(function () {
					return $(this).find('option').length < 2;
				}).hide();
			});
		})(jQuery);
	</script>
	<?php
});
