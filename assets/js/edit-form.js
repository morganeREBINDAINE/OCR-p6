const urlRegex = new RegExp('^/modifier-figure-[0-9]{1,}$')

if (urlRegex.test(window.location.pathname)) {
    // get trick from URL
    const trickID = window.location.pathname.split('-')[2]

    //functions
    const deleteImagesButtonsEffect = (evt) => {
        evt.preventDefault()
        const actualBtn = $(evt.currentTarget)


        actualBtn.hide()
        $(actualBtn[0].parentElement).append($('<img src="images/loader.gif" />'))

        let formData = new FormData()
        formData.append('trick', trickID)
        formData.append('token', actualBtn[0].previousElementSibling.previousElementSibling.value)

        $.ajax({
            url: '/delete-image-' + actualBtn[0].previousElementSibling.value,
            type: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            async: true,
            success: (data) => {
                console.log(data)
                if (data.changed !== false) {
                    (data.changed === 'empty') ?
                        $('.content-img').css('background-image', 'url(/images/placehold.jpg)') :
                        $('.content-img').css('background-image', 'url(/images/tricks/' + data.changed + ')')
                }
                $(actualBtn[0].parentElement.parentElement).remove()
            },
            error: (e) => {
                console.log('error ajax', e)
            }
        })
    }

    const deleteVideosButtonsEffect = (evt) => {
        evt.preventDefault()
        const actualBtn = $(evt.currentTarget)
        const parentElement = evt.currentTarget.parentElement.parentElement

        actualBtn.hide()
        $(actualBtn[0].parentElement).append($('<img src="images/loader.gif" />'))

        let formData = new FormData()
        formData.append('token', actualBtn[0].previousElementSibling.previousElementSibling.value)

        $.ajax({
            url: '/delete-video-' + actualBtn[0].previousElementSibling.value,
            type: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            async: true,
            success: (data) => {
                parentElement.remove()
            },
            error: (e) => {
                actualBtn.show()
                console.log('error ajax', e)
            }
        })
    }

    const changeMainImageEffect = (evt) => {
        evt.preventDefault()
        const infos = evt.currentTarget.previousElementSibling
        const token = infos.children[0].value
        const img = infos.children[1].value
        const bg_img = evt.currentTarget.parentElement.style.backgroundImage

        if (document.querySelector('.content-img').style.backgroundImage
            !== evt.currentTarget.parentElement.style.backgroundImage) {
            $('.content-img').css('background-image', bg_img)
            var formData = new FormData()
            formData.append('token', token)
            formData.append('trick', trickID)

            $.ajax({
                url: '/replace-mainimg-' + img,
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
            if (mimes.includes(evt.target.files[i].type)) {
                formData.append('file' + i, evt.target.files[i])
            } else {
                error = true
            }
        }

        $.ajax({
            url: '/create_images',
            type: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            async: true,
            success: (data) => {
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
            $('#errors').show().html('Merci de ne sélectionner que jpg ou png. Un autre type de fichier a été détecté.').delay(4000).hide()
        }
    })


    // add video
    $('.add_videos_btn').on('click', (evt) => {
        evt.preventDefault()
        $('.add_videos_btn').hide()
        $('.content-content-btn').append($('<div class="div-input-video"><label>Collez une balise iframe puis entrez</label><input class="input-video form-control" type="text" placeholder="<iframe src=...></iframe>"><span class="error-msg input-video-error"></span></div>'))

        const addVideo = (evt) => {
            const element = evt.target.value
            const regex = new RegExp('^<iframe.+>$')

            if (regex.test(element)) {
                var formData = new FormData()
                formData.append('iframe', element)
                formData.append('trick', trickID)

                $.ajax({
                    url: '/create_video',
                    type: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    data: formData,
                    async: true,
                    success: (data) => {
                        console.log('success', data)
                        $('.div-input-video').remove()
                        $('#media-videos').append(data.view)
                        $('.add_videos_btn').show()
                        $('.trick-video-delete').off('click').on('click', deleteVideosButtonsEffect)
                    },
                    error: (e) => {
                        console.log('error', e)
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

