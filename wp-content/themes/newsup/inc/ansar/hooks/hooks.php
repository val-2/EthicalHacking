<?php
if (!function_exists('newsup_banner_trending_posts')):
    /**
     *
     * @since newsup 1.0.0
     *
     */
    function newsup_banner_exclusive_posts()  { 
        if (is_front_page() || is_home()) { ?>
            <section class="mg-latest-news-sec"> 
                <?php
                $show_flash_news_section = newsup_get_option('show_flash_news_section');
                if ($show_flash_news_section) {
                    $category = newsup_get_option('select_flash_news_category');
                    $number_of_posts = newsup_get_option('number_of_flash_news');
                    $newsup_ticker_news_title = newsup_get_option('flash_news_title');

                    $all_posts = newsup_get_posts($number_of_posts, $category);
                    $show_trending = true;
                    $count = 1;
                    ?>
                    <div class="container-fluid">
                        <div class="mg-latest-news">
                            <div class="bn_title">
                                <h2 class="title">
                                    <?php if (!empty($newsup_ticker_news_title)): ?>
                                        <?php echo esc_html($newsup_ticker_news_title); ?><span></span>
                                    <?php endif; ?>
                                </h2>
                            </div>
                            <?php if(is_rtl()){ ?> 
                            <div class="mg-latest-news-slider marquee" data-direction='right' dir="ltr">
                            <?php } else { ?> 
                            <div class="mg-latest-news-slider marquee">
                            <?php }
                                if ($all_posts->have_posts()) :
                                    while ($all_posts->have_posts()) : $all_posts->the_post(); ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <span><?php the_title(); ?></span>
                                            </a>
                                        <?php
                                        $count++;
                                    endwhile;
                                endif;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Excluive line END -->
                <?php }
            echo '</section>';
        }
    }
endif;
add_action('newsup_action_banner_exclusive_posts', 'newsup_banner_exclusive_posts', 10);


