<?php
/*
Plugin Name: S2Member Rewrite
Plugin URI: http://www.bryanheadrick.com
Description: An empty plugin template to start with, including the most basic necessary stuff
Version: 0.1.1.2
Author: Bryan Headrick, bryan@bryanheadrick.com
Author URI: http://www.bryanheadrick.com
License: GPL2
*/

/*  Copyright 2012  Bryan Headrick  (email : bryan@bryanheadrick.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?><?php

// some definition we will use
define( 'S2MRW_PLUGIN_NAME', 'S2Member Rewrite');
define( 'S2MRW_PLUGIN_DIRECTORY', 's2member-rewrite');
define( 'S2MRW_CURRENT_VERSION', '0.1' );
define( 'S2MRW_CURRENT_BUILD', '20' );
define( 'S2MRW_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR).'/s2mrw-logs/');
define( 'S2MRW_DEBUG', false);		# never use debug mode on productive systems
// i18n plugin domain for language files
define( 'EMU2_I18N_DOMAIN', 's2mrw' );


// create custom plugin settings menu
add_action( 'admin_menu', 's2mrw_create_menu' );

//call register settings function
add_action( 'admin_init', 's2mrw_register_settings' );


// deactivating
function s2mrw_deactivate() {
	// needed for proper deletion of every option
	delete_option('s2mrw_options');
}

// uninstalling
function s2mrw_uninstall() {
	# delete all data stored
	delete_option('s2mrw_options');
}

function s2mrw_create_menu() {

	

	// or create options menu page
	add_options_page(__('S2Member Rewrite', EMU2_I18N_DOMAIN), __("S2Member Rewrite Settings", EMU2_I18N_DOMAIN), 9,'s2mrw_options', 's2mrw_do_page');
        

	// or create sub menu page
	$parent_slug="index.php";	# For Dashboard
	#$parent_slug="edit.php";		# For Posts
	// more examples at http://codex.wordpress.org/Administration_Menus
	add_submenu_page( $parent_slug, __("HTML Title 4", EMU2_I18N_DOMAIN), __("Menu title 4", EMU2_I18N_DOMAIN), 9, S2MRW_PLUGIN_DIRECTORY.'/s2mrw_settings_page.php');
}


function s2mrw_register_settings() {
	//register settings
	register_setting( 's2mrw-settings-group', 's2mrw_options','s2mrw_validate' );

}
function s2mrw_validate($input) {
	// Our first value is either 0 or 1
	$input['option1'] = ( $input['brewrite'] == 1 ? 1 : 0 );
	
	// Say our second option must be safe text with no HTML tags
	//$input['sometext'] =  wp_filter_nohtml_kses($input['sometext']);
	
	return $input;
}
// check if debug is activated
function s2mrw_debug() {
	# only run debug on localhost
	if ($_SERVER["HTTP_HOST"]=="localhost" && defined('EPS_DEBUG') && EPS_DEBUG==true) return true;
}
add_filter( 'script_loader_src', 's2member_rewrite' );
add_filter( 'style_loader_src', 's2member_rewrite' );

function s2member_rewrite( $url )
{
    $options = get_option('s2mrw_options');
    if($options['brewrite']==1)  :
    
    
    if(strpos($url,'ws_plugin__s2member_css')){
        
        $url = str_replace('s2member-o.php?ws_plugin__s2member_css=', '', $url);
       $url = str_replace('&', '-', $url);
               $url = str_replace('qcABC=', '', $url);
        $url = str_replace('ver=', '', $url);
        $url .= '.css';

    }
	if(strpos($url,'ws_plugin__s2member_js_w_globals')){
          $url = str_replace('s2member-o.php?ws_plugin__s2member_js_w_globals=', '', $url);
       $url = str_replace('&', '-', $url);
        $url = str_replace('qcABC=', '', $url);
        $url = str_replace('ver=', '', $url);
            $url .= '.js';
        }
        
        endif;
        return $url;
	
	
}

function s2mrw_do_page() {
	?>
	<div class="wrap">
		<h2><?php print S2MRW_PLUGIN_NAME ." ". S2MRW_CURRENT_VERSION. "<sub>(Build ".S2MRW_CURRENT_BUILD.")</sub>"; ?></h2>

		<form method="post" action="options.php">
			<?php settings_fields('s2mrw-settings-group'); ?>
			<?php $options = get_option('s2mrw_options'); ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Enable Rewrite</th>
					<td><input name="s2mrw_options[brewrite]" type="checkbox" value="1" <?php checked('1', $options['brewrite']); ?> /></td>
				</tr>
<!--				<tr valign="top"><th scope="row">Some text</th>
					<td><input type="text" name="ozh_sample[sometext]" value="<?php echo $options['sometext']; ?>" /></td>
				</tr>
-->			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
        $s2memberurl = plugins_url( 's2member' );
        $s2memberurl = str_replace(site_url(),'', $s2memberurl);
        $s2memberpath = realpath(dirname(__FILE__). '/../') .'/s2member';
    echo  '<strong>Path to s2member plugin</strong>:' . $s2memberpath. '';

    $action = esc_attr($_POST["s2rw_action"]);  
if($options['brewrite']==1) 
    {
    $rules = '<IfModule mod_rewrite.c> 
Options +FollowSymlinks
RewriteEngine on
RewriteBase ' . $s2memberurl . '
 
RewriteRule ^([^/]+)-([^/]+)\.css$  s2member-o.php?ws_plugin__s2member_css=$1;qcABC=$2;ver=$3 [NC]
RewriteRule ^([^/]+)-([^/]+)\.js$  s2member-o.php?ws_plugin__s2member_js_w_globals=$1;qcABC=$2;ver=$3 [NC]
						
</ifmodule>';
    
   
     if(!file_exists($s2memberpath . '/.htaccess')){ 
         update_option('rewrite_successful','false');
    
if ( ! file_put_contents($s2memberpath . '/.htaccess', $rules) ) 
                {
    echo '<br><span style="background:#FA5858">error saving file!<span><br>';
    $error = true;
                }
                else {update_option("rewrite_successful", 'true');
                $error = $false;
                 echo '<br><span style="background:#58FA58">.htaccess file is in place!<span><br>';
                }
                
    }
    else{
         $filecontents = file_get_contents($s2memberpath . '/.htaccess');
        if($filecontents == $rules){update_option("rewrite_successful", 'true');
        echo '<br><span style="background:#58FA58">.htaccess file is in place!<span><br>';
        }
    }
    
    } else{
        if(file_exists($s2memberpath . '/.htaccess')){
            unlink($s2memberpath . '/.htaccess');
        }
    }
}
?>
