<?php
/**
 * Amazon Login - Login for WordPress
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
defined('ABSPATH') or die('Access denied');

add_action('wp_enqueue_scripts', 'loginwithamazon_enqueue_script');
add_action('login_enqueue_scripts', 'loginwithamazon_enqueue_script');
add_action('wp_footer', 'loginwithamazon_add_footer_script');
add_action('login_footer', 'loginwithamazon_add_footer_script');

function loginwithamazon_enqueue_script() {
    wp_enqueue_script('loginwithamazon', LOGINWITHAMAZON__PLUGIN_URL . 'add_login.js');
    wp_localize_script( 'loginwithamazon', 'lwaConfig', array(
        'popup' => is_ssl(),
        'csrf' => LoginWithAmazonUtility::hmac( LoginWithAmazonUtility::getCsrfAuthenticator() ),
        'client_id' => get_option('loginwithamazon_client_id'),
        'logout' => (isset($_GET['loggedout']) && $_GET['loggedout'] == 'true'),
        'next_url' => str_replace('http://', 'https://', site_url('wp-login.php')) . '?amazonLogin=1'
    ));
}

function loginwithamazon_add_footer_script() {
    ?>
    <div id="amazon-root"></div>
    <script type="text/javascript">

        window.onAmazonLoginReady = function() {
            amazon.Login.setClientId( lwaConfig.client_id );
            amazon.Login.setUseCookie(true);
            lwaConfig.logout && amazon.Login.logout();
        };
        (function(d) {
            var a = d.createElement('script'); a.type = 'text/javascript';
            a.async = true; a.id = 'amazon-login-sdk';
            a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
            d.getElementById('amazon-root').appendChild(a);
        })(document);

        function activateLoginWithAmazonButtons(elementId) {
            document.getElementById(elementId).onclick = function() {
                var options = {
                    scope: 'profile',
                    state: lwaConfig.csrf,
                    popup: lwaConfig.popup
                };
                amazon.Login.authorize(options, lwaConfig.next_url);

                return false;
            };
        }
    </script>

<?php
}
