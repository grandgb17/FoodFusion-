<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Educational Resources</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="educational_resources.css">
</head>
<body>

<header>
    <div class="logo">🍽 FoodFusion</div>
        <nav>
        <a href="Home.php">Home</a>
        <a href="about.php">About</a>
        <a href="Recipe.php">Recipes</a>
        <a href="COOKBOOK.php">CookBook</a>
        <a href="culinary_resources.php">Resources</a>
        <a href="educational_resources.php" class="active">Learn</a>
        <a href="contact.php">Contact</a>
        <?php if(isset($_SESSION['user'])){ ?>
        <button class="nav-btn" onclick="window.location.href='profile.php'">Profile</button>
        <?php } else { ?>
        <button class="nav-btn" onclick="window.location.href='Home.php?join=1'">Join Us</button>
        <?php } ?>
    </nav>
        <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
</header>

<!-- HERO -->
<section class="er-hero">
    <div class="er-hero-inner">
        <span class="er-eyebrow">🎓 Educational Resources</span>
        <h1>Learn. Cook.<br><span>Grow Greener.</span></h1>
        <p>Explore our library of food science guides, nutrition infographics, renewable energy resources, and expert-led educational videos — designed to make you a more confident, informed, and sustainable home cook.</p>
        <div class="er-hero-stats">
            <div class="er-stat"><strong>40+</strong><span>Guides</span></div>
            <div class="er-stat"><strong>25+</strong><span>Infographics</span></div>
            <div class="er-stat"><strong>60+</strong><span>Videos</span></div>
            <div class="er-stat"><strong>12+</strong><span>Energy Topics</span></div>
        </div>
    </div>
    <div class="er-hero-deco">
        <div class="er-deco-ring">
            <span>🧪</span><span>📊</span><span>🎓</span><span>🥦</span><span>🍎</span><span>📖</span>
        </div>
    </div>
</section>

<!-- TOPIC PILLS -->
<section class="er-topics">
    <h2>Browse by Topic</h2>
    <div class="er-topic-pills">
        <button class="er-topic active" onclick="filterAll('all', this)">🍽 All Topics</button>
        <button class="er-topic" onclick="filterAll('nutrition', this)">🥦 Nutrition</button>
        <button class="er-topic" onclick="filterAll('food-science', this)">🧪 Food Science</button>
        <button class="er-topic" onclick="filterAll('safety', this)">🛡 Food Safety</button>
        <button class="er-topic" onclick="filterAll('history', this)">📜 Food History</button>
        <button class="er-topic" onclick="filterAll('sustainability', this)">🌱 Sustainability</button>
        <button class="er-topic" onclick="filterAll('renewable-energy', this)">⚡ Renewable Energy</button>
    </div>
</section>

<!-- INFOGRAPHICS -->
<section class="er-section" id="section-infographics">
    <div class="er-section-header">
        <h2>📊 Educational Infographics</h2>
        <p>Visual guides you can download, print, and stick on your kitchen wall.</p>
    </div>
    <div class="er-infographic-grid">

        <div class="er-info-card" data-topic="nutrition">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#a8edea,#fed6e3);">
                <span>🥗</span>
                <div class="er-info-badge">Nutrition</div>
            </div>
            <div class="er-info-body">
                <h3>The Balanced Plate Guide</h3>
                <p>Visual breakdown of macronutrients, portion sizes, and how to build a nutritionally complete meal every time.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A4</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="food-science">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#f093fb,#f5576c);">
                <span>🔥</span>
                <div class="er-info-badge">Food Science</div>
            </div>
            <div class="er-info-body">
                <h3>Maillard Reaction Explained</h3>
                <p>The chemistry behind browning — why crusts form, what creates flavour, and how to control it in your cooking.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A4</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="safety">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#4facfe,#00f2fe);">
                <span>🌡️</span>
                <div class="er-info-badge">Food Safety</div>
            </div>
            <div class="er-info-body">
                <h3>Safe Cooking Temperatures</h3>
                <p>At-a-glance guide to internal temperatures for meat, poultry, seafood, and eggs — the essential fridge-door reference.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A5</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="nutrition">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#43e97b,#38f9d7);">
                <span>🧬</span>
                <div class="er-info-badge">Nutrition</div>
            </div>
            <div class="er-info-body">
                <h3>Vitamins & Where to Find Them</h3>
                <p>A comprehensive chart of vitamins A through K, their food sources, daily requirements, and signs of deficiency.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A3</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="sustainability">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#84fab0,#8fd3f4);">
                <span>🌱</span>
                <div class="er-info-badge">Sustainability</div>
            </div>
            <div class="er-info-body">
                <h3>Seasonal Eating Calendar</h3>
                <p>Month-by-month guide to what fruits and vegetables are in season — eat fresher, cheaper, and more sustainably.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A3</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="food-science">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#fa709a,#fee140);">
                <span>⚗️</span>
                <div class="er-info-badge">Food Science</div>
            </div>
            <div class="er-info-body">
                <h3>Emulsification & Sauces</h3>
                <p>How oil and water bind together. Understand mayonnaise, vinaigrettes, hollandaise, and the science of stable emulsions.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A4</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="history">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#d4fc79,#96e6a1);">
                <span>📜</span>
                <div class="er-info-badge">Food History</div>
            </div>
            <div class="er-info-body">
                <h3>Origins of World Cuisines</h3>
                <p>A visual timeline tracing how trade routes, migration, and colonisation shaped the cuisines we know and love today.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A2 Poster</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="safety">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#667eea,#764ba2);">
                <span>🫙</span>
                <div class="er-info-badge">Food Safety</div>
            </div>
            <div class="er-info-body">
                <h3>Food Storage & Shelf Life Guide</h3>
                <p>How long to store fridge, freezer, and pantry items. Reduce food waste and avoid illness with this essential reference.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A4</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- DOWNLOADABLE GUIDES -->
