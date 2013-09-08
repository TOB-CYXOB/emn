<?
global $_GTC;
?>
   <input type="hidden" id="chatSyncID" value="<?=$_GTC->syncID?>">
<? if ($_GTC->limit != 0) { ?>
	 <div id="chat_showAll" class="chat-showAll"><a href="#" onclick="chat_showAll();">показать все ...</a></div>
<? } ?>
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