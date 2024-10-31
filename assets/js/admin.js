jQuery(document).ready(function($) {
	var mediaUploader;

	var mediaUploader_inverted;
	$('#upload-picture-button').on('click', function(e) {
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Select a Picture',
			button: {
				text: 'Select Picture',
			},
			multiple: false,
		});

		mediaUploader.on('select', function() {
			attachment = mediaUploader
				.state()
				.get('selection')
				.first()
				.toJSON();
			$('#piri_faq_ai_assistant_bot_src_value').val(attachment.url);
			$('#user-picture-preview').css('background-image', 'url(' + attachment.url + ')');
		});

		mediaUploader.open();
	});

	$('#upload-picture-button-bot-inverted').on('click', function(e) {
		e.preventDefault();
		if (mediaUploader_inverted) {
			mediaUploader_inverted.open();
			return;
		}

		mediaUploader_inverted = wp.media.frames.file_frame = wp.media({
			title: 'Select a Picture',
			button: {
				text: 'Select Picture',
			},
			multiple: false,
		});

		mediaUploader_inverted.on('select', function() {
			attachment = mediaUploader_inverted
				.state()
				.get('selection')
				.first()
				.toJSON();
			$('#piri_faq_ai_assistant_bot_inverted_value').val(attachment.url);
			$('#piri_faq_ai_assistant_bot_inverted_value_preview').css(
				'background-image',
				'url(' + attachment.url + ')'
			);
		});

		mediaUploader_inverted.open();
	});

	$(function() {
		$('#piri_faq_ai_assistant_primary_color_value').wpColorPicker();
	});
});
