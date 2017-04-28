<header>
	<button class="fa fa-save" role="saveCategory"></button>
	<button class="el el-info-circle" role="infoCategory" data-id="{$category.id_category}"></button>
</header>
<article> 
	<?php foreach ($languages as $iso=>$lang) { ?>
		<div data-lang="{$iso}" class="editor_container">
			<textarea class="ajaxeditor" data-content="{$contents[$iso].id_content}" name="category_{$category.id_category}_content_{$iso}">{$contents[$iso].html}</textarea>
		</div>
	<?php } ?>	
</article>