<header>
	<button role="saveArticleInfos" class="fa fa-save"></button>
</header>
<article>
	<section>
		<input type="hidden" name="controller" value="{$currentSelectedController}">
		<input type="hidden" name="id_article" value="{$article.id_article}">
		<fieldset>
			<label>
				Titre
			</label>
			<div>
				{foreach $languages as $iso=>$lang}
				<input type="text" name="title_{$iso}" role="title" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso].title}{/if}" style="display:none">
				{/foreach}

			</div>
		</fieldset>
		<fieldset>
			{foreach $languages as $iso=>$lang}
			<input type="hidden" name="init_rewrite_{$iso}" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso].rewrite}{/if}">
			{/foreach}
			<label>
				Nom "web" simple
			</label>
			<div>
				{foreach $languages as $iso=>$lang}
				<input type="text" name="rewrite_{$iso}" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso].rewrite}{/if}" style="display:none">
				{/foreach}
			</div>
		</fieldset>
		<fieldset>
			<label>
				Description
			</label>
			<div>
				{foreach $languages as $iso=>$lang}
				<textarea name="description_{$iso}" data-lang="{$iso}" style="height:100px;max-height:100px;display:none">{if isset($contents[$iso])}{$contents[$iso].description}{/if}</textarea>
				{/foreach}

			</div>
		</fieldset>
		<fieldset>
			<label>Publié
			</label>
			<div>
				<select name="enable" class="btn-switch">
					<option value="1"{if $article.enable} selected="selected"{/if}>Oui</option>
					<option value="0"{if !$article.enable} selected="selected"{/if}>Non</option>
				</select>
			</div>
		</fieldset>
		<fieldset>
			<label>Contrôleur
			</label>
			<div>
				<select name="controller">
				{foreach $controllers as $ctrl}
					<option value="{$ctrl}"{if $ctrl==$contents[_CURRENT_LANG_].controller} selected="selected"{/if}>{$ctrl}</option>
				{/foreach}
				</select>
			</div>
		</fieldset>
		<fieldset>
				<label>Catégorie(s) associée(s)</label>
				<div>
					{foreach $categories as $cat}
					{@_id = Tools::random(4)}
					<input name="category" id="{$_id}" type="checkbox" value="{$cat->id_category}"{if in_array($cat->id_category, $associations)} checked="checked"}{/if} data-controller="{$cat->contents[$current_lang].controller}"><label  data-controller="{$cat->contents[$current_lang].controller}" for="{$_id}">{$cat->contents[$current_lang].name}</label>
					{/foreach}
				</div>
			</fieldset>
			<fieldset>
				<label>Date de publication </label>
				<div>
					<input type="text" class="dateUI" name="publish_date" date-format="{$jquery_dateformat}" value="{$publish_date}">
				</div>
			</fieldset>
			<fieldset>
				<label>
					Tags
				</label>
				<div>
					<ul class="articleTags tags">
					{foreach $articleTags as $tag}
						<li class="btn btn-tag" data-id="{$tag->id_tag}" data-controller="{$tag->controller}">
							{foreach $languages as $iso=>$lang}
							<span data-lang="{$iso}">{if isset($tag->contents[$iso])}{$tag->contents[$iso].name}{/if}</span>
							{/foreach}
						</li>
					{/foreach}
					</ul>
					<br>
					<ul class="availableTags tags">
					{foreach $availableTags as $tag}
						{if !array_key_exists($tag->id_tag, $articleTags)}
						<li class="btn btn-tag" data-id="{$tag->id_tag}" data-controller="{$tag->controller}">
							{foreach $languages as $iso=>$lang}
							<span data-lang="{$iso}">{if isset($tag->contents[$iso])}{$tag->contents[$iso].name}{/if}</span>
							{/foreach}
						</li>
						{/if}
					{/foreach}
						<li class="btn btn-info fa fa-plus addTag"></li>
					</ul>
					<br>
					<i style="font-size:.7em">Cliquez droit sur les tags pour les gérer.</i>
				</div>
			</fieldset>
			<br>
			<fieldset>
				<label>Miniature</label>
				<div>
					{@rfile = Tools::random(3)}
					{if is_file(_DATA_DIR_.'/pictures/thumbs/article_'.($article.id_article).'.jpg')}
					<img src="{url}data/pictures/thumbs/article_{$article.id_article}.jpg?v={$rfile}" width="100%" role="thumbmail" name="{$article.id_article}">
					{/if}
				</div>
			</fieldset>
	</section>
</article>