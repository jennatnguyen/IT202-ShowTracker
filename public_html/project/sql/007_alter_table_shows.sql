ALTER TABLE Shows 
ADD COLUMN rated varchar(100),
DROP COLUMN irating,
ADD COLUMN imdb_rating varchar(30),
DROP COLUMN popularity,
ADD COLUMN genres varchar(100),
COMMENT 'fixing columns for fetch by id';