-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Янв 19 2020 г., 18:50
-- Версия сервера: 10.4.6-MariaDB
-- Версия PHP: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `itstart`
--

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Login` (`Nickname` VARCHAR(30), `UserPass` VARCHAR(35))  BEGIN
    SELECT Users.UserId, Users.Nickname, Users.Priority 
    FROM Users
    WHERE Users.Nickname = Nickname AND Users.UserPswrd = UserPass;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Users_ChangeNick` (`Oldnick` VARCHAR(30), `Newnick` VARCHAR(30), `UserPass` VARCHAR(35))  BEGIN
    DECLARE Usr INT DEFAULT 0;
    DECLARE UsrExist INT DEFAULT 0;
    SELECT count(`UserId`) INTO Usr FROM Users WHERE `Nickname` = Oldnick AND `UserPswrd` = UserPass;
    SELECT count(`UserId`) INTO UsrExist FROM Users WHERE `Nickname` = Newnick;
    IF UsrExist = 0 THEN
    BEGIN
	IF Usr = 1 THEN
	BEGIN
	    UPDATE Users SET Nickname = Newnick WHERE `Nickname` = Oldnick;
	    SELECT 0;
	END;
	ELSE SELECT 1;
	END IF;
    END;
    ELSE SELECT 2;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Users_ChangePassword` (IN `Id` INT, IN `UserPass` VARCHAR(35), IN `NewPass` VARCHAR(35))  BEGIN
    DECLARE Usr INT DEFAULT 0;
    SELECT count(Users.UserId) INTO Usr FROM Users WHERE Users.UserId = Id AND Users.UserPswrd = UserPass;
    IF Usr = 1 THEN
    BEGIN
        UPDATE Users SET UserPswrd = NewPass WHERE UserId = Id;
        SELECT 0;
    END;
    ELSE SELECT 1;
    END IF;
END$$

--
-- Функции
--
CREATE DEFINER=`root`@`localhost` FUNCTION `ChildComments` (`parentId` INT, `parentType` INT) RETURNS INT(11) BEGIN 
  DECLARE CN INT DEFAULT 0;
  SELECT COUNT(comments.ComId) INTO CN FROM comments 
  WHERE `TypePar`=parentType and `Parent`= parentId;
  RETURN CN;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getMark` (`parentId` INT, `parentType` INT, `markType` INT) RETURNS INT(11) BEGIN 
  DECLARE MR INT DEFAULT 0;
  SELECT COUNT(Marks.Mark) INTO MR FROM Marks 
  WHERE `TypePar`=parentType AND `Parent`= parentId AND `Mark` = markType;
  RETURN MR;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `issetMark` (`parentId` INT, `parentType` INT, `UserId` INT, `TypeMark` INT) RETURNS INT(11) BEGIN 
  DECLARE MR INT DEFAULT 0;
  SELECT count(Marks.`Mark`) INTO MR FROM Marks WHERE 
    Marks.`Mark` = TypeMark AND
    Marks.`UserId` = UserId AND
    Marks.`Parent` = parentId AND
    Marks.`TypePar` = parentType;
  RETURN MR;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `articles`
--

CREATE TABLE `articles` (
  `ArtId` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `ArtType` tinyint(4) NOT NULL DEFAULT 0,
  `Name` varchar(40) NOT NULL,
  `Tags` varchar(30) DEFAULT NULL,
  `DatePubl` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `articles`
--

INSERT INTO `articles` (`ArtId`, `UserId`, `Description`, `ArtType`, `Name`, `Tags`, `DatePubl`) VALUES
(43, 1, '12 лет назад, когда я поступал в университет, родители твердили, что мой путь — программирование.', 0, '5 разочарований программиста', 'programming;work', '2019-12-14 13:28:42'),
(61, 1, 'Сегодня будем добавлять статью', 2, 'Можно создавать статьи', 'itstart;help;articles', '2019-12-18 15:22:49'),
(62, 1, 'Сегодня будем редактировать статьи', 2, 'Редактирование статей', 'itsart;article;edit', '2019-12-18 15:30:10');

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `articlesinfo`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `articlesinfo` (
`ArtId` int(10)
,`Name` varchar(40)
,`Description` varchar(100)
,`ArtType` tinyint(4)
,`UserId` int(10)
,`Nickname` varchar(30)
,`Tags` varchar(30)
,`DatePubl` timestamp
,`Rating` bigint(20)
,`Comments` int(11)
);

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `ComId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Parent` int(11) NOT NULL,
  `Textcom` varchar(255) DEFAULT NULL,
  `TypePar` tinyint(4) NOT NULL,
  `DatePubl` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`ComId`, `UserId`, `Parent`, `Textcom`, `TypePar`, `DatePubl`) VALUES
