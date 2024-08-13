<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_filter('woocommerce_payment_gateways', 'marinsberg_credit_card_payment');
function marinsberg_credit_card_payment($methods)
{
    $methods[] = 'WC_Marinsberg_Credit_Card';
    return $methods;
}

add_action('plugins_loaded', 'init_marinsberg_credit_card_class');
function init_marinsberg_credit_card_class()
{

    class WC_Marinsberg_Credit_Card extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = 'marinsberg_credit_card';
            $this->icon = '';
            $this->has_fields = true;
            $this->method_title = 'Cartão de Crédito';
            $this->method_description = 'Aceite pagamentos via cartão de crédito com a Marins Berg.';

            $this->supports = array(
                'products'
            );

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Habilitar/Desabilitar', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Habilitar Pagamento por Cartão de Crédito', 'woocommerce'),
                    'default' => 'no'
                ),
                'title' => array(
                    'title'       => 'Título',
                    'type'        => 'text',
                    'description' => 'Define o título visível no checkout.',
                    'default'     => 'Cartão de Crédito',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Descrição',
                    'type'        => 'textarea',
                    'description' => 'Define a descrição visível no checkout.',
                    'default'     => 'Aceite pagamentos via cartão de crédito com a Marins Berg.',
                ),
            );
        }

        public function payment_fields()
        {
            echo '<div class="marinsberg-credit-card-form">';

            echo '<p class="form-row form-row-wide">';
            echo '<label for="marinsberg-card-holder-name">' . __('Nome do Titular', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input id="marinsberg-card-holder-name" class="input-text wc-credit-card-form-card-holder-name" type="text" autocomplete="off" placeholder="' . __('Nome conforme exibido no cartão', 'woocommerce') . '" name="marinsberg-card-holder-name" />';
            echo '</p>';
            
            echo '<p class="form-row form-row-wide">';
            echo '<label for="marinsberg-card-number">' . __('Número do Cartão', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input id="marinsberg-card-number" class="input-text wc-credit-card-form-card-number" type="text" autocomplete="off" placeholder="' . __('Número do cartão', 'woocommerce') . '" name="marinsberg-card-number" />';
            echo '</p>';
    
            echo '<p class="form-row form-row-first">';
            echo '<label for="marinsberg-card-expiry">' . __('Expiração (MM/AA)', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input id="marinsberg-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . __('MM/AA', 'woocommerce') . '" name="marinsberg-card-expiry" />';
            echo '</p>';
    
            echo '<p class="form-row form-row-last">';
            echo '<label for="marinsberg-card-cvc">' . __('Código de Segurança', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input id="marinsberg-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . __('CVC', 'woocommerce') . '" name="marinsberg-card-cvc" />';
            echo '</p>';
    
            echo '<div class="clear"></div>';
            echo '</div>';
        }

        public function validate_fields()
        {
            if (empty($_POST['marinsberg-card-holder-name']) || empty($_POST['marinsberg-card-number']) || empty($_POST['marinsberg-card-expiry']) || empty($_POST['marinsberg-card-cvc'])) {
                wc_add_notice(__('Todos os campos do cartão de crédito são obrigatórios.', 'woocommerce'), 'error');
            }
        }    

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
    
            $order->payment_complete();
            $order->reduce_order_stock();
            $order->add_order_note(__('Pagamento aprovado automaticamente.', 'woocommerce'));
    
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
    }

}