<section class="er-guides-section">
    <div class="er-section-header">
        <h2>📖 Downloadable Learning Guides</h2>
        <p>In-depth PDF guides written by chefs, nutritionists, and food scientists.</p>
    </div>
    <div class="er-guides-list">

        <div class="er-guide-item" data-topic="nutrition">
            <div class="er-guide-icon" style="background:#fff3e0; color:#ff6f3c;">🥩</div>
            <div class="er-guide-content">
                <h3>Protein for Home Cooks</h3>
                <p>Complete guide to protein sources, cooking methods that preserve nutrition, and daily requirements for all dietary preferences.</p>
                <div class="er-guide-meta"><span>📄 18 pages</span><span>🥦 Nutrition</span><span>⭐ Beginner-friendly</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="food-science">
            <div class="er-guide-icon" style="background:#fce4ec; color:#e91e63;">🧪</div>
            <div class="er-guide-content">
                <h3>Understanding Fermentation</h3>
                <p>The microbiology of kombucha, kimchi, sourdough, and yoghurt — make your own probiotic-rich foods at home.</p>
                <div class="er-guide-meta"><span>📄 24 pages</span><span>🧪 Food Science</span><span>⭐ Intermediate</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="sustainability">
            <div class="er-guide-icon" style="background:#e8f5e9; color:#2e7d32;">🌎</div>
            <div class="er-guide-content">
                <h3>Sustainable Kitchen Handbook</h3>
                <p>Zero-waste cooking, composting, reducing food miles, plant-forward eating, and packaging-free shopping strategies.</p>
                <div class="er-guide-meta"><span>📄 32 pages</span><span>🌱 Sustainability</span><span>⭐ All levels</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="history">
            <div class="er-guide-icon" style="background:#ede7f6; color:#673ab7;">🏛</div>
            <div class="er-guide-content">
                <h3>A History of Food & Culture</h3>
                <p>How food shaped civilisations, religions, economies, and identities — from ancient Mesopotamia to the modern food movement.</p>
                <div class="er-guide-meta"><span>📄 40 pages</span><span>📜 Food History</span><span>⭐ All levels</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="safety">
            <div class="er-guide-icon" style="background:#e3f2fd; color:#1565c0;">🦠</div>
            <div class="er-guide-content">
                <h3>Food Hygiene & HACCP Basics</h3>
                <p>Practical food safety principles for home cooks — cross-contamination, handwashing, chilling, and danger zone temperatures.</p>
                <div class="er-guide-meta"><span>📄 14 pages</span><span>🛡 Food Safety</span><span>⭐ Beginner-friendly</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="food-science">
            <div class="er-guide-icon" style="background:#fff8e1; color:#f57f17;">🍬</div>
            <div class="er-guide-content">
                <h3>Sugar Science & Confectionery</h3>
                <p>Sugar stages, crystallisation, caramelisation, and tempering chocolate — the complete science of sweet cooking.</p>
                <div class="er-guide-meta"><span>📄 20 pages</span><span>🧪 Food Science</span><span>⭐ Advanced</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

    </div>
