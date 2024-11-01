<?php
namespace AMZNBS;

/*
  Admin Class

  Contains all of the functions for managing AMZNBS administration functions.

*/
if (!class_exists('\HeyPublisher\Base')) {
  require_once( dirname(__FILE__) . '/../HeyPublisher/Base.class.php');
}
class Admin extends \HeyPublisher\Base {

  var $help = false;
  var $options = array();
  var $old_widget_name = 'widget_supportgreatwriters';
  var $widget_name = 'widget_sgw';
  var $max_asins_per = 4;
  var $post_meta_key = SGW_POST_META_KEY;
  var $error = false;
  var $donate_link = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y8SL68GN5J2PL';
  var $defaults = array('tag' => 'SGW_ASIN', 'aff_id' => 'sgw02-20', 'aff_cc' => 'com');

  public function __construct() {
    parent::__construct();
    $this->logger->debug("HeyPublisher::Base::Admin loaded");
    $this->options = get_option(SGW_PLUGIN_OPTTIONS);
    $this->logger->debug(sprintf("\tAdmin#__construct\n\t\$this->options = %s",print_r($this->options,true)));
    // $this->check_plugin_version();  // may need to reintroduce this
    $this->nav_slug = SGW_ADMIN_PAGE; // not 'amazon_bookstore' because this needs to map to dir name
    $this->slug = 'support-great-writers'; // not 'amazon_bookstore' because this needs to map to dir name
    // Sidebar configs
    $this->plugin['home'] = 'https://github.com/HeyPublisher/amazon-book-store';
    $this->plugin['support'] = 'https://github.com/HeyPublisher/amazon-book-store/issues';
    $this->plugin['contact'] = 'mailto:wordpress@heypublisher.com';
    $this->plugin['more'] = 'https://github.com/HeyPublisher/';
  }

  public function __destruct() {
    parent::__destruct();
  }
  public function activate_plugin() {
    $this->logger->debug("in the activate_plugin()");
    $this->check_plugin_version();
  }

  // Primary action handler for page
  function action_handler() {
    parent::page('Amazon Book Store Settings', '', array($this,'content'));
  }

  public function deactivate_plugin() {
    $this->options = false;
    delete_option(SGW_PLUGIN_OPTTIONS);  // remove the default options
	  return;
  }

  private function plugin_admin_url() {
    $url = 'options-general.php?page='.SGW_ADMIN_PAGE;
    return $url;
  }
  // Called by plugin filter to create the link to settings
  public function plugin_link($links) {
    $url = $this->plugin_admin_url();
    $settings = '<a href="'. $url . '">'.__("Settings", "sgw").'</a>';
    array_unshift($links, $settings);  // push to left side
    return $links;
  }

  // Filter for creating the link to settings
  public function plugin_filter() {
    return sprintf('plugin_action_links_%s',SGW_PLUGIN_FILE);
  }

    public function check_plugin_version() {
    $this->logger->debug("in check_plugin_version()");

    $opts = get_option(SGW_PLUGIN_OPTTIONS);
    // printf("<pre>In check_plugin_version()\n opts = %s</pre>",print_r($opts,1));
    if (!$opts || !$opts[plugin] || $opts[plugin][version_last] == false) {
      $this->logger->debug("no old version - initializing");
      $this->init_plugin();
      // there is a possible upgrade path from old widget to this one - in which case we want to migrate data
      $this->migrate_old_widget();
      return;
    }
    // check for upgrade option here
    if ($opts[plugin][version_current] != SGW_PLUGIN_VERSION) {
      $this->logger->debug("need to upgrade version");
      $this->upgrade_plugin($opts);
      return;
    }
  }

  public function admin_stylesheets(){
    wp_register_style( 'amznbs-heypublisher', plugins_url($this->slug . '/include/css/heypublisher.css' ) );
    wp_register_style( 'amznbs-admin', plugins_url($this->slug . '/include/css/admin.css' ), array('amznbs-heypublisher') );
    wp_enqueue_style('amznbs-heypublisher');
    wp_enqueue_style('amznbs-admin');
  }

