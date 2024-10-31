<?php defined('ABSPATH') or die;

	/* ---------- SHORTCODES ---------- */
	define('MCI_SC_SINGLE', 'mci-single');


	/* ---------- DIRECTORIES ---------- */
	define('MCI_DIR_PLUGIN', plugin_dir_path(__DIR__));
	define('MCI_DIR_IMAGES', wp_upload_dir()['basedir'] . '/mci/');
	define('MCI_DIR_ORIGINALS', 'originals/');
	define('MCI_DIR_RESIZED', 'resized/');
	define('MCI_DIR_CROPPED', 'cropped/');
	define('MCI_DIR_ICONS', 'icons/');


	/* ---------- URL's ---------- */
	define('MCI_URL_PLUGIN', plugin_dir_url(__DIR__));
	define('MCI_URL_ASSETS', MCI_URL_PLUGIN . 'assets/');
	define('MCI_URL_IMG', MCI_URL_ASSETS . 'img/');
	define('MCI_URL_CSS', MCI_URL_ASSETS . 'css/');
	define('MCI_URL_JS', MCI_URL_ASSETS . 'js/');
	define('MCI_URL_IMAGES', wp_upload_dir()['baseurl'] . '/mci/');


	/* ---------- SIZES ----------*/
	define('MCI_SIZE_ICON', 98);


	/* ---------- LOCALIZATION ----------*/
	define('MCI_LOC_DOMAIN', 'multiple-cropped-images');