</section>

<!-- EDUCATIONAL VIDEOS -->
<section class="er-section" style="background:white;">
    <div class="er-section-header">
        <h2>🎬 Educational Video Series</h2>
        <p>Structured learning series from beginner foundations to advanced culinary science.</p>
    </div>
    <div class="er-video-series">

        <div class="er-series-card">
            <div class="er-series-header" style="background: linear-gradient(135deg,#ff6f3c,#ff9a3c);">
                <span class="er-series-icon">🔬</span>
                <div>
                    <h3>Food Science 101</h3>
                    <p>6-part series · Beginner</p>
                </div>
            </div>
            <ul class="er-episode-list">
                <li onclick="openEduVideo('Ep 1: Why Food Turns Brown','The Maillard reaction and caramelisation explained with kitchen experiments.')">▶ Ep 1: Why Food Turns Brown <span>12:04</span></li>
                <li onclick="openEduVideo('Ep 2: The Science of Salt','How salt affects taste, texture, preservation, and cooking chemistry.')">▶ Ep 2: The Science of Salt <span>14:22</span></li>
                <li onclick="openEduVideo('Ep 3: How Fat Works','Fats in cooking — smoke points, emulsification, flavour, and health.')">▶ Ep 3: How Fat Works <span>18:07</span></li>
                <li onclick="openEduVideo('Ep 4: Acids & Bases in Cooking','pH in food — why acid brightens flavour and baking soda makes things rise.')">▶ Ep 4: Acids & Bases <span>11:45</span></li>
                <li onclick="openEduVideo('Ep 5: Gluten — Friend or Foe?','What gluten is, how it forms, and why it matters for bread, pastry, and pasta.')">▶ Ep 5: Gluten Explained <span>16:30</span></li>
                <li onclick="openEduVideo('Ep 6: The Science of Flavour','How aroma, taste, and texture combine in the brain to create the experience of flavour.')">▶ Ep 6: Science of Flavour <span>20:15</span></li>
            </ul>
        </div>

        <div class="er-series-card">
            <div class="er-series-header" style="background: linear-gradient(135deg,#43e97b,#38f9d7);">
                <span class="er-series-icon">🥦</span>
                <div>
                    <h3>Nutrition for Cooks</h3>
                    <p>5-part series · All levels</p>
                </div>
            </div>
            <ul class="er-episode-list">
                <li onclick="openEduVideo('Ep 1: Macronutrients Demystified','Proteins, fats, and carbohydrates — what they do and how much you need.')">▶ Ep 1: Macronutrients <span>15:10</span></li>
                <li onclick="openEduVideo('Ep 2: Micronutrients in Food','Vitamins and minerals — the difference between fat-soluble and water-soluble nutrients.')">▶ Ep 2: Micronutrients <span>13:40</span></li>
                <li onclick="openEduVideo('Ep 3: Cooking & Nutrient Loss','Which nutrients survive cooking and which don\'t — and how to retain more.')">▶ Ep 3: Nutrient Retention <span>12:55</span></li>
                <li onclick="openEduVideo('Ep 4: Plant-Based Nutrition','Complete proteins, iron absorption, B12, and how to thrive on a plant-based diet.')">▶ Ep 4: Plant-Based Nutrition <span>22:00</span></li>
                <li onclick="openEduVideo('Ep 5: Reading Food Labels','Decode nutrition labels, understand ingredient lists, and make better choices.')">▶ Ep 5: Reading Food Labels <span>10:30</span></li>
            </ul>
        </div>

        <div class="er-series-card">
            <div class="er-series-header" style="background: linear-gradient(135deg,#4facfe,#00f2fe);">
                <span class="er-series-icon">🌍</span>
                <div>
                    <h3>World Food Culture</h3>
                    <p>4-part series · All levels</p>
                </div>
            </div>
            <ul class="er-episode-list">
                <li onclick="openEduVideo('Ep 1: The Spice Trade & Modern Cuisine','How the global spice trade shaped flavour, wealth, and colonisation.')">▶ Ep 1: The Spice Trade <span>19:20</span></li>
                <li onclick="openEduVideo('Ep 2: Food & Religion','Dietary laws in Judaism, Islam, Hinduism, and Buddhism — and the food they shaped.')">▶ Ep 2: Food & Religion <span>17:45</span></li>
                <li onclick="openEduVideo('Ep 3: Street Food Around the World','The social history of street food from Rome\'s thermopolia to Bangkok\'s night markets.')">▶ Ep 3: Street Food History <span>23:10</span></li>
                <li onclick="openEduVideo('Ep 4: The Future of Food','Lab-grown meat, precision fermentation, vertical farming, and what we\'ll eat in 2050.')">▶ Ep 4: Future of Food <span>25:00</span></li>
            </ul>
        </div>

    </div>
