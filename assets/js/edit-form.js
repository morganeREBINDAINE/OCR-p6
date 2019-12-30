const urlRegex = new RegExp('^/modifier-figure-[0-9]?$')

if (urlRegex.test(window.location.pathname)) {
    // upload imgs
    $('#trick_imagesFiles').on('change', (evt) => {
        $('.add_imgs_btn').hide()
        $('.loader').show()

        let formData = new FormData()
        formData.append('trick', trickID)

        for (var i = 0; i < evt.target.files.length; i++){
            formData.append('file'+i, evt.target.files[i])
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
                $('.add_imgs_btn').show()
                $('.loader').hide()
                $('#media-images').append(data.view)
                if (data.changed !== false) {
                    $('.content-img').css('background-image', 'url(/images/tricks/'+data.changed+')')
                }
            },
            error: (e) => {
                $('.add_imgs_btn').show()
                $('.loader').hide()
                console.log('error ajax', e)
            }
        })
    })

    //delete imgs
    $('.trick-image-delete').on('click', (evt) => {
        evt.preventDefault()
        const actualBtn = $(evt.currentTarget)
        const loader = $('.loader').clone()

        actualBtn.hide()
        $(actualBtn[0].parentElement).append($('<img src="images/loader.gif" />'))

        let formData = new FormData()
        formData.append('trick', trickID)
        formData.append('token', actualBtn[0].previousElementSibling.previousElementSibling.value)

        $.ajax({
            url: '/delete-image-'+ actualBtn[0].previousElementSibling.value,
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
                        $('.content-img').css('background-image', 'url(/images/placehold.jpg)') : $('.content-img').css('background-image', 'url('+data.changed+')')
                }
                $(actualBtn[0].parentElement.parentElement.parentElement).remove()
            },
            error: (e) => {
                console.log('error ajax', e)
            }
        })
    })

    // add video
    $('.add_videos_btn').on('click', (evt)=>{
        evt.preventDefault()
        $('.add_videos_btn').hide()
        $('.content-content-btn').append($('<div id="div-input-video"><label>Collez une balise iframe</label><input id="input-video" type="text" placeholder="<iframe src=...></iframe>"></div>'))

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
                        console.log('success',data)
                        $('#div-input-video').remove()
                        $('#media-videos').append(data.view)
                        $('.add_videos_btn').show()
                    },
                    error: (e) => {
                        console.log('error',e)
                        $('#div-input-video').remove()
                        $('.add_videos_btn').show()
                    }
                })

                $('#div-input-video').html('').append($('<img src="images/loader.gif" />'))
            }
            // @todo add error msg
        }

        $('#input-video').blur(addVideo)
        $('#input-video').keydown((evt) => {
            if (evt.keyCode === 13) {
                addVideo(evt)
            }
        })
    })

    // delete video
    $('.trick-video-delete').on('click', (evt) => {
        evt.preventDefault()
        const actualBtn = $(evt.currentTarget)
        const parentElement = evt.currentTarget.parentElement.parentElement.parentElement

        console.log(evt)

        actualBtn.hide()
        $(actualBtn[0].parentElement).append($('<img src="images/loader.gif" />'))

        let formData = new FormData()
        formData.append('token', actualBtn[0].previousElementSibling.previousElementSibling.value)

        $.ajax({
            url: '/delete-video-'+ actualBtn[0].previousElementSibling.value,
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
    })


    // choose main img
        $('.mainimg-btn').on('click', (evt) => {
        evt.preventDefault()
        const infos = evt.currentTarget.previousElementSibling
        const token = infos.children[0].value
        const img = infos.children[1].value
        const bg_img = evt.currentTarget.parentElement.children[0].style.backgroundImage

        $('.content-img').css('background-image', bg_img)

        var formData = new FormData()
        formData.append('token', token)
        formData.append('trick', trickID)

        $.ajax({
            url: '/replace-mainimg-'+ img,
            type: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            async: true,
            success: (data) => {console.log(data)},
            error: (e) => {
                $('.content-img').css('background-image', 'url(/images/placehold.jpg)')
                alert('error')
            }
        })
    })
}



// imgs button
$('.add_imgs_btn').on('click', (evt) => {
    evt.preventDefault()
    $('#trick_imagesFiles').trigger('click')
})

// disable submit on enter
$(window).keydown(function(event){
    if(event.keyCode === 13) {
        event.preventDefault();
    }
});