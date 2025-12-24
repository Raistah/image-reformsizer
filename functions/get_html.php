<?php
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
				irfs_write_log("Image Reformsizer (Error: `file not found`)\n");
				return "";
			}
		}

		if (count($formats) == 0) {
			irfs_write_log("Image Reformsizer (Error: `there is no formats specified`)\n");
			return "";
		}
		$formats_string = " -f " . implode(" ", $formats);

		if (count($targets) == 0) {
			irfs_write_log("Image Reformsizer (Error: `there is no targets specified`)\n");
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
