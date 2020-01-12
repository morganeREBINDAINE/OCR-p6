$('#flash').modal('show')


$('#show-medias').on('click', (evt) => {
    evt.preventDefault()
    $('#medias').toggle()
    console.log($(evt.target).html())
    switch ($(evt.target).html()) {
        case 'Voir les médias':
            $(evt.target).html('Cacher les médias')
            break
        case 'Cacher les médias':
            $(evt.target).html('Voir les médias')
            break
    }
})


$('.load-comments-btn').on('click', () => {
    $('.load-comments-btn').hide()
    $('.loader').show()

    var formData = new FormData()
    formData.append('first', $('.comment').length)
    formData.append('trick', window.location.pathname.split('-')[1])

    $.ajax({
        url:'/ajax_request/comments',
        type: "POST",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        async: true,
        success: (data) => {
            switch (data.count) {
                case 5:
                    $('.load-comments-btn').show()
                    break
                case 0:
                    $('.comments-published').append($('<div>Il n\'y a plus de commentaires.</div>'))
                    break
            }
            $('.loader').hide()
            $('.comments-published').append(data.view)
        },
        error: () => {
            $('.loader').hide()
            console.log('error ajax')
        }
    })
})