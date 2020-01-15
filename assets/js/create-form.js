// A special file has been created to handle the trick creation form.
// Images and videos are handled on submission, because the trick hasn't id yet.

if ((new RegExp('^/creer-figure$')).test(window.location.pathname)) {
    // FUNCTIONS
    const previewImagesUploaded = (target, treatment, onSuccess) => {
        let error = false

        const files = target.files

        if (files.length > 0) {
            for (i = 0; i < files.length; i++) {
                if (["image/jpeg", "image/png"].includes(files[i].type)
                    && files[i].size < 2000000
                ){
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        treatment(event)
                    }

                    reader.readAsDataURL(files[i]);
                }
                else {
                    error = true
                }
            }
        }

        error ? displayErrorUploadedImages($(target)) : onSuccess()
    }


    // EVENTS

    // trigger input on main image button click
    $('.trick-image-edit, .trick-image-add').on('click', (evt) => {
        evt.preventDefault()
        $('#trick_mainImageFile').trigger('click')
    })

    // empty input on main image trash button
    $('.trick-image-delete').on('click', (evt) => {
        evt.preventDefault()
        $('#trick_mainImageFile').val('')
        $('.generated.main').remove()
        $('.content-img').css('background-image', 'url(/images/placehold.jpg)')
        $('.trick-image-edit, .trick-image-delete').hide()
        $('.trick-image-add').show()
        $('#mainphoto-title').hide()
        $('#errors').hide()
    })

    // display trick images preview
    $('#trick_imagesFiles').on('change', (evt) => {
        $('#media-images').empty()
        $('.delete_imgs_btn').remove()

        previewImagesUploaded(evt.target, (event) => {
                const image = $('<div class="image generated" style="background-image: url('+event.target.result+')"></div>').append(image)
                $('#media-images').append(image)
        }, () => {
            $('#errors').hide()
            $('#photos-title').show()
            $('<a class=" delete_imgs_btn btn btn-blue">Supprimer les images</a>').insertAfter($('#media-images'))

            $('.delete_imgs_btn').on('click', (evt) => {
                evt.preventDefault()
                $('#trick_imagesFiles').val('')
                $('#media-images').empty()
                $('.delete_imgs_btn').remove()
                $('.add_imgs_btn').html('Ajouter des images')
                $('#photos-title').hide()
            })

            $('.add_imgs_btn').html('Modifier les images')
        })

    })

    // display main image preview
    $('#trick_mainImageFile').on('change', (evt) => {
        $('.trick-image-edit, .trick-image-delete').show()
        $('.trick-image-add').hide()

        previewImagesUploaded(evt.target, (event) => {
            $('.generated.main').remove()
            const image = $('<div class="image generated main" style="background-image: url('+event.target.result+')"></div>')
            $('#mainphoto-title').show().append(image)

            $('.content-img').css('background-image', 'url('+event.target.result+')').addClass('has-image')
        })
    })

    //add video
    const videos = []
    $('.add_videos_btn').on('click', (evt)=> {
        evt.preventDefault()
        $('.add_videos_btn').hide()
        $('.content-content-btn').append($('<div class="div-input-video"><label>Collez une balise iframe puis entrez</label><input class="input-video form-control" type="text" placeholder="<iframe src=...></iframe>"><span class="error-msg input-video-error"></span></div>'))

        const addVideo = (evt) => {
            const element = evt.target.value
            const regex = new RegExp('^<iframe.+>$')

            if (regex.test(element)) {
                const video = $('<div col="col-sm"><div class="video">'+element+'<div class="image-icons"><a class="trick-video-delete"><i class="fas fa-trash-alt"></i></a></div></div></div>')
                $('#videos-title').show()
                $('#media-videos').append(video)

                //delete previewed video
                $('.trick-video-delete').off('click').on('click', (evt) => {
                    evt.preventDefault()
                    const targetVideo = evt.currentTarget.parentElement.parentElement.parentElement
                    const allVideos = document.querySelector('#media-videos').children

                    for (var i = 0; i < allVideos.length; i++) {
                        if (allVideos[i] == targetVideo) {
                            videos.splice(i, 1)
                            $(targetVideo).remove()
                            if(videos.length === 0){
                                $('#videos-title').hide()
                            }
                        }
                    }
                })

                videos.push(element)
                $('.div-input-video').remove()
                $('.add_videos_btn').show()
            } else {
                $(evt.target.nextSibling).html('Balise iframe incorrecte !')
            }
        }

        $('.input-video').blur(addVideo)
        $('.input-video').keydown((evt) => {
            if (evt.keyCode === 13) {
                addVideo(evt)
            }
        })
    })

    // onsubmit, send videos
    $('form').on('submit', () => {
        $("<input />").attr("type", "hidden")
            .attr("name", "videos")
            .attr("value", videos.join('|||'))
            .appendTo("form");
    })
}
