<?php

/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 20);

function child_enqueue_styles() {

wp_enqueue_style ('theme-style', '/wp-content/themes/storefront-child/assets/css/slick.min.css');

if ( is_product() ) {
        wp_enqueue_style( 'contact-form-style', '/wp-content/themes/storefront-child/assets/css/cf7-custom.css');
}
if (is_front_page()) {
    wp_enqueue_style( 'home-page-style', '/wp-content/themes/storefront-child/assets/css/home-page.css');    
}
if (is_page('О Компании')) {
    wp_enqueue_style( 'about-page-style', '/wp-content/themes/storefront-child/assets/css/about-page.css'); 
}
if (is_page('Контакты')) {
    wp_enqueue_style( 'contacts-page-style', '/wp-content/themes/storefront-child/assets/css/contacts-page.css'); 
}
if (is_page('Услуги')) {
    wp_enqueue_style( 'services-page-style', '/wp-content/themes/storefront-child/assets/css/services-page.css'); 
}
if ( in_category('works') ) {
    wp_enqueue_style( 'works-single-style', '/wp-content/themes/storefront-child/assets/css/works-single-post.css'); 
}
if ( !is_front_page() && is_home() )  {
    wp_enqueue_style( 'blog-style', '/wp-content/themes/storefront-child/assets/css/blog.css'); 
}
if ( !is_singular( 'portfolio' ) && is_singular() ) {
    wp_enqueue_style( 'blog-style', '/wp-content/themes/storefront-child/assets/css/blog-single.css'); 
}
}

function sf_child_theme_dequeue_style() {
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
    
}
function wpse_remove_edit_post_link( $link ) {
    return '';
}
add_filter('edit_post_link', 'wpse_remove_edit_post_link');

/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */

