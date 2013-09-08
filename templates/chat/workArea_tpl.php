<? global $_GTC; ?>

	<script language="javascript" type="text/javascript" src="/scripts/jquery.nanoscroller.0.6.9/jquery.nanoscroller.min.js"></script>
	<link rel="stylesheet" media="all" type="text/css"   href="/scripts/jquery.nanoscroller.0.6.9/nanoscroller.css" />


<script>

	function nanoScrollerTop()
	{
		$(".nano").nanoScroller({ scroll: 'top' });
	}

	function chat_showAll()
	{
		clearInterval(chatSyncF);
		chatSync(0,1);
		$('#chatMode').val(1);
		chatSyncF = setInterval('chatSync(1,1);', 5000);
        setTimeout('nanoScrollerTop();', 1000);

	}

	chatSyncF = 0;
	chatAllMode = 0;

	$(document).ready(function(){


	$(".nano").nanoScroller();

	chatSync(0,0);
	chatSyncF = setInterval('chatSync(1,0);', 5000);



	$('#chatTripID').val($('#tripID').val());




	$('#chatMessage').keypress(function(event){

					if (event.which != 13) return;

					var options = {
					target: "#chat",
//  					url: "/ajax/getpricelist.php",
//					beforeSubmit:  showRequest,
					resetForm :true,
  					success: function() 
					{
						$(".nano").nanoScroller({ scroll: 'bottom' });
  					}				


/*  					error: function() 
					{
						//$('#resume').css('display','none');
						$('#captcha_error').css('display','block');
  					}
*/
					};

					// передаем опции в  ajaxSubmit
					$("#chatForm").ajaxSubmit(options);

		
	});



// pre-submit callback 
function showRequest(formData, jqForm, options) { 
    // formData is an array; here we use $.param to convert it to a string to display it 
    // but the form plugin does this for you automatically when it submits the data 
    var queryString = $.param(formData); 
 
    // jqForm is a jQuery object encapsulating the form element.  To access the 
    // DOM element for the form do this: 
    // var formElement = jqForm[0]; 
 
    alert('About to submit: \n\n' + queryString); 
 
    // here we could return false to prevent the form from being submitted; 
    // returning anything other than false will allow the form submit to continue 
    return true; 
} 


	});
</script>

	<div class="chat_wrapper hidden-phone">
		<div class="chat-list-wrapper nano">
			<div class="content">
				<div id="chat"></div>
			</div>
		</div>		
		<?// if (false) { ?>
		<? if (strlen($_GTC->user->userKey)) { ?>
			<div id="chatFormDIV">
				<form id="chatForm" method="post" action="/chat/say/">
					<input id="chatMode" type="hidden" name="params[all]" value="0">
					<input id="chatTripID" type="hidden" name="params[TripID]" value="0">
					<textarea id="chatMessage" cols="40" rows="5" name="params[Message]"></textarea><br>
				</form>
			</div>
		<? } ?>
		<div style="display:none">
		debug console:<br>
		<textarea id="debug" cols="40" rows="7"></textarea>
		</div>
	</div>
