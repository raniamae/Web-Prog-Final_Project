-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql301.infinityfree.com
-- Generation Time: Feb 10, 2026 at 08:20 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40953391_sinerate`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Comedy'),
(4, 'Crime'),
(5, 'Documentary'),
(6, 'Fantasy'),
(7, 'Horror'),
(8, 'Mystery'),
(9, 'Romance'),
(10, 'Sci-Fi'),
(11, 'Thriller'),
(12, 'Animation');

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `collection_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `added_at` date DEFAULT curdate()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `collections`
--

INSERT INTO `collections` (`collection_id`, `user_id`, `movie_id`, `added_at`) VALUES
(67, 5, 7, '2026-02-03'),
(75, 9, 5, '2026-02-06'),
(72, 9, 3, '2026-02-06'),
(83, 9, 2, '2026-02-06'),
(76, 9, 27, '2026-02-06'),
(89, 3, 7, '2026-02-10'),
(95, 6, 31, '2026-02-10'),
(88, 3, 32, '2026-02-09'),
(90, 3, 28, '2026-02-10'),
(94, 3, 35, '2026-02-10'),
(96, 6, 6, '2026-02-10');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `release_year` year(4) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `poster_url` varchar(255) DEFAULT NULL,
  `backdrop_url` varchar(255) DEFAULT NULL,
  `average_rating` decimal(3,1) DEFAULT 0.0,
  `tmdb_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `description`, `release_year`, `duration_minutes`, `poster_url`, `backdrop_url`, `average_rating`, `tmdb_id`) VALUES
(1, 'Avatar: Fire and Ash', 'A new era rises on Pandora. As fire and ash reshape the world, ancient forces awaken and survival becomes a battle of destiny, power, and unity.', 2025, 197, 'img/avatar_fire_and_ash_ver2.jpg', 'img/avatar_fire_ash.jpg', '8.7', 83533),
(2, 'Maze Runner: The Death Cure', 'Trapped in a deadly maze with no memory and no escape, a group of teens must uncover the truth behind the walls before time runs out.', 2018, 142, 'img/maze_runner_the_death_cure_ver2.jpg', 'img/maze_runner_the_death_cure_ver2.jpg', '10.0', 336843),
(3, 'How to Make a Killing', 'One mistake. One secret. A chain of choices that spiral into chaos. A darkly comedic thriller about ordinary people crossing extraordinary lines.', 2026, 109, 'img/how_to_make_a_killing_ver3.jpg', 'img/killing.jpg', '0.0', 467905),
(4, 'Truth or Dare', 'A simple game becomes a deadly curse. Every choice has a price, and every round brings them closer to a terrifying end.', 2018, 100, 'img/truth_or_dare.jpg', 'img/truth_or_dare.jpg', '0.0', 460019),
(5, 'The Super Mario Galaxy', 'Blast into an epic cosmic adventure as Mario journeys across the stars to save Princess Peach and stop Bowser’s galactic conquest.', 2026, 110, 'img/super_mario_galaxy_movie_ver4.jpg', 'img/supermario.jpg', '10.0', 1226863),
(6, 'Minions: The Rise of Gru', 'Before he became the world’s greatest villain, he was just a boy with a dream. Follow young Gru and his chaotic Minions as they rise through madness, mischief, and mayhem in this hilarious origin story.', 2022, 87, 'img/minions4.jpg', 'img/minions4.jpg', '10.0', 438148),
(7, 'Zootopia 2', 'The city evolves. The danger grows. Judy and Nick return to uncover a deeper conspiracy threatening the balance of Zootopia.', 2025, 110, 'img/zootopia_two_ver5.jpg', 'img/zoo.jpg', '0.0', 1084242),
(8, 'No Hard Feelings', 'A bold deal turns into unexpected connection. A sharp, hilarious comedy about love, growth, and emotional awakening.', 2023, 103, 'img/no_hard_feelings.jpg', 'img/no_hard_feelings.jpg', '0.0', 884605),
(9, 'Kinda Pregnant', 'A fake pregnancy. Real consequences. A wild comedy about identity, lies, and finding purpose in the most unexpected ways.', 2025, 98, 'img/kinda_pregnant.jpg', 'img/kinda_pregnant.jpg', '0.0', 1212142),
(10, 'Haikyu!! The Dumpster Battle', 'Rivals collide. Pride ignites. The legendary match begins as passion, teamwork, and determination clash on the court.', 2024, 85, 'img/gekijoban_haikyu_gomi_suteba_no_kessen.jpg', 'img/gekijoban_haikyu_gomi_suteba_no_kessen.jpg', '0.0', 1012201),
(11, 'Five Nights at Freddy\'s 2', 'The nightmare returns. Darker secrets, deeper fear, and a new level of survival horror begins.', 2025, 104, 'img/five_nights_at_freddys_two.jpg', 'img/five_nights_at_freddys_two.jpg', '0.0', 1228246),
(12, 'Diary of a Wimpy Kid: The Last Straw', 'Family pressure pushes Greg Heffley to his limits. Sent to military school and facing growing expectations, Greg must decide who he really wants to become in this hilarious and heartfelt chapter of his coming-of-age journey.', 2025, 90, 'img/diary_of_a_wimpy_kid_the_last_straw.jpg', 'img/diary_of_a_wimpy_kid_the_last_straw.jpg', '0.0', 1392796),
(13, 'Hot Girls Wanted', 'A raw and unfiltered look into the amateur adult film industry, following young women as they enter a world driven by fame, pressure, and digital exploitation. A powerful documentary exposing the hidden realities behind online fantasy.', 2015, 84, 'img/hot_girls.jpg', 'img/hot_girls.jpg', '0.0', 318256),
(14, 'The Rip', 'When danger strikes beneath the surface, survival becomes a race against time. A high-intensity story of fear, resilience, and human instinct in the face of unstoppable natural force.', 2026, 112, 'img/ripver2.jpg', 'img/ripver2.jpg', '10.0', 1306368),
(15, 'Mystery Team', 'Once legendary kid detectives, now clueless adults. When a real murder case lands in their hands, a group of former child sleuths must prove they still have what it takes — in a wildly funny, chaotic crime comedy.', 2009, 105, 'img/mysteryteam.jpg', 'img/mysteryteam.jpg', '0.0', 38087),
(16, 'Scary Movie', 'A hilarious parody of classic horror films, where screams meet comedy and every scary moment turns into pure chaos. From masked killers to supernatural chaos, this cult comedy delivers nonstop laughs and outrageous satire.', 2000, 88, 'img/scarymovie.jpg', 'img/scarymovie.jpg', '0.0', 4247),
(17, 'Pacific Rim', 'When colossal monsters rise from the sea, humanity builds giants to fight back. Pilots link minds, control massive Jaegers, and wage an epic war for Earth’s survival in this explosive sci-fi action spectacle.', 2013, 131, 'img/pacific_rim_ver3.jpg', 'img/pacific_rim_ver3.jpg', '0.0', 68726),
(18, 'The Princess Bride', 'A timeless fairy-tale adventure of true love, courage, and legend. Heroes, villains, sword fights, and romance collide in a magical journey filled with humor, heart, and unforgettable charm.', 1987, 98, 'img/princess_bride.jpg', 'img/princess_bride.jpg', '0.0', 2493),
(19, 'Anyone But You', 'From enemies to lovers in the most unexpected way. A fake relationship turns into real chaos as two opposites are forced together in a sharp, sexy, and hilarious modern romantic comedy.', 2023, 103, 'img/anyone.jpg', 'img/anyone.jpg', '0.0', 1072790),
(20, 'Mr. Peabody & Sherman', 'A genius dog. A curious boy. One time machine. Travel through history in a fast-paced, hilarious adventure filled with heart, humor, and unforgettable moments for all ages.', 2015, 92, 'img/mr_peabody.jpg', 'img/mr_peabody.jpg', '0.0', 71377),
(21, 'The Devil on Trial', 'Dive into the chilling true story where demonic possession became a real courtroom defense — a gripping Netflix documentary that probes fear, faith, and one of the most bizarre murder trials in U.S. history.', 2023, 81, 'img/devil.jpg', 'img/devil.jpg', '0.0', 1171989),
(22, 'Love Untangled', 'In 1998 Busan, shy high schooler Se?ri tries to straighten her curly hair to win her crush’s heart, but a new transfer student turns her plans — and her world — upside down. A heartwarming story of first love, friendship, and self?acceptance.', 2025, 118, 'img/Untangled.jpg', 'img/Untangled.jpg', '0.0', 1355666),
(23, 'The Thinking Game', 'Step inside the world of Google DeepMind, where brilliant minds and cutting-edge AI collide! Follow the team behind revolutionary breakthroughs like AlphaFold as they push the boundaries of technology, tackle humanity’s toughest challenges, and redefine what machines — and humans — are capable of.', 2025, 83, 'img/thinkinggame.jpg', 'img/thinkinggame.jpg', '0.0', 1276011),
(24, 'A Quiet Place', 'In a world where sound means death, one family must live in silence to survive against relentless sound?hunting monsters — a heart?pounding, edge?of?your?seat thriller that redefines fear.', 2018, 90, 'img/quietplace.jpg', 'img/quietplace.jpg', '0.0', 447332),
(25, 'Bird Box', 'In a ravaged world where seeing means certain death, one mother and her two children must journey blindfolded through terror and chaos to find safety — a gripping post?apocalyptic thriller you won’t forget.', 2018, 124, 'img/birdbox.jpg', 'img/birdbox.jpg', '7.0', 405774),
(26, 'Senior Year', 'A cheerleading accident puts her in a 20?year coma — now 37 and waking into a world she barely recognizes, she’s heading back to high school to reclaim her crown, relive her youth, and prove it’s never too late to finish what you started in this hilarious, feel?good comedy.', 2022, 111, 'img/senioryear.jpg', 'img/senioryear.jpg', '0.0', 800937),
(27, 'Troll 2', 'When a colossal troll awakens and lays waste to Norway, a team of heroes must race against time, unravel ancient secrets, and forge unlikely alliances to stop the unstoppable — an epic monster thriller full of myth, mayhem, and breath?stealing action!', 2025, 95, 'img/Troll2.jpg', 'img/Troll2.jpg', '0.0', 1180831),
(28, 'American Murder: The Family Next Door', 'A chilling true?crime documentary pieced together from real social media posts, texts, and police footage, unraveling the shocking disappearance and murders of a family next door — an intimate, haunting look at how dark secrets hide behind everyday smiles.', 2020, 82, 'img/nextdoor.jpg', 'img/nextdoor.jpg', '0.0', 743601),
(29, 'Theevram', 'A gripping Malayalam crime thriller where a determined musician turns avenger and outsmarts the law in a chilling hunt for justice — a tense tale of revenge, mystery, and moral collision.', 2012, 135, 'img/Thee.jpg', 'img/Thee.jpg', '0.0', 165755),
(30, 'The Tinder Swindler', 'Swipe right on danger—this riveting true?crime documentary follows a charismatic conman who posed as a billionaire to deceive women on Tinder, swindle them out of millions, and ultimately face the fierce women determined to expose him and get justice.', 2022, 114, 'img/tinder.jpg', 'img/tinder.jpg', '0.0', 923632),
(31, 'When Harry Met Sally...', 'When your Dad\'s an undertaker, your Mom\'s in heaven, and your Grandma\'s got a screw loose...it\'s good to have a friend who understands you. Even if he is a boy.', 1989, 96, 'img/whenharrymetsally.jpg', 'img/whenharrymetsally.jpg', '10.0', 639),
(32, 'My Girl', NULL, 1991, 103, 'img/my_girl.jpg', 'img/my_girl.jpg', '9.0', 4032),
(33, 'Tinker Bell', 'Journey into the secret world of Pixie Hollow and hear Tinker Bell speak for the very first time as the astonishing story of Disney\'s most famous fairy is finally revealed in the all-new motion picture \'Tinker Bell.', 2008, 78, 'img/tinker_bell.jpg', 'img/tinker_bell.jpg', '0.0', 13179),
(34, 'Tinker Bell and the Lost Treasure', 'A blue harvest moon will rise, allowing the fairies to use a precious moonstone to restore the Pixie Dust Tree, the source of all their magic. But when Tinker Bell accidentally puts all of Pixie Hollow in jeopardy, she must venture out across the sea on a secret quest to set things right.', 2009, 81, 'img/tinker_bell_treasure.jpg', 'img/tinker_bell_treasure.jpg', '0.0', 25475),
(35, 'Tinker Bell and the Legend of the NeverBeast', 'An ancient myth of a massive creature sparks the curiosity of Tinker Bell and her good friend Fawn, an animal fairy who’s not afraid to break the rules to help an animal in need. But this creature is not welcome in Pixie Hollow — and the scout fairies are determined to capture the mysterious beast, who they fear will destroy their home. Fawn must convince her fairy friends to risk everything to rescue the NeverBeast.', 2014, 77, 'img/tinkerbell_and_the_legend_of_the_neverbeast.jpg', 'img/tinkerbell_and_the_legend_of_the_neverbeast.jpg', '10.0', 297270),
(36, 'Tinker Bell and the Pirate Fairy', 'Zarina, a smart and ambitious dust-keeper fairy who’s captivated by Blue Pixie Dust and its endless possibilities, flees Pixie Hollow and joins forces with the scheming pirates of Skull Rock, who make her captain of their ship. Tinker Bell and her friends must embark on an epic adventure to find Zarina, and together they go sword-to-sword with the band of pirates led by a cabin boy named James, who’ll soon be known as Captain Hook himself.', 2014, 78, 'img/tinker_bell_pirate.jpg', 'img/tinker_bell_pirate.jpg', '0.0', 175112),
(37, 'Secret of the Wings', 'Tinkerbell wanders into the forbidden Winter woods and meets Periwinkle. Together they learn the secret of their wings and try to unite the warm fairies and the winter fairies to help Pixie Hollow.', 2012, 75, 'img/tinker_secret_wings.jpg', 'img/tinker_secret_wings.jpg', '0.0', 75258),
(38, 'Tinker Bell and the Great Fairy Rescue', 'During a summer stay on the mainland, Tinker Bell is accidentally discovered while investigating a little girl\'s fairy house. As the other fairies, led by the brash Vidia, launch a daring rescue in the middle of a fierce storm, Tink develops a special bond with the lonely, little girl.', 2010, 76, 'img/tinnker_bell_rescue.jpg', 'img/tinnker_bell_rescue.jpg', '0.0', 44683),
(39, 'Wicked', 'In the land of Oz, ostracized and misunderstood green-skinned Elphaba is forced to share a room with the popular aristocrat Glinda at Shiz University, and the two\'s unlikely friendship is tested as they begin to fulfill their respective destinies as Glinda the Good and the Wicked Witch of the West.', 2024, 162, 'img/wicked.jpg', 'img/wicked.jpg', '0.0', 402431),
(40, 'Wicked: For Good', 'As an angry mob rises against the Wicked Witch, Glinda and Elphaba will need to come together one final time. With their singular friendship now the fulcrum of their futures, they will need to truly see each other, with honesty and empathy, if they are to change themselves, and all of Oz, for good.', 2025, 137, 'img/wicked_for_good.jpg', 'img/wicked_for_good.jpg', '0.0', 967941),
(41, 'Peter Pan', 'In stifling Edwardian London, Wendy Darling mesmerizes her brothers every night with bedtime tales of swordplay, swashbuckling and the fearsome Captain Hook. But the children become the heroes of an even greater story, when Peter Pan flies into their nursery one night and leads them over moonlit rooftops through a galaxy of stars and to the lush jungles of Neverland.', 2003, 113, 'img/peter_pan.jpg', 'img/peter_pan.jpg', '0.0', 10601),
(42, 'How to Lose a Guy in 10 Days', 'In stifling Edwardian London, Wendy Darling mesmerizes her brothers every night with bedtime tales of swordplay, swashbuckling and the fearsome Captain Hook. But the children become the heroes of an even greater story, when Peter Pan flies into their nursery one night and leads them over moonlit rooftops through a galaxy of stars and to the lush jungles of Neverland.', 2003, 116, 'img/how_to_lose_a_guy_in_10_days.jpg', 'img/how_to_lose_a_guy_in_10_days.jpg', '0.0', 9919),
(43, '10 Things I Hate About You', 'On the first day at his new school, Cameron instantly falls for Bianca, the gorgeous girl of his dreams. The only problem is that Bianca is forbidden to date until her ill-tempered, completely un-dateable older sister Kat goes out, too. In an attempt to solve his problem, Cameron singles out the only guy who could possibly be a match for Kat: a mysterious bad boy with a nasty reputation of his own.', 1999, 97, 'img/10_things_i_hate_about_you.jpg', 'img/10_things_i_hate_about_you.jpg', '0.0', 4951),
(44, 'He\'s Just Not That Into You', 'Have you ever sat by the phone wondering why he said he would call, but didn\'t, or you can\'t figure out why she doesn\'t want to sleep with you anymore, or why your relationship just isn\'t going to the next level... they\'re just not that into you. Gigi just wants a man who says he\'ll call—and does—while Alex advises her to stop waiting by the phone. Beth wants a proposal after years of a committed relationship with her boyfriend, Neil, who sees nothing wrong with the status quo. Janine\'s not sure if she can trust her husband, Ben, who can\'t quite trust himself around Anna. Anna can\'t decide between the sexy married guy, or her straightforward, no-sparks standby, Conor, who can\'t get over the fact that he can\'t have her. And Mary, who\'s found an entire network of loving, supportive men, just needs to find one who\'s straight.', 2009, 129, 'img/hes_just_not_that_into_you.jpg', 'img/hes_just_not_that_into_you.jpg', '0.0', 10184),
(45, 'She\'s All That', 'High school hotshot Zach Siler is the envy of his peers. But his popularity declines sharply when his cheerleader girlfriend, Taylor, leaves him for sleazy reality-television star Brock Hudson. Desperate to revive his fading reputation, Siler agrees to a seemingly impossible challenge. He has six weeks to gain the trust of nerdy outcast Laney Boggs -- and help her to become the school\'s next prom queen.', 1999, 95, 'img/shes_all_that.jpg', 'img/shes_all_that.jpg', '0.0', 10314),
(46, '27 Dresses', 'Altruistic Jane finds herself facing her worst nightmare as her younger sister announces her engagement to the man Jane secretly adores.', 2008, 111, 'img/27_dresses.jpg', 'img/27_dresses.jpg', '0.0', 6557),
(47, 'Doctor Plague', 'Jaded detective John Verney is on the trail of an ancient cult of Plague Doctors which is cutting a bloody swathe through the London underworld. Dismissed by his superiors as gang on gang killings, the murders draw Verney into an obsessive maze of a secret society conspiracy with links to the Jack The Ripper murders of 1888, putting him and his family in grave danger.', 2026, 82, 'img/doctor_plague.jpg', 'img/doctor_plague.jpg', '0.0', 1320756);

-- --------------------------------------------------------

--
-- Table structure for table `movie_categories`
--

CREATE TABLE `movie_categories` (
  `movie_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `movie_categories`