function valid_attribute_name( $attribute_name ) {
    if ( strlen( $attribute_name ) >= 128 ) {
            return new WP_Error( 'error', sprintf( __( 'Slug "%s" is too long (128 characters max). Shorten it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) ) );
    } elseif ( wc_check_if_attribute_name_is_reserved( $attribute_name ) ) {
            return new WP_Error( 'error', sprintf( __( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) ) );
    }

    return true;
}
//Allow SVG
function cc_mime_types($mimes) {
 $mimes['svg'] = 'image/svg+xml';
 return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


function remove_fonts(){

wp_dequeue_style('storefront-fonts');

}

add_action( 'wp_enqueue_scripts', 'remove_fonts', 999);

/**
 * Disable the Search Box in the Storefront Theme
 */
add_action( 'init', 'jk_remove_storefront_header_search' );

function jk_remove_storefront_header_search() {

remove_action( 'storefront_header', 'storefront_product_search', 40 );

}



function wp_scripts_custom() {  
 
    wp_register_script( 'custom', '/wp-content/themes/storefront-child/assets/js/custom.js', array('jquery') ); 
    wp_enqueue_script( 'custom' );  
    wp_register_script( 'slick', '/wp-content/themes/storefront-child/assets/js/slick.min.js', array('jquery') ); 
    wp_enqueue_script( 'slick' );

}  
add_action( 'wp_enqueue_scripts', 'wp_scripts_custom' );


// Hide Product Category Count
add_filter( 'woocommerce_subcategory_count_html', 'prima_hide_subcategory_count' );
function prima_hide_subcategory_count() {
  /* empty - no count */
}





if ( ! function_exists( 'storefront_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_credit() {
		?>
            <div class="site-info-wrap">
	        	<div class="site-info">
	        		<?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . date( 'Y' ) . ' '  . get_bloginfo( 'name' )) ); ?>
	        	</div>
            </div>
		<?php
	}
}



add_action( 'wp', 'bbloomer_storefront_remove_title_from_home_default_template' );
 
function bbloomer_storefront_remove_title_from_home_default_template() {
   if ( is_front_page() ) remove_action( 'storefront_page', 'storefront_page_header', 10 );
}

/**
 * Disable sidebar on product pages in WooCoomerce.
 *
 */
 
function njengah_remove_sidebar( $is_active_sidebar, $index ) {              
 
    if( $index !== "sidebar-1" ) {
 
        return $is_active_sidebar;
 
    }
 
    if( ! is_product() ) {
 
        return $is_active_sidebar;
 
    }
 
    return false;
 
}
 
add_filter( 'is_active_sidebar', 'njengah_remove_sidebar', 10, 2 );


add_action( 'get_header', 'remove_storefront_sidebar' );
function remove_storefront_sidebar() {
    if ( is_home()) {
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
    }
    elseif ( is_archive() && (! is_woocommerce () ) ) {
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
    }
    elseif ( is_single() ) {
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
    }
}


/*Cart header*/

add_action( 'init' , 'add_and_remove' , 15 );
function add_and_remove() {
        remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
        add_action( 'storefront_header', 'storefront_secondary_navigation', 6 );
        remove_action( 'storefront_header', 'storefront_site_branding', 20 );
        add_action( 'storefront_header', 'storefront_site_branding', 45 );
}



if ( ! function_exists( 'storefront_cart_link' ) ) {
    function storefront_cart_link() {
        if ( ! storefront_woo_cart_available() ) {
            return;
        }
        ?>
            <a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
                <?php /* translators: %d: number of items in cart */ ?>
                <span class="count"><?php echo wp_kses_data( sprintf( WC()->cart->get_cart_contents_count() ) ); ?></span>
            </a>
        <?php
    }
}
/*Contacts and desktop search Header*/
add_action( 'storefront_header', 'storefront_header_content', 55 );
function storefront_header_content() { 
    if( ! wp_is_mobile() ) {
    // тут выполняем действия только для НЕ мобильных устройств.
            echo do_shortcode('[fibosearch]');


        ?>

            <div class="header-contacts">
                <span>
                <a href="https://web.whatsapp.com/send?phone=+77762724000" target="_blank"><img class="whatsapp" src="/wp-content/themes/storefront-child/img/whatsapp.svg"></a>
                </span>
                <span>
                    <a href="https://2gis.kz/almaty/firm/70000001029790567?m=76.954057%2C43.225474%2F16" target="_blank"><img src="/wp-content/themes/storefront-child/img/location-pin.svg"></a>
                </span>
                <span class="header-phone">
                    <a href="tel:+77273398860">+7 727 33 98 860</a>
                    <a href="mailto:order@volt.kz"><span>order@volt.kz</span></a>
                </span>
            </div>  
        <?php
    }
}
/*Contacts and mobile search Header*/
add_action( 'storefront_header', 'storefront_header_content_mobile', 5 );
function storefront_header_content_mobile() { 
    if( wp_is_mobile() ) {
                
    // тут выполняем действия только для мобильных устройств.
        ?>
                <div class="search_mobile">
        <?php
        
        echo do_shortcode('[fibosearch]');

        ?>

            <div class="header-contacts">
                <span>
                <a href="https://web.whatsapp.com/send?phone=+77762724000" target="_blank"><img class="whatsapp" src="/wp-content/themes/storefront-child/img/whatsapp.svg"></a>
                </span>
                <span>
                    <a href="https://2gis.kz/almaty/firm/70000001029790567?m=76.954057%2C43.225474%2F16" target="_blank"><img src="/wp-content/themes/storefront-child/img/location-pin.svg"></a>
                </span>
            </div>
        </div>  
        <?php
    }
}

// Выводим наличие товара в каталоге
add_action('woocommerce_shop_loop_item_title','bbloomer_show_stock_shop', 10);

function bbloomer_show_stock_shop() {
global $product;
if ($product->get_stock_quantity()>0) {
echo '<div class="stockstatus">' . number_format($product->stock,0,'','') . ' в наличии</div>'; 
}
if ($product->get_stock_quantity()==0) {
    echo '<div class="stockstatus">По предзаказу</div>';
}
if ($product->get_stock_quantity()<0) {
    echo '<div class="stockstatus">По предзаказу</div>';
}
}

///Выводим наличие товара в блоки
function kia_blocks_product_grid_item_html( $html, $data, $product ) {
    if ($product->get_stock_quantity()>0) {
    $short_description = '<div class="stockstatus">' . number_format($product->stock,0,'','') . ' в наличии</div>'; 
    }
    if ($product->get_stock_quantity()==0) {
        $short_description = '<div class="stockstatus">По предзаказу</div>';
    }
    if ($product->get_stock_quantity()<0) {
        $short_description = '<div class="stockstatus">По предзаказу</div>';
    }
    return 

        "<li class=\"wc-block-grid__product\">
            <a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
                {$data->image}
                {$data->title}
            </a>"
            .$short_description.

            "{$data->badge}
            {$data->price}
            {$data->rating}
            {$data->button}
        </li>";
}
add_filter( 'woocommerce_blocks_product_grid_item_html', 'kia_blocks_product_grid_item_html', 10, 3 );

//Убираем кнопку Купить в Каталоге
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');


/* megamenu custom js */
add_action('wp_footer', 'your_function_name', 99);
function your_function_name(){
?>
<script>
jQuery(function($) {
    $( ".mega-menu-item-object-product_cat.mega-toggle-on" ).removeClass( "mega-toggle-on" );
    
    $("#mega-menu-wrap-primary").click(function() {  //use a class, since your ID gets mangled
        $("#mega-menu-item-135").addClass("mega-toggle-on");      //add the class to the clicked element
    });


$('.best-sellers ul').slick({
  slidesToShow: 5,
  infinite: true,
  slidesToScroll: 2,
  prevArrow: '<div class="slick-prev slick-arrow" style=""><i class="fa fa-chevron-left"></i></div>',
  nextArrow: '<div class="slick-next slick-arrow" style=""><i class="fa fa-chevron-right"></i></div>',
  autoplay: false,
  autoplaySpeed: 2000,
  cssEase: 'linear',
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        slidesToScroll: 2,
        slidesToShow: 2,
        dots: true
      }
    }
  ]
});

$('.works').slick({
  slidesToShow: 2,
  infinite: true,
  slidesToScroll: 1,
  prevArrow: '<div class="slick-prev slick-arrow" style=""><i class="fa fa-chevron-left"></i></div>',
  nextArrow: '<div class="slick-next slick-arrow" style=""><i class="fa fa-chevron-right"></i></div>',
  autoplay: false,
  autoplaySpeed: 2000,
  cssEase: 'linear',
  responsive: [
    {
      breakpoint: 600,
      settings: {
        arrows: false,
        slidesToShow: 2,
        dots: true
      }
    },
    {
      breakpoint: 480,
      settings: {
      arrows: false,
      slidesToShow: 1,
      dots: true
      }
    }
  ]
});

$('.brands .blocks-gallery-grid').slick({
  slidesToShow: 6,
  infinite: true,
  slidesToScroll: 3,
  prevArrow: '<div class="slick-prev slick-arrow" style=""><i class="fa fa-chevron-left"></i></div>',
  nextArrow: '<div class="slick-next slick-arrow" style=""><i class="fa fa-chevron-right"></i></div>',
  autoplay: false,
  autoplaySpeed: 2000,
  cssEase: 'linear',
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        slidesToShow: 1,
        slidesPerRow: 2,
        rows: 2,
        dots: true
      }
    }
  ]
});


