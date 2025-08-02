<?php
/**
 * Script de diagnostic pour vérifier le statut des patterns
 * À ajouter temporairement dans functions.php pour déboguer
 */

// À ajouter uniquement pour le débogage - supprimer après résolution
function debug_patterns_status() {
    if (!current_user_can('administrator')) {
        return;
    }
    
    echo '<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px; font-family: monospace;">';
    echo '<h3>🔍 Diagnostic Patterns - Thème Moderne</h3>';

    
    // Vérifier les patterns
    if (class_exists('WP_Block_Patterns_Registry')) {
        $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
        echo '<h4>Patterns enregistrés:</h4><ul>';
        $theme_patterns = 0;
        foreach ($patterns as $pattern) {
            if (strpos($pattern['name'], 'theme-moderne/') === 0) {
                echo '<li>✅ ' . $pattern['title'] . ' (' . $pattern['name'] . ')</li>';
                $theme_patterns++;
            }
        }
        echo '</ul>';
        echo '<p><strong>Total patterns du thème: ' . $theme_patterns . '</strong></p>';
    } else {
        echo '<h4>Patterns enregistrés:</h4><p>Registre des patterns non disponible dans cette version de WordPress.</p>';
    }
    
    // Vérifier les fichiers
    $pattern_dir = get_template_directory() . '/patterns/';
    if (is_dir($pattern_dir)) {
        $files = glob($pattern_dir . '*.html');
        echo '<h4>Fichiers patterns trouvés:</h4><ul>';
        foreach ($files as $file) {
            echo '<li>' . basename($file) . '</li>';
        }
        echo '</ul>';
        echo '<p><strong>Total fichiers: ' . count($files) . '</strong></p>';
    }
    
    echo '<p><em>Si les patterns n\'apparaissent pas dans l\'éditeur, essayez de:</em></p>';
    echo '<ol>';
    echo '<li>Réactiver le thème</li>';
    echo '<li>Vider le cache</li>';
    echo '<li>Vérifier que les couleurs dans les patterns existent dans theme.json</li>';
    echo '</ol>';
    
    echo '</div>';
}

// Afficher uniquement sur le tableau de bord
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget('debug_patterns_widget', 'Diagnostic Patterns', 'debug_patterns_status');
});
?>