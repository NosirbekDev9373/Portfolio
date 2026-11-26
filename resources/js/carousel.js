const AUTOPLAY_DELAY = 5000;
const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

const slideDistance = (track) => {
    const slides = track.querySelectorAll('[data-carousel-slide]');

    if (slides.length < 2) {
        return track.clientWidth;
    }

    const first = slides[0].getBoundingClientRect();
    const second = slides[1].getBoundingClientRect();

    return Math.abs(second.left - first.left);
};

const currentIndex = (track) => {
    const slides = track.querySelectorAll('[data-carousel-slide]');
    const distance = slideDistance(track);

    if (distance <= 0 || slides.length === 0) {
        return 0;
    }

    return Math.min(slides.length - 1, Math.max(0, Math.round(track.scrollLeft / distance)));
};

const scrollBehavior = () => (motionQuery.matches ? 'auto' : 'smooth');

export function initCarousels() {
    document.querySelectorAll('[data-project-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-carousel-track]');

        if (!track) {
            return;
        }

        const slides = Array.from(track.querySelectorAll('[data-carousel-slide]'));

        if (slides.length === 0) {
            return;
        }

        const previousButton = carousel.querySelector('[data-carousel-prev]');
        const nextButton = carousel.querySelector('[data-carousel-next]');
        const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));
        const status = carousel.querySelector('[data-carousel-status]');
        const autoplayEnabled = carousel.hasAttribute('data-carousel-autoplay') && slides.length > 1;

        const update = () => {
            const index = currentIndex(track);

            if (previousButton) {
                previousButton.disabled = track.scrollLeft <= 1;
            }

            if (nextButton) {
                nextButton.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
            }

            dots.forEach((dot, dotIndex) => {
                const active = dotIndex === index;

                dot.classList.toggle('bg-accent', active);
                dot.classList.toggle('bg-white/25', !active);
                dot.classList.toggle('hover:bg-white/50', !active);

                if (active) {
                    dot.setAttribute('aria-current', 'true');
                } else {
                    dot.removeAttribute('aria-current');
                }
            });

            if (status) {
                status.textContent = `${index + 1} / ${slides.length}`;
            }
        };

        const goTo = (index) => {
            const target = Math.min(slides.length - 1, Math.max(0, index));

            track.scrollTo({
                left: target * slideDistance(track),
                behavior: scrollBehavior(),
            });
        };

        const move = (direction) => {
            const target = currentIndex(track) + direction;

            if (target >= 0 && target < slides.length) {
                goTo(target);
            }
        };

        let timer = null;
        let hovered = false;
        let focused = false;

        const canAutoplay = () => autoplayEnabled
            && !motionQuery.matches
            && !document.hidden
            && !hovered
            && !focused;

        const stopAutoplay = () => {
            if (timer !== null) {
                window.clearInterval(timer);
                timer = null;
            }
        };

        const startAutoplay = () => {
            if (!canAutoplay() || timer !== null) {
                return;
            }

            timer = window.setInterval(() => {
                if (!canAutoplay()) {
                    stopAutoplay();

                    return;
                }

                goTo((currentIndex(track) + 1) % slides.length);
            }, AUTOPLAY_DELAY);
        };

        const resetAutoplay = () => {
            stopAutoplay();
            startAutoplay();
        };

        previousButton?.addEventListener('click', () => {
            move(-1);
            resetAutoplay();
        });

        nextButton?.addEventListener('click', () => {
            move(1);
            resetAutoplay();
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goTo(index);
                resetAutoplay();
            });
        });

        track.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                move(-1);
                resetAutoplay();
            }

            if (event.key === 'ArrowRight') {
                event.preventDefault();
                move(1);
                resetAutoplay();
            }
        });

        carousel.addEventListener('mouseenter', () => {
            hovered = true;
            stopAutoplay();
        });

        carousel.addEventListener('mouseleave', () => {
            hovered = false;
            startAutoplay();
        });

        carousel.addEventListener('focusin', () => {
            focused = true;
            stopAutoplay();
        });

        carousel.addEventListener('focusout', (event) => {
            if (!carousel.contains(event.relatedTarget)) {
                focused = false;
            }

            startAutoplay();
        });

        track.addEventListener('touchstart', stopAutoplay, { passive: true });
        track.addEventListener('touchend', startAutoplay, { passive: true });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoplay();
            } else {
                startAutoplay();
            }
        });

        const handleMotionChange = () => {
            if (motionQuery.matches) {
                stopAutoplay();
            } else {
                startAutoplay();
            }
        };

        if (typeof motionQuery.addEventListener === 'function') {
            motionQuery.addEventListener('change', handleMotionChange);
        } else if (typeof motionQuery.addListener === 'function') {
            motionQuery.addListener(handleMotionChange);
        }

        track.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);

        update();
        startAutoplay();
    });
}
