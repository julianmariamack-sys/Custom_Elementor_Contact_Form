<?php
/**
 * Plugin Name: Custom Elementor Contact Form
 * Description: A lightweight Elementor contact form widget styled like a modern dark contact panel. <a href="tools.php?page=custom-elementor-contact-form">Configure/test emails</a>
 * Version: 1.3.3
 * Author: JM Custom Web Dev & IT
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: custom-elementor-contact-form
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CECF_VERSION', '1.3.3' );
define( 'CECF_PATH', plugin_dir_path( __FILE__ ) );
define( 'CECF_URL', plugin_dir_url( __FILE__ ) );

final class CECF_Plugin {

    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'init' ] );
    }

    public function init() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'elementor_missing_notice' ] );
            return;
        }

        add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'wp_ajax_cecf_submit', [ $this, 'handle_submission' ] );
        add_action( 'wp_ajax_nopriv_cecf_submit', [ $this, 'handle_submission' ] );
        add_action( 'admin_menu', [ $this, 'register_tools_page' ] );
        add_action( 'admin_post_cecf_send_test', [ $this, 'send_test_email' ] );
        add_action( 'wp_mail_failed', [ $this, 'capture_mail_failure' ] );

    }

    public function register_assets() {
        wp_register_style(
            'cecf-style',
            CECF_URL . 'assets/css/contact-form.css',
            [],
            CECF_VERSION
        );

        wp_register_script(
            'cecf-script',
            CECF_URL . 'assets/js/contact-form.js',
            [],
            CECF_VERSION,
            true
        );

        wp_localize_script(
            'cecf-script',
            'CECF_DATA',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'sending' => __( 'Sending…', 'custom-elementor-contact-form' ),
                'error'   => __( 'Something went wrong. Please try again.', 'custom-elementor-contact-form' ),
            ]
        );
    }

    public function register_widget( $widgets_manager ) {
        require_once CECF_PATH . 'includes/class-cecf-widget.php';
        $widgets_manager->register( new \CECF_Widget() );
    }


    public function register_tools_page() {
        add_management_page(
            __( 'Custom Contact Form', 'custom-elementor-contact-form' ),
            __( 'Custom Contact Form', 'custom-elementor-contact-form' ),
            'manage_options',
            'custom-elementor-contact-form',
            [ $this, 'render_tools_page' ]
        );
    }

    private function get_mail_debug_log() {
        $log = get_option( 'cecf_mail_debug_log', [] );
        return is_array( $log ) ? $log : [];
    }

    private function add_mail_debug_entry( $entry ) {
        $log = $this->get_mail_debug_log();
        array_unshift( $log, array_merge(
            [
                'time' => current_time( 'mysql' ),
            ],
            $entry
        ) );
        $log = array_slice( $log, 0, 50 );
        update_option( 'cecf_mail_debug_log', $log, false );
    }

    public function capture_mail_failure( $wp_error ) {
        if ( ! is_wp_error( $wp_error ) ) {
            return;
        }

        $this->add_mail_debug_entry(
            [
                'type' => 'wp_mail_failed',
                'status' => 'FAILED',
                'message' => $wp_error->get_error_message(),
                'data' => wp_json_encode( $wp_error->get_error_data() ),
            ]
        );
    }

    private function get_brevo_status() {
        $active = (array) get_option( 'active_plugins', [] );

        $network_active = [];
        if ( is_multisite() ) {
            $network_active = array_keys( (array) get_site_option( 'active_sitewide_plugins', [] ) );
        }

        $plugins = array_merge( $active, $network_active );
        $brevo_active = false;

        foreach ( $plugins as $plugin_file ) {
            $plugin_lower = strtolower( (string) $plugin_file );
            if ( strpos( $plugin_lower, 'brevo' ) !== false || strpos( $plugin_lower, 'mailin' ) !== false ) {
                $brevo_active = true;
                break;
            }
        }

        return $brevo_active;
    }

    public function render_tools_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $recipient = get_option( 'admin_email' );
        $brevo_active = $this->get_brevo_status();
        $log = $this->get_mail_debug_log();
        $test_status = isset( $_GET['cecf_test'] ) ? sanitize_text_field( wp_unslash( $_GET['cecf_test'] ) ) : '';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Custom Contact Form Diagnostics', 'custom-elementor-contact-form' ); ?></h1>

            <?php if ( 'sent' === $test_status ) : ?>
                <div class="notice notice-success"><p><?php esc_html_e( 'WordPress accepted the test email through wp_mail(). Check Brevo Transactional → Logs for the message.', 'custom-elementor-contact-form' ); ?></p></div>
            <?php elseif ( 'failed' === $test_status ) : ?>
                <div class="notice notice-error"><p><?php esc_html_e( 'wp_mail() reported a failure. See the Mail Debug Log below for the error.', 'custom-elementor-contact-form' ); ?></p></div>
            <?php endif; ?>

            <table class="widefat striped" style="max-width:900px;margin-top:20px">
                <tbody>
                    <tr>
                        <th><?php esc_html_e( 'wp_mail() available', 'custom-elementor-contact-form' ); ?></th>
                        <td><strong><?php echo function_exists( 'wp_mail' ) ? 'YES' : 'NO'; ?></strong></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Brevo WordPress plugin detected', 'custom-elementor-contact-form' ); ?></th>
                        <td><strong><?php echo $brevo_active ? 'YES' : 'NO'; ?></strong></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Default WordPress recipient', 'custom-elementor-contact-form' ); ?></th>
                        <td><code><?php echo esc_html( $recipient ); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Mail transport used by this plugin', 'custom-elementor-contact-form' ); ?></th>
                        <td><code>wp_mail()</code></td>
                    </tr>
                </tbody>
            </table>

            <h2><?php esc_html_e( 'Send a direct wp_mail() test', 'custom-elementor-contact-form' ); ?></h2>
            <p><?php esc_html_e( 'This test bypasses Elementor and sends a simple message directly through WordPress wp_mail(). If Brevo SMTP is active, the Brevo plugin should route it through Brevo.', 'custom-elementor-contact-form' ); ?></p>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="cecf_send_test">
                <?php wp_nonce_field( 'cecf_send_test' ); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th><label for="cecf_test_recipient"><?php esc_html_e( 'Test recipient', 'custom-elementor-contact-form' ); ?></label></th>
                        <td><input class="regular-text" type="email" id="cecf_test_recipient" name="test_recipient" value="<?php echo esc_attr( $recipient ); ?>" required></td>
                    </tr>
                </table>
                <?php submit_button( __( 'Send Test Email', 'custom-elementor-contact-form' ) ); ?>
            </form>

            <h2><?php esc_html_e( 'Mail Debug Log', 'custom-elementor-contact-form' ); ?></h2>
            <p><?php esc_html_e( 'The plugin records the result of its form mail attempts and wp_mail_failed events. Up to 50 entries are retained.', 'custom-elementor-contact-form' ); ?></p>

            <?php if ( empty( $log ) ) : ?>
                <p><em><?php esc_html_e( 'No mail events have been recorded yet.', 'custom-elementor-contact-form' ); ?></em></p>
            <?php else : ?>
                <table class="widefat striped" style="max-width:1100px">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Time', 'custom-elementor-contact-form' ); ?></th>
                            <th><?php esc_html_e( 'Type', 'custom-elementor-contact-form' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'custom-elementor-contact-form' ); ?></th>
                            <th><?php esc_html_e( 'Recipient', 'custom-elementor-contact-form' ); ?></th>
                            <th><?php esc_html_e( 'Message / Error', 'custom-elementor-contact-form' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $log as $entry ) : ?>
                        <tr>
                            <td><?php echo esc_html( isset( $entry['time'] ) ? $entry['time'] : '' ); ?></td>
                            <td><?php echo esc_html( isset( $entry['type'] ) ? $entry['type'] : '' ); ?></td>
                            <td><?php echo esc_html( isset( $entry['status'] ) ? $entry['status'] : '' ); ?></td>
                            <td><?php echo esc_html( isset( $entry['recipient'] ) ? $entry['recipient'] : '' ); ?></td>
                            <td><code><?php echo esc_html( isset( $entry['message'] ) ? $entry['message'] : '' ); ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }

    public function send_test_email() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to run this test.', 'custom-elementor-contact-form' ) );
        }

        check_admin_referer( 'cecf_send_test' );

        $recipient = isset( $_POST['test_recipient'] )
            ? sanitize_email( wp_unslash( $_POST['test_recipient'] ) )
            : '';

        if ( ! is_email( $recipient ) ) {
            wp_die( esc_html__( 'Please enter a valid test recipient.', 'custom-elementor-contact-form' ) );
        }

        $subject = 'Vaultcore Contact Form — Mail Delivery Test';
        $body = "This is a diagnostic email from the Custom Elementor Contact Form plugin.\n\n";
        $body .= 'Time: ' . current_time( 'mysql' ) . "\n";
        $body .= 'Site: ' . home_url( '/' ) . "\n";
        $body .= 'If you received this through Brevo, the WordPress-to-Brevo mail path is working.';

        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
        ];

        $sent = wp_mail( $recipient, $subject, $body, $headers );

        $this->add_mail_debug_entry(
            [
                'type' => 'direct_test',
                'status' => $sent ? 'SUCCESS' : 'FAILED',
                'recipient' => $recipient,
                'message' => $sent
                    ? 'wp_mail() returned TRUE.'
                    : 'wp_mail() returned FALSE. Check wp_mail_failed entries and the active mailer.',
            ]
        );

        $redirect = add_query_arg(
            [
                'page' => 'custom-elementor-contact-form',
                'cecf_test' => $sent ? 'sent' : 'failed',
            ],
            admin_url( 'tools.php' )
        );

        wp_safe_redirect( $redirect );
        exit;
    }

    public function elementor_missing_notice() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        echo '<div class="notice notice-warning"><p>';
        echo esc_html__( 'Custom Elementor Contact Form requires Elementor to be installed and activated.', 'custom-elementor-contact-form' );
        echo '</p></div>';
    }

    public function handle_submission() {
        if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'cecf_submit' ) ) {
            wp_send_json_error( [ 'message' => __( 'Security check failed.', 'custom-elementor-contact-form' ) ], 403 );
        }

        // Honeypot for simple bot protection.
        if ( ! empty( $_POST['website'] ) ) {
            wp_send_json_success( [ 'message' => __( 'Thank you. Your message has been sent.', 'custom-elementor-contact-form' ) ] );
        }

        $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
        $consent = ! empty( $_POST['consent'] );

        if ( ! $name || ! is_email( $email ) || ! $message || ! $consent ) {
            wp_send_json_error( [ 'message' => __( 'Please complete all required fields and accept the consent statement.', 'custom-elementor-contact-form' ) ], 422 );
        }

        // Use the destination configured in the Elementor widget. Fall back to
        // the WordPress administrator email only if the submitted value is
        // missing or invalid.
        $to = isset( $_POST['recipient'] )
            ? sanitize_email( wp_unslash( $_POST['recipient'] ) )
            : '';

        if ( ! is_email( $to ) ) {
            $to = get_option( 'admin_email' );
        }

        $subject = isset( $_POST['email_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['email_subject'] ) ) : '';
        if ( ! $subject ) {
            $subject = sprintf(
                /* translators: %s: sender name */
                __( 'Website contact form message from %s', 'custom-elementor-contact-form' ),
                $name
            );
        }

        $from_name = isset( $_POST['from_name'] ) ? sanitize_text_field( wp_unslash( $_POST['from_name'] ) ) : get_bloginfo( 'name' );
        $from_email = isset( $_POST['from_email'] ) ? sanitize_email( wp_unslash( $_POST['from_email'] ) ) : get_option( 'admin_email' );
        if ( ! is_email( $from_email ) ) {
            $from_email = get_option( 'admin_email' );
        }

        // Use the Elementor subject as the actual email subject. Existing saved
        // widget values are preserved; the widget's new default is more descriptive.
        // The visitor's address is always Reply-To so the recipient can simply hit Reply.
        $site_name = get_bloginfo( 'name' );
        $site_url  = home_url( '/' );
        $submitted = current_time( 'mysql' );

        // HTML email for a clean, readable notification.
        $body  = '<!doctype html><html><body style="margin:0;padding:24px;background:#f4f4f4;font-family:Arial,sans-serif;color:#222;">';
        $body .= '<div style="max-width:680px;margin:0 auto;background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden;">';
        $body .= '<div style="padding:20px 24px;background:#1c1c1c;color:#fff;"><h2 style="margin:0;font-size:22px;font-weight:600;">New Contact Form Submission</h2></div>';
        $body .= '<div style="padding:24px;">';
        $body .= '<table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-collapse:collapse;">';
        $body .= '<tr><td style="padding:8px 0;font-weight:600;width:120px;">Name</td><td style="padding:8px 0;">' . esc_html( $name ) . '</td></tr>';
        $body .= '<tr><td style="padding:8px 0;font-weight:600;">Email</td><td style="padding:8px 0;"><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></td></tr>';
        $body .= '<tr><td style="padding:8px 0;font-weight:600;vertical-align:top;">Message</td><td style="padding:8px 0;white-space:pre-wrap;">' . nl2br( esc_html( $message ) ) . '</td></tr>';
        $body .= '<tr><td style="padding:8px 0;font-weight:600;">Submitted</td><td style="padding:8px 0;">' . esc_html( $submitted ) . '</td></tr>';
        $body .= '</table>';
        $body .= '</div>';
        $body .= '<div style="padding:14px 24px;background:#f7f7f7;color:#666;font-size:12px;">Sent from ' . esc_html( $site_name ) . ' — ' . esc_html( $site_url ) . '</div>';
        $body .= '</div></body></html>';

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $name . ' <' . $email . '>',
        ];

        // When Brevo SMTP is active, let Brevo use the verified sender selected
        // in its WordPress settings instead of forcing an arbitrary From address.
        // This avoids conflicts with Brevo's verified sender/domain requirements.
        // For other mailers, retain the Elementor-configured From name/address.
        if ( ! $this->get_brevo_status() ) {
            $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
        }

        $sent = wp_mail( $to, $subject, $body, $headers );

        $this->add_mail_debug_entry(
            [
                'type' => 'form_submission',
                'status' => $sent ? 'SUCCESS' : 'FAILED',
                'recipient' => $to,
                'message' => $sent
                    ? 'wp_mail() returned TRUE.'
                    : 'wp_mail() returned FALSE. Check wp_mail_failed entries and the active mailer.',
            ]
        );

        if ( ! $sent ) {
            wp_send_json_error( [ 'message' => __( 'The message could not be sent. Please try again later.', 'custom-elementor-contact-form' ) ], 500 );
        }

        wp_send_json_success( [ 'message' => __( 'Thank you! Your message has been sent.', 'custom-elementor-contact-form' ) ] );
    }
}

new CECF_Plugin();
