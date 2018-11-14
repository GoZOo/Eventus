//Upload file
jQuery(document).ready(function ($) {
    // Uploading files
    var file_frame;
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    var set_to_post_id = jQuery('#image_attachment_id').val(); // Set this
    jQuery('#upload_image_button').on('click', function (event) {
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if (file_frame) {
            // Set the post ID to what we want
            file_frame.uploader.uploader.param('post_id', set_to_post_id);
            // Open frame
            file_frame.open();
            return;
        } else {
            // Set the wp.media post id so the uploader grabs the ID we want when initialised
            wp.media.model.settings.post.id = set_to_post_id;
        }
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Sélectionnez l\'image par défaut de l\'équipe',
            button: {
                text: 'Utiliser cette image',
            },
            multiple: false	// Set to true to allow multiple files to be selected
        });
        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();
            // Do something with attachment.id and/or attachment.url here
            $('#image-preview').attr('src', attachment.url).css('width', 'auto');
            $('#image_attachment_id').val(attachment.id);
            // Restore the main post ID
            wp.media.model.settings.post.id = wp_media_post_id;
        });
        file_frame.on('open', function () {
            var selection = file_frame.state().get('selection');
            var selected = jQuery('#image_attachment_id').val(); // the id of the image
            if (selected) {
                selection.add(wp.media.attachment(selected));
            }
        });
        // Finally, open the modal
        file_frame.open();
    });
    // Restore the main ID when the add media button is pressed
    jQuery('a.add_media').on('click', function () {
        wp.media.model.settings.post.id = wp_media_post_id;
    });
});

// Button delete image 
if(!jQuery('#image_attachment_id').val()){
	jQuery('#delete_image_button').attr('disabled', 'true');
} else {	
	jQuery('#delete_image_button').removeAttr('disabled');
}
jQuery('#delete_image_button').click(()=>{
	jQuery('#image_attachment_id').val("");
	jQuery('#delete_image_button').attr('disabled', 'true');
});
