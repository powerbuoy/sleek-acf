<?php
# Add support for polylang
if (function_exists('pll_current_language')) {
	add_filter('acf/settings/current_language', function ($language) {
		return pll_current_language();
	});

	add_filter('acf/settings/default_language', function ($language) {
		return pll_default_language();
	});
}
