<?php
/**
 * Template pour le bloc ACF Section Galerie
 */

if (!defined('ABSPATH')) {
    exit;
}

// Récupérer les champs ACF
$section_title = get_field('section_title') ?: 'Notre Galerie';
$section_description = get_field('section_description') ?: 'Découvrez notre collection d\'images et de moments capturés';
$images = get_field('gallery_images');
$layout = get_field('gallery_layout') ?: 'grid';
$columns = get_field('gallery_columns') ?: '3';
$gap = get_field('gallery_gap') ?: '20';
$border_radius = get_field('gallery_border_radius') ?: '12';
$hover_effects = get_field('gallery_hover_effects') ?: array('zoom', 'lift');
$image_height = get_field('gallery_image_height') ?: '250';

// Vérifier si on est en mode preview dans l'éditeur
$is_preview = isset($block['data']['_is_preview']) && $block['data']['_is_preview'];

// Générer un ID unique pour cette galerie
$gallery_id = 'gallery-section-' . uniqid();

// Classes CSS de base
$block_classes = array('gallery-section-block');
if (!empty($block['className'])) {
    $block_classes[] = $block['className'];
}

// Classes pour la galerie
$gallery_classes = array(
    'wp-block-gallery',
    'gallery-grid',
    'gallery-' . $layout,
    'columns-' . $columns
);

// Si pas d'images, afficher un placeholder en mode preview
if (!$images) {
    if ($is_preview) {
        echo '<div class="gallery-section-placeholder" style="padding: 40px; border: 2px dashed #007acc; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 48px; margin-bottom: 16px;">🖼️</div>
                <h3 style="margin: 0 0 8px 0; color: #333;">Section Galerie</h3>
                <p style="margin: 0; font-size: 14px;">Ajoutez des images dans les réglages →</p>
              </div>';
    }
    return;
}

// Attributs data pour JavaScript
$data_attributes = array(
    'data-layout' => $layout,
    'data-columns' => $columns,
    'data-gap' => $gap,
    'data-border-radius' => $border_radius,
    'data-hover-effects' => implode(',', $hover_effects),
    'data-image-height' => $image_height,
);

// Styles CSS dynamiques
$styles = array();

if ($layout === 'grid') {
    $styles[] = "
        #{$gallery_id} {
            display: grid !important;
            grid-template-columns: repeat({$columns}, 1fr) !important;
            gap: {$gap}px !important;
        }
        #{$gallery_id} .wp-block-image {
            margin: 0 !important;
        }
        #{$gallery_id} .wp-block-image img {
            height: {$image_height}px !important;
            object-fit: cover !important;
            border-radius: {$border_radius}px !important;
            width: 100% !important;
        }
    ";
} else {
    $styles[] = "
        #{$gallery_id} {
            column-count: {$columns} !important;
            column-gap: {$gap}px !important;
            display: block !important;
        }
        #{$gallery_id} .wp-block-image {
            break-inside: avoid !important;
            margin-bottom: {$gap}px !important;
            display: block !important;
        }
        #{$gallery_id} .wp-block-image img {
            height: auto !important;
            border-radius: {$border_radius}px !important;
            width: 100% !important;
        }
    ";
}

// Responsive
$styles[] = "
    @media (max-width: 1024px) {
        #{$gallery_id} {
            " . ($layout === 'grid' ? 
                "grid-template-columns: repeat(" . max(1, $columns - 1) . ", 1fr) !important;" :
                "column-count: " . max(1, $columns - 1) . " !important;"
            ) . "
        }
    }
    @media (max-width: 768px) {
        #{$gallery_id} {
            " . ($layout === 'grid' ? 
                "grid-template-columns: repeat(" . max(1, ceil($columns / 2)) . ", 1fr) !important;" :
                "column-count: " . max(1, ceil($columns / 2)) . " !important;"
            ) . "
        }
    }
    @media (max-width: 480px) {
        #{$gallery_id} {
            " . ($layout === 'grid' ? 
                "grid-template-columns: 1fr !important;" :
                "column-count: 1 !important;"
            ) . "
        }
    }
";

