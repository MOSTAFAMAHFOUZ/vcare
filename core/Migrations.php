<?php
$ini_data = parse_ini_file('../app.ini');
$database_name = $ini_data['DATABASE_NAME'];
$database_host = $ini_data['DATABASE_HOST'];
$database_user = $ini_data['DATABASE_USER'];
$database_password = $ini_data['DATABASE_PASSWORD'];
$connection = mysqli_connect($database_host, $database_user, $database_password);
if (mysqli_connect_errno()) {
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    exit ();
}

mysqli_query($connection, "DROP DATABASE IF EXISTS `$database_name` ");
mysqli_query($connection, "CREATE DATABASE IF NOT EXISTS `$database_name` ");

mysqli_close($connection);

$connection = mysqli_connect($database_host, $database_user, $database_password, $database_name);

if (mysqli_connect_errno()) {
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    exit ();
}

// governorate table
mysqli_query($connection, 'CREATE TABLE `governorates`(
    `id`  INT UNSIGNED PRIMARY KEY  AUTO_INCREMENT ,
    `name` VARCHAR(200) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`  TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP()
)');

// city table
mysqli_query($connection, 'CREATE TABLE `cities`(
    `id`  INT UNSIGNED PRIMARY KEY  AUTO_INCREMENT ,
    `name` VARCHAR(200) NOT NULL,
    `governorate_id` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`  TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP(),
    FOREIGN KEY (governorate_id) REFERENCES `governorates`(id) ON DELETE RESTRICT
)');

// major table
mysqli_query($connection, 'CREATE TABLE `majors`(
    `id`  INT UNSIGNED PRIMARY KEY  AUTO_INCREMENT ,
    `name` VARCHAR(200) NOT NULL,
    `image` VARCHAR(200)  NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`  TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP()
)');

// Doctor Table
mysqli_query($connection, 'CREATE TABLE `doctors`(
    `id`  INT UNSIGNED PRIMARY KEY  AUTO_INCREMENT ,
    `name` VARCHAR(200) NOT NULL,
    `email` VARCHAR(200) UNIQUE NOT NULL,
    `password` VARCHAR(200)  NOT NULL,
    `city_id` INT UNSIGNED,
    `major_id` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`  TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP(),
    FOREIGN KEY (city_id) REFERENCES `cities`(id) ON DELETE RESTRICT,
    FOREIGN KEY (major_id) REFERENCES `majors`(id) ON DELETE RESTRICT
)');

// User Table
mysqli_query($connection, "CREATE TABLE `users`(
    `id`  INT UNSIGNED PRIMARY KEY  AUTO_INCREMENT ,
    `name` VARCHAR(200) NOT NULL,
    `email` VARCHAR(200) UNIQUE NOT NULL,
    `password` VARCHAR(200)  NOT NULL,
    `type` ENUM('patient','admin') NOT NULL DEFAULT 'patient',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`  TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP()
)");

// Reservation Table
mysqli_query($connection, "CREATE TABLE `reservations`(
    `id`  INT UNSIGNED PRIMARY KEY  AUTO_INCREMENT ,
    `reservation_time` TIMESTAMP  NOT NULL,
    `user_id` INT UNSIGNED,
    `doctor_id` INT UNSIGNED,
    `status` ENUM('pending','done','canceled') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`  TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP(),
    FOREIGN KEY (user_id) REFERENCES `users`(id) ON DELETE RESTRICT,
    FOREIGN KEY (doctor_id) REFERENCES `doctors`(id) ON DELETE RESTRICT
)");

// Reservation Table
mysqli_query($connection, 'CREATE TABLE `rates`(
    `id`  INT UNSIGNED PRIMARY KEY  AUTO_INCREMENT ,
    `reservation_id` INT UNSIGNED,
    `doctor_id` INT UNSIGNED,
    `user_id` INT UNSIGNED,
    `rate` TINYINT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`  TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP(),
    FOREIGN KEY (user_id) REFERENCES `users`(id) ON DELETE RESTRICT,
    FOREIGN KEY (doctor_id) REFERENCES `doctors`(id) ON DELETE RESTRICT,
    FOREIGN KEY (reservation_id) REFERENCES `reservations`(id) ON DELETE RESTRICT
)');

echo "database $database_name created successfully ";
