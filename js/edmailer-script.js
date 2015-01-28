 /**
 * jQuery jPages Custom Script v0.1
 * Client side pagination with jQuery
 *
 *
 */
   jQuery(document).ready(function() {
    

    /**
     * Custom javascript for any div pagination
     *
     *
     */
    jQuery("div.edd_holder").jPages({
            containerID : "edd_container",
            perPage : 12
    });


    jQuery("div.edd_holder").jPages({
    containerID : "edd_promo_container",
    perPage : 12
    });

});


  


