<?php
echo irfs_get_html(
	$attributes["imageId"],
	isset($attributes["formats"]) ? $attributes["formats"] : ["png"],
	isset($attributes["targets"]) ? $attributes["targets"] : [[
		"width" => 200,
		"height" => 200,
		"v_align" => "c",
		"h_align" => "c",
	]],
	id: isset($attributes["id"]) ? $attributes["id"] : null,
	alt: isset($attributes["alt"]) ? $attributes["alt"] : null,
	picture_class: isset($attributes["className"]) ? $attributes["className"] : null,
	img_class: isset($attributes["imgClass"]) ? $attributes["imgClass"] : null,
	extra_atts: isset($attributes["extraAtts"]) ? $attributes["extraAtts"] : null
);
