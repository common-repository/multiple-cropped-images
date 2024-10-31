<?php defined('ABSPATH') or die;

	/**
	 * Class MCI_Options
	 */
	class MCI_Options
	{
		private $page_title;

		private $menu_slug;
		private $active_tab;
		private $options_set;

		/**
		 * MCI_Options constructor
		 */
		public function __construct()
		{
			$this->page_title = __('MCI Settings', MCI_LOC_DOMAIN);
			$this->menu_slug  = 'mci';
			$this->active_tab = isset($_GET['tab']) ? $_GET['tab'] : false;

			if(!$this->active_tab)
			{
				reset(MCI_Config::$settings);
				$this->active_tab = key(MCI_Config::$settings);
			}

			add_action('admin_init', [
				$this,
				'register_settings'
			]);

			add_action('admin_menu', [
				$this,
				'add_admin_menu'
			]);

			add_action('admin_enqueue_scripts', [
				$this,
				'enqueue_scripts'
			]);
		}

		/**
		 * Enqueues MCI specific scripts and styles
		 */
		public function enqueue_scripts()
		{
			wp_enqueue_style('mci-options', MCI_URL_CSS . 'mci-options.min.css');

			wp_enqueue_script('mci-options', MCI_URL_JS . 'mci-options.min.js', ['jquery']);
		}

		/**
		 * Registers settings sections and their corresponding fields
		 */
		public function register_settings()
		{
			foreach(MCI_Config::$settings as $tab_key => $tab)
			{
				register_setting($tab_key, $tab_key, [
					$this,
					'sanitize'
				]);

				foreach($tab['sections'] as $section_key => $section)
				{
					$callback = $this->getCallback($section['callback']);

					add_settings_section($section_key, $section['title'], $callback, $tab_key);

					foreach($section['fields'] as $field_key => $field)
					{
						$callback = $this->getCallback($field['callback']);

						$callback_params = isset($field['callback_params']) ? $field['callback_params'] : [];

						add_settings_field($field_key, $field['title'], $callback, $tab_key, $section_key, $callback_params);
					}
				}
			}

			$this->options_set = get_option($this->active_tab);
		}

		private function getCallback($callback)
		{
			return is_callable($callback) || $callback === null ? $callback : [
				$this,
				$callback
			];
		}

		/**
		 * Adds the MCI Options page to the admin menu
		 */
		public function add_admin_menu()
		{
			add_menu_page($this->page_title, __('MCI', MCI_LOC_DOMAIN), 'manage_options', $this->menu_slug, [
				$this,
				'create_options_page'
			], 'dashicons-image-crop');
		}

		/**
		 * Displays the tabbed HTML content of the options page
		 */
		public function create_options_page()
		{
			?>
			<div class="wrap">
				<h1><?php echo $this->page_title; ?></h1>
				<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
					<?php
						foreach(MCI_Config::$settings as $tid => $tab)
						{
							$class = ($tid === $this->active_tab) ? 'nav-tab nav-tab-active' : 'nav-tab';
							$href  = '?page=' . $this->menu_slug . '&tab=' . $tid;

							echo '<a href="' . $href . '" class="' . $class . '">' . $tab['title'] . '</a>';
						}
					?>
				</nav>
				<form id="mci-options" method="post" action="options.php">
					<?php
						settings_fields($this->active_tab);
						do_settings_sections($this->active_tab);
						submit_button();
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Validates and sanitizes inputs
		 *
		 * @param array $input Contains all input values
		 *
		 * @return array Sanitized input values
		 */
		public function sanitize($input)
		{
			if(isset($input['sizes']))
			{
				array_walk($input['sizes'], function(&$sizes)
				{
					array_walk($sizes, function(&$size)
					{
						$size['width']      = MCI_Core::sanitizeSize($size['width']);
						$size['height']     = MCI_Core::sanitizeSize($size['height']);
						$size['thumb_size'] = MCI_Core::sanitizeSize($size['thumb_size']);
					});
				});
			}

			return $input;
		}

		private function get_input_data($key, $empty_allowed = true)
		{
			return [
				'name'    => esc_attr($this->active_tab . '[' . $key . ']'),
				'value'   => esc_attr(MCI_Config::get_by_key($this->active_tab, $key, $empty_allowed)),
				'default' => esc_attr(MCI_Config::get_default_by_key($this->active_tab, $key))
			];
		}

		public function cb_status_description()
		{
			echo '<p class="description">' . __('Activate the plugin for specific post types.', MCI_LOC_DOMAIN) . '</p>';
		}

		public function cb_post_types_description()
		{
			echo '<p class="description">' . __('Set custom sizes for each post type.', MCI_LOC_DOMAIN) . '</p>';
			echo '<p class="description">' . __('All sizes are to be given in pixels.', MCI_LOC_DOMAIN) . '</p>';
		}

		public function cb_statuses($args)
		{
			extract($args, EXTR_OVERWRITE);

			$statuses = MCI_Config::get_by_key($this->active_tab, 'statuses');
			$status   = isset($statuses[$post_type->name]) ? $statuses[$post_type->name] : 0;

			$name    = $this->active_tab . '[statuses][' . $post_type->name . ']';
			$checked = $status ? ' checked = "checked"' : '';

			echo ' <input type = "checkbox" name = "' . $name . '"' . $checked . '>';
		}

		public function cb_sizes($args)
		{
			extract($args, EXTR_OVERWRITE);

			$sizes = MCI_Config::get_by_key($this->active_tab, 'sizes');

			$name = $this->active_tab . '[sizes][#POST_TYPE#][#ID#]';

			$this->show_size($name, '256', '256', '128', true);

			if(isset($sizes[$post_type->name]))
			{
				$name = str_replace('#POST_TYPE#', $post_type->name, $name);

				foreach($sizes[$post_type->name] as $id => $size)
				{
					$_name = str_replace('#ID#', $id, $name);

					$this->show_size($_name, $size['width'], $size['height'], $size['thumb_size']);
				}
			}

			$id = isset($sizes[$post_type->name]) ? count($sizes[$post_type->name]) : 0;

			echo '<span class="add button-secondary">' . __('Add', MCI_LOC_DOMAIN) . '</span>';
			echo '<span class="id">' . $id . '</span>';
			echo '<span class="post_type">' . $post_type->name . '</span>';
		}

		private function show_size($name, $width = '', $height = '', $thumb_size = '', $dummy = false)
		{
			$classes = $dummy ? 'size dummy' : 'size';

			require __DIR__ . '/templates/mci-size.php';
		}
	}