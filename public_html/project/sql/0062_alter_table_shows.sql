ALTER TABLE Shows 
ADD COLUMN description varchar(1000),
ADD COLUMN irating varchar(30),
ADD COLUMN popularity varchar(30),
ADD COLUMN poster varchar(100)
COMMENT 'adding columns for fetch by id';