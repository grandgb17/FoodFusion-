document.addEventListener('DOMContentLoaded', function () {

    /* ── User preference (set via data attribute on <body>) ── */
    const USER_PREFERENCE = document.body.dataset.userPreference || 'Non-Vegetarian';

    const categoryEmojis = {
        all: '🍽', pasta: '🍝', curry: '🍛',
        dessert: '🍰', healthy: '🥗', general: '🍽', noodles: '🍜'
    };

    /* ────────────────────────────────────────────────────────
       PREFERENCE FILTER HELPER
       Hides Non-Vegetarian cards for Veg/Vegan users by default.
       When the user is actively searching, all matches show
       (so they can still find non-veg if they specifically look).
    ──────────────────────────────────────────────────────── */
    function isHiddenByPreference(cardDietary) {
        return (USER_PREFERENCE === 'Vegetarian' || USER_PREFERENCE === 'Vegan')
            && cardDietary === 'Non-Vegetarian';
    }

    /* ────────────────────────────────────────────────────────
       FILTER + SORT
    ──────────────────────────────────────────────────────── */
    window.applyFilters = function () {
        const search = document.getElementById('cbSearch').value.toLowerCase().trim();
        const cat    = document.getElementById('cbCategory').value;
        const sort   = document.getElementById('cbSort').value;
        const cards  = Array.from(document.querySelectorAll('.recipe-card'));

        cards.forEach(function (card) {
            const title       = card.querySelector('h3') ? card.querySelector('h3').textContent.toLowerCase() : '';
            const desc        = card.querySelector('.card-description') ? card.querySelector('.card-description').textContent.toLowerCase() : '';
            const cardDietary = card.dataset.dietary || '';

            const matchCat    = cat === 'all' || card.dataset.category === cat;
            const matchSearch = !search || title.includes(search) || desc.includes(search);
            const prefHidden  = !search && isHiddenByPreference(cardDietary);

            card.style.display = (matchCat && matchSearch && !prefHidden) ? '' : 'none';
        });

        if (sort === 'most-liked') {
            const grid = document.getElementById('communityRecipes');
            cards.filter(function (c) { return c.style.display !== 'none'; })
                 .sort(function (a, b) { return (parseInt(b.dataset.likes) || 0) - (parseInt(a.dataset.likes) || 0); })
                 .forEach(function (c) { grid.appendChild(c); });
        }
    };

    /* ────────────────────────────────────────────────────────
       SINGLE-CLICK on PHP-rendered cards
    ──────────────────────────────────────────────────────── */
    document.querySelectorAll('.recipe-card[data-id]').forEach(function (card) {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function (e) {
            if (e.target.classList.contains('like-btn')) return;
            window.location.href = 'recipe_detail.php?id=' + card.dataset.id + '&src=community';
        });
        card.addEventListener('mouseenter', function () {
            var o = card.querySelector('.click-overlay');
            if (o) o.style.opacity = '1';
        });
        card.addEventListener('mouseleave', function () {
            var o = card.querySelector('.click-overlay');
            if (o) o.style.opacity = '0';
        });
    });

    /* ────────────────────────────────────────────────────────
       ADD CARD TO FEED (after successful AJAX submit)
    ──────────────────────────────────────────────────────── */
    function addCardToFeed(recipe) {
        const display = document.getElementById('communityRecipes');
        const noMsg   = display.querySelector('p');
        if (noMsg) noMsg.remove();

        const emoji = categoryEmojis[recipe.category] || '🍽';
        const card  = document.createElement('div');
        card.className        = 'recipe-card';
        card.dataset.id       = recipe.recipe_id;
        card.dataset.category = recipe.category;
        card.dataset.dietary  = recipe.dietary || '';
        card.dataset.likes    = '0';
        card.style.cursor     = 'pointer';

        var bannerHTML;
        if (recipe.image_path) {
            bannerHTML = '<div class="card-banner card-banner--photo"><img src="' + escHtml(recipe.image_path) + '" class="card-banner-img" alt="' + escHtml(recipe.title) + '"></div>';
        } else {
            bannerHTML = '<div class="card-banner">' + emoji + '</div>';
        }

        card.innerHTML =
            bannerHTML +
            '<div class="card-body">' +
                '<div class="card-category">' + escHtml(recipe.category) + '</div>' +
                '<h3>' + escHtml(recipe.title) + '</h3>' +
                '<p class="card-description">' + escHtml(recipe.description) + '</p>' +
                '<div class="card-footer">' +
                    '<span class="card-author">👨‍🍳 ' + escHtml(recipe.author || 'Anonymous Chef') +
                        (recipe.cooktime ? ' · ⏱ ' + escHtml(recipe.cooktime) : '') + '</span>' +
                    '<button class="like-btn" data-recipe-id="' + recipe.recipe_id + '" onclick="toggleLike(this)">❤️ 0</button>' +
                '</div>' +
            '</div>' +
            '<div class="click-overlay">Click to view full recipe →</div>';

        card.addEventListener('click', function (e) {
            if (e.target.classList.contains('like-btn')) return;
            if (recipe.recipe_id) window.location.href = 'recipe_detail.php?id=' + recipe.recipe_id + '&src=community';
        });
        card.addEventListener('mouseenter', function () { card.querySelector('.click-overlay').style.opacity = '1'; });
        card.addEventListener('mouseleave', function () { card.querySelector('.click-overlay').style.opacity = '0'; });

        display.insertBefore(card, display.firstChild);
    }

    /* ────────────────────────────────────────────────────────
       PHOTO UPLOAD — preview & remove
    ──────────────────────────────────────────────────────── */
    var imageInput = document.getElementById('recipeImage');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            var allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowed.includes(file.type)) {
                alert('Please choose a JPG, PNG, or WEBP image.');
                this.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Image must be under 5 MB.');
                this.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                var area        = document.getElementById('photoUploadArea');
                var preview     = document.getElementById('photoPreview');
                var placeholder = document.getElementById('photoPlaceholder');
                var meta        = document.getElementById('photoMeta');
                var filename    = document.getElementById('photoFilename');
                var hoverHint   = document.getElementById('photoHoverHint');

                /* Show preview, hide placeholder */
                preview.src          = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';

                /* Show file meta strip */
                meta.style.display = 'flex';
                filename.textContent = file.name + '  (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';

                /* Hover hint on the area */
                area.addEventListener('mouseenter', function () { hoverHint.style.display = 'flex'; });
                area.addEventListener('mouseleave', function () { hoverHint.style.display = 'none'; });

                /* Orange border once loaded */
                area.style.borderColor = '#ff6f3c';
                area.style.borderStyle = 'solid';
            };
            reader.readAsDataURL(file);
        });
    }

    window.removePhoto = function () {
        var imageInput  = document.getElementById('recipeImage');
        var area        = document.getElementById('photoUploadArea');
        var preview     = document.getElementById('photoPreview');
        var placeholder = document.getElementById('photoPlaceholder');
        var meta        = document.getElementById('photoMeta');
        var hoverHint   = document.getElementById('photoHoverHint');

        imageInput.value           = '';
        preview.src                = '';
        preview.style.display      = 'none';
        placeholder.style.display  = 'flex';
        meta.style.display         = 'none';
        hoverHint.style.display    = 'none';
        area.style.borderColor     = '#ffb38a';
        area.style.borderStyle     = 'dashed';
    };

    /* ────────────────────────────────────────────────────────
       AJAX FORM SUBMIT → save_recipe.php
    ──────────────────────────────────────────────────────── */
    var form = document.getElementById('recipeForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn          = document.getElementById('submitBtn');
            var title        = document.getElementById('title').value.trim();
            var desc         = document.getElementById('description').value.trim();
            var ingredients  = document.getElementById('ingredients').value.trim();
            var instructions = document.getElementById('instructions').value.trim();

            if (!title || !desc || !ingredients || !instructions) {
                alert('Please fill in all required fields (Title, Description, Ingredients, Instructions).');
                return;
            }

            btn.textContent = '⏳ Publishing...';
            btn.disabled    = true;

            var data = new FormData();
            data.append('title',        title);
            data.append('description',  desc);
            data.append('category',     document.getElementById('category').value);
            data.append('cooktime',     document.getElementById('cooktime').value.trim());
            data.append('preptime',     document.getElementById('preptime').value.trim());
            data.append('serves',       document.getElementById('serves').value.trim());
            data.append('difficulty',   document.getElementById('difficulty').value);
            data.append('cuisine',      document.getElementById('cuisine').value.trim());
            data.append('dietary',      document.getElementById('dietary').value);
            data.append('story',        document.getElementById('story').value.trim());
            data.append('ingredients',  ingredients);
            data.append('instructions', instructions);
            data.append('chef_tips',    document.getElementById('chef_tips').value.trim());
            data.append('calories',     document.getElementById('calories').value.trim());
            data.append('protein',      document.getElementById('protein').value.trim());
            data.append('carbs',        document.getElementById('carbs').value.trim());
            data.append('fat',          document.getElementById('fat').value.trim());
            data.append('author',       document.getElementById('author').value.trim());

            fetch('save_recipe.php', { method: 'POST', body: data })
                .then(function (r) { return r.text(); })
                .then(function (text) {
                    var res;
                    try { res = JSON.parse(text); }
                    catch (err) {
                        console.error('Server response:', text);
                        alert('Server error. Check browser console for details.');
                        btn.textContent = '🚀 Publish My Recipe';
                        btn.disabled    = false;
                        return;
                    }
                    if (res.success) {
                        document.getElementById('submitSuccess').style.display = 'block';
                        form.reset();
                        addCardToFeed(res);
                        setTimeout(function () {
                            document.getElementById('submitSuccess').style.display = 'none';
                        }, 4000);
                    } else {
                        alert(res.message || 'Could not save. Please try again.');
                    }
                    btn.textContent = '🚀 Publish My Recipe';
                    btn.disabled    = false;
                })
                .catch(function (err) {
                    alert('Network error: ' + err.message);
                    btn.textContent = '🚀 Publish My Recipe';
                    btn.disabled    = false;
                });
        });
    }

    /* ────────────────────────────────────────────────────────
       LIKE BUTTON
    ──────────────────────────────────────────────────────── */
    window.toggleLike = function (btn) {
        var recipeId = btn.dataset.recipeId;
        if (!recipeId) return;
        var data = new FormData();
        data.append('recipe_id', recipeId);
        fetch('toggle_like.php', { method: 'POST', body: data })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.success) {
                    btn.textContent      = '❤️ ' + res.likes;
                    btn.style.background = res.liked ? '#fff0ea' : '';
                } else {
                    alert(res.message);
                }
            });
    };

    /* ────────────────────────────────────────────────────────
       SMOOTH SCROLL
    ──────────────────────────────────────────────────────── */
    window.smoothScrollTo = function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var navH = document.querySelector('header').offsetHeight;
        window.scrollTo({ top: el.getBoundingClientRect().top + window.pageYOffset - navH - 20, behavior: 'smooth' });
    };

    /* ────────────────────────────────────────────────────────
       UTILITY
    ──────────────────────────────────────────────────────── */
    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    /* Apply preference filter on page load */
    applyFilters();

    /* ────────────────────────────────────────────────────────
       CULINARY TIPS / EXPERIENCES FORM
    ──────────────────────────────────────────────────────── */
    var tipForm = document.getElementById('tipForm');
    if (tipForm) {

        /* Map type values → display labels & emojis */
        var tipTypeEmoji = { tip:'💡', experience:'🌟', recipe_hack:'🔧', technique:'🔪', other:'💬' };
        var tipTypeLabel = { tip:'Culinary Tip', experience:'Experience', recipe_hack:'Recipe Hack', technique:'Technique', other:'Share' };

        function addTipCard(res) {
            var feed  = document.getElementById('cbTipsFeed');
            var empty = document.getElementById('cbTipsEmpty');
            if (!feed) return;
            if (empty) empty.remove();

            var em  = tipTypeEmoji[res.tip_type] || '💬';
            var lbl = tipTypeLabel[res.tip_type]  || 'Share';
            var now = new Date();
            var ago = now.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

            var cuisineBadge = res.cuisine
                ? '<span class="cb-tip-badge-cuisine">' + escHtml(res.cuisine) + '</span>'
                : '';

            var card = document.createElement('div');
            card.className = 'cb-tip-card cb-tip-card--new';
            card.innerHTML =
                '<div class="cb-tip-card-top">' +
                    '<span class="cb-tip-card-emoji">' + em + '</span>' +
                    '<div class="cb-tip-card-badges">' +
                        '<span class="cb-tip-badge-type">' + escHtml(lbl) + '</span>' +
                        cuisineBadge +
                    '</div>' +
                '</div>' +
                '<h4 class="cb-tip-card-title">' + escHtml(res.title) + '</h4>' +
                '<p class="cb-tip-card-content">' + escHtml(res.content).replace(/\n/g, '<br>') + '</p>' +
                '<div class="cb-tip-card-footer">' +
                    '<span>👨‍🍳 ' + escHtml(res.author_name) + '</span>' +
                    '<span>📅 ' + ago + '</span>' +
                '</div>';

            feed.insertBefore(card, feed.firstChild);
            /* Trigger animation on next frame */
            requestAnimationFrame(function () { card.classList.add('cb-tip-card--visible'); });
        }

        tipForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn     = document.getElementById('tipSubmitBtn');
            var title   = document.getElementById('tipTitle').value.trim();
            var content = document.getElementById('tipContent').value.trim();

            if (!title || !content) {
                alert('Please fill in the Title and your tip/experience.');
                return;
            }

            btn.textContent = '⏳ Sharing...';
            btn.disabled    = true;

            var data = new FormData();
            data.append('author',   document.getElementById('tipAuthor').value.trim());
            data.append('tip_type', document.getElementById('tipType').value);
            data.append('cuisine',  document.getElementById('tipCuisine').value.trim());
            data.append('title',    title);
            data.append('content',  content);

            fetch('save_tip.php', { method: 'POST', body: data })
                .then(function (r) { return r.text(); })
                .then(function (text) {
                    var res;
                    try { res = JSON.parse(text); }
                    catch (err) {
                        console.error('Server response:', text);
                        alert('Server error. Check console for details.');
                        btn.textContent = '✨ Share with Community';
                        btn.disabled    = false;
                        return;
                    }
                    if (res.success) {
                        var successEl = document.getElementById('tipSuccess');
                        successEl.style.display = 'block';
                        addTipCard(res);
                        tipForm.reset();
                        setTimeout(function () { successEl.style.display = 'none'; }, 4000);
                    } else {
                        alert(res.message || 'Could not save. Please try again.');
                    }
                    btn.textContent = '✨ Share with Community';
                    btn.disabled    = false;
                })
                .catch(function (err) {
                    alert('Network error: ' + err.message);
                    btn.textContent = '✨ Share with Community';
                    btn.disabled    = false;
                });
        });
    }

});
