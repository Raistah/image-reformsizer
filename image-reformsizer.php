<?php
/*
Plugin Name: Image Reformsizer
Plugin URI: https://github.com/Raistah/image-reformsizer
Description: Allows you generate specific sources of your image including conversion to other image formats
Version: 1.0.0
Requires PHP: 8.2
Author: Your Name
Author URI: https://github.com/Raistah
License: Apache License 2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0
*/

register_activation_hook(__FILE__, 'activate_image_reformsizer');

function activate_image_reformsizer()
{
	$system_name = php_uname("s");
	$system = $system_name . "_" . php_uname("m");

	switch (true) {
		case "FreeBSD_x86_64" == $system:
			copy_bin(__DIR__ . "/bin/image-resizer-FreeBSD-x86_64", __DIR__ . "/bin/image-resizer");
			break;
		case "Linux_x86_64" == $system:
			copy_bin(__DIR__ . "/bin/image-resizer-Linux-musl-x86_64", __DIR__ . "/bin/image-resizer");
			break;
		case "Linux_aarch64" == $system:
			copy_bin(__DIR__ . "/bin/image-resizer-Linux-musl-arm64", __DIR__ . "/bin/image-resizer");
			break;
		case "Windows" == $system_name:
			copy_bin(__DIR__ . "/bin/image-resizer-Windows-msvc-x86_64", __DIR__ . "/bin/image-resizer");
			break;
		case "Darwin_x86_64" == $system:
			copy_bin(__DIR__ . "/bin/image-resizer-macOS-x86_64", __DIR__ . "/bin/image-resizer");
			break;
		case "Darwin_arm64" == $system:
			copy_bin(__DIR__ . "/bin/image-resizer-macOS-arm64", __DIR__ . "/bin/image-resizer");
			break;
		default:
			wp_die("Error: your system not supported yet ($system). Please contact me here: <a href=\"https://github.com/Raistah/image-reformsizer\">https://github.com/Raistah/image-reformsizer</a>");
			break;
	}
}

function copy_bin(string $source, string $dist): void
{
	if (file_exists($source)) {
		if (!copy($source, $dist)) {
			wp_die("Error: Failed to copy the file($source to $dist).");
			echo "Error: Failed to copy the file.";
		}
	} else {
		wp_die("Error: Source file does not exist. $source");
	}
}
