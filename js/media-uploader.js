jQuery(document).ready(function($){
    var custom_uploader;
    $('#select-media').click(function(e) {
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media({
            title: '選択',
            library: {
                type: 'image'
            },
            button: {
                text: '選択'
            },
            multiple: true // falseにすると画像を1つしか選択できなくなる
        });
        custom_uploader.on('select', function() {
            var images = custom_uploader.state().get('selection');
            images.each(function(file){
                var img = file.toJSON();
                $('#upload-images').append('<div class="image-item"><img image-id="'+ img.id +'" src="'+img.url+'" /><span image-id="'+ img.id +'" class="image-remove">削除</span></div>');
            });
        });
        custom_uploader.open();
    });

});