<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

function wf_enqueue_scripts() {
    wp_enqueue_style( 'wf-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' );
    wp_enqueue_style( 'wf-styles', plugin_dir_url( __FILE__ ) . '../css/wf-styles.css' );
    wp_enqueue_script( 'wf-scripts', plugin_dir_url( __FILE__ ) . '../js/wf-scripts.js', array('jquery'), null, true );

    wp_localize_script( 'wf-scripts', 'wf_ajax_object', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'wf_ajax_nonce' )
    ));
}
add_action( 'wp_enqueue_scripts', 'wf_enqueue_scripts' );

/**
 * Cria o shortcode do plugin.
 */
function wf_shortcode() {
    ob_start();
    ?>
    <div class="wf-container">
        <h1>Seu Endereço IP é:</h1>
        <p class="wf-ip" id="wf-user-ip">Carregando...</p>
        <div class="wf-info" id="wf-ip-info"></div>

        <h2 class="wf-section-title">Buscar Informações Whois</h2>
        <div class="wf-whois-form">
            <input type="text" id="wf-domain-input" placeholder="Digite o domínio (ex: exemplo.com)" />
            <button id="wf-buscar-btn">Buscar</button>
        </div>
        <div class="wf-whois-info" id="wf-whois-info"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'whoisfinder', 'wf_shortcode' );

/**
 * Obtém detalhes do IP do usuário.
 */
function wf_get_client_ip() {
    $headers = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            return sanitize_text_field($_SERVER[$header]);
        }
    }
    return 'IP não detectado';
}

function wf_fetch_ip_details() {
    check_ajax_referer( 'wf_ajax_nonce', 'nonce' );

    $ip_address = wf_get_client_ip();

    $response = wp_remote_get( 'https://ipwhois.app/json/' . $ip_address, array( 'timeout' => 10 ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Erro ao obter informações do IP.' );
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( empty( $data ) || ( isset( $data['success'] ) && $data['success'] === false ) ) {
        wp_send_json_error( 'Falha na API de geolocalização.' );
    }

    wp_send_json_success( $data );
}
add_action( 'wp_ajax_wf_fetch_ip_details', 'wf_fetch_ip_details' );
add_action( 'wp_ajax_nopriv_wf_fetch_ip_details', 'wf_fetch_ip_details' );

/**
 * Obtém informações Whois de um domínio.
 */
function wf_fetch_whois_info() {
    check_ajax_referer( 'wf_ajax_nonce', 'nonce' );

    if ( empty( $_POST['domain'] ) ) {
        wp_send_json_error( 'Domínio não fornecido.' );
    }

    $domain = sanitize_text_field( $_POST['domain'] );

    // Valida se o domínio tem um formato correto
    if (!preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+(\.[a-zA-Z]{2,})+)$/', $domain)) {
        wp_send_json_error( 'Domínio inválido.' );
    }

    $whois_api_key = 'SUA_CHAVE_DE_API_AQUI';
    
    $response = wp_remote_get( "https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey={$whois_api_key}&domainName={$domain}&outputFormat=JSON", 
        array( 'timeout' => 10 ) 
    );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Erro ao obter informações Whois.' );
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( empty( $data['WhoisRecord'] ) ) {
        wp_send_json_error( 'Informações Whois não disponíveis ou domínio inválido.' );
    }

    wp_send_json_success( $data['WhoisRecord'] );
}
add_action( 'wp_ajax_wf_fetch_whois_info', 'wf_fetch_whois_info' );
add_action( 'wp_ajax_nopriv_wf_fetch_whois_info', 'wf_fetch_whois_info' );
