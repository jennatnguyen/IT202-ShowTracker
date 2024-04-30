CREATE TABLE IF NOT EXISTS  `UserShows`
(
    `id`         int auto_increment not null,
    `user_id`    int,
    `show_id`  int,
    
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`show_id`) REFERENCES Shows(`id`)
  --  UNIQUE KEY (`user_id`, `show_id`)
)