import { __ } from '@wordpress/i18n';

import { Placeholder, CheckboxControl, RadioControl, TextControl, Button, SelectControl } from '@wordpress/components';
import { InspectorAdvancedControls, InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } from '@wordpress/block-editor';
import { image } from '@wordpress/icons';
import "./editor.scss";
import { useEffect } from 'react';

export default function Edit({ attributes, setAttributes }) {

	useEffect(() => {
		const handler = setTimeout(() => {
			if (attributes.imageId != null) {
				fetch(`${window.wpApiSettings.root}image-reformsizer/api/get-html/`, {
					method: 'POST',
					body: JSON.stringify(attributes),
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.wpApiSettings.nonce,
					}
				})
					.then(r => {
						if (r.status == 200) {
							r.json().then(body => {
								setAttributes({ tag: body.html });
							})
						}
					});
			}
		}, 200);

		return () => {
			clearTimeout(handler);
		};
	}, [attributes]);

	useEffect(() => {
		if (attributes.formats.length == 0) {
			attributes.formats.push('png');
			setAttributes({ primaryFormat: 'png' });
		} else {
			if (!attributes.formats.includes(attributes.primaryFormat)) {
				setAttributes({ primaryFormat: attributes.formats[0] });
				return;
			}
		}
	}, [attributes.formats])
	return (
		<div {...useBlockProps()}>
			<Placeholder
				icon={image}
				label="IRFS Picture Block"
				instructions="Select image and add sources in sidebar"
			>
				<InspectorControls>
					<div class="irfs">
						<div class="image-selector">
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(val) => {
										setAttributes({
											imageId: val.id,
											imageUrl: val.url,
										});
									}}
									allowedTypes={['image']}
									value={attributes.imageId}
									render={({ open }) => (
										<div className="image-selector-wrapper">
											{!attributes.imageUrl ? (
												<Button
													variant="secondary"
													onClick={open}
												>
													{__('Select Image', 'picture-block')}
												</Button>
											) : (
												<>
													<div className="preview-container" onClick={open} style={{ cursor: 'pointer' }}>
														<img src={attributes.imageUrl} alt="Selected" />
													</div>
													<Button
														isDestructive
														variant="link"
														onClick={() => {
															setAttributes({
																imageId: null,
																imageUrl: null,
															});
														}}
													>
														{__('Remove Image', 'picture-block')}
													</Button>
												</>
											)}
										</div>
									)}
								/>
							</MediaUploadCheck>
						</div>

						<div class="formats-col">
							<div class="formats">
								<legend class="label">Active formats</legend>
								{['png', 'webp', 'jpeg', 'avif'].map((format) => (
									<div class="format">
										<CheckboxControl
											label={format}
											checked={typeof attributes.formats !== 'undefined' && attributes.formats.includes(format)}
											onChange={(val) => {
												let index = attributes.formats.indexOf(format);
												if (val && index == -1) {
													let newArray = [...attributes.formats];
													newArray.push(format);
													setAttributes({ formats: newArray });
												} else if (!val && index != -1 && attributes.formats.length > 1) {
													let newArray = [...attributes.formats];
													newArray.splice(index, 1);
													setAttributes({ formats: newArray });
												}
											}}
										/>
									</div>
								))}
							</div>
							<div class="primary-format">
								<RadioControl
									label="primary format"
									options={attributes.formats.map(val => {
										return {
											label: val,
											value: val
										};
									})}
									selected={attributes.primaryFormat}
									onChange={(val) => setAttributes({ primaryFormat: val })}
								/>
							</div>
						</div>

						<div class="targets">
							<div class="targets-list">
								{attributes.targets.map((val, index) => (
									<div class="target">
										{index != 0 && (
											<Button
												isDestructive
												variant="link"
												onClick={() => {
													let newArray = [...attributes.targets];
													newArray.splice(index, 1);
													setAttributes({ targets: newArray });
												}}
											>
												{__('Remove Target', 'picture-block')}
											</Button>
										)}
										<TextControl
											label="width"
											type="number"
											value={attributes.targets[index].width}
											onChange={(val) => {
												let newArray = [...attributes.targets];
												newArray[index].width = val;
												setAttributes({ targets: newArray });
											}}
										/>
										<TextControl
											label="heigth"
											type="number"
											value={attributes.targets[index].heigth}
											onChange={(val) => {
												let newArray = [...attributes.targets];
												newArray[index].heigth = val;
												setAttributes({ targets: newArray });
											}}
										/>
										<SelectControl
											label="Vertical align"
											value={attributes.targets[index].vAlign}
											options={[
												{
													label: 'Start',
													value: 's'
												},
												{
													label: 'Center',
													value: 'c'
												},
												{
													label: 'End',
													value: 'e'
												},
											]}
											onChange={(val) => {
												let newArray = [...attributes.targets];
												newArray[index].vAlign = val;
												setAttributes({ targets: newArray });
											}}
										/>
										<SelectControl
											label="Horizontal align"
											value={attributes.targets[index].hAlign}
											options={[
												{
													label: 'Start',
													value: 's'
												},
												{
													label: 'Center',
													value: 'c'
												},
												{
													label: 'End',
													value: 'e'
												},
											]}
											onChange={(val) => {
												let newArray = [...attributes.targets];
												newArray[index].hAlign = val;
												setAttributes({ targets: newArray });
											}}
										/>
										{index != 0 && (
											<>
												<SelectControl
													label="Media query type"
													value={attributes.targets[index].mediaType}
													options={[
														{
															label: 'Min Width',
															value: 'min'
														},
														{
															label: 'Max Width',
															value: 'max'
														},
														{
															label: 'Custom',
															value: 'custom'
														},
													]}
													onChange={(val) => {
														let newArray = [...attributes.targets];
														newArray[index].mediaType = val;
														setAttributes({ targets: newArray });
													}}
												/>
												<TextControl
													label="Media query"
													type={attributes.targets[index].mediaType == 'custom' ? "text" : "number"}
													value={attributes.targets[index].media}
													onChange={(val) => {
														let newArray = [...attributes.targets];
														newArray[index].media = val;
														setAttributes({ targets: newArray });
													}}
												/>
											</>
										)}
									</div>
								))}
							</div>
							<Button
								variant="secondary"
								onClick={() => {
									let newArray = [...attributes.targets];
									newArray.push({
										width: 1,
										heigth: 1,
										hAlign: 'c',
										vAlign: 'c',
										mediaType: 'min',
										media: '',
									});
									setAttributes({ targets: newArray });
								}}
							>
								{__('Add target', 'picture-block')}
							</Button>
						</div>
					</div>
				</InspectorControls>
				<InspectorAdvancedControls>
					<div class="irfs-advanced">
						<TextControl
							__next40pxDefaultSize
							label="Img Class"
							help="This classes will be added to img tag inside picture tag"
							type="text"
							value={attributes.imgClass}
							onChange={(val) => { setAttributes({ imgClass: val }) }}
						/>
						<TextControl
							__next40pxDefaultSize
							label="id"
							help="CSS id of picture tag"
							type="text"
							value={attributes.id}
							onChange={(val) => { setAttributes({ id: val }) }}
						/>
						<TextControl
							__next40pxDefaultSize
							label="Extra Attributes"
							help={`Here you can place anything from 'loding="lazy"' to 'onclick="this.remove()"'`}
							type="text"
							value={attributes.extraAtts}
							onChange={(val) => { setAttributes({ extraAtts: val }) }}
						/>
					</div>
				</InspectorAdvancedControls>
				<div class="preview" dangerouslySetInnerHTML={{__html: attributes.tag}}/>
			</Placeholder>
		</div>
	);
}
