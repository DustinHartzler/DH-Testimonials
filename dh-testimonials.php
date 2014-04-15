<?php
/* 
Plugin Name: DH Testimonials
Plugin URI: http://YourWebsiteEngineer.com
Description: Displays Testimonals for a website in a Speech Bubble
Version: 1.00
Author: Dustin Hartzler
Author URI: http://YourWebsiteEngineer.com
*/


if ( !class_exists('DHTestimonails') ) {
    class DHTestimonails {
    	//public $wpdb;
// +---------------------------------------------------------------------------+
// | WP hooks                                                                  |
// +---------------------------------------------------------------------------+
		function __construct() {
		/* WP actions */
            add_action( 'init', array(&$this, 'dht_addscripts'));
            add_action( 'admin_init', array(&$this, 'register_options'));
            add_action( 'admin_menu', array(&$this, 'addpages'));
			add_action( 'plugins_loaded', array(&$this, 'set'));
			add_shortcode( 'dht', array(&$this, 'showall'));
		}

		function register_options() { // whitelist options
			register_setting( 'option-widget', 'admng' );
			register_setting( 'option-widget', 'showlink' );
			register_setting( 'option-widget', 'linktext' );
			register_setting( 'option-widget', 'image_width');
			register_setting( 'option-widget', 'image_height');
			register_setting( 'option-widget', 'opacity');
			register_setting( 'option-widget', 'setlimit' );
			register_setting( 'option-widget', 'linkurl' );
			register_setting( 'option-page', 'dht_imgalign' );
			register_setting( 'option-page', 'imgdisplay' );
			register_setting( 'option-page', 'imgmax' );
			register_setting( 'option-page', 'sorder' );
			register_setting( 'option-page', 'deldata' );
		}

		function unregister_options() { // unset options
			unregister_setting( 'option-widget', 'admng' );
			unregister_setting( 'option-widget', 'showlink' );
			unregister_setting( 'option-widget', 'linktext' );
			unregister_setting( 'option-widget', 'image_width');
			unregister_setting( 'option-widget', 'image_height');
			unregister_setting( 'option-widget', 'opacity');
			unregister_setting( 'option-widget', 'setlimit' );
			unregister_setting( 'option-widget', 'linkurl' );
			unregister_setting( 'option-page', 'dht_imgalign' );
			unregister_setting( 'option-page', 'imgmax' );
			unregister_setting( 'option-page', 'sorder' );
			unregister_setting( 'option-page', 'deldata' );
		}


		function dht_addscripts() { // include style sheet
			wp_enqueue_style('style_css', plugins_url('/dh-testimonials/css/as-heard-on-style.css') );
			if ( ! is_admin() ) {
				wp_enqueue_script( 'display', plugins_url('/as-heard-on/js/display.js') ,array('jquery') ); 
				wp_enqueue_script( 'jquery' );
			} 
		} 
	
// +---------------------------------------------------------------------------+
// | Create admin links                                                        |
// +---------------------------------------------------------------------------+

		function addpages() { 
			add_menu_page('Testimonials', 'Testimonials', 'manage_options', 'dht_setting_page', array($this, 'settings_pages'), 'dashicons-testimonial');
		}


// +---------------------------------------------------------------------------+
// | Add Settings Link to Plugins Page                                         |
// +---------------------------------------------------------------------------+

		function add_settings_link($links, $file) {
			static $plugin;
			if (!$plugin) $plugin = plugin_basename(__FILE__);
			
			if ($file == $plugin){
				$settings_link = '<a href="admin.php?page=dht_setting_page">'.__("Configure").'</a>';
				$links[] = $settings_link;
			}
			return $links;
		}

		function set() {
			if (current_user_can('update_plugins')) 
			add_filter('plugin_action_links', array(&$this, 'add_settings_link'), 10, 2 );
		}

// +---------------------------------------------------------------------------+
// | Plugin Settings Pages 										               |
// +---------------------------------------------------------------------------+

		function settings_pages(){
			global $saf_networks; ?>

			<div class="wrap">
				<?php screen_icon('options-general'); ?>
				<h2>Testimonial Settings</h2>
				<style>
					#reset_color { cursor:pointer; }
				</style>

				<?php
				$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'dht_add_new_testimonial';
				?>

				<h2 class="nav-tab-wrapper">
					<a href="admin.php?page=setting_page&tab=dht_add_new_testimonial" class="nav-tab <?php echo $active_tab == 'dht_add_new_testimonial' ? 'nav-tab-active' : ''; ?>">Testimonials</a>
				</h2>

				<?php
				if ( $active_tab == 'dht_add_new_testimonial' ) {  
					$this->adminpage();
				} elseif ( $active_tab == 'dht_widget_options' ) { 
					$this->widget_options();
				} elseif ( $active_tab == 'dht_full_page_options' ) {
					$this->page_options();
				}

				?> </div> <?php
		}

// +---------------------------------------------------------------------------+
// | Add New Testimonail                                                       |
// +---------------------------------------------------------------------------+

		function newform() { ?>
			<div class="wrap">
				<h2>Add New Testimonial</h2>
				<ul>
				<li>If you want the testimonial to appear, you must include &quot;Testimonial&quot; field.</li>
				</ul>
				<br />
				<div id="ppg-form">
					<form name="AddNew" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
						<table cellpadding="0" cellspacing="2">
							<tr valign="top">
								<td><label for="showname">Name:</label></td>
								<td><input name="show_name" type="text" size="45" ></td>
							</tr>
							<tr valign="top">
								<td><label for="hostname">Company Name:</label></td>
								<td><input name="host_name" type="text" size="45" ></td>
							</tr>

							<tr valign="top">
								<td><label for="showurl">Company URL:</label></td>
								<td><input name="show_url" type="text" size="45" value="http://" onFocus="this.value=''"></td>
							</tr>

							<tr valign="top">
								<td><label for="testimonial">Testimonial:</label></td>
								<td><textarea name="testimonial" cols="45" rows="7"></textarea></td>
							</tr>

							<tr valign="top">
								<td><label for="imgurl">Image URL:</label></td>
								<td><input name="imgurl" type="text" size="45" ><input class="media-upload-button button" type="button" value="Upload Image" /></td>
							</tr>

							<tr valign="top">
								<td><label for="storder">Sort order:</label></td>
								<td><input name="storder" type="text" size="10" /> (optional) </td>
							</tr>
							<tr valign="top">
								<td></td>
								<td><input type="submit" name="addnew" class="button button-primary" value="<?php _e('Add Testimonal', 'addnew' ) ?>" /></td>
							</tr>
						
					</table>
					</form>
				</div>
			</div>
		<?php } 

/* insert podcast into DB */
		function insertnew() {
			global $wpdb;

			$allowed_html = array(
			    'a' => array(
			        'href' => array(),
			        'title' => array()
			    ),
			    'br' => array(),
			    'em' => array(),
			    'strong' => array()
			);

			$table_name = $wpdb->prefix . "dht";
			$show_name 	= sanitize_text_field( $_POST['show_name'] );	
			$host_name 	= sanitize_text_field( $_POST['host_name'] );
			$show_url 	= sanitize_text_field( $_POST['show_url'] );
			$imgurl 	= sanitize_text_field( $_POST['imgurl'] );
			$testimonial 	= wp_kses( $_POST['testimonial'], $allowed_html );
			$storder 	= sanitize_text_field( $_POST['storder'] );
			
			$insert = $wpdb->prepare( "INSERT INTO " . $table_name .
				" (show_name,host_name,show_url,imgurl,episode,testimonial,storder) " .
				"VALUES ('%s','%s','%s','%s','%d','%s','%s')",
				$show_name,
				$host_name,
				$show_url,
				$imgurl,
				$episode,
				$testimonial,
				$storder
			);
			
			$results = $wpdb->query( $insert );

		}
// +---------------------------------------------------------------------------+
// | Create table on activation                                                |
// +---------------------------------------------------------------------------+

		function activate () {
   			global $wpdb;

   			$table_name = $wpdb->prefix . "dht";
   			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				if ( $wpdb->supports_collation() ) {
						if ( ! empty($wpdb->charset) )
							$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
						if ( ! empty($wpdb->collate) )
							$charset_collate .= " COLLATE $wpdb->collate";
				}
      
			   $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
				testid int( 15 ) NOT NULL AUTO_INCREMENT ,
				show_name text,
				host_name text,
				show_url text,
				episode text,
				imgurl text,
				testimonial text,
				storder INT( 5 ) NOT NULL,
				PRIMARY KEY ( `testid` )
				) ".$charset_collate.";";
	  
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
	  
			   	$insert = "INSERT INTO " . $table_name .
		           	" (show_name,host_name,show_url,episode,imgurl) " .
		            "VALUES ('Your Website Engineer','Dustin Hartzler','http://YourWebsiteEngineer.com','001','http://YourWebsiteEngineer.com/AlbumArt.png')";
		      	$results = $wpdb->query( $insert );

				// insert default settings into wp_options 
				$toptions = $wpdb->prefix ."options";
				$defset = "INSERT INTO ".$toptions.
					"(option_name, option_value) " .
					"VALUES ('sfs_admng', 'update_plugins'),('sfs_deldata', ''),".
					"('sfs_linktext', 'Read More'),('sfs_linkurl', ''),('sfs_setlimit', '1'),".
					"('sfs_showlink', ''),('sfs_imgalign','right'),('sfs_sorder', 'testid DESC')";
				$dodef = $wpdb->query( $defset );

			} 	
				// update version in options table
				  delete_option("ppg_version");
				  add_option("ppg_version", "0.5");
		}

		/* update item in DB */
		function dht_editdo($testid){
			global $wpdb;
			$allowed_html = array(
			    'a' => array(
			        'href' => array(),
			        'title' => array()
			    ),
			    'br' => array(),
			    'em' => array(),
			    'strong' => array()
			);

			$table_name = $wpdb->prefix . "dht";
			
			$testid = $testid;
			$show_name 	= sanitize_text_field( $_POST['show_name'] );	
			$host_name 	= sanitize_text_field( $_POST['host_name'] );
			$show_url 	= sanitize_text_field( $_POST['show_url'] );
			$imgurl 	= sanitize_text_field( $_POST['imgurl'] );
			$episode 	= sanitize_text_field( $_POST['episode'] );
			$testimonial 	= wp_kses( $_POST['testimonial'], $allowed_html );
			$storder 	= sanitize_text_field( $_POST['storder'] );
			
			$wpdb->query("UPDATE " . $table_name .
			" SET show_name = '$show_name', ".
			" host_name = '$host_name', ".
			" show_url = '$show_url', ".
			" imgurl = '$imgurl', ".
			" episode = '$episode', ".
			" testimonial = '$testimonial', ".
			" storder = '$storder' ".
			" WHERE testid = '$testid'");
		}

		/* delete testimonials from DB */
		function removetst($testid) {
			global $wpdb;
			$table_name = $wpdb->prefix . "dht";
			
			$insert = $wpdb->prepare( "DELETE FROM " . $table_name .
			" WHERE testid = '%d'", absint( $testid ) );
			
			$results = $wpdb->query( $insert );

		}

		/* admin page display */
		function adminpage() {
			global $wpdb; ?>
			<div class="wrap">
			<?php
				if (isset($_POST['addnew'])) {
					$this->insertnew();
					?><div id="message" class="updated fade"><p><strong><?php _e('Testimonial Added'); ?>.</strong></p></div><?php
				}
				if ($_REQUEST['mode']=='dhtrem') {
					$this->removetst($_REQUEST['testid']);
					?><div id="message" class="updated fade"><p><strong><?php _e('Testimonial Deleted'); ?>.</strong></p></div><?php
				}
				if ($_REQUEST['mode']=='dhtedit') {
					$this->dht_edit($_REQUEST['testid']);
					exit;
				}
				if (isset($_REQUEST['editdo'])) {
					$this->dht_editdo($_REQUEST['testid']);
					?><div id="message" class="updated fade"><p><strong><?php _e('Testimonial Updated'); ?>.</strong></p></div><?php
				}
					$this->showlist(); // show podcasts
				?>
			</div>
			<div class="wrap">
				<?php $this->newform(); // show form to add new podcast ?>
			</div>
<?php }



