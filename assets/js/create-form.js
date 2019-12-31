const urlRegex = new RegExp('^/creer-figure$')

if (urlRegex.test(window.location.pathname)) {
    //display image preview
    $('#trick_imagesFiles').on('change', (evt) => {
        $('#media-images').empty()
        for (i = 0; i < evt.target.files.length; i++) {
            var reader = new FileReader();

            reader.onload = function(event) {
                const image = $('<div>').css('background-image', 'url('+event.target.result+')');
                const image_container = $('<div class="col-sm generated"></div>')
                image_container.append(image)
                $('#media-images').append(image_container)
            }

            reader.readAsDataURL(evt.target.files[i]);
        }
    })

    //main_img trigger click
    $('.trick-image-edit, .trick-image-add').on('click', (evt) => {
        evt.preventDefault()
        $('#trick_mainImageFile').trigger('click')
    })

    // main img display preview
    $('#trick_mainImageFile').on('change', (evt) => {
        if (evt.target.files.length > 0) {
            $('.trick-image-edit, .trick-image-delete').show()
            $('.trick-image-add').hide()
        }
        var reader = new FileReader();

        reader.onload = function(event) {
            $('.generated.main').remove()
            const image = $('<div>').css('background-image', 'url('+event.target.result+')');
            const image_container = $('<div class="col-sm generated main"></div>')
            image_container.append(image)
            $('#media').append(image_container)

            $('.content-img').css('background-image', 'url('+event.target.result+')').addClass('has-image')
        }

        reader.readAsDataURL(evt.target.files[0]);
    })

    // empty input mainImg
    $('.trick-image-delete').on('click', (evt) => {
        evt.preventDefault()
        $('#trick_mainImageFile').val('')
        $('.generated.main').remove()
        $('.content-img').css('background-image', 'url(/images/placehold.jpg)')
        $('.trick-image-edit, .trick-image-delete').hide()
        $('.trick-image-add').show()
    })

    //add video

}
