<?php
/**
 * Script de test pour vérifier le support SVG
 * Accédez à cette page pour tester : http://votresite.local/wp-content/themes/theme-moderne-wp/test-svg-upload.php
 */

// Inclure WordPress
require_once('../../../wp-load.php');

// Vérifier les permissions
if (!current_user_can('upload_files')) {
    die('Vous n\'avez pas les permissions pour télécharger des fichiers.');
}

echo '<h1>🔧 Test Support SVG - Thème Moderne</h1>';

echo '<h2>✅ Vérifications :</h2>';
echo '<ul>';

// Vérifier si SVG est autorisé dans les types MIME
$allowed_mimes = get_allowed_mime_types();
if (isset($allowed_mimes['svg'])) {
    echo '<li>✅ SVG autorisé dans WordPress : ' . $allowed_mimes['svg'] . '</li>';
} else {
    echo '<li>❌ SVG non autorisé dans WordPress</li>';
}

// Vérifier les extensions d'images supportées
if (extension_loaded('gd')) {
    echo '<li>✅ Extension GD chargée</li>';
} else {
    echo '<li>❌ Extension GD non disponible</li>';
}

if (extension_loaded('imagick')) {
    echo '<li>✅ Extension ImageMagick chargée</li>';
} else {
    echo '<li>⚠️ Extension ImageMagick non disponible (pas obligatoire)</li>';
}

// Vérifier les limites d'upload
echo '<li>Taille max upload : ' . size_format(wp_max_upload_size()) . '</li>';
echo '<li>Limite mémoire : ' . ini_get('memory_limit') . '</li>';
echo '<li>Temps max exécution : ' . ini_get('max_execution_time') . 's</li>';

echo '</ul>';

echo '<h2>🎯 Solutions si SVG ne fonctionne toujours pas :</h2>';
echo '<ol>';
echo '<li><strong>Serveur :</strong> Vérifiez que votre serveur autorise les SVG</li>';
echo '<li><strong>Sécurité :</strong> Certains hébergeurs bloquent les SVG par sécurité</li>';
echo '<li><strong>Plugin :</strong> Installez le plugin "SVG Support" si nécessaire</li>';
echo '<li><strong>Fichier .htaccess :</strong> Utilisez le fichier .htaccess fourni dans le thème</li>';
echo '</ol>';

echo '<h2>📁 Test d\'upload manuel :</h2>';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_svg'])) {
    $file = $_FILES['test_svg'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = $file['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (strtolower($ext) === 'svg') {
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . '/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                echo '<p style="color: green;">✅ SVG uploadé avec succès : ' . $filename . '</p>';
                echo '<p>URL : <a href="' . $upload_dir['url'] . '/' . $filename . '" target="_blank">' . $upload_dir['url'] . '/' . $filename . '</a></p>';
            } else {
                echo '<p style="color: red;">❌ Erreur lors de l\'upload</p>';
            }
        } else {
            echo '<p style="color: red;">❌ Veuillez sélectionner un fichier SVG</p>';
        }
    } else {
        echo '<p style="color: red;">❌ Erreur upload : ' . $file['error'] . '</p>';
    }
}

echo '<form method="post" enctype="multipart/form-data">';
echo '<p><input type="file" name="test_svg" accept=".svg" required></p>';
echo '<p><button type="submit">Tester l\'upload SVG</button></p>';
echo '</form>';

echo '<h2>🔍 Types MIME autorisés :</h2>';
echo '<details>';
echo '<summary>Voir la liste complète</summary>';
echo '<pre>';
print_r($allowed_mimes);
echo '</pre>';
echo '</details>';

echo '<p><em>Vous pouvez supprimer ce fichier après les tests.</em></p>';

add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});
?>