(78, 1, 43, '&lt;p&gt;Фигня это всё&lt;/p&gt;\n', 1, '2020-01-07 21:13:08'),
(86, 25, 43, '&lt;p&gt;Здорово&lt;/p&gt;\n', 1, '2020-01-10 18:29:54'),
(87, 25, 78, '&lt;p&gt;Согласен&lt;/p&gt;\n', 0, '2020-01-10 18:30:02'),
(88, 25, 61, '&lt;p&gt;Ураааа&lt;/p&gt;\n', 1, '2020-01-10 18:42:55'),
(89, 25, 78, '&lt;p&gt;frgr&lt;/p&gt;\n', 0, '2020-01-10 20:39:17'),
(90, 25, 86, '&lt;p&gt;fffff&lt;/p&gt;\n', 0, '2020-01-10 20:43:22'),
(100, 25, 8, '&lt;p&gt;ertyu&lt;/p&gt;\n', 2, '2020-01-13 19:27:55'),
(101, 25, 100, '&lt;p&gt;,kf,kkf&lt;/p&gt;\n', 0, '2020-01-13 19:28:27'),
(102, 25, 100, '&lt;p&gt;dfef4&lt;/p&gt;\n', 0, '2020-01-13 20:55:14');

--
-- Триггеры `comments`
--
DELIMITER $$
CREATE TRIGGER `addComment` BEFORE INSERT ON `comments` FOR EACH ROW BEGIN
    DECLARE n INT DEFAULT 0;
    CASE NEW.TypePar
	WHEN 0 THEN 
	    SELECT COUNT(Comments.ComId) INTO n FROM Comments WHERE Comments.ComId = NEW.Parent;
	WHEN 1 THEN 
	    SELECT COUNT(Articles.ArtId) INTO n FROM Articles WHERE Articles.ArtId = NEW.Parent;
	WHEN 2 THEN 
	    SELECT COUNT(Discussion.DiscId) INTO n FROM Discussion WHERE Discussion.DiscId = NEW.Parent;
	ELSE KILL QUERY CONNECTION_ID();
    END CASE;
    IF n=0 THEN KILL QUERY CONNECTION_ID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `discussion`
--

CREATE TABLE `discussion` (
  `DiscId` int(10) NOT NULL,
  `GroupId` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `Title` varchar(25) NOT NULL,
  `Tags` varchar(30) DEFAULT NULL,
  `DatePubl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `discussion`
--

INSERT INTO `discussion` (`DiscId`, `GroupId`, `UserId`, `Title`, `Tags`, `DatePubl`) VALUES
(8, 11, 25, 'Не работает комп', 'Копмьютеры;Запуск', '2020-01-13 19:10:08'),
(9, 9, 25, 'Помогите нарисовать коров', '', '2020-01-13 21:13:25');

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `discussionsinfo`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `discussionsinfo` (
`DiscId` int(10)
,`UserId` int(10)
,`GroupId` int(10)
,`Title` varchar(25)
,`Tags` varchar(30)
,`DatePubl` timestamp
,`Group` varchar(30)
,`Nickname` varchar(30)
,`Rating` bigint(20)
);

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `groupsinfo`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `groupsinfo` (
`GroupId` int(11)
,`Theme` varchar(25)
,`Title` varchar(30)
,`Description` varchar(255)
,`Owner` varchar(30)
,`OwnerId` int(11)
,`Users` bigint(21)
,`Discussions` bigint(21)
);

-- --------------------------------------------------------

--
-- Структура таблицы `marks`
--

CREATE TABLE `marks` (
  `TypePar` tinyint(4) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Parent` int(11) NOT NULL,
  `Mark` int(11) NOT NULL
) ;

--
-- Дамп данных таблицы `marks`
--

