// MOBILE MENU
function toggleMenu() {
    document.getElementById("menu").classList.toggle("active");
}

// STICKY HEADER ON SCROLL
window.addEventListener("scroll", function () {

    const header = document.querySelector(".top-header");

    if (window.scrollY > 50) {
        header.classList.add("header-scrolled");
    } else {
        header.classList.remove("header-scrolled");
    }

});
/* SWIPE GESTURE SUPPORT */

let currentSlide = 0;
let totalSlides = 0;
let autoSlideInterval;
let touchStartX = 0;
let touchEndX = 0;

/* INIT */
document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll(".slide");
    totalSlides = slides.length;

    startAutoSlide();

    /* Enable swipe gestures on mobile */
    const hero = document.querySelector(".hero");
    hero.addEventListener("touchstart", handleTouchStart, false);
    hero.addEventListener("touchend", handleTouchEnd, false);
});

/* SWIPE EVENT HANDLERS */
function handleTouchStart(e) {
    touchStartX = e.changedTouches[0].screenX;
}

function handleTouchEnd(e) {
    touchEndX = e.changedTouches[0].screenX;
    if (touchStartX - touchEndX > 50) {
        moveSlide(currentSlide + 1); // Swipe left
    } else if (touchEndX - touchStartX > 50) {
        moveSlide(currentSlide - 1); // Swipe right
    }
}

/* AUTO SLIDE */
function startAutoSlide() {
    autoSlideInterval = setInterval(() => {
        currentSlide++;
        if (currentSlide >= totalSlides) {
            currentSlide = 0;
        }
        moveSlide(currentSlide);
    }, 6000); // 6 seconds
}

/* MOVE SLIDE FUNCTION */
function moveSlide(index) {
    const slidesContainer = document.querySelector(".slides");
    const dots = document.querySelectorAll(".dot");

    if (index < 0) {
        index = totalSlides - 1; // Go to last slide
    } else if (index >= totalSlides) {
        index = 0; // Go to first slide
    }

    currentSlide = index;

    slidesContainer.style.transform = `translateX(-${index * 100}%)`;

    dots.forEach(dot => dot.classList.remove("active"));
    dots[index].classList.add("active");
}

/* DOT CLICK (MANUAL CONTROL) */
function goToSlide(index) {
    currentSlide = index;
    moveSlide(index);
    /* RESET TIMER */
    clearInterval(autoSlideInterval);
    startAutoSlide();
}

/* ARROW CLICK */
document.querySelector(".arrow-left").addEventListener("click", () => {
    moveSlide(currentSlide - 1);
});

document.querySelector(".arrow-right").addEventListener("click", () => {
    moveSlide(currentSlide + 1);
});
const form = document.getElementById("offerForm");
const btn = document.getElementById("submitBtn");

form.addEventListener("submit", function (e) {
    e.preventDefault();

    // LOADING
    btn.classList.add("loading");

    setTimeout(() => {
        btn.classList.remove("loading");

        // SUCCESS EFFECT
        btn.classList.add("success");
        btn.innerHTML = "✓ Registered";

        // CONFETTI
        for (let i = 0; i < 40; i++) {
            let confetti = document.createElement("div");
            confetti.classList.add("confetti");

            confetti.style.left = Math.random() * 100 + "vw";
            confetti.style.background = `hsl(${Math.random() * 360},100%,60%)`;

            document.body.appendChild(confetti);

            setTimeout(() => confetti.remove(), 3000);
        }

    }, 1500);
});

/* ===== CART & WISHLIST AJAX ===== */
function toggleWishlist(e, productId, btn) {
    if (e) e.stopPropagation();
    fetch('ajax/wishlist_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ product_id: productId })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('wishlist-count-badge').innerText = data.count;
                if (data.in_wishlist) {
                    btn.classList.add('active');
                    btn.innerHTML = '❤️';
                } else {
                    btn.classList.remove('active');
                    btn.innerHTML = '🤍';
                }
            }
        })
        .catch(err => console.error(err));
}

function addToCart(productId, qty = 1, e = null) {
    if (e) e.stopPropagation();
    fetch('ajax/cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ product_id: productId, action: 'add', qty: qty })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count-badge').innerText = data.count;

                // Pop effect for button if triggered from a button
                if (e && e.target && e.target.tagName === 'BUTTON') {
                    const btn = e.target;
                    const originalText = btn.innerText;
                    btn.innerText = "Added ✓";
                    btn.classList.add('success');
                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.classList.remove('success');
                    }, 1500);
                }
            }
        })
        .catch(err => console.error(err));
}
