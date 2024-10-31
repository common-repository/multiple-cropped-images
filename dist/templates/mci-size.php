<div class="<?php echo $classes; ?>">

	<label>
		<?php _e('Width', MCI_LOC_DOMAIN); ?>:
		<input type="number" min="1" max="2560" name="<?php echo $name; ?>[width]" value="<?php echo $width; ?>">
	</label>

	<label>
		<?php _e('Height', MCI_LOC_DOMAIN); ?>:
		<input type="number" min="1" max="2560" name="<?php echo $name; ?>[height]" value="<?php echo $height; ?>">
	</label>

	<label>
		<?php _e('Max. Thumbnail Size', MCI_LOC_DOMAIN); ?>:
		<input type="number" min="1" max="2560" name="<?php echo $name; ?>[thumb_size]"
			   value="<?php echo $thumb_size; ?>">
	</label>

	<span class="mci-control mci-remove"></span>

</div>