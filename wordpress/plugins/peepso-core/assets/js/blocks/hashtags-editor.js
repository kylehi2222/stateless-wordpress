(function (wp, data) {
	const { hooks, serverSideRender } = wp;
	const { __ } = wp.i18n;
	const { select } = wp.data;
	const { createElement } = wp.element;
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, RadioControl, RangeControl, SelectControl, TextControl } = wp.components;

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

	function configLimit({ attributes, setAttributes }) {
		return createElement(RangeControl, {
			label: __('Limit', 'peepso-core'),
			value: +attributes.limit,
			onChange: value => setAttributes({ limit: +value }),
			min: 1,
			max: 100
		});
	}

	function configDisplay({ attributes, setAttributes }) {
		let options = [
			{ value: 0, label: __('Cloud', 'peepso-core') },
			{ value: 1, label: __('List', 'peepso-core') },
			{ value: 2, label: __('Mixed', 'peepso-core') }
		];

		return createElement(RadioControl, {
			label: __('Display style', 'peepso-core'),
			selected: +attributes.displaystyle,
			onChange: value => setAttributes({ displaystyle: +value }),
			options
		});
	}

	function configSort({ attributes, setAttributes }) {
		let sortBy = createElement(SelectControl, {
			label: __('Sort by', 'peepso-core'),
			value: +attributes.sortby,
			onChange: value => setAttributes({ sortby: +value }),
			options: [
				{ value: 0, label: __('Sorted by name', 'peepso-core') },
				{ value: 1, label: __('Sorted by size', 'peepso-core') }
			]
		});

		let sortOrder = createElement(SelectControl, {
			value: +attributes.sortorder,
			onChange: value => setAttributes({ sortorder: +value }),
			options: [
				{ value: 0, label: __('↑', 'peepso-core') },
				{ value: 1, label: __('↓', 'peepso-core') }
			]
		});

		return [sortBy, sortOrder];
	}

	function configMinSize({ attributes, setAttributes }) {
		return createElement(RangeControl, {
			label: __('Minimum post count', 'peepso-core'),
			value: +attributes.minsize,
			onChange: value => setAttributes({ minsize: +value }),
			min: 0,
			max: 100
		});
	}

	registerBlockType('peepso/hashtags', {
		title: __('PeepSo Hashtags', 'peepso-core'),
		description: __('Show PeepSo hashtags based on a specific settings.', 'peepso-core'),
		category: 'widgets',
		// icon: 'calendar',
		attributes,
		edit(props) {
			let { attributes, setAttributes } = props;

			// Assign block ID.
			if (attributes.__internalWidgetId) {
				setAttributes({ __psBlockId: attributes.__internalWidgetId });
			}

			// Check if block is rendered as a widget, inside a "sidebar".
			const { clientId } = props;
			const parentId = select('core/block-editor').getBlockHierarchyRootClientId(clientId);
			if (parentId !== clientId) {
				const parentAttributes = select('core/block-editor').getBlockAttributes(parentId);
				setAttributes({ __psSidebarId: parentAttributes.id });
			}

			// Compose block settings section.
			let settings = [
				panelize(
					configTitle(props),
					configLimit(props),
					configDisplay(props),
					configSort(props),
					configMinSize(props)
				)
			];

			let controls = createElement(
				InspectorControls,
				null,
				...hooks.applyFilters('peepso_block_settings', settings, props, 'peepso/hashtags')
			);

			// Render content.
			let content = createElement(serverSideRender, {
				block: 'peepso/hashtags',
				attributes: props.attributes
			});

			return createElement('div', null, controls, content);
		},
		save() {
			return null;
		}
	});
})(window.wp, window.peepsoBlockHashtagsEditorData);
