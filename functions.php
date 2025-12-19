<?php
if (!defined("IRFS_URL_START")) {
	define("IRFS_URL_START", "/wp-content/plugins/image-reformsizer");
}

if (!defined("IRFS_OUTPUT_DIR")) {
	define("IRFS_OUTPUT_DIR", __DIR__ . "/output");
}

if (!function_exists('irfs_get_array')) {
	/**
	 * @param string|int $image path to image (not url), or id of attachment (recomended)
	 * @param array<"webp"|"png"|"jpeg"|"avif"> $formats list of formats image should be converted to
	 * @param array<array{width: int, height: int, v_align: 's'|'c'|'e', h_align: 's'|'c'|'e' }|string> $targets what sources you want to generate, if you use string use this format: "int,int,'s'|'c'|'e','s'|'c'|'e'" order as array type
	 * @return array
	 */
	function irfs_get_array(
		string|int $image,
		array $formats,
		array $targets,
	): array {
		if (gettype($image) == 'integer') {
			$image = get_attached_file($image);
			if ($image == false) {
				irfs_write_log("Image Reformsizer Error: file not found");
				return [];
			}
		}

		if (count($formats) == 0) {
			irfs_write_log("Image Reformsizer Error: there is no formats specified");
			return [];
		}
		$formats_string = " -f " . implode(" ", $formats);

		if (count($targets) == 0) {
			irfs_write_log("Image Reformsizer Error: there is no targets specified");
			return [];
		}
		$targets_string = " -t";
		foreach ($targets as $target) {
			if (gettype($target) == 'array') {
				$targets_string .= " " . str_replace(' ', '', '"' . $target['width'] . ',' . $target['height'] . ',' . $target['v_align'] . ',' . $target['h_align'] . '"');
				continue;
			}
			$targets_string .= " " . str_replace(' ', '', '"' . $target . '"');
		}

		$url_start_string = " -u " . IRFS_URL_START;
		$output_dir_string = " -o " . IRFS_OUTPUT_DIR;

		$result = irfs_exec_and_handle(
			__DIR__ . "/bin/image-resizer -d " .
				__DIR__ . "/data.db to-vec " .
				$image .
				$url_start_string .
				$output_dir_string .
				$formats_string .
				$targets_string
		);

		return json_decode($result, true);
	}
}

if (!function_exists('irfs_get_html')) {
	/**
	 * @param string|int $image path to image (not url), or id of attachment (recomended)
	 * @param array<"webp"|"png"|"jpeg"|"avif"> $formats list of formats image should be converted to
	 * @param array<array{width: int, height: int, v_align: 's'|'c'|'e', h_align: 's'|'c'|'e' }|string> $targets what sources you want to generate, if you use string use this format: "int,int,'s'|'c'|'e','s'|'c'|'e'" order as array type
	 * @param string|null $alt alt text of image
	 * @param string|null $picture_class classes of picture tag
	 * @param string|null $img_class classes of img tag
	 * @return array
	 */
	function irfs_get_html(
		string|int $image,
		array $formats,
		array $targets,
		string|null $alt = null,
		string|null $picture_class = null,
		string|null $img_class = null,
	) : string {

		return "";
	}
}

if (! function_exists('irfs_write_log')) {
	function irfs_write_log($log)
	{
		if (true === WP_DEBUG) {
			if (is_array($log) || is_object($log)) {
				error_log(print_r($log, true));
			} else {
				error_log($log);
			}
		}
	}
}

if (! function_exists('irfs_clear_cache')) {
	function irfs_clear_cache()
	{
		$files = [
			__DIR__ . "/data.db",
			__DIR__ . "/data.db-shm",
			__DIR__ . "/data.db-wal",
			IRFS_OUTPUT_DIR,
		];

		foreach ($files as $file) {
			if (file_exists($file)) {
				if (is_dir($file)) {
					rmdir($file);
				} else {
					unlink($file);
				}
			}
		}
	}
}
