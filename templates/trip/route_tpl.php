<? global $_GTC;?>
<!-- trip stat -->
<?
				$t = clone($_GTC);
				echo $_GTC->__this->phptpl_include($t,$_GTC->__this->_tplDir().'tripstat_tpl.php');
?>
    <script src="//api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU" type="text/javascript"></script>

<? if(strlen($_GTC->Coords)) {?>

	<script>
ymaps.ready(init);

	<?
	$coords = explode('x', $_GTC->Coords);
	
	?>

function init() {
    // Данные о местоположении, определённом по IP
    // координаты
        coords = [<?=$coords[0]?>],
        myMap = new ymaps.Map('map', {
            center: coords,
            zoom: <?=$coords[1]?>
        });

		tripPlacemark = new ymaps.Placemark(coords, {
                    hintContent: 'Точка сбора'
                });

    	myMap.geoObjects.add(tripPlacemark);

		myMap.controls
                // Кнопка изменения масштаба
                .add('zoomControl')
                // Список типов карты
                .add('typeSelector')
                // Стандартный набор кнопок
                .add('mapTools');

	<? if ($_GTC->access->change) {?>
        coords = [<?=$coords[0]?>],
        myMap2 = new ymaps.Map('mapedit', {
            center: coords,
            zoom: <?=$coords[1]?>
        });

		tripPlacemark2 = new ymaps.Placemark(coords, {
                    hintContent: 'Переместите на точку сбора'
                }, {
                    draggable: true // Метку можно перетаскивать, зажав левую кнопку мыши.
                });

		tripPlacemark2.events.add('drag', function (e) {
    		$('#tripCoords').val(tripPlacemark2.geometry.getCoordinates()+'x'+myMap2.getZoom());
		});

          // Обработка события, возникающего при щелчке
          // левой кнопкой мыши в любой точке карты.
           myMap2.events.add('click', function (e) {
                    var coords = e.get('coordPosition');
    				$('#tripCoords').val([
                                coords[0].toPrecision(6),
                                coords[1].toPrecision(6)
                            ].join(',')+'x'+myMap2.getZoom());

					tripPlacemark2.geometry.setCoordinates(coords);
            });



    	myMap2.geoObjects.add(tripPlacemark2);

		myMap2.controls
                // Кнопка изменения масштаба
                .add('zoomControl')
                // Список типов карты
                .add('typeSelector')
                // Стандартный набор кнопок
                .add('mapTools');

	<?}?>


}
	</script>
<? } else if ($_GTC->access->change) { ?>
	<script>
	ymaps.ready(init);

	function init() 
	{

    // Данные о местоположении, определённом по IP
    var geolocation = ymaps.geolocation,
    // координаты
        coords = [geolocation.latitude, geolocation.longitude],
        myMap2 = new ymaps.Map('mapedit', {
            center: coords,
            zoom: 12
        });

		tripPlacemark = new ymaps.Placemark(coords, {
                    hintContent: 'Переместите на точку сбора'
                }, {
                    draggable: true // Метку можно перетаскивать, зажав левую кнопку мыши.
                });

		tripPlacemark.events.add('drag', function (e) {
    		$('#tripCoords').val(tripPlacemark.geometry.getCoordinates()+'x'+myMap2.getZoom());
		});

    	myMap2.geoObjects.add(tripPlacemark);

		myMap2.controls
                // Кнопка изменения масштаба
                .add('zoomControl')
                // Список типов карты
                .add('typeSelector')
                // Стандартный набор кнопок
                .add('mapTools');
     }
	</script>
<? } ?>

<div class="content_wrapper" id="route_block">

	<input type="hidden" id="syncID" value="<?=$_GTC->syncID?>">
	<input type="hidden" id="tripID" value="<?=$_GTC->ID?>">
	<input type="hidden" id="tripKey" value="<?=$_GTC->Key?>">

<? if ($_GTC->access->change) {?>
	<script src="/ckeditor/ckeditor.js"></script>


<div id="trip-info-edit"><a href="#" id="tripEdit">Редактировать</a></div>
<? } ?>

