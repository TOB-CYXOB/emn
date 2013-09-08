	//tripSync
	function chatSync(use_sync, all_mode)
	{

		var tripID = parseInt($('#tripID').val());
		var syncID = $('#chatSyncID').val();
		//var tripKey = $('#tripKey').val();
		var url = "/chat/get/TripID-"+tripID;

		if (use_sync == 1)
			url = "/chat/chatsync/TripID-"+tripID+"_syncID-"+syncID;

		if (all_mode == 1)
		{
			url += '_all-1';
//			alert(url);
		}


		$.ajax({
   		type: "GET",
   		url: url,
		dataType: "html",
   		success: function(msg){

			if (msg == 'nochange') 
			{
				dtext = $('#debug').val();
				if(dtext.Length > 512) dtext = '';
				dtext = "nochange\n"+dtext;
				$('#debug').val(dtext);

				return;
			}

			var scroll_flag = false;
			if ($('#chat').html() == '')
				scroll_flag = true;

			$('#chat').html(msg);
			if (scroll_flag)
				$(".nano").nanoScroller({ scroll: 'bottom' });

/*
				dtext = $('#debug').val();
				if(dtext.Length > 512) dtext = '';
				dtext = "reload\n"+dtext;
				$('#debug').val(dtext);
*/

   		}

		});		

	}


	function messageDel(tripID, msgID)
	{

		var url = "/chat/del/TripID-"+tripID+"_id-"+msgID;

		$.ajax({
   		type: "GET",
   		url: url,
		dataType: "html",
   		success: function(msg){
            $('#chatmessage_'+msgID).css('display','none');
			//$('#chat').html(msg);
			//$(".nano").nanoScroller({ scroll: 'bottom' });
   		}

		});		

	}
