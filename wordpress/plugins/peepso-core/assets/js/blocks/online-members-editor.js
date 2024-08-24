(function (wp, data) {
	const { hooks, serverSideRender } = wp;
	const { __ } = wp.i18n;
	const { select } = wp.data;
	const { createElement } = wp.element;
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, ToggleControl, RangeControl } = wp.components;

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

	function configHideEmpty({ attributes, setAttributes }) {
		return createElement(ToggleControl, {
			label: __('Hide when empty', 'peepso-core'),
			checked: +attributes.hide_empty,
			onChange: value => setAttributes({ hide_empty: +value })
		});
	}

	function configShowTotalOnline({ attributes, setAttributes }) {
		return createElement(ToggleControl, {
			label: __('Show total online members count', 'peepso-core'),
			checked: +attributes.show_total_online,
			onChange: value => setAttributes({ show_total_online: +value })
		});
	}

	function configShowTotalMembers({ attributes, setAttributes }) {
		return createElement(ToggleControl, {
			label: __('Show total members count', 'peepso-core'),
			checked: +attributes.show_total_members,
			onChange: value => setAttributes({ show_total_members: +value })
		});
	}

	registerBlockType('peepso/online-members', {
		title: __('PeepSo Online Members', 'peepso-core'),
		description: __('Show online members based on the following settings.', 'peepso-core'),
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
					configHideEmpty(props),
					configShowTotalOnline(props),
					configShowTotalMembers(props)
				)
			];

			let controls = createElement(
				InspectorControls,
				null,
				...hooks.applyFilters(
					'peepso_block_settings',
					settings,
					props,
					'peepso/online-members'
				)
			);

			// Render content.
			let content = createElement(serverSideRender, {
				block: 'peepso/online-members',
				attributes: props.attributes
			});

			return createElement('div', null, controls, content);
		},
		save() {
			return null;
		}
	});
})(window.wp, window.peepsoBlockOnlineMembersEditorData);