<?=$_GTC->Description?>


<? if(strlen($_GTC->Coords)) {?>

<div id="map" style="width:400px; height:300px"></div>
<? } ?>
</div>

<? if ($_GTC->access->change) {?>

<div class="content_edit_wrapper" id="route_edit_block" style="display:none">
<form name="TripEdit" id="TripEdit" action="/trip/edit/" method="POST">
	<input type="hidden" name="params[tripKey]" value="<?=$_GTC->Key?>">

  <div class="control-group">
    <label class="control-label" for="frm_Title">Куда?</label>
    <div class="controls">
		<input type="text" name="params[Title]" id="frm_Title" value="<?=$_GTC->Title?>" size="60" placeholder="Название мероприятия"/>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="frm_Start">Когда?</label>
    <div class="controls">
		<input type="text" name="params[Start]" id="frm_Start" value="<?=date('d.m.Y H:i',strtotime($_GTC->Start))?>" placeholder="Дата и время отъезда" />
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputEmail">Описание</label>
    <div class="controls" style="width: 650px;">
		<textarea name="params[Description]"  cols="40" rows="10" class="ckeditor" id="editor1" /><?=htmlspecialchars($_GTC->Description)?></textarea>
	</div>
  </div>

  <div class="control-group">
    <label class="control-label">Точка сбора</label> 
    <div class="btn controls" id="startPlace-show">Указать на карте</div>
    <div class="controls" style="display:none;" id="startPlace">    	
		<div id="mapedit" style="width: 650px; height: 400px; background-color: #f2f2f2"></div>
		<input type="hidden" id="tripCoords" value="" name="params[Coords]" size="40" />
    	<span class="btn" style="margin-top: 3px;" id="startPlace-hide">Отменить</span>
	</div>

  </div>



<div class="form-actions">
	<input type="button" id="frm_Submit" class="btn btn-success" value="Сохранить">
	<input type="button" id="frm_Cancel" class="btn btn-cancel" value="Отмена">
</div>



</form>


<!--



-->

<script>
	$(document).ready(function(){

				$('#tripEdit').click(function(){
					$('#route_block').toggle(0);	
					$('#route_edit_block').toggle(0);	
				});

				$('#frm_Cancel').click(function(){

					$('#route_edit_block').toggle(0);	
					$('#route_block').toggle(0);	

				});


				$('#frm_Submit').click(function(){


					if ($('#frm_Title').val() == '')
					{
						alert('Не указано название.');
						return;
					}

					if ($('#frm_Start').val() == '')
					{
						alert('Не указано время старта/отправления');
						return;
					}


					// передаем опции в  ajaxSubmit
					$("#TripEdit").submit();
				});


$.datepicker.regional['ru'] = {
	closeText: 'Закрыть',
	prevText: '<Пред',
	nextText: 'След>',
	currentText: 'Сегодня',
	monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
	'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
	monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
	'Июл','Авг','Сен','Окт','Ноя','Дек'],
	dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
	dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
	dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
	weekHeader: 'Не',
	dateFormat: 'dd.mm.yy',
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['ru']);


$.timepicker.regional['ru'] = {
	timeOnlyTitle: 'Выберите время',
	timeText: 'Время',
	hourText: 'Часы',
	minuteText: 'Минуты',
	secondText: 'Секунды',
	millisecText: 'Миллисекунды',
	timezoneText: 'Часовой пояс',
	currentText: 'Сейчас',
	closeText: 'Закрыть',
	timeFormat: 'HH:mm',
	amNames: ['AM', 'A'],
	pmNames: ['PM', 'P'],
	isRTL: false
};
$.timepicker.setDefaults($.timepicker.regional['ru']);

$('#frm_Start').datetimepicker({
	dateFormat: 'dd.mm.yy',
	timeFormat: 'HH:mm',
	stepMinute: 5,
	});

}); // document ready

</script>


</div>
<? } ?>