INSERT INTO `marks` (`TypePar`, `UserId`, `Parent`, `Mark`) VALUES
(0, 1, 78, 0),
(0, 25, 78, 0),
(0, 25, 86, 0),
(0, 25, 87, 0),
(1, 1, 43, 0),
(1, 1, 61, 2),
(1, 1, 62, 1),
(1, 25, 43, 0),
(1, 25, 61, 0),
(2, 1, 8, 0),
(2, 1, 9, 1),
(2, 25, 8, 0);

--
-- Триггеры `marks`
--
DELIMITER $$
CREATE TRIGGER `addMark` BEFORE INSERT ON `marks` FOR EACH ROW BEGIN
    DECLARE n INT DEFAULT 0;
    CASE NEW.TypePar
	WHEN 0 THEN 
	    SELECT COUNT(Comments.ComId) INTO n FROM Comments WHERE Comments.ComId = NEW.Parent;
	WHEN 1 THEN 
	    SELECT COUNT(Articles.ArtId) INTO n FROM Articles WHERE Articles.ArtId = NEW.Parent;
	WHEN 2 THEN 
	    SELECT COUNT(Discussion.DiscId) INTO n FROM Discussion WHERE Discussion.DiscId = NEW.Parent;
	ELSE KILL QUERY CONNECTION_ID();
    END CASE;
    IF n=0 THEN KILL QUERY CONNECTION_ID();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `addMarkRating` BEFORE INSERT ON `marks` FOR EACH ROW BEGIN
    DECLARE n INT DEFAULT 0;
	IF NEW.Mark = 0 THEN 
	    SELECT COUNT(Mark) INTO n FROM Marks 
	    WHERE `Parent` = NEW.Parent 
	    AND `TypePar` = NEW.TypePar 
	    AND `UserId` = NEW.UserId
	    AND `Mark` = 1;
	END IF;
	IF NEW.Mark = 1 THEN 
	    SELECT COUNT(Mark) INTO n FROM Marks 
	    WHERE `Parent` = NEW.Parent 
	    AND `TypePar` = NEW.TypePar 
	    AND `UserId` = NEW.UserId
	    AND `Mark` = 0;
	END IF;
    IF n!=0 THEN KILL QUERY CONNECTION_ID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `marksinfo`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `marksinfo` (
`TypePar` tinyint(4)
,`Parent` int(11)
,`Like` int(11)
,`Dislike` int(11)
,`Rating` bigint(12)
);

-- --------------------------------------------------------

--
-- Структура таблицы `participation`
--