//Banner Tabed Section
if (!function_exists('newsup_banner_tabbed_posts')):
    /**
     *
     * @since Newsup 1.0.0
     *
     */
    function newsup_banner_tabbed_posts() { 
        $show_excerpt = 'false';
        $excerpt_length = '20';
        $number_of_posts = '4';

        $enable_categorised_tab = 'true';
        $latest_title = newsup_get_option('latest_tab_title');
        $popular_title = newsup_get_option('popular_tab_title');
        $categorised_title = newsup_get_option('trending_tab_title');
        $category = newsup_get_option('select_trending_tab_news_category');
        $tab_id = 'tan-main-banner-latest-trending-popular'
        ?> 
        <div class="col-md-4 top-right-area">
            <div id="exTab2" >
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#<?php echo esc_attr($tab_id); ?>-recent"
                            aria-controls="<?php esc_attr_e('Recent', 'newsup'); ?>">
                            <i class="fas fa-clock"></i><?php echo esc_html($latest_title); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#<?php echo esc_attr($tab_id); ?>-popular"
                            aria-controls="<?php esc_attr_e('Popular', 'newsup'); ?>">
                            <i class="fas fa-fire"></i> <?php echo esc_html($popular_title); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#<?php echo esc_attr($tab_id); ?>-categorised"
                            aria-controls="<?php esc_attr_e('Categorised', 'newsup'); ?>">
                            <i class="fas fa-bolt"></i> <?php echo esc_html($categorised_title); ?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="<?php echo esc_attr($tab_id); ?>-recent" role="tabpanel" class="tab-pane active fade show">
                        <?php newsup_render_posts('recent', $show_excerpt, $excerpt_length, $number_of_posts); ?>
                    </div>
                    <div id="<?php echo esc_attr($tab_id); ?>-popular" role="tabpanel" class="tab-pane fade">
                        <?php newsup_render_posts('popular', $show_excerpt, $excerpt_length, $number_of_posts); ?>
                    </div>
                    <?php if ($enable_categorised_tab == 'true'): ?>
                        <div id="<?php echo esc_attr($tab_id); ?>-categorised" role="tabpanel" class="tab-pane fade">
                            <?php newsup_render_posts('categorised', $show_excerpt, $excerpt_length, $number_of_posts, $category); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
endif;

add_action('newsup_action_banner_tabbed_posts', 'newsup_banner_tabbed_posts', 10);

//Banner Advertisment
if (!function_exists('newsup_banner_advertisement')):
    /**
     *
     * @since Newsup 1.0.0
     *
     */
    function newsup_banner_advertisement() {
        if (('' != newsup_get_option('banner_advertisement_section')) ) { ?>
            <?php $newsup_center_logo_title = get_theme_mod('newsup_center_logo_title',false); ?>
            <div class="<?php echo esc_attr($newsup_center_logo_title == false ? 'col-md-9' : 'col text-center mx-auto') ?>"> <?php  
                $newsup_banner_advertisement = newsup_get_option('banner_advertisement_section');
                $newsup_banner_advertisement = absint($newsup_banner_advertisement);
                $newsup_banner_advertisement = wp_get_attachment_image($newsup_banner_advertisement, 'full');
                $newsup_banner_advertisement_url = newsup_get_option('banner_advertisement_section_url');
                $newsup_banner_advertisement_url = isset($newsup_banner_advertisement_url) ? esc_url($newsup_banner_advertisement_url) : '#';
                $newsup_open_on_new_tab = get_theme_mod('newsup_open_on_new_tab',true); ?>
                <div class="header-ads">
                    <a class="pull-right" <?php echo esc_url($newsup_banner_advertisement_url); ?> href="<?php echo $newsup_banner_advertisement_url; ?>"
                        <?php if($newsup_open_on_new_tab) { ?>target="_blank" <?php } ?> >
                        <?php echo $newsup_banner_advertisement; ?>
                    </a>
                </div>
            </div>
            <!-- Trending line END -->
            <?php
        }
    }
endif;

add_action('newsup_action_banner_advertisement', 'newsup_banner_advertisement', 10);

//Front Page Banner
if (!function_exists('newsup_front_page_banner_section')) :
    /**
     *
     * @since Newsup
     *
     */
    function newsup_front_page_banner_section() {
        if (is_front_page() || is_home()) {
            $newsup_enable_main_slider = newsup_get_option('show_main_news_section');
            $select_vertical_slider_news_category = newsup_get_option('select_vertical_slider_news_category');
            $vertical_slider_number_of_slides = newsup_get_option('vertical_slider_number_of_slides');
            $all_posts_vertical = newsup_get_posts($vertical_slider_number_of_slides, $select_vertical_slider_news_category);
            if ($newsup_enable_main_slider){

                $newsup_select_slider_setting = get_theme_mod('newsup_select_slider_setting','left') == 'right' ? ' flex-row-reverse' :'';
                $main_banner_section_background_image = newsup_get_option('main_banner_section_background_image');
                $main_banner_section_background_image_url = wp_get_attachment_image_src($main_banner_section_background_image, 'full');
                if(!empty($main_banner_section_background_image)){ ?>
                    <section class="mg-fea-area over" style="background-image:url('<?php echo esc_url($main_banner_section_background_image_url[0]); ?>');">
                <?php }else{ ?>
                    <section class="mg-fea-area">
                <?php  } ?>
                    <div class="overlay">
                        <div class="container-fluid">
                            <div class="row<?php echo $newsup_select_slider_setting ?>">
                                <div class="col-md-8">
                                    <div id="homemain"class="homemain owl-carousel"> 
                                        <?php newsup_get_block('list', 'banner'); ?>
                                    </div>
                                </div>
                                <?php do_action('newsup_action_banner_tabbed_posts'); ?>
                            </div>
                        </div>
                    </div>
                </section>
                <!--==/ Home Slider ==-->
            <?php } ?>
            <!-- end slider-section -->
        <?php }
    }
endif;
add_action('newsup_action_front_page_main_section_1', 'newsup_front_page_banner_section', 40);