<? global $_GTC;?>
	<div class="heder_panel">
		<div class="heder_panel-logo_name">
			<a href="/" class="heder_panel-logo_name-logo"><?=$_GTC->SiteName?></a>
			<? if (strlen($_GTC->PageName)) { ?>
			<span class="heder_panel-logo_name-septer">/</span>
			<span class="heder_panel-logo_name-name"><?=$_GTC->PageName?></span>
			<? } ?>

			<? if (strlen($_GTC->CurrentTripUrl)) { ?>
<!--
			 <a href="#" onclick="alert('<?=$_GTC->CurrentTripUrl?>'); return false;"> [>>> Пригласить <<<]</a>
-->
<div class='pluso pluso-theme-light pluso-small'><div class='pluso-more-container'><a class='pluso-more' href=''></a></div><a class='pluso-facebook'></a><a class='pluso-vkontakte'></a><a class='pluso-twitter'></a><a class='pluso-odnoklassniki'></a><a class='pluso-google'></a><a class='pluso-email'></a></div>
<script type='text/javascript'>if(!window.pluso){pluso={version:'0.9.1',url:'http://share.pluso.ru/'};h=document.getElementsByTagName('head')[0];l=document.createElement('link');l.href=pluso.url+'pluso.css';l.type='text/css';l.rel='stylesheet';s=document.createElement('script');s.src=pluso.url+'pluso.js';s.charset='UTF-8';h.appendChild(l);h.appendChild(s)}</script>
			<? } ?>
		</div>

		<div class="heder_panel-user_menu">
			<? if( strlen($_GTC->user->userKey)) { ?>
				<? if (strlen($_GTC->user->avatar)){?>
						<span class="heder_panel-user_menu-avatar"><img title="<?=$_GTC->user->nickname?>" src="<?=$_GTC->user->avatar?>"></span>
				<? } ?>
				<a href="/trip/my">Мои трипы</a> <a href="#" id="authBlockQuit" onclick="emnQuit();return false;">Выход</a>
			<? } else { ?>
				<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
				<?=$_GTC->user->nickname?>
				<a href="http://loginza.ru/api/widget?token_url=<?=$_GTC->siteurl?>" class="loginza">
					<img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Вход через loginza"/>
				</a>
				<a href="/regme/">вход по EMAIL</a> 
			<? } ?>
		</div>
	</div>