--

INSERT INTO `movie_categories` (`movie_id`, `category_id`) VALUES
(1, 1),
(1, 6),
(2, 1),
(2, 10),
(3, 3),
(3, 11),
(4, 1),
(4, 7),
(5, 2),
(5, 12),
(6, 3),
(6, 12),
(7, 3),
(7, 12),
(8, 3),
(8, 9),
(9, 3),
(9, 9),
(10, 12),
(11, 7),
(12, 3),
(12, 12),
(13, 5),
(14, 4),
(14, 11),
(15, 3),
(15, 8),
(16, 3),
(16, 7),
(17, 1),
(17, 10),
(18, 3),
(19, 3),
(19, 9),
(20, 2),
(20, 12),
(21, 5),
(21, 7),
(22, 3),
(22, 9),
(23, 5),
(24, 7),
(24, 10),
(25, 7),
(25, 10),
(26, 3),
(27, 1),
(27, 2),
(28, 5),
(28, 11),
(29, 1),
(29, 11),
(30, 4),
(31, 3),
(31, 9),
(32, 9),
(33, 12),
(34, 12),
(35, 12),
(36, 12),
(37, 12),
(38, 12),
(39, 6),
(40, 6),
(41, 6),
(42, 3),
(42, 9),
(43, 3),
(43, 9),
(44, 3),
(44, 9),
(45, 3),
(45, 9),
(46, 3),
(46, 9),
(47, 4),
(47, 7),
(47, 11);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` date DEFAULT curdate(),
  `user_id` int(11) DEFAULT NULL,
  `movie_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `rating`, `comment`, `created_at`, `user_id`, `movie_id`) VALUES
