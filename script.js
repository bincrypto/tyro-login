// ===========================
// Theme Toggle
// ===========================

const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

// Check for saved theme preference or default to 'light'
const currentTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', currentTheme);

if (themeToggle) {
themeToggle.addEventListener('click', () => {
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    // Add a subtle animation
    themeToggle.style.transform = 'rotate(360deg) scale(1.1)';
    setTimeout(() => {
        themeToggle.style.transform = '';
    }, 300);
});
}

// ===========================
// Copy to Clipboard
// ===========================

function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHTML = button.innerHTML;
        button.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        `;

        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}

const copyBtn = document.getElementById('copyBtn');
const copyBtnFooter = document.getElementById('copyBtnFooter');
const installCommand = document.getElementById('installCommand');

if (copyBtn) {
    copyBtn.addEventListener('click', () => {
        copyToClipboard(installCommand.textContent, copyBtn);
    });
}

if (copyBtnFooter) {
    copyBtnFooter.addEventListener('click', () => {
        const command = copyBtnFooter.previousElementSibling.textContent;
        copyToClipboard(command, copyBtnFooter);
    });
}

// ===========================
// FAQ Accordion
// ===========================

const faqQuestions = document.querySelectorAll('.faq-question');

faqQuestions.forEach(question => {
    question.addEventListener('click', () => {
        const isExpanded = question.getAttribute('aria-expanded') === 'true';

        // Close all other FAQs
        faqQuestions.forEach(q => {
            if (q !== question) {
                q.setAttribute('aria-expanded', 'false');
            }
        });

        // Toggle current FAQ
        question.setAttribute('aria-expanded', !isExpanded);
    });
});

// ===========================
// Layout Tabs
// ===========================

const layoutTabs = document.querySelectorAll('.layout-tab');
const layoutPreviews = document.querySelectorAll('.layout-preview');

layoutTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const layout = tab.getAttribute('data-layout');

        // Remove active class from all tabs
        layoutTabs.forEach(t => t.classList.remove('active'));

        // Remove active class from all previews
        layoutPreviews.forEach(p => p.classList.remove('active'));

        // Add active class to clicked tab
        tab.classList.add('active');

        // Show corresponding preview
        const preview = document.getElementById(`layout-${layout}`);
        if (preview) {
            preview.classList.add('active');
        }
    });

    // Keyboard navigation
    tab.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            tab.click();
        }
    });
});

// ===========================
// Smooth Scroll
// ===========================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');

        // Only prevent default for internal links
        if (href !== '#' && document.querySelector(href)) {
            e.preventDefault();

            const target = document.querySelector(href);
            const offsetTop = target.offsetTop - 100;

            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ===========================
// Intersection Observer for Animations
// ===========================

const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe feature cards
document.querySelectorAll('.feature-card').forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = `all 0.6s ease-out ${index * 0.1}s`;
    observer.observe(card);
});

// Observe security cards
document.querySelectorAll('.security-card').forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = `all 0.6s ease-out ${index * 0.1}s`;
    observer.observe(card);
});

// Observe steps
document.querySelectorAll('.step').forEach((step, index) => {
    step.style.opacity = '0';
    step.style.transform = 'translateY(30px)';
    step.style.transition = `all 0.6s ease-out ${index * 0.2}s`;
    observer.observe(step);
});

// ===========================
// Parallax Effect for Gradient Orbs
// ===========================

let mouseX = 0;
let mouseY = 0;
let currentX = 0;
let currentY = 0;

document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX / window.innerWidth - 0.5;
    mouseY = e.clientY / window.innerHeight - 0.5;
});

function animateOrbs() {
    currentX += (mouseX - currentX) * 0.05;
    currentY += (mouseY - currentY) * 0.05;

    const orbs = document.querySelectorAll('.gradient-orb');
    orbs.forEach((orb, index) => {
        const speed = (index + 1) * 20;
        orb.style.transform = `translate(${currentX * speed}px, ${currentY * speed}px)`;
    });

    requestAnimationFrame(animateOrbs);
}

animateOrbs();

// ===========================
// Add Scroll Progress Indicator
// ===========================

const progressBar = document.createElement('div');
progressBar.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(135deg, #e05a33 0%, #d97b4a 100%);
    z-index: 9999;
    transition: width 0.1s ease-out;
`;
document.body.appendChild(progressBar);

