CREATE TABLE Users(
    UserId INT AUTO_INCREMENT UNIQUE,
    Firstname VARCHAR(30) NOT NULL,
    Surname VARCHAR(30) NOT NULL,
    Nickname VARCHAR(30) UNIQUE NOT NULL,
    Email VARCHAR(30) NOT NULL,
    Birth DATE,
    City VARCHAR(30),
    Gender tinyint,
    About VARCHAR(255),
    Priority INT NOT NULL DEFAULT(0),
    UserPswrd varchar(35) NOT NULL
    PRIMARY KEY(UserId)
);

CREATE TABLE UsGroup (
  GroupId int(10) AUTO_INCREMENT, 
  OwnerId   int(10) NOT NULL, 
  Title varchar(30) NOT NULL, 
  Theme int(10) NOT NULL, 
  Tags     varchar(30), 
  PRIMARY KEY (GroupId, OwnerId),
  CONSTRAINT Group_Owner_FK FOREIGN KEY (OwnerId) REFERENCES Users (UserId)
);

CREATE TABLE Article (
    ArtId  int AUTO_INCREMENT, 
    UserId    int NOT NULL, 
    Description  varchar(100) NOT NULL, 
    Mini varchar(30), 
    ArtType  tinyint DEFAULT 0 NOT NULL, 
    Name  varchar(40) NOT NULL, 
    Tags      varchar(30), 
    DatePubl  timestamp NOT NULL, 
    PRIMARY KEY (ArtId,UserId),
    CONSTRAINT Article_User_FK FOREIGN KEY (UserId) REFERENCES Users (UserId)
);

CREATE TABLE Discussion (
    IdDisc   int AUTO_INCREMENT, 
    GroupId int NOT NULL, 
    UserId  int NOT NULL, 
    Theme     varchar(30) NOT NULL, 
    Tags     varchar(30), 
    PRIMARY KEY (IdDisc,GroupId, UserId),
	  CONSTRAINT Discussion_Group_FK FOREIGN KEY (GroupId) REFERENCES UsGroup (GroupId),
	  CONSTRAINT Discussion_User_FK FOREIGN KEY (UserId) REFERENCES Users (UserId)
);

CREATE TABLE Comments (
  ComId   int NOT NULL UNIQUE, 
  UserId int NOT NULL, 
  Parent     int NOT NULL, 
  Text   int, 
  TypePar     tinyint NOT NULL, 
  DatePubl    timestamp NOT NULL, 
  PRIMARY KEY (ComId, UserId, Parent),
  CONSTRAINT Comments_User FOREIGN KEY (UserId) REFERENCES Users (UserId)
);

CREATE TABLE Marks (
  TypePar     tinyint NOT NULL, 
  UserId int NOT NULL, 
  Parent   int NOT NULL, 
  Mark int NOT NULL, 
  PRIMARY KEY (TypePar, Parent, UserId),
  CONSTRAINT Marks_User_FK FOREIGN KEY (UserId) REFERENCES Users(UserId)
);

CREATE INDEX User_login ON users(Nickname);
--------------------------------------------------------------------------------

DELIMITER //
CREATE PROCEDURE Users_ChangeNick(Oldnick VARCHAR(30),Newnick VARCHAR(30),UserPass varchar(35))
BEGIN
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
END //
DELIMITER ;
DELIMITER //

CREATE PROCEDURE Users_ChangePassword(Id INT, UserPass varchar(35),NewPass VARCHAR(35))
BEGIN
    DECLARE Usr INT DEFAULT 0;
    SELECT count(Users.UserId) INTO Usr FROM Users WHERE Users.UserId = Id AND Users.UserPswrd = UserPass;
    IF Usr = 1 THEN
    BEGIN
        UPDATE Users SET UserPswrd = NewPass WHERE UserId = Id;
        SELECT 0;
    END;
    ELSE SELECT 1;
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE Login(Nickname varchar(30), UserPass varchar(35))
BEGIN
    SELECT Users.UserId, Users.Nickname, Users.Priority 
    FROM Users
    WHERE Users.Nickname = Nickname AND Users.UserPswrd = UserPass;
END //
DELIMITER ;
-------------------------------------------------------
CREATE VIEW UserArt
AS
SELECT 
    articles.`ArtId`,
    articles.`Description`,
    articles.`Mini`,
    articles.`ArtType`,
    articles.`Name`,
    articles.`Tags`,
    articles.`DatePubl`,
    users.`UserId`,
    users.`Firstname`,
    users.`Surname`,
    users.`Nickname`,
    users.`Email`
FROM articles
INNER JOIN users
ON articles.UserId = Users.UserId


DELIMITER $$
CREATE FUNCTION ChildComments (parentId INT, parentType INT DEFAULT 0)
RETURNS INT
BEGIN 
  DECLARE CN INT DEFAULT 0;
  SELECT COUNT(comments.ComId) INTO CN FROM comments 
  WHERE `TypePar`=parentType and `Parent`= parentId;
  RETURN CN;
END$$
DELIMITER ;

DELIMITER $$
CREATE FUNCTION countComments (parentId INT, parentType INT DEFAULT 0)
RETURNS INT
BEGIN 
  DECLARE CN INT DEFAULT 0;
  IF ChildComments(parentId,parentType)>0 THEN
  SET CN = countComments(parentId,parentType)+1;
  END IF;
  RETURN CN;
