<?
global $_GTC;
?>

<div class="page-header">
  <h1>Вход по e-mail</h1>
</div>
   <div id="mailreg" style="width: 350px;">
	  <form name="regform" id="regform" method="post" action="/regme/">
		<p>Введите ваш e-mail, имя и мы вышлем письмо,содержащее ссылку на активацию аккаунта.</p>
        <input type="text" size="40" name="params[email]" id="frm_email" placeholder="Электронная почта">
		<input type="text" size="60" name="params[nickname]" id="frm_nickname" placeholder="Ваше имя или ник">
        <button id="frm_Submit"  class="btn btn-large btn-success" type="button">Зарегистрировать меня</button>
      </form>

    </div>





</div>

<script>
	$(document).ready(function(){

				$('#frm_Submit').click(function(){


					if ($('#frm_email').val() == '')
					{
						alert('Не указан email.');
						return;
					}

					if ($('#frm_nickname').val() == '')
					{
						alert('Не указано имя/никнейм');
						return;
					}

					var options = {
					target: "#mailreg",
//  					url: "/ajax/getpricelist.php",
//					resetForm :true,
/*  					success: function() 
					{
						$('#resume').css('display','none');
						$('#captcha_error').css('display','block');
  					},

  					error: function() 
					{
						//$('#resume').css('display','none');
						$('#captcha_error').css('display','block');
  					}
*/
					};

					// передаем опции в  ajaxSubmit
					$("#regform").ajaxSubmit(options);

				});
	});
</script>
