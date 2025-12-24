<?php
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
				irfs_write_log("Image Reformsizer (Error: `: file not found`)\n");
				return [];
			}
		}

		if (count($formats) == 0) {
			irfs_write_log("Image Reformsizer (Error: `: there is no formats specified`)\n");
			return [];
		}
		$formats_string = " -f " . implode(" ", $formats);

		if (count($targets) == 0) {
			irfs_write_log("Image Reformsizer (Error: `: there is no targets specified`)\n");
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
		$output_dir_string = " -o " . IRFS_WORKING_DIR;

		$result = irfs_exec_and_handle(
			__DIR__ . "/bin/image-resizer -w " .
				IRFS_WORKING_DIR . " " .
				$image .
				$url_start_string .
				$output_dir_string .
				$formats_string .
				$targets_string
		);

		return json_decode($result, true);
	}
}
