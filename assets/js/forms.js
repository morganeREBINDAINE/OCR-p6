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

// display flash error message on improper uploaded image
const displayErrorUploadedImages = (target) => {
    target.val('')
    $('#errors').show().html('Erreur: Les images doivent être de format jpg ou png, inférieures à 2Mo. Merci de resélectionner une/des image(s) conforme(s).')
    setTimeout(() => {
        $('#errors').hide()
    }, 6000)
}