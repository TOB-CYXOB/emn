<?
global $_GTC;
?>

	<script src="/ckeditor/ckeditor.js"></script>
    <script src="//api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU" type="text/javascript"></script>

	<script>
ymaps.ready(init);

function init() {
    // Данные о местоположении, определённом по IP
    var geolocation = ymaps.geolocation,
    // координаты
        coords = [geolocation.latitude, geolocation.longitude],
        myMap = new ymaps.Map('map', {
            center: coords,
            zoom: 12
        });

		tripPlacemark = new ymaps.Placemark(coords, {
                    hintContent: 'Переместите на точку сбора'
                }, {
                    draggable: true // Метку можно перетаскивать, зажав левую кнопку мыши.
                });

		tripPlacemark.events.add('drag', function (e) {
    		$('#tripCoords').val(tripPlacemark.geometry.getCoordinates()+'x'+myMap.getZoom());
		});

          // Обработка события, возникающего при щелчке
          // левой кнопкой мыши в любой точке карты.
           myMap.events.add('click', function (e) {
                    var coords = e.get('coordPosition');
    				$('#tripCoords').val([
                                coords[0].toPrecision(6),
                                coords[1].toPrecision(6)
                            ].join(',')+'x'+myMap.getZoom());

					tripPlacemark.geometry.setCoordinates(coords);
            });


    	myMap.geoObjects.add(tripPlacemark);

		myMap.controls
                // Кнопка изменения масштаба
                .add('zoomControl')
                // Список типов карты
                .add('typeSelector')
                // Стандартный набор кнопок
                .add('mapTools');
}
	</script>


<div class="page-header">
	<h1>Создание трипа <small>(мероприятия)</small></h1>
</div>

<form name="TripCreate" id="TripCreate" action="/trip/create/" method="POST">
  <div class="control-group">
    <label class="control-label" for="frm_Title">Куда?</label>
    <div class="controls">
		<input type="text" name="params[Title]" id="frm_Title" value="" size="60" placeholder="Название мероприятия"/>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="frm_Start">Когда?</label>
    <div class="controls">
		<input type="text" name="params[Start]" id="frm_Start" value="" placeholder="Дата и время отъезда" />
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputEmail">Описание</label>
    <div class="controls" style="width: 650px;">
		<textarea name="params[Description]" value="" cols="40" rows="10" class="ckeditor" id="editor1" /></textarea>
	</div>
  </div>

  <div class="control-group">
    <label class="control-label">Точка сбора</label> 
    <div class="btn controls" id="startPlace-show">Указать на карте</div>
    <div class="controls" style="display:none;" id="startPlace">    	
		<div id="map" style="width: 650px; height: 400px; background-color: #f2f2f2"></div>
		<input type="hidden" id="tripCoords" value="" name="params[Coords]" size="40" />
    	<span class="btn" style="margin-top: 3px;" id="startPlace-hide">Отменить</span>
	</div>

  </div>



<div class="form-actions">
	<input type="button" id="frm_Submit" class="btn btn-success" value="Создать трип">
</div>



</form>


<!--



-->

<script>
	$(document).ready(function(){

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
					$("#TripCreate").submit();
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
