# Setup.md
### Following SQL Queries will help creating the database.
### Also checkout **connection.php** for the connection to the database.
CREATE TABLE `users` (
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL PRIMARY KEY,
  `phone` varchar(12) NOT NULL,
  `address` varchar(100) NOT NULL,
  `wallet` float(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `restaurants` (
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL PRIMARY KEY,
  `phone` varchar(12) NOT NULL,
  `address` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `wallet` float NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Offline'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `riders` (
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL PRIMARY KEY,
  `phone` varchar(12) NOT NULL,
  `address` varchar(100) NOT NULL,
  `wallet` float DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Offline',
  `streak` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `support` (
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL PRIMARY KEY,
  `phone` varchar(12) NOT NULL,
  `address` varchar(100) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `menu` (
  `restaurant_id` varchar(40) NOT NULL,
  `sno` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `price` int(10) NOT NULL,
  `discount` int(3) NOT NULL,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`sno`,`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `menu` AUTO_INCREMENT=1;

CREATE TABLE `orders` (
  `order_id` int(20) NOT NULL AUTO_INCREMENT,
  `order_from` varchar(40) NOT NULL,
  `order_by` varchar(40) NOT NULL,
  `rider` varchar(40) NOT NULL,
  `rider_status` varchar(30) NOT NULL DEFAULT 'pending',
  `items` varchar(100) NOT NULL,
  `total` float(20) NOT NULL,
  `instance` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(100) NOT NULL,
  `otp` varchar(5) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'placed',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `orders` AUTO_INCREMENT=100000;

CREATE TABLE `chat_support` (
  `txt_id` int(20) NOT NULL,
  `client` varchar(20) NOT NULL,
  `executive` varchar(30) NOT NULL DEFAULT 'Not Allotted',
  `txt_from` varchar(30) NOT NULL,
  `txt_to` varchar(30) NOT NULL,
  `txt` varchar(200) NOT NULL,
  `instance` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `stats` (
  `ip_address` varchar(30) NOT NULL,
  `coordinates` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'visited',
  `client` varchar(30) NOT NULL DEFAULT 'visit',
  `instance` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

CREATE TABLE `recommend` (
  `res_name` varchar(500) NOT NULL,
  `item_name` varchar(500) NOT NULL,
  `date` date NOT NULL,
  `first` int(20) NOT NULL,
  `second` int(20) NOT NULL,
  `third` int(20) NOT NULL,
  `fourth` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `donate` (
  `restaurant` varchar(30) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `item_quan` varchar(30) NOT NULL,
  `instance` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dummy Data Seeding
INSERT INTO `users` (`name`, `password`, `email`, `phone`, `address`, `wallet`) VALUES
('Admin User', 'admin', 'admin', '1234567890', '123 Main St, Cityville', 100.00),
('Jane Doe', 'password123', 'jane@foodly.test', '0987654321', '456 Oak Ave, Townsburg', 50.00);

INSERT INTO `restaurants` (`name`, `password`, `email`, `phone`, `address`, `description`, `wallet`, `status`) VALUES
('Pizza Palace', 'pizza123', 'contact@pizzapalace.test', '5551234567', '789 Pine Rd, Food City', 'Best pizza in town!', 0, 'Online'),
('Burger Barn', 'burger123', 'hello@burgerbarn.test', '5559876543', '321 Elm St, Food City', 'Juicy burgers and fries.', 0, 'Online'),
('Taco Fiesta', 'taco123', 'hola@tacofiesta.test', '5552345678', '12 Salsa St, Food City', 'Authentic Mexican street tacos.', 0, 'Online'),
('Sushi Zen', 'sushi123', 'order@sushizen.test', '5553456789', '88 Wasabi Way, Food City', 'Fresh sushi and sashimi daily.', 0, 'Online'),
('Curry House', 'curry123', 'namaste@curryhouse.test', '5554567890', '45 Spice Ln, Food City', 'Rich Indian curries and biryani.', 0, 'Online'),
('Noodle Nook', 'noodle123', 'slurp@noodlenook.test', '5555678901', '67 Ramen Rd, Food City', 'Hand-pulled noodles and broth.', 0, 'Offline'),
('Green Bowl', 'green123', 'fresh@greenbowl.test', '5556789012', '90 Garden Ave, Food City', 'Healthy salads and grain bowls.', 0, 'Online'),
('Sweet Tooth Bakery', 'sweet123', 'hello@sweettooth.test', '5557890123', '23 Sugar St, Food City', 'Cakes, pastries and desserts.', 0, 'Online'),
('Dragon Wok', 'dragon123', 'order@dragonwok.test', '5558901234', '15 Bamboo Blvd, Food City', 'Classic Chinese stir-fry and dim sum.', 0, 'Online'),
('Mediterraneo', 'medi123', 'ciao@mediterraneo.test', '5559012345', '34 Olive Grove, Food City', 'Greek and Mediterranean plates.', 0, 'Online'),
('BBQ Smokehouse', 'bbq123', 'pit@bbqsmoke.test', '5550123456', '56 Smoke St, Food City', 'Slow-smoked ribs and brisket.', 0, 'Online'),
('Veggie Delight', 'veggie123', 'eat@veggiedelight.test', '5551112222', '78 Sprout Ln, Food City', 'Pure vegetarian and vegan dishes.', 0, 'Online'),
('Seafood Shack', 'sea123', 'catch@seafoodshack.test', '5552223333', '90 Harbor Rd, Food City', 'Fresh catch, fried and grilled.', 0, 'Online'),
('Cafe Mocha', 'mocha123', 'brew@cafemocha.test', '5553334444', '11 Bean St, Food City', 'Coffee, sandwiches and breakfast.', 0, 'Online'),
('Steak House 21', 'steak123', 'reserve@steakhouse21.test', '5554445555', '21 Grill Ave, Food City', 'Premium steaks and grills.', 0, 'Offline'),
('Pho Real', 'pho123', 'xinchao@phoreal.test', '5555556666', '43 Saigon St, Food City', 'Vietnamese pho and banh mi.', 0, 'Online'),
('Falafel Corner', 'falafel123', 'salaam@falafelcorner.test', '5556667777', '65 Pita Pl, Food City', 'Middle Eastern wraps and mezze.', 0, 'Online'),
('Ice Cream Lab', 'icecream123', 'scoop@icecreamlab.test', '5557778888', '87 Frost Ave, Food City', 'Artisan ice cream and shakes.', 0, 'Online');

INSERT INTO `riders` (`name`, `password`, `email`, `phone`, `address`, `wallet`, `status`, `streak`) VALUES
('Speedy Gonzales', 'rider123', 'speedy@rider.test', '1112223333', '111 Fast Ln, Cityville', 0, 'Online', 5);

INSERT INTO `support` (`name`, `password`, `email`, `phone`, `address`, `status`) VALUES
('Helpful Henry', 'support123', 'henry@support.test', '4445556666', 'Support Center HQ', 'Online');

INSERT INTO `menu` (`restaurant_id`, `name`, `price`, `discount`, `description`) VALUES
('contact@pizzapalace.test', 'Margherita Pizza', 12, 0, 'Classic cheese and tomato pizza.'),
('contact@pizzapalace.test', 'Pepperoni Pizza', 15, 10, 'Spicy pepperoni with mozzarella.'),
('contact@pizzapalace.test', 'Garlic Bread', 5, 0, 'Toasted bread with garlic butter.'),
('hello@burgerbarn.test', 'Classic Cheeseburger', 8, 0, 'Beef patty with cheddar cheese.'),
('hello@burgerbarn.test', 'Bacon Burger', 10, 5, 'Beef patty with crispy bacon.'),
('hello@burgerbarn.test', 'Loaded Fries', 6, 0, 'Fries topped with cheese and jalapenos.'),
('hola@tacofiesta.test', 'Chicken Tacos', 9, 0, 'Three soft tacos with grilled chicken.'),
('hola@tacofiesta.test', 'Beef Burrito', 11, 10, 'Loaded burrito with beans and rice.'),
('hola@tacofiesta.test', 'Nachos Supreme', 8, 0, 'Tortilla chips with cheese and salsa.'),
('order@sushizen.test', 'Salmon Nigiri', 14, 0, 'Two pieces of fresh salmon nigiri.'),
('order@sushizen.test', 'California Roll', 12, 5, 'Crab, avocado and cucumber roll.'),
('order@sushizen.test', 'Miso Soup', 4, 0, 'Traditional soybean paste soup.'),
('namaste@curryhouse.test', 'Chicken Tikka Masala', 13, 0, 'Creamy tomato curry with chicken.'),
('namaste@curryhouse.test', 'Veg Biryani', 11, 10, 'Fragrant rice with mixed vegetables.'),
('namaste@curryhouse.test', 'Garlic Naan', 3, 0, 'Soft flatbread with garlic.'),
('slurp@noodlenook.test', 'Shoyu Ramen', 12, 0, 'Soy-based broth with pork and egg.'),
('slurp@noodlenook.test', 'Pad Thai', 10, 5, 'Stir-fried rice noodles with peanuts.'),
('fresh@greenbowl.test', 'Caesar Salad', 9, 0, 'Romaine, croutons and Caesar dressing.'),
('fresh@greenbowl.test', 'Quinoa Power Bowl', 11, 0, 'Quinoa, chickpeas and roasted veggies.'),
('hello@sweettooth.test', 'Chocolate Cake', 6, 0, 'Rich layered chocolate cake slice.'),
('hello@sweettooth.test', 'Blueberry Muffin', 4, 10, 'Freshly baked muffin with blueberries.'),
('hello@sweettooth.test', 'Cheesecake', 7, 0, 'New York style cheesecake slice.'),
('hello@sweettooth.test', 'Cinnamon Roll', 5, 0, 'Warm roll with cream cheese glaze.'),
('hello@sweettooth.test', 'Macaron Box', 9, 5, 'Assorted box of six French macarons.'),
-- Dragon Wok
('order@dragonwok.test', 'Kung Pao Chicken', 11, 0, 'Diced chicken with peanuts and chili.'),
('order@dragonwok.test', 'Vegetable Spring Rolls', 5, 0, 'Crispy rolls with mixed vegetables.'),
('order@dragonwok.test', 'Sweet and Sour Pork', 12, 10, 'Battered pork with pineapple sauce.'),
('order@dragonwok.test', 'Egg Fried Rice', 7, 0, 'Wok-tossed rice with egg and scallions.'),
('order@dragonwok.test', 'Pork Dumplings', 8, 0, 'Steamed dumplings, six pieces.'),
('order@dragonwok.test', 'Chow Mein', 9, 5, 'Stir-fried noodles with vegetables.'),
-- Mediterraneo
('ciao@mediterraneo.test', 'Chicken Souvlaki', 12, 0, 'Grilled skewers with tzatziki.'),
('ciao@mediterraneo.test', 'Greek Salad', 8, 0, 'Tomato, cucumber, feta and olives.'),
('ciao@mediterraneo.test', 'Lamb Gyro', 11, 10, 'Pita wrap with lamb and salad.'),
('ciao@mediterraneo.test', 'Hummus Plate', 6, 0, 'Creamy hummus with warm pita.'),
('ciao@mediterraneo.test', 'Spanakopita', 7, 0, 'Spinach and feta filo pastry.'),
('ciao@mediterraneo.test', 'Baklava', 5, 0, 'Layered filo with honey and nuts.'),
-- BBQ Smokehouse
('pit@bbqsmoke.test', 'Pulled Pork Sandwich', 10, 0, 'Smoked pork with slaw on a bun.'),
('pit@bbqsmoke.test', 'Beef Brisket Plate', 16, 5, 'Half pound of smoked brisket.'),
('pit@bbqsmoke.test', 'Baby Back Ribs', 18, 0, 'Full rack with house BBQ sauce.'),
('pit@bbqsmoke.test', 'Smoked Wings', 9, 0, 'Eight smoked and glazed wings.'),
('pit@bbqsmoke.test', 'Mac and Cheese', 5, 0, 'Creamy baked mac and cheese.'),
('pit@bbqsmoke.test', 'Cornbread', 3, 0, 'Sweet buttery cornbread slice.'),
-- Veggie Delight
('eat@veggiedelight.test', 'Paneer Tikka', 10, 0, 'Grilled spiced cottage cheese.'),
('eat@veggiedelight.test', 'Veggie Buddha Bowl', 11, 0, 'Grains, greens and roasted veg.'),
('eat@veggiedelight.test', 'Falafel Wrap', 8, 10, 'Falafel, hummus and salad in a wrap.'),
('eat@veggiedelight.test', 'Mushroom Risotto', 12, 0, 'Creamy arborio rice with mushrooms.'),
('eat@veggiedelight.test', 'Vegan Brownie', 5, 0, 'Fudgy dairy-free chocolate brownie.'),
-- Seafood Shack
('catch@seafoodshack.test', 'Fish and Chips', 12, 0, 'Battered cod with fries.'),
('catch@seafoodshack.test', 'Grilled Salmon', 16, 5, 'Salmon fillet with lemon butter.'),
('catch@seafoodshack.test', 'Shrimp Basket', 13, 0, 'Crispy fried shrimp with dip.'),
('catch@seafoodshack.test', 'Lobster Roll', 18, 0, 'Buttered roll loaded with lobster.'),
('catch@seafoodshack.test', 'Clam Chowder', 7, 0, 'Creamy New England chowder.'),
-- Cafe Mocha
('brew@cafemocha.test', 'Cappuccino', 4, 0, 'Espresso with steamed milk foam.'),
('brew@cafemocha.test', 'Avocado Toast', 7, 0, 'Sourdough with smashed avocado.'),
('brew@cafemocha.test', 'Club Sandwich', 9, 10, 'Triple-decker with chicken and bacon.'),
('brew@cafemocha.test', 'Pancake Stack', 8, 0, 'Three pancakes with maple syrup.'),
('brew@cafemocha.test', 'Iced Latte', 5, 0, 'Chilled espresso over milk and ice.'),
-- Steak House 21
('reserve@steakhouse21.test', 'Ribeye Steak', 24, 0, '12oz ribeye with herb butter.'),
('reserve@steakhouse21.test', 'Filet Mignon', 28, 5, 'Tender 8oz center-cut filet.'),
('reserve@steakhouse21.test', 'Grilled Chicken Breast', 15, 0, 'Marinated chicken with veggies.'),
('reserve@steakhouse21.test', 'Loaded Baked Potato', 6, 0, 'Potato with cheese, bacon and chives.'),
('reserve@steakhouse21.test', 'Caesar Side Salad', 5, 0, 'Crisp romaine with parmesan.'),
-- Pho Real
('xinchao@phoreal.test', 'Beef Pho', 11, 0, 'Rice noodle soup with sliced beef.'),
('xinchao@phoreal.test', 'Chicken Pho', 10, 0, 'Rice noodle soup with chicken.'),
('xinchao@phoreal.test', 'Banh Mi Sandwich', 8, 10, 'Baguette with pork and pickled veg.'),
('xinchao@phoreal.test', 'Fresh Spring Rolls', 6, 0, 'Rice paper rolls with shrimp.'),
('xinchao@phoreal.test', 'Vietnamese Iced Coffee', 4, 0, 'Strong coffee with condensed milk.'),
-- Falafel Corner
('salaam@falafelcorner.test', 'Falafel Plate', 9, 0, 'Falafel with rice, salad and tahini.'),
('salaam@falafelcorner.test', 'Chicken Shawarma', 10, 5, 'Spiced chicken wrap with garlic sauce.'),
('salaam@falafelcorner.test', 'Beef Kebab', 12, 0, 'Grilled beef skewers with rice.'),
('salaam@falafelcorner.test', 'Mezze Platter', 11, 0, 'Hummus, baba ganoush and pita.'),
('salaam@falafelcorner.test', 'Stuffed Grape Leaves', 6, 0, 'Rice-filled vine leaves, six pieces.'),
-- Ice Cream Lab
('scoop@icecreamlab.test', 'Single Scoop', 3, 0, 'One scoop of any flavor.'),
('scoop@icecreamlab.test', 'Banana Split', 8, 0, 'Three scoops with banana and toppings.'),
('scoop@icecreamlab.test', 'Chocolate Shake', 6, 10, 'Thick shake with whipped cream.'),
('scoop@icecreamlab.test', 'Waffle Sundae', 9, 0, 'Warm waffle with ice cream and sauce.'),
('scoop@icecreamlab.test', 'Vegan Sorbet', 5, 0, 'Dairy-free fruit sorbet, two scoops.');
