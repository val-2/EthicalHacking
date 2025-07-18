<?php
if (!class_exists('Newsup_Posts_List')) :
    /**
     * Adds Newsup_Posts_List widget.
     */
    class Newsup_Posts_List extends Newsup_Widget_Base {
        /**
         * Sets up a new widget instance.
         *
         * @since 1.0.0
         */
        function __construct() {
            $this->text_fields = array('newsup-categorised-posts-title', 'newsup-excerpt-length', 'newsup-posts-number');
            $this->select_fields = array('newsup-select-category', 'newsup-show-excerpt');

            $widget_ops = array(
                'classname' => 'mg-posts-sec mg-posts-modul-2',
                'description' => __('Displays posts from selected category in a list.', 'newsup'),
                'customize_selective_refresh' => true,
            );

            parent::__construct('newsup_posts_list', __('AR: Posts List', 'newsup'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args Widget arguments.
         * @param array $instance Saved values from database.
         */

        public function widget($args, $instance) {

            $instance = parent::newsup_sanitize_data($instance, $instance);

            /** This filter is documented in wp-includes/default-widgets.php */
            $title = apply_filters('widget_title', $instance['newsup-categorised-posts-title'], $instance, $this->id_base);
            $category = isset($instance['newsup-select-category']) ? $instance['newsup-select-category'] : '0';
            $number_of_posts = isset($instance['newsup-posts-number']) ? $instance['newsup-posts-number'] : 10;


            // open the widget container
            echo '<!-- widget_post_list -->' .$args['before_widget'];
            ?>
            <div class="mg-posts-sec mg-posts-modul-2 widget_post_list mt-0">
                <?php if (!empty($title)): ?>
                    <?php if (!empty($title)): ?> 
                    <div class="mg-sec-title">
                    <!-- mg-sec-title -->
                    <h4><?php echo esc_html($title); ?></h4>
                    </div>
                    <?php endif; ?>
                <?php endif;
                $all_posts = newsup_get_posts($number_of_posts, $category);
                ?>
                <!-- mg-posts-sec-inner -->
                <div class="mg-posts-sec-inner widget_post_list_inner">
                    <?php
                    $count = 1;
                    if ($all_posts->have_posts()) :
                        while ($all_posts->have_posts()) : $all_posts->the_post();
                            global $post;
                            $url = newsup_get_freatured_image_url($post->ID, 'thumbnail');
                            ?>
                            <!-- small-list-post --> 
                                <div class="small-post mb-0">
                                    <!-- small_post -->
                                    <?php if ( has_post_thumbnail() ) {  ?>
                                    <div class="img-small-post">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (!empty($url)): ?>
                                                <img src="<?php echo esc_url($url); ?>" alt="<?php the_title(); ?>">
                                            <?php endif; ?>
                                        </a>
                                        <!-- img-small-post -->
                                    </div>
                                    <!-- // img-small-post -->
                                    <?php } ?>
                                    <div class="small-post-content">
                                    <?php newsup_post_categories(); ?> 
                                        <!-- small-post-content -->
                                        <h5 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                        <!-- // title_small_post -->
                                    </div>
                                    <!-- // small-post-content -->
                                </div>
                            <?php
                            $count++;
                        endwhile;
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php
            // close the widget container
            echo $args['after_widget']. '<!-- /widget_post_list -->';
        }
        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            $this->form_instance = $instance;
            $options = array(
                'true' => __('Yes', 'newsup'),
                'false' => __('No', 'newsup')
            );

            $categories = newsup_get_terms();

            if (isset($categories) && !empty($categories)) {
                // generate the text input for the title of the widget. Note that the first parameter matches text_fields array entry
                echo parent::newsup_generate_text_input('newsup-categorised-posts-title', __('Title', 'newsup'), __('Posts List', 'newsup'));
                echo parent::newsup_generate_select_options('newsup-select-category', __('Select category', 'newsup'), $categories);
            }
            //print_pre($terms);
        }
    }
endif;