  // This is throw-away code.  Once we get everyone upgraded, this can be removed.
  private function migrate_old_widget() {
    $old = get_option($this->old_widget_name);
    if ($old) {
      $asins = array();
      foreach ($old as $i=>$hash) {
        if ($hash[asin1]) { $asins[] = $hash[asin1]; }
        if ($hash[asin2]) { $asins[] = $hash[asin2]; }
      }
      $this->options['default'] = join(',',$asins);
      update_option(SGW_PLUGIN_OPTTIONS,$this->options);
      // uncomment this before final testing
      // delete_option($this->old_widget_name);
    }
    return;
  }
  private function get_version_as_int($str) {
    $var = intval(preg_replace("/[^0-9 ]/", '', $str));
    return $var;
  }

  /**
  * Upgrade path
  */
  private function upgrade_plugin($opts) {
    $ver = $this->get_version_as_int($this->options[plugin][version_current]);
    $this->logger->debug("Version = $ver");
    // printf("<pre>In upgrade_plugin()\n ver = %s\nopts = %s</pre>",print_r($ver,1),print_r($this->options,1));
    if ($ver < 210) {
      $url = $this->plugin_admin_url();
      // need to show the mesage about id changing
      // $html = '<div class="updated"><p>';
      // $html .= __( 'You will need to update your Amazon Associate ID <a href="'.$url.'">on the Settings page</a>.', 'sgw' );
      // echo $html;
    }
    $this->options[plugin][version_last] = $this->options[plugin][version_current];
    $this->options[plugin][version_current] = SGW_PLUGIN_VERSION;
    $this->options[plugin][upgrade_date] = Date('Y-m-d');
    update_option(SGW_PLUGIN_OPTTIONS,$this->options);
  }

  /**
  * Init the Plugin
  */
  private function init_plugin() {
    $this->init_install_options();
    $this->options[plugin][version_last] = SGW_PLUGIN_VERSION;
    $this->options[plugin][version_current] = SGW_PLUGIN_VERSION;
    $this->options[plugin][install_date] = Date('Y-m-d');
    $this->options[plugin][upgrade_date] = Date('Y-m-d');
    add_option(SGW_PLUGIN_OPTTIONS,$this->options);
    return;
  }

  private function init_install_options() {
    $this->options = array(
      'plugin' => array(
        'version_last'    => null,
        'version_current' => null,
        'install_date'    => null,
        'upgrade_date'    => null),
      'affiliate_id'      => 'pif-richard-20',  // default or things break
      'country_id'        => 'com',       // default
      'default' => null,
      'dynamic' => array(),
      'default_meta' => array()
    );
    return;
  }

  private function print_process_errors() {
?>
  <div id='sgw_error'>
    <h2>Error Encountered</h2>
    <p><?php echo $this->error; ?></p>
    <p><b><?php echo SGW_PLUGIN_ERROR_CONTACT; ?></b></p>
  </div>
<?php
  }



  private function normalize_asin_list($list) {
    if (!$list) {
      $list = $this->initialize_default_asins();
      // $this->error = 'You must input at least one ASIN'; return false;
    }
    $new = array();
    $array = explode(',',$list);
    foreach ($array as $asin) {
      $x = trim($asin);
      if (strlen($x) != 10) {
        $this->error = "The ASIN '$x' is invalid - only 10-character ASINs are allowed"; return false;
      } else {
        $new[] = $x;
      }
    }
    $newlist = join(',',$new);
    return $newlist;
  }

