jQuery($ => {
	const { hooks, template } = window.peepso;
	const { rest_url, rest_nonce } = window.peepsodata;

	class PagePhotos extends PsPageAutoload {
		constructor(prefix, container, editorMode) {
			if (container.getAttribute('data-init')) return;
			container.setAttribute('data-init', 1);

			super(prefix, container);

			this._search_params = { limit: 8, page: 1 };
			this._editor_mode = !!editorMode;
		}

		onDocumentLoaded() {
			super.onDocumentLoaded();
			this._search_$ct.on('click', '[data-id=item] a', e => this._onclick(e));

			// Override "no more" message on the block editor view.
			if (this._editor_mode) {
				let message = $('[data-id=block-editor-load-notice]', this._container).text();
				this._search_$nomore.html(message);
			}
		}

		_onclick(e) {
			e.preventDefault();
			e.stopPropagation();

			if (this._editor_mode) return;

			let el = e.currentTarget;
			let id = el.getAttribute('data-pho-id');
			let item = el.closest('[data-id=item]');

			ps_comments.open(id, 'photo', {
				nonav: () => item.parentElement.childElementCount < 2,
				prev: () => this._go_prev(e.currentTarget),
				next: () => this._go_next(e.currentTarget)
			});
		}

		_go_prev(el) {
			el = el.closest('[data-id=item]');
			el = el.previousElementSibling || el.parentElement.lastElementChild;
			el && el.querySelector('a[data-pho-id]').click();
		}

		_go_next(el) {
			el = el.closest('[data-id=item]');
			el = el.nextElementSibling || el.parentElement.firstElementChild;
			el && el.querySelector('a[data-pho-id]').click();
		}

		_search_render_html(data) {
			_.defer(() => {
				this._search_$ct
					.find('.ps-js-beforeloaded')
					.toggleClass('ps-js-beforeloaded loaded');
			});

			let html = '';
			let tmpl = template(this._search_$ct.siblings('[data-id=item-template]').text());
			if (data instanceof Array) {
				html = data.map(photo => tmpl(photo)).join('');
			}

			return html;
		}

		_search_get_items() {
			return this._search_$ct.children('[data-id=item]');
		}

		/**
		 * @param {Object} data
		 * @returns jQuery.Deferred
		 */
		_fetch(data) {
			return $.Deferred(defer => {
				let params = {
					url: `${rest_url}photos`,
					type: 'GET',
					data,
					dataType: 'json',
					beforeSend: xhr => xhr.setRequestHeader('X-WP-Nonce', rest_nonce)
				};

				this._fetch_xhr && this._fetch_xhr.abort();
				this._fetch_xhr = $.ajax(params)
					.fail(() => defer.rejectWith(this))
					.done(json => {
						if (json && json.photos && json.message === 'success') {
							if (this._editor_mode && data.page > 1) {
								defer.rejectWith(this);
							} else {
								defer.resolveWith(this, [json.photos]);
							}
						} else {
							defer.rejectWith(this, [(json && json.message) || null]);
						}
					});
			});
		}
	}

	let $shortcodes = $('[data-id=peepso-photos-shortcode]');
	$shortcodes.each((i, el) => {
		new PagePhotos('.ps-js-photos-standalone', el);
	});

	if (wp && wp.domReady) {
		wp.domReady(() => {
			function replacePlaceholder() {
				let $placeholders = $('[data-id=peepso-photos-shortcode-placeholder]');
				$placeholders.each((i, placeholder) => {
					let el = $(peepsoPhotosBlockData.template).get(0);
					placeholder.replaceWith(el);
					new PagePhotos('.ps-js-photos-standalone', el, true);
				});
			}

			setTimeout(replacePlaceholder, 1000);
			hooks.addAction('block_added', 'peepso_photos', replacePlaceholder);
		});
	}
});
