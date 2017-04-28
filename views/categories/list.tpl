<header>
	
	<button><i class="fa fa-search"></i></button><input type="text" class="search" data-filter="begins" data-filtername="rewrite">

	<button style="float:right" role="createCategory"><i class="fa fa-plus"></i></button>
	</a>
</header>
<article>
	<ul class="customlist adv">
		<?php foreach ($list_item as $item) {?>
			<li class="item_line" data-id="{$item.id_category}" data-enable="{$item.enable}" data-rewrite="{$item.contents[$current_lang].rewrite}" title="{$item.contents[$current_lang].title}" data-controller="{$item.contents[$current_lang].controller}">
				<i class="fa fa-folder fa-4x"></i>
				<span>
						{$item.contents[$current_lang].name}
				</span>
			</li>
		<?php } ?>
	</ul>
	<section>

		
	</section>	
</article>