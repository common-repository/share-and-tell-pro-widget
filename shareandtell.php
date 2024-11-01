<?php
/*
Plugin Name: Share And Tell Pro
Plugin URI: http://pro.shareandtell.com
Description: Harness the power of word-of-mouth marketing over popular social networks. Sign up for Share and Tell Pro at http://pro.shareandtell.com
Version: 1.06
Author: Shareandtell
Author URI: http://shareandtell.com
*/
add_option("shareandtell_config_id", 185);
add_option("shareandtell_async", true);


// only add the option if it doesn't already exist, otherwise sat server is hit on every widget load to provide the js
if(get_option("shareandtell_script") == null){
  add_option("shareandtell_script", shareandtell_js_from_sat_server());
}


add_action('get_footer', 'shareandtell_output_js');

// return the script stored as an option
function shareandtell_js_from_option(){
  return get_option("shareandtell_script");
}

// get the javascript from the sat server
function shareandtell_js_from_sat_server(){
  $host = "http://www.shareandtell.com";

  $async = get_option("shareandtell_async");
  $config = get_option("shareandtell_config_id");

  if($async == "true"){
    $type = "async";
  }else{
    $type = "simple";
  }

  return file_get_contents($host."/widget/widget_javascript?config_id=".$config."&type=".$type);
}

// echo the javascript onto the page
function shareandtell_output_js(){
  echo shareandtell_js_from_option();
  return;
}

// update the script stored in the options
function shareandtell_update_script_option(){
  update_option("shareandtell_script", shareandtell_js_from_sat_server());
  return;
}

// Build the settings menu
add_action('admin_menu', 'shareandtell_menu');

function shareandtell_menu(){
  add_submenu_page('options-general.php', 'Edit ShareAndTell Plugin', 'ShareAndTell Plugin', 'administrator', 'shareandtell-plugin-edit', 'shareandtell_options');
  add_action('admin_init', 'register_options');
}
function register_options(){
  register_setting('shareandtell-plugin-settings', 'shareandtell_config_id');
  register_setting('shareandtell-plugin-settings', 'shareandtell_async');
}
function shareandtell_options(){
  // update the javascript on the sat options page
  shareandtell_update_script_option();
  ?>
  <div class="wrap">
    <h2>Share and Tell Plugin Settings</h2>
    <form method='POST' action='options.php'>
      <?php settings_fields('shareandtell-plugin-settings'); ?>
      <style>
        .sat_settings_desc{
          font-size:8pt;
          color:#aaa
        }
        .sat_table_tr{
          border-bottom:1px solid #ddd;
        }
      </style>
      <table class='form-table'>
        <tr valign='top' class="sat_table_tr">
          <th scope='row'>Config ID</th>
          <td><input type='text' name='shareandtell_config_id' value='<?php echo get_option('shareandtell_config_id'); ?>' /></td>
          <td class="sat_settings_desc">Get your config ID from http://pro.shareandtell.com, login and look at the page titled "Your Widgets" for your configuration ID.  It is unique to your product / website and will let the widget know what product to show when a user clicks it.</td>
        </tr>
        <tr valign='top' class="sat_table_tr">
          <th scope='row'>Script Type</th>
          <td>
            <?php if(get_option("shareandtell_async") == "true"){ ?>
              <input type="radio" name="shareandtell_async" value="true" checked> Asynchronous<br/>
              <input type="radio" name="shareandtell_async" value="false"> Simple
            <?php } else { ?>
              <input type="radio" name="shareandtell_async" value="true"> Asynchronous<br/>
              <input type="radio" name="shareandtell_async" value="false" checked> Simple
            <?php } ?>
          </td>
          <td class="sat_settings_desc">We offer an asynchronous version of our widget so that installation of the widget JavaScript file will occur at the same time that the rest of your website is loading.  This will allow you to install the widget without slowing down the load time of your website. If you are having problems getting the widget to work, try using the simple synchronous solution.</td>
        </tr>
      </table>
      <p class='submit'>
          <input type='submit' class='button-primary' value='<?php _e('Save Changes') ?>' />
      </p>
    </form>
  </div>
  <?php
}

?>