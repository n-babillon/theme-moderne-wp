<?php
/**
 * Configuration et intégration d'Advanced Custom Fields
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Vérification de la présence d'ACF
 */
function theme_moderne_check_acf() {
    if (!class_exists('ACF')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo __('Le thème moderne nécessite le plugin Advanced Custom Fields pour fonctionner correctement.', 'theme-moderne');
            echo '</p></div>';
        });
        return false;
    }
    return true;
}

/**
 * Configuration par défaut d'ACF
 */
function theme_moderne_acf_init() {
    if (!theme_moderne_check_acf()) {
        return;
    }
}
add_action('init', 'theme_moderne_acf_init');

/**
 * Enregistrer le bloc ACF pour la Section Galerie
 */
function theme_moderne_register_gallery_section_block() {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type(array(
        'name'              => 'gallery-section',
        'title'             => __('Section Galerie'),
        'description'       => __('Une section galerie avec titre, description et galerie d\'images personnalisable.'),
        'render_template'   => get_template_directory() . '/template-parts/blocks/gallery-section.php',
        'category'          => 'media',
        'icon'              => 'format-gallery',
        'keywords'          => array('galerie', 'images', 'section', 'grille', 'masonry'),
        'mode'              => 'preview',
        'supports'          => array(
            'align'         => array('wide', 'full'),
            'anchor'        => true,
            'customClassName' => true,
        ),
        'example'           => array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'gallery_layout' => 'grid',
                    'gallery_gap' => '20',
                )
            )
        )
    ));
}
add_action('acf/init', 'theme_moderne_register_gallery_section_block');

/**
 * Champs ACF pour le bloc Section Galerie
 */
function theme_moderne_register_gallery_section_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_gallery_section',
        'title' => 'Section Galerie - Réglages',
        'fields' => array(
            array(
                'key' => 'field_section_title',
                'label' => 'Titre de la section',
                'name' => 'section_title',
                'type' => 'text',
                'instructions' => 'Titre principal de la section galerie',
                'required' => 0,
                'default_value' => 'Notre Galerie',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_section_description',
                'label' => 'Description',
                'name' => 'section_description',
                'type' => 'textarea',
                'instructions' => 'Description sous le titre',
                'required' => 0,
                'default_value' => 'Découvrez notre collection d\'images et de moments capturés',
                'rows' => 3,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_gallery_images',
                'label' => 'Images de la galerie',
                'name' => 'gallery_images',
                'type' => 'gallery',
                'instructions' => 'Sélectionnez les images à afficher',
                'required' => 1,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'insert' => 'append',
                'library' => 'all',
                'min' => 1,
                'max' => '',
            ),
            array(
                'key' => 'field_gallery_layout',
                'label' => 'Type d\'affichage',
                'name' => 'gallery_layout',
                'type' => 'radio',
                'instructions' => 'Choisissez entre grille ou mosaïque',
                'required' => 0,
                'choices' => array(
                    'grid' => 'Grille (hauteur uniforme)',
                    'masonry' => 'Mosaïque (hauteurs variables)',
                ),
                'default_value' => 'grid',
                'layout' => 'horizontal',
                'return_format' => 'value',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_gallery_columns',
                'label' => 'Nombre de colonnes',
                'name' => 'gallery_columns',
                'type' => 'select',
                'instructions' => 'Nombre de colonnes sur desktop',
                'required' => 0,
                'choices' => array(
                    '2' => '2 colonnes',
                    '3' => '3 colonnes',
                    '4' => '4 colonnes',
                    '5' => '5 colonnes',
                ),
                'default_value' => '3',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_gallery_gap',
                'label' => 'Largeur des gouttières',
                'name' => 'gallery_gap',
                'type' => 'range',
                'instructions' => 'Espacement entre les images (en pixels)',
                'required' => 0,
                'default_value' => 20,
                'min' => 0,
                'max' => 50,
                'step' => 5,
                'prepend' => '',
                'append' => 'px',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_gallery_border_radius',
                'label' => 'Arrondi des coins',
                'name' => 'gallery_border_radius',
                'type' => 'range',
                'instructions' => 'Arrondissement des coins des images',
                'required' => 0,
                'default_value' => 12,
                'min' => 0,
                'max' => 30,
                'step' => 2,
                'append' => 'px',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_gallery_image_height',
                'label' => 'Hauteur des images (grille)',
                'name' => 'gallery_image_height',
                'type' => 'range',
                'instructions' => 'Hauteur fixe des images en mode grille',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_gallery_layout',
                            'operator' => '==',
                            'value' => 'grid',
                        ),
                    ),
                ),
                'default_value' => 250,
                'min' => 150,
                'max' => 400,
                'step' => 10,
                'append' => 'px',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_gallery_hover_effects',
                'label' => 'Effets au survol',
                'name' => 'gallery_hover_effects',
                'type' => 'checkbox',
                'instructions' => 'Sélectionnez les effets désirés',
                'required' => 0,
                'choices' => array(
                    'zoom' => 'Zoom de l\'image',
                    'lift' => 'Élévation de l\'image',
                    'overlay' => 'Overlay sombre',
                ),
                'default_value' => array('zoom', 'lift'),
                'layout' => 'vertical',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/gallery-section',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Réglages pour la section galerie personnalisée',
    ));
}
add_action('acf/init', 'theme_moderne_register_gallery_section_fields');