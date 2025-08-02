/**
 * JavaScript principal du thème moderne
 * Toutes les interactions en vanilla JavaScript (sans jQuery)
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // =============================================================================
    // NAVIGATION MOBILE
    // =============================================================================
    
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const searchToggle = document.querySelector('.search-toggle');
    const searchContainer = document.querySelector('.search-form-container');
    
    // Toggle menu mobile
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');
            
            // Animation de l'icône
            const icon = this.querySelector('svg');
            if (icon) {
                icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(90deg)';
            }
        });
    }
    
    // Toggle recherche
    if (searchToggle && searchContainer) {
        searchToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            searchContainer.classList.toggle('hidden');
            
            // Focus sur le champ de recherche
            if (!isExpanded) {
                const searchField = searchContainer.querySelector('.search-field');
                if (searchField) {
                    setTimeout(() => searchField.focus(), 100);
                }
            }
        });
    }
    
    // Fermer les menus en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (mobileMenu && !mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
            mobileMenu.classList.add('hidden');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }
        
        if (searchContainer && !searchContainer.contains(e.target) && !searchToggle.contains(e.target)) {
            searchContainer.classList.add('hidden');
            searchToggle.setAttribute('aria-expanded', 'false');
        }
    });
    
    // =============================================================================
    // SMOOTH SCROLLING POUR LES ANCRES
    // =============================================================================
    
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Ignorer les liens vides ou "#"
            if (href === '#' || href === '#!') {
                return;
            }
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                
                const headerHeight = document.querySelector('.site-header')?.offsetHeight || 0;
                const targetPosition = target.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // // =============================================================================
    // // HEADER STICKY AU SCROLL
    // // =============================================================================
    
    // const siteHeader = document.querySelector('.site-header');
    // let lastScrollTop = 0;
    
    // if (siteHeader) {
    //     window.addEventListener('scroll', function() {
    //         const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
    //         // Ajouter/retirer classe scrolled
    //         if (scrollTop > 100) {
    //             siteHeader.classList.add('scrolled');
    //         } else {
    //             siteHeader.classList.remove('scrolled');
    //         }
            
    //         // Auto-hide sur scroll down (optionnel)
    //         if (scrollTop > lastScrollTop && scrollTop > 200) {
    //             siteHeader.style.transform = 'translateY(-100%)';
    //         } else {
    //             siteHeader.style.transform = 'translateY(0)';
    //         }
            
    //         lastScrollTop = scrollTop;
    //     });
    // }
    
    // =============================================================================
    // LAZY LOADING IMAGES
    // =============================================================================
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    
                    if (src) {
                        img.setAttribute('src', src);
                        img.removeAttribute('data-src');
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // =============================================================================
    // ANIMATIONS AU SCROLL
    // =============================================================================
    
    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        animatedElements.forEach(el => animationObserver.observe(el));
    }
    
    // =============================================================================
    // COMPTEURS ANIMÉS
    // =============================================================================
    
    function animateCounter(element, start, end, duration) {
        const startTime = performance.now();
        const range = end - start;
        
        function updateCounter(currentTime) {
            const elapsedTime = currentTime - startTime;
            const progress = Math.min(elapsedTime / duration, 1);
            
            // Fonction d'easing out cubic
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            const currentValue = Math.round(start + (range * easeOutCubic));
            
            // Formater le nombre avec des espaces pour les milliers
            const formattedValue = currentValue.toLocaleString('fr-FR');
            element.textContent = formattedValue;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.classList.remove('counting');
            }
        }
        
        element.classList.add('counting');
        requestAnimationFrame(updateCounter);
    }
    
    // Observer pour déclencher l'animation des compteurs
    if ('IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counterSection = entry.target;
                    const animationSpeed = parseInt(counterSection.getAttribute('data-animation-speed')) || 2000;
                    
                    // Animer chaque compteur individuellement
                    const counters = counterSection.querySelectorAll('.counter-number');
                    
                    counters.forEach((counter, index) => {
                        const start = parseInt(counter.getAttribute('data-start')) || 0;
                        const end = parseInt(counter.getAttribute('data-end')) || 0;
                        
                        // Délai progressif pour un effet de cascade
                        setTimeout(() => {
                            animateCounter(counter, start, end, animationSpeed);
                        }, index * 200);
                    });
                    
                    // Animer l'apparition des items
                    const counterItems = counterSection.querySelectorAll('.counter-item');
                    counterItems.forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('animate-in');
                        }, index * 100);
                    });
                    
                    // Ne déclencher qu'une seule fois
                    counterObserver.unobserve(counterSection);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -100px 0px'
        });
        
        // Observer toutes les sections de compteurs
        const counterSections = document.querySelectorAll('.counters-grid');
        counterSections.forEach(section => {
            counterObserver.observe(section);
        });
    }
    
    // =============================================================================
    // ANIMATIONS CARTES D'ARTICLES
    // =============================================================================
    
    // Observer pour animer l'apparition des cartes d'articles
    if ('IntersectionObserver' in window) {
        const postsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const postCard = entry.target;
                    
                    // Délai basé sur l'index de la carte
                    const cards = Array.from(postCard.parentNode.children);
                    const index = cards.indexOf(postCard);
                    const delay = index * 100; // 100ms entre chaque carte
                    
                    setTimeout(() => {
                        postCard.classList.add('animate-in');
                    }, delay);
                    
                    // Ne déclencher qu'une seule fois
                    postsObserver.unobserve(postCard);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        // Observer toutes les cartes d'articles
        const postCards = document.querySelectorAll('.post-card');
        postCards.forEach(card => {
            postsObserver.observe(card);
        });
    }
    
    // Gestion du chargement des images des cartes
    function setupPostCardImages() {
        const postImages = document.querySelectorAll('.post-card-image img');
        
        postImages.forEach(img => {
            if (img.complete) {
                img.classList.add('loaded');
            } else {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
                
                img.addEventListener('error', function() {
                    this.style.display = 'none';
                    // Optionnel : ajouter une image de placeholder
                });
            }
        });
    }
    
    // Initialiser les images des cartes
    setupPostCardImages();
    
    // =============================================================================
    // FORMULAIRES
    // =============================================================================
    
    // Validation basique des formulaires
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    
                    // Retirer l'erreur au focus
                    field.addEventListener('focus', function() {
                        this.classList.remove('error');
                    }, { once: true });
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Afficher un message d'erreur
                let errorMessage = form.querySelector('.form-error');
                if (!errorMessage) {
                    errorMessage = document.createElement('div');
                    errorMessage.className = 'form-error text-red-600 text-sm mt-2';
                    errorMessage.textContent = 'Veuillez remplir tous les champs obligatoires.';
                    form.insertBefore(errorMessage, form.firstChild);
                }
                
                // Supprimer le message après 5 secondes
                setTimeout(() => {
                    if (errorMessage.parentNode) {
                        errorMessage.remove();
                    }
                }, 5000);
            }
        });
    });
    
    // =============================================================================
    // BACK TO TOP
    // =============================================================================
    
    // Créer le bouton back to top
    const backToTop = document.createElement('button');
    backToTop.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    `;
    backToTop.className = 'back-to-top fixed bottom-8 right-8 z-50 btn btn-primary btn-icon-only opacity-0 pointer-events-none transition-opacity';
    backToTop.setAttribute('aria-label', 'Retour en haut');
    document.body.appendChild(backToTop);
    
    // Afficher/masquer le bouton
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTop.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            backToTop.classList.add('opacity-0', 'pointer-events-none');
        }
    });
    
    // Action du bouton
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // =============================================================================
    // PERFORMANCE
    // =============================================================================
    
    // Préchargement des liens au hover
    const links = document.querySelectorAll('a[href^="/"], a[href^="' + window.location.origin + '"]');
    
    links.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const href = this.href;
            
            // Éviter de précharger plusieurs fois le même lien
            if (href && !document.querySelector(`link[rel="prefetch"][href="${href}"]`)) {
                const prefetchLink = document.createElement('link');
                prefetchLink.rel = 'prefetch';
                prefetchLink.href = href;
                document.head.appendChild(prefetchLink);
            }
        });
    });
    
    // =============================================================================
    // ACCESSIBILITÉ
    // =============================================================================
    
    // Gestion du focus visible pour l'accessibilité
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('user-is-tabbing');
        }
    });
    
    document.addEventListener('mousedown', function() {
        document.body.classList.remove('user-is-tabbing');
    });
    
    // Piège de focus pour les modales (si présentes)
    const modals = document.querySelectorAll('.modal, .overlay');
    
    modals.forEach(modal => {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length > 0) {
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            modal.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstElement) {
                            lastElement.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastElement) {
                            firstElement.focus();
                            e.preventDefault();
                        }
                    }
                }
                
                if (e.key === 'Escape') {
                    // Fermer la modale si elle a un bouton de fermeture
                    const closeBtn = modal.querySelector('.modal-close, [data-dismiss="modal"]');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }
            });
        }
    });
    
    console.log('🎉 Thème moderne initialisé avec succès !');
});