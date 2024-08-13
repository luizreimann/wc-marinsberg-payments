<?php
/**
 * Plugin Name: Marins Berg Payments Gateway for WooCommerce
 * Description: Adiciona formas de pagamento da Marins Berg ao WooCommerce.
 * Plugin URI: https://github.com/luizreimann/wc-marinsberg-payments/
 * Version: 1.0
 * Author: Luiz Reimann
 * Author URI: https://luizreimann.dev/
 * Text Domain: woocommerce-marinsberg
 * WC requires at least: 5.5.2
 * WC tested up to: 8.1.0
 * Requires PHP: 7.4
*/


// Adiciona o submenu no menu WooCommerce
function marinsberg_add_submenu() {
    add_submenu_page(
        'woocommerce',
        'Marins Berg',
        'Marins Berg',
        'manage_options',
        'marinsberg-settings',
        'marinsberg_settings_page'
    );
}
add_action('admin_menu', 'marinsberg_add_submenu');


// Registrar a página do token (apenas na primeira visita)
function marinsberg_add_token_settings_page() {
    if (!get_option('marinsberg_token')) {
        add_options_page(
            'Marins Berg',
            'Marins Berg',
            'manage_options',
            'marinsberg-token-settings',
            'marinsberg_token_settings_page'
        );
    }
}
add_action('admin_menu', 'marinsberg_add_token_settings_page');


// Registra as keys como settings 
register_setting('marinsberg-settings', 'marinsberg_token');
register_setting('marinsberg-settings', 'marinsberg_public_key');
register_setting('marinsberg-settings', 'marinsberg_private_key');


// Adicionar compatibilidade com o 'High-Performance Order Storage'
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});


// Inclui todos os arquivos dentro das subpastas do diretório 'templates'
foreach (glob(plugin_dir_path(__FILE__) . 'templates/*/*.php') as $file) {
    include_once $file;
}
// Inclui todos os arquivos dentro das subpastas do diretório 'includes'
foreach (glob(plugin_dir_path(__FILE__) . 'includes/*/*.php') as $file) {
    include_once $file;
}


// Função para redirecionar após a ativação do plugin
function marinsberg_activation_redirect($plugin) {
    if (strpos($plugin, 'wc-marinsberg-payments/wc-marinsberg-payments.php') !== false) {
        if (get_option('marinsberg_token')) {
            exit(wp_redirect(admin_url('admin.php?page=marinsberg-settings')));
        } else {
            exit(wp_redirect(admin_url('options-general.php?page=marinsberg-token-settings')));
        }
    }
}
add_action('activated_plugin', 'marinsberg_activation_redirect');
