CREATE TABLE IF NOT EXISTS `clients` (
`id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `clients` (`id`, `firstname`, `lastname`, `phone`) VALUES
(1, 'Иван', 'Иванов', '555-55-55'),
(2, 'Петр', 'Петров', '777-77-77'),
(3, 'Дмитрий', 'Дмитров', '333-33-33');