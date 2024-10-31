jQuery(($) =>
{
	'use strict';

	let $mci_options = $('#mci-options');

	$mci_options.on('click', '.mci-remove', (e) =>
	{
		let $this = $(e.currentTarget);

		$this.closest('.size').remove();
	});

	$mci_options.find('.add').click((e) =>
	{
		let $this = $(e.currentTarget);
		let $td   = $this.closest('td');
		let $id   = $td.find('.id');

		let id        = parseInt($id.html());
		let post_type = $td.find('.post_type').html();

		let $size     = $this.prevAll('.dummy').clone();
		let size_html = $size[0].outerHTML.replace(/#POST_TYPE#/g, post_type).replace(/#ID#/g, id);

		$(size_html).removeClass('dummy').insertBefore($this);

		$id.html(++id);
	});

	$mci_options.on('submit', (e) =>
	{
		let $this = $(e.currentTarget);

		$this.find('.dummy').remove();

		$this.submit();
	});
});