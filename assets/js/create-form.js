var urlRegex = new RegExp('^/creer-figure$')

if (urlRegex.test(window.location.pathname)) {
    const mimes = [
        "image/jpeg",
        "image/png"
    ]

    //display image preview
    $('#trick_imagesFiles').on('change', (evt) => {
        $('#media-images').empty()
        $('.delete_imgs_btn').remove()
        if (evt.target.files.length > 0) {
            var error = false
            for (i = 0; i < evt.target.files.length; i++) {
                if (mimes.includes(evt.target.files[i].type) && evt.target.files[i].size < 2000000) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        const image = $('<div class="image generated" style="background-image: url('+event.target.result+')"></div>').append(image)
                        $('#media-images').append(image)
                    }

                    reader.readAsDataURL(evt.target.files[i]);
                }
                else {
                    error = true
                }
            }

            if (error) {
                $('#trick_imagesFiles').val('')
                $('#errors').show().html('Les images doivent être de format jpg ou png, inférieures à 2Mo. Une image (ou plusieurs) non conforme a été détectée et ignorée.')
                setTimeout(()=> {
                    $('#errors').hide()
                }, 5000)
            } else {
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
            }

        }
    })

    // main img trigger click
    $('.trick-image-edit, .trick-image-add').on('click', (evt) => {
        evt.preventDefault()
        $('#trick_mainImageFile').trigger('click')
    })

    // main img display preview
    $('#trick_mainImageFile').on('change', (evt) => {
        if (evt.target.files.length > 0 && mimes.includes(evt.target.files[0].type)) {
            $('.trick-image-edit, .trick-image-delete').show()
            $('.trick-image-add').hide()

            var reader = new FileReader();

            reader.onload = function(event) {
                $('.generated.main').remove()
                const image = $('<div class="image generated main" style="background-image: url('+event.target.result+')"></div>')
                $('#mainphoto-title').show().append(image)

                $('.content-img').css('background-image', 'url('+event.target.result+')').addClass('has-image')
            }

            reader.readAsDataURL(evt.target.files[0]);
        } else {
            $('#trick_mainImageFile').val('')
            $('#errors').show().html('Merci de ne sélectionner que jpg ou png. Un autre type de fichier a été détecté.').delay(4000).hide()
        }
    })

    // empty input mainImg
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
                $('.trick-video-delete').off('click')
                $('.trick-video-delete').on('click', (evt) => {
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
