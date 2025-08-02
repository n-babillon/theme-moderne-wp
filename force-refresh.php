<?php
/**
 * SOLUTION RAPIDE : Forcer le déverrouillage des patterns
 * 
 * À utiliser si les patterns restent verrouillés
 * Copier ce code dans functions.php TEMPORAIREMENT
 */

// 1. Nettoyer tous les patterns du thème existants
add_action('init', function() {
    if (current_user_can('manage_options')) {
        $registry = WP_Block_Patterns_Registry::get_instance();
        $all_patterns = $registry->get_all_registered();
        
        // Supprimer tous les patterns theme-moderne
        foreach ($all_patterns as $pattern_name => $pattern_data) {
            if (strpos($pattern_name, 'theme-moderne/') === 0) {
                $registry->unregister($pattern_name);
            }
        }
        
        // Forcer le re-enregistrement
        theme_moderne_register_patterns();
    }
}, 1);

// 2. Message de confirmation en admin
add_action('admin_notices', function() {
    if (current_user_can('manage_options')) {
        $screen = get_current_screen();
        if ($screen && $screen->base === 'themes') {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<h3>🔓 Patterns déverrouillés !</h3>';
            echo '<p>Les patterns Hero et Témoignages ont été forcés à être éditables.</p>';
            echo '<p><strong>Action :</strong> Allez dans Pages → Ajouter → "+" → Cherchez "Thème Moderne"</p>';
            echo '<p><em>Note : Vous pouvez supprimer le fichier force-refresh.php après vérification.</em></p>';
            echo '</div>';
        }
    }
});

// 3. Forcer l'invalidation de tous les caches
add_action('init', function() {
    if (current_user_can('manage_options')) {
        // Nettoyer les transients liés aux patterns
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%pattern%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%pattern%'");
        
        // Cache WordPress
        wp_cache_flush();
    }
}, 2);
?>