<?php

class WCMp_Settings_WCMp_Addons {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp, $wp_version;
        $args = array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
            'body' => null,
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'filename' => null
        );
        $url = 'https://wc-marketplace.com/wp-json/wc/v2/products/?per_page=100&orderby=title&order=asc&status=publish';
        $response = wp_remote_get($url, $args);
        ?>
        <div class="wcmp-addon-container">
            <div class="addon-banner">
                <img src="<?php echo $WCMp->plugin_url.'assets/images/addon-banner.png' ?>" />
                <div class="addon-banner-content">
                    <h1>WCMp Bundled Addons is available</h1>
                    <p>Give more power to your vendors to manage their shop, allow them to track their sales and control your marketplace with additional authority- unwrap powerful marketplace solution all in one bundle.</p>
                    <a href="https://wc-marketplace.com/wcmp-bundle/" target="_blank" class="">Grab It Now</a>
                </div>
            </div>
            <div class="addonbox-container">
                <?php
                if (!is_wp_error($response) && isset($response['body'])) {
                    foreach (json_decode($response['body']) as $product) {
                        if (isset($product->id) && $product->id != 12603) {
                            ?>
                            <div class="addonbox">
                                <h2><?php echo $product->name; ?></h2> 
                                <div class="addon-img-holder">
                                    <?php
                                        $all_meta_data = wp_list_pluck($product->meta_data, 'value' ,'key'); 
                                        if( ! empty( $all_meta_data['extension_img_path'] ) ) {
                                    ?>
                                    <img src="<?php echo $all_meta_data['extension_img_path']; ?>" alt="wcmp" />    
                                    <?php
                                        } else {
                                    ?>

                                    <img src="<?php echo $product->images[0]->src; ?>" alt="wcmp" />

                                    <?php
                                        }
                                    ?>  
                                </div>   
                                <div class="addon-content-holder">
                                    <p><?php echo wp_trim_words(strip_tags($product->short_description), 25, '...'); ?></p> 
                                    <a href="<?php echo $product->permalink; ?>" target="_blank" class="button">View More</a>  
                                </div> 
                            </div>
                            <?php
                        }
                    }
                } else{
                    ?>
                    <div class="offline-addon-wrap">
                        <div class="addon-content">
                            <h2>Create the best Marketplace with our coolest add-ons!</h2>
                            <p>Rise above your peers and grab the attention of all your vendors and customers. WCMp Extensions eases the way you do business!</p>
                        </div>
                        <a href="https://wc-marketplace.com/addons/" target="_blank">Get Addons!</a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

}
