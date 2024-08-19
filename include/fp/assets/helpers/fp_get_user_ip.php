<?php

if (!defined('ABSPATH')) exit;

function fetch_and_cache_cloudflare_ips()
{
    $cloudflare_ips = get_transient('cloudflare_ips');

    if (false === $cloudflare_ips) {
        $response = wp_remote_get('https://api.cloudflare.com/client/v4/ips');

        if (is_wp_error($response)) {
            // Log the error
            error_log('Error fetching Cloudflare IPs: ' . $response->get_error_message());
            // Fallback data if the Cloudflare API fails
            $cloudflare_ips = [
                "ipv4_cidrs" => [
                    "173.245.48.0/20", "103.21.244.0/22", "103.22.200.0/22", "103.31.4.0/22", "141.101.64.0/18",
                    "108.162.192.0/18", "190.93.240.0/20", "188.114.96.0/20", "197.234.240.0/22", "198.41.128.0/17",
                    "162.158.0.0/15", "104.16.0.0/13", "104.24.0.0/14", "172.64.0.0/13", "131.0.72.0/22"
                ],
                "ipv6_cidrs" => [
                    "2400:cb00::/32", "2606:4700::/32", "2803:f800::/32", "2405:b500::/32", "2405:8100::/32",
                    "2a06:98c0::/29", "2c0f:f248::/32"
                ]
            ];
        } else {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (isset($data['result'])) {
                $cloudflare_ips = $data['result'];
            } else {
                // Log unexpected response structure
                error_log('Unexpected Cloudflare API response structure.');
            }
        }

        // Cache the data for 12 hours
        set_transient('cloudflare_ips', $cloudflare_ips, 12 * HOUR_IN_SECONDS);
    }

    return $cloudflare_ips;
}



function get_the_user_ip() {
    
    // First, check for Cloudflare's forwarded IP
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        return $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    // Then check if IP is from a shared internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    // Next, check if the IP is passed from a proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Sometimes HTTP_X_FORWARDED_FOR contains a list of IPs separated by comma
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);  // Return the first IP in the list which is the client's original IP
    }
    // Otherwise, use the direct connection remote address
    return $_SERVER['REMOTE_ADDR'];
}

