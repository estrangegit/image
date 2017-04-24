CREATE TABLE image (
  id INTEGER PRIMARY KEY,
  path TEXT,
  category TEXT,
  comment TEXT,
  nbLike INTEGER DEFAULT 0
);

CREATE TABLE user (
  id INTEGER PRIMARY KEY,
  login TEXT
);

CREATE TABLE like_user_image (
  id_user INTEGER,
  id_image INTEGER,
  PRIMARY KEY (id_user, id_image)
);