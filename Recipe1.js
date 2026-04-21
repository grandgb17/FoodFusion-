const searchInput      = document.getElementById("search");
const cuisineFilter    = document.getElementById("cuisineFilter");
const difficultyFilter = document.getElementById("difficultyFilter");
const noResults        = document.getElementById("no-results");

// USER_PREFERENCE is set inline by PHP:
// 'Vegetarian' or 'Vegan'  → hide Non-Vegetarian by default
// 'Non-Vegetarian' or guest → show everything by default
// Users can still search/type to override the default hiding

function isHiddenByPreference(cardDietary) {
    // Only auto-hide Non-Veg cards for Vegetarian/Vegan users
    if ((USER_PREFERENCE === 'Vegetarian' || USER_PREFERENCE === 'Vegan')
        && cardDietary === 'Non-Vegetarian') {
        return true;
    }
    return false;
}

function filterRecipes() {
    const searchText  = searchInput.value.toLowerCase();
    const cuisine     = cuisineFilter.value;
    const difficulty  = difficultyFilter.value;
    const cards       = document.querySelectorAll(".recipe-card");
    let visible       = 0;

    cards.forEach(card => {
        const h3    = card.querySelector("h3");
        const p     = card.querySelector(".recipe-card-body p");
        const title = h3 ? h3.innerText.toLowerCase() : "";
        const desc  = p  ? p.innerText.toLowerCase()  : "";
        const cardDietary = card.dataset.dietary || "";

        const matchSearch     = !searchText || title.includes(searchText) || desc.includes(searchText);
        const matchCuisine    = cuisine    === "all" || card.dataset.cuisine    === cuisine;
        const matchDifficulty = difficulty === "all" || card.dataset.difficulty === difficulty;

        // If user is searching, show everything that matches the search
        // (lets Veg/Vegan users find non-veg if they specifically look for it)
        // If not searching, apply the preference-based auto-filter
        const prefHidden = !searchText && isHiddenByPreference(cardDietary);

        const show = matchSearch && matchCuisine && matchDifficulty && !prefHidden;
        card.style.display = show ? "flex" : "none";
        if (show) visible++;
    });

    noResults.style.display = visible === 0 ? "block" : "none";
}

// Single click → recipe detail page
document.querySelectorAll(".recipe-card").forEach(function(card) {
    card.style.cursor = "pointer";
    card.addEventListener("click", function() {
        var id = card.dataset.id;
        if (id) {
            window.location.href = "recipe_detail.php?id=" + id;
        }
    });
});

searchInput.addEventListener("input", filterRecipes);
cuisineFilter.addEventListener("change", filterRecipes);
difficultyFilter.addEventListener("change", filterRecipes);

// Apply preference filter on page load
filterRecipes();