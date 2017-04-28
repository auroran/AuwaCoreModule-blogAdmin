<header>
	<button role="saveTag" class="fa fa-save"></button>
</header>
<article>
	<section>
		<input type="hidden" name="controller" value="{$mwController}">
		<input type="hidden" name="id_tag" value="{$tag->id_tag}">
		<fieldset>
			<label>
				Nom
			</label>
			<div>
				{foreach $languages as $iso=>$lang}
				<input type="text" name="name_{$iso}" role="title" data-lang="{$iso}" value="{$tag->contents[$iso].name}">
				{/foreach}
			</div>
		</fieldset>
	</section>
</article>