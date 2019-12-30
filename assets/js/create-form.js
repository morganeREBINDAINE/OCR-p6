const urlRegex = new RegExp('^/creer-figure$')

if (urlRegex.test(window.location.pathname)) {
    //display image preview and video
    $('#trick_imagesFiles').on('change', (evt) => {
        console.log(evt.target.files)
        for (i = 0; i < evt.target.files.length; i++) {
            var reader = new FileReader();

            reader.onload = function(event) {
                const image = $('<div>').css('background-image', 'url('+event.target.result+')');
                const image_container = $('<div class="col-sm generated"></div>')
                image_container.append(image)
                $('#media-images').append(image_container)
                $('.generated').off('click')
                $('.generated').on('click', (evt) => {
                    document.querySelectorAll('.generated')
                    // for(j=0;j<elem.parentNode.length;j++) {
                    //     // if (elem.parentNode[i] == elem) //.... etc.. etc...
                    // }
                })
        // console.log(document.querySelectorAll('.generated'))
            }

            reader.readAsDataURL(evt.target.files[i]);
        }
    })

}
