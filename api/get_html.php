<?php
if (!function_exists('irfs_api_get_html')) {
	function irfs_api_get_html(WP_REST_Request $data)
	{
		$response = new WP_REST_Response();

		$body = json_decode($data->get_body());
		$tag = irfs_get_html(
			$body->imageId,
			$body->formats,
			__irfs_prepare_targets($body->targets),
			strlen($body->id) > 0 ? $body->id : null,
			strlen($body->alt) > 0 ? $body->alt : null,
			isset($body->className) && strlen($body->className) > 0 ? $body->className : null,
			strlen($body->imgClass) > 0 ? $body->imgClass : null,
			strlen($body->extraAtts) > 0 ? $body->extraAtts : null,
		);

		$response->set_data(["html" => $tag]);
		return $response;
	}

	function __irfs_prepare_targets(array $targets): array
	{
		$prep_targets = [];
		foreach ($targets as $index => $target) {
			if ($index == 0) {
				$prep_targets[] = $target->width . "," . $target->heigth . "," . $target->vAlign . "," . $target->hAlign;
				continue;
			}
			$prep_target = $target->width . "," . $target->heigth . "," . $target->vAlign . "," . $target->hAlign;

			switch ($target->mediaType) {
				case "min":
					$prep_target .= ",(min-width:" . $target->media . "px)";
					break;
				case "max":
					$prep_target .= ",(max-width:" . $target->media . "px)";
					break;
				case "custom":
					$prep_target .= "," . $target->media;
					break;
			}

			$prep_targets[] = $prep_target;
		}

		return $prep_targets;
	}
}
