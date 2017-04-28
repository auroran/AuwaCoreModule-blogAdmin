<article>
	<section>
		<fieldset>
			<label>Miniature</label>
			<div>
				{if isset($article['id_article'])}
				<a id="thumb_nav" class="btn fa fa-download"></a> <span class="alert alert-info">Automatiquement sauvegardée en cas de changement</span>
				<input type="hidden" name="thumb">
				<div id="thumb_preview" style="background-color: #ddd; background-size:cover; width: 100%; height: auto">
					{if $article.thumb_exists}
						<img src="{url}data/pictures/thumbs/article_{$article.id_article}.jpg" width="100%" height="auto"> 
					{else}
						<img src="{url}data/pictures/thumbs/none.jpg" width="100%" height="auto"> 
					{/if}
				</div>
				{else}
				<div class="alert alert-warning" style="width: 100%"> Vous devez enregistrer l'article pour créer sa miniature.</div>
				{/if}
			</div>
		</fieldset>
	</section>
</article>