CREATE TABLE `participation` (
  `UserId` int(11) NOT NULL,
  `GroupId` int(11) NOT NULL,
  `Post` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `participation`
--

INSERT INTO `participation` (`UserId`, `GroupId`, `Post`) VALUES
(1, 9, 2),
(1, 10, 0),
(25, 7, 0),
(26, 7, 1),
(27, 10, 0);

--
-- Триггеры `participation`
--
DELIMITER $$
CREATE TRIGGER `addParticipation` BEFORE INSERT ON `participation` FOR EACH ROW BEGIN
    DECLARE Owner INT DEFAULT 0;
    SELECT COUNT(GroupId) INTO Owner FROM Usgroup
    WHERE OwnerId = NEW.UserId AND GroupId = NEW.GroupId;
    IF Owner!=0 THEN KILL QUERY CONNECTION_ID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `partinfo`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `partinfo` (
`UserId` int(11)
,`GroupId` int(11)
,`Post` int(11)
,`Nickname` varchar(30)
,`Firstname` varchar(30)
,`Surname` varchar(30)
);

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `search`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `search` (
`Parent` int(11)
,`TypePar` int(1)
,`Title` varchar(255)
,`Description` varchar(100)
,`Nickname` varchar(30)
,`UserId` int(11)
);

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `themes`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `themes` (
`Theme` varchar(25)
,`Pop` bigint(21)
);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `UserId` int(11) NOT NULL,
  `Firstname` varchar(30) NOT NULL,
  `Surname` varchar(30) NOT NULL,
  `Nickname` varchar(30) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Birth` date DEFAULT NULL,
  `City` varchar(30) DEFAULT NULL,
  `Gender` tinyint(1) DEFAULT 0,
  `About` varchar(255) DEFAULT NULL,
  `Priority` int(11) NOT NULL DEFAULT 0,
  `UserPswrd` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`UserId`, `Firstname`, `Surname`, `Nickname`, `Email`, `Birth`, `City`, `Gender`, `About`, `Priority`, `UserPswrd`) VALUES
(1, 'Yaser', 'Admin', 'YASER', 'yaser@local', '1995-07-13', 'Нижний Тагил', 1, 'Я есть', 2, '046de3f0b6c5156148f5d7e7d5d264db'),
(2, 'Александр', 'Иванов', 'Alex', 'Alex@loc.ru', '1994-06-24', 'Москва', 1, 'Специалист по винде. Ещё и вышивать могу, крестиком', 1, '79ea36a38c10e9079d010247fd9cab1e'),
(25, 'Виктор', 'Викторов', 'Victor', 'Victor@itstart.su', NULL, NULL, 0, NULL, 0, 'af38a78e11aa957c13a7222c78c7901b'),
(26, 'Игорь', 'Гориев', 'Igor', 'Igor@loc.ru', NULL, NULL, 0, NULL, 0, '074aa9259339af9b114e61dab082de62'),
(27, 'Дмитрий', 'Баранов', 'Mitekbar', 'mitekbar@yandex.ru', NULL, NULL, 0, NULL, 0, '30e986078bf1a08c28b9613da71810b7');

-- --------------------------------------------------------

--
-- Структура таблицы `usgroup`
--

CREATE TABLE `usgroup` (
  `GroupId` int(11) NOT NULL,
  `OwnerId` int(11) NOT NULL,
  `Title` varchar(30) NOT NULL,
  `Theme` varchar(25) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `usgroup`
--

INSERT INTO `usgroup` (`GroupId`, `OwnerId`, `Title`, `Theme`, `Description`) VALUES
(7, 1, 'Программисты всех стран объеди', 'Разработка ПО', 'Мы пишем чумовые проги'),
(9, 25, 'Школа дизайнеров', 'Дизайн', 'Дизайнеры всех времён хотят с вами связаться'),
(10, 25, 'Векторный мир победил', 'Дизайн', 'Графон оказался сильней'),
(11, 1, 'Вопросы и ответы для пользоват', 'Помощь', 'Вы можете найти ответы в нашем сообществе'),
(12, 1, 'Группа', 'rrrrrrrrrrrrrrrrrrrrrrrrr', 'rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr'),
(13, 1, 'Векторный мир победил', 'Дизайн', 'Графон оказался тупей');

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `viewcomments`
-- (См. Ниже фактическое представление)
--
CREATE TABLE `viewcomments` (
`UserId` int(11)
,`Nickname` varchar(30)
,`CommentId` int(11)
,`Text` varchar(255)
,`Parent` int(11)
,`TypePar` tinyint(4)
,`DatePubl` timestamp
,`Nchild` int(11)
);

-- --------------------------------------------------------

--
-- Структура для представления `articlesinfo`
--
DROP TABLE IF EXISTS `articlesinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `articlesinfo`  AS  select `articles`.`ArtId` AS `ArtId`,`articles`.`Name` AS `Name`,`articles`.`Description` AS `Description`,`articles`.`ArtType` AS `ArtType`,`articles`.`UserId` AS `UserId`,`users`.`Nickname` AS `Nickname`,`articles`.`Tags` AS `Tags`,`articles`.`DatePubl` AS `DatePubl`,if(`marksinfo`.`Rating` is null,0,`marksinfo`.`Rating`) AS `Rating`,`ChildComments`(`articles`.`ArtId`,1) AS `Comments` from ((`articles` join `users` on(`users`.`UserId` = `articles`.`UserId`)) left join `marksinfo` on(`marksinfo`.`Parent` = `articles`.`ArtId` and `marksinfo`.`TypePar` = 1)) ;

-- --------------------------------------------------------

--
-- Структура для представления `discussionsinfo`
--
DROP TABLE IF EXISTS `discussionsinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `discussionsinfo`  AS  select `discussion`.`DiscId` AS `DiscId`,`discussion`.`UserId` AS `UserId`,`discussion`.`GroupId` AS `GroupId`,`discussion`.`Title` AS `Title`,`discussion`.`Tags` AS `Tags`,`discussion`.`DatePubl` AS `DatePubl`,`usgroup`.`Title` AS `Group`,`users`.`Nickname` AS `Nickname`,if(`marksinfo`.`Rating` is null,0,`marksinfo`.`Rating`) AS `Rating` from (((`discussion` join `usgroup` on(`usgroup`.`GroupId` = `discussion`.`GroupId`)) join `users` on(`users`.`UserId` = `discussion`.`UserId`)) left join `marksinfo` on(`marksinfo`.`Parent` = `discussion`.`DiscId` and `marksinfo`.`TypePar` = 2)) ;

-- --------------------------------------------------------

--
-- Структура для представления `groupsinfo`
--
DROP TABLE IF EXISTS `groupsinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `groupsinfo`  AS  select `usgroup`.`GroupId` AS `GroupId`,`usgroup`.`Theme` AS `Theme`,`usgroup`.`Title` AS `Title`,`usgroup`.`Description` AS `Description`,`users`.`Nickname` AS `Owner`,`usgroup`.`OwnerId` AS `OwnerId`,count(`participation`.`UserId`) AS `Users`,count(`discussion`.`DiscId`) AS `Discussions` from (((`usgroup` join `users` on(`users`.`UserId` = `usgroup`.`OwnerId`)) left join `participation` on(`participation`.`GroupId` = `usgroup`.`GroupId`)) left join `discussion` on(`discussion`.`GroupId` = `usgroup`.`GroupId`)) group by `usgroup`.`GroupId` ;

-- --------------------------------------------------------

--
-- Структура для представления `marksinfo`
--
DROP TABLE IF EXISTS `marksinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `marksinfo`  AS  select distinct `marks`.`TypePar` AS `TypePar`,`marks`.`Parent` AS `Parent`,`getMark`(`marks`.`Parent`,`marks`.`TypePar`,0) AS `Like`,`getMark`(`marks`.`Parent`,`marks`.`TypePar`,1) AS `Dislike`,`getMark`(`marks`.`Parent`,`marks`.`TypePar`,0) - `getMark`(`marks`.`Parent`,`marks`.`TypePar`,1) AS `Rating` from `marks` ;

-- --------------------------------------------------------

--
-- Структура для представления `partinfo`
--
DROP TABLE IF EXISTS `partinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `partinfo`  AS  select `participation`.`UserId` AS `UserId`,`participation`.`GroupId` AS `GroupId`,`participation`.`Post` AS `Post`,`users`.`Nickname` AS `Nickname`,`users`.`Firstname` AS `Firstname`,`users`.`Surname` AS `Surname` from (`participation` join `users` on(`users`.`UserId` = `participation`.`UserId`)) where 1 ;

-- --------------------------------------------------------

--
-- Структура для представления `search`
--
DROP TABLE IF EXISTS `search`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `search`  AS  select `sr`.`Parent` AS `Parent`,`sr`.`TypePar` AS `TypePar`,`sr`.`title` AS `Title`,`sr`.`Description` AS `Description`,`users`.`Nickname` AS `Nickname`,`sr`.`UserId` AS `UserId` from ((select 1 AS `TypePar`,`articles`.`ArtId` AS `Parent`,`articles`.`UserId` AS `UserId`,`articles`.`Description` AS `Description`,`articles`.`Name` AS `title` from `articles` union select 2 AS `TypePar`,`usgroup`.`GroupId` AS `Parent`,`usgroup`.`OwnerId` AS `UserId`,`usgroup`.`Title` AS `Title`,`usgroup`.`Description` AS `Description` from `usgroup` union select 3 AS `TypePar`,`discussion`.`DiscId` AS `Parent`,`discussion`.`UserId` AS `UserId`,`discussion`.`Title` AS `Title`,'' AS `Description` from `discussion`) `sr` join `users` on(`users`.`UserId` = `sr`.`UserId`)) ;

-- --------------------------------------------------------

--
-- Структура для представления `themes`
--
DROP TABLE IF EXISTS `themes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `themes`  AS  select distinct `usgroup`.`Theme` AS `Theme`,count(`usgroup`.`Theme`) AS `Pop` from `usgroup` group by `usgroup`.`Theme` order by count(`usgroup`.`Theme`) desc ;

-- --------------------------------------------------------

--
-- Структура для представления `viewcomments`
--
DROP TABLE IF EXISTS `viewcomments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewcomments`  AS  select `users`.`UserId` AS `UserId`,`users`.`Nickname` AS `Nickname`,`comments`.`ComId` AS `CommentId`,`comments`.`Textcom` AS `Text`,`comments`.`Parent` AS `Parent`,`comments`.`TypePar` AS `TypePar`,`comments`.`DatePubl` AS `DatePubl`,`ChildComments`(`comments`.`ComId`,0) AS `Nchild` from (`comments` join `users` on(`users`.`UserId` = `comments`.`UserId`)) ;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`ArtId`,`UserId`),
  ADD KEY `Article_User_FK` (`UserId`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ComId`,`UserId`,`Parent`),
  ADD KEY `Comments_User` (`UserId`);