window.addEventListener('scroll', () => {
    const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (window.scrollY / windowHeight) * 100;
    progressBar.style.width = scrolled + '%';
});

// ===========================
// Navbar Scroll Effect
// ===========================

let lastScrollTop = 0;
const navbar = document.querySelector('.navbar');
const themeToggleBtn = document.getElementById('themeToggle');

window.addEventListener('scroll', () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (navbar) {
        if (scrollTop > 100) {
            navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.boxShadow = 'none';
        }
    }

    if (themeToggleBtn) {
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            themeToggleBtn.style.transform = 'scale(0.9)';
        } else {
            // Scrolling up
            themeToggleBtn.style.transform = 'scale(1)';
        }
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
}, false);

// ===========================
// Keyboard Navigation for FAQ
// ===========================

faqQuestions.forEach((question, index) => {
    question.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            question.click();
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = faqQuestions[index + 1];
            if (next) next.focus();
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = faqQuestions[index - 1];
            if (prev) prev.focus();
        }
    });
});

// ===========================
// Add Loading Animation
// ===========================

window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease-in';
        document.body.style.opacity = '1';
    }, 100);
});

// ===========================
// Social Share Functions
// ===========================

function shareOnTwitter() {
    const text = "🔐 Just discovered Tyro Login - Beautiful, customizable authentication views for Laravel 12! Multiple layouts, dark/light themes, lockout protection. Check it out! #Laravel #PHP #WebDev";
    const url = "https://github.com/hasinhayder/tyro-login";
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank', 'width=550,height=420');
}

function shareOnLinkedIn() {
    const url = "https://github.com/hasinhayder/tyro-login";
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank', 'width=550,height=420');
}

// ===========================
// Mobile Menu Toggle
// ===========================

const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navbarLinks = document.querySelector('.navbar-links');

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', () => {
        navbarLinks.classList.toggle('mobile-open');
    });
}

// ===========================
// Syntax Highlighting (highlight.js)
// ===========================

function initHighlightJS() {
    if (typeof hljs !== 'undefined') {
        document.querySelectorAll('pre code').forEach(block => {
            // Only highlight if not already highlighted
            if (!block.classList.contains('hljs')) {
                hljs.highlightElement(block);
            }
        });
    }
}

// Run on DOMContentLoaded
document.addEventListener('DOMContentLoaded', initHighlightJS);

// Also run after a short delay to catch dynamically loaded content
setTimeout(initHighlightJS, 100);

// ===========================
// Easter Egg: Konami Code
// ===========================

let konamiCode = [];
const konamiSequence = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];

document.addEventListener('keydown', (e) => {
    konamiCode.push(e.key);
    konamiCode = konamiCode.slice(-10);

    if (konamiCode.join('') === konamiSequence.join('')) {
        // Easter egg activated!
        const orbs = document.querySelectorAll('.gradient-orb');
        orbs.forEach(orb => {
            orb.style.animation = 'float 2s ease-in-out infinite';
        });

        // Show a fun message
        const message = document.createElement('div');
        message.textContent = '🔐 Tyro Login Activated! 🔐';
        message.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #e05a33 0%, #d97b4a 100%);
            color: white;
            padding: 2rem 3rem;
            border-radius: 1rem;
            font-size: 1.5rem;
            font-weight: 900;
            z-index: 10000;
            animation: fadeIn 0.5s ease-out;
        `;
        document.body.appendChild(message);

        setTimeout(() => {
            message.style.animation = 'fadeOut 0.5s ease-out';
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 500);
        }, 2000);

        konamiCode = [];
    }
});
