(function( sgw, $, undefined ) {
  /*
  ---------------------------------------------
      PRIVATE PROPERTIES
  ---------------------------------------------
  */

  var WIDGET_CNT = 0;
  var WIDGET_SET = {};
  var AFFILIATES = {
    'com':    {domain: 'https://affiliate-program.amazon.com', id: 'pif-richard-20'},
    'co.uk':  {domain: 'https://affiliate-program.amazon.co.uk', id: 'sgw0a-21'},
    'de':     {domain: 'https://partnernet.amazon.de', id: 'sgw05-21'},
    'fr':     {domain: 'https://partenaires.amazon.fr', id: 'sgw08-21'},
    'it':     {domain: 'https://programma-affiliazione.amazon.it', id: 'sgw0c-21'},
    'es':     {domain: 'https://afiliados.amazon.es', id: 'sgw0f-21'},
    'ca':     {domain: 'https://associates.amazon.ca', id: 'sgw0a-20'},
    'br':     {domain: 'https://associados.amazon.com.br/', id: 'sgw0b-20'}
  };

  function getAffiliateId(region) {
    return AFFILIATES[region].id || AFFILIATES['com'].id;
  };

  function getAffiliateDomain(region) {
    return AFFILIATES[region].domain || AFFILIATES['com'].domain;
  };

  function updateAffiliateDomain() {
    var $sel = $('#amznbs_country_id').val();
    $('#sgw_domain').attr('href',getAffiliateDomain($sel));
  };
  /*
  ---------------------------------------------
      PUBLIC FUNCTIONS
  ---------------------------------------------
  */
  sgw.bind = function() {
    var $val = $(this).val();
    var $id = getAffiliateId($val);
    $('#sgw_affiliate_id').val($id);
    updateAffiliateDomain();
  };

  sgw.init = function() {
    $('#amznbs_country_id').change(sgw.bind);
    // ensure all fields are set properly
    updateAffiliateDomain();
  };

  /*
  ---------------------------------------------
      PUBLIC FUNCTIONS
  ---------------------------------------------
  */
  sgw.append_asin_block = function(val) {
    WIDGET_CNT++;  // this will increment each time this function is called, allowing us to create as many as we want
    if (val > 0) {
      var text = $('#sgw_add_new option[value="'+val+'"]').text();
      if (text.length > 40) {
        text = text.substring(0,40) + ' ...';
      }
      if (!WIDGET_SET[val]) {
        WIDGET_SET[val] = true;
        // append the content to the div
        var block = $('<br/><label class="sgw_label add_new" for="sgw_new['+val+']">'+ text +'</label><input type="text" name="sgw_opt[new]['+val+'][asin]" id="sgw_new_'+val+'" class="sgw_input" /><input type="hidden" name="sgw_opt[new]['+val+'][title]" value="'+text+'"/>');
        $('#newly_added_post_asins').append(block);

      } else {
        var txt = "#sgw_new_"+val;
        if (!$(txt).value) {
          $(txt).value = 'update me'
        }
        $(txt).select();
      }
      $('sgw_add_new').value = 0;
    }
    return false;
  };

}( window.sgw = window.sgw || {}, jQuery ));
jQuery( document ).ready( function( $ ) {
  sgw.init();
});
