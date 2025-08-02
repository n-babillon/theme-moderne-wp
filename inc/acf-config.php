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
 * Enregistrer le bloc ACF pour les Compteurs
 */
function theme_moderne_register_counters_block() {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type(array(
        'name'              => 'counters-section',
        'title'             => __('Section Compteurs'),
        'description'       => __('Une section avec des compteurs animés en colonnes personnalisables.'),
        'render_template'   => get_template_directory() . '/template-parts/blocks/counters-section.php',
        'category'          => 'design',
        'icon'              => 'chart-bar',
        'keywords'          => array('compteur', 'chiffres', 'statistiques', 'colonnes', 'animation'),
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
                    'counters' => array(
                        array(
                            'number' => '150',
                            'label' => 'Projets réalisés',
                            'icon' => 'briefcase'
                        )
                    )
                )
            )
        )
    ));
}
add_action('acf/init', 'theme_moderne_register_counters_block');

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

/**
 * Champs ACF pour le bloc Section Compteurs
 */
function theme_moderne_register_counters_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_counters_section',
        'title' => 'Section Compteurs - Réglages',
        'fields' => array(
            array(
                'key' => 'field_counters_title',
                'label' => 'Titre de la section',
                'name' => 'counters_title',
                'type' => 'text',
                'instructions' => 'Titre principal de la section compteurs',
                'required' => 0,
                'default_value' => 'Nos Résultats en Chiffres',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_counters_description',
                'label' => 'Description',
                'name' => 'counters_description',
                'type' => 'textarea',
                'instructions' => 'Description sous le titre',
                'required' => 0,
                'default_value' => 'Découvrez nos accomplissements et notre expertise à travers ces statistiques',
                'rows' => 3,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_counters_list',
                'label' => 'Compteurs',
                'name' => 'counters_list',
                'type' => 'repeater',
                'instructions' => 'Ajoutez vos compteurs statistiques',
                'required' => 1,
                'layout' => 'table',
                'button_label' => 'Ajouter un compteur',
                'min' => 1,
                'max' => 6,
                'sub_fields' => array(
                    array(
                        'key' => 'field_counter_number',
                        'label' => 'Nombre',
                        'name' => 'counter_number',
                        'type' => 'number',
                        'instructions' => 'Le nombre à afficher (ex: 150, 2500, etc.)',
                        'required' => 1,
                        'default_value' => 100,
                        'min' => 0,
                        'max' => 999999,
                        'wrapper' => array(
                            'width' => '20',
                        ),
                    ),
                    array(
                        'key' => 'field_counter_suffix',
                        'label' => 'Suffixe',
                        'name' => 'counter_suffix',
                        'type' => 'text',
                        'instructions' => 'Texte après le nombre (ex: +, %, K, M)',
                        'required' => 0,
                        'default_value' => '+',
                        'maxlength' => 5,
                        'wrapper' => array(
                            'width' => '15',
                        ),
                    ),
                    array(
                        'key' => 'field_counter_label',
                        'label' => 'Étiquette',
                        'name' => 'counter_label',
                        'type' => 'text',
                        'instructions' => 'Texte descriptif du compteur',
                        'required' => 1,
                        'default_value' => 'Projets réalisés',
                        'wrapper' => array(
                            'width' => '35',
                        ),
                    ),
                    array(
                        'key' => 'field_counter_icon',
                        'label' => 'Icône',
                        'name' => 'counter_icon',
                        'type' => 'select',
                        'instructions' => 'Icône à afficher avec le compteur',
                        'required' => 0,
                        'choices' => array(
                            'briefcase' => 'Porte-documents',
                            'users' => 'Utilisateurs',
                            'trophy' => 'Trophée',
                            'star' => 'Étoile',
                            'heart' => 'Cœur',
                            'thumbs-up' => 'Pouce levé',
                            'check-circle' => 'Coche',
                            'target' => 'Cible',
                            'trending-up' => 'Tendance croissante',
                            'clock' => 'Horloge',
                            'globe' => 'Globe',
                            'coffee' => 'Café'
                        ),
                        'default_value' => 'briefcase',
                        'wrapper' => array(
                            'width' => '30',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_counters_columns',
                'label' => 'Nombre de colonnes',
                'name' => 'counters_columns',
                'type' => 'radio',
                'instructions' => 'Choisissez le nombre de colonnes pour l\'affichage',
                'required' => 1,
                'choices' => array(
                    '2' => '2 colonnes',
                    '3' => '3 colonnes',
                    '4' => '4 colonnes',
                ),
                'default_value' => '4',
                'layout' => 'horizontal',
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_counters_animation_speed',
                'label' => 'Vitesse d\'animation',
                'name' => 'counters_animation_speed',
                'type' => 'range',
                'instructions' => 'Durée de l\'animation en millisecondes',
                'required' => 0,
                'default_value' => 2000,
                'min' => 1000,
                'max' => 5000,
                'step' => 500,
                'append' => 'ms',
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_counters_color_scheme',
                'label' => 'Schéma de couleurs',
                'name' => 'counters_color_scheme',
                'type' => 'select',
                'instructions' => 'Choisissez le thème de couleur',
                'required' => 1,
                'choices' => array(
                    'primary' => 'Couleur principale',
                    'secondary' => 'Couleur secondaire',
                    'gradient' => 'Dégradé',
                    'dark' => 'Sombre',
                ),
                'default_value' => 'primary',
                'wrapper' => array(
                    'width' => '34',
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/counters-section',
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
        'description' => 'Réglages pour la section compteurs personnalisée',
    ));
}
add_action('acf/init', 'theme_moderne_register_counters_fields');