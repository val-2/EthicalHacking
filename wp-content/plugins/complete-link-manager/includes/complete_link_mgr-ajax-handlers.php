<?php
if (!defined('ABSPATH')) {
    exit;
}

class complete_link_mgr_Ajax_Handlers  {
    public function __construct(){
        add_action('wp_ajax_complete_link_mgr_update_link', [$this, 'complete_link_mgr_update_link']);
        add_action('wp_ajax_complete_link_mgr_unlink_action', [$this, 'complete_link_mgr_unlink_action']);
    }

    public function complete_link_mgr_update_link() {

        check_ajax_referer('complete_link_mgr_nonce', 'nonce');

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash ($_POST['nonce'])), 'complete_link_mgr_nonce')) {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }
    
        $post_id = intval($_POST['post_id']);

        $link_url = esc_url_raw(wp_unslash($_POST['link_url']));
        $target_index = isset($_POST['target_index']) ? intval($_POST['target_index']) : 0;
    
        $post_content = get_post_field('post_content', $post_id);
        $pattern = '/<a\s+href="[^"]*"(.*?)>(.*?)<\/a>/';
    
        $matches = [];
        preg_match_all($pattern, $post_content, $matches, PREG_OFFSET_CAPTURE);
    
        if (isset($matches[0][$target_index])) {
            $full_tag = $matches[0][$target_index][0];
            $start_offset = $matches[0][$target_index][1];
            $current_href = $matches[1][$target_index][0];
            $inner_html = $matches[2][$target_index][0];
    
            $updated_tag = preg_replace(
                '/href="([^"]*)"/',
                'href="' . $link_url . '"',
                $full_tag
            );
    
            $updated_content = substr_replace($post_content, $updated_tag, $start_offset, strlen($full_tag));
    
            if ($updated_content) {
                $updated = wp_update_post([
                    'ID' => $post_id,
                    'post_content' => $updated_content,
                ]);
    
                if ($updated) {
                    $post_title = get_the_title($post_id);
                    $response = wp_remote_get($link_url);
                    $status_code = !empty(wp_remote_retrieve_response_code($response)) ? wp_remote_retrieve_response_code($response) : 'Unknown';
    
                    wp_send_json_success([
                        'message' => 'URL updated successfully.',
                        'old_link_url' => $current_href,
                        'new_link_url' => $link_url,
                        'inner_html' => $inner_html,
                        'status_code' => $status_code,
                        'target_index' => $target_index,
                        'page_name' => $post_title,
                        'edit_page_url' => get_edit_post_link($post_id),
                    ]);
                }
            }
        }
    
        wp_send_json_error(['message' => 'Link update failed.']);
    }

    public function complete_link_mgr_unlink_action() {

        check_ajax_referer('complete_link_mgr_nonce', 'nonce');
        
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash ($_POST['nonce'])), 'complete_link_mgr_nonce')) {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }
    
        $post_id = intval($_POST['post_id']);
        $link_url = esc_url_raw(wp_unslash($_POST['link_url']));
        $link_text = sanitize_text_field($_POST['link_text']);
        $target_index = isset($_POST['target_index']) ? intval($_POST['target_index']) : 0;
    
        $post_content = get_post_field('post_content', $post_id);

        $pattern = '/<a\s+href="([^"]+)"[^>]*>(.*?)<\/a>/';
    
        $matches = [];
        preg_match_all($pattern, $post_content, $matches, PREG_OFFSET_CAPTURE);
    
        if (isset($matches[0][$target_index])) {

            $match_to_remove = $matches[0][$target_index][0]; 
            $link_text_to_keep = $matches[2][$target_index][0];
            $updated_content = substr_replace($post_content, $link_text_to_keep, $matches[0][$target_index][1], strlen($match_to_remove));

            if ($updated_content !== $post_content) {
                $updated = wp_update_post([
                    'ID' => $post_id,
                    'post_content' => $updated_content,
                ]);
    
                if ($updated) {
                    wp_send_json_success([
                        'message' => 'Link removed successfully.',
                        'plain_text' => $link_text,
                    ]);
                } else {
                    wp_send_json_error(['message' => 'Failed to remove the link.']);
                }
            } else {
                wp_send_json_error(['message' => 'No changes detected.']);
            }
        } else {
            wp_send_json_error(['message' => 'No link found at the specified index.']);
        }
    }
}
new complete_link_mgr_Ajax_Handlers();
?>