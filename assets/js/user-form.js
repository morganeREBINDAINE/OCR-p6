// preview avatar selected name
$('#registration_avatarFile').on('change', (evt) => {
    $('#avatar-name').empty().html('<div class="mb-4">Vous avez sélectionné : '+ evt.target.files[0].name + '</div>')
})

// display pretty button in place of nasty file input
$('#add-avatar-btn').on('click', (evt) => {
    evt.preventDefault()
    $('#registration_avatarFile').trigger('click')
})