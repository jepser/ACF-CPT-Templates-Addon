<?php
/*
Plugin Name: Advanced Custom Fields: Custom Post Type Template Rules
Plugin URI: http://jepserbernardino.com/labs/acf-cptt
Description: Adds show rule for Custom Post Type plugin. Requeries Advanced Custom Fields plugin and Custom Post Templates plugin.
Version: 0.2
Author: Royal Estudios
Author URI: http://royalestudios.com
License: GPL2
*/

class WP_cpttr{
	public function __construct(){
		//register_activation_hook(__FILE__, array(&$this, 'activate')); 
		//register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php');
		if(!is_plugin_active('advanced-custom-fields/acf.php') && !is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) || !is_plugin_active('custom-post-template/custom-post-templates.php')){
			add_action( 'admin_notices', array(&$this,'activate_error_notice') );
			deactivate_plugins('acf-cpt-template/index.php');
		} //is_plugin_active
		
		add_filter('cpt_post_types', array(&$this, 'add_cpt_post_types' ));
		add_filter('acf/location/rule_match/cpt_template', array(&$this, 'acf_location_rules_match_cpt_template'), 10, 3);
		add_filter('acf/location/rule_values/cpt_template', array(&$this, 'acf_add_cpt_list'));
		add_filter('acf/location/rule_types', array(&$this, 'acf_location_rules_types'));

		add_action('admin_enqueue_scripts', array(&$this, 'acf_cpt_javascript'));
		
	}
	
	public static function activate() {
		
	}
	
	public static function desactivate() {
		
	}
	
	function activate_error_notice() {
		?>
        <div class="error">
            <p><?php _e( 'ACF - Template Error. You must have plugin dependecies activated', 'acf-cpt' ); ?></p>
        </div>
        <?php
    }
	
	//adding templating for every Custom Post Type
	function add_cpt_post_types( $post_types ) {
		$pts = get_post_types(array('public' => true, '_builtin' => false));
		$post_types = array_merge($post_types, $pts);
		return $post_types;
	}
	
	//adding in location options
	function acf_location_rules_types( $choices ){
		$choices['Post']['cpt_template'] = __('Post Type Template','acf-cpt');
	 
		return $choices;
	}
	
	//
	function acf_add_cpt_list($choices){
		
		$ctheme = wp_get_theme();
		$ctheme->get_files( 'php', 1 );
		$choices = array();
		$files = (array) $ctheme->get_files( 'php', 1 );
		foreach ( $files as $file => $full_path ) {
			$headers = get_file_data( $full_path, array( 'Template Name Posts' => 'Template Name Posts' ) );
			if ( empty( $headers['Template Name Posts'] ) )
				continue;
			$choices[ $file ] = $headers['Template Name Posts'];
		}
		return $choices;
	}

	
	function acf_location_rules_match_cpt_template( $match, $rule, $options )
	{
		global $post;
		$current_template = get_post_meta($post->ID, 'custom_post_template',true);
		$selected_template = $rule['value'];
		
		if(!$current_template) return;
	 
		if($rule['operator'] == "==")
		{
			$match = ( $current_template == $selected_template );
		}
		elseif($rule['operator'] == "!=")
		{
			$match = ( $current_template != $selected_template );
		}
	 
		return $match;
	}

	function acf_cpt_javascript(){
		wp_enqueue_script('acf_cpt', plugin_dir_url( __FILE__ ) . '/scripts/acf_cpt.js', array('jquery'), '0.2');
	}
	
	function add_action ($action, $function = '', $priority = 10, $accepted_args = 1) {
		add_action ($action, array (&$this, $function == '' ? $action : $function), $priority, $accepted_args);
	}
	function add_filter ($filter, $function = '', $priority = 10, $accepted_args = 1){
		add_filter ($filter, array (&$this, $function == '' ? $filter : $function), $priority, $accepted_args);
	}

	
}
$wp_plugin_template = new WP_cpttr();

?>