USE foodfusion;

-- Step 1: Add all needed columns safely
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS description  TEXT         DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS story        TEXT         DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS cuisine      VARCHAR(50)  DEFAULT 'General';
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS category     VARCHAR(50)  DEFAULT 'general';
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS difficulty   VARCHAR(20)  DEFAULT 'Easy';
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS dietary      VARCHAR(30)  DEFAULT 'Non-Vegetarian';
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS cook_time    VARCHAR(50)  DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS prep_time    VARCHAR(50)  DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS serves       VARCHAR(20)  DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS emoji        VARCHAR(10)  DEFAULT '🍽';
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS author_name  VARCHAR(100) DEFAULT 'Anonymous Chef';
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS ingredients  TEXT         DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS instructions TEXT         DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS tips         TEXT         DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS calories     VARCHAR(20)  DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS protein      VARCHAR(20)  DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS carbs        VARCHAR(20)  DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS fat          VARCHAR(20)  DEFAULT NULL;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS likes        INT          DEFAULT 0;
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS source       VARCHAR(20)  DEFAULT 'collection';
ALTER TABLE recipes ADD COLUMN IF NOT EXISTS created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP;

-- Also add to users
ALTER TABLE users ADD COLUMN IF NOT EXISTS first_name  VARCHAR(50) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_name   VARCHAR(50) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS preference  VARCHAR(30) DEFAULT 'Non-Vegetarian';

-- Also add to contact_messages
ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS subject  VARCHAR(150) DEFAULT NULL;
ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS sent_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP;

-- Create saved_recipes if missing
CREATE TABLE IF NOT EXISTS saved_recipes (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    recipe_id INT NOT NULL,
    saved_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (user_id, recipe_id)
);

-- Create recipe_likes if missing
CREATE TABLE IF NOT EXISTS recipe_likes (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    recipe_id INT NOT NULL,
    liked_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (user_id, recipe_id)
);

-- Step 2: Insert sample recipes one by one
INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Classic Spaghetti Carbonara',
  'Authentic Roman pasta with crispy pancetta, silky egg yolk sauce and Pecorino Romano. No cream ever.',
  'Born in Rome after World War II, carbonara is the ultimate pantry pasta. The trick is tempering the eggs off the heat so they coat the pasta without scrambling.',
  'Italian', 'pasta', 'Easy', 'Non-Vegetarian', '25 mins', '10 mins', '4', '🍝', 'Chef Carlo R.',
  'Spaghetti 400g|Pancetta 150g|Egg yolks 4|Pecorino Romano 80g|Black pepper to taste|Salt for pasta water',
  'Boil spaghetti in salted water until al dente.|Fry pancetta in a dry pan until crispy. Remove from heat.|Whisk egg yolks with Pecorino and black pepper.|Reserve 1 cup pasta water then drain pasta.|Add pasta to pancetta pan off the heat. Toss well.|Add egg mixture and a splash of pasta water. Toss until creamy.|Serve with extra Pecorino and black pepper.',
  'Never add cream — pasta water is your secret weapon.|Take pan off heat before adding eggs or they will scramble.',
  '520', '28g', '68g', '14g', 214, 'collection'
);

INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Chicken Tikka Masala',
  'Tender marinated chicken in a rich spiced tomato-cream sauce. The most beloved curry in the world.',
  'A dish that bridges cultures — marinated chicken cooked in a tandoor then finished in a velvety masala sauce. Comfort food at its finest.',
  'Indian', 'curry', 'Medium', 'Non-Vegetarian', '45 mins', '20 mins', '4', '🍛', 'Chef Priya N.',
  'Chicken thighs 700g|Plain yoghurt 200ml|Garlic 4 cloves|Ginger 2cm piece|Garam masala 2 tsp|Cumin 1 tsp|Turmeric 1 tsp|Chilli powder 1 tsp|Canned tomatoes 2|Double cream 150ml|Onion 1 large|Butter 2 tbsp|Coriander to garnish',
  'Mix yoghurt with garlic, ginger and spices. Marinate chicken at least 2 hours.|Grill or pan-fry chicken until charred. Set aside.|Fry onion in butter until golden. Add spices and cook 2 mins.|Add canned tomatoes and simmer 15 mins until thick.|Blend sauce smooth. Add cream and chicken.|Simmer 10 mins. Garnish with coriander. Serve with naan.',
  'Marinate overnight for deeper flavour.|Char the chicken — that smoky flavour is essential.|Use thighs not breasts — they stay juicy.',
  '480', '42g', '22g', '24g', 187, 'collection'
);

INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Veg Tacos',
  'Crispy corn tortillas loaded with spiced black beans, roasted corn, avocado and zingy salsa verde.',
  'Street tacos from Mexico City — vibrant, fast, and completely satisfying without a scrap of meat.',
  'Mexican', 'healthy', 'Easy', 'Vegetarian', '20 mins', '10 mins', '3', '🌮', 'Chef Maria L.',
  'Corn tortillas 6|Black beans 1 can|Corn kernels 1 cup|Avocado 1|Red onion half|Lime 2|Coriander bunch|Jalapeno 1|Cumin 1 tsp|Smoked paprika 1 tsp|Salt and pepper',
  'Drain beans and heat in pan with cumin, paprika, salt and pepper.|Char corn in a dry pan until lightly blackened.|Mash avocado with lime juice and salt.|Dice red onion and jalapeno. Chop coriander.|Warm tortillas over a gas flame 20 seconds each side.|Layer beans, corn, avocado, onion, jalapeno and coriander.|Squeeze lime over everything.',
  'Warm tortillas directly on the flame for authentic char.|Add hot sauce for extra kick.',
  '320', '12g', '48g', '9g', 156, 'collection'
);

INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Beef Lasagna',
  'Seven layers of slow-cooked ragu, silky bechamel and freshly grated Parmigiano. A weekend labour of love.',
  'Every Sunday my grandmother woke at 6am to start the ragu. She never used a recipe — everything lived in her hands. This is as close as I have ever gotten.',
  'Italian', 'pasta', 'Hard', 'Non-Vegetarian', '2.5 hours', '30 mins', '6', '🫕', 'Chef Sofia M.',
  'Beef mince 500g|Crushed tomatoes 1 can|Onion 1|Garlic 3 cloves|Bay leaves 2|Red wine 150ml|Olive oil 2 tbsp|Butter 60g|Plain flour 60g|Whole milk 700ml|Nutmeg half tsp|Lasagna sheets 12|Parmigiano 200g|Mozzarella 250g',
  'Saute onion in olive oil. Add garlic then beef and brown well.|Add red wine and let evaporate. Add tomatoes and bay leaves. Simmer 2 hours.|Make bechamel: melt butter, stir in flour, whisk in warm milk. Season with nutmeg.|Boil lasagna sheets until al dente. Drain and lay flat.|Layer pasta, ragu, bechamel, Parmigiano and mozzarella. Repeat 7 times.|Top with bechamel and Parmigiano. Cover and bake 180C for 35 mins.|Remove foil and bake 15 mins more until golden. Rest 15 mins before slicing.',
  'Never rush the ragu — 3 hours is better than 2.|Always use freshly grated Parmigiano.|Let it rest before cutting or it will collapse.',
  '620', '38g', '52g', '26g', 302, 'collection'
);

INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Pad Thai',
  'Classic Thai stir-fried rice noodles with egg, bean sprouts, peanuts and your choice of protein.',
  'The ultimate Thai street food — a perfect balance of sweet, sour, salty and umami in every bite.',
  'Asian', 'noodles', 'Medium', 'Non-Vegetarian', '25 mins', '15 mins', '2', '🍜', 'Chef Somchai K.',
  'Rice noodles 200g|Eggs 2|Bean sprouts 100g|Spring onions 3|Crushed peanuts 4 tbsp|Tamarind paste 3 tbsp|Fish sauce 2 tbsp|Sugar 1 tbsp|Vegetable oil 2 tbsp|Lime 1|Tofu or prawns 150g',
  'Soak rice noodles in warm water 20 mins. Drain.|Mix tamarind paste, fish sauce and sugar. Set aside.|Heat oil in wok until smoking. Add tofu or prawns and cook 2 mins.|Push to side, crack in eggs and scramble.|Add noodles and sauce. Toss over high heat.|Add bean sprouts and toss 1 more minute.|Serve with spring onions, peanuts and lime wedges.',
  'High heat is essential — use your hottest burner.|Do not overcook noodles when soaking or they turn mushy.',
  '490', '26g', '72g', '12g', 143, 'collection'
);

INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Chocolate Lava Cake',
  'Warm chocolate cakes with a molten liquid centre. Ready in 20 minutes and impossible to resist.',
  'The dessert that made restaurant-goers everywhere fall in love with chocolate all over again. Deceptively simple at home.',
  'French', 'dessert', 'Medium', 'Vegetarian', '20 mins', '10 mins', '4', '🍫', 'Chef Pierre D.',
  'Dark chocolate 200g|Butter 100g|Eggs 4|Caster sugar 100g|Plain flour 60g|Cocoa powder 2 tbsp|Vanilla extract 1 tsp|Pinch of salt|Icing sugar to dust',
  'Preheat oven to 200C. Grease 4 ramekins and dust with cocoa powder.|Melt chocolate and butter together over simmering water. Cool slightly.|Whisk eggs, sugar and vanilla until pale and thick.|Fold chocolate into egg mixture.|Sift in flour and salt. Fold until just combined. Do not overmix.|Divide between ramekins. Chill 30 mins or bake straight away.|Bake exactly 12 minutes until edges set but centre still wobbly.|Run knife around edge, turn out onto plate and dust with icing sugar.',
  'Timing is everything — 12 minutes is the sweet spot.|Chill the batter for a more reliable molten centre.|Use at least 70% dark chocolate for best flavour.',
  '440', '8g', '52g', '22g', 389, 'collection'
);

INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Shakshuka',
  'Eggs poached in a spiced tomato and pepper sauce. The greatest one-pan breakfast in the world.',
  'A North African and Middle Eastern staple that has taken the whole world by storm. One pan, big flavours, 30 minutes.',
  'Middle Eastern', 'healthy', 'Easy', 'Vegetarian', '25 mins', '5 mins', '2', '🍳', 'Chef Leila A.',
  'Eggs 4|Canned tomatoes 2 cans|Red peppers 2|Onion 1|Garlic 3 cloves|Cumin 1 tsp|Paprika 1 tsp|Chilli flakes half tsp|Olive oil 2 tbsp|Fresh parsley|Feta cheese optional|Salt and pepper',
  'Heat oil in a wide pan. Saute onion until soft about 5 mins.|Add sliced peppers and cook 5 mins more. Add garlic and spices, cook 1 min.|Pour in tomatoes. Season well. Simmer 10 mins until thick.|Make wells in sauce and crack an egg into each one.|Cover and cook on low heat until whites set but yolks still runny, about 7 mins.|Crumble feta over the top. Garnish with parsley.|Serve straight from pan with crusty bread.',
  'Keep the yolks runny — that is the whole point.|Serve directly from the pan for maximum drama.|Great with warm pita or sourdough.',
  '280', '18g', '22g', '12g', 228, 'collection'
);

INSERT IGNORE INTO recipes (title, description, story, cuisine, category, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, ingredients, instructions, tips, calories, protein, carbs, fat, likes, source)
VALUES (
  'Mango Sticky Rice',
  'Sweet glutinous rice with coconut cream and fresh mango. Thailand most beloved dessert.',
  'Every time I visit Thailand this is the first thing I eat at the market. Simple ingredients, extraordinary result.',
  'Asian', 'dessert', 'Easy', 'Vegan', '30 mins', '10 mins', '4', '🥭', 'Chef Nong P.',
  'Glutinous rice 300g|Coconut milk 400ml|Sugar 4 tbsp|Salt 1 tsp|Ripe mangoes 2|Toasted sesame seeds optional',
  'Soak glutinous rice in water at least 4 hours or overnight. Drain.|Steam rice 25 mins until tender and translucent.|Heat coconut milk with sugar and salt until dissolved. Do not boil.|Mix two thirds of coconut mixture into hot rice. Cover and rest 15 mins.|Slice mangoes alongside stone into elegant fans.|Serve rice moulded alongside mango. Drizzle remaining coconut sauce over.|Scatter sesame seeds if using.',
  'Use ripe yellow mangoes — unripe mango will ruin the dish.|Do not skip the 15 minute rest — the rice absorbs the coconut milk as it sits.',
  '380', '5g', '74g', '8g', 167, 'collection'
);

-- Confirm
SELECT CONCAT('Done! Total recipes: ', COUNT(*)) AS result FROM recipes;
