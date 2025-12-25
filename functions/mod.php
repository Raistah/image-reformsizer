<?php
require_once __DIR__ . "/get_html.php";
require_once __DIR__ . "/get_array.php";
require_once __DIR__ . "/admin_page.php";

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

		irfs_exec_and_handle(IRFS_PLUGIN_ROOT . "/bin/image-resizer -w " . IRFS_WORKING_DIR . " install");
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