  /**
  * Get all of the posts with our custom meta field
  */
  public function get_post_meta() {
    global $wpdb;
    $sql = sprintf("
        SELECT wposts.post_title, wposts.ID, wpostmeta.meta_value
        FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
        WHERE wposts.ID = wpostmeta.post_id
        AND wpostmeta.meta_key = '%s'
        AND wposts.post_type = 'post'
        ORDER BY wposts.post_title ASC", SGW_POST_META_KEY);
    if ($posts = $wpdb->get_results($sql, ARRAY_A)) {
      return $posts;
    }
    return array();
  }

  public function truncate_string($str) {
    if (strlen($str)>40) {
      $str = substr($str,0,40) . '...';
    }
    return $str;
  }

  public function supported_countries() {
    $countries = array(
      'br' => 'Brazil (amazon.com.br)',
      'ca' => 'Canada (amazon.ca)',
      'fr' => 'France (amazon.fr)',
      'de' => 'Germany (amazon.de)',
      'it' => 'Italy (amazon.it)',
      'es' => 'Spain (amazon.es)',
      'co.uk' => 'United Kingdon (amazon.co.uk)',
      'com' => 'United States (amazon.com)'
    );
    return $countries;
  }

  //
  // Update all of the page options sent by the form post
  //
  public function update_options($form) {
     $message = 'Your updates have been saved.';
    if(isset($_POST['save_settings'])) {
      check_admin_referer(SGW_ADMIN_PAGE_NONCE);
      if (isset($_POST['sgw_opt'])) {
        $opts = $_POST['sgw_opt'];
        // printf("<pre>In update_options()\OPTS: %s\naction = %s</pre>",print_r($opts,1),$_REQUEST['action']);
        $this->options['affiliate_id'] = $opts['affiliate_id'];
        $this->options['country_id'] = $opts['country_id'];

        $this->update_default_asins($opts['default']);

        // update the newly added ASINs
        if ($opts['new']) {
          foreach ($opts['new'] as $id=>$hash) {
            if ($test = $this->normalize_asin_list($hash['asin'])) {
              add_post_meta($id,SGW_POST_META_KEY,$test,true) or update_post_meta($id,SGW_POST_META_KEY,$test);
							$message = "Your updates have been saved.";
            } else {
              $this->print_process_errors();
              return false;
            }
          }
        }
        // update the existing post ASINs
        if ($opts['posts']) {
          foreach ($opts['posts'] as $id=>$asin_list) {
            // we delete by setting to null
            if (!$asin_list) {
              delete_post_meta($id,SGW_POST_META_KEY);
            } elseif ($test = $this->normalize_asin_list($asin_list,true)) {
              update_post_meta($id,SGW_POST_META_KEY,$test);
							$message = "Your updates have been saved.";
            } else {
              $this->print_process_errors();
              return false;
            }
          }
        }

      }
      return $message;
    }
  }

  // Purpose - fetch all of the asins requested by $list
  // append the fetched data to the passed-in $meta list and return
  public function fetch_asin_meta_data($list,$meta) {
    global $SGW_API;
    $this->logger->debug("Admin#fetch_asin_meta_data()");
    $this->logger->debug(sprintf("\t\$meta IN (%s) = %s",count($meta), print_r($meta,1)));
    $data = $SGW_API->fetch_asins($list);
    if ($data) {
      $meta = array_merge($meta,$data);
    }
    $this->logger->debug(sprintf("\t\$data (%s) = %s",count($data),print_r($data,1)));
    $this->logger->debug(sprintf("\t\$meta OUT (%s) = %s",count($meta), print_r($meta,1)));
    return $meta;
  }

  // normalize the keys in the meta hash
  // need to strip off the prefix `ASIN_`
  public function normalize_meta_keys($hash){
    $this->logger->debug("Admin#normalize_meta_keys()");
    $keys = array_keys($hash);
    $set = str_replace("ASIN_","",$keys);
    $this->logger->debug(sprintf("\t\$keys %s",print_r($keys,1)));
    $this->logger->debug(sprintf("\t\$set %s",print_r($set,1)));
    return $set;
  }

  // Tests for the difference between the array of asins, and the asin meta hash
  // Returns a hash of meta data suitable for saving to disk, as it includes all asins in $list
  public function ensure_meta_for_asins($list,$hash){
    $this->logger->debug("ADMIN#ensure_meta_for_asins()");

    $asins = array_filter(array_unique(explode(',',$list)));
    $meta = $this->normalize_meta_keys($hash);
    $diff = array_diff($asins,$meta);

    $this->logger->debug(sprintf("\t\$asins (%s) = %s",count($asins),print_r($asins,1)));
    $this->logger->debug(sprintf("\t\$meta (%s) = %s",count($meta),print_r($meta,1)));
    $this->logger->debug(sprintf("\t\$diff (%s) = %s",count($diff),print_r($diff,1)));

    if (count($diff) > 0) {
      $this->logger->debug("\tfetching missing ASINS!");
      // Only need to fetch the diff
      // but append the meta data fetched into what we already have
      $newlist = join(',',$diff);
      $this->logger->debug(sprintf("\tfetching \$newlist from API : %s",print_r($newlist,1)));
      $fetched = $this->fetch_asin_meta_data($newlist,$hash);
      return $fetched;
    }
    return $hash;
  }

  private function update_default_asins($defaults){
    // update the default asins, if present
    if ($test = $this->normalize_asin_list($defaults)) {
      $this->options['default']       = $test;
      $this->options['default_meta']  = $this->ensure_meta_for_asins($test,$this->options['default_meta']);
    } else {
      $this->options['default']       = $this->initialize_default_asins();
      $this->options['default_meta']  = $this->initialize_default_asin_meta();
    }
    update_option(SGW_PLUGIN_OPTTIONS,$this->options);
    return true;
  }

	/* Contextual Help for the Plugin Configuration Screen */
  public function configuration_screen_help($contextual_help, $screen_id, $screen) {
    if ($screen_id == $this->help) {
      $contextual_help = <<<EOF
<h2>Overview</h2>
<p>
You can sell any kind of Amazon product using this plugin.
To begin, you must first find the ASIN of the product(s) you want to sell.
</p>
<p>See <a href="https://www.amazon.com/gp/help/customer/display.html?nodeId=200202190#find_asins" target='_blank'>How to Find Amazon ASINs</a> for more information.  You can input more than one ASIN - just seperate multiple values with a comma.
</p>

<h2>Settings</h2>
<p>
  Select the approprite affiliate country and provide your Amazon Affiliate ID for that country.
  You can also provide a comma-separated list of ASINs for the products you want to display <i>by default</i> if a more specific list for an individual POST is not configured.
  To get you started, we have pre-populated this field with some of the best-selling books currently on Amazon.
</p>

<h2>POST-specific ASINs</h2>
<p>
  Select a POST from the drop-down list and provide a comma-separated list of ASINs in the input field.
<br/>
  You can also edit the POST directly, adding the custom field <code>$this->post_meta_key</code> to the POST.
</p>

EOF;
    }
  	return $contextual_help;
  }

  public function content() {
    $html = '';
    if (is_user_logged_in() && is_admin() ){

//
// <a target=_new href='{SGW_BASE_URL}images/flow.png' title='Click to see larger image'>
//   <img src='{SGW_BASE_URL}images/flow_thumb.png'>
// </a>

      $message = $this->update_options($_POST);
      $opts = get_option(SGW_PLUGIN_OPTTIONS);
      $posts = $this->get_post_meta();
      $existing = array();

      // TODO: this should return - not print!
      if ($message) {
        printf('<div id="message" class="updated fade"><p>%s</p></div>',$message);
      } elseif ($this->error) { // reload the form post in this form
        // set the defaults
        $opts['default'] =  $_POST['sgw_opt']['default'];
        // restructure the posts hash
        foreach ($posts as $x=>$hash) {
          $id = $hash['ID'];
          if (isset($_POST['sgw_opt']['posts'][$id])) {
            $hash['meta_value'] = $_POST['sgw_opt']['posts'][$id];
            $posts[$x] = $hash;
          }
        }
      }
      if ($opts['default'] && !$opts['affiliate_id']) {
        // $this->missing_affiliate_id();
      }
    	if (!$opts['default']) {
    		$opts['default'] = $this->initialize_default_asins();
    	}
      $countries = $this->supported_countries();
      $select = '';
      foreach ($countries as $key=>$val) {
        $sel = '';
        if ($opts['country_id']==$key) { $sel = 'selected="selected"'; }
        $select .= sprintf("<option value='%s' %s>%s</option>",$key,$sel,$val);
      }
      $post_asins = '';
      if ($posts) {
        $post_asins .= '<ul>';
        foreach ($posts as $id=>$hash) {
          $existing[] = $hash['ID'];
          $post_asins .= sprintf('<li><label class="sgw_label" for="sgw_posts_%s">%s</label><input type="text" name="sgw_opt[posts][%s]" id="sgw_posts_%s" class="sgw_input"  value="%s"/></li>',
            $hash['ID'],$this->truncate_string($hash['post_title']),$hash['ID'],$hash['ID'],$hash['meta_value']);
        }
        $post_asins .= '</ul>';
      }
      $post_errors = '';
      if ($this->error && @$_POST['sgw_opt']['new']) {
        $post_errors .= '<ul>';
        foreach ($_POST['sgw_opt']['new'] as $id=>$hash) {
          if (!in_array($id,$existing)) { // this prevents successful saves from being re-listed
            $existing[] = $id;
            $post_errors .= sprintf('<br><label class="sgw_label" for="sgw_new[%s]">%s</label><input type="text" name="sgw_opt[new][%s][asin]" id="sgw_new_%s" class="sgw_input" value="%s"/><input type="hidden" name="sgw_opt[new][%s][title]" value="%s"/>',$id,$this->truncate_string($hash['post_title']),$id,$id,$hash['asin'],$id,$hash['title']);
          }
        }
        $post_errors .= '</ul>';
      }
      $new_posts = '';
      $post_list = get_posts(array('numberposts' => -1,'orderby' => 'title', 'order' => 'ASC' ));
      foreach($post_list as $post) {
        if (!in_array($post->ID,$existing)) {
          $new_posts .= sprintf('<option value="%s">%s</option>',$post->ID,$post->post_title);
        }
      }

      $nonce = wp_nonce_field(SGW_ADMIN_PAGE_NONCE);
      $html =<<< EOF
      <form method="post" action="admin.php?page={$this->nav_slug}">
        {$nonce}
  			<p>Add the widget to your side-bar and configure which products you want to sell using the form below.</p>
        <p>Ensure your Affiliate ID is accurate. A default ID may be displayed below so that the plugin works while you are testing.</p>
        <ul>
          <li>
            <label class='sgw_label' for='amznbs_country_id'>Affiliate Country</label>
            <select name="sgw_opt[country_id]" id="amznbs_country_id" class='sgw_input'>
              {$select}
            </select>
            <a id='sgw_domain' class='sgw_domain' href='#' title='Signup for an Amazon Affiliate account' target='_blank'>
              <span class="dashicons dashicons-external"></span>
            </a>
          </li>
          <li>
            <label class='sgw_label' for='sgw_affiliate_id'>Affiliate ID</label>
            <input type="text" name="sgw_opt[affiliate_id]" id="sgw_affiliate_id" class='sgw_input' value="{$opts['affiliate_id']}" />
          </li>
          <li>
            <label class='sgw_label' for='sgw_default_asins'>Default ASINs:</label>
            <input type="text" name="sgw_opt[default]" id="sgw_default" class='sgw_input' value="{$opts['default']}" />
            <input type="hidden" name="save_settings" value="1" />
          </li>
        </ul>
        <p>
          If you want specific products to display on individual pages, add those product ASINs here.
        </p>
        <p>
          Select the POST from the drop-down list below then input the desired ASINs as a
          comma-separated list.  You can add as many or as few as you like.
        </p>
        <p>
          You can also set the ASINs in the Post Edit page by using the custom field
          <code>{$this->post_meta_key}</code>.
        </p>
        {$post_asins}
        {$post_errors}
        <!-- placeholder for where new entries are put -->
        <div id="newly_added_post_asins"></div>
        <ul>
          <li>
            <label class='sgw_label add_new' for='sgw_add_new'>Add New Post ASINs</label>
            <select name="sgw_opt[list_all]" id="sgw_add_new" class='sgw_input' onchange='sgw.append_asin_block(this.value);'/>
              <option value='0' selected='selected'>-- Select --</option>
              {$new_posts}
            </select>
          </li>
        </ul>
        <input type="submit" class="button-primary" name="save_button" value="Update Settings" />
  	  </form>
EOF;
    }
    return $html;
  }
  private function missing_affiliate_id() {
?>
    <div id="affiliate_id_message" class="update-nag">
      <p>Though this plugin will work without one, until you input your Affiliate ID you will not get credit for sales made from the widget.</p>
    </div>
<?php
  }
  // Get the default asins in a comma-separated list
  private function initialize_default_asins() {
    $hash = $this->initialize_default_asin_meta();
    // TODO: consolidate calls to normalize_meta_keys()
    $prep = $this->normalize_meta_keys($hash);
    $list = join(',',$prep);
    $this->logger->debug(sprintf("\tinitialize_default_asins()\t\$list = %s",$list));
    return $list;
  }
  // Get the default asin meta data in a hash, with asin as a string-based key
  private function initialize_default_asin_meta() {
    $hash = array(
      'ASIN_1455570249' => array('title'=> 'Make Your Bed','image' => 'https://m.media-amazon.com/images/I/41nYEMfvoEL.jpg'),
      'ASIN_144947425X' => array('title'=> 'Milk and Honey','image' => 'https://m.media-amazon.com/images/I/41rrZplMctL.jpg'),
      'ASIN_1501164589' => array('title'=> 'Unshakeable: Your Financial Freedom Playbook','image' => 'https://m.media-amazon.com/images/I/51yjcDMAjEL.jpg')
    );
    return $hash;
  }

}
?>
