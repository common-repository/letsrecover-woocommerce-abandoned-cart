<div class="wplrp-notification-wrapper">
    <h2>Send Notification</h2>
    
    <?php if( $subscriber && $subscriber['status'] == 1){ ?>
        <div class="wplrp-from-wrapper wplrp-send-notification-wrapper form-wrap" style="padding-left:0;">        
            <div class="wplrp-push-content">
                <form method="post" action="" id="frm-send-notification">
                    <?php 
                        wp_nonce_field( 'wplrp_nounce_action', 'wplrp_setup_nonce_field' ); 
                        $options = get_option('wplrp_options');
                    ?>

                    <ul>

                        <li>
                            <label for="title">Notification Title</label>
                            <input name="title" tabindex="1" id="title" class="title wplrp-textfield emojifield" type="text"  maxlength="70" size="100" required="required" aria-required="true" >
                        </li>

                        <li>
                            <label for="message">Notification Message</label>
                            <textarea tabindex="2" rows="5" maxlength="250" id="message" name="message" required class="message required-field wplrp-textfield emojifield"></textarea>
                        </li>

                        <li>
                            <label for="icon">Notification Icon</label>
                            <div class="wplrp_push_icon wplrp-group-fields">
                                <input tabindex="" class="upload-field" name="icon" id="icon" placeholder="" type="text"   aria-required="true" style="" >
                                <button tabindex="3" class="wplrp-btn wplrp-btn-primary wplrp-btn-upload" type="button" data-label="Icon">Choose Icon</button>
                            </div>
                        </li>

                        
                        <li>
                            <label for="image">Notification Image</label>
                            <div class="wplrp_push_icon wplrp-group-fields">

                                <?php if( $subscriber['platform'] == 'Mac OS X'){ ?>
                                    <p>Notification (large) image is not support by <strong>Mac OS X</strong></p>
                                
                                <?php } elseif( $subscriber['browser'] != 'Chrome' ){ ?>
                                    <p>Notification (large) image is only supported by Chrome browser</p>
                                
                                <?php } else { ?>
                                    <input class="upload-field" name="image" id="image" placeholder="" type="text" value=""  aria-required="true" style="" >
                                    <button tabindex="4" class="wplrp-btn wplrp-btn-primary wplrp-btn-upload" type="button"  data-label="Image">Choose Image</button>
                                <?php } ?>
                                
                            </div>
                        </li>


                        <li>
                            <label for="url">Target URL</label>
                            <input name="url" id="url" type="url"  size="100" aria-required="true" value="<?php echo get_site_url();?>" >
                        </li>

                        <?php if( $subscriber['browser'] == 'Chrome' ){ ?>
                            <li class="lp-action-button lp-action-button-1">
                                <fieldset>
                                    <legend>Button 1</legend>
                                    <label for="wplrp_button1_text">Text</label>
                                    <input name="button_1_text" id="wplrp_button1_text" class="emojifield button_1_text wplrp-textfield" type="text" value=""  >
                                    <label for="wplrp_button1_url">URL</label>
                                    <input name="button_1_url" id="wplrp_button1_url" class=" wplrp-textfield" type="text" value=""  >
                                    <button class="action-button-remove" type="button" style="display:block;"><svg class="svg-inline--fa fa-times fa-w-11" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" data-fa-i2svg=""><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></button>
                                </fieldset>
                            </li>

                            <li class="lp-action-button lp-action-button-2">
                                <fieldset>
                                    <legend>Button 2</legend>
                                    <label for="wplrp_button2_text">Text</label>
                                    <input name="button_2_text" id="wplrp_button2_text" class="emojifield button_2_text  wplrp-textfield" type="text" value=""  >
                                    <label for="wplrp_button2_url">URL</label>
                                    <input name="button_2_url" id="wplrp_button2_url" class=" wplrp-textfield" type="text"  value=""  >
                                    <button class="action-button-remove" type="button" style="display:block;"><svg class="svg-inline--fa fa-times fa-w-11" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" data-fa-i2svg=""><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></button>
                                </fieldset>
                            </li>

                            <li class="add-action-button-wrapper">
                                <button class="wplrp-btn wplrp-btn-info add-action-button">+ Add Action Button</button>  <div class="wplrp-info lp-position-right"><span class="dashicons dashicons-editor-help"></span><p style="font-weight:normal">Action button is only supported by Chrome browser</p></div></label>
                            </li>
                        <?php } ?>
                    </ul>

                    <div class="wplrp-button-wrapper">
                        <button type="submit" class="wplrp-btn wplrp-btn-primary" id="send-push-button" name="wplrp_send_push" >Send Notification</button>
                    </div>

                </form>
            </div>
            <div class="wplrp-push-preview"  >

                <div class="wplrp-preview-heading">
                    <h3>Notification Preview</h3>
                    <p>Preview is generated for <strong><?php echo esc_html($subscriber['browser']);?></strong> browser on <strong><?php echo esc_html($subscriber['platform']);?></strong> operating system based on subscriber's user agent</p>
                </div>

                <div class="wplrp-windows-preview wplrp-platform-preview" <?php if( $subscriber['platform'] != 'Mac OS X' && $subscriber['platform'] != 'Android' ) { ?> style='display:block' <?php };?>>
                    <div class="wplrp-windows-notification" <?php if( $subscriber['browser'] == 'Firefox' ){ ?> style="display:none" <?php } ?>>
                        <div class="preview-container">
                            <div class="wplrp-broswer-icon"></div>
                            <div class="wplrp-windows-notification-header">
                                <div>
                                    <span style="margin-right:5px;">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                                            <g>
                                                <path fill="#777" d="M56.408,73.067l-15.378,25.63c-11.794-1.777-19.914-7.766-27.838-16.835 C5.265,72.797,1.302,62.174,1.302,50c0-8.495,0.941-14.302,5.126-21.786l20.504,32.038c7.259,9.852,14.097,15.378,21.786,15.378 C53.845,75.63,56.408,73.067,56.408,73.067z" />
                                                <path fill="#777" d="M26.436,43.723L10.728,20.153c4.565-5.707,10.891-10.767,17.486-14C34.807,2.919,42.391,1.302,50,1.302 c21.786,0,37.977,15.714,42.29,23.067c0,0-40.719,0-42.29,0c-5.707,0-10.813,1.722-15.313,5.209 C30.184,33.067,27.831,38.271,26.436,43.723z" />
                                                <path fill="#777" d="M66.66,30.777h28.193c2.283,5.706,3.845,13.01,3.845,19.223c0,13.316-4.694,24.729-14.077,34.24 C75.235,93.752,61.534,98.698,50,98.698l20.544-34.431c2.915-4.31,3.805-9.066,3.805-14.267 C74.349,43.027,71.606,35.723,66.66,30.777z" />
                                                <circle fill="#777" cx="50" cy="50" r="16.66" />
                                            </g>
                                        </svg>
                                    </span>
                                    <span>
                                        <?php echo parse_url(site_url(),PHP_URL_HOST);?> • now
                                    </span>
                                </div>
                                <div>
                                    <span style="position:absolute; top:1px; right:15px;font-size:12px" class="closex"> X </span>
                                    <span style="position:absolute; top:1px; right:30px;" class="closex">
                                        <svg class="svg-inline--fa fa-cog fa-w-16" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="cog" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"></path></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="wplrp-windows-notification-content">
                                <div class="wplrp-windows-notification-text">
                                    <div class="wplrp-windows-notification-title wplrp-preview-title"></div>
                                    <div class="wplrp-windows-notification-msg wplrp-preview-msg"></div>
                                </div>
                                <div class="wplrp-windows-notification-icon  wplrp-preview-icon"></div>
                            </div>
                            <div class="lp-window-preview-img  lp-preview-image"></div>
                            <div class="lp-action-button-windows-preview">
                                <span class="button1-preview"></span>
                                <span class="button2-preview"></span>
                            </div>
                            
                        </div>
                    </div>

                    <!-- WINDOWS - FIREFOX -->
                    <div class="wplrp-windows-notification firefox-preview" <?php if( $subscriber['browser'] != 'Firefox' ){ ?> style="display:none" <?php } ?>>
                        <div class="preview-container">

                            <div class="wplrp-firefox-title">
                                <div class="wplrp-windows-notification-title wplrp-preview-title"></div>
                                <div class="closex"> X </div>
                            </div>
                            
                            <div class="wplrp-firefox-body">

                                <div class="wplrp-firefox-icon">
                                    <div class="wplrp-windows-notification-icon  wplrp-preview-icon"></div>
                                </div>

                                <div class="wplrp-firefox-msg">                            
                                    <div class="wplrp-windows-notification-msg wplrp-preview-msg"></div>                            
                                    <div class="wplrp-firefox-footer">
                                        <div style="width:100%">via <?php echo parse_url(site_url(),PHP_URL_HOST);?></div>
                                        <div class="closex">
                                            <svg style="width:14px;" class="svg-inline--fa fa-cog fa-w-16" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="cog" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wplrp-mac-preview wplrp-platform-preview" <?php if( $subscriber['platform'] == 'Mac OS X') { ?> style='display:block' <?php };?>>
                    <div class="wplrp-mac-preview-container">
                        <div class="wplrp-mac-notification">
                            <div class="wplrp-mac-top-header">

                                <?php if($subscriber['browser'] == 'Firefox') { ?>
                                    <div class="wplrp-broswer-icon wplrp-firefox"> FIREFOX</div>

                                <?php } elseif($subscriber['browser'] == 'Opera'){ ?>
                                    <div class="wplrp-broswer-icon wplrp-opera"> OPERA</div>
                                    
                                <?php } elseif($subscriber['browser'] == 'Microsoft Edge'){ ?>
                                    <div class="wplrp-broswer-icon wplrp-msedge"> MICROSOFT EDGE</div>

                                <?php } else { ?>
                                    <div class="wplrp-broswer-icon wplrp-chrom"> GOOGLE CHROME</div>
                                <?php } ?>
                                
                                <div class="wplrp-notification-time">now</div>
                            </div>
                            <div class="wplrp-mac-notification-title wplrp-preview-title"> </div>
                            
                            <div class="wplrp-mac-bottom-row">
                                <div class="" style="width:100%">
                                    <div class="wplrp-mac-notification-domain"><?php echo esc_html($_SERVER['HTTP_HOST']);?></div>
                                    <div class="wplrp-mac-notification-msg  wplrp-preview-msg"></div>
                                </div>
                                <div>
                                    <div class="wplrp-mac-notification-icon wplrp-mac-preview-icon wplrp-preview-icon"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="wplrp-android-preview wplrp-platform-preview" <?php if( $subscriber['platform'] == 'Android') { ?> style='display:block' <?php };?>>
                    <div class="wplrp-notification-notification">
                        <div class="androidNotificationContent">
                            <div class="androidNotificationInfo">
                            <span class="androidNotificationBadge">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                                    <g>
                                    <path fill="#777" d="M56.408,73.067l-15.378,25.63c-11.794-1.777-19.914-7.766-27.838-16.835 C5.265,72.797,1.302,62.174,1.302,50c0-8.495,0.941-14.302,5.126-21.786l20.504,32.038c7.259,9.852,14.097,15.378,21.786,15.378 C53.845,75.63,56.408,73.067,56.408,73.067z" />
                                    <path fill="#777" d="M26.436,43.723L10.728,20.153c4.565-5.707,10.891-10.767,17.486-14C34.807,2.919,42.391,1.302,50,1.302 c21.786,0,37.977,15.714,42.29,23.067c0,0-40.719,0-42.29,0c-5.707,0-10.813,1.722-15.313,5.209 C30.184,33.067,27.831,38.271,26.436,43.723z" />
                                    <path fill="#777" d="M66.66,30.777h28.193c2.283,5.706,3.845,13.01,3.845,19.223c0,13.316-4.694,24.729-14.077,34.24 C75.235,93.752,61.534,98.698,50,98.698l20.544-34.431c2.915-4.31,3.805-9.066,3.805-14.267 C74.349,43.027,71.606,35.723,66.66,30.777z" />
                                    <circle fill="#777" cx="50" cy="50" r="16.66" />
                                    </g>
                                </svg>
                            </span>
                            <span class="androidNotificationBrowser">Chrome • <?php echo parse_url(site_url(),PHP_URL_HOST);?> • now</span>
                            </div>
                            <div class="androidNotificationBody">
                                <div class="androidNotificationHeader">
                                    <div class="androidNotificationTitle pushTitle wplrp-preview-title"></div>
                                    <div class="androidNotificationIcon  wplrp-preview-icon">
                                        <div class="androidDefultIcon"><?php echo parse_url(site_url(),PHP_URL_HOST)[0];?></div>
                                    </div>                                
                                </div>
                                <div class="androidNotificationMessage pushBody  wplrp-preview-msg"></div>
                                <div class="androidNotificationImage  lp-preview-image"></div>
                            </div>
                            
                        </div>
                        <div class="adnroidNotificationSettings">
                            <span class="button1-preview"></span>
                            <span class="button2-preview"></span>
                            <span >SITE SETTINGS</span>
                        </div>
                    </div>
                </div>
                <p>NOTE: The preview may vary depending on the version of your Platform and Browser</p>

            </div>
        </div>
    <?php } else { ?>
        <p>You cannot send notification to this subscriber. Either the suscriber ID not found or the user has unsubscribed</p> 
    <?php } ?>

</div>
<?php wp_enqueue_media(); ?>

