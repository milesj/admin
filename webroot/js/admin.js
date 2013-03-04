$(function() {
	$('.clickable tbody tr').click(function() {
		var id = $(this).find('.col-id');

		if (id.length) {
			location.href = id.find('a:first').attr('href');
		}
	});

	$('#check-all').click(function() {
		$('#table input:checkbox').prop('checked', $(this).prop('checked'));
	});
});