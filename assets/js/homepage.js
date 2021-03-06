// set height homepage bg
$('#full-width-img').css('height', (window.innerHeight-55))

// animate arrow down
$(function(){
    $('.arrow-down').delay( 1000 ).fadeIn( 800 )
    setTimeout(function(){
        let started = false
        const addBounce = () => {
            if(started === false) {
                started = true
                $('.arrow-down').addClass('animated bounce')
                setTimeout(function(){
                    $('.arrow-down').removeClass('animated bounce')
                    started = false
                }, 1000)
            }
        }

        addBounce()
        $('.arrow-down').hover(function(){
            addBounce()
        })
    }, 4000)
})

// ajax call load more
$('.load-btn').on('click', function() {
    $('.load-btn').hide()
    $('.loader').show()

    var formData = new FormData()
    formData.append('first', $('.trick-card').length)

    $.ajax({
        url:'/ajax_request/tricks',
        type: "POST",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        async: true,
        success: (data) => {
            switch (data.count) {
                case 5:
                    $('.arrow-up').show()
                    $('.load-btn').show()
                    break
                case 0:
                    $('#tricks').after($('<div>Il n\'y a plus de figures.</div>'))
                    break
            }
            $('.loader').hide()
            $('#tricks').append(data.view)
        },
        error: () => {
            $('.loader').hide()
        }
    })
})