<?php defined('ABSPATH') or die;

	$post_types = MCI_Core::getPostTypes();

	return [
		'mci-tab-general' => [
			'title'    => __('General', MCI_LOC_DOMAIN),
			'sections' => [
				'mci-section-status' => [
					'title'    => __('Status', MCI_LOC_DOMAIN),
					'callback' => 'cb_status_description',
					'fields'   => array_map(function($post_type)
					{
						return [
							'title'           => $post_type->label,
							'callback'        => 'cb_statuses',
							'callback_params' => [
								'post_type' => $post_type
							]
						];

					}, $post_types)
				]
			]
		],
		'mci-tab-sizes'   => [
			'title'    => __('Sizes', MCI_LOC_DOMAIN),
			'sections' => [
				'mci-section-post-types' => [
					'title'    => __('Post Types', MCI_LOC_DOMAIN),
					'callback' => 'cb_post_types_description',
					'fields'   => array_map(function($post_type)
					{
						return [
							'title'           => $post_type->label,
							'callback'        => 'cb_sizes',
							'callback_params' => [
								'post_type' => $post_type
							]
						];

					}, $post_types)
				],
			]
		]
	];