END$$
DELIMITER ;

CREATE VIEW `ViewComments` AS
SELECT 
    Users.UserId, 
    Users.Nickname,
    comments.ComId AS `CommentId`,
    comments.Textcom AS `Text`,
    comments.Parent,
    comments.TypePar,
    comments.DatePubl,
	ChildComments(comments.ComId,0) AS `Nchild`
FROM `comments`
JOIN users
ON users.UserId = comments.UserId;

DELIMITER ||
CREATE TRIGGER addMark BEFORE INSERT ON Marks
FOR EACH ROW
BEGIN
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
END||
DELIMITER ;

DELIMITER ||
CREATE TRIGGER addComment BEFORE INSERT ON Comments
FOR EACH ROW
BEGIN
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
END||
DELIMITER ;


DELIMITER $$
CREATE FUNCTION getMark (parentId INT, parentType INT, markType INT)
RETURNS INT
BEGIN 
  DECLARE MR INT DEFAULT 0;
  SELECT COUNT(Marks.Mark) INTO MR FROM Marks 
  WHERE `TypePar`=parentType AND `Parent`= parentId AND `Mark` = markType;
  RETURN MR;
END$$
DELIMITER ;

CREATE VIEW MarksInfo AS
SELECT DISTINCT 
Marks.TypePar,Marks.Parent,getMark(Marks.Parent,Marks.TypePar,0) AS 'Like',
getMark(Marks.Parent,Marks.TypePar,1) AS 'Dislike',
getMark(Marks.Parent,Marks.TypePar,0) - getMark(Marks.Parent,Marks.TypePar,1) AS 'Rating' 
FROM Marks

DELIMITER ||
CREATE TRIGGER addMarkRating BEFORE INSERT ON Marks
FOR EACH ROW
BEGIN
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
END||
DELIMITER ;

DELIMITER ||
CREATE TRIGGER addParticipation BEFORE INSERT ON Participation
FOR EACH ROW
BEGIN
    DECLARE Owner INT DEFAULT 0;
    SELECT COUNT(GroupId) INTO Owner FROM Usgroup
    WHERE OwnerId = NEW.UserId;
    IF Owner!=0 THEN KILL QUERY CONNECTION_ID();
    END IF;
END||
DELIMITER ;

CREATE VIEW ArticlesInfo AS
SELECT
    Articles.ArtId,
    Articles.Name,
    Articles.Description,
    Articles.ArtType,
    Articles.UserId,
    Users.Nickname,
    Articles.Tags,
    Articles.DatePubl,
    IF(ISNULL(Marksinfo.Rating),0,Marksinfo.Rating) AS 'Rating',
    ChildComments(Articles.ArtId,1) AS 'Comments'
    
FROM Articles
INNER JOIN Users
ON Users.UserId = Articles.UserId
LEFT JOIN Marksinfo
ON Marksinfo.Parent = Articles.ArtId AND Marksinfo.TypePar=1


CREATE VIEW DiscussionsInfo AS
SELECT
    Discussion.DiscId,
    Discussion.UserId,
    Discussion.GroupId,
    Discussion.Title,
    Discussion.Tags,
    Discussion.DatePubl,
    usgroup.Title AS 'Group',
    Users.Nickname,
    IF(ISNULL(Marksinfo.Rating),0,Marksinfo.Rating) AS 'Rating'
FROM `discussion`
INNER JOIN usgroup
ON usgroup.GroupId = discussion.GroupId
INNER JOIN users
ON users.UserId = discussion.UserId
LEFT JOIN marksinfo
ON marksinfo.Parent = discussion.DiscId AND marksinfo.TypePar=2
    
FROM Articles
INNER JOIN Users
ON Users.UserId = Articles.UserId
LEFT JOIN Marksinfo
ON Marksinfo.Parent = Articles.ArtId AND Marksinfo.TypePar=1
/*INSERT INTO `marks`(`TypePar`, `UserId`, `Parent`, `Mark`) VALUES (1,1,61,1)*/

DELIMITER $$
CREATE FUNCTION issetMark (parentId INT, parentType INT, UserId INT, TypeMark INT)
RETURNS INT
BEGIN 
  DECLARE MR INT DEFAULT 0;
  SELECT count(mark) FROM `marks` WHERE 
    Marks.`Mark` = TypeMark AND
    Marks.`UserId` = UserId AND
    Marks.`Parent` = parentId AND
    Marks.`TypePar` = parentType
  RETURN MR;
END$$
DELIMITER ;


DELIMITER //
CREATE PROCEDURE getNotHiden(UserId INT, TypePar INT)
BEGIN
     CASE TypePar
	WHEN 0 THEN 
	    SELECT * FROM Viewcomments WHERE isMark(Viewcomments.CimmentId,0,UserId,2)=0;
	WHEN 1 THEN 
	    SELECT * FROM Articlesinfo WHERE isMark(Articlesinfo.IrtId,1,UserId,2)=0;
	WHEN 2 THEN 
	    SELECT COUNT(Discussion.DiscId) INTO n FROM Discussion WHERE Discussion.DiscId = NEW.Parent;
	ELSE KILL QUERY CONNECTION_ID();
    END CASE;
END //
DELIMITER ;