<?php
if (!defined('ABSPATH')) {
    exit;
}

// Inclusion de la configuration ACF
require_once get_template_directory() . '/inc/acf-config.php';

function theme_moderne_setup() {
    load_theme_textdomain('theme-moderne', get_template_directory() . '/languages');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', [
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'style',
        'script'
    ]);
    add_post_type_support('page', 'excerpt');
    register_nav_menus([
        'primary' => __('Menu Principal', 'theme-moderne'),
        'footer'  => __('Menu Pied de page', 'theme-moderne')
    ]);
}
add_action('after_setup_theme', 'theme_moderne_setup');

function theme_moderne_scripts() {
    wp_enqueue_style(
        'theme-moderne-style',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        wp_get_theme()->get('Version')
    );
    wp_enqueue_script(
        'theme-moderne-script',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
    
    // Script pour les blocs ACF côté frontend
    wp_enqueue_script(
        'theme-moderne-acf-blocks-frontend',
        get_template_directory_uri() . '/assets/js/acf-blocks-frontend.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
    
    // Script pour les améliorations des blocs ACF (admin uniquement)
    if (is_admin()) {
        wp_enqueue_script(
            'theme-moderne-acf-blocks',
            get_template_directory_uri() . '/assets/js/acf-blocks.js',
            array('wp-blocks', 'wp-element', 'wp-components'),
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'theme_moderne_scripts');

/**
 * Enregistrer le script des template-parts
 */
function theme_moderne_enqueue_template_parts_script() {
    wp_enqueue_script(
        'theme-moderne-template-parts',
        get_template_directory_uri() . '/assets/js/template-parts.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'theme_moderne_enqueue_template_parts_script');

/**
 * Personnaliser les classes des template-parts
 */
function theme_moderne_customize_template_part_classes($block_content, $block) {
    // Vérifier si c'est un template-part
    if ($block['blockName'] === 'core/template-part') {
        $area = $block['attrs']['area'] ?? '';
        $slug = $block['attrs']['slug'] ?? '';
        
        // Ajouter des classes personnalisées selon la zone
        $custom_classes = [];
        
        switch($area) {
            case 'header':
                $custom_classes[] = 'site-header';
                $custom_classes[] = 'main-header';
                break;
            case 'footer':
                $custom_classes[] = 'site-footer';
                $custom_classes[] = 'main-footer';
                break;
        }
        
        // Ajouter le slug comme classe
        if ($slug) {
            $custom_classes[] = 'template-' . sanitize_html_class($slug);
        }
        
        // Injecter les classes dans le HTML
        if (!empty($custom_classes)) {
            $classes_string = implode(' ', $custom_classes);
            $block_content = preg_replace(
                '/class="([^"]*wp-block-template-part[^"]*)"/',
                'class="$1 ' . $classes_string . '"',
                $block_content
            );
        }
    }
    
    return $block_content;
}
add_filter('render_block', 'theme_moderne_customize_template_part_classes', 10, 2);

/**
 * Personnaliser les classes de tous les blocs WordPress
 */
function theme_moderne_customize_all_block_classes($block_content, $block) {
    // Vérification de sécurité
    if (empty($block_content) || empty($block['blockName'])) {
        return $block_content;
    }
    
    $block_name = $block['blockName'];
    $custom_classes = [];
    
    // Debug : log des blocs traités (uniquement en mode debug)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Traitement du bloc: $block_name");
    }
    
    // Personnalisation par type de bloc
    switch ($block_name) {
        case 'core/group':
            // Vérifier les classes existantes
            $existing_classes = $block['attrs']['className'] ?? '';
            if (strpos($existing_classes, 'site-header') !== false) {
                $custom_classes[] = 'main-navigation-wrapper';
                $custom_classes[] = 'header-container';
            }
            break;
            
        case 'core/navigation':
            $custom_classes[] = 'main-navigation';
            $custom_classes[] = 'primary-menu';
            break;
            
        case 'core/site-title':
            $custom_classes[] = 'site-brand';
            $custom_classes[] = 'main-logo';
            break;
            
        case 'core/site-logo':
            $custom_classes[] = 'brand-logo';
            $custom_classes[] = 'header-logo';
            break;
            
        case 'core/buttons':
            $custom_classes[] = 'action-buttons';
            break;
            
        case 'core/button':
            $existing_classes = $block['attrs']['className'] ?? '';
            if (strpos($existing_classes, 'cta') !== false) {
                $custom_classes[] = 'primary-cta';
            }
            break;
            
        case 'core/heading':
            $level = $block['attrs']['level'] ?? 2;
            $custom_classes[] = 'heading-level-' . $level;
            
            if ($level === 1) {
                $custom_classes[] = 'page-title';
            } elseif ($level === 2) {
                $custom_classes[] = 'section-title';
            }
            break;
            
        case 'core/image':
            $custom_classes[] = 'responsive-image';
            if (strpos($block_content, 'site-logo') !== false) {
                $custom_classes[] = 'logo-image';
            }
            break;
            
        case 'core/cover':
            $custom_classes[] = 'hero-section';
            $custom_classes[] = 'cover-background';
            break;
            
        case 'core/columns':
            $custom_classes[] = 'layout-columns';
            $custom_classes[] = 'responsive-grid';
            break;
            
        case 'core/column':
            $custom_classes[] = 'grid-column';
            break;
            
        case 'core/media-text':
            $custom_classes[] = 'media-content-split';
            break;
    }
    
    // Ajouter les classes si on en a trouvé
    if (!empty($custom_classes)) {
        $classes_string = implode(' ', $custom_classes);
        
        // Debug
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Ajout des classes: $classes_string");
        }
        
        // Méthode plus robuste pour ajouter les classes
        if (preg_match('/class="([^"]*)"/', $block_content, $matches)) {
            // Classe existante trouvée, l'étendre
            $existing_classes = $matches[1];
            $new_classes = $existing_classes . ' ' . $classes_string;
            $block_content = str_replace(
                'class="' . $existing_classes . '"',
                'class="' . $new_classes . '"',
                $block_content
            );
        } else {
            // Pas de classe existante, ajouter au premier élément HTML
            $block_content = preg_replace(
                '/(<[a-zA-Z][^>]*?)>/',
                '$1 class="' . $classes_string . '">',
                $block_content,
                1
            );
        }
    }
    
    return $block_content;
}
add_filter('render_block', 'theme_moderne_customize_all_block_classes', 15, 2);

/**
 * Alternative : Ajouter des classes CSS via JavaScript (méthode de fallback)
 */
function theme_moderne_add_block_classes_js() {
    if (is_admin()) return; // Seulement côté front-end
    
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎨 Application des classes personnalisées...');
        
        // Navigation
        const navBlocks = document.querySelectorAll('.wp-block-navigation');
        navBlocks.forEach(nav => {
            nav.classList.add('main-navigation', 'primary-menu');
            console.log('✅ Classes ajoutées à la navigation');
        });
        
        // Site title
        const siteTitles = document.querySelectorAll('.wp-block-site-title');
        siteTitles.forEach(title => {
            title.classList.add('site-brand', 'main-logo');
            console.log('✅ Classes ajoutées au titre du site');
        });
        
        // Site logo
        const siteLogos = document.querySelectorAll('.wp-block-site-logo');
        siteLogos.forEach(logo => {
            logo.classList.add('brand-logo', 'header-logo');
            console.log('✅ Classes ajoutées au logo');
        });
        
        // Groups avec site-header
        const headerGroups = document.querySelectorAll('.wp-block-group.site-header');
        headerGroups.forEach(group => {
            group.classList.add('main-navigation-wrapper', 'header-container');
            console.log('✅ Classes ajoutées au groupe header');
        });
        
        // Buttons
        const buttons = document.querySelectorAll('.wp-block-button');
        buttons.forEach(button => {
            button.classList.add('action-buttons');
            console.log('✅ Classes ajoutées aux boutons');
        });
        
        // Headings
        const headings = document.querySelectorAll('[class*="wp-block-heading"], h1, h2, h3, h4, h5, h6');
        headings.forEach(heading => {
            const level = heading.tagName.toLowerCase().replace('h', '');
            heading.classList.add('heading-level-' + level);
            
            if (level === '1') {
                heading.classList.add('page-title');
            } else if (level === '2') {
                heading.classList.add('section-title');
            }
            console.log('✅ Classes ajoutées au titre H' + level);
        });
        
        console.log('🎉 Classes personnalisées appliquées avec succès !');
    });
    </script>
    <?php
}
add_action('wp_footer', 'theme_moderne_add_block_classes_js');

/**
 * Ajouter des attributs data personnalisés aux template-parts
 */
function theme_moderne_add_template_part_attributes($block_content, $block) {
    if ($block['blockName'] === 'core/template-part') {
        $area = $block['attrs']['area'] ?? '';
        $slug = $block['attrs']['slug'] ?? '';
        $theme = $block['attrs']['theme'] ?? get_stylesheet();
        
        // Ajouter des attributs data
        $attributes = [];
        if ($area) $attributes[] = 'data-area="' . esc_attr($area) . '"';
        if ($slug) $attributes[] = 'data-template-slug="' . esc_attr($slug) . '"';
        if ($theme) $attributes[] = 'data-theme="' . esc_attr($theme) . '"';
        
        if (!empty($attributes)) {
            $attrs_string = implode(' ', $attributes);
            $block_content = preg_replace(
                '/(<[^>]*wp-block-template-part[^>]*)(>)/',
                '$1 ' . $attrs_string . '$2',
                $block_content
            );
        }
    }
    
    return $block_content;
}
add_filter('render_block', 'theme_moderne_add_template_part_attributes', 10, 2);

function theme_moderne_register_pattern_categories() {
    register_block_pattern_category(
        'theme-moderne',
        [
            'label' => __('Thème Moderne', 'theme-moderne'),
            'description' => __('Patterns du thème moderne', 'theme-moderne'),
        ]
    );
}
add_action('init', 'theme_moderne_register_pattern_categories');

function theme_moderne_custom_image_sizes() {
    add_image_size('hero-image', 1920, 800, true);
    add_image_size('card-image', 400, 300, true);
}
add_action('after_setup_theme', 'theme_moderne_custom_image_sizes');

function theme_moderne_editor_setup() {
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
}
add_action('after_setup_theme', 'theme_moderne_editor_setup');

function theme_moderne_register_patterns_from_files() {
    $pattern_dir = get_template_directory() . '/patterns/';
    if (!is_dir($pattern_dir)) {
        return;
    }
    $pattern_files = glob($pattern_dir . '*.html');
    foreach ($pattern_files as $file) {
        $content = file_get_contents($file);
        preg_match('/<!--\s*(.*?)\s*-->/s', $content, $matches);
        if (!isset($matches[1])) {
            continue;
        }
        $header = $matches[1];
        $title = '';
        $slug = '';
        $description = '';
        $categories = array();
        $keywords = array();
        if (preg_match('/Title:\s*(.+)/', $header, $m)) {
            $title = trim($m[1]);
        }
        if (preg_match('/Slug:\s*(.+)/', $header, $m)) {
            $slug = trim($m[1]);
        }
        if (preg_match('/Description:\s*(.+)/', $header, $m)) {
            $description = trim($m[1]);
        }
        if (preg_match('/Categories:\s*(.+)/', $header, $m)) {
            $categories = array_map('trim', explode(',', $m[1]));
        }
        if (preg_match('/Keywords:\s*(.+)/', $header, $m)) {
            $keywords = array_map('trim', explode(',', $m[1]));
        }
        $pattern_content = preg_replace('/<!--\s*(.*?)\s*-->\s*/s', '', $content, 1);
        if (!empty($slug) && !empty($title) && !empty($pattern_content)) {
            register_block_pattern(
                $slug,
                array(
                    'title' => $title,
                    'description' => $description,
                    'content' => trim($pattern_content),
                    'categories' => $categories,
                    'keywords' => $keywords,
                )
            );
        }
    }
}
add_action('init', 'theme_moderne_register_patterns_from_files');

/**
 * Enregistre les styles de blocs personnalisés
 */
function theme_moderne_register_block_styles() {
    
    // Vérifier que la fonction est disponible
    if (!function_exists('register_block_style')) {
        return;
    }
    
    // Styles personnalisés pour le bloc Navigation
    register_block_style(
        'core/navigation',
        array(
            'name'  => 'header-nav',
            'label' => __('Navigation Header', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
    
    register_block_style(
        'core/navigation',
        array(
            'name'  => 'footer-nav',
            'label' => __('Navigation Footer', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
    
    // Styles personnalisés pour les boutons
    register_block_style(
        'core/button',
        array(
            'name'  => 'modern-primary',
            'label' => __('Bouton Principal Moderne', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
    
    register_block_style(
        'core/button',
        array(
            'name'  => 'modern-secondary',
            'label' => __('Bouton Secondaire Moderne', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
    
    // Styles pour les groupes (utile pour les headers)
    register_block_style(
        'core/group',
        array(
            'name'  => 'header-wrapper',
            'label' => __('Conteneur Header', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
    
    register_block_style(
        'core/group',
        array(
            'name'  => 'section-container',
            'label' => __('Conteneur Section', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
    
    // Styles pour les titres
    register_block_style(
        'core/heading',
        array(
            'name'  => 'gradient-title',
            'label' => __('Titre avec Dégradé', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
    
    // Styles pour les colonnes
    register_block_style(
        'core/columns',
        array(
            'name'  => 'equal-height',
            'label' => __('Hauteur Égale', 'theme-moderne'),
            'style_handle' => 'theme-moderne-blocks'
        )
    );
}
add_action('init', 'theme_moderne_register_block_styles');

// Ajout du widget de diagnostic des patterns (voir debug-patterns-status.php)
if (is_admin()) {
    /**
     * Affiche un widget de diagnostic des patterns dans le tableau de bord
     */
    function theme_moderne_debug_patterns_status() {
        if (!current_user_can('administrator')) {
            return;
        }
        
        echo '<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px; font-family: monospace;">';
        echo '<h3>🔍 Diagnostic Patterns - Thème Moderne</h3>';

        // Vérification des patterns enregistrés
        if (class_exists('WP_Block_Patterns_Registry')) {
            $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
            echo '<h4>Patterns enregistrés :</h4><ul>';
            $theme_patterns = 0;
            foreach ($patterns as $pattern) {
                if (strpos($pattern['name'], 'theme-moderne/') === 0) {
                    echo '<li>✅ ' . esc_html($pattern['title']) . ' (' . esc_html($pattern['name']) . ')</li>';
                    $theme_patterns++;
                }
            }
            echo '</ul>';
            echo '<p><strong>Total patterns du thème : ' . intval($theme_patterns) . '</strong></p>';
        } else {
            echo '<h4>Patterns enregistrés :</h4><p>Registre des patterns non disponible dans cette version de WordPress.</p>';
        }

        // Vérification des fichiers patterns
        $pattern_dir = get_template_directory() . '/patterns/';
        if (is_dir($pattern_dir)) {
            $files = glob($pattern_dir . '*.html');
            echo '<h4>Fichiers patterns trouvés :</h4><ul>';
            foreach ($files as $file) {
                echo '<li>' . esc_html(basename($file)) . '</li>';
            }
            echo '</ul>';
            echo '<p><strong>Total fichiers : ' . count($files) . '</strong></p>';
        }

        echo '<p><em>Si les patterns n\'apparaissent pas dans l\'éditeur, essayez de :</em></p>';
        echo '<ol>';
        echo '<li>Réactiver le thème</li>';
        echo '<li>Vider le cache</li>';
        echo '<li>Vérifier que les couleurs dans les patterns existent dans theme.json</li>';
        echo '</ol>';

        echo '</div>';
    }

    // Affiche le widget uniquement sur le tableau de bord admin
    add_action('wp_dashboard_setup', function() {
        wp_add_dashboard_widget('theme_moderne_debug_patterns_widget', 'Diagnostic Patterns', 'theme_moderne_debug_patterns_status');
    });
}