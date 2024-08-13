# Marins Berg Payments Gateway for WooCommerce
 
**WooCommerce Requires at least:** 5.5.2  
**WooCommerce Tested up to:** 8.1.0  
**Requires PHP:** 7.4  
**Stable tag:** 1.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

## Descrição

O plugin **Marins Berg Payments Gateway for WooCommerce** adiciona dois métodos de pagamento ao WooCommerce: **PIX** e **Cartão de Crédito**, utilizando a API da MarinsBerg Payments. É uma solução simples e eficaz para integrar o sistema de pagamento da MarinsBerg ao seu e-commerce.

## Instalação

### Pré-requisitos

Antes de instalar o plugin, certifique-se de que você possui:

- Uma conta cadastrada na [MarinsBerg Payments](https://painel.marinsberg.com/).
- A versão do WooCommerce 5.5.2 ou superior.
- Para utilizar o método de pagamento PIX, é recomendado o plugin [Brazilian Market on WooCommerce](https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).
- PHP 7.4 ou superior.

### Passo a Passo

1. Faça o download do plugin e adicione-o à sua instalação do WordPress.
2. Ative o plugin através do menu `Plugins` no WordPress.
3. Acesse o painel administrativo do WordPress, vá até **WooCommerce > MarinsBerg**.
4. Cadastre as chaves **pública** e **privada** obtidas no painel da MarinsBerg Payments.
5. Vá em **WooCommerce > Configurações > Pagamentos**.
6. Habilite e configure os métodos de pagamento **PIX** e **Cartão de Crédito** conforme a necessidade da sua loja.

## Configuração

### 1. Obtenção das Chaves API

- Acesse o [painel da MarinsBerg](https://painel.marinsberg.com/) e faça login.
- No menu de navegação, localize a seção de **API Keys**.
- Gere e copie a **chave pública** e a **chave privada**.

### 2. Configurando no WordPress

- No painel administrativo do WordPress, navegue até **WooCommerce > MarinsBerg**.
- Cole a **chave pública** e a **chave privada** nos campos correspondentes.
- Salve as alterações.

### 3. Habilitando os Métodos de Pagamento

- Acesse **WooCommerce > Configurações > Pagamentos**.
- Você verá os métodos de pagamento **PIX** e **Cartão de Crédito** fornecidos pela MarinsBerg.
- Configure as opções de cada método conforme as necessidades da sua loja.
- Habilite os métodos e salve as configurações.

## Suporte

Para dúvidas e suporte, acesse o repositório oficial do plugin no [GitHub](https://github.com/luizreimann/wc-marinsberg-payments/) ou entre em contato através do [site do autor](https://luizreimann.dev/).

## Contribuição

Se você deseja contribuir com o desenvolvimento deste plugin, sinta-se à vontade para abrir issues ou pull requests no [repositório oficial no GitHub](https://github.com/luizreimann/wc-marinsberg-payments/).

## Changelog

### 1.0
- Lançamento inicial do plugin.
- Adiciona suporte para pagamento via **PIX** e **Cartão de Crédito** utilizando a API da MarinsBerg Payments.

## Licença

Este plugin é distribuído sob a licença [GPLv2 ou posterior](https://www.gnu.org/licenses/gpl-2.0.html).
