// imgs button
$('.add_imgs_btn').on('click', (evt) => {
    evt.preventDefault()
    $('#trick_imagesFiles').trigger('click')
})

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
            $('#media').append(data.view)
            if (data.changed !== false) {
                $('.content-img').css('background-image', 'url('+data.changed+')')
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

    console.log(evt.currentTarget)
})

// add video
$('.add_videos_btn').on('click', (evt)=>{
    evt.preventDefault()
    $('#add_videos_btn')
})