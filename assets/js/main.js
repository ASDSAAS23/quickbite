document.addEventListener('DOMContentLoaded', () => {
    // === Delivery address toggle ===
    const deliveryOptions = document.querySelectorAll('input[name="delivery_method"]');
    const addressGroup = document.querySelector('[data-address-group]');

    const toggleAddress = () => {
        const selected = document.querySelector('input[name="delivery_method"]:checked');
        if (!addressGroup || !selected) return;
        addressGroup.classList.toggle('hidden', selected.value !== 'Delivery');
    };

    deliveryOptions.forEach((option) => option.addEventListener('change', toggleAddress));
    toggleAddress();

    // === Sidebar toggle (logged-in pages) ===
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const appSidebar = document.getElementById('appSidebar');

    // === Mobile drawer toggle (public pages) ===
    const mobileDrawer = document.getElementById('mobileDrawer');
    const drawerClose = document.getElementById('drawerClose');

    const hasSidebar = document.body.classList.contains('has-sidebar');

    const openPanel = () => {
        sidebarOverlay?.classList.add('active');
        if (hasSidebar) {
            appSidebar?.classList.add('active');
        } else {
            mobileDrawer?.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
    };

    const closePanel = () => {
        sidebarOverlay?.classList.remove('active');
        appSidebar?.classList.remove('active');
        mobileDrawer?.classList.remove('active');
        document.body.style.overflow = '';
    };

    hamburgerBtn?.addEventListener('click', openPanel);
    sidebarOverlay?.addEventListener('click', closePanel);
    drawerClose?.addEventListener('click', closePanel);

    // === Scroll to Top ===
    const scrollToTopBtn = document.getElementById('scrollToTop');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            scrollToTopBtn?.classList.add('visible');
        } else {
            scrollToTopBtn?.classList.remove('visible');
        }
    });

    scrollToTopBtn?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // === Featured Slider ===
    const sliderTrack = document.getElementById('sliderTrack');
    const sliderPrev = document.getElementById('sliderPrev');
    const sliderNext = document.getElementById('sliderNext');

    if (sliderTrack && sliderPrev && sliderNext) {
        let offset = 0;
        const cardWidth = 258;

        const getMaxOffset = () => {
            return Math.max(0, sliderTrack.scrollWidth - sliderTrack.parentElement.offsetWidth);
        };

        sliderNext.addEventListener('click', () => {
            offset = Math.min(offset + cardWidth * 2, getMaxOffset());
            sliderTrack.style.transform = `translateX(-${offset}px)`;
        });

        sliderPrev.addEventListener('click', () => {
            offset = Math.max(offset - cardWidth * 2, 0);
            sliderTrack.style.transform = `translateX(-${offset}px)`;
        });
    }

    // === Entrance Animations ===
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    if (animatedElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-up');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        animatedElements.forEach((el) => observer.observe(el));
    }
});
