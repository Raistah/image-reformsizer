# Image Reformsizer
This is pretty simple plugin designed to make developers (and probably content managers too) life easier by automating process of resizing/croping and converting images to different formats.

How it works, plugin have two main functions irfs_get_array and irfs_get_html, irfs_get_array - returns array of sources and you can do whatever you want with it, irfs_get_html - returns picture tag with all information filled.

Here is more detailed info:

## irfs_get_array

**code example:**
```php
var_dump(
	irfs_get_array(
		"wp-content/plugins/image-reformsizer/example-image.png",
		["png", "webp"],
		[
			[ // you can pass target as array with specific keys
				"width" => 100,
				"height" => 200,
				"h_align" => "s",
				"v_align" => "s",
			],
			"300,200,e,e", // or as string
		],
	)
);
```

**output:**
```
array (size=2)
  0 => 
    array (size=2)
      0 => 
        array (size=6)
          'path' => string '/wp-content/plugins/image-reformsizer/sources/example-image_100x200_s-s.png' (length=75)
          'width' => int 100
          'height' => int 200
          'v_align' => string 'Start' (length=5)
          'h_align' => string 'Start' (length=5)
          'format' => string 'PNG' (length=3)
      1 => 
        array (size=6)
          'path' => string '/wp-content/plugins/image-reformsizer/sources/example-image_100x200_s-s.webp' (length=76)
          'width' => int 100
          'height' => int 200
          'v_align' => string 'Start' (length=5)
          'h_align' => string 'Start' (length=5)
          'format' => string 'WEBP' (length=4)
  1 => 
    array (size=2)
      0 => 
        array (size=6)
          'path' => string '/wp-content/plugins/image-reformsizer/sources/example-image_300x200_e-e.png' (length=75)
          'width' => int 300
          'height' => int 200
          'v_align' => string 'End' (length=3)
          'h_align' => string 'End' (length=3)
          'format' => string 'PNG' (length=3)
      1 => 
        array (size=6)
          'path' => string '/wp-content/plugins/image-reformsizer/sources/example-image_300x200_e-e.webp' (length=76)
          'width' => int 300
          'height' => int 200
          'v_align' => string 'End' (length=3)
          'h_align' => string 'End' (length=3)
          'format' => string 'WEBP' (length=4)
```

## irfs_get_html

```php
echo irfs_get_html(
	"wp-content/plugins/image-reformsizer/example-image.png",
	["png", "webp"],
	[
		[
			"width" => 100,
			"height" => 200,
			"h_align" => "s",
			"v_align" => "s",
		],
		"300,200,e,e,(min-width: 200px)",
		"1000,500,c,c,(min-width: 500px)",
	],
	id: "glory-to-ukraine",
	alt: "this is alt",
	picture_class: "p-10 mb-10",
	img_class: "block",
	extra_atts: 'loading="lazy"'
);
```

**output:**
```html
<picture id="heheh" class="p-10 mb-10">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_1000x500_c-c.jpg" width="1000" height="500" type="image/jpeg" media="(min-width:500px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_1000x500_c-c.avif" width="1000" height="500" type="image/avif" media="(min-width:500px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_1000x500_c-c.webp" width="1000" height="500" type="image/webp" media="(min-width:500px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_1000x500_c-c.png" width="1000" height="500" type="image/png" media="(min-width:500px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_900x600_c-c.jpg" width="900" height="600" type="image/jpeg" media="(min-width:400px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_900x600_c-c.avif" width="900" height="600" type="image/avif" media="(min-width:400px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_900x600_c-c.webp" width="900" height="600" type="image/webp" media="(min-width:400px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_900x600_c-c.png" width="900" height="600" type="image/png" media="(min-width:400px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x600_s-s.jpg" width="300" height="600" type="image/jpeg" media="(min-width:300px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x600_s-s.avif" width="300" height="600" type="image/avif" media="(min-width:300px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x600_s-s.webp" width="300" height="600" type="image/webp" media="(min-width:300px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x600_s-s.png" width="300" height="600" type="image/png" media="(min-width:300px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x200_e-e.jpg" width="300" height="200" type="image/jpeg" media="(min-width:200px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x200_e-e.avif" width="300" height="200" type="image/avif" media="(min-width:200px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x200_e-e.webp" width="300" height="200" type="image/webp" media="(min-width:200px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_300x200_e-e.png" width="300" height="200" type="image/png" media="(min-width:200px)">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_100x200_s-s.jpg" width="100" height="200" type="image/jpeg">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_100x200_s-s.avif" width="100" height="200" type="image/avif">
	<source srcset="/wp-content/plugins/image-reformsizer/sources/example-image_100x200_s-s.webp" width="100" height="200" type="image/webp">
	<img class="p-10 mb-10" src="/wp-content/plugins/image-reformsizer/sources/example-image_100x200_s-s.png" width="100" height="200" alt="this is alt" loading="lazy">
</picture>
```
This example is exaggerated (or maybe not -_-), but nevertheless, you can imagine how much time it saves.

## irfs_clear_cache

This is internal function, it cleans all the generated sources and wipes a database (sqlite, not your main db). You can use it for automatic cleaning with wp cron for example.

## shortcode

Also you can use shortcode to generate picture tag like irfs_get_html do.

**example:**
```php
echo do_shortcode('[irfs_picture image="8" formats="png,webp,avif,jpeg" targets="1000,200,c,c|300,200,e,e,(min-width: 200px)" id="hehe" alt="this is alt" picture_class="block" extra_atts=\'loading="lazy"\']');
```

Currently it accepts only image id, but i will probably change it in the future.
