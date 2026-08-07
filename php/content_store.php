<?php
/**
 * content_store.php
 * Tiny file-based JSON store for the site-content manager (competitions,
 * sponsors, workshops, about/contact). Not a database table — this is the
 * simplest thing that lets the admin dashboard and the public pages share
 * the same data without you standing up new DB tables.
 *
 * If you'd rather keep everything in your existing database, swap the two
 * functions below (content_load / content_save_section) for real queries
 * against a `site_content` table (columns: section, data JSON) and leave
 * content_get.php / content_save.php untouched.
 */

define('CONTENT_FILE', __DIR__ . '/data/content.json');

define('CONTENT_DEFAULTS', [
  'competitions'     => [],
  'sponsor_tiers'    => [],
  'sponsors'         => [],
  'workshops'        => [],
  'about'            => ['heading' => '', 'body' => ''],
  'contact_details'  => [],
  'coordinators'     => [],
]);

function content_load() {
  if (!file_exists(CONTENT_FILE)) {
    return CONTENT_DEFAULTS;
  }
  $raw = file_get_contents(CONTENT_FILE);
  $data = json_decode($raw, true);
  if (!is_array($data)) {
    return CONTENT_DEFAULTS;
  }
  // Fill in any sections that don't exist yet (e.g. after adding a new one).
  return array_merge(CONTENT_DEFAULTS, $data);
}

function content_save_section($section, $sectionData) {
  if (!array_key_exists($section, CONTENT_DEFAULTS)) {
    return false;
  }
  $all = content_load();
  $all[$section] = $sectionData;

  $dir = dirname(CONTENT_FILE);
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }
  $ok = file_put_contents(CONTENT_FILE, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
  return $ok !== false;
}