--
-- Индексы таблицы `discussion`
--
ALTER TABLE `discussion`
  ADD PRIMARY KEY (`DiscId`,`GroupId`,`UserId`),
  ADD KEY `Discussion_Group_FK` (`GroupId`),
  ADD KEY `Discussion_User_FK` (`UserId`);

--
-- Индексы таблицы `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`TypePar`,`UserId`,`Parent`,`Mark`),
  ADD KEY `Marks_User_FK` (`UserId`);

--
-- Индексы таблицы `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`UserId`,`GroupId`),
  ADD KEY `Partic_Group_FK` (`GroupId`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`),
  ADD UNIQUE KEY `UserId` (`UserId`),
  ADD UNIQUE KEY `Nickname` (`Nickname`),
  ADD KEY `User_login` (`Nickname`);

--
-- Индексы таблицы `usgroup`
--
ALTER TABLE `usgroup`
  ADD PRIMARY KEY (`GroupId`,`OwnerId`),
  ADD KEY `Group_Owner_FK` (`OwnerId`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `articles`
--
ALTER TABLE `articles`
  MODIFY `ArtId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `ComId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT для таблицы `discussion`
--
ALTER TABLE `discussion`
  MODIFY `DiscId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `usgroup`
--
ALTER TABLE `usgroup`
  MODIFY `GroupId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `Article_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `Comments_User` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

--
-- Ограничения внешнего ключа таблицы `discussion`
--
ALTER TABLE `discussion`
  ADD CONSTRAINT `Discussion_Group_FK` FOREIGN KEY (`GroupId`) REFERENCES `usgroup` (`GroupId`),
  ADD CONSTRAINT `Discussion_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

--
-- Ограничения внешнего ключа таблицы `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `Marks_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

--
-- Ограничения внешнего ключа таблицы `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `Partic_Group_FK` FOREIGN KEY (`GroupId`) REFERENCES `usgroup` (`GroupId`),
  ADD CONSTRAINT `Partic_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

--
-- Ограничения внешнего ключа таблицы `usgroup`
--
ALTER TABLE `usgroup`
  ADD CONSTRAINT `Group_Owner_FK` FOREIGN KEY (`OwnerId`) REFERENCES `users` (`UserId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `search`  AS  select `sr`.`Parent` AS `Parent`,`sr`.`TypePar` AS `TypePar`,`sr`.`title` AS `Title`,`sr`.`Description` AS `Description`,`users`.`Nickname` AS `Nickname`,`sr`.`UserId` AS `UserId` from ((select 1 AS `TypePar`,`articles`.`ArtId` AS `Parent`,`articles`.`UserId` AS `UserId`,`articles`.`Description` AS `Description`,`articles`.`Name` AS `title` from `articles` union select 2 AS `TypePar`,`usgroup`.`GroupId` AS `Parent`,`usgroup`.`OwnerId` AS `UserId`,`usgroup`.`Title` AS `Title`,`usgroup`.`Description` AS `Description` from `usgroup` union select 3 AS `TypePar`,`discussion`.`DiscId` AS `Parent`,`discussion`.`UserId` AS `UserId`,`discussion`.`Title` AS `Title`,'' AS `Description` from `discussion`) `sr` join `users` on(`users`.`UserId` = `sr`.`UserId`)) ;