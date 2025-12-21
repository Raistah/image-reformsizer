<?php
/*
Plugin Name: Image Reformsizer
Plugin URI: https://github.com/Raistah/image-reformsizer
Description: Allows you generate specific sources of your image including conversion to other image formats
Version: 1.0.0
Requires PHP: 8.2
Author: Raistah
Author URI: https://github.com/Raistah
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
*/

include_once __DIR__ . "/functions.php";

register_activation_hook(__FILE__, 'activate_image_reformsizer');

function activate_image_reformsizer()
{
	$system_name = php_uname("s");
	$system = $system_name . "_" . php_uname("m");

	switch (true) {
		case "FreeBSD_x86_64" == $system:
			irfs_copy_bin(__DIR__ . "/bin/image-resizer-FreeBSD-x86_64", __DIR__ . "/bin/image-resizer");
			irfs_exec_and_handle("chmod u+x " . __DIR__ . "/bin/image-resizer");
			break;
		case "Linux_x86_64" == $system:
			irfs_copy_bin(__DIR__ . "/bin/image-resizer-Linux-musl-x86_64", __DIR__ . "/bin/image-resizer");
			irfs_exec_and_handle("chmod u+x " . __DIR__ . "/bin/image-resizer");
			break;
		case "Linux_aarch64" == $system:
			irfs_copy_bin(__DIR__ . "/bin/image-resizer-Linux-musl-arm64", __DIR__ . "/bin/image-resizer");
			irfs_exec_and_handle("chmod u+x " . __DIR__ . "/bin/image-resizer");
			break;
		case "Windows" == $system_name:
			irfs_copy_bin(__DIR__ . "/bin/image-resizer-Windows-msvc-x86_64", __DIR__ . "/bin/image-resizer");
			break;
		case "Darwin_x86_64" == $system:
			irfs_copy_bin(__DIR__ . "/bin/image-resizer-macOS-x86_64", __DIR__ . "/bin/image-resizer");
			irfs_exec_and_handle("chmod u+x " . __DIR__ . "/bin/image-resizer");
			break;
		case "Darwin_arm64" == $system:
			irfs_copy_bin(__DIR__ . "/bin/image-resizer-macOS-arm64", __DIR__ . "/bin/image-resizer");
			irfs_exec_and_handle("chmod u+x " . __DIR__ . "/bin/image-resizer");
			break;
		default:
			wp_die("Image Reformsizer (Error: `your system not supported yet ($system). Please contact me here: <a href=\"https://github.com/Raistah/image-reformsizer\">https://github.com/Raistah/image-reformsizer</a>`)\n");
			break;
	}

	irfs_exec_and_handle(__DIR__ . "/bin/image-resizer -d " . __DIR__ . "/data.db install");
}

function irfs_copy_bin(string $source, string $dist): void
{
	if (file_exists($source)) {
		if (!copy($source, $dist)) {
			wp_die("Image Reformsizer (Error: `Failed to copy the file($source to $dist).`)\n");
			echo "Error: Failed to copy the file.";
		}
	} else {
		wp_die("Image Reformsizer (Error: `Source file does not exist. $source`)\n");
	}
}

function irfs_exec_and_handle($command): string {
	$output = [];
	$result_code = 0;
	exec($command . " 2>&1", $output, $result_code);
	$result = implode("\n", $output);

	if($result_code != 0) {
		wp_die("Image Reformsizer (" . $result . ")\n");
	}

	return $result;
}

add_shortcode( 'irfs_picture', 'irfs_shortcode' );
function irfs_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'image' => null,
		'formats' => 'png,webp',
		'targets' => '100,100,c,c|150,150,c,c,(min-width:400px)',
		'id' => null,
		'alt' => null,
		'picture_class' => null,
		'img_class' => null,
		'extra_atts' => null
	), $atts, 'irfs_picture' );

	var_dump($atts);

	$formats = explode(',', $atts['formats']);
	$targets = explode('|', $atts['targets']);

	if ($atts['image'] != null) {
		return irfs_get_html(
			intval($atts['image']),
			$formats,
			$targets,
			$atts['id'],
			$atts['alt'],
			$atts['picture_class'],
			$atts['img_class'],
		);
	}

	return "Image Reformsizer (Error: `image not provided`)\n";
}
