<?php
/**
 * Theme Developer Admin Menu
 */
 
// Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}
//
 
if ( ! class_exists( 'Moyna_Developer' ) ) {
    class Moyna_Developer {
        private $config;
        private $theme_name;
        private $theme_version;
        private $page_title;
        private $menu_title;

        /**
         * Constructor.
         */
        public function __construct( $config ) {
            $this->config = $config;
            $this->initialize();
        }

        /**
         * Initialize class properties and hooks.
         */
        private function initialize() {
            $theme = wp_get_theme();
            $this->theme_name    = esc_attr( $theme->get( 'Name' ) );
            $this->theme_version = $theme->get( 'Version' );
            
            // Fix dynamic variables in translation functions using sprintf
            $this->page_title = esc_html__( '%s Developer', 'moyna' );
            $this->menu_title = esc_html__( '%s Theme', 'moyna' );

            add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        }

        /**
         * Add Theme Developer Menu under Appearance.
         */
        public function add_admin_menu() {
            add_theme_page(
                sprintf( $this->page_title, $this->theme_name ), 
                sprintf( $this->menu_title, $this->theme_name ), 
                'edit_theme_options',
                'moyna-developer',
                [ $this, 'render_screen' ]
            );
        }

        /**
         * Render the developer content screen.
         */
        public function render_screen() {
            $config = $this->config;

            echo '<div class="information-wrap">';

            if ( ! empty( $config['developer_title'] ) ) {
                echo '<h1 class="theme-name-moyna">' . esc_html( $config['developer_title'] ) . ' ' . esc_html( $this->theme_version ) . '</h1>';
            }

            if ( ! empty( $config['developer_content'] ) ) {
                echo '<p class="theme-description-moyna">' . wp_kses_post( $config['developer_content'] ) . '</p>';
            }

            if ( ! empty( $config['quick_links'] ) && is_array( $config['quick_links'] ) ) {
                echo '<div class="quick-links">';
                foreach ( $config['quick_links'] as $link ) {
                    echo '<a href="' . esc_url( $link['url'] ) . '" target="_blank" class="button-link">' . esc_html( $link['text'] ) . '</a>';
                }
                echo '</div>';
            }

            // Handle the feedback form submission
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Get the form data
                $email = sanitize_email($_POST['email']);
                $feedback = sanitize_textarea_field($_POST['feedback']);
                
                // Prepare the email content
                $subject = 'Feedback from ' . $email;
                $body = "Feedback: \n" . $feedback . "\n\nSent from: " . $email;
                $headers = 'From: ' . $email;

                // Attempt to send the email using PHP's mail() function
                $mail_sent = mail('developer@example.com', $subject, $body, $headers);

                // Check if mail() succeeded
                if ($mail_sent) {
                    echo '<p>' . esc_html__('Thank you for your feedback!', 'moyna') . '</p>';
                } else {
                    // If mail fails, redirect to Gmail or email client with the pre-filled content
                    echo '<script type="text/javascript">
                            var email = "' . esc_js($email) . '";
                            var feedback = "' . esc_js($feedback) . '";
                            var subject = "Feedback from " + email;
                            var body = "Feedback: \\n" + feedback + "\\n\\nSent from: " + email;
                            window.location.href = "mailto:developer@example.com?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);
                          </script>';
                }
            } else {
                // Show the form if it's not a POST request
                ?>
                <div class="feedback-form">
                    <h2><?php echo esc_html__('Feedback', 'moyna'); ?></h2>
                    <form method="post" action="">
                        <p><label><?php echo esc_html__('Your Email:', 'moyna'); ?></label><br><input type="email" name="email" required></p>
                        <p><label><?php echo esc_html__('Your Feedback:', 'moyna'); ?></label><br><textarea name="feedback" required></textarea></p>
                        <p><button type="submit"><?php echo esc_html__('Submit Feedback', 'moyna'); ?></button></p>
                    </form>
                </div>
                <?php
            }

            echo '</div>'; // End information-wrap
        }

        /**
         * Enqueue CSS for the admin page.
         */
        public function enqueue_assets( $hook_suffix ) {
            wp_enqueue_style( 'moyna-theme-developer-css', get_template_directory_uri() . '/developers/assets/developers.css' );
        }
    }
}

// Configuration setup
$theme_name = esc_attr( wp_get_theme()->get( 'Name' ) );
$config = [
    'developer_title'   => sprintf( esc_html__( 'Welcome to %s', 'moyna' ), $theme_name ),
    'developer_content' => sprintf( esc_html__( '%s is ideal for micro blogs, social networks, and news headlines.', 'moyna' ), $theme_name ),
    'quick_links'       => [
        [ 'text' => esc_html__( 'Live Preview', 'moyna' ), 'url' => 'https://wp-themes.com/moyna/' ],
        [ 'text' => esc_html__( 'Introduction to Theme', 'moyna' ), 'url' => 'https://store.devilhunter.net/wordpress-theme/moyna' ],
        [ 'text' => esc_html__( 'Theme on WordPress.org', 'moyna' ), 'url' => 'https://wordpress.org/themes/moyna/' ],
        [ 'text' => esc_html__( 'Web Documentation', 'moyna' ), 'url' => 'https://store.devilhunter.net/documentation/moyna/' ],
        [ 'text' => esc_html__( 'Theme Developer', 'moyna' ), 'url' => 'https://www.tawhidurrahmandear.com/' ],
        [ 'text' => esc_html__( 'Rate and Review', 'moyna' ), 'url' => 'https://wordpress.org/support/theme/moyna/reviews/#new-post' ],
        [ 'text' => esc_html__( 'Released under GPL 2.0 or later', 'moyna' ), 'url' => 'https://www.gnu.org/licenses/gpl-3.0.en.html' ],
    ],
];

return new Moyna_Developer( $config );
?>
