<?php
/*
Plugin Name: ReverseProxy (Fork by Darkhorse)
Plugin URI: https://github.com/paramedicspecialist/yourls_reverseproxy_support/
Description: Fixes and anonymizes incoming IPs from reverse proxies. Original plugin by Diftraku: https://github.com/Diftraku/yourls_cloudflare/
Version: 1.0
Author: Darkhorse
Author URI: https://devenprasad.ca/
*/

// Block direct access to the plugin
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Add a filter to 'get_IP' for the real IP instead of the reverse proxy.
yourls_add_filter( 'get_IP', 'reverseproxy_get_ip');

function reverseproxy_get_ip( $ip ) {
    // Get real IP from common proxy headers, prioritized
    if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $forwarded_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim( $forwarded_ips[0] ); // Get first IP in chain
    } elseif ( isset( $_SERVER['HTTP_X_REAL_IP'] ) && !empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }

    // Sanitize the IP
    $sanitized_ip = yourls_sanitize_ip( $ip );

    // Anonymize the IP for Privacy (e.g., GDPR)
    if ( filter_var( $sanitized_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
        $parts = explode('.', $sanitized_ip);
        if ( count($parts) === 4 ) {
            $parts[3] = '0'; // Mask last octet for IPv4
            $sanitized_ip = implode('.', $parts);
        }
    } elseif ( filter_var( $sanitized_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
        // Mask the last 4 hextets for IPv6
        $sanitized_ip = preg_replace('/(:[^:]*){4}$/', ':0:0:0:0', $sanitized_ip);
    }

    return $sanitized_ip;
}
