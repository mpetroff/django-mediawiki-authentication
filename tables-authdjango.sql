-- Copyright (C) 2011 Jack Grigg
-- Table structure for table 'Django User'
-- Replace <PREFIX> with the proper prefix

CREATE TABLE IF NOT EXISTS <PREFIX>_authdjango (
    'mw_user_id' integer unsigned NOT NULL PRIMARY KEY,
    'd_user_id' integer unsigned,
    FOREIGN KEY ('mw_user_id') REFERENCES '<PREFIX>_user' ('user_id')
    ) ENGINE=InnoDB DEFAULT CHARSET=binary;
