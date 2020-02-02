/* Создание бд */
DROP DATABASE IF EXISTS tforce;
CREATE DATABASE tforce
  COLLATE utf8_general_ci;
USE tforce;

# ======= Создание таблицы notices_types

CREATE TABLE `tforce`.`notices_types` (
  `id`   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(256)     NOT NULL,

  PRIMARY KEY (`id`),

  UNIQUE `UNIQUE_NOTICE_TYPE` (`type`)

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы categories

CREATE TABLE `tforce`.`categories` (
  `id`        TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(256)     NOT NULL,
  `icon_link` VARCHAR(512)     NULL,

  PRIMARY KEY (`id`),

  UNIQUE `UNIQUE_NAME_CATEGORY` (`name`)

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы cities

CREATE TABLE `tforce`.`cities` (
  `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,

  PRIMARY KEY (`id`),

  UNIQUE `UNIQUE_NAME_CITY` (`name`)

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы users

CREATE TABLE `tforce`.`users` (
  `id`            INT UNSIGNED                          NOT NULL AUTO_INCREMENT,
  `email`         VARCHAR(256)                          NOT NULL,
  `password_hash` VARCHAR(512)                          NOT NULL,
  `created_at`    TIMESTAMP                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `phio`          VARCHAR(256)                          NOT NULL,
  `city_id`       INT UNSIGNED                          NOT NULL,
  `open_profile`  BOOLEAN                               NOT NULL DEFAULT FALSE,
  `open_contacts` BOOLEAN                               NOT NULL DEFAULT FALSE,
  `avatar_link`   VARCHAR(512)                          NULL,
  `birthday`      DATE                                  NOT NULL,
  `biography`     TEXT                                  NULL,
  `phone`         VARCHAR(256)                          NULL,
  `skype`         VARCHAR(256)                          NULL,
  `telegram`      VARCHAR(256)                          NULL,
  `views_number`  INT UNSIGNED ZEROFILL                 NOT NULL DEFAULT '0',
  `rate`          DECIMAL(2, 1) UNSIGNED ZEROFILL       NULL     DEFAULT NULL,
  `fail_count`    INT UNSIGNED ZEROFILL                 NOT NULL DEFAULT '0',

  PRIMARY KEY (`id`),

  UNIQUE `UNIQUE_USER_EMAIL` (`email`),

  CONSTRAINT `users_cities`
  FOREIGN KEY (`city_id`) REFERENCES `tforce`.`cities` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы favorites

CREATE TABLE `tforce`.`favorites` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`          INT UNSIGNED NOT NULL,
  `selected_user_id` INT UNSIGNED NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE `UNIQUE_USER_SELECTED_USER` (`user_id`, `selected_user_id`),

  CONSTRAINT `favorites_users_1`
  FOREIGN KEY (`selected_user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `favorites_users_2`
  FOREIGN KEY (`user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы users_media

CREATE TABLE `tforce`.`users_media` (
  `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `link`    VARCHAR(512) NOT NULL,

  PRIMARY KEY (`id`),

  CONSTRAINT `media_users`
  FOREIGN KEY (`user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

)
  ENGINE = InnoDB;

# ======= Создание таблицы tasks

CREATE TABLE `tforce`.`tasks` (
  `id`          INT UNSIGNED                                          NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED                                          NOT NULL,
  `executor_id` INT UNSIGNED                                          NULL,
  `city_id`     INT UNSIGNED                                          NULL,
  `category_id` TINYINT UNSIGNED                                      NOT NULL,
  `title`       TEXT                                                  NOT NULL,
  `text`        TEXT                                                  NOT NULL,
  `time_start`  TIMESTAMP                                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_end`    TIMESTAMP                                             NOT NULL,
  `budget`      INT                                                   NULL,
  `status`      ENUM ('new', 'canceled', 'working', 'done', 'failed') NOT NULL DEFAULT 'new',
  `latitude`    DECIMAL(10, 7)                                        NULL,
  `longitude`   DECIMAL(10, 7)                                        NULL,

  PRIMARY KEY (`id`),

  CONSTRAINT `tasks_cities`
  FOREIGN KEY (`city_id`) REFERENCES `tforce`.`cities` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,

  CONSTRAINT `tasks_customers`
  FOREIGN KEY (`customer_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `tasks_executors`
  FOREIGN KEY (`executor_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,

  CONSTRAINT `tasks_categories`
  FOREIGN KEY (`category_id`) REFERENCES `tforce`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы tasks

CREATE TABLE `tforce`.`tasks_media` (
  `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` INT UNSIGNED NOT NULL,
  `link`    VARCHAR(512) NOT NULL,

  PRIMARY KEY (`id`),

  CONSTRAINT `media-tasks`
  FOREIGN KEY (`task_id`) REFERENCES `tforce`.`tasks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы users_categories

CREATE TABLE `tforce`.`users_categories` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED     NOT NULL,
  `category_id` TINYINT UNSIGNED NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE `UNIQUE_USER_CATEGORY` (`user_id`, `category_id`),

  CONSTRAINT `usersCategories_users`
  FOREIGN KEY (`user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `usersCategories_categories`
  FOREIGN KEY (`category_id`) REFERENCES `tforce`.`categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы users_notices_types

CREATE TABLE `tforce`.`users_notices_types` (
  `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED     NOT NULL,
  `notice_type_id` TINYINT UNSIGNED NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE `UNIQUE_USER_NOTICE_TYPE` (`user_id`, `notice_type_id`),

  CONSTRAINT `usersNoticesTypes_users`
  FOREIGN KEY (`user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `usersNoticesTypes_noticesTypes`
  FOREIGN KEY (`notice_type_id`) REFERENCES `tforce`.`notices_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы notices

CREATE TABLE `tforce`.`notices` (
  `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED     NOT NULL,
  `notice_type_id` TINYINT UNSIGNED NOT NULL,
  `details`        TEXT             NULL,
  `is_new`         BOOLEAN          NOT NULL DEFAULT TRUE,

  PRIMARY KEY (`id`),

  CONSTRAINT `notices_users`
  FOREIGN KEY (`user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `notices_noticesTypes`
  FOREIGN KEY (`notice_type_id`) REFERENCES `tforce`.`notices_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы chat_msgs

CREATE TABLE `tforce`.`chat_msgs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED    NOT NULL,
  `task_id`    INT UNSIGNED    NOT NULL,
  `is_new`     BOOLEAN         NOT NULL DEFAULT TRUE,
  `text`       TEXT            NOT NULL,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `chatMsgs_users`
  FOREIGN KEY (`user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `chatMsgs_tasks`
  FOREIGN KEY (`task_id`) REFERENCES `tforce`.`tasks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы responses

CREATE TABLE `tforce`.`responses` (
  `id`            INT UNSIGNED          NOT NULL AUTO_INCREMENT,
  `task_id`       INT UNSIGNED          NOT NULL,
  `whose_user_id` INT UNSIGNED          NOT NULL,
  `price`         INT UNSIGNED ZEROFILL NULL,
  `text`          TEXT                  NULL,
  `created_at`    TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `responses_tasks`
  FOREIGN KEY (`task_id`) REFERENCES `tforce`.`tasks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `responses_users`
  FOREIGN KEY (`whose_user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;

# ======= Создание таблицы reviews

CREATE TABLE `tforce`.`reviews` (
  `id`           INT UNSIGNED        NOT NULL AUTO_INCREMENT,
  `task_id`      INT UNSIGNED        NOT NULL,
  `whom_user_id` INT UNSIGNED        NOT NULL,
  `score`        TINYINT(1) UNSIGNED NULL,
  `text`         TEXT                NULL,
  `created_at`   TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `reviews_tasks`
  FOREIGN KEY (`task_id`) REFERENCES `tforce`.`tasks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `reviews_users`
  FOREIGN KEY (`whom_user_id`) REFERENCES `tforce`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT

)
  ENGINE = InnoDB
  CHARSET = utf8
  COLLATE utf8_general_ci;
