<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * @link              https://github.com/aFarkas/wp-lazysizes
 * @since             2.0.0
 * @package           https://github.com/aFarkas/wp-lazysizes
 *
 * @wordpress-plugin
 * Plugin Name:       wp-lazysizes
 * Plugin URI:        https://github.com/aFarkas/wp-lazysizes
 * Description:       Lazyload responsive images with automatic sizes calculation
 * Version:           0.9.0
 * Author:            Alexander Farkas
 * Author URI:        https://github.com/aFarkas/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
if ( ! class_exists( 'LazySizes' ) ) :

    $lazySizesDefaults = array(
        'expand' => 80,
        'optimumx' => 'false',
        'intrinsicRatio' => 'false'
    );
    require_once( plugin_dir_path( __FILE__ ) . 'settings.php' );



    class LazySizes {

        const version = '0.9.0';
        private static $options = array();

        static function init() {
            if ( !is_admin() ) {

                self::getOptions();

                add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_styles' ), 9 );
                add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ), 99 );
                add_filter( 'the_content', array( __CLASS__, '_filter_images' ), 99 ); // run this later, so other content filters have run, including image_add_wh on WP.com
                add_filter( 'post_thumbnail_html', array( __CLASS__, '_filter_images' ), 99 );
                add_filter( 'get_avatar', function($content){
                    return self::_filter_images($content, 'noratio');
                }, 99 );
            }
        }

        static  function getOptions() {
            global $lazySizesDefaults;
            self::$options = wp_parse_args( get_option( 'lazysizes_settings', $lazySizesDefaults), $lazySizesDefaults );


            if(is_numeric(self::$options['expand'])){
                self::$options['expand'] = (float)self::$options['expand'];
            } else {
                self::$options['expand'] = $lazySizesDefaults['expand'];
            }


        }

        static function add_styles() {
            wp_enqueue_style( 'lazysizes', self::get_url( 'css/lazysizes.css', __FILE__ ), array(), self::version );
        }

        static function add_scripts() {
            LazySizes_writeCfg(self::$options['expand']);

            wp_enqueue_script( 'lazysizes', self::get_url( 'js/lazysizes/lazysizes.min.js', __FILE__ ), array(), self::version, false );
            if(self::$options['optimumx'] != 'false'){
                wp_enqueue_script( 'lazysizesoptimumx', self::get_url( 'js/lazysizes/plugins/optimumx/ls.optimumx.min.js', __FILE__ ), array(), self::version, false );
            }
        }

        static function _filter_images( $content, $type = 'ratio' ) {

            if( is_feed() ) {
                return $content;
            }

            $ratioBox = false;

            $respReplace = 'data-sizes="auto" data-srcset=';

            if(self::$options['optimumx'] != 'false'){
                $respReplace = 'data-optimumx="' . self::$options['optimumx'] . '" ' . $respReplace;
            }

            if($type == 'ratio' && self::$options['intrinsicRatio'] != 'false'){
                $ratioBox = '<span class="intrinsic-ratio-box';

                if(self::$options['intrinsicRatio'] == 'animated'){
                    $ratioBox .= ' lazyload" data-expand="-1';
                }

                $ratioBox .= '"><span class="intrinsic-ratio-helper" style="padding-bottom: ';
            }

            $matches = array();
            $skip_images_regex = '/class=".*lazyload.*"/';
            $placeholder_image = apply_filters( 'lazysizes_placeholder_image', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' );
            preg_match_all( '/<img\s+.*?>/', $content, $matches );

            $search = array();
            $replace = array();

            foreach ( $matches[0] as $imgHTML ) {

                // don't to the replacement if a skip class is provided and the image has the class
                if ( ! ( preg_match( $skip_images_regex, $imgHTML ) ) ) {

                    $replaceHTML = preg_replace( '/<img(.*?)src=/i', '<img$1src="' . $placeholder_image . '" data-src=', $imgHTML );

                    $replaceHTML = preg_replace( '/srcset=/i', $respReplace, $replaceHTML );

                    if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
                        $replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/i', 'class=$1lazyload $2$1', $replaceHTML );
                    } else {
                        $replaceHTML = preg_replace( '/<img/i', '<img class="lazyload"', $replaceHTML );
                    }

                    $replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';


                    if($ratioBox && preg_match('/width=["|\']*(\d+)["|\']*/', $imgHTML, $width) == 1 && preg_match('/height=["|\']*(\d+)["|\']*/', $imgHTML, $height) == 1){
                        $replaceHTML = $ratioBox . (($height[1] / $width[1]) * 100) .'%;"></span>'.$replaceHTML.'</span>';
                    }

                    array_push( $search, $imgHTML );
                    array_push( $replace, $replaceHTML );
                }
            }

            $content = str_replace( $search, $replace, $content );


            return $content;
        }

        static function get_url( $path = '' ) {
            return plugins_url( ltrim( $path, '/' ), __FILE__ );
        }
    }

    function LazySizes_add_placeholders( $content, $type = 'ratio' ) {
        return LazySizes::_filter_images( $content, $type );
    }

    function LazySizes_writeCfg( $expand ) {
?>
<script>
window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.expand = <?php echo $expand; ?>;
window.lazySizesConfig.addClasses = true;
</script>
<?php

    }

    LazySizes::init();




endif;
