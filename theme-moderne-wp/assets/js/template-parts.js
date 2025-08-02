/**
 * Script pour personnaliser les template-parts WordPress
 * Ajoute des classes personnalisées et gère les interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Fonction principale pour personnaliser les template-parts
    function customizeTemplateParts() {
        const templateParts = document.querySelectorAll('.wp-block-template-part');
        
        templateParts.forEach(part => {
            const area = part.getAttribute('data-area');
            const slug = part.getAttribute('data-template-slug');
            
            // Ajouter des classes selon la zone
            switch(area) {
                case 'header':
                    part.classList.add('site-header', 'main-header');
                    break;
                case 'footer':
                    part.classList.add('site-footer', 'main-footer');
                    break;
                default:
                    part.classList.add('custom-template-part');
            }
            
            // Ajouter le slug comme classe si disponible
            if (slug) {
                part.classList.add(`template-${slug}`);
            }
            
            // Debug: afficher les modifications
            if (window.location.search.includes('debug=template-parts')) {
                console.log(`Template-part personnalisé: ${area} (${slug})`, part);
            }
        });
    }
    
    // Gestion du scroll pour le header
    function handleHeaderScroll() {
        const header = document.querySelector('.wp-block-template-part[data-area="header"], .site-header');
        if (!header) return;
        
        let lastScrollY = window.scrollY;
        let isScrolling = false;
        
        function updateHeader() {
            const currentScrollY = window.scrollY;
            
            // Ajouter/retirer la classe scrolled
            if (currentScrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            // Header qui se cache/montre au scroll (optionnel)
            if (currentScrollY > lastScrollY && currentScrollY > 200) {
                header.classList.add('header-hidden');
            } else {
                header.classList.remove('header-hidden');
            }
            
            lastScrollY = currentScrollY;
            isScrolling = false;
        }
        
        window.addEventListener('scroll', () => {
            if (!isScrolling) {
                requestAnimationFrame(updateHeader);
                isScrolling = true;
            }
        }, { passive: true });
    }
    
    // Menu mobile pour le header
    function initMobileMenu() {
        const header = document.querySelector('.wp-block-template-part[data-area="header"], .site-header');
        if (!header) return;
        
        // Chercher le bouton menu et la navigation
        const menuToggle = header.querySelector('.menu-toggle, .wp-block-navigation__responsive-container-open, [aria-label*="Menu"]');
        const navigation = header.querySelector('.wp-block-navigation, .main-navigation, .nav-menu');
        
        if (menuToggle && navigation) {
            menuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                navigation.classList.toggle('active');
                menuToggle.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            });
            
            // Fermer le menu en cliquant à l'extérieur
            document.addEventListener('click', (e) => {
                if (!header.contains(e.target) && navigation.classList.contains('active')) {
                    navigation.classList.remove('active');
                    menuToggle.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
            
            // Fermer le menu avec Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && navigation.classList.contains('active')) {
                    navigation.classList.remove('active');
                    menuToggle.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        }
    }
    
    // Bouton retour en haut pour le footer
    function initBackToTop() {
        // Créer le bouton s'il n'existe pas
        let backToTopBtn = document.querySelector('.back-to-top');
        if (!backToTopBtn) {
            backToTopBtn = document.createElement('button');
            backToTopBtn.className = 'back-to-top';
            backToTopBtn.innerHTML = '↑';
            backToTopBtn.setAttribute('aria-label', 'Retour en haut de page');
            backToTopBtn.setAttribute('title', 'Retour en haut');
            document.body.appendChild(backToTopBtn);
        }
        
        // Afficher/masquer selon le scroll
        function toggleBackToTop() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        }
        
        window.addEventListener('scroll', toggleBackToTop, { passive: true });
        
        // Action du bouton
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Initial check
        toggleBackToTop();
    }
    
    // Amélioration de l'accessibilité
    function enhanceAccessibility() {
        // Ajouter des attributs ARIA manquants
        const templateParts = document.querySelectorAll('.wp-block-template-part');
        
        templateParts.forEach(part => {
            const area = part.getAttribute('data-area');
            
            switch(area) {
                case 'header':
                    if (!part.getAttribute('role')) {
                        part.setAttribute('role', 'banner');
                    }
                    break;
                case 'footer':
                    if (!part.getAttribute('role')) {
                        part.setAttribute('role', 'contentinfo');
                    }
                    break;
            }
        });
        
        // Améliorer les liens sans texte
        const links = document.querySelectorAll('a:not([aria-label]):not([title])');
        links.forEach(link => {
            if (!link.textContent.trim() && link.querySelector('img')) {
                const img = link.querySelector('img');
                if (img.alt) {
                    link.setAttribute('aria-label', img.alt);
                }
            }
        });
    }
    
    // Observer pour détecter les changements de template-parts (pour les thèmes dynamiques)
    function observeTemplateParts() {
        const observer = new MutationObserver((mutations) => {
            let shouldUpdate = false;
            
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1 && 
                        (node.classList.contains('wp-block-template-part') || 
                         node.querySelector('.wp-block-template-part'))) {
                        shouldUpdate = true;
                    }
                });
            });
            
            if (shouldUpdate) {
                customizeTemplateParts();
                enhanceAccessibility();
                // Réinitialiser le menu mobile si nécessaire
                initMobileMenu();
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Fonction utilitaire pour remplacer complètement la classe
    function replaceTemplatePartClass(oldClass = 'wp-block-template-part', newClass = 'custom-template-part') {
        const elements = document.querySelectorAll(`.${oldClass}`);
        elements.forEach(el => {
            el.classList.remove(oldClass);
            el.classList.add(newClass);
        });
        
        return elements.length;
    }
    
    // Animation d'entrée pour les template-parts
    function animateTemplateParts() {
        const templateParts = document.querySelectorAll('.wp-block-template-part');
        
        templateParts.forEach((part, index) => {
            part.style.opacity = '0';
            part.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                part.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                part.style.opacity = '1';
                part.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }
    
    // Initialisation principale
    function init() {
        customizeTemplateParts();
        handleHeaderScroll();
        initMobileMenu();
        initBackToTop();
        enhanceAccessibility();
        observeTemplateParts();
        
        // Animation optionnelle (seulement si pas de mouvement de scroll récent)
        if (window.scrollY < 100) {
            animateTemplateParts();
        }
        
        // Debug info
        if (window.location.search.includes('debug=template-parts')) {
            console.group('🔧 Template-Parts Debug Info');
            console.log('Template-parts détectés:', document.querySelectorAll('.wp-block-template-part'));
            console.log('Headers détectés:', document.querySelectorAll('[data-area="header"]'));
            console.log('Footers détectés:', document.querySelectorAll('[data-area="footer"]'));
            console.groupEnd();
        }
    }
    
    // Démarrer l'initialisation
    init();
    
    // Exporter les fonctions utilitaires pour utilisation globale
    window.themeModerne = window.themeModerne || {};
    window.themeModerne.templateParts = {
        replaceClass: replaceTemplatePartClass,
        customize: customizeTemplateParts,
        init: init
    };
    
    // Événement personnalisé pour signaler que les template-parts sont prêts
    const event = new CustomEvent('templatePartsReady', {
        detail: {
            templateParts: document.querySelectorAll('.wp-block-template-part'),
            timestamp: Date.now()
        }
    });
    document.dispatchEvent(event);
});

// Fonction de fallback au cas où DOMContentLoaded a déjà été déclenché
if (document.readyState === 'loading') {
    // Le script ci-dessus se chargera automatiquement
} else {
    // DOM déjà chargé, exécuter immédiatement
    if (window.themeModerne && window.themeModerne.templateParts) {
        window.themeModerne.templateParts.init();
    }
}