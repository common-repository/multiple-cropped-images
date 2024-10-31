<?php defined('ABSPATH') or die;

	/**
	 * Class MCI_Config
	 */
	class MCI_Config
	{
		/**
		 * @var array $settings
		 */
		public static $settings;

		private static $config;

		/**
		 * Initialize static member variables
		 */
		public static function init()
		{
			self::$settings = require __DIR__ . '/config/settings.php';

			self::$config = [];

			foreach(self::$settings as $tab_id => $tab)
			{
				$options = get_option($tab_id);

				self::$config[$tab_id] = $options ?: [];
			}
		}

		/**
		 * Retrieves the saved value of a given option key
		 *
		 * @param string $tab_key       Identifier of the tab
		 *
		 * @param string $field_key     Identifier of the option to look up
		 *
		 * @param bool   $empty_allowed Whether to look up for the default value if the value is empty
		 *
		 * @return mixed The saved value | The default value
		 */
		public static function get_by_key($tab_key, $field_key, $empty_allowed = true)
		{
			$value = isset(self::$config[$tab_key][$field_key]) ? self::$config[$tab_key][$field_key] : '';

			if(!$empty_allowed && empty($value))
			{
				return self::get_default_by_key($tab_key, $field_key);
			}

			return $value;
		}

		/**
		 * Retrieves the default value of a given option key
		 *
		 * @param string $tab_key   Identifier of the tab
		 *
		 * @param string $field_key Identifier of the option to look up
		 *
		 * @return string The default value
		 */
		public static function get_default_by_key($tab_key, $field_key)
		{
			foreach(self::$settings[$tab_key]['sections'] as $section_key => $section)
			{
				if(isset($section['fields'][$field_key]['default']))
				{
					return $section['fields'][$field_key]['default'];
				}
			}

			return '';
		}
	}