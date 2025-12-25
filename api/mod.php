<?php

if (!function_exists('irfs_api_clear_cache')) {
	function irfs_api_clear_cache(WP_REST_Request $data) {
		irfs_clear_cache();
		return true;
	}
}
