<?php defined('ABSPATH') or die;

	/**
	 * Class MCI_Core
	 */
	class MCI_Core
	{
		public static function getPostTypes()
		{
			$post_types = get_post_types([
				'show_ui' => true
			], 'objects');

			unset($post_types['attachment']);

			asort($post_types);

			return $post_types;
		}

		public static function getActivePostTypes()
		{
			$post_types = MCI_Config::get_by_key('mci-tab-general', 'statuses');

			return is_array($post_types) ? $post_types : [];
		}

		/**
		 * @param bool|string $post_type
		 *
		 * @return bool
		 */
		public static function postTypeIsActive($post_type = false)
		{
			$post_type = ($post_type === false) ? get_post_type(get_the_ID()) : $post_type;

			if($post_type !== false)
			{
				return array_key_exists($post_type, self::getActivePostTypes());
			}

			return false;
		}

		public static function sanitizeSize($size)
		{
			$size = abs(ceil((float)$size));

			return $size > 0 ? $size : 1;
		}

		public static function getMCIEntries($post_id, $full_data = false)
		{
			$mci_entries = get_post_meta($post_id, 'mci', true);

			if($full_data && !empty($mci_entries))
			{
				foreach($mci_entries as $i => $mci_entry)
				{
					$mci_entries[$i] = self::getFullEntryData($post_id, $mci_entry);
				}
			}

			return is_array($mci_entries) ? $mci_entries : [];
		}

		public static function getMCIEntry($post_id, $id)
		{
			$mci_entries = self::getMCIEntries($post_id);

			$key = array_search($id, array_column($mci_entries, 'id'), false);

			return $key !== false ? self::getFullEntryData($post_id, $mci_entries[$key]) : [];
		}

		public static function getFullEntryData($post_id, $data = false)
		{
			$data['original'] = self::getImageUrl($post_id, $data['id'], $data['extension'], MCI_DIR_ORIGINALS, false);
			$data['resized']  = self::getImageUrl($post_id, $data['id'], $data['extension'], MCI_DIR_RESIZED, false);
			$data['cropped']  = self::getImageUrl($post_id, $data['id'], $data['extension'], MCI_DIR_CROPPED, false);

			return $data;
		}

		/**
		 * @param string | null $post_type
		 *
		 * @return array
		 */
		public static function getSizes($post_type = null)
		{
			$post_type = ($post_type === null) ? get_current_screen()->post_type : $post_type;

			$sizes = MCI_Config::get_by_key('mci-tab-sizes', 'sizes');

			return isset($sizes[$post_type]) ? $sizes[$post_type] : [];
		}

		public static function getImagesDirectory()
		{
			if(!file_exists(MCI_DIR_IMAGES))
			{
				if(!mkdir(MCI_DIR_IMAGES) && !is_dir(MCI_DIR_IMAGES))
				{
					return false;
				}

				chmod(MCI_DIR_IMAGES, 0777);
			}

			return MCI_DIR_IMAGES;
		}

		public static function getIdDirectory($post_id)
		{
			return self::makedir(self::getImagesDirectory() . $post_id . '/');
		}

		public static function getImageDirectory($post_id, $directory)
		{
			$directory = trim($directory, '/') . '/';

			return self::makedir(self::getIdDirectory($post_id) . $directory);
		}

		public static function getImageUrl($post_id, $id, $extension, $directory, $timestamp = true)
		{
			$time      = $timestamp ? '?' . time() : '';
			$directory = trim($directory, '/') . '/';

			return MCI_URL_IMAGES . $post_id . '/' . $directory . $id . $extension . $time;
		}

		public static function makedir($dir, $chmod = 0777)
		{
			if(!file_exists($dir))
			{
				if(!mkdir($dir) && !is_dir($dir))
				{
					return false;
				}

				chmod($dir, $chmod);
			}

			return $dir;
		}

		public static function getFileExtension($post_id, $id)
		{
			$type = isset($_FILES['mci']['type'][$id]['image']) ? $_FILES['mci']['type'][$id]['image'] : false;

			if($type)
			{
				return $type === 'image/png' ? '.png' : '.jpg';
			}

			$files = glob(self::getImageDirectory($post_id, MCI_DIR_ORIGINALS) . $id . '.*');

			if(($file = reset($files)) !== false)
			{
				return '.' . pathinfo($file, PATHINFO_EXTENSION);
			}

			return '.jpg';
		}

		public static function getUploadedFile($id)
		{
			$file = isset($_FILES['mci']['tmp_name'][$id]['image']) ? $_FILES['mci']['tmp_name'][$id]['image'] : '';

			return !empty($file) ? $file : false;
		}

		public static function getBase64FromDataString($data)
		{
			$data = explode(',', $data);

			return end($data);
		}

		public static function createImageResource($base64)
		{
			$base64 = self::getBase64FromDataString($base64);

			return imagecreatefromstring(base64_decode($base64));
		}

		public static function createImageResourceFromFile($file, $extension)
		{
			return $extension === '.png' ? imagecreatefrompng($file) : imagecreatefromjpeg($file);
		}

		public static function createImageFromResource($resource, $destination, $extension = '.jpg')
		{
			if($extension === '.png')
			{
				$background = imagecolorallocate($resource, 0, 0, 0);

				imagecolortransparent($resource, $background);

				imagealphablending($resource, false);

				imagesavealpha($resource, true);

				imagepng($resource, $destination, 9);
			}
			else
			{
				imagejpeg($resource, $destination, 100);
			}

			chmod($destination, 0777);
		}

		public static function createOriginalFile($file, $post_id, $id, $extension)
		{
			$destination = self::getImageDirectory($post_id, MCI_DIR_ORIGINALS) . $id . $extension;

			move_uploaded_file($file, $destination);
			chmod($destination, 0777);

			return $destination;
		}

		public static function createResizedFile($post_id, $id, $extension, $original, $thumb_size)
		{
			$destination = self::getImageDirectory($post_id, MCI_DIR_RESIZED) . $id . $extension;
			$original    = self::createImageResourceFromFile($original, $extension);

			$width  = imagesx($original);
			$height = imagesy($original);

			$ar            = $width >= $height ? $thumb_size / $width : $thumb_size / $height;
			$resizedWidth  = floor($ar * $width);
			$resizedHeight = floor($ar * $height);

			$resized = imagecreatetruecolor($resizedWidth, $resizedHeight);

			$x = $y = 0;

			imagecopyresampled($resized, $original, $x, $y, $x, $y, $resizedWidth, $resizedHeight, $width, $height);

			self::createImageFromResource($resized, $destination, $extension);
		}

		public static function createCroppedFile($post_id, $id, $extension, $resource)
		{
			$destination = self::getImageDirectory($post_id, MCI_DIR_CROPPED) . $id . $extension;

			self::createImageFromResource($resource, $destination, $extension);
		}

		public static function createIconFile($post_id, $id, $extension, $resource, $width, $height)
		{
			$destination = self::getImageDirectory($post_id, MCI_DIR_ICONS) . $id . $extension;

			$ar         = $width >= $height ? MCI_SIZE_ICON / $height : MCI_SIZE_ICON / $width;
			$iconWidth  = floor($ar * $width);
			$iconHeight = floor($ar * $height);

			$icon = imagecreatetruecolor($iconWidth, $iconHeight);

			$x = $y = 0;

			imagecopyresampled($icon, $resource, $x, $y, $x, $y, $iconWidth, $iconHeight, $width, $height);

			self::createImageFromResource($icon, $destination, $extension);
		}

		public static function deleteImage($post_id, $id, $extension)
		{
			$directories = [
				'original' => self::getImageDirectory($post_id, MCI_DIR_ORIGINALS),
				'resized'  => self::getImageDirectory($post_id, MCI_DIR_RESIZED),
				'cropped'  => self::getImageDirectory($post_id, MCI_DIR_CROPPED),
				'icon'     => self::getImageDirectory($post_id, MCI_DIR_ICONS)
			];

			foreach($directories as $directory)
			{
				self::deleteFile($directory . $id . $extension);

				if(count(glob($directory . '*')) === 0)
				{
					self::deleteDirectory($directory);
				}
			}

			//TODO DELETE ID DIR
		}

		public static function deleteFile($file)
		{
			return file_exists($file) ? unlink($file) : false;
		}

		public static function deleteDirectory($directory)
		{
			return is_dir($directory) ? rmdir($directory) : false;
		}
	}