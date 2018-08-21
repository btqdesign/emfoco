<?php

if (!defined('ABSPATH')) exit;

?><div class="<?php echo !empty($other_products_hidden) && $other_products_hidden === 'true' ? 'yottie-admin-other-products-hidden-permanently' : 'yottie-admin-other-products-hidden'; ?> yottie-admin-other-products" data-nonce="<?php echo wp_create_nonce('elfsight_yottie_hide_other_products_nonce'); ?>">
	<h4 class="yottie-admin-other-products-title"><?php _e('More products by Elfsight', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></h4>

	<a href="#" class="yottie-admin-other-products-close"><?php _e('Close', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></a>

	<div class="yottie-admin-other-products-list">
		<div class="yottie-admin-other-products-list-item-instashow yottie-admin-other-products-list-item">
			<a href="<?php echo ELFSIGHT_YOTTIE_INSTASHOW_URL; ?>" target="_blank">
				<span class="yottie-admin-other-products-list-item-info">
					<img class="yottie-admin-other-products-list-item-image" src="<?php echo plugins_url('assets/img/instashow-logo.svg', ELFSIGHT_YOTTIE_FILE); ?>">
			
					<span class="yottie-admin-other-products-list-item-title"><?php _e('InstaShow', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>
					<span class="yottie-admin-other-products-list-item-description"><?php _e('Show Instagram images on your website', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>
				</span>

				<span class="yottie-admin-other-products-list-item-more">
					<span class="yottie-admin-other-products-list-item-more-label">
						<?php _e('Learn more', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
					</span>

					<svg class="yottie-admin-svg-arrow-more">
                        <line x1="0" y1="0" x2="4" y2="4"></line>
                        <line x1="0" y1="8" x2="4" y2="4"></line>
                    </svg>
				</span>
			</a>
		</div>

		<div class="yottie-admin-other-products-list-item-instalink yottie-admin-other-products-list-item">
			<a href="<?php echo ELFSIGHT_YOTTIE_INSTALINK_URL; ?>" target="_blank">
				<span class="yottie-admin-other-products-list-item-info">
					<img class="yottie-admin-other-products-list-item-image" src="<?php echo plugins_url('assets/img/instalink-logo.svg', ELFSIGHT_YOTTIE_FILE); ?>">
			
					<span class="yottie-admin-other-products-list-item-title"><?php _e('InstaLink', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>
					<span class="yottie-admin-other-products-list-item-description"><?php _e('Embed Instagram profile to your website.', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>
				</span>

				<span class="yottie-admin-other-products-list-item-more">
					<span class="yottie-admin-other-products-list-item-more-label">
						<?php _e('Learn more', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
					</span>

					<svg class="yottie-admin-svg-arrow-more">
                        <line x1="0" y1="0" x2="4" y2="4"></line>
                        <line x1="0" y1="8" x2="4" y2="4"></line>
                    </svg>
				</span>
			</a>
		</div>
	</div>
</div>