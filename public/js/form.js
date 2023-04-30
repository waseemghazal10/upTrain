$(document).ready(function () {
    checkFormInputs();
    var form=  $("form.dd");

    form.submit(function (event) {

      event.preventDefault();
      $.ajax({
        type: "POST",
        url: "/authenticate",
        data: form.serialize(),
        dataType: "json",
        encode: true,
        success: function(result){
          window.location= "/header";
          console.log(result);
        },
        error:function(result){
          console.log(result);
          $(".error").html(result.responseJSON.msg);
          if(result.responseJSON.error === "email"){
            $("#email").addClass("border-red");
            $("#password").removeClass("border-red");
          }
          else if(result.responseJSON.error === "password"){
            $("#password").addClass("border-red");
            $("#email").removeClass("border-red");
          }
        }
      })
    });
    $('input').on('keyup', function() {
      checkFormInputs();
    });
  });
  function checkFormInputs(){
    if ($('#email').val() === '' || $('#password').val() === '') {
      $('#login').prop('disabled', true);
    }
    else {
      $('#login').prop('disabled', false);
    }
  }