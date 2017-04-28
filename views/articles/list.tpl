<header>
	
	<button data-filter="published" data-value="1" title="Publié">
		<i class="fa fa-check"></i>
	</button>
	<button data-filter="published" data-value="0" title="Publié plus tard">
		<i class="fa fa-clock-o"></i>
	</button>	 
	<button data-filter="enable" data-value="1" title="Validé">
		<i class="fa fa-eye"></i>
	</button>	
	<button data-filter="enable" data-value="0" title="Brouillons">
		<i class="fa fa-eye-slash"></i>
	</button>	
	&nbsp;&nbsp;&nbsp;&nbsp;
	<button><i class="fa fa-search"></i></button><input type="text" class="search" data-filter="begins" data-filtername="rewrite">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<select class="list-filter" data-filter="contains" data-filtername="category" name="articleCategories">
		<option value="">Toute catégorie</option>
		{foreach $categories as $cat}
		<option value="{$cat.id_category}" data-controller="{$cat.content[$current_lang].controller}">{$cat.content[$current_lang].name}</option>
		{/foreach}
	</select>

	<button style="float:right" role="createArticle"><i class="fa fa-plus"></i></button>
	</a>
</header>
<article>
	<ul class="customlist adv">
		<?php foreach ($list_item as $item) {?>
			<li class="item_line" data-id="{$item.id_article}" data-controller="{$item.controller}" data-enable="{$item['enable']}" data-category="{fn:echo implode(' ',$item.categories)}" data-published="{if $item.isPublished}1{else}0{/if}" data-category="{" data-rewrite="{$item.contents[$current_lang].rewrite}" title="{$item.contents[$current_lang].title}" >
				<i class="fa fa-file fa-4x">
				</i>
				<span>						
						{$item.contents[$current_lang].rewrite}
				</span>
				</li>
			</li>
		<?php } ?>
	</ul>
	<section>

		
	</section>	
</article>