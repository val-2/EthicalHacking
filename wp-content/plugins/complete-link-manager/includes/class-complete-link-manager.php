<?php
if (!defined('ABSPATH')) {
    exit;
}

class complete_link_mgr_CompleteLinkManager {

    public function __construct(){
        add_action('admin_menu', [$this, 'complete_link_mgr_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'complete_link_mgr_enqueue_scripts']);
    }

    public function complete_link_mgr_admin_menu() {
        add_submenu_page(
            'tools.php',
            __('Link Manager', 'complete-link-manager'), 
            __('Link Manager', 'complete-link-manager'),  
            'manage_options',
            'complete-link-manager',  
            [$this, 'complete_link_mgr_dashboard']
        );
    }

    public function complete_link_mgr_enqueue_scripts($hook) {
        if ('tools_page_complete-link-manager' === $hook) {
            wp_enqueue_style('complete_link_mgr-datatables-style', plugin_dir_url(__FILE__).'../assets/css/complete_link_mgr.dataTables.css', [], '2.1.8', 'all');
            wp_enqueue_style('complete_link_mgr-style', plugin_dir_url(__FILE__).'../assets/css/complete_link_mgr-admin-css.css', [], filemtime( plugin_dir_url(__FILE__) . 'assets/css/clm-admin-css'));

            wp_enqueue_script('jquery');
            wp_enqueue_script('complete_link_mgr-datatables-script', plugin_dir_url(__FILE__).'../assets/js/complete_link_mgr.dataTables.js', ['jquery'], '2.1.8', true);
            wp_enqueue_script('complete_link_mgr-script', plugin_dir_url(__FILE__).'../assets/js/complete_link_mgr-admin-js.js', ['jquery', 'complete_link_mgr-datatables-script'], filemtime( plugin_dir_url(__FILE__) . 'assets/js/clm-admin-js.js'), true);
            wp_localize_script('complete_link_mgr-script', 'complete_link_mgr_ajax_obj', [
                'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('complete_link_mgr_nonce')
            ]);
        }
    }

    public function complete_link_mgr_dashboard() {
        ?>
        <div class="wrap clm-main">
            <h1>Complete Link Manager</h1>
            <?php
            $args = [
                'post_type' => 'any',
                'posts_per_page' => -1,
            ];
    
            $posts = get_posts($args);
            $links_data = [];
    
            foreach ($posts as $post) {
                $post_links = $this->complete_link_mgr_get_all_links_with_text_from_content($post->ID);
    
                $link_target_index = 0;
                foreach ($post_links as $link) {
                    $links_data[] = [
                        'page_id' => $post->ID,
                        'page_name' => $post->post_title,
                        'page_url' => get_permalink($post->ID),
                        'link_text' => $link['text'],
                        'link_url' => $link['url'],
                        'link_target_index' => $link_target_index,
                        'status_code' => $link['status'],
                        'edit_page_url' => get_edit_post_link($post->ID),
                    ];
                    $link_target_index++;
                }
            }
    
            usort($links_data, function ($a, $b) {
                $priority = [404 => 1, 500 => 2, 301 => 3, 302 => 4, 200 => 5, 'default' => 6];
    
                $a_priority = $priority[$a['status_code']] ?? $priority['default'];
                $b_priority = $priority[$b['status_code']] ?? $priority['default'];
    
                return $a_priority - $b_priority;
            });
    
            echo '<table id="complete-links-table" class="complete-links-table wp-list-table widefat fixed striped">';
            echo '<thead>
                    <tr>
                        <th>URL</th>
                        <th>Link Text</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Actions</th>
                    </tr>
                </thead>';
            echo '<tbody>';
    
            if (!empty($links_data)) {
                $index = 0;
                foreach ($links_data as $link) {
                    $status_class = match ($link['status_code']) {
                        200 => 'status-green',
                        301, 302 => 'status-blue',
                        404 => 'status-red',
                        default => 'status-orange',
                    };
    
                    // Check if page is edited with any builder
                    $post_content = get_post_field('post_content', $link['page_id']);
                    $elementor_page = get_post_meta($link['page_id'], '_elementor_edit_mode', true);
                    $is_disabled = false;
                    $disabled_message = '';
    
                    if ($elementor_page) {
                        $is_disabled = true;
                    } else if (strpos($post_content, 'et_pb_section') !== false || strpos($post_content, 'et_pb_row') !== false || strpos($post_content, 'et_pb_column') !== false) {
                        $is_disabled = true;
                    } else if (strpos($post_content, '[vc_row') !== false || strpos($post_content, '[vc_column')) {
                        $is_disabled = true;
                    } else if (strpos($post_content, '[fl_builder') !== false) {
                        $is_disabled = true;
                    } else if (strpos($post_content, '[fusion_builder_row') !== false || 
                        strpos($post_content, '[fusion_builder_column') !== false || 
                        strpos($post_content, '[fusion_text') !== false) {
                        $is_disabled = true;
                    }
    
                    echo '<tr row-index="' . esc_html($index) . '" data-link-url="' . esc_url($link['link_url']) . '">';
                    echo '<td>' . esc_html($link['editor_type']) . '<a href="' . esc_url($link['link_url']) . '" target="_blank" class="link-' . esc_html($status_class) . '">' . esc_url($link['link_url']) . '</a></td>';
                    echo '<td>' . esc_html($link['link_text']) . '</td>';
                    echo '<td><span class="' . esc_html($status_class) . '">' . esc_html($link['status_code']) . '</span></td>';
                    echo '<td>' . esc_html($link['page_name']) . '</td>';
                    echo '<td>';
    
                    if ($is_disabled) {
                        echo '<a href="https://profiles.wordpress.org/developer1998/" target="_blank" class="button upgrade-pro" title="Upgrade Pro"><span class="dashicons dashicons-lock"></span> Upgrade Pro</button>';
                    } else {
                        echo '<div class="clm-action-buttons">';
                        echo '<button class="button edit-link" data-link-url="' . esc_url($link['link_url']) . '" data-link-text="' . esc_html($link['link_text']) . '" data-post-id="' . esc_html($link['page_id']) . '" target-index="' . esc_html($link['link_target_index']) . '" title="Edit URL"><span class="dashicons dashicons-edit-large"></span></button>';
                        echo '<button class="button unlink" data-link-url="' . esc_url($link['link_url']) . '" data-link-text="' . esc_html($link['link_text']) . '" data-post-id="' . esc_html($link['page_id']) . '" target-index="' . esc_html($link['link_target_index']) . '" title="Unlink"><span class="dashicons dashicons-editor-unlink"></span></button>';
                        echo '<a class="button edit-page" href="' . esc_url($link['edit_page_url']) . '" target="_blank" title="Edit post/page"><span class="dashicons dashicons-edit-page"></span></a>';
                        echo '</div>';
                    }
                    echo '</td>';
                    echo '</tr>';
                    $index++;
                }
            } else {
                echo '<tr><td colspan="5">No links found.</td></tr>';
            }
    
            echo '</tbody>';
            echo '</table>';
    
            echo '<div id="unlinkConfirmationModal" class="custom-modal" style="display: none;">
                <div class="white-popup-block">
                <h2>Confirmation</h2>
                <p>Are you sure you want to remove this link?</p>
                <button id="confirmUnlink" class="button button-primary">Yes</button>
                <button id="cancelUnlink" class="button">No</button>
                </div>
            </div>';
    
            echo '<div id="editLinkModal" class="custom-modal" style="display: none;">
                <div class="white-popup-block">
                <h2>Edit Link</h2>
                <form id="editLinkForm">
                    <div class="input-field-section">
                    <input type="hidden" id="postId" name="post_id" value="">
                    <input type="hidden" id="old_link_url" name="old_link_url" value="">
                    <input type="hidden" id="row_index" name="row_index" value="">
                    <input type="hidden" id="target_index" name="target_index" value="">
                    <input type="hidden" id="linkText" name="link_text" required>
                    <label for="linkUrl">New URL:</label><br/>
                    <input type="url" id="linkUrl" name="link_url" required>
                    </div>
                    <div class="input-action-section">
                    <button type="submit" class="button button-primary">Update Link</button>
                    <button type="button" id="closeModal" class="button">Close</button>
                    </div>
                </form>
                </div>
            </div>';
            ?>
        </div>
        <?php
    }    
    
    private function complete_link_mgr_get_all_links_with_text_from_content($post_id) {
        $content = get_post_field('post_content', $post_id);
        $links_with_text_and_status = [];
    
        if (empty($content)) {
            return $links_with_text_and_status;
        }
    
        $content = '<!DOCTYPE html><html><body>' . $content . '</body></html>';
        $dom = new DOMDocument();
    
        libxml_use_internal_errors(true);
    
        try {
            $dom->loadHTML($content, LIBXML_NOERROR | LIBXML_NOWARNING);
        } catch (Exception $e) {
            libxml_clear_errors();
            return $links_with_text_and_status;
        }
    
        libxml_clear_errors();
    
        foreach ($dom->getElementsByTagName('a') as $link) {
            $href = $link->getAttribute('href');
            $text = trim($link->textContent);
    
            if (!empty($href) && strpos($href, '#') !== 0) {
                $response = wp_remote_get($href);
                $status_code = is_wp_error($response) ? 'Unknown' : wp_remote_retrieve_response_code($response);
    
                $links_with_text_and_status[] = [
                    'url' => $href,
                    'text' => $text,
                    'status' => $status_code,
                ];
            }
        }
    
        return $links_with_text_and_status;
    }  
}
?>