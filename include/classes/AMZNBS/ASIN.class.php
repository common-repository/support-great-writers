<?php
namespace AMZNBS;

// ASIN API Wrapper
//
// This class enables fetching ASIN info from the API when not cached locally

if (!class_exists("\HeyPublisher\Base\API")) {
  require_once(SGW_PLUGIN_FULLPATH . '/include/classes/HeyPublisher/Base/API.class.php');
}

class ASIN {
  var $api = null;
  var $logger = null;

  public function __construct() {
    // don't extend the API class to prevent from getting instantiated multiple times
    global $HEYPUB_API, $HEYPUB_LOGGER;
    $this->api = $HEYPUB_API;
    $this->logger = $HEYPUB_LOGGER;
    $this->logger->debug("ASIN#__construct()");
  }

  // Fetch 'n' number of asins from comma-separated list
  public function fetch_asins($list) {
    $data = array();
    $path = sprintf('asins/%s',$list);
    $result = $this->api->get($path);
    if ($result && key_exists('object',$result) && $result['object'] == 'list' ) {
      foreach ($result['data'] as $hash) {
        // TODO: Normalize the transposition of the ASIN
        $asin = sprintf("ASIN_%s",$hash['id']);
        $data[$asin] = array(
          'title' => $hash['title'],
          'image' => $hash['image'],
        );
      }
      return $data;
    }
    return;
  }

}

// This class sets a global accessor
if (!isset($SGW_API)) {
  $SGW_API  = new \AMZNBS\ASIN();
}
?>
