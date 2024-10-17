<?php
/**
 * Plugin Name: WhoisFinder
 * Description: Exibe informações detalhadas sobre o IP do usuário e permite a busca de informações Whois de domínios.
 * Version: 1.0
 * Author: Gabriel Vendramim
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

require_once plugin_dir_path( __FILE__ ) . 'includes/whoisfinder-functions.php';