</section>

<!-- VIDEO MODAL -->
<div class="er-modal" id="eduModal" onclick="closeEduModal()">
    <div class="er-modal-box" onclick="event.stopPropagation()">
        <button class="er-modal-close" onclick="closeEduModal()">✕</button>
        <span style="font-size:64px;">🎬</span>
        <p id="eduModalTitle" style="font-size:18px;font-weight:bold;color:#fff;margin:16px 0 8px;text-align:center;max-width:420px;"></p>
        <p id="eduModalDesc" style="color:#bbb;font-size:13px;text-align:center;max-width:400px;line-height:1.6;"></p>
        <p style="color:#ff6f3c;margin-top:20px;font-size:13px;">▶ Connect your video hosting to enable streaming.</p>
    </div>
</div>

<!-- RENEWABLE ENERGY SECTION -->
<section class="er-section er-energy-section" id="section-renewable-energy">
    <div class="er-section-header">
        <h2>⚡ Renewable Energy & Sustainable Cooking</h2>
        <p>Understanding how energy powers our food system — and how clean energy choices in the kitchen can help the planet.</p>
    </div>

    <!-- Energy Infographics -->
    <div class="er-energy-sub-heading">📊 Infographics</div>
    <div class="er-infographic-grid">

        <div class="er-info-card" data-topic="renewable-energy">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#f7971e,#ffd200);">
                <span>☀️</span>
                <div class="er-info-badge er-badge-energy">Renewable Energy</div>
            </div>
            <div class="er-info-body">
                <h3>Solar Cooking Explained</h3>
                <p>How solar ovens, parabolic cookers, and box cookers harness the sun's energy — zero fuel, zero emissions, full meals.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A4</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="renewable-energy">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#56ab2f,#a8e063);">
                <span>🌬️</span>
                <div class="er-info-badge er-badge-energy">Renewable Energy</div>
            </div>
            <div class="er-info-body">
                <h3>Wind & Solar in Food Production</h3>
                <p>How wind turbines and solar farms are powering farms, greenhouses, food factories, and cold storage facilities worldwide.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A3</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="renewable-energy">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#11998e,#38ef7d);">
                <span>🔋</span>
                <div class="er-info-badge er-badge-energy">Renewable Energy</div>
            </div>
            <div class="er-info-body">
                <h3>Carbon Footprint of Your Meal</h3>
                <p>A visual breakdown of CO₂ emissions by food type — from beef to lentils — and how cooking method affects your meal's total carbon cost.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A4</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

        <div class="er-info-card" data-topic="renewable-energy">
            <div class="er-info-visual" style="background: linear-gradient(135deg,#1a1a2e,#16213e,#0f3460);">
                <span>⚡</span>
                <div class="er-info-badge er-badge-energy">Renewable Energy</div>
            </div>
            <div class="er-info-body">
                <h3>Induction vs Gas vs Electric</h3>
                <p>Energy efficiency comparison across cooking hob types — cost per hour, CO₂ per meal, speed, and which works best with renewable electricity.</p>
                <div class="er-info-footer">
                    <span class="er-tag">PDF · A5</span>
                    <a href="#" class="er-dl-sm" onclick="simulateDownload(event,this)">⬇ Download</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Energy Downloadable Guides -->
    <div class="er-energy-sub-heading" style="margin-top:48px;">📖 Downloadable Guides</div>
    <div class="er-guides-list">

        <div class="er-guide-item" data-topic="renewable-energy">
            <div class="er-guide-icon" style="background:#fffde7; color:#f57f17;">☀️</div>
            <div class="er-guide-content">
                <h3>The Home Cook's Guide to Green Energy</h3>
                <p>Switch to induction cooking, use off-peak tariffs, reduce appliance energy waste, and understand how renewable electricity is generated and supplied to your home.</p>
                <div class="er-guide-meta"><span>📄 22 pages</span><span>⚡ Renewable Energy</span><span>⭐ Beginner-friendly</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="renewable-energy">
            <div class="er-guide-icon" style="background:#e8f5e9; color:#2e7d32;">🌍</div>
            <div class="er-guide-content">
                <h3>Renewable Energy in Global Food Systems</h3>
                <p>How solar irrigation in Sub-Saharan Africa, wind-powered fish farms in the North Sea, and biogas digesters on Asian farms are transforming food production with clean energy.</p>
                <div class="er-guide-meta"><span>📄 36 pages</span><span>⚡ Renewable Energy</span><span>⭐ Intermediate</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="renewable-energy">
            <div class="er-guide-icon" style="background:#e3f2fd; color:#0d47a1;">🔬</div>
            <div class="er-guide-content">
                <h3>Biogas & Food Waste Energy Recovery</h3>
                <p>How anaerobic digestion converts food waste into biogas for cooking and electricity — turning kitchen scraps into clean, usable energy at home and at scale.</p>
                <div class="er-guide-meta"><span>📄 28 pages</span><span>⚡ Renewable Energy</span><span>⭐ Advanced</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

        <div class="er-guide-item" data-topic="renewable-energy">
            <div class="er-guide-icon" style="background:#fce4ec; color:#c62828;">🏭</div>
            <div class="er-guide-content">
                <h3>Food Industry Decarbonisation Report</h3>
                <p>How the world's largest food manufacturers — from dairy to confectionery — are transitioning to renewable energy, and what it means for food prices and availability.</p>
                <div class="er-guide-meta"><span>📄 44 pages</span><span>⚡ Renewable Energy</span><span>⭐ All levels</span></div>
            </div>
            <a href="#" class="er-guide-dl" onclick="simulateDownload(event,this)">⬇ PDF</a>
        </div>

    </div>

    <!-- Renewable Energy Video Series -->
    <div class="er-energy-sub-heading" style="margin-top:48px;">🎬 Video Series</div>
    <div class="er-video-series">

        <div class="er-series-card">
            <div class="er-series-header" style="background: linear-gradient(135deg,#f7971e,#ffd200);">
                <span class="er-series-icon">☀️</span>
                <div>
                    <h3>Clean Energy Kitchen</h3>
                    <p>5-part series · Beginner</p>
                </div>
            </div>
            <ul class="er-episode-list">
                <li onclick="openEduVideo('Ep 1: How Solar Power Reaches Your Hob','From solar panel to cooking ring — how photovoltaic electricity is generated, stored, and used in your kitchen.')">▶ Ep 1: Solar Power in Your Kitchen <span>14:20</span></li>
                <li onclick="openEduVideo('Ep 2: Why Induction Cooking is the Greenest','Induction hobs convert up to 90% of energy into heat vs 40% for gas — the science and the savings.')">▶ Ep 2: Why Induction is Greenest <span>11:45</span></li>
                <li onclick="openEduVideo('Ep 3: Home Biogas — Cooking on Food Waste','How small-scale biogas digesters turn kitchen waste into cooking fuel — with a live demonstration.')">▶ Ep 3: Biogas at Home <span>18:30</span></li>
                <li onclick="openEduVideo('Ep 4: Reading Your Energy Tariff','How to identify green electricity tariffs, compare providers, and ensure your cooking is genuinely powered by renewables.')">▶ Ep 4: Green Energy Tariffs <span>13:10</span></li>
                <li onclick="openEduVideo('Ep 5: The Zero-Carbon Kitchen','Combining solar panels, an induction hob, a heat-pump fridge, and smart metering for a fully carbon-neutral kitchen.')">▶ Ep 5: The Zero-Carbon Kitchen <span>22:00</span></li>
            </ul>
        </div>

        <div class="er-series-card">
            <div class="er-series-header" style="background: linear-gradient(135deg,#11998e,#38ef7d);">
                <span class="er-series-icon">🌿</span>
                <div>
                    <h3>Food, Energy & Climate</h3>
                    <p>4-part series · All levels</p>
                </div>
            </div>
            <ul class="er-episode-list">
                <li onclick="openEduVideo('Ep 1: How Agriculture Drives Climate Change','Agriculture accounts for 26% of global greenhouse gas emissions — how farming energy use, deforestation, and livestock methane stack up.')">▶ Ep 1: Agriculture & Emissions <span>20:15</span></li>
                <li onclick="openEduVideo('Ep 2: Renewable Energy Farms','Agrivoltaics — growing crops under solar panels — and how wind farms are being integrated into agricultural land without reducing yield.')">▶ Ep 2: Solar & Wind Farms <span>17:40</span></li>
                <li onclick="openEduVideo('Ep 3: The Future of Green Proteins','Lab-grown meat, precision fermentation, and algae farming use a fraction of the energy of conventional protein — what is it like to eat?')">▶ Ep 3: Green Proteins <span>24:50</span></li>
                <li onclick="openEduVideo('Ep 4: Eating to Lower Your Carbon Footprint','How choosing seasonal, local, plant-forward food — and cooking it efficiently — can cut your personal food carbon footprint by up to 70%.')">▶ Ep 4: Eat Low-Carbon <span>19:05</span></li>
            </ul>
        </div>

    </div>
