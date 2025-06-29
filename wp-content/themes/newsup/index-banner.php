<?php $newsup_background_image = get_theme_support( 'custom-header', 'default-image' );
if ( has_header_image() ) { $newsup_background_image = get_header_image(); } ?>
<div class="mg-breadcrumb-section" style='background: url("<?php echo esc_url( $newsup_background_image ); ?>" ) repeat scroll center 0 #143745;'>
  <?php $newsup_remove_header_image_overlay = get_theme_mods('remove_header_image_overlay',true);
  if($newsup_remove_header_image_overlay == true){ ?> <div class="overlay"> <?php } ?>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="mg-breadcrumb-title">
              <?php
              if( class_exists( 'WooCommerce' )) {
               if(is_shop()) { ?>
                  <h1 class="title"><?php woocommerce_page_title();?></h1><?php
                } elseif(is_product_category() || is_product_tag()){ 
                  the_archive_title( '<h2 class="title">', '</h2>' );
                }elseif(is_archive()) {
                  the_archive_title( '<h1 class="title">', '</h1>' );
                  the_archive_description( '<div class="archive-description">', '</div>' );
                } elseif(is_search()){ ?>
                  <h1><?php /* translators: %s: search term */ printf( esc_html__( 'Search Results for: %s','newsup'), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1> <?php
                  newsup_search_count();
                } else { ?>
                  <h1 class="title"><?php the_title(); ?></h1>
                <?php }
              } elseif(is_archive()) {
                the_archive_title( '<h1 class="title">', '</h1>' );
                the_archive_description( '<div class="archive-description">', '</div>' );
              } elseif(is_search()){ ?>
                <h1><?php /* translators: %s: search term */ printf( esc_html__( 'Search Results for: %s','newsup'), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1> <?php
                newsup_search_count();
              } else { ?>
                <h1 class="title"><?php the_title(); ?></h1>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
  <?php if($newsup_remove_header_image_overlay == true){ ?> </div> <?php } ?>
</div>
<div class="clearfix"></div>