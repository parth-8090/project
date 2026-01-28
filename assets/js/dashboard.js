// Dashboard JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate stats cards
    gsap.from('.stat-card', {
        duration: 0.8,
        y: 30,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });

    // Animate quick access cards
    gsap.from('.card', {
        duration: 0.8,
        y: 30,
        opacity: 0,
        stagger: 0.05,
        delay: 0.3,
        ease: 'power3.out'
    });

    // Counter animation for stats
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target')) || 0;
        
        // Only animate if target is greater than 0
        if (target > 0) {
            let obj = { val: 0 };
            gsap.to(obj, {
                val: target,
                duration: 2,
                ease: 'power2.out',
                onUpdate: function() {
                    counter.textContent = Math.floor(obj.val);
                }
            });
        } else {
            counter.textContent = target;
        }
    });

    // Hover effects for stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            gsap.to(this, {
                y: -5,
                boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                duration: 0.3
            });
        });
        
        card.addEventListener('mouseleave', function() {
            gsap.to(this, {
                y: 0,
                boxShadow: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                duration: 0.3
            });
        });
    });
});
