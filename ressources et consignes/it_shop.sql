-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 03 juin 2025 à 06:50
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `it_shop`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `mot_de_passe_hash` char(64) NOT NULL,
  `role` enum('gerant','assistant') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id`, `login`, `mot_de_passe_hash`, `role`) VALUES
(1, 'gerant', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'gerant'),
(2, 'assistant', 'f8638b979b2f4f793ddb6dbd197e0ee25a7a6ea32b0ae22f5e3c5d119d839e75', 'assistant');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `description` text,
  `prix` decimal(10,2) DEFAULT NULL,
  `quantite_en_stock` int DEFAULT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `marque` varchar(255) DEFAULT NULL,
  `date_ajout` date DEFAULT NULL,
  `evaluation_moyenne` decimal(3,2) DEFAULT NULL,
  `statut` enum('disponible','en rupture') DEFAULT NULL,
  `photo_produit` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `prix`, `quantite_en_stock`, `categorie`, `marque`, `date_ajout`, `evaluation_moyenne`, `statut`, `photo_produit`) VALUES
(1, 'Echo Dot (4ème génération)', 'L\'Echo Dot est une enceinte intelligente compacte qui s\'intègre parfaitement dans les petits espaces. Profitez d\'un son riche et de qualité, contrôlez votre musique par la voix avec Alexa, et gérez vos appareils connectés.', 59.99, 100, 'Électronique', 'Amazon', '2025-03-16', 4.70, 'disponible', 'echo_dot.jpg'),
(2, 'Kindle Paperwhite', 'La Kindle Paperwhite est une liseuse électronique avec un écran haute résolution de 300 ppi, offrant une lecture sans reflets même en plein soleil. Elle est étanche, vous permettant de lire à la plage ou dans le bain.', 129.99, 0, 'Électronique', 'Amazon', '2025-03-16', 4.80, 'disponible', 'kindle_paperwhite.jpg'),
(3, 'Samsung Galaxy S21', 'Le Samsung Galaxy S21 est un smartphone haut de gamme avec un écran AMOLED de 6,2 pouces, un processeur puissant et un système de caméra avancé. Il offre une expérience utilisateur fluide et des performances exceptionnelles.', 799.99, 30, 'Téléphonie', 'Samsung', '2025-03-16', 4.50, 'disponible', 'galaxy_s21.jpg'),
(4, 'Apple MacBook Air', 'L\'Apple MacBook Air avec puce M1 est un ordinateur portable ultra-fin et léger, offrant des performances révolutionnaires et une autonomie impressionnante. Idéal pour le travail et les loisirs.', 999.99, 0, 'Informatique', 'Apple', '2025-03-16', 4.60, 'en rupture', 'macbook_air.jpg'),
(5, 'Sony WH-1000XM4', 'Le casque sans fil Sony WH-1000XM4 offre une réduction de bruit de pointe, un son haute résolution et un confort exceptionnel. Profitez de votre musique sans interruptions, où que vous soyez.', 349.99, 0, 'Audio', 'Sony', '2025-03-16', 4.70, 'disponible', 'sony_wh1000xm4.jpg'),
(6, 'Canon EOS R5', 'L\'appareil photo hybride Canon EOS R5 est conçu pour les professionnels, avec un capteur plein format de 45 mégapixels, une vidéo 8K et une stabilisation d\'image intégrée. Capturez des images et des vidéos d\'une qualité exceptionnelle.', 3899.99, 10, 'Photo', 'Canon', '2025-03-16', 4.90, 'disponible', 'canon_eos_r5.jpg'),
(7, 'Dyson V11 Absolute', 'L\'aspirateur sans fil Dyson V11 Absolute offre une puissance d\'aspiration inégalée et une autonomie prolongée. Il est équipé d\'un écran LCD qui affiche les performances en temps réel et les rappels d\'entretien.', 599.99, 25, 'Maison', 'Dyson', '2025-03-16', 4.80, 'disponible', 'dyson_v11.jpg'),
(8, 'Nintendo Switch', 'La Nintendo Switch est une console de jeu hybride qui peut être utilisée à la fois comme console de salon et console portable. Profitez d\'une vaste bibliothèque de jeux et de la flexibilité de jouer où que vous soyez.', 299.99, 60, 'Jeux vidéo', 'Nintendo', '2025-03-16', 4.70, 'disponible', 'nintendo_switch.jpg'),
(9, 'Bose SoundLink Revolve', 'L\'enceinte Bluetooth portable Bose SoundLink Revolve offre un son à 360 degrés pour une expérience d\'écoute immersive. Elle est résistante à l\'eau et dispose d\'une autonomie de 12 heures.', 199.99, 35, 'Audio', 'Bose', '2025-03-16', 4.60, 'disponible', 'bose_soundlink.jpg'),
(10, 'GoPro HERO9 Black', 'La GoPro HERO9 Black est une caméra d\'action avec une résolution de 5K, une stabilisation d\'image avancée et un écran avant pour les selfies. Capturez des vidéos et des photos incroyables dans toutes les conditions.', 449.99, 15, 'Photo', 'GoPro', '2025-03-16', 4.80, 'disponible', 'gopro_hero9.jpg'),
(11, 'Samsung QLED TV', 'Le téléviseur Samsung QLED offre une qualité d\'image 4K exceptionnelle avec des couleurs vives et des contrastes profonds. Profitez d\'une expérience de visionnage immersive avec la technologie QLED.', 1199.99, 20, 'Électronique', 'Samsung', '2025-03-16', 4.70, 'disponible', 'samsung_qled.jpg'),
(12, 'Apple iPad Pro', 'L\'Apple iPad Pro est une tablette puissante avec un écran Retina de 12,9 pouces, idéale pour le travail créatif et les loisirs. Elle est compatible avec l\'Apple Pencil et le Magic Keyboard.', 1099.99, 25, 'Informatique', 'Apple', '2025-03-16', 4.80, 'disponible', 'ipad_pro.jpg'),
(13, 'JBL Flip 5', 'L\'enceinte Bluetooth JBL Flip 5 est étanche et offre un son puissant avec des basses profondes. Emportez-la partout avec vous et profitez de votre musique préférée pendant 12 heures.', 129.99, 50, 'Audio', 'JBL', '2025-03-16', 4.50, 'disponible', 'jbl_flip5.jpg'),
(14, 'Sony Alpha 7 IV', 'L\'appareil photo hybride Sony Alpha 7 IV est doté d\'un capteur plein format de 33 mégapixels, d\'une mise au point automatique rapide et d\'une vidéo 4K. Capturez des images et des vidéos de haute qualité.', 2499.99, 10, 'Photo', 'Sony', '2025-03-16', 4.90, 'disponible', 'sony_alpha7.jpg'),
(15, 'Dyson Airwrap', 'Le Dyson Airwrap est un styler multi-fonctions pour cheveux qui utilise l\'air pour coiffer et sécher en même temps. Créez des boucles, des ondulations et des coiffures lisses sans chaleur extrême.', 499.99, 30, 'Beauté', 'Dyson', '2025-03-16', 4.80, 'disponible', 'dyson_airwrap.jpg'),
(16, 'PlayStation 5', 'La PlayStation 5 est une console de jeu nouvelle génération offrant des graphismes époustouflants, des temps de chargement rapides et une immersion totale grâce à la manette DualSense.', 499.99, 40, 'Jeux vidéo', 'Sony', '2025-03-16', 4.70, 'disponible', 'ps5.jpg'),
(17, 'Bose QuietComfort 35 II', 'Le casque sans fil Bose QuietComfort 35 II offre une réduction de bruit exceptionnelle, un son de haute qualité et un confort optimal. Profitez de votre musique sans distractions.', 299.99, 35, 'Audio', 'Bose', '2025-03-16', 4.60, 'disponible', 'bose_qc35.jpg'),
(18, 'Canon EOS M50', 'L\'appareil photo hybride Canon EOS M50 est compact et léger, avec un capteur APS-C de 24,1 mégapixels et une vidéo 4K. Idéal pour les vloggers et les photographes en déplacement.', 699.99, 20, 'Photo', 'Canon', '2025-03-16', 4.70, 'disponible', 'canon_eos_m50.jpg'),
(19, 'Samsung Galaxy Tab S7', 'La Samsung Galaxy Tab S7 est une tablette puissante avec un écran AMOLED de 11 pouces, idéale pour le travail et les loisirs. Elle est compatible avec le S Pen pour une productivité accrue.', 649.99, 25, 'Informatique', 'Samsung', '2025-03-16', 4.60, 'disponible', 'galaxy_tab_s7.jpg'),
(20, 'Nintendo Switch Lite', 'La Nintendo Switch Lite est une console de jeu portable conçue pour le jeu en déplacement. Elle est compacte, légère et compatible avec tous les jeux Nintendo Switch qui prennent en charge le mode portable.', 199.99, 50, 'Jeux vidéo', 'Nintendo', '2025-03-16', 4.50, 'disponible', 'switch_lite.jpg'),
(21, 'Fitbit Charge 5', 'Le Fitbit Charge 5 est un bracelet connecté avec suivi de la santé et du fitness, incluant un GPS intégré, un suivi du sommeil et des notifications intelligentes.', 149.99, 40, 'Santé', 'Fitbit', '2025-03-16', 4.60, 'disponible', 'fitbit_charge5.jpg'),
(22, 'Logitech MX Master 3', 'La souris sans fil Logitech MX Master 3 offre une précision et un confort exceptionnels, avec une molette de défilement électromagnétique et une autonomie longue durée.', 99.99, 50, 'Informatique', 'Logitech', '2025-03-16', 4.80, 'disponible', 'logitech_mx_master3.jpg'),
(23, 'Razer BlackWidow V3', 'Le clavier mécanique Razer BlackWidow V3 est conçu pour les gamers, avec des switches mécaniques Razer Green, un rétroéclairage RGB et une durabilité exceptionnelle.', 139.99, 30, 'Informatique', 'Razer', '2025-03-16', 4.70, 'en rupture', 'razer_blackwidow_v3.jpg'),
(24, 'Garmin Forerunner 945', 'La montre GPS Garmin Forerunner 945 est idéale pour les athlètes, avec des fonctionnalités avancées de suivi de la performance, des cartes intégrées et une autonomie prolongée.', 599.99, 20, 'Santé', 'Garmin', '2025-03-16', 4.90, 'disponible', 'garmin_forerunner_945.jpg'),
(25, 'HP Envy 13', 'L\'ordinateur portable HP Envy 13 offre des performances puissantes dans un design élégant et compact, avec un écran Full HD, un processeur Intel Core i7 et une autonomie longue durée.', 899.99, 25, 'Informatique', 'HP', '2025-03-16', 4.60, 'disponible', 'hp_envy_13.jpg'),
(26, 'Philips Hue Starter Kit', 'Le kit de démarrage Philips Hue comprend trois ampoules intelligentes et un pont de connexion, permettant de contrôler l\'éclairage de votre maison via une application mobile ou des commandes vocales.', 199.99, 35, 'Maison', 'Philips', '2025-03-16', 4.80, 'disponible', 'philips_hue.jpg'),
(27, 'Anker PowerCore 20000', 'La batterie externe Anker PowerCore 20000 offre une capacité élevée de 20000mAh, permettant de recharger plusieurs appareils en déplacement. Elle est compacte et légère.', 49.99, 60, 'Accessoires', 'Anker', '2025-03-16', 4.70, 'disponible', 'anker_powercore.jpg'),
(28, 'Instant Pot Duo 7-en-1', 'L\'Instant Pot Duo est un multicuiseur 7-en-1 qui combine une cocotte-minute, une mijoteuse, un cuiseur à riz, un cuiseur vapeur, une sauteuse, une yaourtière et un réchauffeur.', 99.99, 40, 'Cuisine', 'Instant Pot', '2025-03-16', 4.80, 'disponible', 'instant_pot_duo.jpg'),
(29, 'Ring Video Doorbell 4', 'La sonnette vidéo Ring Video Doorbell 4 offre une surveillance de votre porte d\'entrée avec une vidéo HD, une détection de mouvement avancée et des notifications en temps réel.', 199.99, 30, 'Sécurité', 'Ring', '2025-03-16', 4.60, 'en rupture', 'ring_video_doorbell.jpg'),
(30, 'Nespresso VertuoPlus', 'La machine à café Nespresso VertuoPlus offre une expérience de café haut de gamme avec une technologie de centrifusion, permettant de préparer des cafés et des espressos de qualité.', 149.99, 50, 'Cuisine', 'Nespresso', '2025-03-16', 4.70, 'disponible', 'nespresso_vertuo.jpg'),
(31, 'Tile Pro', 'Le Tile Pro est un traqueur Bluetooth puissant qui vous aide à retrouver vos objets perdus. Il offre une portée étendue et une alarme sonore pour localiser facilement vos affaires.', 34.99, 70, 'Accessoires', 'Tile', '2025-03-16', 4.60, 'disponible', 'tile_pro.jpg'),
(32, 'Sony Bravia X90J', 'Le téléviseur Sony Bravia X90J offre une qualité d\'image 4K exceptionnelle avec la technologie HDR et un processeur puissant pour une expérience de visionnage immersive.', 1299.99, 15, 'Électronique', 'Sony', '2025-03-16', 4.80, 'disponible', 'sony_bravia.jpg'),
(33, 'Apple Watch Series 7', 'L\'Apple Watch Series 7 est une montre connectée avec un écran plus grand, une charge rapide et des fonctionnalités avancées de suivi de la santé et du fitness.', 399.99, 40, 'Santé', 'Apple', '2025-03-16', 4.70, 'disponible', 'apple_watch.jpg'),
(34, 'Bose Home Speaker 500', 'L\'enceinte Bose Home Speaker 500 offre un son stéréo puissant et une connectivité intelligente avec Alexa et Google Assistant intégrés. Profitez de votre musique préférée avec une qualité sonore exceptionnelle.', 299.99, 25, 'Audio', 'Bose', '2025-03-16', 4.60, 'disponible', 'bose_home_speaker.jpg'),
(35, 'DJI Mavic Air 2', 'Le drone DJI Mavic Air 2 offre des performances de vol avancées et une caméra 4K pour capturer des vidéos et des photos aériennes de haute qualité. Il est compact et facile à transporter.', 799.99, 20, 'Photo', 'DJI', '2025-03-16', 4.80, 'en rupture', 'dji_mavic_air.jpg'),
(36, 'Microsoft Surface Pro 7', 'La Microsoft Surface Pro 7 est une tablette 2-en-1 puissante avec un écran PixelSense, un processeur Intel Core i5 et une autonomie longue durée. Idéale pour le travail et les loisirs.', 899.99, 30, 'Informatique', 'Microsoft', '2025-03-16', 4.70, 'disponible', 'surface_pro.jpg'),
(37, 'GoPro MAX', 'La GoPro MAX est une caméra 360° qui capture des vidéos et des photos immersives. Elle offre une stabilisation d\'image avancée et des fonctionnalités de montage intuitives.', 499.99, 15, 'Photo', 'GoPro', '2025-03-16', 4.80, 'disponible', 'gopro_max.jpg'),
(38, 'KitchenAid Artisan', 'Le robot pâtissier KitchenAid Artisan est un appareil de cuisine polyvalent avec un moteur puissant et une gamme d\'accessoires pour préparer une variété de recettes.', 399.99, 20, 'Cuisine', 'KitchenAid', '2025-03-16', 4.70, 'en rupture', 'kitchenaid_artisan.jpg'),
(39, 'Fitbit Versa 3', 'La montre connectée Fitbit Versa 3 offre un suivi avancé de la santé et du fitness, avec un GPS intégré, des notifications intelligentes et une autonomie longue durée.', 229.99, 35, 'Santé', 'Fitbit', '2025-03-16', 4.60, 'disponible', 'fitbit_versa.jpg'),
(40, 'Samsung Galaxy Buds Pro', 'Les écouteurs sans fil Samsung Galaxy Buds Pro offrent une qualité sonore exceptionnelle, une réduction de bruit active et une autonomie prolongée. Idéaux pour écouter de la musique en déplacement.', 199.99, 50, 'Audio', 'Samsung', '2025-03-16', 4.70, 'disponible', 'galaxy_buds_pro.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
