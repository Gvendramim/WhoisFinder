<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

/**
 * Enfileira os scripts e estilos necessários.
 */
function wf_enqueue_scripts() {
    wp_enqueue_style( 'wf-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap', false );
    wp_enqueue_style( 'wf-styles', plugin_dir_url( __FILE__ ) . '../css/wf-styles.css' );
    wp_enqueue_script( 'wf-scripts', plugin_dir_url( __FILE__ ) . '../js/wf-scripts.js', array('jquery'), null, true );
    wp_localize_script( 'wf-scripts', 'wf_ajax_object', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'wf_ajax_nonce' )
    ));
}
add_action( 'wp_enqueue_scripts', 'wf_enqueue_scripts' );

/**
 * Cria o shortcode para exibir as informações.
 */
function wf_shortcode() {
    ob_start();
    ?>
    <div class="wf-container">
        <h1>Seu Endereço IP é:</h1>
        <p class="wf-ip" id="wf-user-ip">Carregando...</p>
        <div class="wf-info" id="wf-ip-info">
        </div>

        <h2 class="wf-section-title">Buscar Informações Whois</h2>
        <div class="wf-whois-form">
            <input type="text" id="wf-domain-input" placeholder="Digite o domínio (ex: exemplo.com)" />
            <button id="wf-buscar-btn">Buscar</button>
        </div>
        <div class="wf-whois-info" id="wf-whois-info">
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'whoisfinder', 'wf_shortcode' );

/**
 * Manipulador AJAX para buscar detalhes do IP.
 */
function wf_fetch_ip_details() {
    check_ajax_referer( 'wf_ajax_nonce', 'nonce' );

    // Obter o endereço IP do usuário
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Chamada para a API de geolocalização
    $response = wp_remote_get( 'https://ipwhois.app/json/' . $ip_address );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Erro ao obter informações do IP.' );
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( isset( $data['success'] ) && $data['success'] === false ) {
        wp_send_json_error( 'Falha na API de geolocalização.' );
    }

    wp_send_json_success( $data );
}
add_action( 'wp_ajax_wf_fetch_ip_details', 'wf_fetch_ip_details' );
add_action( 'wp_ajax_nopriv_wf_fetch_ip_details', 'wf_fetch_ip_details' );

/**
 * Manipulador AJAX para buscar informações Whois.
 */
function wf_fetch_whois_info() {
    check_ajax_referer( 'wf_ajax_nonce', 'nonce' );

    $domain = sanitize_text_field( $_POST['domain'] );

    if ( empty( $domain ) ) {
        wp_send_json_error( 'Domínio não fornecido.' );
    }

    // Substitua pela sua chave de API da WhoisXML API
    $whois_api_key = 'SUA_CHAVE_DE_API_AQUI';

    $response = wp_remote_get( 'https://www.whoisxmlapi.com/whoisserver/WhoisService', array(
        'body' => array(
            'apiKey'      => $whois_api_key,
            'domainName'  => $domain,
            'outputFormat'=> 'JSON'
        )
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Erro ao obter informações Whois.' );
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! isset( $data['WhoisRecord'] ) ) {
        wp_send_json_error( 'Informações Whois não disponíveis ou domínio inválido.' );
    }

    wp_send_json_success( $data['WhoisRecord'] );
}
add_action( 'wp_ajax_wf_fetch_whois_info', 'wf_fetch_whois_info' );
add_action( 'wp_ajax_nopriv_wf_fetch_whois_info', 'wf_fetch_whois_info' );
