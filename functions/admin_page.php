<?php
function irfs_admin_panel_page()
{
	wp_enqueue_style('irfs-styles', IRFS_PLUGIN_ROOT_REL . '/dist/style.css', array(), "0.1.0", false);
	wp_enqueue_script('irfs-script', IRFS_PLUGIN_ROOT_REL . '/dist/main.js', array(), "0.1.0", false);
?>
	<div class="irfs-base">
		<div>
			<h1 class="mt-10">Image Reformsizer</h1>
			<div class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-6">
				<p>Clear all cache</p>
				<div>
					<button class="button button-primary" type="button">Clear</button>
				</div>
			</div>
		</div>
	</div>
<?php
}
