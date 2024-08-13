<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_filter('woocommerce_payment_gateways', 'marinsberg_pix_payment');
function marinsberg_pix_payment($methods)
{
    $methods[] = 'WC_Marinsberg_PIX';
    return $methods;
}

add_action('plugins_loaded', 'init_marinsberg_pix_class');
function init_marinsberg_pix_class()
{

    class WC_Marinsberg_PIX extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = 'marinsberg_pix';
            $this->icon = '';
            $this->has_fields = true;
            $this->method_title = 'PIX';
            $this->method_description = 'Aceite pagamentos via PIX através da Marins Berg.';

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
                    'label' => __('Habilitar Pagamento via PIX', 'woocommerce'),
                    'default' => 'no'
                ),
                'title' => array(
                    'title'       => 'Título',
                    'type'        => 'text',
                    'description' => 'Define o título visível no checkout.',
                    'default'     => 'Pague com PIX',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Descrição',
                    'type'        => 'textarea',
                    'description' => 'Define a descrição visível no checkout.',
                    'default'     => 'Aceite pagamentos via PIX com a Marins Berg.',
                ),
            );
        }

        public function payment_fields()
        {
            ?>
            <fieldset>
                <p class="form-row form-row-wide">
                    <img src="<?php echo plugin_dir_url(__FILE__) . '../../assets/img/pix-logo.svg'; ?>" alt="PIX Logo" width="100" style="float:left;margin-bottom:10px;">
                </p>
                <p class="form-row form-row-wide" style="font-weight: bold;">
                    Pague com PIX Copia e Cola
                </p>
                <p class="form-row form-row-wide">
                    Após confirmar a compra, vamos te mostrar o código e QR Code para pagamento.
                </p>
            </fieldset>
            <?php
        }

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            
            $marinsberg_token = get_option('marinsberg_token');

            if (!empty($marinsberg_token)) {
                $order = wc_get_order($order_id);
                $total_amount = $order->get_total();

                $total_amount = (float) $total_amount;

                $data = array(
                    "amount" => $total_amount,
                    "paymentMethod" => "pix",
                    "pix" => array(
                        "description" => "venda pix"
                    ),
                    "postbackUrl" => home_url('/wc-api/marinsberg_payment_confirmation')
                );

                $data_json = json_encode($data);


                $headers = array(
                    'Authorization: Basic ' . $marinsberg_token,
                    'Content-Type: application/json'
                );

                $ch = curl_init();

                curl_setopt_array($ch, array(
                    CURLOPT_URL => 'https://v-api.volutipay.com.br/v1/transactions',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data_json,
                    CURLOPT_HTTPHEADER => $headers,
                ));

                $response = curl_exec($ch);

                curl_close($ch);

                $response_data = json_decode($response, true);

                if (isset($response_data['qrCode'])) {
                    update_post_meta($order_id, '_transaction_id', $response_data['conciliationId']);
                    update_post_meta($order_id, '_pix_id', $response_data['qrCode']);
                    
                    $payment_instruction = __('Aguardando pagamento via PIX. Código PIX: ', 'textdomain') . $response_data['qrCode'];
                    
                    $order->update_status('pending', $payment_instruction);
                
                    WC()->cart->empty_cart();
                
                    return array(
                        'result' => 'success',
                        'redirect' => $this->get_return_url($order)
                    );
                } else {
                    wc_add_notice(json_encode($response), 'error');
                    return;
                }
            } else {
                wc_add_notice('Erro: Método de pagamento indisponível no momento. Tente novamente mais tarde.', 'error');
                return;
            }
        }

    }
}

add_action( 'woocommerce_order_details_after_order_table', 'marinsberg_pix_payment_instructions', 10, 1 );
function marinsberg_pix_payment_instructions( $order ) {
    $codigo_pix = get_post_meta( $order->get_id(), '_pix_id', true );

    if ( $codigo_pix ) {
        echo '<div class="marinsberg-pix-payment-instructions">';
        echo '<h2 class="marinsberg-pix-payment-h2">Instruções de pagamento via PIX</h2>';
        echo '<img class="marinsberg-pix-payment-qrcode" src="https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode( $codigo_pix ) . '&amp;size=200x200" alt="QR Code PIX">';
        echo '<p class="marinsberg-pix-payment-code">Código PIX: <span id="codigo-pix">' . $codigo_pix . '</span></p>';
        echo '<button id="botao-copiar" onclick="copiarCodigo()" class="marinsberg-pix-payment-button">Copiar Código</button>';
        echo '<p class="marinsberg-pix-payment-instructions">Faça o pagamento utilizando seu aplicativo bancário ou carteira digital, inserindo o código PIX fornecido acima.</p>';
        echo '</div>';
        $script = "
        <script>
        function copiarCodigo() {
            var codigoPix = document.getElementById('codigo-pix');
            var codigoTexto = codigoPix.textContent || codigoPix.innerText;
            var inputTemporario = document.createElement('input');
            inputTemporario.value = codigoTexto;
            document.body.appendChild(inputTemporario);
            inputTemporario.select();
            document.execCommand('copy');
            document.body.removeChild(inputTemporario);
            alert('Código copiado.');
        }
        </script>
        ";
        echo $script;
    }
}

add_action('woocommerce_api_marinsberg_payment_confirmation', 'marinsberg_payment_confirmation');
function marinsberg_payment_confirmation() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['conciliationId']) && isset($data['status']) && $data['status'] === 'approved') {
            $conciliation_id = $data['conciliationId'];
            
            $orders = wc_get_orders(array(
                'meta_key' => '_transaction_id',
                'meta_value' => $conciliation_id,
                'post_status' => array_keys(wc_get_order_statuses())
            ));
            
            if (!empty($orders)) {
                $order = reset($orders);
                $order_id = $order->get_id();
                
                $order = wc_get_order($order_id);
                $order->update_status('processing', __('Pagamento recebido via PIX. Conciliation ID: ' . $conciliation_id, 'textdomain'));
                
                WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order_id);
                
                wp_send_json_success('Pagamento confirmado com sucesso.');
            } else {
                wp_send_json_error('Não foi possível encontrar o pedido associado ao Conciliation ID fornecido.');
            }
        } else {
            wp_send_json_error('O pagamento não foi aprovado.');
        }
    } else {
        wp_send_json_error('Método de solicitação inválido.');
    }
}
