<?
global $_GTC;
?>
   <input type="hidden" id="chatSyncID" value="<?=$_GTC->syncID?>">
   <div id="chat_showAll" class="chat-showAll">
		<? if (($_GTC->limit != 0) && count($_GTC->messages) == 20) { ?>
	 		<a href="#" class="btn btn-link" onclick="chat_showAll();">Показать все...</a>
		<? } ?>
	 </div>
<? if (is_array($_GTC->messages)) foreach($_GTC->messages as $msg){ ?>
	<div class="chat-message" id="chatmessage_<?=$msg->id?>">
		<div class="chat-message-pan">
			<?=$msg->UserNickname?>
			<? if (intval($msg->access->delete)){?> <a  class="chat-message-message-del" href="#" onclick="messageDel($('#chatTripID').val(),<?=$msg->id?>);" title="удалить">удалить</a><? } ?>		
			<div class="chat-message-date"><?=$msg->MsgTimeFormat?></div>
		</div>
		<div class="chat-message-message"><?=$msg->Message?></div>		
	</div>
<? } ?>
<? if (count($_GTC->messages) == 0) { ?>
	<div class="chat-message chat-message-empty" id="chatmessage_<?=$msg->id?>">
		<div class="chat-message-message">
			<p class="lead">В чате ещё никто<br /> не общался</p>
		</div>		
	</div>
<? } ?>
