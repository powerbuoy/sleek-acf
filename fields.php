<?php
namespace Sleek\Acf\Fields;

##############
# Redirect URL
# TODO: Convert to custom acf-field-type "redirect_url" (so name can be anything)
# And remove get_theme_support when it's a real field type
add_action('after_setup_theme', function () {
	if (get_theme_support('sleek/acf/fields/redirect_url')) {
		# Make sure the_permalink() points to the redirect URL
		add_filter('the_permalink', function ($url, $postId) {
			if (function_exists('get_field')) {
				$redirectUrl = get_field('redirect_url', $postId);

				if (!empty($redirectUrl)) {
					return $redirectUrl;
				}
			}

			return $url;
		}, 10, 2);

		# Redirect single pages to the redirect URL
		add_action('template_redirect', function () {
			if (is_singular() and function_exists('get_field')) {
				global $post;

				$redirectUrl = get_field('redirect_url', $post->ID);

				if (!empty($redirectUrl)) {
					wp_redirect($redirectUrl, 301);
				}
			}
		}, 10, 1);

		# Remove posts with redirect_url from sitemap
		add_filter('wpseo_exclude_from_sitemap_by_post_ids', function () {
			return get_posts([
				'post_type' => 'any',
				'posts_per_page' => -1,
				'fields' => 'ids',
				'meta_query' => [
					[
						'key' => 'redirect_url',
						'compare' => '!=',
						'value' => ''
					]
				]
			]);
		});
	}
});
