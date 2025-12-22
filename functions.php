<?php
if (!defined("IRFS_URL_START")) {
	define("IRFS_URL_START", "/wp-content/plugins/image-reformsizer");
}

if (!defined("IRFS_WORKING_DIR")) {
	define("IRFS_WORKING_DIR", __DIR__ . "/");
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

if (!function_exists('irfs_get_html')) {
	/**
	 * @param string|int $image path to image (not url), or id of attachment (recomended)
	 * @param array<"webp"|"png"|"jpeg"|"avif"> $formats list of formats image should be converted to
	 * @param array<array{width: int, height: int, v_align: 's'|'c'|'e', h_align: 's'|'c'|'e', media: string }|string> $targets what sources you want to generate, if you use string use this format: "int,int,'s'|'c'|'e','s'|'c'|'e',string" order as array type, media is not required (will be ignored) for first source. Media should have syntax as in html attribute
	 * @param string|null $id id of picture tag
	 * @param string|null $alt alt text of image
	 * @param string|null $picture_class classes of picture tag
	 * @param string|null $img_class classes of img tag
	 * @param string|null $extra_atts extra attribures to add to img tag (use "" inside string)
	 * @return array
	 */
	function irfs_get_html(
		string|int $image,
		array $formats,
		array $targets,
		string|null $id = null,
		string|null $alt = null,
		string|null $picture_class = null,
		string|null $img_class = null,
		string|null $extra_atts = null,
	): string {
		if (gettype($image) == 'integer') {
			$image = get_attached_file($image);
			if ($image == false) {
				irfs_write_log("Image Reformsizer (Error: `: file not found`)\n");
				return "";
			}
		}

		if (count($formats) == 0) {
			irfs_write_log("Image Reformsizer (Error: `: there is no formats specified`)\n");
			return "";
		}
		$formats_string = " -f " . implode(" ", $formats);

		if (count($targets) == 0) {
			irfs_write_log("Image Reformsizer (Error: `: there is no targets specified`)\n");
			return "";
		}
		$targets_string = " -t";
		$first_key = array_key_first($targets);
		foreach ($targets as $index => $target) {
			if (gettype($target) == 'array') {
				if ($index == $first_key) {
					$targets_string .= " " . str_replace(' ', '', '"' . $target['width'] . ',' . $target['height'] . ',' . $target['v_align'] . ',' . $target['h_align'] . '"');
					continue;
				}
				$targets_string .= " " . str_replace(' ', '', '"' . $target['width'] . ',' . $target['height'] . ',' . $target['v_align'] . ',' . $target['h_align'] . ',' . $target['media'] . '"');
				continue;
			}
			$targets_string .= " " . str_replace(' ', '', '"' . $target . '"');
		}

		$url_start_string = " -u " . IRFS_URL_START;

		$id_string = $id != null ? ' -I "' . $id . '"' : "";
		$alt_string = $alt != null ? ' -a "' . $alt . '"' : "";
		$picture_class_string = $picture_class != null ? ' -p "' . $picture_class . '"' : "";
		$img_class_string = $img_class != null ? ' -i "' . $img_class . '"' : "";
		$extra_atts_string = $extra_atts != null ? " -e '" . str_replace('"', '', $extra_atts) . "'" : "";

		return irfs_exec_and_handle(
			__DIR__ . "/bin/image-resizer -w " .
				IRFS_WORKING_DIR .
				" to-html " .
				$image .
				$url_start_string .
				$id_string .
				$alt_string .
				$picture_class_string .
				$img_class_string .
				$formats_string .
				$targets_string .
				$extra_atts_string
		);
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
			IRFS_WORKING_DIR . "/data.db",
			IRFS_WORKING_DIR . "/data.db-shm",
			IRFS_WORKING_DIR . "/data.db-wal",
			IRFS_WORKING_DIR . "/sources",
		];

		foreach ($files as $file) {
			if (file_exists($file)) {
				if (is_dir($file)) {
					irfs_delete_directory($file);
				} else {
					unlink($file);
				}
			}
		}

		irfs_exec_and_handle(__DIR__ . "/bin/image-resizer -w " . IRFS_WORKING_DIR . " install");
	}
}

function irfs_delete_directory($dir) {
    if (!file_exists($dir)) {
        return true; // Directory doesn't exist, so it's "deleted"
    }

    if (!is_dir($dir)) {
        return unlink($dir); // It's a file, so just delete it
    }

    // Iterate through the directory contents
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            // Recursively call the function for subdirectories
            irfs_delete_directory($path);
        } else {
            // Delete files
            unlink($path);
        }
    }

    // Remove the empty directory
    return rmdir($dir);
}
