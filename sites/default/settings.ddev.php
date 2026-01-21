<?php

/**
 * @file
 * This is a Drupal 11 settings.ddev.php file modified for Drupal 7 migration.
 */

$host = "db";
$port = 3306;
$driver = "mysql";

// Current Drupal 11 database
$databases["default"]["default"] = [
  "database" => "db",
  "username" => "db",
  "password" => "db",
  "host" => "db",
  "port" => 3306,
  "driver" => "mysql",
  "namespace" => "Drupal\mysql\Driver\Database\mysql",
  "prefix" => "",
];

// Database connection for Drupal 7 migration source
// UPDATED: Added the "drupal5_" prefix found in your database
$databases["migrate"]["default"] = [
  "database" => "db",
  "username" => "db",
  "password" => "db",
  "host" => "ddev-alhadiya-db", 
  "port" => 3306,
  "driver" => "mysql",
  "namespace" => "Drupal\mysql\Driver\Database\mysql",
  "prefix" => "drupal5_", 
];

$settings["migrate_source_connection"] = "migrate";
$settings["migrate_source_version"] = "7";

// Point to the Drupal 7 site files
$settings["migrate_file_public_path"] = "https://alhadiya.ddev.site/sites/default/files";
$settings["migrate_file_private_path"] = "";

$settings["hash_salt"] = "80a289145bffa7ea48f19a6162238621eec1eb2eeea90267689f9f3c1b8b17a9";
$settings["skip_permissions_hardening"] = TRUE;
$settings["trusted_host_patterns"] = [".*"];

if (empty($settings["config_sync_directory"])) {
  $settings["config_sync_directory"] = "sites/default/files/sync";
}

$config["symfony_mailer.settings"]["default_transport"] = "sendmail";
$config["system.logging"]["error_level"] = "verbose";
