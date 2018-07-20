jQuery(document).ready(function(jQuery){

    var custom_uploader;

    jQuery('#upload_image_button').click(function(e) {

        e.preventDefault();

        // Si el objeto Uploader ya ha sido creado, reabrimos el cuadro de dialogo.
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        // Extendemos del objeto wp.media
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Usar Imagen',
            button: {
                text: 'Usar Imagen'
            },
            multiple: false
        });

        // Cuando añadimos un archivo, se atrapa la URL y se manda al campo de texto #upload_image.
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#upload_image').val(attachment.url);
        });

        // Abre el cuadro de dialogo
        custom_uploader.open();

    });

});