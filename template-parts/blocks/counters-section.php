<?php
/**
 * Template pour le bloc ACF Section Compteurs
 */

if (!defined('ABSPATH')) {
    exit;
}

// Récupérer les champs ACF
$section_title = get_field('counters_title') ?: 'Nos Résultats en Chiffres';
$section_description = get_field('counters_description') ?: 'Découvrez nos accomplissements et notre expertise à travers ces statistiques';
$counters = get_field('counters_list');
$columns = get_field('counters_columns') ?: '4';
$animation_speed = get_field('counters_animation_speed') ?: 2000;
$color_scheme = get_field('counters_color_scheme') ?: 'primary';

// Vérifier si on est en mode preview dans l'éditeur
$is_preview = isset($block['data']['_is_preview']) && $block['data']['_is_preview'];

// Générer un ID unique pour cette section
$section_id = 'counters-section-' . uniqid();

// Classes CSS de base
$block_classes = array('counters-section-block');
if (!empty($block['className'])) {
    $block_classes[] = $block['className'];
}

// Classes pour la grille de compteurs
$grid_classes = array(
    'counters-grid',
    'columns-' . $columns,
    'color-scheme-' . $color_scheme
);

// Si pas de compteurs, afficher un placeholder en mode preview
if (!$counters) {
    if ($is_preview) {
        echo '<div class="counters-section-placeholder" style="padding: 40px; border: 2px dashed #007acc; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
                <h3 style="margin: 0 0 8px 0; color: #333;">Section Compteurs</h3>
                <p style="margin: 0; font-size: 14px;">Ajoutez des compteurs dans les réglages →</p>
              </div>';
    }
    return;
}

// Fonction pour obtenir l'icône SVG
function get_counter_icon_svg($icon_name) {
    $icons = array(
        'briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="m16 21-4-4-4 4"></path><path d="m9 7v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"></path></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="m22 21-3-3 3-3"></path></svg>',
        'trophy' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55.47.98.97 1.21C14.02 18.68 16 20.08 16 22"></path><path d="M7 8h10"></path><path d="M7 8a4 4 0 1 0 8 0c0-1.5.5-4-4-4s-4 2.5-4 4"></path></svg>',
        'star' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"></polygon></svg>',
        'heart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>',
        'thumbs-up' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 10v12"></path><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h3.73a2 2 0 0 1 1.92 2.56z"></path></svg>',
        'check-circle' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22,4 12,14.01 9,11.01"></polyline></svg>',
        'target' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>',
        'trending-up' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,7 13.5,15.5 8.5,10.5 2,17"></polyline><polyline points="16,7 22,7 22,13"></polyline></svg>',
        'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12,6 12,12 16,14"></polyline></svg>',
        'globe' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>',
        'coffee' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>'
    );
    
    return isset($icons[$icon_name]) ? $icons[$icon_name] : $icons['briefcase'];
}

// Attributs data pour JavaScript
$data_attributes = array(
    'data-animation-speed' => $animation_speed,
    'data-color-scheme' => $color_scheme,
);
?>

<!-- Structure de la section compteurs -->
<div class="<?php echo esc_attr(implode(' ', $block_classes)); ?>" id="<?php echo esc_attr($section_id . '-wrapper'); ?>">
    
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
        
        <!-- Grille de compteurs -->
        <div 
            id="<?php echo esc_attr($section_id); ?>"
            class="<?php echo esc_attr(implode(' ', $grid_classes)); ?>"
            <?php foreach ($data_attributes as $attr => $value): ?>
                <?php echo esc_attr($attr); ?>="<?php echo esc_attr($value); ?>"
            <?php endforeach; ?>
        >
            <?php foreach ($counters as $index => $counter): ?>
                <div class="counter-item" data-target="<?php echo esc_attr($counter['counter_number']); ?>">
                    
                    <?php if (!empty($counter['counter_icon'])): ?>
                        <div class="counter-icon">
                            <?php echo get_counter_icon_svg($counter['counter_icon']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="counter-content">
                        <div class="counter-number-wrapper">
                            <span class="counter-number" data-start="0" data-end="<?php echo esc_attr($counter['counter_number']); ?>">0</span>
                            <?php if (!empty($counter['counter_suffix'])): ?>
                                <span class="counter-suffix"><?php echo esc_html($counter['counter_suffix']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="counter-label"><?php echo esc_html($counter['counter_label']); ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if ($is_preview): ?>
<!-- Indicateur preview pour l'éditeur -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('<?php echo esc_js($section_id . '-wrapper'); ?>');
    if (wrapper && !wrapper.querySelector('.preview-badge')) {
        const badge = document.createElement('div');
        badge.className = 'preview-badge';
        badge.innerHTML = '📊 Aperçu compteurs';
        badge.style.cssText = `
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 122, 204, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            z-index: 10;
            pointer-events: none;
        `;
        wrapper.style.position = 'relative';
        wrapper.appendChild(badge);
    }
});
</script>
<?php endif; ?>