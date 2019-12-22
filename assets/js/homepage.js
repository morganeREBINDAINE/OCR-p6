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
    let id = $('.trick-card').length;
    $.ajax({
        url:'/ajax_request/' + id,
        type: "GET",
        dataType: "json",
        async: true,
        success: (data) => {
            $('.load-btn').show()
            $('.loader').hide()
            $('#tricks').append(data.view)
            $('.arrow-up').show()
        },
        error: () => {
            $('.loader').hide()
            console.log('error ajax')
        }
    })
})