$('.up-sells .products').slick({
  slidesToShow: 5,
  infinite: true,
  slidesToScroll: 1,
  prevArrow: '<div class="slick-prev slick-arrow" style=""><i class="fa fa-chevron-left"></i></div>',
  nextArrow: '<div class="slick-next slick-arrow" style=""><i class="fa fa-chevron-right"></i></div>',
  autoplay: false,
  autoplaySpeed: 2000,
  cssEase: 'linear',
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        slidesToShow: 2
      }
    }
  ]
});

$('.related .products').slick({
  slidesToShow: 5,
  infinite: true,
  slidesToScroll: 1,
  prevArrow: '<div class="slick-prev slick-arrow" style=""><i class="fa fa-chevron-left"></i></div>',
  nextArrow: '<div class="slick-next slick-arrow" style=""><i class="fa fa-chevron-right"></i></div>',
  autoplay: false,
  autoplaySpeed: 2000,
  cssEase: 'linear',
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        slidesToShow: 2
      }
    }
  ]
});


});


</script>

<?php
if( wp_is_mobile() ) {
   ?>
        <script>
            jQuery(function($) {
                $('.home_cat ul').slick({
                    slidesPerRow: 2,
                    rows: 2,
                    infinite: false,
                    slidesToScroll: 1,
                    autoplay: false,
                    cssEase: 'linear',
                    arrows: false,
                    dots: true
                });
            });
        </script> 
   <?php

}
};


