<?php
/*
Plugin Name: Reverse Proxy Support
Plugin URI: https://github.com/paramedicspecialist/yourls_reverseproxy_support/
Description: Fixes and anonymizes incoming IPs from reverse proxies. Original plugin by Diftraku: https://github.com/Diftraku/yourls_cloudflare/
Version: 1.0.1
Author: Darkhorse
Author URI: https://devenprasad.ca/
*/

// Block Direct Access to the Plugin
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Add a Filter to 'get_IP' for the Real IP instead of the Reverse Proxy.
yourls_add_filter( 'get_IP', 'reverseproxy_get_ip');

function reverseproxy_get_ip( $ip ) {
    // Get Real IP from Common Proxy Headers
    if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $forwarded_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim( $forwarded_ips[0] ); // Get First IP in Chain
    } elseif ( isset( $_SERVER['HTTP_X_REAL_IP'] ) && !empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }

    // Sanitize the IP
    $sanitized_ip = yourls_sanitize_ip( $ip );

    // Anonymize the IP for Privacy (e.g., GDPR)
    if ( filter_var( $sanitized_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
        $parts = explode('.', $sanitized_ip);
        if ( count($parts) === 4 ) {
            $parts[3] = '0'; // Mask Last Octet for IPv4
            $sanitized_ip = implode('.', $parts);
        }
    } elseif ( filter_var( $sanitized_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
        // Mask the Last 4 Hextets for IPv6
        $sanitized_ip = preg_replace('/(:[^:]*){4}$/', ':0:0:0:0', $sanitized_ip);
    }

    return $sanitized_ip;
}
