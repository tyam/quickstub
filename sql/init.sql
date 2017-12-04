
/* CREATE DATABASE quickstub; */

CREATE TABLE sequence (
    name VARCHAR(50) NOT NULL,
    current_value BIGINT NOT NULL,
    increment INT NOT NULL DEFAULT 1,
    PRIMARY KEY (name)
);

DELIMITER $
CREATE FUNCTION currval (seq_name VARCHAR(50))
    RETURNS BIGINT
    LANGUAGE SQL
    DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
    DECLARE value BIGINT;
    SET value = 0;
    SELECT current_value INTO value
        FROM sequence
        WHERE name = seq_name;
    RETURN value;
END
$
DELIMITER ;

DELIMITER $
CREATE FUNCTION nextval (seq_name VARCHAR(50))
    RETURNS BIGINT
    LANGUAGE SQL
    DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
    UPDATE sequence
    SET current_value = current_value + increment
    WHERE name = seq_name;
    RETURN currval(seq_name);
END
$
DELIMITER ;

DELIMITER $
CREATE FUNCTION setval (seq_name VARCHAR(50), value BIGINT)
    RETURNS BIGINT
    LANGUAGE SQL
    DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
    UPDATE sequence
    SET current_value = value
    WHERE name = seq_name;
    RETURN currval(seq_name);
END
$
DELIMITER ;

CREATE TABLE user (
    userId BIGINT NOT NULL PRIMARY KEY, 
    displayName VARCHAR(50) NOT NULL, 
    created DATETIME NOT NULL
);
INSERT INTO sequence VALUES ('userId', 121, 1);
INSERT INTO user VALUES (nextval('userId'), '新規ユーザー', NOW());

CREATE TABLE stub (
    stubId BIGINT NOT NULL PRIMARY KEY, 
    ownerId BIGINT NOT NULL, 
    methods INTEGER NOT NULL, 
    `path` VARCHAR(100) NOT NULL, 
    statusCode INTEGER NOT NULL, 
    header TEXT NOT NULL, 
    body TEXT NOT NULL
);
INSERT INTO sequence VALUES ('stubId', 532, 1);

CREATE TABLE stubOrdering (
    ownerId BIGINT NOT NULL, 
    ord INTEGER NOT NULL, 
    stubId BIGINT NOT NULL, 
    PRIMARY KEY (ownerId, ord)
);

CREATE TABLE access (
    accessId BIGINT NOT NULL PRIMARY KEY, 
    stubId BIGINT NOT NULL, 
    ownerId BIGINT NOT NULL, 
    request TEXT NOT NULL, 
    response TEXT NOT NULL, 
    accessed DATETIME NOT NULL, 
    INDEX byOwner (ownerId, accessed DESC), 
    INDEX byStub (stubId, accessed DESC)
);
INSERT INTO sequence VALUES ('accessId', 255, 1);