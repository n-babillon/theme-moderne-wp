<?php
/**
 * Script de rafraîchissement des patterns
 * À exécuter une seule fois pour forcer la re-détection
 */

// Inclure WordPress
require_once('../../../wp-load.php');

// Vérifier les permissions
if (!current_user_can('administrator')) {
    die('Accès non autorisé');
}

// Supprimer le cache des patterns
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Supprimer les transients liés aux patterns
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%pattern%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%pattern%'");

// Forcer le re-enregistrement des patterns
if (class_exists('WP_Block_Patterns_Registry')) {
    $registry = WP_Block_Patterns_Registry::get_instance();
    $patterns = $registry->get_all_registered();
    
    // Supprimer tous les patterns du thème
    foreach ($patterns as $name => $pattern) {
        if (strpos($name, 'theme-moderne/') === 0) {
            $registry->unregister($name);
        }
    }
}

// Re-enregistrer depuis les fichiers
theme_moderne_register_patterns_from_files();

echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px;">';
echo '<h2>✅ Patterns rafraîchis avec succès !</h2>';
echo '<p>Les actions suivantes ont été effectuées :</p>';
echo '<ul>';
echo '<li>Cache WordPress vidé</li>';
echo '<li>Transients des patterns supprimés</li>';
echo '<li>Patterns du thème ré-enregistrés</li>';
echo '</ul>';

// Afficher les patterns détectés
if (class_exists('WP_Block_Patterns_Registry')) {
    $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
    $theme_patterns = 0;
    echo '<h3>Patterns du thème détectés :</h3><ul>';
    foreach ($patterns as $name => $pattern) {
        if (strpos($name, 'theme-moderne/') === 0) {
            echo "<li>✅ {$pattern['title']} ({$name})</li>";
            $theme_patterns++;
        }
    }
    echo '</ul>';
    echo "<p><strong>Total : {$theme_patterns} patterns</strong></p>";
}

// Afficher les fichiers trouvés
$pattern_dir = get_template_directory() . '/patterns/';
$files = glob($pattern_dir . '*.html');
echo '<h3>Fichiers patterns trouvés :</h3><ul>';
foreach ($files as $file) {
    echo '<li>' . basename($file) . '</li>';
}
echo '</ul>';
echo '<p><strong>Total fichiers : ' . count($files) . '</strong></p>';

echo '<p><strong>Prochaines étapes :</strong></p>';
echo '<ol>';
echo '<li>Allez dans l\'éditeur WordPress (Pages → Ajouter)</li>';
echo '<li>Cliquez sur le "+" pour ajouter des blocs</li>';
echo '<li>Cherchez "Patterns" ou "Thème Moderne"</li>';
echo '<li>Vos patterns devraient maintenant apparaître</li>';
echo '</ol>';

echo '<p><em>Vous pouvez supprimer ce fichier après utilisation.</em></p>';
echo '</div>';
?>