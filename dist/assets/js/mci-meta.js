jQuery(($) =>
{
	'use strict';

	let $mci_meta = $('#mci-meta');
	let id = getBiggestID();
	let croppers = [];

	$mci_meta.find('.sortable').sortable({
		connectWith: $('.mci').not('.dummy')
	});

	$mci_meta.find('.mci-add').click((e) =>
	{
		let $this = $(e.currentTarget);
		let $container = $this.prevAll('.sortable');
		let $mci = $container.find('.dummy').clone();
		let mci_html = $mci[0].outerHTML.replace(/#ID#/g, ++id);

		let $new_mci = $(mci_html).removeClass('dummy').appendTo($container);

		toggleEntry($new_mci.find('.mci-toggle'), 0);
	});

	$(document).on('click', '.mci-remove', (e) =>
	{
		$(e.currentTarget).closest('.mci').remove();
	});

	$(document).on('change keyup paste', '.mci-title', (e) =>
	{
		let $this = $(e.currentTarget);

		$this.closest('.mci').find('.info-title').html($this.val());
	});

	$(document).on('click', '.mci-recrop:not(.disabled)', (e) =>
	{
		let $this = $(e.currentTarget);
		let $mci = $this.closest('.mci');
		let id = getID($mci);

		initCropper(id, $mci);
		showCropper(id, $mci, $mci.find('.mci-image-original').val());
	});

	$mci_meta.on('change', '.mci-upload', (e) =>
	{
		let $this = $(e.currentTarget);
		let $mci = $this.closest('.mci');
		let id = getID($mci);

		initCropper(id, $mci);

		let reader = new FileReader();

		reader.onload = (e) =>
		{
			showCropper(id, $mci, e.target.result);
		};

		reader.readAsDataURL($this[0].files[0]);
	});

	$(document).on('change', 'select', (e) =>
	{
		let $this = $(e.currentTarget);
		let $mci = $this.closest('.mci');
		let id = getID($mci);

		let size = getSelectedSize($mci);
		let ar = getAspectRatio(size.width, size.height);

		croppers[id].setAspectRatio(ar);
	});

	$(document).on('submit', 'form', () =>
	{
		$('.mci.dummy').remove();

		$('.mci').not('.dummy').each((i, v) =>
		{
			let $mci_image = $(v).find('.mci-image');

			if ($mci_image.attr('src') !== '')
			{
				let size = getSelectedSize($(v));
				let id = getID($(v));
				let data = croppers[id].getCroppedCanvas({
					width: size.width,
					height: size.height
				}).toDataURL();

				$(v).find('.mci-image-base64').val(data);
				$(v).find('.mci-image-width').val(size.width);
				$(v).find('.mci-image-height').val(size.height);
				$(v).find('.mci-image-thumb_size').val(size.thumb_size);
			}
		});
	});

	$(document).on('click', '.mci-toggle', (e) =>
	{
		toggleEntry($(e.currentTarget), 200);
	});

	$(document).on('click', '.cancel', (e) =>
	{
		let $this = $(e.currentTarget);
		let $mci = $this.closest('.mci');
		let id = getID($mci);

		croppers[id].destroy();

		$mci.find('.mci-controls-container').hide();

		$mci.find('.mci-upload').val(null);
	});

	$(document).on('click', '.info-shortcode', (e) =>
	{
		let $this = $(e.currentTarget);
		let $notification = $this.find('.notification');

		$this.find('input')[0].select();

		if (!$notification.is(':animated') && document.execCommand('copy'))
		{
			$notification.fadeIn(600, () =>
			{
				setTimeout(() =>
				{
					$notification.fadeOut(500);
				}, 500);
			});
		}
	});

	function initCropper(id, $mci)
	{
		if (croppers[id] === undefined)
		{
			croppers[id] = new Cropper($mci.find('.mci-image')[0], {
				viewMode: 2,
				dragMode: 'move'
			});
		}
	}

	function showCropper(id, $mci, image)
	{
		let size = getSelectedSize($mci);
		let ar = getAspectRatio(size.width, size.height);

		if (!$mci.find('.mci-body').is(':visible'))
		{
			toggleEntry($mci, 0);
		}

		croppers[id].replace(image);
		croppers[id].setAspectRatio(ar);

		$mci.find('.mci-controls-container').show();

		scrollTo($mci);
	}

	function getSelectedSize($mci)
	{
		let size = JSON.parse($mci.find('.mci-size option:selected').val());

		return {
			width: Math.ceil(parseFloat(size.width)),
			height: Math.ceil(parseFloat(size.height)),
			thumb_size: Math.ceil(parseFloat(size.thumb_size))
		};
	}

	function getAspectRatio(width, height)
	{
		return (width === 0 || height === 0) ? 0 : width / height;
	}

	function getID($mci)
	{
		return parseInt($mci.find('.mci-image-id').val());
	}

	function getBiggestID()
	{
		let ids = [];

		$('.mci').not('.dummy').each(function(i, v)
		{
			ids.push(getID($(v)));
		});

		return ids.length > 0 ? Math.max.apply(Math, ids) : 0;
	}

	function scrollTo($object)
	{
		$('html, body').animate({
				scrollTop: $object.offset().top - 32
			},
			'slow');
	}

	function toggleEntry($toggle, speed)
	{
		$toggle.toggleClass('toggled').closest('.mci').find('.mci-body').slideToggle(speed);
	}
});