</section>

<!-- NEWSLETTER STRIP -->
<section class="er-newsletter">
    <div class="er-newsletter-inner">
        <div>
            <h2>📬 Get New Resources Every Week</h2>
            <p>New infographics, guides, and educational videos — delivered to your inbox every Friday.</p>
        </div>
        <form class="er-newsletter-form" onsubmit="subscribeNewsletter(event)">
            <input type="email" placeholder="Your email address" required>
            <button type="submit">Subscribe Free</button>
        </form>
    </div>
    <div id="er-sub-success" class="er-sub-success hidden">✅ You're subscribed! Check your inbox for a welcome pack.</div>
</section>

<footer>
    <div class="social">
        <a href="https://www.facebook.com/foodfusion" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://www.instagram.com/foodfusion" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a href="https://www.youtube.com/foodfusion" target="_blank" rel="noopener noreferrer">YouTube</a>
    </div>
    <p><a href="culinary_resources.php">Culinary Resources</a> | <a href="educational_resources.php">Educational Resources</a> | <a href="privacy_policy.php">Privacy Policy</a> | &copy; 2026 FoodFusion</p>
</footer>

<script>
function filterAll(topic, btn) {
    document.querySelectorAll('.er-topic').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Filter infographic cards and guide items by data-topic
    document.querySelectorAll('.er-info-card, .er-guide-item').forEach(el => {
        el.style.display = (topic === 'all' || el.dataset.topic === topic) ? '' : 'none';
    });

    // Show/hide the full renewable energy section (has its own sub-headings + video series)
    const energySection = document.getElementById('section-renewable-energy');
    if (energySection) {
        energySection.style.display = (topic === 'all' || topic === 'renewable-energy') ? '' : 'none';
    }

    // Show/hide the other video series section (non-energy)
    const videoSection = document.querySelector('.er-section:not(.er-energy-section)');

    // Hide/show sub-headings inside the energy section when filtering to non-energy topics
    document.querySelectorAll('.er-energy-sub-heading').forEach(h => {
        h.style.display = (topic === 'all' || topic === 'renewable-energy') ? '' : 'none';
    });
}
function simulateDownload(e, el) {
    e.preventDefault();
    const orig = el.textContent;
    el.textContent = '⏳';
    setTimeout(() => { el.textContent = '✅'; setTimeout(() => { el.textContent = orig; }, 1500); }, 1200);
}
function openEduVideo(title, desc) {
    document.getElementById('eduModalTitle').textContent = title;
    document.getElementById('eduModalDesc').textContent = desc;
    document.getElementById('eduModal').style.display = 'flex';
}
function closeEduModal() { document.getElementById('eduModal').style.display = 'none'; }
function subscribeNewsletter(e) {
    e.preventDefault();
    document.querySelector('.er-newsletter-form').style.display = 'none';
    const msg = document.getElementById('er-sub-success');
    msg.classList.remove('hidden');
}
</script>

<script>
function toggleNav() {
    var nav = document.querySelector('header nav');
    var btn = document.getElementById('hamburger');
    nav.classList.toggle('open');
    btn.classList.toggle('open');
}
// Close menu when a nav link is clicked
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('header nav a').forEach(function(link) {
        link.addEventListener('click', function() {
            document.querySelector('header nav').classList.remove('open');
            document.getElementById('hamburger').classList.remove('open');
        });
    });
});
</script>
</body>
</html>
