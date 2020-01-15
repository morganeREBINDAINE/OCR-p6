// A special file has been created to handle the trick editing form.
// Images and videos are handled dynamically to enhance pleasant UX.

if ((new RegExp('^/modifier-figure-[0-9]{1,}$')).test(window.location.pathname)) {
    // get trick from URL
    const trickID = window.location.pathname.split('-')[2]

    //functions
    const deleteButtonEffect = (evt, ajax) => {
        evt.preventDefault()

        $(evt.currentTarget).hide()
        $(evt.currentTarget.parentElement).append($('<img src="images/loader.gif" />'))

        let formData = new FormData()
        formData.append('trick', trickID)
        formData.append('token', evt.currentTarget.previousElementSibling.previousElementSibling.value)

        ajax(formData)
    }
    const deleteImagesButtonsEffect = (evt) => {
        deleteButtonEffect(evt, (formData) => {
            $.ajax({
                url: '/delete-image-' + evt.currentTarget.previousElementSibling.value,
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                async: true,
                success: (data) => {
                    if (data.changed !== false) {
                        (data.changed === 'empty') ?
                            $('.content-img').css('background-image', 'url(/images/placehold.jpg)') :
                            $('.content-img').css('background-image', 'url(/images/tricks/' + data.changed + ')')
                    }
                    $(evt.currentTarget.parentElement.parentElement).remove()
                }
            })
        })

    }
    const deleteVideosButtonsEffect = (evt) => {
        deleteButtonEffect(evt, (formData) => {
            $.ajax({
                url: '/delete-video-' + evt.currentTarget.previousElementSibling.value,
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                async: true,
                success: (data) => {
                    evt.currentTarget.parentElement.parentElement.remove()
                },
                error: (e) => {
                    $(evt.currentTarget).show()
                }
            })
        })
    }
    const changeMainImageEffect = (evt) => {
        evt.preventDefault()
        const infos = evt.currentTarget.previousElementSibling

        const target_token = infos.children[0].value
        const target_id = infos.children[1].value
        const target_bg = evt.currentTarget.parentElement.style.backgroundImage
        const mainImage_bg = document.querySelector('.content-img').style.backgroundImage

        if (mainImage_bg !== target_bg) {
            $('.content-img').css('background-image', target_bg)
            var formData = new FormData()
            formData.append('token', target_token)
            formData.append('trick', trickID)

            $.ajax({
                url: '/replace-mainimg-' + target_id,
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                async: true,
                success: (data) => {
                    if (data.error === true) {
                        $('.add_imgs_btn').append($('<span class="error-msg">Erreur lors du changement d\'image</span>'))
                    }
                },
                error: (e) => {
                    $('.content-img').css('background-image', 'url(/images/placehold.jpg)')
                    $('.add_imgs_btn').append($('<span class="error-msg">Erreur lors du changement d\'image</span>'))
                }
            })
        }
    }

    // event listener
    $('.trick-image-delete').on('click', deleteImagesButtonsEffect)
    $('.trick-video-delete').on('click', deleteVideosButtonsEffect)
    $('.mainimg-btn').on('click', changeMainImageEffect)


    // add imgs
    $('#trick_imagesFiles').on('change', (evt) => {
        $('.add_imgs_btn').hide()
        $('.loader').show()

        let formData = new FormData()
        formData.append('trick', trickID)

        var error = false

        for (var i = 0; i < evt.target.files.length; i++) {
            if (["image/jpeg", "image/png"].includes(evt.target.files[i].type)
                && evt.target.files[i].size < 2000000) {
                formData.append('file' + i, evt.target.files[i])
            } else {
                error = true
            }
        }

        $.ajax({
            url: '/create-images',
            type: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            async: true,
            success: (data) => {
                console.log(data)
                if (!data.error) {
                    $('.add_imgs_btn').show()
                    $('.loader').hide()
                    $('#media-images').append(data.view)
                    if (data.changed !== false) {
                        $('.content-img').css('background-image', 'url(/images/tricks/' + data.changed + ')')
                    }
                    $('.trick-image-delete').off('click').on('click', deleteImagesButtonsEffect)
                    $('.mainimg-btn').off('click').on('click', changeMainImageEffect)
                } else {
                    $('.add_imgs_btn').append($('<span class="error-msg">Erreur lors de l\'ajout</span>'))
                }
            },
            error: (e) => {
                $('.add_imgs_btn').show()
                $('.loader').hide()
                $('.add_imgs_btn').append($('<span class="error-msg">Erreur lors de l\'ajout</span>'))
            }
        })

        if (error) {
            displayErrorUploadedImages($('#trick_imagesFiles'))
        }
    })


    // add video
    $('.add_videos_btn').on('click', (evt) => {
        evt.preventDefault()
        $('.add_videos_btn').hide()
        $('.content-content-btn').append($('<div class="div-input-video"><label>Collez une balise iframe puis entrez</label><input class="input-video form-control" type="text" placeholder="<iframe src=...></iframe>"><span class="error-msg input-video-error"></span></div>'))

        const addVideo = (evt) => {
            const element = evt.target.value

            if ((new RegExp('^<iframe.+>$')).test(element)) {
                var formData = new FormData()
                formData.append('iframe', element)
                formData.append('trick', trickID)

                $.ajax({
                    url: '/create-video',
                    type: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    data: formData,
                    async: true,
                    success: (data) => {
                        $('.div-input-video').remove()
                        $('#media-videos').append(data.view)
                        $('.add_videos_btn').show()
                        $('.trick-video-delete').off('click').on('click', deleteVideosButtonsEffect)
                    },
                    error: (e) => {
                        $('.div-input-video').remove()
                        $('.add_videos_btn').show()
                    }
                })

                $('.div-input-video').html('').append($('<img src="images/loader.gif" />'))
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


}

