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
if (!defined("IRFS_PLUGIN_ROOT")) {
	define("IRFS_PLUGIN_ROOT", __DIR__);
}

if (!defined("IRFS_PLUGIN_ROOT_REL")) {
	if (defined('ABSPATH')) {
		define("IRFS_PLUGIN_ROOT_REL", str_replace(ABSPATH, '/', __DIR__));
	} else {
		define("IRFS_PLUGIN_ROOT_REL", "/wp-content/plugins/image-reformsizer");
	}
}

require_once __DIR__ . "/functions/mod.php";
require_once __DIR__ . "/api/mod.php";
if (file_exists(__DIR__ . "/bin_handle.php")) {
    include __DIR__ . "/bin_handle.php";
}

if (!defined("IRFS_URL_START")) {
	define("IRFS_URL_START", "/wp-content/plugins/image-reformsizer");
}

if (!defined("IRFS_WORKING_DIR")) {
	define("IRFS_WORKING_DIR", __DIR__ . "/");
}

register_activation_hook(__FILE__, 'activate_image_reformsizer');

function activate_image_reformsizer()
{
	$system_name = php_uname("s");
	$system = $system_name . "_" . php_uname("m");
	$bin_handle = "";

	switch (true) {
		case "FreeBSD_x86_64" == $system:
			$bin_handle = __DIR__ . "/bin/image-resizer-FreeBSD-x86_64";
			irfs_exec_and_handle("chmod u+x " . $bin_handle);
			break;
		case "Linux_x86_64" == $system:
			$bin_handle = __DIR__ . "/bin/image-resizer-Linux-musl-x86_64";
			irfs_exec_and_handle("chmod u+x " . $bin_handle);
			break;
		case "Linux_aarch64" == $system:
			$bin_handle = __DIR__ . "/bin/image-resizer-Linux-musl-arm64";
			irfs_exec_and_handle("chmod u+x " . $bin_handle);
			break;
		case  str_contains($system_name, "Windows"):
			$bin_handle = __DIR__ . "/bin/image-resizer-Windows-msvc-x86_64.exe";
			break;
		case "Darwin_x86_64" == $system:
			$bin_handle = __DIR__ . "/bin/image-resizer-macOS-x86_64";
			irfs_exec_and_handle("chmod u+x " . $bin_handle);
			break;
		case "Darwin_arm64" == $system:
			$bin_handle = __DIR__ . "/bin/image-resizer-macOS-arm64";
			irfs_exec_and_handle("chmod u+x " . $bin_handle);
			break;
		default:
			wp_die("Image Reformsizer (Error: `your system not supported yet ($system). Please contact me here: <a href=\"https://github.com/Raistah/image-reformsizer\">https://github.com/Raistah/image-reformsizer</a>`)\n");
			break;
	}
	irfs_create_bin_handle($bin_handle);

	irfs_exec_and_handle($bin_handle . " -w " . __DIR__ . "/ install");
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

function irfs_create_bin_handle(string $source): void
{
	$file_name = "bin_handle.php";
	$content = <<<EOT
	<?php
	if (!defined("IRFS_BIN_HANDLE")) {
		define("IRFS_BIN_HANDLE", "$source");
	}
	EOT;
	if (file_put_contents(IRFS_PLUGIN_ROOT . "/" . $file_name, $content) == false) {
		wp_die("Image Reformsizer (Error: `cannot create file '$file_name'. Check permissions.`)\n");
	}
}

function irfs_exec_and_handle($command): string
{
	$output = [];
	$result_code = 0;
	exec($command . " 2>&1", $output, $result_code);
	$result = implode("\n", $output);

	if ($result_code != 0) {
		wp_die("Image Reformsizer (" . $result . ")\n");
	}

	return $result;
}

add_shortcode('irfs_picture', 'irfs_shortcode');
function irfs_shortcode($atts)
{
	$atts = shortcode_atts(array(
		'image' => null,
		'formats' => 'png,webp',
		'targets' => '100,100,c,c|150,150,c,c,(min-width:400px)',
		'id' => null,
		'alt' => null,
		'picture_class' => null,
		'img_class' => null,
		'extra_atts' => null
	), $atts, 'irfs_picture');

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

add_action('admin_menu', 'my_custom_admin_menu');
function my_custom_admin_menu()
{
	add_management_page(
		'Image Reformsizer',
		'Image Reformsizer',
		'manage_options',
		'image-reformsizer',
		'irfs_admin_panel_page'
	);
}

add_action('rest_api_init', function () {
	register_rest_route('image-reformsizer/api', '/clear-cache/', array(
		'methods' => 'GET',
		'callback' =>  'irfs_api_clear_cache',
		'permission_callback' => 'prefix_admin_permission_check'
	));

	register_rest_route('image-reformsizer/api', '/get-html/', array(
		'methods' => 'POST',
		'callback' =>  'irfs_api_get_html',
		'permission_callback' => 'prefix_admin_permission_check'
	));
});

function prefix_admin_permission_check()
{
	return current_user_can('manage_options');
}

function irfs_register_block()
{
	register_block_type(__DIR__ . '/blocks/build/picture-block');
}
add_action('init', 'irfs_register_block');
