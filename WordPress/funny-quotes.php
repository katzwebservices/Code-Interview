<?php
/**
 * Plugin Name:         Exammple Funny Quotes Plugin
 * Plugin URI:        	https://example.com
 * Description:       	Assume this is a WordPress plugin file. Improve the code as much as you possibly can. *Make sure to update the readme.txt with a detailed changelog*.
 * Version:          	1.2.0
 * Author:            	Not Really Funny
 * Text Domain :		funny-quotes
 * Domain Path :		/languages
 * Author URI:        	https://example.com
 * License:           	GPLv2 or later
 * License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Include the Funny quotes settings page
 */
include_once( 'settings.php' );
/**
 * Add up all this plugins actions
 */
add_action( 'init', 'funny_quotes_setup');
add_action( 'widgets_init', 'funny_quotes_widget_register' );
add_action( 'plugins_loaded', 'funny_quotes_plugin_textdomain' );
add_shortcode( 'funny_quotes', 'funny_quotes_render_shortcode' );

/**
 * Funny quote plugin init actions. 
 *
 * Register the `funny-quote` custom post type.
 *
 * @since 1.1.0
 */
function funny_quotes_setup()
{
	register_post_type( 
		'funny-quote', 
		array( 
				'labels' => array( 
					'name' => __('Quotes', 'funny-quotes') ,
					'singular_name' => __('Quote', 'funny-quotes'),
					'add_new_item' => __('Add new Quote', 'funny-quotes'),
					'edit_item' => __('Edit Quote', 'funny-quotes'),
					'name_admin_bar' => __('Quotes', 'funny-quotes') 
				), 
			'public'  => true 
		)
	);

	if( is_admin() ){
    	$my_settings_page = new Funny_Quotes_Settings_Page();
	}
}
/**
 * Register the actual funny quotes widget
 *
 * @since 1.1.0
 */
function funny_quotes_widget_register() {
	register_widget( 'funny_quotes_widget' );
}

/**
 * Loads up the files needed for localization (required for WordPress versions pre 4.6).
 *
 * @since 1.1.0
 */
function funny_quotes_plugin_textdomain() {
    load_plugin_textdomain( 'funny-quotes', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/**
 * Builds,Renders and modifies the funny quote WordPress widget.
 * 
 * @since 1.1.0
 */
class funny_quotes_widget extends WP_Widget {

	/**
	 * Stores default widget values
	 * 
	 * @since 1.1.0
	 * @access private
	 */
	private $defaults;

	function __construct() {
		/**
		 * Set default values for the widget fields.
		 */
		$this->defaults['numberofquotes'] = 3;
		$this->defaults['whentoshow'] = 'every-page-load';
		$this->defaults['title'] = __('Funny & Motivational Quotes', 'funny-quotes');
		// Instantiate the parent object
		parent::__construct( false, __('Funny & Motivational Quotes', 'funny-quotes'));
	}

	/**
	 * Render the actual widget.
	 * 
	 * @since 1.1.0
	 * @access public
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/ 
	 */
	function widget( $args, $instance ) {
		$D = &$this->defaults;
		extract($args);
		$title   = ( isset( $instance['title'] ) ) ? $instance['title'] : $D['title'];
		$num   = ( isset( $instance['numberofquotes'] ) ) ? $instance['numberofquotes'] : $D['numberofquotes'];
		$quotes_content = funny_quotes_get_quotes($num);
		if ( strlen( $quotes_content ) > 0 ){
			echo $before_widget;
			if(!empty($title)) { echo  $before_title . $title . $after_title;}
			echo $quotes_content;
			echo $after_widget;
		}
	}

	/**
	 * Save widget options.
	 * 
	 * @since 1.1.0
	 * @access public
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/ 
	 */
	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['numberofquotes']      = absint( strip_tags( $new_instance['numberofquotes'] ) );
		$instance['whentoshow']      =  strip_tags( $new_instance['whentoshow'] ) ;
		return $instance;
	}

	/**
	 * Output the widget options form in wp-admin.
	 * 
	 * @since 1.1.0
	 * @access public
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/ 
	 */
	function form( $instance ) {
		$defaults = array(
			'title'          => $this->defaults['title'],
			'numberofquotes' => $this->defaults['numberofquotes'],
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'funny-quotes' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'numberofquotes' ) ); ?>"><?php _e( 'Show # of Quotes', 'funny-quotes' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'numberofquotes' ) ); ?>">
				<?php for ($i=0; $i < 10; $i++) { ?>
					<option value="<?php echo $i ?>" <?php selected( $instance['numberofquotes'], $i ); ?>>
						<?php echo $i ?>
					</option>
				<?php } ?>
			</select>
		</p>

		<?php
	}
}

/**
 * Render the Funny Quotes Shortcode.
 *
 * @since 1.2.0
 */
function funny_quotes_render_shortcode( $atts, $content = "" ) {
	$defaults = array('numberofquotes'=> 3);
	$atts = wp_parse_args( $atts, $defaults );
    return funny_quotes_get_quotes($atts['numberofquotes']);
}

/**
 * Shows quotes that are stored as "funny-quote" post type on the site.
 *
 * @since 1.1.0
 * @param int $number_to_show number of quotes to show.
 * @return string containing requested number of quotes.
 * 
 */
function funny_quotes_get_quotes( $number_to_show ) {
	$args = array(
		'numberposts' => $number_to_show,
		'post_type' => 'funny-quote',
	);

	// echo time()-60;
	$show_each_quote_at = 'every-page-load';

	$options = get_option('funny_quotes');

	if ( $options ){

		$show_each_quote_at = isset( $options['whentoshow'] ) ? $options['whentoshow'] : $show_each_quote_at;

	}

	switch ( $show_each_quote_at ) {
		case 'one-per-hour':
			$modify_args = array(
				'meta_key' => 'lastshown',
				'meta_value' => ( time()-3600 ),
				'meta_compare' => '<',
			);
			$args = array_merge( $args, $modify_args );
			break;
		case 'one-per-day':
			$modify_args = array(
				'meta_key' => 'lastshown',
				'meta_value' => ( time()-86400 ),
				'meta_compare' => '<',
			);
			$args = array_merge( $args, $modify_args );
			break;
		default:
			# code...
			break;
	}

	// var_dump( $show_each_quote_at );

	$quotes = get_posts( $args );

	$output = sprintf( __('Showing %d quotes:' , 'funny-quotes') , $number_to_show ) ;

	if ( sizeof( $quotes ) == 0 )
		return;

	foreach ( $quotes as $quote ) {

		$output .= wpautop( $quote->post_content );

		$id = $quote->ID;

		update_post_meta( $id, 'lastshown' , time() );

	}

	return $output;
}

/**
 * Install sample posts (for testing).
 *
 * Inserts posts of the funny-quote type into WordPress.
 *
 * @since 1.2.0
 */
function funny_quotes_install_test_data(){
	for ($i=0; $i < 3; $i++) { 
		$id = wp_insert_post(
			array(
				'post_title' => 'This is funny quote # ' . $i,
				'post_content' => 'Here\'s the content of funny quote ' . $i,
				'post_status'	 => 'publish',
				'post_type' => 'funny-quote',
			)
		);
		update_post_meta( $id, 'lastshown' , time() );
		echo $id;
	}
}