// =========================================================================
// REMOVE ACCOUNT AND SEARCH ICON FROM storefront-handheld-footer-bar
// =========================================================================
function jk_remove_handheld_footer_links( $links ) {
    unset( $links['my-account'] );
    unset( $links['search'] );
    return $links;
}
add_filter( 'storefront_handheld_footer_bar_links', 'jk_remove_handheld_footer_links' );
 
// =========================================================================
// REMOVE STOREFRONT HANDHELD BAR
// =========================================================================
add_action( 'init', 'jk_remove_storefront_handheld_footer_bar' );

function jk_remove_storefront_handheld_footer_bar() {
  remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );
}
 


// =========================================================================
// REMOVE blog category (news, works)
// =========================================================================
function storefront_post_taxonomy() {
    remove_action( 'storefront_single_post_bottom', 'storefront_post_taxonomy', 10 );
    
};

add_action( 'init', 'storefront_post_taxonomy');


// =========================================================================
// show only exceprt (news, works)
// =========================================================================
add_action( 'init', function() {

            remove_action( 'storefront_loop_post', 'storefront_post_content', 30 );

            add_action( 'storefront_loop_post', function() {

                        echo '<div class="entry-content" itemprop="articleBody">';

                        if( has_post_thumbnail() ) {

                                    the_post_thumbnail( 'full', [ 'itemprop' => 'image' ] );

                        }

                        the_excerpt();

                        echo '</div>';

            }, 30 );

} );
// =========================================================================
// allow links in exceprt (news, works)
// =========================================================================
function keep_my_links($text) {
  global $post;
if ( '' == $text ) {
    $text = get_the_content('');
    $text = apply_filters('the_content', $text);
    $text = str_replace('\]\]\>', ']]&gt;', $text);
    $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
    $text = strip_tags($text, '<a>');
  }
  return $text;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'keep_my_links');
// =========================================================================
// add links to featered images (news, works)
// =========================================================================
add_filter( 'post_thumbnail_html', 'my_post_image_html', 10, 3 );
 
function my_post_image_html( $html, $post_id, $post_image_id ) {
 
  $html = '<a href="' . get_permalink( $post_id ) . '">' . $html . '</a>';
  return $html;
 
}


// =========================================================================
// remove featured image in single post (news, works)
// =========================================================================
function wordpress_hide_feature_image( $html, $post_id, $post_image_id ) {
  return is_single() ? '' : $html;
}

add_filter( 'post_thumbnail_html', 'wordpress_hide_feature_image', 10, 3);


// необходимый div в карточке товара
add_action('woocommerce_before_single_product_summary','product_top_div_start');
function product_top_div_start() {
echo '<div class="product-top">';
}
add_action('woocommerce_share','product_top_div_end');
function product_top_div_end() {
echo '</div>';
}
// необходимый div в карточке товара
add_action('woocommerce_after_single_product_summary','product_right');
function product_right() {
echo '<div class="product-right"><button class="show-popup">Задать вопрос по товару</button></div></div>';
}
add_action('woocommerce_after_single_product_summary','product_bottom', 1);
function product_bottom() {
echo '<div class="product-bottom">';
}


/**remove META from Single Product Page */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
/**Show Product SKU on Single Product Page - WooCommerce*/
add_action( 'woocommerce_single_product_summary', 'custom_show_sku_single_product', 28 ); 
function custom_show_sku_single_product() {
global $product;
   ?>
   <div class="product_sku">
   <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
      <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
   <?php endif; ?>
   </div>
   <?php
}

// переносим кнопку Купить наверх
add_action('woocommerce_single_product_summary', 'move_single_product_price', 1);
function move_single_product_price() {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 28);
}

