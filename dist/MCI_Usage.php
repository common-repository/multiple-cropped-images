<?php defined('ABSPATH') or die;

	require_once __DIR__ . '/MCI_Core.php';

	if(!function_exists('mci_get_entries'))
	{
		/**
		 * @param int|bool $post_id
		 *
		 * @return array
		 */
		function mci_get_entries($post_id = false)
		{
			$post_id = $post_id === false ? get_the_ID() : $post_id;

			if($post_id !== false)
			{
				return MCI_Core::getMCIEntries($post_id, true);
			}

			return [];
		}
	}

	if(!function_exists('mci_get_entry'))
	{
		/**
		 * @param bool|int  $post_id
		 * @param           $id
		 *
		 * @return array|bool
		 */
		function mci_get_entry($post_id = false, $id)
		{
			$post_id = $post_id === false ? get_the_ID() : $post_id;

			$mci_entries = ($post_id !== false) ? MCI_Core::getMCIEntries($post_id, true) : [];

			if(!empty($mci_entries))
			{
				$key = array_search($id, array_column($mci_entries, 'id'), false);

				if($key !== false)
				{
					return $mci_entries[$key];
				}
			}

			return [];
		}
	}

	if(!shortcode_exists('mci'))
	{
		add_shortcode('mci', function($atts)
		{
			require_once __DIR__ . '/MCI_Core.php';

			$post_id = get_the_ID();

			$atts = shortcode_atts([
				'id'      => 0,
				'post-id' => $post_id,
				'size'    => 'cropped'
			], $atts);

			if($atts['id'] > 0)
			{
				$mci_entry = MCI_Core::getMCIEntry($atts['post-id'], $atts['id']);

				if(empty($mci_entry))
				{
					return '';
				}


				$src       = isset($mci_entry[$atts['size']]) ? $mci_entry[$atts['size']] : $mci_entry['cropped'];
				$seo_alt   = !empty($mci_entry['seo_alt']) ? ' alt="' . $mci_entry['seo_alt'] . '"' : '';
				$seo_title = !empty($mci_entry['seo_title']) ? ' title="' . $mci_entry['seo_title'] . '"' : '';

				return '<img src="' . $src . '"' . $seo_alt . $seo_title . '>';
			}

			return '';
		});
	}
