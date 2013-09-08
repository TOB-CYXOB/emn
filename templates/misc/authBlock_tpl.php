<? global $_GTC;?>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand logo" href="/"><?=$_GTC->SiteName?></a>
          <div class="nav-collapse collapse">

			<? if( strlen($_GTC->user->userKey)) { ?>
			   <ul class="nav pull-right">
					<? if (strlen($_GTC->user->avatar)){?>
					<li>
						<span class="navbar-text heder_panel-user_menu-avatar"><img class="img-rounded" title="<?=$_GTC->user->nickname?>" src="<?=$_GTC->user->avatar?>"></span>
					</li>
					<? } ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$_GTC->user->nickname?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
						  <li><a href="#" onclick="emnQuit();return false;">Выход</a></li>
						</ul>
					  </li>
				</ul>
			<? } else { ?>
	            <p class="navbar-text pull-right">
					<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
					<a href="/regme/" class="navbar-link">Вход по e-mail</a> 
					<a class="btn btn-success loginza" style="margin-top: -3px; margin-left: 5px;" href="http://loginza.ru/api/widget?token_url=<?=$_GTC->siteurl?>">Войти</a>
				</p>
			<? } ?>


			<? if( strlen($_GTC->user->userKey)) { ?>
				<ul class="nav">
				  <li><a href="/trip/my"><i class="icon-align-justify icon-white"></i> Мои трипы</a></li>
					<? if (intval($_GTC->user->admin)){ ?>
				  <li><a href="/trip/last"><i class="icon-align-justify icon-white"></i> Последние 100</a></li>
					<? } ?>
				  <li><a href="/trip/create"><i class="icon-map-marker icon-white"></i> Создать трип</a></li>
				</ul>
			<? } ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>