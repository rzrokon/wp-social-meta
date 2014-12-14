<?php
/*
Plugin Name: WP Social Meta
Plugin URI: http://fewpress.com
Description: WP Social Meta plugin generates necessary social meta tags for every pages you have in your WordPress website. It helps you to gently represent websites on the social networks. It generates open graph, twitter card, schema.org and other meta tags  which smartly works represent your website over internet like Facebook, Twitter, Google+ and other social networking websites.
Version: 1.0.0
Author: Md. Rokonuzzaman
Author URI: http://fewpress.com
*/

/*--Add a meta box for pages--*/
add_action( 'admin_enqueue_scripts', 'plugin_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'plugin_admin_styles' );

/*--Javascript files--*/
function plugin_admin_scripts(){
	wp_enqueue_script("uploader", plugins_url('js/uploader.js', __FILE__));
	wp_enqueue_script("add-image", plugins_url('js/add-meta-image.js', __FILE__));
}

/*--CSS Files--*/
function plugin_admin_styles(){
  //wp_enqueue_style("metastyles", plugins_url('css/meta-styles.css', __FILE__));
}

/*--Define Metabox--*/
function surface_define_page_metabox($post){
  global $post,$surface_meta;
  //Existing Meta value
  $meta_fb_title			= get_post_meta($post->ID,'fb_title',true);
  $meta_fb_image			= get_post_meta($post->ID,'fb_image',true);
  $meta_fb_url				= get_post_meta($post->ID,'fb_url',true);
  $meta_fb_description		= get_post_meta($post->ID,'fb_description',true);

  // Use nonce for verification
  wp_nonce_field(plugin_basename( __FILE__ ), 'surface_noncename' );

  //Title
  $html = "<div class='title_boost' style=\"border-top: solid 0px #DFDFDF;\">";
  $html .= '<div class="title_boost">';
  $html .= "<h4 class='labelclass'>Title</h4>";
  $html .= '<input type="text" id="fb_title" name="fb_title" value="'. $meta_fb_title .'" size="90%"/>'; 
  $html .= '</div>';
  $html .= '</div><br>';

  //URL
  $html .= "<div class='title_boost' style=\"border-top: solid 0px #DFDFDF;\">";
  $html .= '<div class="title_boost">';  
  $html .= "<h4 class='labelclass'>Content URL</h4>";
  $html .= '<input type="text" id="fb_url" name="fb_url" value="' . $meta_fb_url . '" size="90%" />';
  $html .= '</div>';
  $html .= '</div><br>';

  //Description
  $html .= "<div class='title_boost' style=\"border-top: solid 0px #DFDFDF;\">";
  $html .= '<div class="title_boost">';  
  $html .= "<h4 class='labelclass'>Description</h4>";
  $html .= '<textarea cols="90%" rows="3" id="fb_description" name="fb_description">'. $meta_fb_description .'</textarea>'; 
  $html .= '</div>';
  $html .= '</div><br>';

  $html .= '<hr>
            <div class="title_boost">
              <br>
              <div class="labelclass">Image</div>
              <input readonly="readonly" id="fb_image" value="' . $meta_fb_image . '" name="fb_image"  class="kp_input_box" type="hidden"/>
              <input title="Upload" onclick="register_upload_button_event(jQuery(this));" class="kp_button_upload button" value="Add Image" type="button">
              <span style="padding-left:10px;"></span>
              <input title="Remove" onclick="register_remove_button_event(jQuery(this));" class="kp_button_remove button" value="Remove Image" type="button">
              <img class="image_preview" style="max-width:300px; display:block; clear:both; margin-top:10px;" src="' . $meta_fb_image . '" title="Image URL" alt=""/>
            </div><br><br>';

  echo'<input type="hidden" name="submit_chk" value="" />';
  echo '<small>';
       _e("",'realmlang' );
  echo '</small>'; 
  
  echo $html;  
}

/*Invoke the box*/
function surface_create_page_metabox()
{
  if(function_exists('add_meta_box')){
    add_meta_box( 'page', 'Social Meta Informations', 'surface_define_page_metabox', 'page', 'normal', 'high' );
	
  }
}

/*-for saving the meta--*/
function surface_save_metaboxdata($post_id){

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;
	if(isset( $_POST['surface_noncename'])) 
	{
		if ( !wp_verify_nonce( $_POST['surface_noncename'], plugin_basename( __FILE__ ) ) )
		  return;
	}
  // Check permissions
	if(isset( $_POST['post_type'])){
		if ( 'page' == $_POST['post_type'] ){
		  if ( !current_user_can( 'edit_page', $post_id ) )
			  return;
		}
		else{
		  if ( !current_user_can( 'edit_post', $post_id ) )
			  return;
		}
	}

	if(isset($_POST['submit_chk'])){
		$fb_title			= $_POST['fb_title'];
		$fb_type			= $_POST['fb_type'];
		$fb_image			= $_POST['fb_image'];
		$fb_url				= $_POST['fb_url'];
		$fb_site_name		= $_POST['fb_site_name'];
		$fb_description		= $_POST['fb_description'];
		
		update_post_meta($post_id,'fb_title',$fb_title);
		update_post_meta($post_id,'fb_type',$fb_type);
		update_post_meta($post_id,'fb_image',$fb_image);
		update_post_meta($post_id,'fb_url',$fb_url);
		update_post_meta($post_id,'fb_site_name',$fb_site_name);
		update_post_meta($post_id,'fb_description',$fb_description);
	}

}

//Initialize
add_action('admin_menu', 'surface_create_page_metabox'); /*--Plug the metabox*/
add_action( 'save_post', 'surface_save_metaboxdata' ); /*--save metabox content*/

/*--Writing in header--*/
if(!function_exists('wp_social_meta_function')){
	function wp_social_meta_function(){
		
		global $wp_query;
		
		if(get_queried_object_id()){
			$meta_fb_title = get_post_meta($wp_query->get_queried_object_id(),'fb_title',true);
			$meta_fb_type = 'article';
			$meta_fb_image = get_post_meta($wp_query->get_queried_object_id(),'fb_image',true);
			$meta_fb_url = get_post_meta($wp_query->get_queried_object_id(),'fb_url',true);
			$meta_fb_site_name = get_bloginfo( 'name' );
			$meta_fb_description = get_post_meta($wp_query->get_queried_object_id(),'fb_description',true);
		}else{
			$meta_fb_title			= get_option('wpsm_title');
			$meta_fb_type			= 'article';
			$meta_fb_image			= get_option('wpsm_image');
			$meta_fb_url			= get_option('wpsm_url');
			$meta_fb_site_name		= get_bloginfo( 'name' );
			$meta_fb_description	= get_option('wpsm_description');
		}
		
		$output='<!-- for Google -->
<meta itemprop="name" content="' . $meta_fb_title . '"/>
<meta itemprop="description" content="' . $meta_fb_description . '"/>
<meta itemprop="image" content="' . $meta_fb_image . '"/>	
		
<!-- for Facebook -->  
<meta property="og:title" content="' . $meta_fb_title . '" />
<meta property="og:type" content="' . $meta_fb_type . '" />
<meta property="og:image" content="' . $meta_fb_image . '" />
<meta property="og:url" content="' . $meta_fb_url . '" />
<meta property="og:site_name" content="' . $meta_fb_site_name . '"/>
<meta property="og:description" content="' . $meta_fb_description . '" />
		
<!-- for Twitter -->          
<meta name="twitter:card" content="summary">
<meta name="twitter:url" content="' . $meta_fb_url . '">
<meta name="twitter:title" content="' . $meta_fb_title . '">
<meta name="twitter:description" content="' . $meta_fb_description . '">
<meta name="twitter:image" content="' . $meta_fb_image . '">';
		echo $output;
	}
}

/*--Initialize header function--*/
add_action('wp_head','wp_social_meta_function');

/*--Settings Page--*/
add_action('admin_menu', 'wp_social_meta_settings');

function wp_social_meta_settings() {
    add_menu_page('WP Social Meta', 'WP Social Meta', 'administrator', 'wpsm_settings', 'wpsm_display_settings','');
}

function wpsm_display_settings() {
    $wpsm_title = (get_option('wpsm_title') != '') ? get_option('wpsm_title') : '' . get_bloginfo( 'name' ) . '';
    $wpsm_description = (get_option('wpsm_description') != '') ? get_option('wpsm_description') : '' . get_bloginfo( 'description' ) . '';
    $wpsm_url = (get_option('wpsm_url') != '') ? get_option('wpsm_url') : '' . network_site_url( '/' ) . '';
    $wpsm_image = (get_option('wpsm_image') != '') ? get_option('wpsm_image') : '';
    
    $html = '<div class="wrap">

            <form method="post" name="options" action="options.php">

            <h2> WP Social Meta Settings</h2><em>If your website home is not a single page or your website home is a blog consisting of posts, then you need to fill the following meta informations for your home page.</em>' . wp_nonce_field('update-options') . '
            <table width="100%" cellpadding="10" class="form-table">
                
                <tr>
                    <td align="left" scope="row">
                    <label><h4>Website Title</h4></label><input type="text" size="50" name="wpsm_title" value="' . $wpsm_title . '" />
                    </td> 
                </tr>
				
                <tr>
                    <td align="left" scope="row">
                    <label><h4>Website Description</h4></label><textarea name="wpsm_description" rows="4" cols="50">' . $wpsm_description . '</textarea>
                    </td> 
                </tr>
				
                <tr>
                    <td align="left" scope="row">
                    <label><h4>Website URL</h4></label><input type="text" size="50" name="wpsm_url" value="' . $wpsm_url . '" />
                    </td> 
                </tr>
				
				<tr valign="top">
					<td><label for="upload_image"><h4>Banner Image URL</h4></label>
					<input id="upload_image" type="text" size="36" name="wpsm_image" value="' . $wpsm_image . '" />
					<input id="upload_image_button" type="button" value="Upload Image" />
					<br />Enter an URL or upload an image for the banner.
					</label></td>
				</tr>
				
            </table>
            <p class="submit">
                <input type="hidden" name="action" value="update" />  
                <input type="hidden" name="page_options" value="wpsm_title,wpsm_description,wpsm_url,wpsm_image" /> 
                <input type="submit" name="Submit" value="Update" />
            </p>
            </form>

        </div>';
    echo $html;
}

function my_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
}

function my_admin_styles() {
	wp_enqueue_style('thickbox');
}

if (isset($_GET['page']) && $_GET['page'] == 'wpsm_settings') {
	add_action('admin_print_scripts', 'my_admin_scripts');
	add_action('admin_print_styles', 'my_admin_styles');
}
/*--Settings Page--*/
?>