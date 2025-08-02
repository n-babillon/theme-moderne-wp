<?php
/**
 * Fonctions de debug pour les blocks personnalisés
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Afficher les patterns enregistrés dans l'admin (pour debug)
 */
function theme_moderne_debug_patterns() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    add_action('admin_notices', function() {
        if (WP_DEBUG) {
            $registered_patterns = \WP_Block_Patterns_Registry::get_instance()->get_all_registered();
            $theme_patterns = array_filter($registered_patterns, function($pattern_name) {
                return strpos($pattern_name, 'theme-moderne/') === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            if (!empty($theme_patterns)) {
                echo '<div class="notice notice-success"><p>';
                echo '<strong>Patterns du thème enregistrés :</strong><br>';
                foreach ($theme_patterns as $pattern_name => $pattern) {
                    echo "✅ $pattern_name<br>";
                }
                echo '</p></div>';
            } else {
                echo '<div class="notice notice-warning"><p>';
                echo '⚠️ <strong>Aucun pattern du thème trouvé !</strong><br>';
                echo 'Vérifiez que les fichiers de patterns sont corrects.';
                echo '</p></div>';
            }
        }
    });
}

/**
 * Activer le debug seulement si WP_DEBUG est activé
 */
if (WP_DEBUG) {
    add_action('admin_init', 'theme_moderne_debug_patterns');
}

/**
 * Fonction pour lister les patterns disponibles (pour debug)
 */
function theme_moderne_list_patterns() {
    if (!current_user_can('manage_options')) {
        wp_die('Accès refusé');
    }
    
    $registered_patterns = \WP_Block_Patterns_Registry::get_instance()->get_all_registered();
    
    echo '<h1>Patterns enregistrés</h1>';
    echo '<h2>Patterns du thème :</h2>';
    
    $theme_patterns = array_filter($registered_patterns, function($pattern_name) {
        return strpos($pattern_name, 'theme-moderne/') === 0;
    }, ARRAY_FILTER_USE_KEY);
    
    if (!empty($theme_patterns)) {
        echo '<ul>';
        foreach ($theme_patterns as $pattern_name => $pattern) {
            echo "<li><strong>$pattern_name</strong> - {$pattern['title']}</li>";
        }
        echo '</ul>';
    } else {
        echo '<p style="color: orange;">Aucun pattern du thème trouvé !</p>';
    }
    
    echo '<h2>Tous les patterns :</h2>';
    echo '<details><summary>Voir tous les patterns (' . count($registered_patterns) . ')</summary>';
    echo '<ul>';
    foreach ($registered_patterns as $pattern_name => $pattern) {
        echo "<li>$pattern_name</li>";
    }
    echo '</ul>';
    echo '</details>';
}

/**
 * Ajouter une page de debug dans l'admin
 */
function theme_moderne_add_debug_menu() {
    if (WP_DEBUG && current_user_can('manage_options')) {
        add_submenu_page(
            'themes.php',
            'Debug Patterns',
            'Debug Patterns',
            'manage_options',
            'theme-moderne-debug',
            'theme_moderne_list_patterns'
        );
    }
}
add_action('admin_menu', 'theme_moderne_add_debug_menu');