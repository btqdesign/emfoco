				<form class="search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label>
						<span class="screen-reader-text"><?php esc_html_e('Search for:', 'fallsky'); ?></span>
						<?php 
							$fallsky_search_str 	= get_search_query();
							$fallsky_search_value 	= empty($fallsky_search_str) ? '' : sprintf('value="%s" ', esc_attr($fallsky_search_str)); 
						?>
						<input type="search" class="search-field" placeholder="<?php esc_attr_e('Search', 'fallsky'); ?>" autocomplete="off" <?php print($fallsky_search_value); ?>name="s">
					</label>
					<button type="submit" class="search-submit"><span class="screen-reader-text"><?php esc_html_e('Search', 'fallsky'); ?></span></button>
				</form>