// +---------------------------------------------------------------------------+
// | Manage Page - list all and show edit/delete options                       |
// +---------------------------------------------------------------------------+
/* show podcast on settings page */
		function showlist() { 
			global $wpdb;
			$table_name = $wpdb->prefix . "dht";
			$dhtlists = $wpdb->get_results("SELECT testid,show_name,host_name,show_url,imgurl,episode FROM $table_name");

			foreach ($dhtlists as $dhtlist) {
				echo '<div class="podcast-display">';
				echo '<img src="'.$dhtlist->imgurl.'" width="100px" class="alignleft" style="margin:0 10px 10px 0; border-radius: 50px;">';
				echo '<a href="admin.php?page=dht_setting_page&amp;mode=dhtedit&amp;testid='.$dhtlist->testid.'">Edit</a>';
				echo '&nbsp;|&nbsp;';
				echo '<a href="admin.php?page=dht_setting_page&amp;mode=dhtrem&amp;testid='.$dhtlist->testid.'" onClick="return confirm(\'Delete this testimonial?\')">Delete</a>';
				echo '<br>';
				echo '<strong>Name: </strong>';
				echo stripslashes($dhtlist->show_name);
					if ($dhtlist->host_name != '') {
						echo '<br><strong>Company Name: </strong>'.stripslashes($dhtlist->host_name).'';
						if ($dhtlist->show_url != '') {
							echo '<br><strong>Company URL: </strong> <a href="'.$dhtlist->show_url.'" rel="wordbreak">'.stripslashes($dhtlist->show_url).'</a> ';
							echo '<br><strong>Testimonial: </strong>'.stripslashes($dhtlist->testimonial).'';	
						}
					}
				echo '</div>'; 
			}
			echo '<div class="dht-clear"></div>';
		}

		/* edit podcast form */
		function dht_edit($testid){
			global $wpdb;
			$table_name = $wpdb->prefix . "dht";
			
			$getdht = $wpdb->get_row("SELECT testid, show_name, host_name, show_url, imgurl, episode, testimonial, storder FROM $table_name WHERE testid = $testid"); ?>
			
			<h3>Edit Podcast</h3
			<div id="ppg-form">
				<?php echo '<form name="edittst" method="post" action="admin.php?page=dh_setting_page">';?>
					<table cellpadding="2" cellspacing="2">
						<tr valign="top">	
							<td><label for="show_name">Show Name:</label></td>
				  			<?php echo '<td><input name="show_name" type="text" size="45" value="'. stripslashes($getdht->show_name).'"></td>';
				  		?></tr>
				  		<tr valign="top">
							<td><label for="host_name">Host Name:</label></td>
				  			<td><input name="host_name" type="text" size="45" value="<?php echo stripslashes($getdht->host_name)?>"></td>
						</tr>

						<tr valign="top">
							<td><label for="show_url">Show URL:</label></td>
				 			<td><input name="show_url" type="text" size="45" value="<?php echo $getdht->show_url ?>"></td>
				 		</tr>
				
						<tr valign="top">
							<td><label for="imgurl">Image URL:</label></td>
							<td><input name="imgurl" type="text" size="45" value="<?php echo $getdht->imgurl ?>"><input class="media-upload-button button" type="button" value="Upload Image" /></td>
						</tr>
						
						<tr valign="top">
							<td><label for="episode">Episode:</label></td>
				 			<td><input name="episode" type="text" size="2" value="<?php echo $getdht->episode ?>"></td>
				 		</tr>

				 		<tr valign="top">
				 			<td><label for="testimonial">Testimonial:</label></td>
				  			<td><textarea name="testimonial" cols="45" rows="7"><?php echo stripslashes($getdht->testimonial) ?></textarea></td>
				  		</tr>

				  		<tr valign="top">
							<td><label for="storder">Sort order:</label></td>
				 			<td><input name="storder" type="text" size="2" value="<?php echo $getdht->storder ?>">(optional)</td>
				 		</tr>

				 		<tr valign="top">
				  			<?php echo'<td><input type="hidden" name="testid" value="'.$getdht->testid.'"></td>'; ?>
				  			<td><input name="editdo" type="submit" class="button button-primary" value="Update"></td>
				  		</tr>
				  	</table>

			<?php echo '<h3>Preview</h3>';
			echo '<div class="podcast-display" >';
			echo '<img src="'.$getdht->imgurl.'" width="90px" class="alignleft" style="margin:0 10px 10px 0;">';
				echo '<strong>Show Name: </strong>';
				echo stripslashes($getdht->show_name);
					if ($getdht->host_name != '') {
						echo '<br><strong>Host Name: </strong>'.stripslashes($getdht->host_name).'';
						if ($getdht->show_url != '') {
							echo '<br><strong>Show URL: </strong> <a href="'.$getdht->show_url.'">'.stripslashes($getdht->show_url).'</a> ';
							if ($getdht->episode !=''){
							echo '<br><strong>Episode: </strong>'.stripslashes($getdht->episode).'';	
							}	
							if ($getdht->testimonial !=''){
							echo '<br><strong>Show Recap: </strong>'.stripslashes($getdht->testimonial).'';	
							}
						}
					}
				echo '</div>'; 
			echo '</form>';
			echo '</div>';
		}


