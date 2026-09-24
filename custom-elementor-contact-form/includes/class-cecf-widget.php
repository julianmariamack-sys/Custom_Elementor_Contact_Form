<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

class CECF_Widget extends Widget_Base {

    public function get_name() {
        return 'custom-elementor-contact-form';
    }

    public function get_title() {
        return __( 'Custom Contact Form', 'custom-elementor-contact-form' );
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_keywords() {
        return [ 'contact', 'form', 'email', 'message', 'custom' ];
    }

    public function get_style_depends() {
        return [ 'cecf-style' ];
    }

    public function get_script_depends() {
        return [ 'cecf-script' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'custom-elementor-contact-form' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'eyebrow',
            [
                'label'       => __( 'Eyebrow', 'custom-elementor-contact-form' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'CONTACT US',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'heading',
            [
                'label'       => __( 'Heading', 'custom-elementor-contact-form' ),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => "Have questions?\nGet in touch!",
                'label_block' => true,
            ]
        );

        $this->add_control(
            'name_placeholder',
            [
                'label'   => __( 'Name Placeholder', 'custom-elementor-contact-form' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Your name',
            ]
        );

        $this->add_control(
            'email_placeholder',
            [
                'label'   => __( 'Email Placeholder', 'custom-elementor-contact-form' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Your e-mail',
            ]
        );

        $this->add_control(
            'message_placeholder',
            [
                'label'   => __( 'Message Placeholder', 'custom-elementor-contact-form' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Your message',
            ]
        );

        $this->add_control(
            'consent_text',
            [
                'label'   => __( 'Consent Text', 'custom-elementor-contact-form' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'I agree that my submitted data is being collected and stored.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'   => __( 'Button Text', 'custom-elementor-contact-form' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'SEND MESSAGE',
            ]
        );

        $this->add_control(
            'success_message',
            [
                'label'   => __( 'Success Message', 'custom-elementor-contact-form' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Thank you! Your message has been sent.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'email_settings_heading',
            [
                'label' => __( 'Email Settings', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'recipient_email',
            [
                'label' => __( 'Send Submissions To', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'email',
                'default' => get_option( 'admin_email' ),
                'description' => __( 'Destination address for form submissions. Existing WordPress/SMTP delivery is used.', 'custom-elementor-contact-form' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'from_name',
            [
                'label' => __( 'From Name', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::TEXT,
                'default' => get_bloginfo( 'name' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'from_email',
            [
                'label' => __( 'From Email', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'email',
                'default' => get_option( 'admin_email' ),
                'description' => __( 'Prefer an address on your website domain.', 'custom-elementor-contact-form' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'email_subject',
            [
                'label' => __( 'Email Subject', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'New Vaultcore Contact Form Submission',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'layout_section',
            [
                'label' => __( 'Layout', 'custom-elementor-contact-form' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'max_width',
            [
                'label' => __( 'Form Max Width', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [ 'min' => 300, 'max' => 1400 ],
                    '%'  => [ 'min' => 50, 'max' => 100 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 1100 ],
                'selectors' => [
                    '{{WRAPPER}} .cecf-form-inner' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'panel_padding',
            [
                'label' => __( 'Panel Padding', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 26, 'right' => 32, 'bottom' => 22, 'left' => 32,
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .cecf-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'panel_radius',
            [
                'label' => __( 'Panel Border Radius', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default' => [ 'unit' => 'px', 'size' => 12 ],
                'selectors' => [
                    '{{WRAPPER}} .cecf-panel' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'field_height',
            [
                'label' => __( 'Input Height', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [ 'px' => [ 'min' => 35, 'max' => 100 ] ],
                'default' => [ 'unit' => 'px', 'size' => 47 ],
                'selectors' => [
                    '{{WRAPPER}} .cecf-input' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'textarea_height',
            [
                'label' => __( 'Message Height', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [ 'px' => [ 'min' => 100, 'max' => 600 ] ],
                'default' => [ 'unit' => 'px', 'size' => 250 ],
                'selectors' => [
                    '{{WRAPPER}} .cecf-textarea' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_left_padding',
            [
                'label' => __( 'Field Left Padding', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
                'default' => [ 'unit' => 'px', 'size' => 20 ],
                'selectors' => [
                    '{{WRAPPER}} .cecf-input, {{WRAPPER}} .cecf-textarea' => 'padding-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'colors_section',
            [
                'label' => __( 'Colors', 'custom-elementor-contact-form' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'panel_background',
            [
                'label' => __( 'Panel Background', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#1c1c1c',
                'selectors' => [
                    '{{WRAPPER}} .cecf-panel' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_background',
            [
                'label' => __( 'Field Background', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#5b5b5d',
                'selectors' => [
                    '{{WRAPPER}} .cecf-input, {{WRAPPER}} .cecf-textarea' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __( 'Heading Color', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .cecf-heading, {{WRAPPER}} .cecf-eyebrow' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'placeholder_color',
            [
                'label' => __( 'Placeholder Color', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#eeeeee',
                'selectors' => [
                    '{{WRAPPER}} .cecf-input, {{WRAPPER}} .cecf-textarea' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cecf-input::placeholder, {{WRAPPER}} .cecf-textarea::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'consent_color',
            [
                'label' => __( 'Consent Text Color', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#777777',
                'selectors' => [
                    '{{WRAPPER}} .cecf-consent-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => __( 'Button Background', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#e6bb2d',
                'selectors' => [
                    '{{WRAPPER}} .cecf-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __( 'Button Text Color', 'custom-elementor-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .cecf-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'typography_section',
            [
                'label' => __( 'Typography', 'custom-elementor-contact-form' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eyebrow_typography',
                'label' => __( 'Eyebrow Typography', 'custom-elementor-contact-form' ),
                'selector' => '{{WRAPPER}} .cecf-eyebrow',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => __( 'Heading Typography', 'custom-elementor-contact-form' ),
                'selector' => '{{WRAPPER}} .cecf-heading',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'field_typography',
                'label' => __( 'Field Typography', 'custom-elementor-contact-form' ),
                'selector' => '{{WRAPPER}} .cecf-input, {{WRAPPER}} .cecf-textarea',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $form_id  = 'cecf-' . $this->get_id();

        wp_enqueue_style( 'cecf-style' );
        wp_enqueue_script( 'cecf-script' );

        $heading = nl2br( esc_html( $settings['heading'] ) );
        ?>
        <div class="cecf-widget">
            <div class="cecf-panel">
                <div class="cecf-form-inner">
                    <div class="cecf-eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></div>
                    <h2 class="cecf-heading"><?php echo $heading; ?></h2>

                    <form class="cecf-form" id="<?php echo esc_attr( $form_id ); ?>" novalidate>
                        <input type="hidden" name="action" value="cecf_submit">
                        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'cecf_submit' ) ); ?>">
                        <input type="hidden" name="success_message" value="<?php echo esc_attr( $settings['success_message'] ); ?>">
                        <input type="hidden" name="recipient" value="<?php echo esc_attr( $settings['recipient_email'] ); ?>">
                        <input type="hidden" name="from_name" value="<?php echo esc_attr( $settings['from_name'] ); ?>">
                        <input type="hidden" name="from_email" value="<?php echo esc_attr( $settings['from_email'] ); ?>">
                        <input type="hidden" name="email_subject" value="<?php echo esc_attr( $settings['email_subject'] ); ?>">

                        <div class="cecf-honeypot" aria-hidden="true">
                            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="cecf-field">
                            <label class="screen-reader-text" for="<?php echo esc_attr( $form_id ); ?>-name"><?php echo esc_html( $settings['name_placeholder'] ); ?></label>
                            <input class="cecf-input" id="<?php echo esc_attr( $form_id ); ?>-name" type="text" name="name" placeholder="<?php echo esc_attr( $settings['name_placeholder'] ); ?>" required>
                        </div>

                        <div class="cecf-field">
                            <label class="screen-reader-text" for="<?php echo esc_attr( $form_id ); ?>-email"><?php echo esc_html( $settings['email_placeholder'] ); ?></label>
                            <input class="cecf-input" id="<?php echo esc_attr( $form_id ); ?>-email" type="email" name="email" placeholder="<?php echo esc_attr( $settings['email_placeholder'] ); ?>" required>
                        </div>

                        <div class="cecf-field">
                            <label class="screen-reader-text" for="<?php echo esc_attr( $form_id ); ?>-message"><?php echo esc_html( $settings['message_placeholder'] ); ?></label>
                            <textarea class="cecf-textarea" id="<?php echo esc_attr( $form_id ); ?>-message" name="message" placeholder="<?php echo esc_attr( $settings['message_placeholder'] ); ?>" required></textarea>
                        </div>

                        <div class="cecf-bottom">
                            <label class="cecf-consent">
                                <input type="checkbox" name="consent" value="1" required>
                                <span class="cecf-check" aria-hidden="true"></span>
                                <span class="cecf-consent-label"><?php echo esc_html( $settings['consent_text'] ); ?></span>
                            </label>

                            <button class="cecf-button" type="submit">
                                <span class="cecf-button-text"><?php echo esc_html( $settings['button_text'] ); ?></span>
                                <span class="cecf-spinner" aria-hidden="true"></span>
                            </button>
                        </div>

                        <div class="cecf-response" role="status" aria-live="polite"></div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
