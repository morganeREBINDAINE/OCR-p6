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
            $('#media-images').prepend(image_container)

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
    const videos = []
    $('.add_videos_btn').on('click', (evt)=> {
        evt.preventDefault()
        $('.add_videos_btn').hide()
        $('.content-content-btn').append($('<div id="div-input-video"><label>Collez une balise iframe</label><input id="input-video" type="text" placeholder="<iframe src=...></iframe>"></div>'))

        const addVideo = (evt) => {
            // @todo error msg if not match regex
            const element = evt.target.value
            const regex = new RegExp('^<iframe.+>$')

            if (regex.test(element)) {
                const video = $('<div col="col-sm"><div class="video">'+element+'<div class="image-icons"><a class="trick-video-delete"><i class="fas fa-trash-alt"></i></a></div></div></div>')
                $('#media-videos').append(video)
                //delete preview video
                $('.trick-video-delete').off('click')
                $('.trick-video-delete').on('click', (evt) => {
                    evt.preventDefault()
                    const targetVideo = evt.currentTarget.parentElement.parentElement.parentElement
                    const allVideos = document.querySelector('#media-videos').children

                    for (var i = 0; i < allVideos.length; i++) {
                        if (allVideos[i] == targetVideo) {
                            videos.splice(i, 1)
                            $(targetVideo).remove()
                        }
                    }
                })


                videos.push(element)
                $('#div-input-video').remove()
                $('.add_videos_btn').show()
            }
        }

        $('#input-video').blur(addVideo)
        $('#input-video').keydown((evt) => {
            if (evt.keyCode === 13) {
                addVideo(evt)
            }
        })
    })

    $('form').on('submit', () => {
        $("<input />").attr("type", "hidden")
            .attr("name", "videos")
            .attr("value", videos.join('|||'))
            .appendTo("form");
    })
}
