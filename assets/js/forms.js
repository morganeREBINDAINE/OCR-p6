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