(1, 9, 'Amazing visual effects!', '2026-01-29', 1, 1),
(2, 7, 'Good but long.', '2026-01-29', 2, 1),
(3, 10, 'So funny!', '2026-01-29', 1, 6),
(4, 5, 'Too scary for me.', '2026-01-29', 2, 11),
(5, 5, 'It\'s boring', '2026-01-31', 1, 3),
(6, 10, 'love it', '2026-01-31', 1, 14),
(25, 10, 'my hussssssbaannnnnnnnnddddd!!', '2026-02-06', 3, 14),
(34, 8, 'my childhood!!', '2026-02-09', 3, 32),
(21, 10, 'cute', '2026-02-06', 8, 5),
(20, 7, 'hey hey hey', '2026-02-06', 5, 25),
(35, 10, 'love', '2026-02-10', 6, 31),
(15, 10, 'recommend with my whole life! ', '2026-02-04', 6, 1),
(16, 10, 'there are no words. LITERAL SPEECHLESS! my newt T^T </3', '2026-02-04', 6, 2),
(19, 10, 'my fav horror movie </3', '2026-02-05', 6, 32),
(30, 10, 'MASTER GRU !!!!!!!!!!!', '2026-02-06', 6, 6),
(33, 10, 'GRUFFFFFF! :((((((', '2026-02-08', 6, 35),
(32, 10, '\"When you realize you want to spend the rest of your life with somebody, you want the rest of your life to start as soon as possible.\" is my favorite line all through out the movie. I LOVE THIS SO MUCH! I recommend it with my whole life <3', '2026-02-06', 6, 31);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `created_at` date DEFAULT curdate()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `avatar_url`, `created_at`) VALUES
(1, 'MovieBuff99', 'movie@gmail.com', '123456', NULL, '0000-00-00'),
(4, 'anilov', 'anilov@gmail.com', '123456', 'uploads/avatar_4_1769879983.jpg', '2026-01-31'),
(2, 'CinemaLover', 'demo2@test.com', '123456', 'https://i.pinimg.com/736x/8b/ab/b6/8babb626efc652b8b2be5c67f42db3a2.jpg', '2026-01-29'),
(3, 'keggy', 'huelin2006@gmail.com', '20060327', 'uploads/avatar_3_1769951445.jpg', '2026-01-31'),
(5, 'Mae', 'rmmeceda@gmail.com', 'qwe123', 'uploads/avatar_6980ac827ab39.png', '2026-02-02'),
(6, 'pretty', 'alyanaaa1256@gmail.com', 'prettypretty', 'uploads/avatar_6_1770420787.jpg', '2026-02-03'),
(7, 'sparkle', 'sparkle@gmail.com', 'sparklesparkle', 'img/default-avatar.jpg', '2026-02-05'),
(8, 'test', 'test@gmail.com', '123456', 'img/default-avatar.jpg', '2026-02-06'),
(9, 'kiggy', 'kiggy@gmail.com', '123', 'uploads/avatar_698622ffb40e9.png', '2026-02-06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`collection_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `movie_categories`
--
ALTER TABLE `movie_categories`
  ADD PRIMARY KEY (`movie_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
