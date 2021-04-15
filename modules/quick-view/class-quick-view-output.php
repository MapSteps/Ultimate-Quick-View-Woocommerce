<?php
/**
 * Quick view module output.
 *
 * @package Woocommerce_Quick_View
 */

namespace Wooquickview\QuickView;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Wooquickview\Base\Base_Output;

/**
 * Class to setup dashboard output.
 */
class Quick_View_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = WOOCOMMERCE_QUICK_VIEW_PLUGIN_URL . '/modules/feature';

	}

	/**
	 * Get instance of the class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup dashboard output.
	 */
	public function setup() {

		// Add quick view button.
		add_action( 'woocommerce_before_shop_loop_item_title', array( self::get_instance(), 'add_quick_view_button' ), 15 );

		// Add empty div to the footer to populate div with response from ajax request.
		add_action( 'wp_footer', array( self::get_instance(), 'popup_output' ) );

		$this->build_quickview_content();

	}

	/**
	 * Add quick view button to products.
	 */
	public function add_quick_view_button( $product_id = 0 ) {

		global $product;

		$product_id = $product->get_id();
		?>

		<a href="javascript:void(0)" id="wooquickview_product_id_<?php echo absint( $product_id ); ?>" class="button wooquickview-button wooquickview-view-button" data-product-id="<?php echo absint( $product_id ); ?>" aria-hidden="true">
			<?php echo esc_attr( apply_filters( 'wooquickview_view_label', __( 'Quick View', 'woocommerce-quick-view' ) ) ); ?>
		</a>
		<?php

	}

	/**
	 * Construct quick view popup.
	 */
	public function popup_output() {

		// Load necessary scripts.
		$this->enqueue_scripts();
		?>

		<div class="wooquickview-popup">
			<span class="wooquickview-close-popup"></span>

			<div class="wooquickview-popup-content">
				<div id="wooquickview-popup-product" class="woocommerce single-product"></div>
			</div>
		</div>

		<?php
	}

	/**
	 * Enqueue necessary styles & scripts.
	 */
	public function enqueue_scripts() {

		/**
		 * Enable Zoom for profuct image.
		 * Enable Auto Change Image for products with mutiple images, when product variation is selected.
		 */
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {

			if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
				wp_enqueue_script( 'zoom' );
			}

			if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {

				wp_enqueue_script( 'photoswipe-ui-default' );
				wp_enqueue_style( 'photoswipe-default-skin' );

				if ( has_action( 'wp_footer', 'woocommerce_photoswipe' ) === false ) {
					add_action( 'wp_footer', 'woocommerce_photoswipe', 15 );
				}
			}

			wp_enqueue_script( 'wc-single-product' );

		}

	}

	/**
	 * Construct quick view content.
	 */
	public function build_quickview_content() {

		// Product's image.
		add_action( 'wooquickview_view_product_image', 'woocommerce_show_product_images' );

		// Product's title.
		add_action( 'wooquickview_product_summary', 'woocommerce_template_single_title' );

		// Product's rating.
		add_action( 'wooquickview_product_summary', 'woocommerce_template_single_rating' );

		// Product's price.
		add_action( 'wooquickview_product_summary', 'woocommerce_template_single_price' );

		// Product's excerpt.
		add_action( 'wooquickview_product_summary', 'woocommerce_template_single_excerpt' );

		// Quantity & add to cart button.
		add_action( 'wooquickview_product_summary', 'woocommerce_template_single_add_to_cart' );

		// Product's meta.
		add_action( 'wooquickview_product_summary', 'woocommerce_template_single_meta' );

	}

}
