----------------------------------------------------+
-- 						Tables						|
----------------------------------------------------+
CREATE TABLE `users` (
  `UserId` int(11) NOT NULL AUTO_INCREMENT,
  `Firstname` varchar(30) NOT NULL,
  `Surname` varchar(30) NOT NULL,
  `Nickname` varchar(30) NOT NULL UNIQUE,
  `Email` varchar(30) NOT NULL,
  `Birth` date DEFAULT NULL,
  `City` varchar(30) DEFAULT NULL,
  `Gender` tinyint(1) DEFAULT 0,
  `About` varchar(255) DEFAULT NULL,
  `Priority` int(11) NOT NULL DEFAULT 0,
  `UserPswrd` varchar(35) NOT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `articles` (
  `ArtId` int(10) NOT NULL AUTO_INCREMENT,
  `UserId` int(10) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `ArtType` tinyint(4) NOT NULL DEFAULT 0,
  `Name` varchar(40) NOT NULL,
  `Tags` varchar(30) DEFAULT NULL,
  `DatePubl` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ArtId`,`UserId`),
  CONSTRAINT `Article_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `usgroup` (
  `GroupId` int(11) NOT NULL AUTO_INCREMENT,
  `OwnerId` int(11) NOT NULL,
  `Title` varchar(30) NOT NULL,
  `Theme` varchar(25) NOT NULL,
  `Description` varchar(255) NOT NULL,
  PRIMARY KEY (`GroupId`,`OwnerId`),
  CONSTRAINT `Group_Owner_FK` FOREIGN KEY (`OwnerId`) REFERENCES `users` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `discussion` (
  `DiscId` int(10) NOT NULL AUTO_INCREMENT,
  `GroupId` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `Title` varchar(25) NOT NULL,
  `Tags` varchar(30) DEFAULT NULL,
  `DatePubl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`DiscId`,`GroupId`,`UserId`),
  CONSTRAINT `Discussion_Group_FK` FOREIGN KEY (`GroupId`) REFERENCES `usgroup` (`GroupId`),
  CONSTRAINT `Discussion_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `participation` (
  `UserId` int(11) NOT NULL,
  `GroupId` int(11) NOT NULL,
  `Post` int(11) DEFAULT NULL,
  PRIMARY KEY (`UserId`,`GroupId`),
  CONSTRAINT `Partic_Group_FK` FOREIGN KEY (`GroupId`) REFERENCES `usgroup` (`GroupId`),
  CONSTRAINT `Partic_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
  `ComId` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `Parent` int(11) NOT NULL,
  `Textcom` varchar(255) DEFAULT NULL,
  `TypePar` tinyint(4) NOT NULL,
  `DatePubl` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ComId`,`UserId`,`Parent`),
  CONSTRAINT `Comments_User` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `marks` (
  `TypePar` tinyint(4) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Parent` int(11) NOT NULL,
  `Mark` int(11) NOT NULL,
  PRIMARY KEY (`TypePar`,`UserId`,`Parent`,`Mark`),
  CONSTRAINT `Marks_User_FK` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`)

);

----------------------------------------------------+
-- 					Procudures						|
----------------------------------------------------+
DELIMITER $$
CREATE PROCEDURE `Login` (`Nickname` VARCHAR(30), `UserPass` VARCHAR(35))  BEGIN
    SELECT Users.UserId, Users.Nickname, Users.Priority 
    FROM Users
    WHERE Users.Nickname = Nickname AND Users.UserPswrd = UserPass;
END$$

CREATE PROCEDURE `Users_ChangeNick` (`Oldnick` VARCHAR(30), `Newnick` VARCHAR(30), `UserPass` VARCHAR(35))  BEGIN
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

CREATE PROCEDURE `Users_ChangePassword` (IN `Id` INT, IN `UserPass` VARCHAR(35), IN `NewPass` VARCHAR(35))  BEGIN
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

----------------------------------------------------+
-- 					Functions						|
----------------------------------------------------+
CREATE FUNCTION `ChildComments` (`parentId` INT, `parentType` INT) RETURNS INT(11) BEGIN 
  DECLARE CN INT DEFAULT 0;
  SELECT COUNT(comments.ComId) INTO CN FROM comments 
  WHERE `TypePar`=parentType and `Parent`= parentId;
  RETURN CN;
END$$

CREATE FUNCTION `getMark` (`parentId` INT, `parentType` INT, `markType` INT) RETURNS INT(11) BEGIN 
  DECLARE MR INT DEFAULT 0;
  SELECT COUNT(Marks.Mark) INTO MR FROM Marks 
  WHERE `TypePar`=parentType AND `Parent`= parentId AND `Mark` = markType;
  RETURN MR;
END$$

CREATE FUNCTION `issetMark` (`parentId` INT, `parentType` INT, `UserId` INT, `TypeMark` INT) RETURNS INT(11) BEGIN 
  DECLARE MR INT DEFAULT 0;
  SELECT count(Marks.`Mark`) INTO MR FROM Marks WHERE 
    Marks.`Mark` = TypeMark AND
    Marks.`UserId` = UserId AND
    Marks.`Parent` = parentId AND
    Marks.`TypePar` = parentType;
  RETURN MR;
END$$

DELIMITER ;

----------------------------------------------------+
-- 						Views						|
----------------------------------------------------+
CREATE VIEW `marksinfo`  AS  select distinct `marks`.`TypePar` AS `TypePar`,`marks`.`Parent` AS `Parent`,`getMark`(`marks`.`Parent`,`marks`.`TypePar`,0) AS `Like`,`getMark`(`marks`.`Parent`,`marks`.`TypePar`,1) AS `Dislike`,`getMark`(`marks`.`Parent`,`marks`.`TypePar`,0) - `getMark`(`marks`.`Parent`,`marks`.`TypePar`,1) AS `Rating` from `marks` ;
CREATE VIEW `articlesinfo`  AS  select `articles`.`ArtId` AS `ArtId`,`articles`.`Name` AS `Name`,`articles`.`Description` AS `Description`,`articles`.`ArtType` AS `ArtType`,`articles`.`UserId` AS `UserId`,`users`.`Nickname` AS `Nickname`,`articles`.`Tags` AS `Tags`,`articles`.`DatePubl` AS `DatePubl`,if(`marksinfo`.`Rating` is null,0,`marksinfo`.`Rating`) AS `Rating`,`ChildComments`(`articles`.`ArtId`,1) AS `Comments` from ((`articles` join `users` on(`users`.`UserId` = `articles`.`UserId`)) left join `marksinfo` on(`marksinfo`.`Parent` = `articles`.`ArtId` and `marksinfo`.`TypePar` = 1)) ;
CREATE VIEW `discussionsinfo`  AS  select `discussion`.`DiscId` AS `DiscId`,`discussion`.`UserId` AS `UserId`,`discussion`.`GroupId` AS `GroupId`,`discussion`.`Title` AS `Title`,`discussion`.`Tags` AS `Tags`,`discussion`.`DatePubl` AS `DatePubl`,`usgroup`.`Title` AS `Group`,`users`.`Nickname` AS `Nickname`,if(`marksinfo`.`Rating` is null,0,`marksinfo`.`Rating`) AS `Rating` from (((`discussion` join `usgroup` on(`usgroup`.`GroupId` = `discussion`.`GroupId`)) join `users` on(`users`.`UserId` = `discussion`.`UserId`)) left join `marksinfo` on(`marksinfo`.`Parent` = `discussion`.`DiscId` and `marksinfo`.`TypePar` = 2)) ;
CREATE VIEW `groupsinfo`  AS  select `usgroup`.`GroupId` AS `GroupId`,`usgroup`.`Theme` AS `Theme`,`usgroup`.`Title` AS `Title`,`usgroup`.`Description` AS `Description`,`users`.`Nickname` AS `Owner`,`usgroup`.`OwnerId` AS `OwnerId`,count(`participation`.`UserId`) AS `Users`,count(`discussion`.`DiscId`) AS `Discussions` from (((`usgroup` join `users` on(`users`.`UserId` = `usgroup`.`OwnerId`)) left join `participation` on(`participation`.`GroupId` = `usgroup`.`GroupId`)) left join `discussion` on(`discussion`.`GroupId` = `usgroup`.`GroupId`)) group by `usgroup`.`GroupId` ;
CREATE VIEW `partinfo`  AS  select `participation`.`UserId` AS `UserId`,`participation`.`GroupId` AS `GroupId`,`participation`.`Post` AS `Post`,`users`.`Nickname` AS `Nickname`,`users`.`Firstname` AS `Firstname`,`users`.`Surname` AS `Surname` from (`participation` join `users` on(`users`.`UserId` = `participation`.`UserId`)) where 1 ;
CREATE VIEW `search`  AS  select `sr`.`Parent` AS `Parent`,`sr`.`TypePar` AS `TypePar`,`sr`.`title` AS `Title`,`sr`.`Description` AS `Description`,`users`.`Nickname` AS `Nickname`,`sr`.`UserId` AS `UserId` from ((select 1 AS `TypePar`,`articles`.`ArtId` AS `Parent`,`articles`.`UserId` AS `UserId`,`articles`.`Description` AS `Description`,`articles`.`Name` AS `title` from `articles` union select 2 AS `TypePar`,`usgroup`.`GroupId` AS `Parent`,`usgroup`.`OwnerId` AS `UserId`,`usgroup`.`Title` AS `Title`,`usgroup`.`Description` AS `Description` from `usgroup` union select 3 AS `TypePar`,`discussion`.`DiscId` AS `Parent`,`discussion`.`UserId` AS `UserId`,`discussion`.`Title` AS `Title`,'' AS `Description` from `discussion`) `sr` join `users` on(`users`.`UserId` = `sr`.`UserId`)) ;
CREATE VIEW `themes`  AS  select distinct `usgroup`.`Theme` AS `Theme`,count(`usgroup`.`Theme`) AS `Pop` from `usgroup` group by `usgroup`.`Theme` order by count(`usgroup`.`Theme`) desc ;
CREATE VIEW `viewcomments`  AS  select `users`.`UserId` AS `UserId`,`users`.`Nickname` AS `Nickname`,`comments`.`ComId` AS `CommentId`,`comments`.`Textcom` AS `Text`,`comments`.`Parent` AS `Parent`,`comments`.`TypePar` AS `TypePar`,`comments`.`DatePubl` AS `DatePubl`,`ChildComments`(`comments`.`ComId`,0) AS `Nchild` from (`comments` join `users` on(`users`.`UserId` = `comments`.`UserId`)) ;

----------------------------------------------------+
-- 					Triggers						|
----------------------------------------------------+
--For marks
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
END$$

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
END$$

-- For `participation`
CREATE TRIGGER `addParticipation` BEFORE INSERT ON `participation` FOR EACH ROW BEGIN
    DECLARE Owner INT DEFAULT 0;
    SELECT COUNT(GroupId) INTO Owner FROM Usgroup
    WHERE OwnerId = NEW.UserId AND GroupId = NEW.GroupId;
    IF Owner!=0 THEN KILL QUERY CONNECTION_ID();
    END IF;
END$$

-- For `comments`
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
END$$
DELIMITER ;