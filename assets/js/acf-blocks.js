/**
 * Améliorations pour les blocs ACF
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Attendre que l'éditeur soit chargé
    if (window.wp && window.wp.data && window.wp.data.select) {
        
        // Observer les changements dans l'éditeur
        const { subscribe, select } = wp.data;
        
        let previousBlocks = [];
        
        // S'abonner aux changements de blocs
        subscribe(() => {
            const blocks = select('core/block-editor').getBlocks();
            
            // Vérifier s'il y a de nouveaux blocs ACF
            if (blocks.length !== previousBlocks.length) {
                setTimeout(() => {
                    enhanceACFBlocks();
                }, 100);
            }
            
            previousBlocks = blocks;
        });
        
        // Améliorer les blocs ACF existants
        setTimeout(() => {
            enhanceACFBlocks();
        }, 1000);
    }
    
    // Fonction pour améliorer les blocs ACF
    function enhanceACFBlocks() {
        
        // Chercher tous les blocs ACF Section Galerie
        const galleryBlocks = document.querySelectorAll('[data-type="acf/gallery-section"]');
        
        galleryBlocks.forEach(block => {
            enhanceGalleryBlock(block);
        });
        
        // Ajouter des styles pour l'éditeur
        addEditorStyles();
    }
    
    // Améliorer un bloc galerie spécifique
    function enhanceGalleryBlock(block) {
        
        // Ajouter une classe pour identifier les blocs améliorés
        if (!block.classList.contains('acf-enhanced')) {
            block.classList.add('acf-enhanced');
            
            // Ajouter un indicateur visuel
            addVisualIndicator(block);
            
            // Observer les changements dans les champs ACF
            observeACFChanges(block);
        }
    }
    
    // Ajouter un indicateur visuel
    function addVisualIndicator(block) {
        
        // Vérifier si l'indicateur n'existe pas déjà
        if (block.querySelector('.acf-block-indicator')) {
            return;
        }
        
        const indicator = document.createElement('div');
        indicator.className = 'acf-block-indicator';
        indicator.innerHTML = '⚡ ACF Pro';
        indicator.style.cssText = `
            position: absolute;
            top: -8px;
            left: -8px;
            background: linear-gradient(45deg, #00d4aa, #007acc);
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            z-index: 1000;
            pointer-events: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        `;
        
        // Positionner le bloc de manière relative
        block.style.position = 'relative';
        block.appendChild(indicator);
    }
    
    // Observer les changements dans les champs ACF
    function observeACFChanges(block) {
        
        // Observer les changements dans les champs
        const acfFields = block.querySelectorAll('.acf-field input, .acf-field select, .acf-field textarea');
        
        acfFields.forEach(field => {
            field.addEventListener('change', () => {
                // Ajouter un effet visuel pour indiquer le changement
                showChangeIndicator(block);
            });
        });
        
        // Observer aussi les changements sur les boutons radio
        const radioButtons = block.querySelectorAll('.acf-field input[type="radio"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                showChangeIndicator(block);
            });
        });
        
        // Observer les sliders de range
        const rangeSliders = block.querySelectorAll('.acf-field input[type="range"]');
        rangeSliders.forEach(slider => {
            slider.addEventListener('input', () => {
                showChangeIndicator(block);
            });
        });
    }
    
    // Afficher un indicateur de changement
    function showChangeIndicator(block) {
        
        // Créer un effet de pulsation
        const pulse = document.createElement('div');
        pulse.className = 'change-pulse';
        pulse.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            background: rgba(0, 212, 170, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 0.6s ease-out;
            pointer-events: none;
            z-index: 999;
        `;
        
        block.appendChild(pulse);
        
        // Supprimer après l'animation
        setTimeout(() => {
            if (pulse.parentNode) {
                pulse.parentNode.removeChild(pulse);
            }
        }, 600);
        
        // Effet de bordure temporaire
        const originalBorder = block.style.border;
        block.style.border = '2px solid #00d4aa';
        block.style.transition = 'border 0.3s ease';
        
        setTimeout(() => {
            block.style.border = originalBorder;
        }, 300);
    }
    
    // Ajouter des styles pour l'éditeur
    function addEditorStyles() {
        
        if (document.getElementById('acf-blocks-styles')) {
            return;
        }
        
        const style = document.createElement('style');
        style.id = 'acf-blocks-styles';
        style.textContent = `
            /* Styles pour les blocs ACF améliorés */
            .acf-enhanced {
                transition: all 0.3s ease;
            }
            
            .acf-enhanced:hover {
                box-shadow: 0 4px 12px rgba(0, 122, 204, 0.15);
            }
            
            .acf-enhanced.is-selected {
                box-shadow: 0 0 0 2px #007acc;
            }
            
            /* Animation de pulsation */
            @keyframes pulse {
                0% {
                    transform: translate(-50%, -50%) scale(0);
                    opacity: 1;
                }
                100% {
                    transform: translate(-50%, -50%) scale(4);
                    opacity: 0;
                }
            }
            
            /* Indicateur de bloc ACF */
            .acf-block-indicator {
                animation: fadeIn 0.5s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.8); }
                to { opacity: 1; transform: scale(1); }
            }
            
            /* Amélioration des champs ACF dans l'éditeur */
            .acf-field .acf-label {
                font-weight: 600;
                color: #1e1e1e;
            }
            
            .acf-field .acf-input input[type="range"] {
                width: 100%;
                margin: 8px 0;
            }
            
            .acf-field .acf-input input[type="radio"] {
                margin-right: 8px;
            }
            
            /* Messages d'aide améliorés */
            .acf-field .description {
                font-size: 12px;
                color: #666;
                font-style: italic;
                margin-top: 4px;
            }
            
            /* Groupement visuel des champs */
            .acf-fields > .acf-field {
                border-bottom: 1px solid #f0f0f0;
                padding-bottom: 12px;
                margin-bottom: 12px;
            }
            
            .acf-fields > .acf-field:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }
        `;
        
        document.head.appendChild(style);
    }
    
    // Ajouter des améliorations pour les tooltips
    function addTooltips() {
        
        const acfLabels = document.querySelectorAll('.acf-field .acf-label label');
        
        acfLabels.forEach(label => {
            const field = label.closest('.acf-field');
            const description = field?.querySelector('.description');
            
            if (description) {
                label.title = description.textContent.trim();
                label.style.cursor = 'help';
            }
        });
    }
    
    // Initialiser les améliorations
    setTimeout(() => {
        addTooltips();
    }, 1500);
    
    // Observer les nouveaux éléments ajoutés
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    // Vérifier s'il y a de nouveaux blocs ACF
                    const acfBlocks = node.classList?.contains('acf-block-component') 
                        ? [node] 
                        : node.querySelectorAll?.('.acf-block-component') || [];
                    
                    if (acfBlocks.length > 0) {
                        setTimeout(() => {
                            enhanceACFBlocks();
                            addTooltips();
                        }, 100);
                    }
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    console.log('🚀 ACF Blocks enhancements loaded');
});