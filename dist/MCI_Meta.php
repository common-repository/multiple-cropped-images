<?php defined('ABSPATH') or die;

	class MCI_Meta
	{
		public function __construct()
		{
			add_action('admin_enqueue_scripts', [
				$this,
				'enqueue_scripts'
			]);

			add_action('add_meta_boxes', [
				$this,
				'meta_box'
			]);

			add_action('post_edit_form_tag', [
				$this,
				'form_tag'
			]);

			add_action('save_post', [
				$this,
				'process_data'
			]);
		}

		public function enqueue_scripts()
		{
			wp_enqueue_style('cropperjs', MCI_URL_CSS . 'cropper.min.css');
			wp_enqueue_style('fancybox-3', MCI_URL_CSS . 'jquery.fancybox.min.css');
			wp_enqueue_style('mci-meta', MCI_URL_CSS . 'mci-meta.min.css');

			wp_register_script('cropperjs', MCI_URL_JS . 'cropper.min.js', ['jquery']);
			wp_register_script('fancybox-3', MCI_URL_JS . 'jquery.fancybox.min.js', ['jquery']);
			wp_register_script('jquery-ui', MCI_URL_JS . 'jquery-ui.min.js', ['jquery']);

			wp_enqueue_script('mci-meta', MCI_URL_JS . 'mci-meta.min.js', [
				'jquery-ui',
				'fancybox-3',
				'cropperjs'
			]);
		}

		public function form_tag()
		{
			echo 'enctype="multipart/form-data" encoding="multipart/form-data"';
		}

		public function meta_box()
		{
			add_meta_box('mci-meta', __('MCI', MCI_LOC_DOMAIN), [
				$this,
				'cb_meta_box'
			]);
		}

		public function cb_meta_box()
		{
			global $post;

			wp_nonce_field('mci-meta', 'mci-nonce');

			echo '<div class="sortable">';

			$this->show_mci_entry('#ID#', [], true);

			foreach(MCI_Core::getMCIEntries($post->ID) as $mci_entry)
			{
				$this->show_mci_entry($mci_entry['id'], $mci_entry);
			}

			echo '</div>';

			if(!empty(MCI_Core::getSizes()))
			{
				echo '<span class="button mci-add">' . __('Add', MCI_LOC_DOMAIN) . '</span>';
			}
			else
			{
				_e('Please configure at least one size set in order to add images', MCI_LOC_DOMAIN);
				echo '<br><br><a href="' . get_admin_url() . '?page=mci" target="_blank">> ' . __('MCI Options', MCI_LOC_DOMAIN) . '</a>';
			}
		}

		public function show_mci_entry($id, $data, $dummy = false)
		{
			global $post;

			$classes   = $dummy ? 'mci dummy' : 'mci';
			$cropped   = $dummy ? false : MCI_Core::getImageUrl($post->ID, $data['id'], $data['extension'], MCI_DIR_CROPPED);
			$original  = $dummy ? false : MCI_Core::getImageUrl($post->ID, $data['id'], $data['extension'], MCI_DIR_ORIGINALS, false);
			$icon      = $dummy ? MCI_URL_IMG . 'placeholder.jpg' : MCI_Core::getImageUrl($post->ID, $id, $data['extension'], MCI_DIR_ICONS);
			$width     = isset($data['width']) ? $data['width'] : '';
			$height    = isset($data['height']) ? $data['height'] : '';
			$title     = $dummy ? '' : $data['title'];
			$text      = $dummy ? '' : $data['text'];
			$seo_title = $dummy ? '' : $data['seo_title'];
			$seo_alt   = $dummy ? '' : $data['seo_alt'];

			require __DIR__ . '/templates/mci-meta.php';
		}

		public function process_data()
		{
			if(!$this->verifyNonce() || !isset($_POST['mci']))
			{
				return;
			}

			global $post;

			$mci_entries = MCI_Core::getMCIEntries($post->ID);
			$post_ids    = array_column($_POST['mci'], 'id');

			foreach($mci_entries as $mci_entry)
			{
				if(!in_array($mci_entry['id'], $post_ids, false))
				{
					MCI_Core::deleteImage($post->ID, $mci_entry['id'], $mci_entry['extension']);
				}
			}

			$post_meta = [];

			foreach($_POST['mci'] as $image)
			{
				$id         = abs((int)$image['id']);
				$width      = MCI_Core::sanitizeSize($image['width']);
				$height     = MCI_Core::sanitizeSize($image['height']);
				$thumb_size = MCI_Core::sanitizeSize($image['thumb_size']);

				if(($original = MCI_Core::getUploadedFile($id)) !== false)
				{
					$extension     = MCI_Core::getFileExtension($post->ID, $image['id']);
					$original_file = MCI_Core::createOriginalFile($original, $post->ID, $id, $extension);

					MCI_Core::createResizedFile($post->ID, $id, $extension, $original_file, $thumb_size);
				}
				else
				{
					$key = array_search($id, array_column($mci_entries, 'id'), false);

					if($key === false)
					{
						continue;
					}

					$extension = $mci_entries[$key]['extension'];
				}

				if(!empty($image['base64']))
				{
					$resource = MCI_Core::createImageResource($image['base64']);

					MCI_Core::createCroppedFile($post->ID, $id, $extension, $resource);
					MCI_Core::createIconFile($post->ID, $id, $extension, $resource, $width, $height);
				}

				$post_meta[] = [
					'id'        => $id,
					'width'     => $width,
					'height'    => $height,
					'extension' => $extension,
					'title'     => sanitize_text_field($image['title']),
					'text'      => sanitize_text_field($image['text']),
					'seo_title' => sanitize_text_field($image['seo_title']),
					'seo_alt'   => sanitize_text_field($image['seo_alt'])
				];
			}

			update_post_meta($post->ID, 'mci', $post_meta);
		}

		public function verifyNonce()
		{
			if(isset($_POST['mci-nonce']))
			{
				return wp_verify_nonce($_POST['mci-nonce'], 'mci-meta');
			}

			return false;
		}
	}