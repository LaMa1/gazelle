<a id="tags"></a>
<div class="head">
	<strong>Tags</strong>
	<span style="float:right;margin-left:5px;"><a href="#" id="tagtoggle" onclick="TagBox_Toggle();
			return false;">(Hide)</a></span>
	<span style="float:right;font-size:0.8em;">
		<a href="tags.php" target="_blank">tags</a> | <a href="articles.php?topic=tag" target="_blank">rules</a>
	</span>
</div>
<div id="tag_container" class="box box_tags <?= (!empty($LoggedUser['ShowTagsHorizontally'])) ? 'box_tags_horizontal' : '' ?>">
	<div class="tag_header">
		<div>
			<input type="hidden" id="sort_groupid" value="<?= $GroupID ?>" />
			<span id="sort_uses" class="button_sort sort_select"><a onclick="Resort_Tags(<?= "$GroupID, 'uses'" ?>);" title="change sort order of tags to total uses">uses</a></span>
			<span id="sort_score" class="button_sort"><a onclick="Resort_Tags(<?= "$GroupID, 'score'" ?>);" title="change sort order of tags to total score">score</a></span>
			<span id="sort_az" class="button_sort"><a onclick="Resort_Tags(<?= "$GroupID, 'az'" ?>);" title="change sort order of tags to total az">az</a></span>
			<!--<span id="sort_added" class="button_sort"><a onclick="Resort_Tags(<?= "$GroupID, 'added'" ?>);" title="change sort order of tags to total added">date</a></span>-->
		</div>
		Please vote for tags based <a href="articles.php?topic=tag" target="_blank"><strong class="important_text">only</strong></a> on their appropriateness for this upload.
	</div>
	<div id="torrent_tags" class="tag_inner">
		<? if (count($Tags) == 0){?>Please add a tag for this torrent!<? } ?>
	</div>
<? if (empty($LoggedUser['DisableTagging']) && (check_perms('site_add_tag') || $IsUploader)){ ?>
		<div class="tag_add">
			<div id="messagebar" class="messagebar hidden"></div>
			<form id="form_addtag" action="" method="post" onsubmit="return false;">
				<input type="hidden" name="action" value="add_tag" />
				<input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
				<input type="hidden" name="groupid" value="<?= $GroupID ?>" />
				<input type="hidden" name="tagsort" value="<?= $tagsort ?>" />
				<input type="text" id="tagname" name="tagname" size="15" onkeydown="if (event.keyCode == 13) {
							Add_Tag();
							return false;
						}" />
				<input type="button" value="+" onclick="Add_Tag();
						return false;" />
			</form>
		</div>
<? } ?>
</div>