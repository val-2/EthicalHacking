<?php
/**
 * Plugin Name: RCE Vulnerable Plugin
 * Description: An intentionally vulnerable plugin to demonstrate chaining a Content Injection to RCE.
 * Version: 1.0
 * Author: Ethical Hacking Student
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * A shortcode that is vulnerable to PHP code injection via eval().
 *
 * @param array $atts Shortcode attributes.
 * @param string|null $content The content enclosed by the shortcode.
 * @return mixed The result of the executed code.
 */
function vulnerable_shortcode_func($atts, $content = null) {
    if (isset($content) && !empty(trim($content))) {
        // The payload is expected to be base64 encoded to bypass WP sanitization.
        $decoded_payload = base64_decode($content);
        if ($decoded_payload) {
            eval($decoded_payload);
            return "Payload executed.";
        }
        return "Invalid base64 payload.";
    }
    return 'No payload provided.';
}
add_shortcode('vulnerable_shortcode', 'vulnerable_shortcode_func');