// Effets hover
if (in_array('zoom', $hover_effects) || in_array('lift', $hover_effects) || in_array('overlay', $hover_effects)) {
    $styles[] = "
        #{$gallery_id} .wp-block-image {
            transition: transform 0.3s ease, box-shadow 0.3s ease !important;
            overflow: hidden !important;
        }
        #{$gallery_id} .wp-block-image img {
            transition: transform 0.3s ease !important;
        }
    ";
    
    if (in_array('lift', $hover_effects)) {
        $styles[] = "
            #{$gallery_id} .wp-block-image:hover {
                transform: translateY(-5px) !important;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            }
        ";
    }
    
    if (in_array('zoom', $hover_effects)) {
        $styles[] = "
            #{$gallery_id} .wp-block-image:hover img {
                transform: scale(1.05) !important;
            }
        ";
    }
    
    if (in_array('overlay', $hover_effects)) {
        $styles[] = "
            #{$gallery_id} .wp-block-image a {
                position: relative !important;
                display: block !important;
            }
            #{$gallery_id} .wp-block-image a::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0);
                transition: background 0.3s ease;
                border-radius: {$border_radius}px;
            }
            #{$gallery_id} .wp-block-image:hover a::after {
                background: rgba(0, 0, 0, 0.2);
            }
        ";
    }
}

$inline_styles = implode('', $styles);
?>

<!-- Styles dynamiques -->
<style>
<?php echo $inline_styles; ?>
</style>

<!-- Structure de la section galerie -->
<div class="<?php echo esc_attr(implode(' ', $block_classes)); ?>" id="<?php echo esc_attr($gallery_id . '-wrapper'); ?>">
    
    <!-- Container de la section -->
    <div class="wp-block-group has-base-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--x-large);padding-right:var(--wp--preset--spacing--medium);padding-bottom:var(--wp--preset--spacing--x-large);padding-left:var(--wp--preset--spacing--medium)">
        
        <!-- Titre de la section -->
        <h2 class="wp-block-heading has-text-align-center has-contrast-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--large);font-size:2.5rem;font-weight:700;line-height:1.2">
            <?php echo esc_html($section_title); ?>
        </h2>
        
        <!-- Description -->
        <p class="has-text-align-center has-contrast-2-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--x-large);font-size:1.125rem;line-height:1.6">
            <?php echo esc_html($section_description); ?>
        </p>
        
        <!-- Galerie avec réglages -->
        <div 
            id="<?php echo esc_attr($gallery_id); ?>"
            class="<?php echo esc_attr(implode(' ', $gallery_classes)); ?>"
            data-gallery-id="<?php echo esc_attr($gallery_id); ?>"
            data-is-preview="<?php echo $is_preview ? 'true' : 'false'; ?>"
            <?php foreach ($data_attributes as $attr => $value): ?>
                <?php echo esc_attr($attr); ?>="<?php echo esc_attr($value); ?>"
            <?php endforeach; ?>
        >
            <?php foreach ($images as $index => $image): ?>
                <figure class="wp-block-image">
                    <a href="<?php echo esc_url($image['url']); ?>" 
                       data-lightbox="gallery-<?php echo esc_attr($gallery_id); ?>"
                       data-title="<?php echo esc_attr($image['alt']); ?>">
                        <img 
                            src="<?php echo esc_url($image['sizes']['large'] ?: $image['url']); ?>"
                            alt="<?php echo esc_attr($image['alt']); ?>"
                            title="<?php echo esc_attr($image['title']); ?>"
                            loading="lazy"
                        />
                    </a>
                    
                    <?php if (!empty($image['caption'])): ?>
                        <figcaption class="wp-element-caption">
                            <?php echo wp_kses_post($image['caption']); ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>

        <!-- Bouton "Voir toute la galerie" -->
        <div class="wp-block-buttons is-content-justification-center" style="margin-top:var(--wp--preset--spacing--large)">
            <div class="wp-block-button">
                <a class="wp-block-button__link wp-element-button" href="#" style="border-radius:8px;background-color:#0066cc;padding-top:12px;padding-right:24px;padding-bottom:12px;padding-left:24px">
                    Voir toute la galerie
                </a>
            </div>
        </div>
    </div>
</div>

