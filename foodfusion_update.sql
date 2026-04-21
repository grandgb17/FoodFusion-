-- ============================================================
-- FoodFusion SAFE UPDATE SQL
-- Run this on your existing database.
-- Uses IF NOT EXISTS everywhere — safe to run multiple times.
-- Does NOT drop or alter any existing tables.
-- ============================================================

USE foodfusion;

-- ── 1. Add missing columns to existing `recipes` table ──────
-- Only adds columns that the PHP code needs, skips if already there
ALTER TABLE recipes
    ADD COLUMN IF NOT EXISTS description   TEXT          DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS story         TEXT          DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS cuisine       VARCHAR(50)   DEFAULT 'General',
    ADD COLUMN IF NOT EXISTS category      VARCHAR(50)   DEFAULT 'general',
    ADD COLUMN IF NOT EXISTS difficulty    ENUM('Easy','Medium','Hard') DEFAULT 'Easy',
    ADD COLUMN IF NOT EXISTS dietary       ENUM('Vegetarian','Non-Vegetarian','Vegan') DEFAULT 'Non-Vegetarian',
    ADD COLUMN IF NOT EXISTS cook_time     VARCHAR(50)   DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS prep_time     VARCHAR(50)   DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS serves        VARCHAR(20)   DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS emoji         VARCHAR(10)   DEFAULT '🍽',
    ADD COLUMN IF NOT EXISTS author_name   VARCHAR(100)  DEFAULT 'Anonymous Chef',
    ADD COLUMN IF NOT EXISTS ingredients   TEXT          DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS instructions  TEXT          DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS tips          TEXT          DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS calories      VARCHAR(20)   DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS protein       VARCHAR(20)   DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS carbs         VARCHAR(20)   DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS fat           VARCHAR(20)   DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS likes         INT           DEFAULT 0,
    ADD COLUMN IF NOT EXISTS source        ENUM('collection','community') DEFAULT 'collection',
    ADD COLUMN IF NOT EXISTS created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP;

-- ── 2. Add missing columns to existing `users` table ────────
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS first_name  VARCHAR(50)  DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS last_name   VARCHAR(50)  DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS preference  ENUM('Vegetarian','Non-Vegetarian','Vegan') DEFAULT 'Non-Vegetarian',
    ADD COLUMN IF NOT EXISTS created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP;

-- ── 3. Ensure contact_messages has all needed columns ────────
ALTER TABLE contact_messages
    ADD COLUMN IF NOT EXISTS subject  VARCHAR(150) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS sent_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP;

-- ── 4. Create saved_recipes table if it doesn't exist ────────
CREATE TABLE IF NOT EXISTS saved_recipes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    recipe_id  INT NOT NULL,
    saved_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (user_id, recipe_id)
);

-- ── 5. Create recipe_likes table if it doesn't exist ─────────
CREATE TABLE IF NOT EXISTS recipe_likes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    recipe_id  INT NOT NULL,
    liked_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (user_id, recipe_id)
);

-- ── 6. Insert sample recipes (only if recipes table is empty) ─
INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Classic Spaghetti Carbonara' AS title,
 'Authentic Roman pasta with crispy pancetta, silky egg yolk sauce and Pecorino Romano. No cream — ever.' AS description,
 'Born in Rome after World War II, carbonara is the ultimate pantry pasta. The trick is tempering the eggs off the heat so they coat the pasta without scrambling.' AS story,
 'Italian' AS cuisine, 'pasta' AS category, 'Easy' AS difficulty, 'Non-Vegetarian' AS dietary,
 '25 mins' AS cook_time, '10 mins' AS prep_time, '4' AS serves, '🍝' AS emoji, 'Chef Carlo R.' AS author_name,
 'Spaghetti 400g|Pancetta or guanciale 150g|Egg yolks 4|Pecorino Romano 80g|Black pepper to taste|Salt for pasta water' AS ingredients,
 'Bring a large pot of salted water to boil and cook spaghetti until al dente.|Fry pancetta in a dry pan over medium heat until crispy. Remove from heat.|Whisk egg yolks with grated Pecorino and black pepper in a bowl.|Reserve 1 cup of pasta water before draining.|Add hot pasta to the pancetta pan off the heat. Toss well.|Add egg mixture and splash of pasta water. Toss quickly until creamy.|Serve immediately with extra Pecorino and black pepper.' AS instructions,
 'Never add cream. The starchy pasta water is your secret weapon for a silky sauce.|Take the pan off the heat before adding eggs or they will scramble.' AS tips,
 '520' AS calories, '28g' AS protein, '68g' AS carbs, '14g' AS fat, 214 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Classic Spaghetti Carbonara');

INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Chicken Tikka Masala' AS title,
 'Tender marinated chicken in a rich, spiced tomato-cream sauce. The most beloved curry in the world for good reason.' AS description,
 'A dish that bridges cultures — marinated chicken cooked in a tandoor then finished in a velvety masala sauce. Comfort food at its finest.' AS story,
 'Indian' AS cuisine, 'curry' AS category, 'Medium' AS difficulty, 'Non-Vegetarian' AS dietary,
 '45 mins' AS cook_time, '20 mins' AS prep_time, '4' AS serves, '🍛' AS emoji, 'Chef Priya N.' AS author_name,
 'Chicken thighs 700g|Plain yoghurt 200ml|Garlic 4 cloves|Ginger 2cm piece|Garam masala 2 tsp|Cumin 1 tsp|Turmeric 1 tsp|Chilli powder 1 tsp|Tomatoes 2 cans|Double cream 150ml|Onion 1 large|Butter 2 tbsp|Coriander to garnish' AS ingredients,
 'Mix yoghurt with garlic, ginger, garam masala, cumin, turmeric and chilli. Marinate chicken for at least 2 hours.|Grill or pan-fry chicken until charred. Set aside.|Fry onion in butter until golden. Add remaining spices and cook 2 mins.|Add canned tomatoes and simmer 15 mins until thickened.|Blend sauce until smooth. Return to pan, add cream and chicken.|Simmer 10 mins. Garnish with coriander and serve with naan.' AS instructions,
 'Marinate overnight for deeper flavour.|Char the chicken — that smoky flavour is essential.|Use thighs not breasts — they stay juicy.' AS tips,
 '480' AS calories, '42g' AS protein, '22g' AS carbs, '24g' AS fat, 187 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Chicken Tikka Masala');

INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Veg Tacos' AS title,
 'Crispy corn tortillas loaded with spiced black beans, roasted corn, avocado and zingy salsa verde.' AS description,
 'Street tacos from Mexico City — vibrant, fast, and completely satisfying without a scrap of meat.' AS story,
 'Mexican' AS cuisine, 'healthy' AS category, 'Easy' AS difficulty, 'Vegetarian' AS dietary,
 '20 mins' AS cook_time, '10 mins' AS prep_time, '3' AS serves, '🌮' AS emoji, 'Chef Maria L.' AS author_name,
 'Corn tortillas 6|Black beans 1 can|Corn kernels 1 cup|Avocado 1|Red onion half|Lime 2|Coriander bunch|Jalapeño 1|Cumin 1 tsp|Smoked paprika 1 tsp|Salt and pepper' AS ingredients,
 'Drain and rinse black beans. Heat in a pan with cumin, paprika, salt and pepper.|Char corn in a dry pan until lightly blackened.|Mash avocado with lime juice and salt.|Dice red onion and jalapeño. Chop coriander.|Warm tortillas directly over a gas flame for 20 seconds each side.|Layer beans, corn, avocado, onion, jalapeño and coriander.|Squeeze lime generously over everything.' AS instructions,
 'Warm tortillas directly on the flame for authentic char.|Add a drizzle of hot sauce for extra kick.' AS tips,
 '320' AS calories, '12g' AS protein, '48g' AS carbs, '9g' AS fat, 156 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Veg Tacos');

INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Beef Lasagna' AS title,
 'Seven layers of slow-cooked ragù, silky béchamel and freshly grated Parmigiano. A weekend labour of love.' AS description,
 'Every Sunday morning, my grandmother would wake up at 6am to start the ragù. She never used a recipe — everything lived in her hands. This is as close as I have ever gotten.' AS story,
 'Italian' AS cuisine, 'pasta' AS category, 'Hard' AS difficulty, 'Non-Vegetarian' AS dietary,
 '2.5 hours' AS cook_time, '30 mins' AS prep_time, '6' AS serves, '🫕' AS emoji, 'Chef Sofia M.' AS author_name,
 'Beef mince 500g|Crushed tomatoes 1 can|Onion 1|Garlic 3 cloves|Bay leaves 2|Red wine 150ml|Olive oil 2 tbsp|Butter 60g|Plain flour 60g|Whole milk 700ml|Nutmeg half tsp|Lasagna sheets 12|Parmigiano Reggiano 200g|Fresh mozzarella 250g' AS ingredients,
 'Sauté onion in olive oil until soft. Add garlic, then beef mince and brown well.|Add red wine and let evaporate. Add tomatoes and bay leaves. Simmer 2 hours.|Make béchamel: melt butter, stir in flour, slowly whisk in warm milk. Season with nutmeg.|Boil lasagna sheets until al dente. Drain and lay flat.|Layer pasta, ragù, béchamel, Parmigiano and mozzarella. Repeat 7 times.|Top with béchamel and Parmigiano. Cover with foil and bake 180°C for 35 mins.|Remove foil and bake 15 mins more until golden. Rest 15 mins before slicing.' AS instructions,
 'Never rush the ragù — 3 hours is better than 2.|Always use freshly grated Parmigiano, never pre-packaged.|Let it rest before cutting or it will collapse.' AS tips,
 '620' AS calories, '38g' AS protein, '52g' AS carbs, '26g' AS fat, 302 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Beef Lasagna');

INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Pad Thai' AS title,
 'Classic Thai stir-fried rice noodles with egg, bean sprouts, peanuts and your choice of protein.' AS description,
 'The ultimate Thai street food — a perfect balance of sweet, sour, salty and umami in every bite.' AS story,
 'Asian' AS cuisine, 'noodles' AS category, 'Medium' AS difficulty, 'Non-Vegetarian' AS dietary,
 '25 mins' AS cook_time, '15 mins' AS prep_time, '2' AS serves, '🍜' AS emoji, 'Chef Somchai K.' AS author_name,
 'Rice noodles 200g|Eggs 2|Bean sprouts 100g|Spring onions 3|Crushed peanuts 4 tbsp|Tamarind paste 3 tbsp|Fish sauce 2 tbsp|Sugar 1 tbsp|Vegetable oil 2 tbsp|Lime 1|Tofu or prawns 150g' AS ingredients,
 'Soak rice noodles in warm water for 20 mins until pliable. Drain.|Mix tamarind paste, fish sauce and sugar in a bowl. Set aside.|Heat oil in a wok until smoking. Add tofu or prawns and cook 2 mins.|Push to side, crack in eggs and scramble.|Add noodles and sauce. Toss everything together over high heat.|Add bean sprouts and toss 1 more minute.|Serve with spring onions, peanuts and lime wedges.' AS instructions,
 'High heat is essential — use your hottest burner.|Do not overcook the noodles when soaking or they will turn mushy.' AS tips,
 '490' AS calories, '26g' AS protein, '72g' AS carbs, '12g' AS fat, 143 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Pad Thai');

INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Chocolate Lava Cake' AS title,
 'Warm chocolate cakes with a molten liquid centre. Ready in 20 minutes, impossible to resist.' AS description,
 'The dessert that made restaurant-goers everywhere fall in love with chocolate all over again. Deceptively simple to make at home.' AS story,
 'French' AS cuisine, 'dessert' AS category, 'Medium' AS difficulty, 'Vegetarian' AS dietary,
 '20 mins' AS cook_time, '10 mins' AS prep_time, '4' AS serves, '🍫' AS emoji, 'Chef Pierre D.' AS author_name,
 'Dark chocolate 200g|Butter 100g|Eggs 4|Caster sugar 100g|Plain flour 60g|Cocoa powder 2 tbsp|Vanilla extract 1 tsp|Pinch of salt|Icing sugar to dust' AS ingredients,
 'Preheat oven to 200°C. Grease 4 ramekins and dust with cocoa powder.|Melt chocolate and butter together in a bowl over simmering water. Cool slightly.|Whisk eggs, sugar and vanilla until pale and thick.|Fold chocolate mixture into egg mixture.|Sift in flour and salt. Fold until just combined. Do not overmix.|Divide between ramekins. Chill 30 mins or bake straight away.|Bake exactly 12 minutes. The edges should be set but centre still wobbly.|Run a knife around edge and turn out onto plate. Dust with icing sugar.' AS instructions,
 'Timing is everything — 12 minutes is the sweet spot.|Chill the batter and bake from cold for a more reliable molten centre.|Use at least 70% dark chocolate for best flavour.' AS tips,
 '440' AS calories, '8g' AS protein, '52g' AS carbs, '22g' AS fat, 389 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Chocolate Lava Cake');

INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Shakshuka' AS title,
 'Eggs poached in a spiced tomato and pepper sauce. The greatest one-pan breakfast in the world.' AS description,
 'A North African and Middle Eastern staple that has taken the whole world by storm. One pan, big flavours, ready in 30 minutes.' AS story,
 'Middle Eastern' AS cuisine, 'healthy' AS category, 'Easy' AS difficulty, 'Vegetarian' AS dietary,
 '25 mins' AS cook_time, '5 mins' AS prep_time, '2' AS serves, '🍳' AS emoji, 'Chef Leila A.' AS author_name,
 'Eggs 4|Canned tomatoes 2 cans|Red peppers 2|Onion 1|Garlic 3 cloves|Cumin 1 tsp|Paprika 1 tsp|Chilli flakes half tsp|Olive oil 2 tbsp|Fresh parsley|Feta cheese optional|Salt and pepper' AS ingredients,
 'Heat olive oil in a wide pan. Sauté onion until soft, about 5 mins.|Add sliced peppers and cook 5 mins more. Add garlic and spices, cook 1 min.|Pour in canned tomatoes. Season well. Simmer 10 mins until sauce thickens.|Make wells in the sauce and crack an egg into each one.|Cover and cook on low heat until whites are set but yolks still runny, about 7 mins.|Crumble feta over the top if using. Garnish with parsley.|Serve straight from the pan with crusty bread.' AS instructions,
 'Keep the yolks runny — that is the whole point.|Serve directly from the pan for maximum drama.|Great with warm pita or crusty sourdough.' AS tips,
 '280' AS calories, '18g' AS protein, '22g' AS carbs, '12g' AS fat, 228 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Shakshuka');

INSERT INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
SELECT * FROM (SELECT
 'Mango Sticky Rice' AS title,
 'Sweet glutinous rice with coconut cream and fresh mango. Thailand is most beloved dessert.' AS description,
 'Every time I visit Thailand this is the first thing I eat at the market. Simple ingredients, extraordinary result.' AS story,
 'Asian' AS cuisine, 'dessert' AS category, 'Easy' AS difficulty, 'Vegan' AS dietary,
 '30 mins' AS cook_time, '10 mins' AS prep_time, '4' AS serves, '🥭' AS emoji, 'Chef Nong P.' AS author_name,
 'Glutinous rice 300g|Coconut milk 400ml|Sugar 4 tbsp|Salt 1 tsp|Ripe mangoes 2|Toasted sesame seeds optional' AS ingredients,
 'Soak glutinous rice in water for at least 4 hours or overnight. Drain.|Steam rice for 25 mins until tender and translucent.|Heat coconut milk with sugar and salt until sugar dissolves. Do not boil.|Mix two thirds of the coconut mixture into the hot rice. Cover and rest 15 mins.|Slice mangoes alongside the stone into elegant fans.|Serve rice moulded alongside mango. Drizzle remaining coconut sauce over.|Scatter sesame seeds if using.' AS instructions,
 'Use ripe yellow mangoes — unripe mango will ruin the dish.|The rice absorbs the coconut milk as it rests — do not skip the 15 minute rest.' AS tips,
 '380' AS calories, '5g' AS protein, '74g' AS carbs, '8g' AS fat, 167 AS likes, 'collection' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE title = 'Mango Sticky Rice');

-- Done!
SELECT CONCAT('✅ Setup complete. Recipes in DB: ', COUNT(*)) AS status FROM recipes;

-- Add image_path column to recipes table (run if upgrading from existing install)
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) DEFAULT NULL;

