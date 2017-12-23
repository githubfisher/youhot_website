$(document).ready(function() {


    if ($.isFunction($.fn.validate)) {

        $('#album_form').validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                content: {
                    url: true,
                    //required: true
                }
            },
        });


        $('#general_validate').validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                formfield1: {
                    required: true
                },
                formfield2: {
                    required: true,
                    email: true
                },
                formfield3: {
                    required: true,
                    url: true
                },
                formfield4: {
                    required: true,
                    creditcardtypes: true
                },
                formfield5: {
                    number: true,
                    required: true,
                },
                formfield6: {
                    minlength: 3,
                    required: true,
                },
                formfield7: {
                    maxlength: 5,
                    required: true,
                },
                formfield8: {
                    maxlength: 5,
                    required: true,
                },
                formfield9: {
                    maxlength: 5,
                    required: true,
                    equalTo: "#formfield8"
                },
                formfield10: {
                    required: true,
                },
                formfield11: {
                    required: true,
                    alphanumeric: true,
                },

            },

            invalidHandler: function(event, validator) {
                //display error alert on form submit
            },

            errorPlacement: function(label, element) { // render error placement for each input type
                console.log(label);
                $('<span class="error"></span>').insertAfter(element).append(label)
                var parent = $(element).parent().parent('.form-group');
                parent.removeClass('has-success').addClass('has-error');
            },

            highlight: function(element) { // hightlight error inputs
                var parent = $(element).parent().parent('.form-group');
                parent.removeClass('has-success').addClass('has-error');
            },

            unhighlight: function(element) { // revert the change done by hightlight

            },

            success: function(label, element) {
                var parent = $(element).parent().parent('.form-group');
                parent.removeClass('has-error').addClass('has-success');
            },

            submitHandler: function(form) {

            }
        });








        //Form Wizard Validations
        var $validator = $("#commentForm").validate({
            rules: {
                txtFullName: {
                    required: true,
                    minlength: 3
                },
                txtEmail: {
                    required: true,
                    email: true,
                },
                txtPhone: {
                    number: true,
                    required: true,
                }
            },
            errorPlacement: function(label, element) {
                $('<span class="arrow"></span>').insertBefore(element);
                $('<span class="error"></span>').insertAfter(element).append(label)
            }
        });


    }



    if ($.isFunction($.fn.bootstrapWizard)) {

        $('#pills').bootstrapWizard({
            'tabClass': 'nav nav-pills',
            'debug': false,
            onShow: function(tab, navigation, index) {
                console.log('onShow');
            },
            onNext: function(tab, navigation, index) {
                console.log('onNext');
                if ($.isFunction($.fn.validate)) {
                    var $valid = $("#product_form").valid();
                    if (!$valid) {
                        $validator.focusInvalid();
                        return false;
                    } else {
                        PRODUCT.save();
                    }
                }
            },

            onTabShow: function(tab, navigation, index) {
                console.log('onTabShow');
                var $total = navigation.find('li').length;
                var $current = index + 1;
                var $percent = ($current / $total) * 100;
                $('#pills .progress-bar').css({
                    width: $percent + '%'
                });
            }
        });

        $('#pills .finish').click(function() {
            $('#pills').find("a[href*='tab1']").trigger('click');
        });







    }




});
