<?php
/**
 * Configuration Advanced Custom Fields
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Vérification de l'existence d'ACF avant toute utilisation
 */
if (!function_exists('get_field')) {
    return;
}

/**
 * Ajouter des scripts de tracking personnalisés
 */
function theme_moderne_add_tracking_scripts() {
    if (!function_exists('get_field')) {
        return;
    }
    
    $google_analytics = get_field('google_analytics', 'option');
    $facebook_pixel = get_field('facebook_pixel', 'option');
    
    if ($google_analytics) {
        echo $google_analytics;
    }
    
    if ($facebook_pixel) {
        echo $facebook_pixel;
    }
}
add_action('wp_head', 'theme_moderne_add_tracking_scripts');

/**
 * Obtenir le texte du footer personnalisé
 */
function theme_moderne_get_footer_text() {
    if (!function_exists('get_field')) {
        return get_bloginfo('name') . ' &copy; ' . date('Y');
    }
    
    $footer_text = get_field('footer_text', 'option');
    return $footer_text ? $footer_text : get_bloginfo('name') . ' &copy; ' . date('Y');
}

/**
 * Configuration des pages d'options ACF
 */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Options du Thème',
        'menu_title' => 'Options Thème',
        'menu_slug' => 'theme-options',
        'capability' => 'edit_posts',
        'redirect' => false
    ]);
    
    acf_add_options_sub_page([
        'page_title' => 'Réglages Généraux',
        'menu_title' => 'Général',
        'parent_slug' => 'theme-options',
    ]);
    
    acf_add_options_sub_page([
        'page_title' => 'Scripts de Tracking',
        'menu_title' => 'Tracking',
        'parent_slug' => 'theme-options',
    ]);
}