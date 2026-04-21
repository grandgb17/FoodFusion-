/* ── Popup ── */
function openPopup() {
    document.getElementById("popup").style.display = "flex";
}
function closePopup() {
    document.getElementById("popup").style.display = "none";
}

/* ── Cookie consent with localStorage memory ── */
function acceptCookies() {
    localStorage.setItem("cookiesAccepted", "true");
    document.getElementById("cookieBox").style.display = "none";
}
if (localStorage.getItem("cookiesAccepted") === "true") {
    document.getElementById("cookieBox").style.display = "none";
}

/* ── Cookie decline ── */
function declineCookies() {
    document.getElementById("cookieBox").style.display = "none";
}

/* ── Auto Carousel with prev/next support ── */
let slides = document.querySelectorAll(".slide");
let index  = 0;
let autoTimer;

function buildDots() {
    var dotsEl = document.getElementById("carouselDots");
    if (!dotsEl || slides.length === 0) return;
    slides.forEach(function(_, i) {
        var d = document.createElement("span");
        d.className = "carousel-dot" + (i === 0 ? " active" : "");
        d.onclick = function() { goToSlide(i); };
        dotsEl.appendChild(d);
    });
}

function goToSlide(n) {
    slides[index].classList.remove("active");
    var dots = document.querySelectorAll(".carousel-dot");
    if (dots.length) dots[index].classList.remove("active");
    index = (n + slides.length) % slides.length;
    slides[index].classList.add("active");
    if (dots.length) dots[index].classList.add("active");
}

function moveSlide(dir) {
    clearInterval(autoTimer);
    goToSlide(index + dir);
    autoTimer = setInterval(function() { goToSlide(index + 1); }, 4000);
}

if (slides.length > 0) {
    buildDots();
    autoTimer = setInterval(function() { goToSlide(index + 1); }, 4000);
}

/* ── Auto-open popup if redirected from another page ── */
if (new URLSearchParams(window.location.search).get('join') === '1') {
    openPopup();
}
