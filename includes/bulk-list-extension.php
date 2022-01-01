<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


if ( 'contacts' === dt_get_post_type() ) {

    /**
     * Removes core send utility and upgrades it.
     */
    remove_action('dt_post_bulk_list_link','dt_post_bulk_list_link_apps', 20 );
    remove_action('dt_post_bulk_list_section','dt_post_bulk_list_section_apps', 20 );

    /**
     * Adds link
     */
    add_action( 'dt_post_bulk_list_link', 'dt_post_bulk_list_link_messages', 30, 3 );
    function dt_post_bulk_list_link_messages( $post_type, $post_settings, $dt_magic_apps ) {
        ?>
        <span style="display:inline-block">
                <button class="button clear" id="bulk_contact_messaging_controls" style="margin:0; padding:0">
                    <?php esc_html_e( 'Bulk Message', 'disciple_tools' ); ?>
                    <img class="dt-icon" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/bulk-edit.svg' ) ?>"/>
                </button>
            </span>
        <?php
    }

    /**
     * Adds hidden toggle body
     */
    add_action( 'dt_post_bulk_list_section', 'dt_post_bulk_list_section_messages', 30, 3 );
    function dt_post_bulk_list_section_messages( $post_type, $post_settings, $dt_magic_apps ){
        $dt_message_methods = apply_filters( 'dt_message_methods', [
            'email' => [
                'key' => 'email',
                'label' => 'Email'
            ]
        ] );
        $dt_user = get_user_meta(get_current_user_id());
        ?>
        <div id="bulk_contact_messaging_picker" style="display:none; padding:20px; border-radius:5px; background-color:#ecf5fc; margin: 30px 0">
            <p style="font-weight:bold"><?php
                echo sprintf( esc_html__( 'Select all the %1$s to whom you want to message.', 'disciple_tools' ), esc_html( $post_type ) );?></p>
            <div class="grid-x grid-margin-x">

                <div class="cell">
                    <label for="bulk_contact_messaging_method"><?php echo esc_html__( 'Method', 'disciple_tools' ); ?></label>
                    <span id="bulk_contact_messaging_method" style="display:none;color:red;"><?php echo esc_html__( 'You must select an app', 'disciple_tools' ); ?></span>
                    <div class="bulk_contact_messaging_method dt-radio button-group toggle ">
                        <?php
                        foreach( $dt_message_methods as $type ) {
                            $checked = false;
                            if ( $type['key'] === 'email') {
                                $checked = true;
                            }
                            ?>
                            <input type="radio" id="bulk_contact_messaging_method<?php echo $type['key'] ?>" value="<?php echo $type['key'] ?>" name="message_type" <?php echo ( $checked) ? 'checked': ''; ?>>
                            <label class="button" for="bulk_contact_messaging_method<?php echo $type['key'] ?>"><?php echo $type['label'] ?></label>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="cell">
                    <label for="bulk_contact_messaging_from_address"><?php echo esc_html__( 'Send From', 'disciple_tools' ); ?></label>
                    <span id="bulk_contact_messaging_from_address" style="display:none;color:red;"><?php echo esc_html__( 'You must select an email', 'disciple_tools' ); ?></span>
                    <div class="bulk_contact_messaging_from_address dt-radio button-group toggle ">
                        <input type="radio" id="bulk_contact_messaging_from_address_none" name="send_from" value="default" checked>
                        <label class="button" for="bulk_contact_messaging_from_address_none">System</label>
                        <?php if ( isset( $dt_user['nickname'][0] ) ) : ?>
                            <input type="radio" id="bulk_contact_messaging_from_address_display" name="send_from" value="<?php echo esc_html( $dt_user['nickname'][0] ) ?>">
                            <label class="button" for="bulk_contact_messaging_from_address_display"><?php echo esc_html( $dt_user['nickname'][0] ) ?></label>
                        <?php endif; ?>
                        <?php if ( isset( $dt_user['first_name'][0] ) ) : ?>
                            <input type="radio" id="bulk_contact_messaging_from_address_first_name" name="send_from" value="<?php echo esc_html( $dt_user['first_name'][0] ) ?>">
                            <label class="button" for="bulk_contact_messaging_from_address_first_name"><?php echo esc_html( $dt_user['first_name'][0] ) ?></label>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="cell">
                    <label for="bulk_contact_messaging_subject"><?php echo esc_html__( 'Subject Line', 'disciple_tools' ); ?></label>
                    <input type="text" id="bulk_contact_messaging_subject" placeholder="<?php echo esc_html__( 'Add brief subject line.', 'disciple_tools' ); ?>" />
                </div>
                <div class="cell">
                    <label for="bulk_contact_messaging_body"><?php echo esc_html__( 'Message', 'disciple_tools' ); ?></label>
                    <textarea id="bulk_contact_messaging_body" style="height:100px;" placeholder="<?php echo esc_html__( 'Add body of the email', 'disciple_tools' ); ?>" ></textarea>
                </div>
                <div class="cell">
                    <label for="bulk_contact_messaging_app_link"><?php echo esc_html__( 'Add app link to message', 'disciple_tools' ); ?></label>
                    <span id="bulk_contact_messaging_app_link" style="display:none;color:red;"><?php echo esc_html__( 'You must select an app', 'disciple_tools' ); ?></span>
                    <div class="bulk_send_app dt-radio button-group toggle ">
                        <input type="radio" id="no_app" data-root="" data-type="" name="app_link" checked>
                        <label class="button" for="no_app">No Link</label>
                        <?php
                        foreach ( $dt_magic_apps as $root ) {
                            foreach ( $root as $type ) {
                                if ( isset( $type['show_bulk_send'] ) && $type['show_bulk_send'] ) {
                                    ?>
                                    <input type="radio" id="<?php echo esc_attr( $type['root'] . '_' . $type['type'] ) ?>" data-root="<?php echo esc_attr( $type['root'] ) ?>" data-type="<?php echo esc_attr( $type['type'] ) ?>" name="app_link">
                                    <label class="button" for="<?php echo esc_attr( $type['root'] . '_' . $type['type'] ) ?>"><?php echo esc_html( $type['name'] ) ?></label>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="cell">
                    <label for="bulk_contact_messaging_required_elements"><?php echo esc_html__( 'Send to selected records', 'disciple_tools' ); ?></label>
                    <span id="bulk_contact_messaging_required_elements" style="display:none;color:red;"><?php echo esc_html__( 'You must select at least one record', 'disciple_tools' ); ?></span>
                    <div>
                        <button class="button dt-green" id="bulk_contact_messaging_submit">
                            <span class="bulk_edit_submit_text" data-pretext="<?php echo esc_html__( 'Send', 'disciple_tools' ); ?>" data-posttext="<?php echo esc_html__( 'Links', 'disciple_tools' ); ?>" style="text-transform:capitalize;">
                                <?php echo esc_html( __( "Make Selections Below", "disciple_tools" ) ); ?>
                            </span>
                            <span id="bulk_contact_messaging_submit-spinner" style="display: inline-block" class="loading-spinner"></span>
                        </button>

                    </div>
                    <span id="bulk_contact_messaging_submit-message"></span>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function(){
                jQuery('#bulk_contact_messaging_controls').on('click', function(){
                    jQuery('#bulk_contact_messaging_picker').toggle();
                    jQuery('#records-table').toggleClass('bulk_edit_on');
                })

                jQuery('#bulk_contact_messaging_submit').on('click', function(e) {

                    let method_input = jQuery('.bulk_contact_messaging_method.dt-radio.button-group input:checked')
                    if ( method_input.length < 1 ) {
                        jQuery("#bulk_contact_messaging_method").show()
                        return
                    } else {
                        jQuery("#bulk_contact_messaging_method").hide()
                    }

                    let send_from_input = jQuery('.bulk_contact_messaging_from_address.dt-radio.button-group input:checked')
                    if ( send_from_input.length < 1 && 'email' === method_input ) {
                        jQuery("#bulk_contact_messaging_from_address").show()
                        return
                    } else {
                        jQuery("#bulk_contact_messaging_from_address").hide()
                    }

                    let subject = jQuery('#bulk_contact_messaging_subject').val()

                    let body = jQuery('#bulk_contact_messaging_body').val()

                    let app_input = jQuery('.bulk_send_app.dt-radio.button-group input:checked')
                    if ( app_input.length < 1 ) {
                        jQuery("#bulk_contact_messaging_app_link").show()
                        return
                    } else {
                        jQuery("#bulk_contact_messaging_app_link").hide()
                    }

                    let root = app_input.data('root')
                    let type = app_input.data('type')

                    let queue =  [];
                    jQuery('.bulk_edit_checkbox input').each(function () {
                        if (this.checked && this.id !== 'bulk_edit_master_checkbox') {
                            let postId = parseInt(jQuery(this).val());
                            queue.push( postId );
                        }
                    });
                    if ( queue.length < 1 ) {
                        jQuery('#bulk_contact_messaging_required_elements').show()
                        return;
                    } else {
                        jQuery('#bulk_contact_messaging_required_elements').hide()
                    }

                    jQuery('#bulk_contact_messaging_submit-spinner').addClass('active')

                    let settings = {
                        method: method_input.val(),
                        send_from: send_from_input.val(),
                        subject: subject,
                        body: body,
                        root: root,
                        type: type
                    }

                    console.log( settings )
                    console.log( queue )

                    makeRequest('POST', list_settings.post_type + '/bulk_messaging', { settings: settings, post_ids: queue } )
                        .done( data => {
                            jQuery('#bulk_contact_messaging_submit-spinner').removeClass('active')
                            jQuery('#bulk_contact_messaging_submit-message').html(`<strong>${data.total_sent}</strong> ${list_settings.translations.sent}!<br><strong>${data.total_unsent}</strong> ${list_settings.translations.not_sent}`)
                            jQuery('#bulk_edit_master_checkbox').prop("checked", false);
                            jQuery('.bulk_edit_checkbox input').prop("checked", false);
                            bulk_edit_count()
                            console.log(data)
                            // window.location.reload();
                        })
                        .fail( e => {
                            jQuery('#bulk_contact_messaging_submit-spinner').removeClass('active')
                            jQuery('#bulk_contact_messaging_submit-message').html('Oops. Something went wrong! Check log.')
                            console.log( e )
                        })
                });

            })
        </script>
        <?php
    };
}
