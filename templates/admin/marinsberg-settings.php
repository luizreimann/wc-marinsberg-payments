<?php

// Construtor da página de configurações
function marinsberg_settings_page() {

    // Verifique se o 'marinsberg_token' está salvo no banco de dados
    if (!get_option('marinsberg_token')) {
        wp_redirect(admin_url('options-general.php?page=marinsberg-token-settings&settings-updated=token_error'));
        exit;
    }

    if (isset($_GET['settings-updated'])) {
        if ($_GET['settings-updated'] === 'success') {
            echo '<div class="notice notice-success"><p>Configurações salvas com sucesso.</p></div>';
        } elseif ($_GET['settings-updated'] === 'token_error') {
            echo '<div class="notice notice-error"><p>O token inserido é inválido. Por favor, verifique e tente novamente.</p></div>';
        }
    }
    ?>
    
    <div class="wrap">
        <h1>Configurações do Marins Berg Pagamentos</h1>
        <p>Esta é a página de configurações do gateway de pagamento da Marins Berg.</p>
        <p>Em caso de dúvidas, acesse nosso canal de ajuda em <a href="https://painel.marinsberg.com/" target="_blank">https://painel.marinsberg.com/</a></p>
    </div>

    <hr>

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

    <hr>

    <?php
}

?>
