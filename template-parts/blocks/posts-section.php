<?php
/**
 * Template pour le bloc ACF Section Articles
 */

if (!defined('ABSPATH')) {
    exit;
}

// Récupérer les champs ACF
$section_title = get_field('section_title') ?: 'Nos Derniers Articles';
$section_description = get_field('section_description') ?: 'Découvrez nos dernières actualités et articles de blog';
$posts_count = get_field('posts_count') ?: 6;
$posts_columns = get_field('posts_columns') ?: '3';
$posts_category = get_field('posts_category');
$posts_order = get_field('posts_order') ?: 'date_desc';
$posts_style = get_field('posts_style') ?: 'modern';
$show_excerpt = get_field('show_excerpt') ?? true;
$show_author = get_field('show_author') ?? true;
$show_date = get_field('show_date') ?? true;
$show_category = get_field('show_category') ?? true;
$show_cta_button = get_field('show_cta_button') ?? true;
$cta_button_text = get_field('cta_button_text') ?: 'Voir tous les articles';

// Vérifier si on est en mode preview dans l'éditeur
$is_preview = isset($block['data']['_is_preview']) && $block['data']['_is_preview'];

// Générer un ID unique pour cette section
$section_id = 'posts-section-' . uniqid();

// Classes CSS de base
$block_classes = array('posts-section-block');
if (!empty($block['className'])) {
    $block_classes[] = $block['className'];
}

// Classes pour la grille
$grid_classes = array(
    'posts-grid',
    'columns-' . $posts_columns,
    'style-' . $posts_style
);

// Configuration de la requête
$query_args = array(
    'post_type' => 'post',
    'posts_per_page' => $posts_count,
    'post_status' => 'publish',
    'ignore_sticky_posts' => true,
);

// Filtrer par catégorie si sélectionnée
if ($posts_category) {
    $query_args['cat'] = $posts_category;
}

// Ordre d'affichage
switch ($posts_order) {
    case 'date_asc':
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'ASC';
        break;
    case 'title_asc':
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'ASC';
        break;
    case 'title_desc':
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'DESC';
        break;
    case 'comment_count':
        $query_args['orderby'] = 'comment_count';
        $query_args['order'] = 'DESC';
        break;
    case 'rand':
        $query_args['orderby'] = 'rand';
        break;
    default: // date_desc
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'DESC';
}

// Exécuter la requête
$posts_query = new WP_Query($query_args);

// Si pas d'articles, afficher un placeholder en mode preview
if (!$posts_query->have_posts()) {
    if ($is_preview) {
        echo '<div class="posts-section-placeholder" style="padding: 40px; border: 2px dashed #007acc; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 48px; margin-bottom: 16px;">📝</div>
                <h3 style="margin: 0 0 8px 0; color: #333;">Section Articles</h3>
                <p style="margin: 0; font-size: 14px;">Aucun article trouvé ou ajoutez du contenu →</p>
              </div>';
    }
    return;
}

// Fonction pour formater la date en français
function format_french_date($date) {
    $months = array(
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
    );
    
    $day = date('j', strtotime($date));
    $month = $months[(int)date('n', strtotime($date))];
    $year = date('Y', strtotime($date));
    
    return "$day $month $year";
}
?>

<!-- Structure de la section articles -->
<div class="<?php echo esc_attr(implode(' ', $block_classes)); ?>" id="<?php echo esc_attr($section_id . '-wrapper'); ?>">
    
    <!-- Container de la section -->
    <div class="wp-block-group has-base-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--x-large);padding-right:var(--wp--preset--spacing--medium);padding-bottom:var(--wp--preset--spacing--x-large);padding-left:var(--wp--preset--spacing--medium)">
        
        <!-- Titre de la section -->
        <h2 class="wp-block-heading has-text-align-center has-contrast-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--medium);font-size:2.5rem;font-weight:700;line-height:1.2">
            <?php echo esc_html($section_title); ?>
        </h2>
        
        <!-- Description -->
        <?php if ($section_description): ?>
            <p class="has-text-align-center has-contrast-2-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--x-large);font-size:1.125rem;line-height:1.6">
                <?php echo esc_html($section_description); ?>
            </p>
        <?php endif; ?>
        
        <!-- Grille d'articles -->
        <div 
            id="<?php echo esc_attr($section_id); ?>"
            class="<?php echo esc_attr(implode(' ', $grid_classes)); ?>"
            data-section-id="<?php echo esc_attr($section_id); ?>"
            data-is-preview="<?php echo $is_preview ? 'true' : 'false'; ?>"
        >
            <?php while ($posts_query->have_posts()): $posts_query->the_post(); ?>
                <article class="post-card" data-post-id="<?php echo get_the_ID(); ?>">
                    
                    <!-- Image de l'article -->
                    <?php if (has_post_thumbnail()): ?>
                        <div class="post-card-image">
                            <a href="<?php echo esc_url(get_permalink()); ?>" aria-label="<?php echo esc_attr('Lire l\'article : ' . get_the_title()); ?>">
                                <?php the_post_thumbnail('medium_large', array(
                                    'loading' => 'lazy',
                                    'alt' => get_the_title()
                                )); ?>
                                
                                <?php if ($posts_style === 'overlay'): ?>
                                    <div class="post-card-overlay">
                                        <span class="read-more-text">Lire l'article</span>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Contenu de la carte -->
                    <div class="post-card-content">
                        
                        <!-- Métadonnées du haut -->
                        <div class="post-card-meta-top">
                            <?php if ($show_category): 
                                $categories = get_the_category();
                                if (!empty($categories)): ?>
                                    <span class="post-category">
                                        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </a>
                                    </span>
                                <?php endif;
                            endif; ?>
                            
                            <?php if ($show_date): ?>
                                <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo format_french_date(get_the_date()); ?>
                                </time>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Titre de l'article -->
                        <h3 class="post-card-title">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        
                        <!-- Extrait -->
                        <?php if ($show_excerpt): ?>
                            <div class="post-card-excerpt">
                                <?php 
                                $excerpt = get_the_excerpt();
                                if (strlen($excerpt) > 120) {
                                    $excerpt = substr($excerpt, 0, 120) . '...';
                                }
                                echo wp_kses_post($excerpt);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Métadonnées du bas -->
                        <div class="post-card-meta-bottom">
                            <?php if ($show_author): ?>
                                <div class="post-author">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 24, '', '', array('class' => 'author-avatar')); ?>
                                    <span class="author-name">
                                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                            <?php the_author(); ?>
                                        </a>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-actions">
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="read-more-link">
                                    Lire la suite
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <!-- Bouton "Voir plus" -->
        <?php if ($show_cta_button): ?>
            <div class="wp-block-buttons is-content-justification-center" style="margin-top:var(--wp--preset--spacing--large)">
                <div class="wp-block-button">
                    <a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" style="border-radius:8px;background-color:#0066cc;padding-top:12px;padding-right:24px;padding-bottom:12px;padding-left:24px">
                        <?php echo esc_html($cta_button_text); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>



<?php
// Restaurer les données globales de post
wp_reset_postdata();
?>