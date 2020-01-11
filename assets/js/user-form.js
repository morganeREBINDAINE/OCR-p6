$('#registration_avatarFile').on('change', (evt) => {
    $('#avatar-name').empty().html('<div class="mb-4">Vous avez sélectionné : '+ evt.target.files[0].name + '</div>')
})