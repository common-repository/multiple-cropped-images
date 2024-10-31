<div class="<?php echo $classes; ?>">

	<div class="mci-header">

		<?php if($cropped) : ?>

			<a href="<?php echo $cropped; ?>" data-fancybox="1" class="mci-icon-fancybox">
				<div class="mci-icon" style="background-image: url('<?php echo $icon; ?>');"></div>
			</a>

		<?php else : ?>

			<div class="mci-icon" style="background-image: url('<?php echo $icon; ?>');"></div>

		<?php endif; ?>

		<div class="mci-header-info">
			<div><b class="info-title"><?php echo $title; ?></b>&nbsp;</div>
			<div class="info-id">ID: <?php echo $id; ?></div>
			<div class="info-shortcode">[mci id=<?php echo $id; ?> post-id=<?php echo get_the_ID(); ?>]<input value="[mci id=<?php echo $id; ?> post-id=<?php echo get_the_ID(); ?>]">
				<span class="notification"><?php _e('Copied to Clipboard', MCI_LOC_DOMAIN); ?></span>
			</div>
		</div>

		<span class="mci-toggle"></span>
		<label class="mci-control mci-upload-label">
			<input id="mci-upload-<?php echo $id; ?>" type="file" name="mci[<?php echo $id; ?>][image]"
				   class="mci-upload" accept="image/jpeg, image/png">
		</label>
		<span class="mci-control mci-recrop<?php echo $dummy ? ' disabled' : ''; ?>"></span>
		<span class="mci-control mci-remove"></span>
	</div>

	<div class="mci-body">

		<div class="mci-controls-container">
			<label for="mci-size-<?php echo $id; ?>" class="mci-size-label"><?php _e('Size', MCI_LOC_DOMAIN); ?></label>
			<select id="mci-size-<?php echo $id; ?>" title="" class="mci-size">

				<?php foreach(MCI_Core::getSizes() as $sid => $size) : ?>

					<option value='<?php echo json_encode($size); ?>'><?php echo $size['width'] . ' x ' . $size['height']; ?></option>

				<?php endforeach; ?>

			</select>
			<span class="button cancel"><?php _e('Cancel', MCI_LOC_DOMAIN); ?></span>
		</div>

		<div class="mci-image-container">
			<img class="mci-image" src="">
			<input type="hidden" class="mci-image-original" value="<?php echo $original; ?>">
			<input type="hidden" name="mci[<?php echo $id; ?>][id]" class="mci-image-id" value="<?php echo $id; ?>">
			<input type="hidden" name="mci[<?php echo $id; ?>][base64]" class="mci-image-base64">
			<input type="hidden" name="mci[<?php echo $id; ?>][width]" class="mci-image-width"
				   value="<?php echo $width; ?>">
			<input type="hidden" name="mci[<?php echo $id; ?>][height]" class="mci-image-height"
				   value="<?php echo $height; ?>">
			<input type="hidden" name="mci[<?php echo $id; ?>][thumb_size]" class="mci-image-thumb_size"
				   value="<?php echo $thumb_size; ?>">
		</div>

		<div class="mci-meta-container">

			<div>
				<input class="mci-title" name="mci[<?php echo $id; ?>][title]"
					   placeholder="<?php _e('Title', MCI_LOC_DOMAIN); ?>" value="<?php echo $title; ?>">
				<textarea name="mci[<?php echo $id; ?>][text]" placeholder="<?php _e('Text', MCI_LOC_DOMAIN); ?>"
						  rows="6"><?php echo $text; ?></textarea>
			</div>

			<div>
				<input name="mci[<?php echo $id; ?>][seo_title]" placeholder="<?php _e('SEO Title', MCI_LOC_DOMAIN); ?>"
					   value="<?php echo $seo_title; ?>">
				<textarea name="mci[<?php echo $id; ?>][seo_alt]" placeholder="<?php _e('SEO Alt', MCI_LOC_DOMAIN); ?>"
						  rows="6"><?php echo $seo_alt; ?></textarea>
			</div>

		</div>

	</div>

</div>