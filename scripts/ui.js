

	$(document).ready(function(){
		startPlace();		
	});






	function uiListener(){
		keepPlace();
		placeHeandlers();
		startPlace();
		transport_allocation();
		console.log("Start uiListener()");
	}
	

	/* Распределитель транспорта */
	function transport_allocation() {
		$('.trip-items').masonry({
		  itemSelector: '.transport-item-wrapper'
		});	
	}

	/* Показать точку сбора */
	function startPlace(){
		$("#startPlace-show").click(function(){
			$("#startPlace").slideDown();
			$(this).hide();
		})
		$("#startPlace-hide").click(function(){
			$("#startPlace").slideUp();
			$("#startPlace-show").show();
			$("#tripCoords").val('');
		})
	}	
	
	
	/* Занять место */
	function keepPlace(){
		$(".transport-place-add").click(function(){
			$(this).hide();
			var placeID = $(this).attr('plID');
			$('.transport-place-text[plID="'+placeID+'"]').show();
			$('.transport-place-text[plID="'+placeID+'"]').focus();
		})
	}	
	
	
	/* Наведение */
	function placeHeandlers () {
		$(".transport-place").hover(function(){
			var placeID = $(this).attr('plID');
			if($('.transport-place-add[plID="'+placeID+'"]').attr('plID')){
				$('.transport-place-text[plID="'+placeID+'"]').hide();
				$('.transport-place-add[plID="'+placeID+'"]').show();			
			}
		}, function(){
			var placeID = $(this).attr('plID');
			$('.transport-place-text[plID="'+placeID+'"]').show();
			$('.transport-place-add[plID="'+placeID+'"]').hide();			
		})
	}