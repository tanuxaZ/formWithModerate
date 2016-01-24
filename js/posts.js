function validateRegisterForm(fieldObj){
    var validator = new formValidation();

    if (!validator.validateForm('form')) {
        return;
    }
    if (fieldObj == undefined) {
        $('form').submit();
    }
}

function delPost(id){
    if (confirm("Вы уверены, что хотите удалить запись") == true) {
        window.location.href = "/private_post/actionDelete/" + id;
    }
}


$(document).ready(function(){
    $('form input[name=save]').click(function(){
        validateRegisterForm();
    })

    var validator = new formValidation();
    validator.validateImg('fileImg', 'fileDisplayArea');
/*
    if($('form[action="/private_users/action_update"]  .success').size() > 0){
        alert('Данные сохранены успешно.');
    }*/
})