(function (wp, data) {
	const { hooks, serverSideRender } = wp;
	const { __ } = wp.i18n;
	const { createElement } = wp.element;
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, SelectControl } = wp.components;

	// Define block attributes.
	const { attributes } = data;

	function panelize(...controls) {
		return createElement(
			PanelBody,
			{ title: __('General Settings', 'peepso-core') },
			...controls
		);
	}

	function configTitle({ attributes, setAttributes }) {
		return createElement(TextControl, {
			label: __('Title', 'peepso-core'),
			value: attributes.title,
			onChange: value => setAttributes({ title: value })
		});
	}

	function configViewOption({ attributes, setAttributes }) {
		return createElement(SelectControl, {
			label: __('View option', 'peepso-core'),
			value: attributes.view_option,
			onChange: value => setAttributes({ view_option: value }),
			options: [
				{ value: 'vertical', label: __('Vertical', 'peepso-core') },
				{ value: 'horizontal', label: __('Horizontal', 'peepso-core') }
			]
		});
	}

	registerBlockType('peepso/login', {
		title: __('PeepSo Login', 'peepso-core'),
		description: __('Show PeepSo login form.', 'peepso-core'),
		category: 'widgets',
		attributes,
		edit(props) {
			// Assign timestamp if necessary for ID and caching purpose.
			let { attributes, setAttributes } = props;
			if (!+attributes.timestamp) {
				setAttributes({ timestamp: new Date().getTime() });
			}

			// Compose block settings section.
			let settings = [panelize(configTitle(props), configViewOption(props))];

			let controls = createElement(
				InspectorControls,
				null,
				...hooks.applyFilters('peepso_block_settings', settings, props, 'peepso/login')
			);

			// Render content.
			let content = createElement(serverSideRender, {
				block: 'peepso/login',
				attributes: props.attributes
			});

			return createElement('div', null, controls, content);
		},
		save() {
			return null;
		}
	});
})(window.wp, window.peepsoBlockLoginEditorData);
