<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Disciple_Tools_Bulk_Contact_Messaging_Menu
 */
class Disciple_Tools_Bulk_Contact_Messaging_Menu {

    public $token = 'disciple_tools_bulk_contact_messaging';
    public $page_title = 'Bulk Contact Messaging';

    private static $_instance = null;

    /**
     * Disciple_Tools_Bulk_Contact_Messaging_Menu Instance
     *
     * Ensures only one instance of Disciple_Tools_Bulk_Contact_Messaging_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return Disciple_Tools_Bulk_Contact_Messaging_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {

        add_action( "admin_menu", array( $this, "register_menu" ) );

    } // End __construct()

    public function my_phpmailer_example( $phpmailer ) {
        // @see https://kb.smtp.com/article/946-php-mailer-setup




        // Additional settings…
        //$phpmailer->SMTPSecure = 'tls'; // Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587
        //$phpmailer->From = "you@yourdomail.com";
        //$phpmailer->FromName = "Your Name";
    }


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', $this->page_title, $this->page_title, 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple.Tools Theme fully loads.
     */
    public function extensions_menu() {}

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple.Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        if ( isset( $_GET["tab"] ) ) {
            $tab = sanitize_key( wp_unslash( $_GET["tab"] ) );
        } else {
            $tab = 'email';
        }

        $link = 'admin.php?page='.$this->token.'&tab=';

        ?>
        <div class="wrap">
            <h2><?php echo esc_html( $this->page_title ) ?></h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'email' ?>"
                   class="nav-tab <?php echo esc_html( ( $tab == 'email' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">Email</a>
                <a href="<?php echo esc_attr( $link ) . 'twilio' ?>" class="nav-tab <?php echo esc_html( ( $tab == 'twilio' ) ? 'nav-tab-active' : '' ); ?>">Twilio</a>
            </h2>

            <?php
            switch ( $tab ) {
                case "email":
                    $object = new DT_Bulk_Contact_Messaging_Tab_Email();
                    $object->content();
                    break;
                case "twilio":
                    $object = new DT_Bulk_Contact_Messaging_Tab_Twilio();
                    $object->content();
                    break;
                default:
                    break;
            }
            ?>

        </div><!-- End wrap -->

        <?php
    }
}
Disciple_Tools_Bulk_Contact_Messaging_Menu::instance();

/**
 * Class DT_Bulk_Contact_Messaging_Tab_Email
 */
class DT_Bulk_Contact_Messaging_Tab_Email {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        $options = dt_bulk_contact_messaging_options();

        if ( isset( $_POST['bulk_contact_messaging_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bulk_contact_messaging_nonce'] ) ), 'bulk_contact_messaging_nonce'.get_current_user_id() ) ) {
            unset( $_POST['bulk_contact_messaging_nonce'] );
            $new_options = dt_recursive_sanitize_array( $_POST );
            $new_options = wp_parse_args( $new_options, $options );
            update_option( 'dt_bulk_contact_messaging_options', $new_options );
            $options = $new_options;
        }
        ?>
        <!-- Box -->
        <form method="post">
            <?php wp_nonce_field( 'bulk_contact_messaging_nonce'.get_current_user_id(), 'bulk_contact_messaging_nonce', false ) ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Bulk Email Settings</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        From Email<br>
                        <input type="text" name="from_email" class="regular-text" value="<?php echo esc_html( $options['from_email'] ) ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        From Name<br>
                        <input type="text" name="from_name" class="regular-text" value="<?php echo esc_html( $options['from_name'] ) ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <button type="submit" class="button">Update</button>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
        <br>
        <!-- End Box -->
        <?php
    }

    public function right_column() {
    }
}


/**
 * Class DT_Bulk_Contact_Messaging_Tab_Twilio
 */
class DT_Bulk_Contact_Messaging_Tab_Twilio {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        $options = dt_bulk_contact_messaging_options();

        if ( isset( $_POST['bulk_contact_messaging_twilio_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bulk_contact_messaging_twilio_nonce'] ) ), 'bulk_contact_messaging_twilio_nonce'.get_current_user_id() ) ) {
            unset( $_POST['bulk_contact_messaging_twilio_nonce'] );
            $new_options = dt_recursive_sanitize_array( $_POST );
            $new_options = wp_parse_args( $new_options, $options );
            update_option( 'dt_bulk_contact_messaging_options', $new_options );
            $options = $new_options;
        }
        ?>
        <!-- Box -->
        <form method="post">
            <?php wp_nonce_field( 'bulk_contact_messaging_twilio_nonce'.get_current_user_id(), 'bulk_contact_messaging_twilio_nonce', false ) ?>
            <table class="widefat striped">
                <thead>
                <tr>
                    <th>Bulk Email Settings</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        Account SID<br>
                        <input type="text" name="twilio_sid" class="regular-text" value="<?php echo esc_html( $options['twilio_sid'] ) ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Auth Token<br>
                        <input type="text" name="twilio_auth" class="regular-text" value="<?php echo esc_html( $options['twilio_auth'] ) ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Twilio Number<br>
                        <input type="text" name="twilio_number" class="regular-text" value="<?php echo esc_html( $options['twilio_number'] ) ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <button type="submit" class="button">Update</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <br>
        <!-- End Box -->
        <?php
    }

    public function right_column() {

    }
}

