//estmesto.net JS library

	//jquery Logout
	function emnQuit()
	{

		$.ajax({
   		type: "GET",
   		url: "/ajax/logout.php",
   		success: function(msg){

			window.location.reload();
   		}

		});		
	}

	function showProgress()
	{
		if (currUpdObject == null) return;

		currUpdObject.addClass('progress');

		return;
	}

	function hideProgress()
	{
		if (currUpdObject == null) return;

		currUpdObject.removeClass('progress');
		return;
	}


	function placeListener()
	{


		//изменение тайтла транспорта
		$(".transport-input-text").keypress(function(event) { 

		if (event.which == 13 )
		{	
			currUpdObject = $(this);

			transportSetProperty($(this).attr('trID'), 'ModelName', $(this).val(), 0);

			if (tripSyncF != 0)
			{
				//отменяем перерисовку по таймингу
				clearInterval(tripSyncF);
				tripSyncF = 0;
			}
			tripSyncF = setInterval('tripSync();', 15000);
		}

		});

		$(".transport-input-text").blur(function() { 
		currUpdObject = $(this);
		transportSetProperty($(this).attr('trID'), 'ModelName', $(this).val(), 1);

		if (tripSyncF != 0)
		{
			//отменяем перерисовку по таймингу
			clearInterval(tripSyncF);
			tripSyncF = 0;
		}

		tripSyncF = setInterval('tripSync();', 15000);

		});

	$(".transport-input-text").focus(function() { 
		//отменяем перерисовку по таймингу
			clearInterval(tripSyncF);
			tripSyncF=0;
		});




	$(".transport-place-text").keypress(function(event) { 


		if (event.which == 13 )
		{	//сохранить состояние места с перерисовкой транспорта
			currUpdObject = $(this);

			placeKeep($(this).attr('trID'), $(this).attr('plID'), $(this).val(), 0);

			if (tripSyncF != 0)
			{
				//отменяем перерисовку по таймингу
				clearInterval(tripSyncF);
				tripSyncF = 0;
			}
			tripSyncF = setInterval('tripSync();', 15000);
		}
		else
		{	//сохранить состояние места без перерисовки транспорта
			if ($(this).val() == '')
				placeKeep($(this).attr('trID'), $(this).attr('plID'), $(this).val(), 1);
		}

		});

	$(".transport-place-text").blur(function() { 
		//сохранить состояние места с перерисовкой транспорта
		currUpdObject = $(this);
		placeKeep($(this).attr('trID'), $(this).attr('plID'), $(this).val(), 0);

		if (tripSyncF != 0)
		{
			//отменяем перерисовку по таймингу
			clearInterval(tripSyncF);
			tripSyncF = 0;
		}

		tripSyncF = setInterval('tripSync();', 15000);

		});


	$(".transport-place-text").focus(function() { 
		//отменяем перерисовку по таймингу
			clearInterval(tripSyncF);
			tripSyncF=0;
		});

		uiListener();
	}


	//tripSync
	function tripSync()
	{
		var tripID = parseInt($('#tripID').val());
		var syncID = $('#syncID').val();
		var tripKey = $('#tripKey').val();

		$.ajax({
   		type: "GET",
   		url: "/"+tripKey+"/sync/tripID-"+tripID+"_syncID-"+syncID,
		dataType: "html",
   		success: function(msg){
			if (msg == 'nochange') return;
			//$('#debug').html(Date());
			$('#trip_'+tripID).html(msg);
			placeListener();

   		}

		});		

	}



	//keep place
	function placeKeep(transportID, placeID, PlaceText, SAVE_ONLY)
	{
		if (SAVE_ONLY != 1)
			showProgress();

		//alert("/trip/placeKeep/transportID-"+transportID+"_placeID-"+placeID);
		$.ajax({
   		type: "GET",
   		url: "/trip/placeKeep/transportID-"+transportID+"_placeID-"+placeID,
		dataType: "html",
		data: {"params[PlaceText]": PlaceText, "params[SAVE_ONLY]": SAVE_ONLY},
   		success: function(msg){
			if (msg != '')
			{
				$('#transport_'+transportID).html(msg);

				if (SAVE_ONLY != 1)
					tripStat(parseInt($('#tripID').val()));

				placeListener();
			}

			hideProgress();
			currUpdObject = null;
   		}

		});		
	}

	//unkeep place
	function placeUnKeep(transportID, placeID)
	{
		showProgress();
		$.ajax({
   		type: "GET",
   		url: "/trip/placeUnKeep/transportID-"+transportID+"_placeID-"+placeID,
		dataType: "html",
   		success: function(msg){
			$('#transport_'+transportID).html(msg);
            tripStat(tripID);
			placeListener();

			hideProgress();
			currUpdObject = null;
   		}

		});		
	}

	//get trip stat
	function tripStat(tripID)
	{
		var syncID = $('#syncID').val();
		var tripKey = $('#tripKey').val();

		$.ajax({
   		type: "GET",
   		url: "/trip/stat/tripID-"+tripID+"_syncID-"+syncID,
		dataType: "html",
   		success: function(msg){
			if (msg == 'nochange') return;
			$('#trip_stat').html(msg);

   		}

		});		

	}


	//unkeep place
	function placeSetText(transportID, placeID, placeText)
	{
		showProgress();
		$.ajax({
   		type: "POST",
   		url: "/trip/placeSetText/transportID-"+transportID+"_placeID-"+placeID,
		data: {"params[PlaceText]": placeText},
		dataType: "html",
   		success: function(msg){
			$('#transport_'+transportID).html(msg);

			hideProgress();
   		}

		});		
	}

	//add transport
	function transportAdd(tripID, typeID)
	{
		if (parseInt(typeID) == 0) 
		{
			alert('Некорректный тип транспорта');
			return;
		}

		showProgress();


		$.ajax({
   		type: "GET",
   		url: "/trip/transportAdd/tripID-"+tripID+"_typeID-"+typeID,
		dataType: "html",
   		success: function(msg){
			$('#trip_'+tripID).html(msg);
			tripStat(tripID);
			placeListener();

			hideProgress();
   		}

		});		
	}

	//del transport
	function transportDelete(tripID, transportID)
	{
		showProgress();
		$.ajax({
   		type: "GET",
   		url: "/trip/transportDel/ID-"+transportID,
		dataType: "html",
   		success: function(msg){
			$('#trip_'+tripID).html(msg);
            tripStat(tripID);
			hideProgress();
   		}

		});		
	}

	//set propery
	function transportSetProperty(ID, property, value, SAVE_ONLY)
	{
		showProgress();
		$.ajax({
   		type: "POST",
   		url: "/trip/transportSetProperty/ID-"+ID,
		data: {"property":property, "value":value},
		dataType: "html",
   		success: function(msg){
			if (SAVE_ONLY != 1)
			{
				$('#transport_'+ID).html(msg);
				placeListener();
			}

			hideProgress();
   		}

		});		
	}


