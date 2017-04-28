<header>
	<button class="fa fa-save" role="saveArticle"></button>
	<button class="el el-info-circle" role="infoArticle" data-id="{$article.id_article}"></button>
</header>
<article>  	
	{foreach $languages as $iso=>$lang}
		<div data-lang="{$iso}" class="editor_container">
			<textarea class="ajaxeditor" data-content="{$contents[$iso].id_content}" name="article_{$article.id_article}_content_{$iso}">{$contents[$iso].html}</textarea>
		</div>
	{/foreach}
</article>