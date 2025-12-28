/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata.name, {
	attributes: {
		tag: {
			type: "string",
			default: '',
		},
		imageId: {
			type: "number",
			default: null,
		},
		imageUrl: {
			type: "string",
			default: null,
		},
		formats: {
			type: "array",
			default: ["png"],
		},
		primaryFormat: {
			type: "string",
			default: "png",
		},
		targets: {
			type: "array",
			default: [{
				width: 200,
				heigth: 200,
				vAlign: 'c',
				hAlign: 'c',
			}],
		},
		id: {
			type: "string",
			default: null,
		},
		alt: {
			type: "string",
			default: null,
		},
		className: {
			type: "string",
			default: null,
		},
		imgClass: {
			type: "string",
			default: null,
		},
		extraAtts: {
			type: "string",
			default: null,
		},
	},
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
} );
