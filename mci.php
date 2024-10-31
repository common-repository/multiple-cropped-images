<?php defined('ABSPATH') or die;

	/*
	 * Plugin Name: Multiple Cropped Images
	 * Description: Allows to upload multiple images to any post and adds cropping functionality to any predefined size.
	 * Version:     1.1.7
	 * Author:      Webtimal GmbH <info@webtimal.ch>
	 * Author URI:  http://www.webtimal.ch
	 * License:		GPL-3.0
	 * License URI:	https://www.gnu.org/licenses/gpl-3.0.html
	 * Text Domain: multiple-cropped-images
	 */

	define('MCI_ENVIRONMENT', file_exists(__DIR__ . '/dev') ? 'src' : 'dist');

	if(MCI_ENVIRONMENT === 'src' && !function_exists('dump'))
	{
		function dump($var, $die = true)
		{
			echo '<pre style="margin: 50px 0 0 200px;">' . print_r($var, true) . '</pre>';

			if($die)
			{
				die;
			}
		}
	}

	add_action('init', function()
	{
		require_once __DIR__ . '/' . MCI_ENVIRONMENT . '/config/constants.php';
		require_once __DIR__ . '/' . MCI_ENVIRONMENT . '/MCI_Core.php';
		require_once __DIR__ . '/' . MCI_ENVIRONMENT . '/MCI_Config.php';

		MCI_Config::init();

		if(is_admin())
		{
			require_once __DIR__ . '/' . MCI_ENVIRONMENT . '/MCI_Options.php';

			new MCI_Options();

			add_action('current_screen', function($screen)
			{
				if($screen->id === $screen->post_type && MCI_Core::postTypeIsActive($screen->post_type))
				{
					require_once __DIR__ . '/' . MCI_ENVIRONMENT . '/MCI_Meta.php';

					new MCI_Meta();
				}
			});
		}
		else
		{
			require_once __DIR__ . '/' . MCI_ENVIRONMENT . '/MCI_Core.php';
			require_once __DIR__ . '/' . MCI_ENVIRONMENT . '/MCI_Usage.php';
		}
	}, 999);