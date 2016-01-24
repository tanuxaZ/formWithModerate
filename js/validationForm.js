
var formValidation = function() {
    var max_img_size = 2000000; //байт
    var max_img_size_mb = max_img_size / 1000000; // М

    var img_dimantions = ["jpeg", "png", "jpg"];

    var validate_classes = {
        'clsStringFrom3': 'stringFrom3',
        'clsStringToo2000': 'stringTo2000'
    };

    var errors_messages = {
        'is_required': 'Поле обязательно для заполнения',
        'stringFrom3': 'Строка должна содержать от 3-ох символов',
        'stringTo2000': 'Строка должна содержать не больше 2000 символов'
    };

    this.validateForm = function(form_container) {
        var isError = false;

        var validateFields = $(form_container).find(':input[type!=hidden], textarea');

        $(form_container).find('div.error').remove();

        validateFields.each(function() {
            var thisObj = $(this);

            var is_required = thisObj.hasClass('ClsRequired');
            var validate_types = getValidateType(thisObj);
            var value = '';

            if (!is_required && !validate_types.length) {
                return;
            }

            switch (thisObj.prop('tagName')) {
                case 'INPUT' :
                    value = $.trim(thisObj.val());
                    break;
                case 'TEXTAREA':
                    value = $.trim(thisObj.val());
                    break;
            }
            if (!value && is_required) {
                thisObj.after('<div class="error">'+errors_messages['is_required']+'</div>');
                isError = true;
                return;
            }

            if (validate_types.length && value) {
                for(var ruleItem in validate_types) {
                    var validate_type = validate_types[ruleItem];

                    var res = rule(value, validate_type);
                    if (typeof(res) == 'object') {
                        if(res.responseText == 1){
                            res = true;
                        }
                        else{
                            res = false;
                        }
                    }
                    if (!res) {
                        thisObj.after('<div class="error">'+errors_messages[validate_type]+'</div>');
                        isError = true;
                    }
                }
            }
        });

        if (isError) {
            return false;
        }

        return true;
    }

    this.validateImg = function(img_id, result_container_id){
        $('form').on('change', '#'+img_id, function(){
            var fileInput = document.getElementById(img_id);
            var fileDisplayArea = document.getElementById(result_container_id);
            var file = fileInput.files[0];
            var file_size = file.size;
            var found = false;
            var errors = new Array();

            img_dimantions.forEach(function(extension) {
                if (file.type.match('image/'+extension)) {
                    found = true;
                }
            })
            if (!found) {
                errors[errors.length] = 'Разрешены только файлы с разширениями: '+ img_dimantions.join(', ');
            }

            if (file_size > max_img_size) {
                errors[errors.length] = 'Размер изображения не должен привышать '+ max_img_size_mb + ' М';
            }

            if (errors.length == 0) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    fileDisplayArea.innerHTML = "";

                    var img = new Image(200);
                    img.src = reader.result;

                    fileDisplayArea.appendChild(img);
                }

                reader.readAsDataURL(file);
            } else {
                var input = $("#"+img_id);
                input.replaceWith(input.val('').clone(true));
                fileDisplayArea.innerHTML = errors.join('<br>');
            }
        });
    }

    var getValidateType = function(el) {
        var rules = new Array();
        for (var i in validate_classes) {
            if (el.hasClass(i)) {
                rules.push(validate_classes[i]);
            }
        }
        return rules;
    }

    var rule = function(value, type) {
        if (type == 'stringFrom3') {
            var result = value.length > 3;
            return result;
        }

        if (type == 'stringTo2000') {
            var result = value.length < 2000;

            return result;
        }

        return true;
    }
}