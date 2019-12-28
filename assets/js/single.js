$('#trick-image-add, #trick-image-edit').on('click', (evt) => {
    evt.preventDefault()
    $('#trick_imageFile_file').trigger('click')
})

$('#trick-image-delete').on('click', (evt) => {
    evt.preventDefault()
    $('#trick_imageFile_file').value = ""
    $('.content-img').css('background-image', `url(/images/placehold.jpg)`)
    $('.content-img-icons').removeClass('has-image')
})

$('#trick_imageFile_file').on('change', (evt) => {
    var reader = new FileReader();
    if (typeof reader != "undefined") {
        reader.onload = function (e) {
            $('.content-img').css('background-image', `url(${e.target.result})`)
            $('.content-img-icons').addClass('has-image')
        }
        reader.readAsDataURL(evt.target.files[0]);
    }
})

$('#flash').modal('show')