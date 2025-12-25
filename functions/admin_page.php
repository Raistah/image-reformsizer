<?php
function irfs_admin_panel_page()
{
	wp_enqueue_style('irfs-styles', IRFS_PLUGIN_ROOT_REL . '/dist/style.css', array(), "0.1.0", false);
	wp_enqueue_script('irfs-script', IRFS_PLUGIN_ROOT_REL . '/dist/main.js', array(), "0.1.0", false);

	wp_localize_script('irfs-script', 'ImageReformsizer', [
		'root'      => esc_url_raw(rest_url("image-reformsizer/api")),
		'nonce'     => wp_create_nonce('wp_rest'),
	]);
?>
	<div class="irfs-base">
		<div class="notifications absolute top-0 right-0 flex flex-col items-end gap-2 p-2 max-w-100 w-full h-0">
		</div>
		<div>
			<h1 class="mt-10">Image Reformsizer</h1>
			<div class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-6">
				<p>Clear all cache</p>
				<div>
					<button class="button button-primary clear-cache" type="button">Clear</button>
				</div>
			</div>
		</div>
	</div>
<?php
}
