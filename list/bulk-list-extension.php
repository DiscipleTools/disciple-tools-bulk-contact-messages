<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


if ( 'contacts' === dt_get_post_type() ) {

    /**
     * Adds link
     */
    add_action( 'dt_post_bulk_list_link', 'dt_post_bulk_list_link_messages', 20, 3 );
    function dt_post_bulk_list_link_messages( $post_type, $post_settings, $dt_magic_apps ) {
        ?>
        <span style="display:inline-block">
                <button class="button clear" id="bulk_email_send_controls" style="margin:0; padding:0">
                    <?php esc_html_e( 'Bulk Message', 'disciple_tools' ); ?>
                    <img class="dt-icon" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/bulk-edit.svg' ) ?>"/>
                </button>
            </span>
        <?php
    }

    /**
     * Adds hidden toggle body
     */
    add_action( 'dt_post_bulk_list_section', 'dt_post_bulk_list_section_messages', 10, 3 );
    function dt_post_bulk_list_section_messages( $post_type, $post_settings, $dt_magic_apps ){
        $dt_message_methods = apply_filters('dt_message_methods', [
            'email' => [
                'key' => 'email',
                'label' => 'Email'
            ]
        ] );
        $default_email = get_option( 'dt_site_default_email', 'no-reply@'.site_url() );
        $dt_contact_id = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );
        $dt_contact = DT_Posts::get_post('contacts', $dt_contact_id );
        ?>
        <div id="bulk_send_message_picker" style="display:none; padding:20px; border-radius:5px; background-color:#ecf5fc; margin: 30px 0">
            <p style="font-weight:bold"><?php
                echo sprintf( esc_html__( 'Select all the %1$s to whom you want to send emails.', 'disciple_tools' ), esc_html( $post_type ) );?></p>
            <div class="grid-x grid-margin-x">
                <div class="cell">
                    <label for="bulk_send_message_note"><?php echo esc_html__( 'Reply to', 'disciple_tools' ); ?></label>
                    <div class="bulk_send_message dt-radio button-group toggle ">
                        <input type="radio" id="dt-email-default" data-root="" data-type="" name="e-group" checked>
                        <label class="button" for="dt-email-default"><?php echo $default_email ?></label>
                        <?php
                        if ( isset( $dt_contact['contact_email'] ) ) {
                            foreach( $dt_contact['contact_email'] as $email ) {
                                ?>
                                <input type="radio" id="dt-email-personal" data-root="" data-type="" name="e-group">
                                <label class="button" for="dt-email-personal"><?php echo $email['value'] ?></label>
                                <?php
                            }
                        }
                        ?>

                    </div>
                </div>
                <div class="cell">
                    <label for="bulk_send_app_required_selection"><?php echo esc_html__( 'Add app link to message', 'disciple_tools' ); ?></label>
                    <span id="bulk_send_app_required_selection" style="display:none;color:red;"><?php echo esc_html__( 'You must select an app', 'disciple_tools' ); ?></span>
                    <div class="bulk_send_app dt-radio button-group toggle ">
                        <?php
                        if ( isset( $dt_contact['contact_email'] ) ) {
                            foreach( $dt_message_methods as $type ) {
                                $checked = false;
                                if ( $type['key'] === 'email') {
                                    $checked = true;
                                }
                                ?>
                                <input type="radio" id="dt-email-personal" data-root="" data-type="" name="e-group" <?php echo ( $checked) ? 'checked': ''; ?>>
                                <label class="button" for="dt-email-personal"><?php echo $type['label'] ?></label>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="cell">
                    <label for="bulk_send_message_note"><?php echo esc_html__( 'Subject Line', 'disciple_tools' ); ?></label>
                    <input type="text" id="bulk_send_message_subject" placeholder="<?php echo esc_html__( 'Add brief subject line.', 'disciple_tools' ); ?>" />
                </div>
                <div class="cell">
                    <label for="bulk_send_message_note"><?php echo esc_html__( 'Message', 'disciple_tools' ); ?></label>
                    <textarea id="bulk_send_message_body" style="height:100px;" placeholder="<?php echo esc_html__( 'Add body of the email', 'disciple_tools' ); ?>" ></textarea>
                </div>
                <div class="cell">
                    <label for="bulk_send_app_required_selection"><?php echo esc_html__( 'Add app link to message', 'disciple_tools' ); ?></label>
                    <span id="bulk_send_app_required_selection" style="display:none;color:red;"><?php echo esc_html__( 'You must select an app', 'disciple_tools' ); ?></span>
                    <div class="bulk_send_app dt-radio button-group toggle ">
                        <?php
                        foreach ( $dt_magic_apps as $root ) {
                            foreach ( $root as $type ) {
                                if ( isset( $type['show_bulk_send'] ) && $type['show_bulk_send'] ) {
                                    ?>
                                    <input type="radio" id="<?php echo esc_attr( $type['root'] . '_' . $type['type'] ) ?>" data-root="<?php echo esc_attr( $type['root'] ) ?>" data-type="<?php echo esc_attr( $type['type'] ) ?>" name="r-group">
                                    <label class="button" for="<?php echo esc_attr( $type['root'] . '_' . $type['type'] ) ?>"><?php echo esc_html( $type['name'] ) ?></label>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="cell">
                    <label for="bulk_send_message_required_elements"><?php echo esc_html__( 'Send to selected records', 'disciple_tools' ); ?></label>
                    <span id="bulk_send_message_required_elements" style="display:none;color:red;"><?php echo esc_html__( 'You must select at least one record', 'disciple_tools' ); ?></span>
                    <div>
                        <button class="button dt-green" id="bulk_send_message_submit">
                            <span class="bulk_edit_submit_text" data-pretext="<?php echo esc_html__( 'Send', 'disciple_tools' ); ?>" data-posttext="<?php echo esc_html__( 'Links', 'disciple_tools' ); ?>" style="text-transform:capitalize;">
                                <?php echo esc_html( __( "Make Selections Below", "disciple_tools" ) ); ?>
                            </span>
                            <span id="bulk_send_message_submit-spinner" style="display: inline-block" class="loading-spinner"></span>
                        </button>

                    </div>
                    <span id="bulk_send_message_submit-message"></span>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function(){
                jQuery('#bulk_email_send_controls').on('click', function(){
                    jQuery('#bulk_send_message_picker').toggle();
                    jQuery('#records-table').toggleClass('bulk_edit_on');
                })

                jQuery('#bulk_send_message_submit').on('click', function(e) {
                    let note = jQuery('#bulk_send_message_note').val()

                    let selected_input = jQuery('.bulk_send_message.dt-radio.button-group input:checked')
                    if ( selected_input.length < 1 ) {
                        jQuery("#bulk_send_message_required_selection").show()
                        return
                    } else {
                        jQuery("#bulk_send_message_required_selection").hide()
                    }

                    let root = selected_input.data('root')
                    let type = selected_input.data('type')

                    let queue =  [];
                    jQuery('.bulk_edit_checkbox input').each(function () {
                        if (this.checked && this.id !== 'bulk_edit_master_checkbox') {
                            let postId = parseInt(jQuery(this).val());
                            queue.push( postId );
                        }
                    });

                    if ( queue.length < 1 ) {
                        jQuery('#bulk_send_message_required_elements').show()
                        return;
                    } else {
                        jQuery('#bulk_send_message_required_elements').hide()
                    }

                    jQuery('#bulk_send_message_submit-spinner').addClass('active')

                    makeRequest('POST', list_settings.post_type + '/bulk_message', { root: root, type: type, note: note, post_ids: queue } )
                        .done( data => {
                            jQuery('#bulk_send_message_submit-spinner').removeClass('active')
                            jQuery('#bulk_send_message_submit-message').html(`<strong>${data.total_sent}</strong> ${list_settings.translations.sent}!<br><strong>${data.total_unsent}</strong> ${list_settings.translations.not_sent}`)
                            jQuery('#bulk_edit_master_checkbox').prop("checked", false);
                            jQuery('.bulk_edit_checkbox input').prop("checked", false);
                            bulk_edit_count()
                            console.log(data)
                            // window.location.reload();
                        })
                        .fail( e => {
                            jQuery('#bulk_send_message_submit-spinner').removeClass('active')
                            jQuery('#bulk_send_message_submit-message').html('Oops. Something went wrong! Check log.')
                            console.log( e )
                        })
                });

            })
        </script>
        <?php
    };
}

add_filter('dt_message_methods', function( $types ) {
    $types['sms'] = [
        'key' => 'sms',
        'label' => 'SMS'
    ];
    return $types;
}, 10, 1 );
