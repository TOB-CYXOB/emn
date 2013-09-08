<?
global $_GTC;
?>

<div id="mailreg">
<h2>Экспресс-регистрация</h2>
<p>Введите ваш Email, имя и мы вышлем письмо,содержащее ссылку на активацию аккаунта.</p>
<form name="regform" id="regform" method="post" action="/regme/">
Email <input type="text" size="40" name="params[email]" id="frm_email"><br />
Ваше имя, фамилия или ник <input type="text" size="60" name="params[nickname]" id="frm_nickname"><br />
<input type="button" id="frm_Submit" value="Зарегистрировать меня">
</form>
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
