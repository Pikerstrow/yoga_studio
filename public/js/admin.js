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
    let photoInput = $('.news-edit-new-photo').find('input[type="file"]');
    let errorDiv = $('.news-edit-new-photo').find('.invalid-feedback');

    let formData = new FormData($('#add-news-form')[0]);

    $.ajax({
        url: 'http://anahata.test/admin/upload-preview-image',
        type: 'POST',
        data: formData,
        success(data) {
            let response = JSON.parse(data);

            if(response.hasOwnProperty('url')){
                let url  = response.url;

                let preview = "<img width='150' src='" + url + "'>";

                $('#news-photo-preview').html('');
                $('#news-photo-preview').html(preview);

                if($(photoInput).hasClass('is-invalid')){
                    $(photoInput).removeClass('is-invalid');
                    $(errorDiv).html('');
                }
            }

            if(response.hasOwnProperty('error')){
                let error  = response.error;

                $('#news-photo-preview').html('');

                $(photoInput).addClass('is-invalid');
                $(errorDiv).text(error);
            }
        },
        error(){
            // console.log(error);
        },
        cache: false,
        contentType: false,
        processData: false
    });
});


/*UPLOAD AND SHOW ADMIN AVATAR*/
$('#edit_profile input[type="file"]').on('change', () => {
    let avatarInput = $('.user-avatar-input-container').find('input[type="file"]');
    let errorDiv = $('.user-avatar-input-container').find('.invalid-feedback');

    let formData = new FormData($('#edit_profile')[0]);

    $.ajax({
        url: 'http://anahata.test/admin/profile/upload-avatar',
        type: 'POST',
        data: formData,
        success(data) {
            let response = JSON.parse(data);

            if(response.hasOwnProperty('url')){
                let url  = response.url;

                let preview = "<img width='150' src='" + url + "'>";

                $('#admin-avatar-preview').html('');
                $('#admin-avatar-preview').html(preview);

                if($(avatarInput).hasClass('is-invalid')){
                    $(avatarInput).removeClass('is-invalid');
                    $(errorDiv).html('');
                }
            }

            if(response.hasOwnProperty('error')){
                let error  = response.error;

                $('#admin-avatar-preview').html('');

                $(avatarInput).addClass('is-invalid');
                $(errorDiv).text(error);
            }

        },
        error(error){
            // console.log(error);
        },
        cache: false,
        contentType: false,
        processData: false
    });
});


/*SHOW MODAL ON DELETE PRODUCT*/
$('.admin-news-delete').click(function(e){
    e.preventDefault();
    let deleteButton = $(this);

    $('#myModal').modal('show');

    $('.modal_delete_link').on('click', function() {
        $(deleteButton).closest('.admin-delete-news-form').submit();
    });
});


/*CHECK CURRENT ADMIN PASSWORD FOR PASSWORD CHANGE PAGE IN ADMIN PANEL*/
$('#pass_change_from_container input[name="old_password"]').on("keyup", function(){

    $.post("http://anahata.test/admin/profile/get_current_password",
        {
            password_change: "initiated",
            pass : $('#pass_change_from_container input[name="old_password"]').val()
        },
        function(data, status){

            let oldPassInput = $('#current-pass-input-group input[name="old_password"]');
            let newPassInput = $('#pass_change_from_container').find('input[name="password"]');
            let passConfInput = $('#pass_change_from_container').find('input[name="password_confirm"]');
            let validFeedbackDiv = $('#current-pass-input-group').find("div.valid-feedback");
            let invalidFeedbackDiv = $('#current-pass-input-group').find('div.invalid-feedback');


            if(status == 'success' && data == 'Пароль введено вірно'){
                if($(oldPassInput).hasClass('is-invalid')){
                    $(oldPassInput).removeClass('is-invalid');
                }
                $(oldPassInput).addClass('is-valid');
                $(invalidFeedbackDiv).html('');
                $(validFeedbackDiv).html('Діючий пароль вірний. Можете задавати новий пароль.');
                $(newPassInput).removeAttr('disabled');
                $(passConfInput).removeAttr('disabled');
            } else {
                if($(oldPassInput).hasClass('is-valid')){
                    $(oldPassInput).removeClass('is-valid');
                }
                $(oldPassInput).addClass('is-invalid');
                $(validFeedbackDiv).html('');
                $(invalidFeedbackDiv).html(data);
                $(newPassInput).attr('disabled', 'disabled');
                $(passConfInput).attr('disabled', 'disabled');
            }
        });
});


/*CHECK IF NEW PASSWORD MATCHES PASSWORD CONFIRM FOR PASSWORD CHANGE PAGE IN ADMIN PANEL*/
$('#pass_change_from_container input[name="password_confirm"]').on('keyup', function(){
    let newPassword = $('#pass_change_from_container input[name="password"]').val();
    let passConfInput = $('#pass_change_from_container').find('input[name="password_confirm"]');

    if($(this).val() !== newPassword){
        if($(passConfInput).hasClass('is-valid')){
            $(passConfInput).removeClass('is-valid');
        }
        $(passConfInput).addClass('is-invalid');

        $(passConfInput).closest('.form-group').find('div.invalid-feedback').html('Паролі не співпадають');

        $('#pass_change_from_container .admin-sumb-button').attr('disabled', 'disabled');
    } else {
        $(passConfInput).closest('.form-group').find('.invalid-feedback').html('');
        if($(passConfInput).hasClass('is-invalid')){
            $(passConfInput).removeClass('is-invalid');
        }
        $('#pass_change_from_container .admin-sumb-button').removeAttr('disabled');
    }
});




