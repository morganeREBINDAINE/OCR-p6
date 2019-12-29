// imgs button
$('#add_imgs_btn').on('click', (evt) => {
    evt.preventDefault()
    $('#trick_imagesFiles').trigger('click')
})

// upload imgs
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

//delete imgs
$('.trick-image-delete').on('click', (evt) => {
    evt.preventDefault()
    const actualBtn = $(evt.currentTarget)
    const loader = $('.loader').clone()

    actualBtn.hide()
    $(actualBtn[0].parentElement).append($('<img src="images/loader.gif" />'))

    let formData = new FormData()
    formData.append('token', actualBtn[0].previousElementSibling.previousElementSibling.value)

    $.ajax({
        url: '/delete-image-'+ actualBtn[0].previousElementSibling.value,
        type: "POST",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        async: true,
        success: () => {
            $(actualBtn[0].parentElement.parentElement.parentElement).remove()
        },
        error: (e) => {
            console.log('error ajax', e)
        }
    })
})

