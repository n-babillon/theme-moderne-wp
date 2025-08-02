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
    // Style plein largeur pour les groupes
    register_block_style(
        'core/group',
        array(
            'name'  => 'full-width',
            'label' => 'Plein largeur',
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