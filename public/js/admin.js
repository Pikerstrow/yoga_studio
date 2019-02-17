/*SIDEBAR DROPDOWN SUBMENU SWITCHER*/
$(document).ready(function(){
    $('ul.sidebar-menu').find('li.dropdown>a').click(function(){
        $(this).closest('.dropdown').find('a>i.fa-angle-right').toggleClass('down');
        $(this).siblings('ul.sidebar_submenu').slideToggle(200);
    });
});


/* HIDE / SHOW SIDEBAR */
$(document).ready(function(){
    $('#hamburger_menu').click(function(){ 
        if ($(window).width() > 815 ) {            
            $('#sidebar-nav').toggleClass('sidebar-hide');
            $('#main-content').find('div.content').toggleClass('content_full_width');
        } else { 
            $('.sidebar-menu').slideToggle();            
        }
    });
});


/* SHOW/HIDE ADMIN PROFILE LINKS */
$(document).ready(function(){
    $('.admin-profile').on('click', function(){
        var dropDown = $(this).closest('.dropdown').find('.dropdown-menu');
        var parentLi  = $(this).closest('li');
        var caret = $(this).find('.fa-caret-down');

        $(dropDown).toggleClass('show');
        $(parentLi).toggleClass('show');
        $(caret).toggleClass('caret-up');

        $(this).attr('aria-expanded') == 'true' ? $(this).attr('aria-expanded', 'false') : $(this).attr('aria-expanded', 'true');        
    });
});


/*SHOW FILE NAME ON INPUT IF FILE IS SELECTED*/
$(document).ready(function(){
    $('.custom-file-input').on('change', function(){
        let filePath = $(this).val();

        let index = filePath.lastIndexOf('/');
        if(index == -1){
            index = filePath.lastIndexOf('\\');
        }

        let fileName = filePath.substring(index+1);

        $('.custom-file-label').html(fileName);
    });
});

/*UPLOAD AND SHOW NEWS MAIN IMAGE*/
$('#add-news-form input[type="file"]').on('change', () => {
    let formData = new FormData($('#add-news-form')[0]);

    $.ajax({
        url: 'http://anahata.test/admin/upload-preview-image',
        type: 'POST',
        data: formData,
        beforeSend(){
            // $(this).attr('disabled', 'disabled');
        },
        success(data) {
            let response = JSON.parse(data);

            if(response.hasOwnProperty('url')){
                let url  = response.url;

                let preview = "<img width='150' src='" + url + "'>";

                $('#news-photo-preview').html();
                $('#news-photo-preview').html(preview);

            }

        },
        error(){
            // console.log(error);
            //
            // let error = xhr.responseJSON.errors ? xhr.responseJSON.errors.photo["0"] : 'Помилка завантаження файлу';
            //
            // console.log(error);
            //
            // $('#photo-preview').html()
            // $('#photo-preview').html(error);
        },
        cache: false,
        contentType: false,
        processData: false
    });
});