// +---------------------------------------------------------------------------+
// | Uninstall plugin                                                          |
// +---------------------------------------------------------------------------+

		function deactivate () {
			global $wpdb;

			$table_name = $wpdb->prefix . "dht";

			$dht_deldata = get_option('dht_deldata');
			if ($dht_deldata == 'yes') {
				$wpdb->query("DROP TABLE {$table_name}");
				delete_option("dht_showlink");
				delete_option("dht_linktext");
				delete_option("dht_linkurl");
				delete_option("dht_deldata");
				delete_option("dht_setlimit");
				delete_option("dht_admng");
				delete_option("dht_sorder");
				delete_option("dht_imgalign");
				delete_option("dht_imgmax");
		 	}
		    delete_option("dht_version");
		}
	}
}

if(class_exists('DHTestimonails')) { 
	// Installation and uninstallation hooks 
	register_activation_hook(__FILE__, array('DHTestimonails', 'activate')); 
	register_deactivation_hook(__FILE__, array('DHTestimonails', 'deactivate')); 

	// instantiate the plugin class 
	$wp_plugin_template = new DHTestimonails(); 
}

// +---------------------------------------------------------------------------+
// | Widget for podcast(s) in sidebar                                          |
// +---------------------------------------------------------------------------+
	### Class: WP-Testimonials Widget
	class DHT_Widget extends WP_Widget {
		// Constructor
		function dht_widget() {
			$widget_ops = array('description' => __('Displays random podcast in your sidebar', 'wp-podcast'));
			$this->WP_Widget('podcasts', __('DH Testimonials'), $widget_ops);
		}
	 
		// Display Widget
		function widget($args, $instance) {
			extract($args);
			$title = esc_attr($instance['title']);
	
			echo $before_widget.$before_title.$title.$after_title;
	
				$this->onerandom();
	
			echo $after_widget;
		}
	 
		// When Widget Control Form Is Posted
		function update($new_instance, $old_instance) {
			if (!isset($new_instance['submit'])) {
				return false;
			}
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			return $instance;
		}
	 
		// Display Widget Control Form
		function form($instance) {
			global $wpdb;
			$instance = wp_parse_args((array) $instance, array('title' => __('Hear Me On Other Shows', 'wp-podcast')));
			$title = esc_attr($instance['title']);
		?>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-podcast'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
	 		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
	<?php
		}

// +---------------------------------------------------------------------------+
// | Sidebar - show random podcast(s) in sidebar                               |
// +---------------------------------------------------------------------------+

/* show random testimonial(s) in sidebar */
		function onerandom() {
			global $wpdb;
			$table_name = $wpdb->prefix . "dht";
				$setlimit = 1;

			$randone = $wpdb->get_results("SELECT show_name, show_url, episode, testimonial, imgurl FROM $table_name WHERE show_url !='' order by RAND() LIMIT $setlimit");

			echo '<div id="quote-3">';
			
			foreach ($randone as $randone2) {
				echo '<div class="quote">';
				echo '<div class="quoteBox-1">';
				echo '<div class="quoteBox-2">';
				echo '<p>'.$randone2->testimonial.'</p>';
				echo '</div></div></div>';
				echo '<a href="'.nl2br(stripslashes($randone2->show_url)).'" target="_blank"><img style="border-radius: 35px;" title="'.$randone2->show_name.'"src="'.$randone2->imgurl.'" width="'.get_option('image_width').'" height="'.get_option('image_height').'" style="margin-right:10px;"></a>';				
				
			} // end loop
			
			echo '<div class="dht-clear"></div>';
			echo '</div>';
		}
}

// +---------------------------------------------------------------------------+
// | Function: Init WP-Testimonials  Widget                                    |
// +---------------------------------------------------------------------------+
	add_action('widgets_init', 'widget_dht_init');
	function widget_dht_init() {
		register_widget('dht_widget');
	}