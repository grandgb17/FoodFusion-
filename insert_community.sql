USE foodfusion;

INSERT IGNORE INTO recipes (title, description, cuisine, category, difficulty, dietary, cook_time, author_name, likes, source) VALUES
("Grandma's Secret Lasagna", "Seven layers of slow-cooked beef ragu, hand-rolled pasta, and bechamel so thick it holds its shape on the fork. A Sunday ritual passed down three generations.", 'Italian', 'pasta', 'Hard', 'Non-Vegetarian', '2.5 hours', 'Sofia R.', 214, 'community'),
('Smoky Mango Chicken Curry', 'The secret? Charred mango skin gives a smoky sweetness that transforms a simple curry into something utterly unforgettable.', 'Indian', 'curry', 'Medium', 'Non-Vegetarian', '55 mins', 'Priya N.', 187, 'community'),
('Brown Butter Chocolate Tart', 'Nutty brown butter in the filling takes this from an ordinary tart to a show-stopping dessert. Guests always ask for the recipe.', 'French', 'dessert', 'Medium', 'Vegetarian', '1 hour', 'Marc D.', 302, 'community'),
('Crispy Chickpea Power Bowl', 'Oven-roasted chickpeas with a tahini drizzle over warm farro and roasted sweet potato. Healthy never tasted this bold.', 'Middle Eastern', 'healthy', 'Easy', 'Vegan', '30 mins', 'Aisha K.', 145, 'community'),
('Midnight Carbonara', 'My flatmates mum taught me this in Rome — no cream, ever. Just eggs, guanciale, pecorino, and black pepper. Simple, perfect, and dangerously addictive.', 'Italian', 'pasta', 'Easy', 'Non-Vegetarian', '20 mins', 'Luca M.', 391, 'community'),
('Kerala Fish Curry', 'A coastal classic with raw coconut milk, kodampuli, and freshly ground spices. Every spoonful tastes like a weekend by the sea.', 'Indian', 'curry', 'Medium', 'Non-Vegetarian', '40 mins', 'Rajan V.', 228, 'community');

SELECT CONCAT('Community recipes: ', COUNT(*)) FROM recipes WHERE source='community';
