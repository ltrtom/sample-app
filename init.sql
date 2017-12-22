CREATE TABLE `user` (
  id    INT          NOT NULL AUTO_INCREMENT,
  name  VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE task (
  id             INT          NOT NULL AUTO_INCREMENT,
  user_id        INT          NOT NULL,
  description    VARCHAR(255) NOT NULL,
  creation_dated DATETIME     NOT NULL,
  status         BOOLEAN      NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES `user` (id)
);

