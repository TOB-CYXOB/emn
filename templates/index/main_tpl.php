<? global $_GTC;?>

    <style type="text/css">
      body {
        padding-bottom: 60px;
      }

      /* Custom container */
      .container {
        margin: 0 auto;
        max-width: 1000px;
      }
      .container > hr {
        margin: 60px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 80px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 100px;
        line-height: 1;
      }
      .jumbotron .lead {
        font-size: 24px;
        line-height: 1.25;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }


    </style>


      <!-- Jumbotron -->
      <div class="jumbotron">
        <h1>Есть место</h1>
        <p class="lead">Сервис организации групповых поездок</p>
		<? if ($_GTC->createButton == 1) {?>
			<a href="/trip/create/" class="btn btn-large btn-success">Создать трип</a>
		<? } else {?>
			<a href="http://loginza.ru/api/widget?token_url=<?=$_GTC->siteurl?>/trip/create/" class="btn btn-large btn-success loginza">Создать трип</a>
		<? } ?>
      </div>

      <hr>

      <!-- Example row of columns -->
      <div class="row-fluid">
        <div class="span12" style="text-align: center;">
          <h2>Создай трип →
          Добавь транспорт →
          Приглашай друзей</h2>
        </div>
      </div>


</center>