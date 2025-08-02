/**
 * JavaScript pour les blocs ACF côté frontend
 * Gestion des badges de prévisualisation et interactions spécifiques
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // =============================================================================
    // BADGES DE PRÉVISUALISATION POUR LES BLOCS ACF
    // =============================================================================
    
    // Fonction générale pour créer un badge de prévisualisation
    function createPreviewBadge(text) {
        const badge = document.createElement('div');
        badge.className = 'preview-badge acf-block-badge';
        badge.innerHTML = text;
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        `;
        return badge;
    }
    
    // Ajouter un badge à un wrapper s'il n'existe pas déjà
    function addBadgeToWrapper(wrapper, badgeText) {
        if (wrapper && !wrapper.querySelector('.preview-badge')) {
            const badge = createPreviewBadge(badgeText);
            wrapper.style.position = 'relative';
            wrapper.appendChild(badge);
            
            // Animation d'apparition
            setTimeout(() => {
                badge.style.opacity = '0';
                badge.style.transform = 'scale(0.8)';
                badge.style.transition = 'all 0.3s ease';
                
                requestAnimationFrame(() => {
                    badge.style.opacity = '1';
                    badge.style.transform = 'scale(1)';
                });
            }, 100);
        }
    }
    
    // Gérer tous les badges de prévisualisation des blocs ACF
    function initializeACFPreviewBadges() {
        // Configuration des badges par type de bloc
        const badgeConfigs = [
            {
                selector: '.posts-grid[data-is-preview="true"]',
                idAttr: 'data-section-id',
                text: '📝 Aperçu articles',
                suffix: '-wrapper'
            },
            {
                selector: '.counters-grid[data-is-preview="true"]',
                idAttr: 'data-section-id',
                text: '📊 Aperçu compteurs',
                suffix: '-wrapper'
            },
            {
                selector: '.gallery-grid[data-is-preview="true"]',
                idAttr: 'data-gallery-id',
                text: '👁️ Aperçu galerie',
                suffix: '-wrapper'
            }
        ];
        
        badgeConfigs.forEach(config => {
            const elements = document.querySelectorAll(config.selector);
            elements.forEach(element => {
                const id = element.getAttribute(config.idAttr);
                const wrapper = document.getElementById(id + config.suffix);
                addBadgeToWrapper(wrapper, config.text);
            });
        });
        
        console.log('🎯 Badges de prévisualisation ACF initialisés');
    }
    
    // =============================================================================
    // GESTION DES MUTATIONS POUR LES NOUVEAUX BLOCS
    // =============================================================================
    
    // Observer pour détecter les nouveaux blocs ajoutés dynamiquement
    const acfBlocksObserver = new MutationObserver((mutations) => {
        let hasNewBlocks = false;
        
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    // Vérifier s'il y a de nouveaux blocs ACF
                    const hasACFBlocks = node.classList?.contains('posts-grid') ||
                                       node.classList?.contains('counters-grid') ||
                                       node.classList?.contains('gallery-grid') ||
                                       node.querySelector?.('.posts-grid, .counters-grid, .gallery-grid');
                    
                    if (hasACFBlocks) {
                        hasNewBlocks = true;
                    }
                }
            });
        });
        
        if (hasNewBlocks) {
            setTimeout(() => {
                initializeACFPreviewBadges();
            }, 100);
        }
    });
    
    // Démarrer l'observation
    acfBlocksObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // =============================================================================
    // INTERACTIONS SPÉCIFIQUES AUX BLOCS ACF
    // =============================================================================
    
    // Gestion des effets de hover pour les blocs en mode preview
    function setupACFBlockHoverEffects() {
        const previewBlocks = document.querySelectorAll('[data-is-preview="true"]');
        
        previewBlocks.forEach(block => {
            const wrapper = block.closest('[id$="-wrapper"]');
            if (wrapper) {
                wrapper.addEventListener('mouseenter', function() {
                    const badge = this.querySelector('.preview-badge');
                    if (badge) {
                        badge.style.transform = 'scale(1.05)';
                        badge.style.background = 'rgba(0, 122, 204, 1)';
                    }
                });
                
                wrapper.addEventListener('mouseleave', function() {
                    const badge = this.querySelector('.preview-badge');
                    if (badge) {
                        badge.style.transform = 'scale(1)';
                        badge.style.background = 'rgba(0, 122, 204, 0.9)';
                    }
                });
            }
        });
    }
    
    // =============================================================================
    // INITIALISATION
    // =============================================================================
    
    // Initialiser tous les badges de prévisualisation
    initializeACFPreviewBadges();
    
    // Configurer les effets de hover
    setupACFBlockHoverEffects();
    
    // Debug
    if (window.location.search.includes('debug=acf-blocks')) {
        console.log('🔧 Mode debug ACF Blocks activé');
        console.log('📊 Blocs compteurs:', document.querySelectorAll('.counters-grid').length);
        console.log('📝 Blocs articles:', document.querySelectorAll('.posts-grid').length);
        console.log('👁️ Blocs galerie:', document.querySelectorAll('.gallery-grid').length);
    }
    
    console.log('🚀 ACF Blocks Frontend initialisé');
});

// =============================================================================
// UTILITAIRES GLOBAUX POUR LES BLOCS ACF
// =============================================================================

// Fonction utilitaire pour obtenir l'ID d'un bloc ACF
window.getACFBlockId = function(element) {
    return element.getAttribute('data-section-id') || 
           element.getAttribute('data-gallery-id') || 
           element.id;
};

// Fonction utilitaire pour vérifier si on est en mode preview
window.isACFBlockPreview = function(element) {
    return element.getAttribute('data-is-preview') === 'true';
};