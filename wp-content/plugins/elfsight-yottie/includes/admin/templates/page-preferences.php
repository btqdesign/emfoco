<?php

if (!defined('ABSPATH')) exit;

?><article class="yottie-admin-page-preferences yottie-admin-page" data-yt-admin-page-id="preferences">
    <div class="yottie-admin-page-heading">
        <h2><?php _e('Preferences', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></h2>

        <div class="yottie-admin-page-heading-subheading">
            <?php _e('These settings will be accepted for each Yottie gallery<br> on your website.', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
        </div>
    </div>

    <div class="yottie-admin-divider"></div>

    <div class="yottie-admin-page-preferences-form" data-nonce="<?php echo wp_create_nonce('elfsight_yottie_update_preferences_nonce'); ?>">
        <div class="yottie-admin-page-preferences-option-api-key yottie-admin-page-preferences-option">
            <div class="yottie-admin-page-preferences-option-info">
                <h4 class="yottie-admin-page-preferences-option-info-name" class="yottie-admin-tooltip-trigger">
                    <?php _e('YouTube API Key', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
                </h4>

                <div class="yottie-admin-caption">
                    <?php printf(__('YouTube API key is required to get independent YouTube API quota.<br> Follow this tutorial to get it: <a href="%1$s" target="_blank">How to get YouTube API key</a>', ELFSIGHT_YOTTIE_TEXTDOMAIN), ELFSIGHT_YOTTIE_GET_API_KEY_URL); ?>
                </div>
            </div>

            <div class="yottie-admin-page-preferences-option-input-container">
                <input type="text" name="preferences_youtube_api_key" id="preferencesYoutubeApiKey" value="<?php echo $preferences_youtube_api_key; ?>">

                <div class="yottie-admin-page-preferences-option-save-container">
                    <a href="#" class="yottie-admin-page-preferences-option-css-save yottie-admin-page-preferences-option-save yottie-admin-button-green yottie-admin-button">
                        <span class="yottie-admin-page-preferences-option-save-label"><?php _e('Save', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>

                        <span class="yottie-admin-page-preferences-option-save-loader"></span>
                    </a>

                    <span class="yottie-admin-page-preferences-option-save-success">
                        <span class="yottie-admin-icon-check-green-small yottie-admin-icon"></span><span class="yottie-admin-page-preferences-option-save-success-label"><?php _e('Done!', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>
                    </span>

                    <span class="yottie-admin-page-preferences-option-save-error"></span>
                </div>
            </div>
        </div>

        <div class="yottie-admin-divider"></div>

        <div class="yottie-admin-page-preferences-option-force-script yottie-admin-page-preferences-option">
            <div class="yottie-admin-page-preferences-option-info">
                <h4 class="yottie-admin-page-preferences-option-info-name">
                    <label for="forceScriptAdd"><?php _e('Add Yottie script to every page', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></label>
                </h4>

                <div class="yottie-admin-caption">
                    <?php _e('By default the plugin adds its scripts only on pages with Yottie shortcode. This option makes the plugin add scripts on every page. It is useful for ajax websites.', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
                </div>
            </div>

            <div class="yottie-admin-page-preferences-option-input-container">
                <input type="checkbox" name="preferences_force_script_add" value="true" id="forceScriptAdd" class="yottie-admin-page-preferences-option-input-toggle"<?php echo ($preferences_force_script_add === 'on') ? ' checked' : ''?>>
                <label for="forceScriptAdd"><i></i></label>
            </div>
        </div>

        <div class="yottie-admin-divider"></div>

        <div class="yottie-admin-page-preferences-option-css yottie-admin-page-preferences-option">
            <div class="yottie-admin-page-preferences-option-info">
                <h4 class="yottie-admin-page-preferences-option-info-name">
                    <?php _e('Custom CSS', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
                </h4>

                <div class="yottie-admin-caption">
                    <?php _e('Here you can specify custom styles for Yottie. It will be used on each page with the plugin.', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
                </div>
            </div>

            <div class="yottie-admin-page-preferences-option-input-container">
                <div class="yottie-admin-page-preferences-option-editor">
                    <div class="yottie-admin-page-preferences-option-editor-code" id="yottiePreferencesSnippetCSS"><?php echo htmlspecialchars($preferences_custom_css)?></div>
                </div>

                <div class="yottie-admin-page-preferences-option-save-container">
                    <a href="#" class="yottie-admin-page-preferences-option-css-save yottie-admin-page-preferences-option-save yottie-admin-button-green yottie-admin-button">
                        <span class="yottie-admin-page-preferences-option-save-label"><?php _e('Save', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>

                        <span class="yottie-admin-page-preferences-option-save-loader"></span>
                    </a>

                    <span class="yottie-admin-page-preferences-option-save-success">
                        <span class="yottie-admin-icon-check-green-small yottie-admin-icon"></span><span class="yottie-admin-page-preferences-option-save-success-label"><?php _e('Done!', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>
                    </span>

                    <span class="yottie-admin-page-preferences-option-save-error"></span>
                </div>
            </div>
        </div>

        <div class="yottie-admin-divider"></div>

        <div class="yottie-admin-page-preferences-option-js yottie-admin-page-preferences-option">
            <div class="yottie-admin-page-preferences-option-info">
                <h4 class="yottie-admin-page-preferences-option-info-name">
                    <?php _e('Custom JavaScript', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
                </h4>

                <div class="yottie-admin-caption">
                    <?php _e('Here you can specify custom JS for initiation of Yottie. This script will be used on each page with the plugin.', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
                </div>
            </div>
            
            <div class="yottie-admin-page-preferences-option-input-container">
                <div class="yottie-admin-page-preferences-option-editor">
                    <div class="yottie-admin-page-preferences-option-editor-code" id="yottiePreferencesSnippetJS"><?php echo htmlspecialchars($preferences_custom_js) ?></div>
                </div>

                <div class="yottie-admin-page-preferences-option-save-container">
                    <a href="#" class="yottie-admin-page-preferences-option-js-save yottie-admin-page-preferences-option-save yottie-admin-button-green yottie-admin-button">
                        <span class="yottie-admin-page-preferences-option-save-label"><?php _e('Save', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>

                        <span class="yottie-admin-page-preferences-option-save-loader"></span>
                    </a>

                    <span class="yottie-admin-page-preferences-option-save-success">
                        <span class="yottie-admin-icon-check-green-small yottie-admin-icon"></span><span class="yottie-admin-page-preferences-option-save-success-label"><?php _e('Done!', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></span>
                    </span>

                    <span class="yottie-admin-page-preferences-option-save-error"></span>
                </div>
            </div>
        </div>
    </div>
</article>