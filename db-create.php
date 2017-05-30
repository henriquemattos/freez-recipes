<?php
$sql_create_recipes = "CREATE TABLE IF NOT EXISTS $this->table_recipes (
  id_recipes INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  instructions TEXT NULL,
  PRIMARY KEY (id_recipes),
  UNIQUE INDEX id_recipes_UNIQUE (id_recipes ASC))
  ENGINE = $this->charset_collate;";

dbDelta($sql_create_recipes);

$sql_create_ingredients = "CREATE TABLE IF NOT EXISTS $this->table_ingredients (
  id_ingredients INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_ingredients),
  UNIQUE INDEX id_ingredients_UNIQUE (id_ingredients ASC))
  ENGINE = $this->charset_collate;";

dbDelta($sql_create_ingredients);

$sql_create_recipes_ingredients = "CREATE TABLE IF NOT EXISTS $this->table_recipes_ingredients (
  id_recipes INT UNSIGNED NOT NULL,
  id_ingredients INT UNSIGNED NOT NULL,
  amount VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_recipes, id_ingredients))
  ENGINE = $this->charset_collate;";

dbDelta($sql_create_recipes_ingredients);
