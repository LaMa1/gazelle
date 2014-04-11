<?
include_once(SERVER_ROOT . '/classes/class_text.php');

$Text = new TEXT;

show_header('Forward to Staff', 'bbcode,inbox,jquery');
?>
<div class="thin">
    <h2>Forward a <?= $MsgType ?> to Staff</h2>
	<div class="head">
		<?= ucwords($MsgType) ?> to be forwarded:
	</div>
	<div class="box vertical_space">
		<div class="body" >
			<?= $Text->full_format($FwdBody, true) ?>
		</div>
	</div>
	<div class="head">Add message</div>
	<form action="staffpm.php" method="post" id="messageform">
		<div class="box pad">
			<div id="preview" class="hidden"></div>
			<div id="quickpost">  
				<input type="hidden" name="action" value="takepost" />
				<input type="hidden" name="prependtitle" value="Staff PM - " />
				<input type="hidden" name="forwardbody" value="<?= $FwdBody ?>" />

				<label for="subject"><h3>Subject</h3></label>
				<input class="long" type="text" name="subject" id="subject" value="<?= display_str($Subject) ?>" />
				<br />

				<label for="message"><h3>Message</h3></label>
				<? $Text->display_bbcode_assistant("message") ?>
				<textarea rows="10" class="long" name="message" id="message"><?= display_str($Msg) ?></textarea>
			</div>
		</div>
		<div class="center">
			<strong>Send to:</strong>
			<select name="level">
				<option value="0" selected="selected">First Line Support</option>
				<option value="500">Mod Pervs</option>
				<option value="600">Admins</option>
			</select>
			<input type="button" id="previewbtn" value="Preview" onclick="Inbox_Preview();" /> 
			<input type="submit" value="Send forwarded message" />
		</div>
	</form>
</div>

<?
show_footer();
?>
