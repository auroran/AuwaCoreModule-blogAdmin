<header>
	<button role="saveCategoryInfos" class="fa fa-save"></button>
</header>
<article>

	<section>
		<input type="hidden" name="controller" value="{$currentSelectedController}">
		<input type="hidden" name="id_category" value="{$category.id_category}">
		<fieldset>
			<label>
				Titre
			</label>
			<div>
				<?php foreach ($languages as $iso=>$lang) { ?>

				<input type="text" name="title_{$iso}" role="title" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso].title}{/if}" style="display:none">
				<?php } ?>
			</div>
		</fieldset>
		<fieldset>
			<label>
				Nom
			</label>
			<div>
				<?php foreach ($languages as $iso=>$lang) { ?>
				<input type="text" name="name_{$iso}" role="name" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso].name}{/if}" style="display:none">
				<?php } ?>
			</div>
		</fieldset>
		<fieldset>
			<?php foreach ($languages as $iso=>$lang) { ?>
			<input type="hidden" name="init_rewrite_{$iso}" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso].rewrite}{/if}">
			<?php } ?>
			<label>
				Nom "web" simple
			</label>
			<div>
				<?php foreach ($languages as $iso=>$lang) { ?>

				<input type="text" name="rewrite_{$iso}" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso].rewrite}{/if}" style="display:none">
				<?php } ?>
			</div>
		</fieldset>
		<fieldset>
			<label>
				Description
			</label>
			<div>
				<?php foreach ($languages as $iso=>$lang) { ?>

				<textarea name="description_{$iso}" data-lang="{$iso}" style="height:100px;max-height:100px;display:none">{if isset($contents[$iso])}{$contents[$iso]['description']}{/if}</textarea>
				<?php } ?>

			</div>
		</fieldset>
		<fieldset>
			<label>Nombre d'article par page
			</label>
			<div>
				<input type="text" name="pagination" value="{$category.pagination}">
			</div>
		</fieldset>
		<fieldset>
			<label>Contrôleur
			</label>
			<div>
				<select name="controller">
				<?php foreach ($controllers as $ctrl) { ?>

					<option value="{$ctrl}"{if $ctrl==$contents[_CURRENT_LANG_].controller} selected="selected"{/if}>{$ctrl}</option>
				<?php } 
				?>
				</select>
			</div>
		</fieldset>
		<fieldset>
			<label>Publiée
			</label>
			<div>
				<select name="enable" class="btn-switch">
					<option value="1"{if $article['enable']} selected="selected"{/if}>Oui</option>
					<option value="0"{if !$article['enable']} selected="selected"{/if}>Non</option>
				</select>
			</div>
		</fieldset>
	</section>
</article>