(function (wp, data) {
	const { hooks, serverSideRender } = wp;
	const { __ } = wp.i18n;
	const { createElement } = wp.element;
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, ToggleControl, SelectControl } = wp.components;

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

	function configGuestBehavior({ attributes, setAttributes }) {
		return createElement(SelectControl, {
			label: __('Guest view', 'peepso-core'),
			value: attributes.guest_behavior,
			onChange: value => setAttributes({ guest_behavior: value }),
			options: [
				{ value: 'login', label: __('Log-in form', 'peepso-core') },
				{ value: 'hide', label: __('Hide', 'peepso-core') }
			]
		});
	}

	function configNotifications({ attributes, setAttributes }) {
		return createElement(ToggleControl, {
			label: __('Show notifications', 'peepso-core'),
			checked: +attributes.show_notifications,
			onChange: value => setAttributes({ show_notifications: +value })
		});
	}

	function configLinks({ attributes, setAttributes }) {
		return createElement(ToggleControl, {
			label: __('Show community links', 'peepso-core'),
			checked: +attributes.show_community_links,
			onChange: value => setAttributes({ show_community_links: +value })
		});
	}

	function configCover({ attributes, setAttributes }) {
		return createElement(ToggleControl, {
			label: __('Show cover', 'peepso-core'),
			checked: +attributes.show_cover,
			onChange: value => setAttributes({ show_cover: +value })
		});
	}

	function configShowInProfile({ attributes, setAttributes }) {
		return createElement(SelectControl, {
			label: __('Show on the Profile page', 'peepso-core'),
			value: +attributes.show_in_profile,
			onChange: value => setAttributes({ show_in_profile: +value }),
			options: [
				{ value: 0, label: __('Never', 'peepso-core') },
				{ value: 1, label: __('When on my profile', 'peepso-core') },
				{ value: 2, label: __('When not on my profile', 'peepso-core') },
				{ value: 3, label: __('Always', 'peepso-core') }
			]
		});
	}

	registerBlockType('peepso/profile', {
		title: __('PeepSo Profile', 'peepso-core'),
		description: __(
			'Show user profile information based on the following settings.',
			'peepso-core'
		),
		category: 'widgets',
		attributes,
		edit(props) {
			// Assign timestamp if necessary for ID and caching purpose.
			let { attributes, setAttributes } = props;
			if (!+attributes.timestamp) {
				setAttributes({ timestamp: new Date().getTime() });
			}

			// Compose block settings section.
			let settings = [
				panelize(
					configTitle(props),
					configGuestBehavior(props),
					configNotifications(props),
					configLinks(props),
					configCover(props),
					configShowInProfile(props)
				)
			];

			let controls = createElement(
				InspectorControls,
				null,
				...hooks.applyFilters('peepso_block_settings', settings, props, 'peepso/profile')
			);

			// Render content.
			let content = createElement(serverSideRender, {
				block: 'peepso/profile',
				attributes: props.attributes
			});

			return createElement('div', null, controls, content);
		},
		save() {
			return null;
		}
	});
})(window.wp, window.peepsoBlockProfileEditorData);
