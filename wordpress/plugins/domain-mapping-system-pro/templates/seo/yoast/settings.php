<?php

use DMS\Includes\Integrations\Seo\Yoast\Seo_Yoast;

if ( $this instanceof Seo_Yoast ) {
	?>
    <div class="dmsy">
		<?php
		if ( ! empty( $data ) ) {
			if ( empty( $this->get_options_per_domain() ) || ! $this->fs->can_use_premium_code__premium_only() ) {
				$popupExist = true;
				if ( ! $this->fs->can_use_premium_code__premium_only() ) {
					?>
                    <div class="dmsy-upgrader">
                        <p style="padding: 0 16px">
							<?= sprintf( __( 'To customize Yoast meta content per domain, please %s', 'domain-mapping-system' ),
								' <a class="upgrade" href="' . $this->fs->get_upgrade_url() . '">' . __( 'Upgrade', 'domain-mapping-system' ) . '&#8594;</a>' ) ?>
                        </p>
                    </div>
					<?php
				} else {
					?>
                    <div class="dmsy-upgrader">
                        <p style="padding: 0 16px">
							<?= sprintf( __( 'Please enable Duplicate SEO Options setting in %s.', 'domain-mapping-system' ),
								' <a class="upgrade" href="' . admin_url() . '?page=domain-mapping-system">' . __( 'Domain Mapping System settings', 'domain-mapping-system' ) . '&#8594;</a>' ) ?>
                        </p>
                    </div>
					<?php
				}
			} ?>

			<?php foreach ( $data as $key => $item ) :
				$hostAndPath = $item['host_path'];
				$meta_data = $item['meta_data'];
				?>
                <div class="dmsy-accordion closed <?= ! empty( $popupExist ) ? 'popup-behind' : '' ?>">
                    <div class="dmsy-accordion-header">
                        <button class="dmsy-accordion-toggle">
                            <span><?= esc_html( $hostAndPath ); ?></span>
                            <svg role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M7.41,8.59L12,13.17l4.59-4.58L18,10l-6,6l-6-6L7.41,8.59z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="dmsy-accordion-body">
                        <div class="dmsy-accordion-body-in">
                            <div class="dmsy-tabs">
                                <ul role="tablist" class="dmsy-tabs-in" aria-label="Yoast SEO">
                                    <!-- add 'active' to li for active tab-->
                                    <li role="presentation" class="dmsy-tabs-item active">
                                        <a role="tab" href="#dmsy-seo-<?= esc_attr( $key ) ?>"
                                           aria-selected="false" tabindex="-1">
                                            <span><?= __( 'SEO', 'domain-mapping-system' ) ?></span>
                                        </a>
                                    </li>
                                    <li role="presentation" class="dmsy-tabs-item">
                                        <a role="tab" href="#dmsy-social-<?= esc_attr( $key ) ?>" aria-selected="false" tabindex="-1">
                                            <span class="dmsy-tabs-icon dashicons dashicons-share"></span>
                                            <span><?= __( 'Social', 'domain-mapping-system' ) ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- add 'active' to tab for active tab-->
                            <div role="tabpanel" id="dmsy-seo-<?= esc_attr( $key ) ?>" aria-labelledby="dmsy-tabs" tabindex="0" class="dmsy-tabs-body active">
                                <div class="dmsy-tabs-row">
                                    <label class="dmsy-tabs-input-holder">
                                        <span class="dmsy-tabs-input-label"><?= __( 'Title', 'domain-mapping-system' ) ?></span>
                                        <input class="dmsy-tabs-input"
                                               name="<?= Seo_Yoast::$form_prefix ?>title<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                               type="text"
                                               value="<?= esc_attr( $meta_data['title'] ?? '' ) ?>"
                                               placeholder="Seo title">
                                    </label>
                                </div>
                                <div class="dmsy-tabs-row">
                                    <label class="dmsy-tabs-input-holder">
                                        <span class="dmsy-tabs-input-label"><?= __( 'Description', 'domain-mapping-system' ) ?></span>
                                        <input class="dmsy-tabs-input"
                                               name="<?= Seo_Yoast::$form_prefix ?>description<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                               type="text"
                                               value="<?= esc_attr( $meta_data['description'] ?? '' ) ?>"
                                               placeholder="Seo description">
                                    </label>
                                </div>
                                <div class="dmsy-tabs-row">
                                    <label class="dmsy-tabs-input-holder">
                                        <span class="dmsy-tabs-input-label"><?= __( 'Keywords', 'domain-mapping-system' ) ?></span>
                                        <input class="dmsy-tabs-input"
                                               name="<?= Seo_Yoast::$form_prefix ?>keywords<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                               type="text"
                                               value="<?= esc_attr( $meta_data['keywords'] ?? '' ) ?>"
                                               placeholder="Focus key-phrase">
                                    </label>
                                </div>
                            </div>
                            <div role="tabpanel" id="dmsy-social-<?= esc_attr( $key ) ?>" aria-labelledby="dmsy-tabs" tabindex="0" class="dmsy-tabs-body">
                                <div class="dmsy-tabs-group">
                                    <div class="dmsy-tabs-row">
                                        <div class="dmsy-tabs-upload">
                                            <span class="dmsy-tabs-input-label"><?= __( 'Social Image', 'domain-mapping-system' ) ?></span>
                                            <button class="dmsy-tabs-upload-button" type="button"><?= ! empty( $meta_data['opengraph-image'] ) ? __( 'Replace image', 'domain-mapping-system' )
													: __( 'Select image', 'domain-mapping-system' ) ?></button>
                                            <input class="dmsy-tabs-upload-image-id" type="hidden"
                                                   name="<?= Seo_Yoast::$form_prefix ?>opengraph-image-id<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   value="<?= esc_attr( $meta_data['opengraph-image-id'] ?? '' ) ?>">
                                            <input class="dmsy-tabs-upload-image-url" type="hidden"
                                                   name="<?= Seo_Yoast::$form_prefix ?>opengraph-image<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   value="<?= esc_attr( $meta_data['opengraph-image'] ?? '' ) ?>">
                                            <a class="dmsy-tabs-upload-image-remove" href="#" style="color: red"><?= __( 'Remove image', 'domain-mapping-system' ) ?></a>
											<?php if ( ! empty( $meta_data['opengraph-image'] ) ): ?>
                                                <img class="dmsy-tabs-upload-image" src="<?= esc_url( $meta_data['opengraph-image'] ) ?>">
											<?php endif; ?>
                                        </div>
                                        <label class="dmsy-tabs-input-holder">
                                            <span class="dmsy-tabs-input-label"><?= __( 'Social Title', 'domain-mapping-system' ) ?></span>
                                            <input class="dmsy-tabs-input"
                                                   name="<?= Seo_Yoast::$form_prefix ?>opengraph-title<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   type="text"
                                                   value="<?= esc_attr( $meta_data['opengraph-title'] ?? '' ) ?>"
                                                   placeholder="<?= __( 'Social title', 'domain-mapping-system' ) ?>">
                                        </label>
                                    </div>
                                    <div class="dmsy-tabs-row">
                                        <label class="dmsy-tabs-input-holder">
                                            <span class="dmsy-tabs-input-label"><?= __( 'Social Description', 'domain-mapping-system' ) ?></span>
                                            <input class="dmsy-tabs-input"
                                                   name="<?= Seo_Yoast::$form_prefix ?>opengraph-description<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   type="text"
                                                   value="<?= esc_attr( $meta_data['opengraph-description'] ?? '' ) ?>"
                                                   placeholder="<?= __( 'Social description', 'domain-mapping-system' ) ?>">
                                        </label>
                                    </div>
                                </div>
                                <div class="dmsy-tabs-group">
                                    <div class="dmsy-tabs-row">
                                        <div class="dmsy-tabs-upload">
                                            <span class="dmsy-tabs-input-label"><?= __( 'Twitter Image', 'domain-mapping-system' ) ?></span>
                                            <button class="dmsy-tabs-upload-button" type="button"><?= ! empty( $meta_data['twitter-image'] ) ? __( 'Replace image', 'domain-mapping-system' )
													: __( 'Select image',  'domain-mapping-system' ) ?></button>
                                            <input class="dmsy-tabs-upload-image-id"
                                                   type="hidden"
                                                   name="<?= Seo_Yoast::$form_prefix ?>twitter-image-id<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   value="<?= esc_attr( $meta_data['twitter-image-id'] ?? '' ) ?>">
                                            <input class="dmsy-tabs-upload-image-url"
                                                   type="hidden"
                                                   name="<?= Seo_Yoast::$form_prefix ?>twitter-image<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   value="<?= esc_attr( $meta_data['twitter-image'] ?? '' ) ?>">
                                            <a class="dmsy-tabs-upload-image-remove" href="#" style="color: red"><?= __( 'Remove image', 'domain-mapping-system' ) ?></a>
											<?php if ( ! empty( $meta_data['twitter-image'] ) ): ?>
                                                <img class="dmsy-tabs-upload-image" src="<?= esc_url( $meta_data['twitter-image'] ) ?>">
											<?php endif; ?>
                                        </div>
                                        <label class="dmsy-tabs-input-holder">
                                            <span class="dmsy-tabs-input-label"><?= __( 'Twitter Title', 'domain-mapping-system' ) ?></span>
                                            <input class="dmsy-tabs-input"
                                                   name="<?= Seo_Yoast::$form_prefix ?>twitter-title<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   type="text"
                                                   value="<?= esc_attr( $meta_data['twitter-title'] ?? '' ) ?>"
                                                   placeholder="<?= __( 'Twitter title', 'domain-mapping-system' ) ?>">
                                        </label>
                                    </div>
                                    <div class="dmsy-tabs-row">
                                        <label class="dmsy-tabs-input-holder">
                                            <span class="dmsy-tabs-input-label"><?= __( 'Twitter Description', 'domain-mapping-system' ) ?></span>
                                            <input class="dmsy-tabs-input"
                                                   name="<?= Seo_Yoast::$form_prefix ?>twitter-description<?= Seo_Yoast::$domain_separator . esc_attr( $hostAndPath ) ?>"
                                                   type="text"
                                                   value="<?= esc_attr( $meta_data['twitter-description'] ?? '' ) ?>"
                                                   placeholder="<?= __( 'Twitter description', 'domain-mapping-system' ) ?>">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			<?php
			endforeach; ?>

			<?php
		} else {
			$popupExist = true;
			if ( $this->fs->can_use_premium_code__premium_only() ) {
				?>
                <div class="dmsy-upgrader">
					<?php if ( empty( $this->get_options_per_domain() ) ) { ?>
                        <p style="padding: 0 16px">
							<?= sprintf( __( 'Please enable Duplicate SEO Options setting in %s.', 'domain-mapping-system' ),
								' <a class="upgrade" href="' . admin_url() . '?page=domain-mapping-system">' . __( 'Domain Mapping System settings', 'domain-mapping-system' ) . '&#8594;</a>' ) ?>
                        </p>
					<?php } else { ?>
                        <p style="padding: 0 16px">
							<?= sprintf( __( 'Please ensure this published resource has a mapping configured in %s. Currently, it is not configured.', 'domain-mapping-system' ),
								' <a class="upgrade" href="' . admin_url() . '?page=domain-mapping-system">' . __( 'Domain Mapping System settings', 'domain-mapping-system' ) . '&#8594;</a>' ) ?>
                        </p>
					<?php } ?>
                </div>
				<?php
			} else {
				?>
                <div class="dmsy-upgrader">
                    <p style="padding: 0 16px">
						<?= sprintf( __( 'To customize Yoast meta content per domain, please %s', 'domain-mapping-system' ),
							' <a class="upgrade" href="' . $this->fs->get_upgrade_url() . '">' . __( 'Upgrade', 'domain-mapping-system' ) . '&#8594;</a>' ) ?>
                    </p>
                </div>
				<?php
			}
			?>
            <div class="dmsy-accordion <?= ! empty( $popupExist ) ? 'popup-behind' : '' ?>">
                <div class="dmsy-accordion-header">
                    <button class="dmsy-accordion-toggle">
                        <span></span>
                        <svg role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M7.41,8.59L12,13.17l4.59-4.58L18,10l-6,6l-6-6L7.41,8.59z"></path>
                        </svg>
                    </button>
                </div>
                <div class="dmsy-accordion-body">
                    <div class="dmsy-accordion-body-in">
                        <div class="dmsy-tabs">
                            <ul role="tablist" class="dmsy-tabs-in" aria-label="Yoast SEO">
                                <!-- add 'active' to li for active tab-->
                                <li role="presentation" class="dmsy-tabs-item active">
                                    <a role="tab" href="#dmsy-seo-0"
                                       aria-selected="false" tabindex="-1">
                                        <span><?= __( 'SEO', 'domain-mapping-system' ) ?></span>
                                    </a>
                                </li>
                                <li role="presentation" class="dmsy-tabs-item">
                                    <a role="tab" href="#dmsy-social-0" aria-selected="false" tabindex="-1">
                                        <span class="dmsy-tabs-icon dashicons dashicons-share"></span>
                                        <span><?= __( 'Social', 'domain-mapping-system' ) ?></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- add 'active' to tab for active tab-->
                        <div role="tabpanel" id="dmsy-seo-0" aria-labelledby="dmsy-tabs" tabindex="0" class="dmsy-tabs-body active">
                            <div class="dmsy-tabs-row">
                                <label class="dmsy-tabs-input-holder">
                                    <span class="dmsy-tabs-input-label"><?= __( 'Title', 'domain-mapping-system' ) ?></span>
                                    <input class="dmsy-tabs-input"
                                           name="<?= Seo_Yoast::$form_prefix ?>title"
                                           type="text"
                                           value=""
                                           placeholder="Seo title">
                                </label>
                            </div>
                            <div class="dmsy-tabs-row">
                                <label class="dmsy-tabs-input-holder">
                                    <span class="dmsy-tabs-input-label"><?= __( 'Description', 'domain-mapping-system' ) ?></span>
                                    <input class="dmsy-tabs-input"
                                           name="<?= Seo_Yoast::$form_prefix ?>description"
                                           type="text"
                                           value=""
                                           placeholder="Seo description">
                                </label>
                            </div>
                            <div class="dmsy-tabs-row">
                                <label class="dmsy-tabs-input-holder">
                                    <span class="dmsy-tabs-input-label"><?= __( 'Keywords', 'domain-mapping-system' ) ?></span>
                                    <input class="dmsy-tabs-input"
                                           name="<?= Seo_Yoast::$form_prefix ?>keywords"
                                           type="text"
                                           value=""
                                           placeholder="Focus key-phrase">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}
		?>
    </div>
	<?php
}