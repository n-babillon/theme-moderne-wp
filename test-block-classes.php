<?php
/**
 * Script de test pour vérifier si les classes personnalisées sont ajoutées
 * À placer dans le répertoire du thème et visiter via navigateur
 */

// Inclure WordPress
require_once('../../../wp-load.php');

// Vérifier les permissions
if (!current_user_can('administrator')) {
    die('Accès non autorisé');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Classes Blocs</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        code { background: #e9ecef; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic des Classes Personnalisées</h1>
    
    <div class="test-section">
        <h2>1. Vérification des Filtres</h2>
        <?php
        global $wp_filter;
        
        if (isset($wp_filter['render_block'])) {
            echo '<div class="success"><strong>✅ Filtre render_block détecté</strong></div>';
            echo '<h3>Callbacks enregistrés :</h3><ul>';
            
            foreach ($wp_filter['render_block']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback_name => $callback_data) {
                    $function_name = '';
                    if (is_array($callback_data['function'])) {
                        $function_name = get_class($callback_data['function'][0]) . '::' . $callback_data['function'][1];
                    } else {
                        $function_name = $callback_data['function'];
                    }
                    echo "<li>Priorité $priority: <code>$function_name</code></li>";
                }
            }
            echo '</ul>';
        } else {
            echo '<div class="error"><strong>❌ Aucun filtre render_block trouvé</strong></div>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>2. Test Manuel des Fonctions</h2>
        <?php
        // Test de la fonction principale
        if (function_exists('theme_moderne_customize_all_block_classes')) {
            echo '<div class="success"><strong>✅ Fonction theme_moderne_customize_all_block_classes existe</strong></div>';
            
            // Test avec un bloc simulé
            $test_block = [
                'blockName' => 'core/navigation',
                'attrs' => ['className' => 'wp-block-navigation']
            ];
            
            $test_content = '<nav class="wp-block-navigation">Test</nav>';
            $result = theme_moderne_customize_all_block_classes($test_content, $test_block);
            
            echo '<h3>Test avec core/navigation :</h3>';
            echo '<p><strong>Avant :</strong> <code>' . htmlspecialchars($test_content) . '</code></p>';
            echo '<p><strong>Après :</strong> <code>' . htmlspecialchars($result) . '</code></p>';
            
            if ($result !== $test_content) {
                echo '<div class="success">✅ La fonction modifie bien le contenu</div>';
            } else {
                echo '<div class="warning">⚠️ La fonction ne modifie pas le contenu</div>';
            }
        } else {
            echo '<div class="error"><strong>❌ Fonction theme_moderne_customize_all_block_classes non trouvée</strong></div>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>3. Test avec Différents Blocs</h2>
        <?php
        if (function_exists('theme_moderne_customize_all_block_classes')) {
            $test_blocks = [
                [
                    'name' => 'core/site-title',
                    'block' => ['blockName' => 'core/site-title'],
                    'content' => '<h1 class="wp-block-site-title">Mon Site</h1>'
                ],
                [
                    'name' => 'core/group (avec site-header)',
                    'block' => ['blockName' => 'core/group', 'attrs' => ['className' => 'site-header']],
                    'content' => '<div class="wp-block-group site-header">Contenu</div>'
                ],
                [
                    'name' => 'core/button',
                    'block' => ['blockName' => 'core/button'],
                    'content' => '<div class="wp-block-button"><a class="wp-block-button__link">Bouton</a></div>'
                ]
            ];
            
            foreach ($test_blocks as $test) {
                echo "<h4>Test: {$test['name']}</h4>";
                $result = theme_moderne_customize_all_block_classes($test['content'], $test['block']);
                echo '<p><strong>Avant :</strong> <code>' . htmlspecialchars($test['content']) . '</code></p>';
                echo '<p><strong>Après :</strong> <code>' . htmlspecialchars($result) . '</code></p>';
                
                if ($result !== $test['content']) {
                    echo '<div class="success">✅ Classes ajoutées</div>';
                } else {
                    echo '<div class="warning">⚠️ Aucune modification</div>';
                }
                echo '<hr>';
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>4. Vérification du CSS</h2>
        <?php
        $css_file = get_template_directory() . '/assets/css/blocks/custom-block-styles.css';
        if (file_exists($css_file)) {
            echo '<div class="success"><strong>✅ Fichier CSS custom-block-styles.css existe</strong></div>';
            echo '<p>Taille : ' . filesize($css_file) . ' octets</p>';
        } else {
            echo '<div class="error"><strong>❌ Fichier CSS custom-block-styles.css manquant</strong></div>';
        }
        
        // Vérifier si le CSS est chargé
        echo '<h3>CSS chargé dans le navigateur :</h3>';
        echo '<p>Vérifiez dans l\'inspecteur si ces classes sont présentes :</p>';
        echo '<ul>';
        echo '<li><code>.main-navigation</code></li>';
        echo '<li><code>.site-brand</code></li>';
        echo '<li><code>.header-container</code></li>';
        echo '</ul>';
        ?>
    </div>
    
    <div class="test-section">
        <h2>5. Actions Correctives</h2>
        <p>Si les tests échouent, essayez :</p>
        <ol>
            <li><strong>Vider le cache :</strong> 
                <a href="<?php echo admin_url('admin.php?page=wp-rocket'); ?>" target="_blank">WP Rocket</a> ou 
                plugin de cache installé
            </li>
            <li><strong>Réactiver le thème :</strong> 
                <a href="<?php echo admin_url('themes.php'); ?>" target="_blank">Apparence → Thèmes</a>
            </li>
            <li><strong>Vérifier les erreurs PHP :</strong> 
                <a href="<?php echo admin_url('tools.php?page=health-check'); ?>" target="_blank">Outils → Santé du site</a>
            </li>
            <li><strong>Mode debug :</strong> Ajouter <code>define('WP_DEBUG', true);</code> dans wp-config.php</li>
        </ol>
    </div>
    
</body>
</html>