wp.domReady(function () {
	const { __ } = wp.i18n;
	const { createElement } = wp.element;
	const { registerBlockType } = wp.blocks;
	const { hooks } = peepso;

	registerBlockType('peepso-photos/peepso-photos-block', {
		title: __('PeepSo Photos', 'peepso-photos'),
		icon: 'images-alt',
		category: 'widgets',
		keywords: [__('PeepSo Photos', 'peepso-photos')],
		edit(props) {
			setTimeout(() => hooks.doAction('block_added'), 400);
			return createElement('div', { 'data-id': 'peepso-photos-shortcode-placeholder' });
		},
		save(props) {
			return null;
		}
	});
});
