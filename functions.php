/**
 * Theme info
 * developed by Tawhidur Rahman Dear, https://www.tawhidurrahmandear.com 
 * Released under GPL-2.0 license on Github at https://github.com/tawhidurrahmandear/wordpress-developer-page 
 */
require get_theme_file_path( '/developers/developers.php' );

function moyna_theme_styles() {
    wp_enqueue_style('moyna-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'moyna_theme_styles');