// Выводим цены для дилеров в товаре
add_action('woocommerce_single_product_summary','show_diler_price', 29);
function show_diler_price() {
global $product;
    $extra_price = wc_price(wc_get_price_to_display($product) / 1.03);
      echo '<span class="pprice_product"><a href="#" class="diler_price">Запросить</a></span>';
      echo '<span class="pprice_product"><a href="#" class="diler_price">Запросить</a></span>';
 
}

// Выводим цены для дилеров в товаре
add_action('woocommerce_single_product_summary','show_diler_price_descr', 28);
function show_diler_price_descr() {
global $product;
    $extra_price = wc_price(wc_get_price_to_display($product) / 1.03);
    if( wp_is_mobile() ) {
        echo '<div class="diler_price_descr"><span>Розничная<div>Розничная цена</div></span><span>Партнерская<div>Партнерская цена</div></span><span>Дилерская<div>Цена для постоянных клиентов, заключивших дилерский договор.</div></span></div>';
    }
    else {
        echo '<div class="diler_price_descr"><span>Розничная цена<div>Розничная цена</div></span><span>Партнерская цена<div>Партнерская цена</div></span><span>Дилерская цена<div>Цена для постоянных клиентов, заключивших дилерский договор.</div></span></div>';
    }

}

// Remove the product description Title
add_filter( 'woocommerce_product_description_heading', '__return_null' );
// Remove the additional information title
add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

/*Reorder Reviews tab to be first*/
add_filter( 'woocommerce_product_tabs', 'woo_reorder_tabs', 98 );
function woo_reorder_tabs( $tabs ) {

    $tabs['additional_information']['priority'] = 5;    
    $tabs['description']['priority'] = 10;
        return $tabs;
}
/*single product image size*/
add_filter( 'woocommerce_get_image_size_single', function( $size ) {
    return array(
        'width'  => 580,
        'height' => 580,
        'crop'   => 0,
    );
} );

// Выводим форму single product
function contact_form() {
    if ( is_product() ) {
        echo do_shortcode( '[contact-form-7 id="1357" title="Задать вопрос по товару"]' );
    }
    
}
add_action( 'wp_footer', 'contact_form', 10 );


add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );

function woo_rename_tabs( $tabs ) {

    global $product;
    
    if( $product->has_attributes() || $product->has_dimensions() || $product->has_weight() ) { // Check if product has attributes, dimensions or weight
        $tabs['additional_information']['title'] = __( 'Характеристики товара' );    // Rename the additional information tab
    }
 
    return $tabs;
 
}
/**
 * Change the number of related products output
 */ 
add_filter( 'woocommerce_output_related_products_args', 'woocommerce_related_products_args', 20 );
  function woocommerce_related_products_args( $args ) {
    $args['posts_per_page'] = 6; // 4 related products
    $args['columns'] = 1; // arranged in 2 columns
    return $args;
}


include_once( '/var/www/www-root/data/www/volt.kz/wp-content/themes/storefront-child/getinvoise.php' );
include_once( '/var/www/www-root/data/www/volt.kz/wp-content/themes/storefront-child/portfolio.php' );
include_once( '/var/www/www-root/data/www/volt.kz/wp-content/themes/storefront-child/category_second_description.php' );

// Displaying the subcategories after category title
add_action('woocommerce_archive_description', 'display_subcategories_list', 5 ); 
function display_subcategories_list() {
    if ( is_product_category() ) {

        $term_id  = get_queried_object_id();
        $taxonomy = 'product_cat';

        // Get subcategories of the current category
        $terms    = get_terms([
            'taxonomy'    => $taxonomy,
            'hide_empty'  => false,
            'parent'      => $term_id
        ]);

        echo '<ul class="subcategories-list">';

        // Loop through product subcategories WP_Term Objects
        foreach ( $terms as $term ) {
            $term_link = get_term_link( $term, $taxonomy );

            echo '<li class="'. $term->slug .'"><a href="'. $term_link .'">'. $term->name .'</a></li>';
        }

        echo '</ul>';
    }
}

