<?php
namespace Sleek\Acf;

#####################
# Hide ACF from admin
# to prevent users from adding ACF from there
add_action('after_setup_theme', function () {
	if (get_theme_support('sleek/acf/hide_admin')) {
		add_filter('acf/settings/show_admin', '__return_false');
	}
});

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
		$newTitle .= strip_tags(": $t");
	}
	# Or a global_module field
	elseif ($t = get_sub_field($layout['key'] . '_global_module')) {
		$newTitle .= ': ' . get_the_title($t);
	}

	# Or template
	if ($t = get_sub_field($layout['key'] . '_template')) {
		$object = get_sub_field_object($layout['key'] . '_template');
		$templateLabel = $object['choices'][$t];
		$templateLabel = preg_replace('/<img.*?>/', '', $templateLabel);
		$templateLabel = preg_replace('/<small.*?>.*?<\/small>/', '', $templateLabel);

		if ($t === 'SLEEK_ACF_HIDDEN_TEMPLATE') {
			$newTitle .= '(' . __('Hidden', 'sleek') . ')';
		}
		else {
			$newTitle .= ' (' . $templateLabel . ')';
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

##############################################
# Set a reasonable max-width on image previews
add_action('acf/input/admin_head', function () {
	?>
	<style>
		.acf-image-uploader .image-wrap img {
			max-width: 20rem;
		}

		#side-sortables .acf-image-uploader .image-wrap img {
			max-width: 100%;
		}
	</style>
	<?php
});

####################
# Remove group label
add_action('admin_head', function () {
	?>
	<style>
		div.sleek-module-group > div.acf-label:first-child {
			display:none;
		}
	</style>
	<?php
});
