<?php

// Construtor da página de configurações do token
function marinsberg_token_settings_page() {
?>

    <div class="wrap">
        <h1>Marins Berg Pagamentos</h1>
        <?php
        if (isset($_GET['settings-updated'])) {
            if ($_GET['settings-updated'] === 'success') {
                echo '<div class="notice notice-success"><p>Token salvo com sucesso.</p></div>';
            } elseif ($_GET['settings-updated'] === 'token_error') {
                echo '<div class="notice notice-error"><p>O token inserido é inválido. Por favor, verifique e tente novamente.</p></div>';
            }
        }
        ?>

        <!-- Token Section -->
        <section>
            <h2>Configurações de Chaves</h2>
            <p>Insira sua chave pública e chave privada para utilizar as formas de pagamento da Marins Berg.</p>
            <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">
                <?php
                settings_fields('marinsberg-settings');
                do_settings_sections('marinsberg-settings');
                ?>
                <label for="marinsberg_public_key">Chave Pública:</label>
                <input type="text" id="marinsberg_public_key" name="marinsberg_public_key" value="<?php echo esc_attr(get_option('marinsberg_public_key')); ?>" maxlength="300" size="56" placeholder="244 caracteres" required>
                <label for="marinsberg_private_key">Chave Privada:</label>
                <input type="text" id="marinsberg_private_key" name="marinsberg_private_key" value="<?php echo esc_attr(get_option('marinsberg_private_key')); ?>" maxlength="300" size="56" placeholder="244 caracteres" required>
                <p class="description">Veja suas chaves acessando o painel de controle em <a href="https://painel.marinsberg.com/" target="_blank">https://painel.marinsberg.com/</a></p>
                <?php submit_button('Salvar'); ?>
            </form>
        </section>


    </div>
    <?php
}

// Salvar o token no banco de dados
function marinsberg_save_token() {
    if (isset($_POST['marinsberg_public_key'], $_POST['marinsberg_private_key'])) {
        // Implementar a validação das chaves aqui
        $public_key = sanitize_text_field($_POST['marinsberg_public_key']);
        $private_key = sanitize_text_field($_POST['marinsberg_private_key']);

        // Gerar o token base64
        $token_base64 = base64_encode($public_key . ':' . $private_key);

        $headers = array(
            'Authorization: Basic ' . $token_base64,
            'Content-Type: application/json'
        );

        // Dados para a requisição POST
        $data = array(
            "amount" => 100,
            "paymentMethod" => "pix",
            "pix" => array(
                "description" => "venda pix"
            ),
            "postbackUrl" => home_url('/wc-api/marinsberg_payment_confirmation')
        );
        $data_json = json_encode($data);

        // Inicialize a sessão cURL
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

        // Execute a sessão cURL e obtenha a resposta
        $response = curl_exec($ch);

        // Obtenha o código de status HTTP da resposta
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Feche a sessão cURL
        curl_close($ch);

        // Verifique o código de status HTTP
        if ($httpcode >= 200 && $httpcode < 300) {
            // O token é válido, salva as chaves no banco de dados
            update_option('marinsberg_public_key', $public_key);
            update_option('marinsberg_private_key', $private_key);
            update_option('marinsberg_token', $token_base64);

            wp_redirect(admin_url('admin.php?page=marinsberg-settings&settings-updated=success'));
            exit;
        } else {
            wp_redirect(admin_url('admin.php?page=marinsberg-settings&settings-updated=token_error'));
            exit;
        }
    }
}
add_action('admin_init', 'marinsberg_save_token');

?>
