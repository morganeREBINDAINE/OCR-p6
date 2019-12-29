$('#add_imgs_btn').on('click', (evt) => {
    evt.preventDefault()
    $('#trick_imagesFiles').trigger('click')
})

$('#trick_imagesFiles').on('change', (evt) => {
    $('#add_imgs_btn').hide()
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
            $('#add_imgs_btn').show()
            $('.loader').hide()
            $('#media').append(data.view)
            console.log('success', data)
        },
        error: (e) => {
            $('#add_imgs_btn').show()
            $('.loader').hide()
            console.log('error ajax', e)
        }
    })
})