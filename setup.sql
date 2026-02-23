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
('Burger Barn', 'burger123', 'hello@burgerbarn.test', '5559876543', '321 Elm St, Food City', 'Juicy burgers and fries.', 0, 'Online');

INSERT INTO `riders` (`name`, `password`, `email`, `phone`, `address`, `wallet`, `status`, `streak`) VALUES
('Speedy Gonzales', 'rider123', 'speedy@rider.test', '1112223333', '111 Fast Ln, Cityville', 0, 'Online', 5);

INSERT INTO `support` (`name`, `password`, `email`, `phone`, `address`, `status`) VALUES
('Helpful Henry', 'support123', 'henry@support.test', '4445556666', 'Support Center HQ', 'Online');

INSERT INTO `menu` (`restaurant_id`, `name`, `price`, `discount`, `description`) VALUES
('contact@pizzapalace.test', 'Margherita Pizza', 12, 0, 'Classic cheese and tomato pizza.'),
('contact@pizzapalace.test', 'Pepperoni Pizza', 15, 10, 'Spicy pepperoni with mozzarella.'),
('hello@burgerbarn.test', 'Classic Cheeseburger', 8, 0, 'Beef patty with cheddar cheese.');
