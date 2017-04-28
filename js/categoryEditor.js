(function(){
	var id_cat_admin = 'blogAdmincategories';
	var _categories_contextmenu = {
		'.customlist li':{
			'infos':{
				class: 'fa fa-info',
				text: 'Éditer les infos',
				fn: function(o,e){
					id_category = o.attr('data-id');
					var data = {
						controller: 'categories',
						module: 'blogAdmin',
						id_category: id_category,
						action: 'infos'
					};
					editorInfo('info_category_'+id_category, data);
				}
			},
			'edit':{
				class: 'fa fa-pencil',
				text: 'Éditer la catégorie',
				fn: function(o,e){
					o.click();
				}
			},
			'delete' : {
				class: 'fa fa-remove',
				text: 'supprimer',
				fn : function(o,e){
					id_category = o.attr('data-id');
					if (!confirm('Voulez-vous vraiment supprimer cette catégorie ? ( id: '+id_category+')')) return;
					var callback={
						success: function(){
							Acui.notice('Catégorie supprimée', 'success');
							Acui('blogAdmincategories').refresh();
						}
					};
					Acui.callCore('deleteCategory', {id_category: id_category}, 'categories', 'blogAdmin', callback );
				}
			}
		}
	};
	
	// launch editor infomations editor
	var editorInfo = function(id_node, data){
		Acui.open(id_node, data, {node:'categoryInfos mergeHeader'} ,function(){
			// date picker
			$('input.dateUI').each(function(){
				$(this).datepicker( {'dateFormat': $(this).attr('date-format')} );
			});
			// save article informations editor
			Acui(id_node).setEvents('click', {
				'[role=saveCategoryInfos]': function(e){
					e.stopPropagation();
					var $form = $(this).parents('section');
					var current_lang =  Acui.$languageSelector.val();
					var id_node = $(this).parents('.categoryInfos.uiNode').attr('id');
					var id_category =  $form.find('input[name=id_category]').val();
					var infos = {
						id_category: id_category,
						title: {},
						name: {},
						rewrite: {},
						description: {},
						controller: $form.find('select[name=controller]').val(),
						pagination: parseInt($form.find('input[name=pagination]').val()),
						enable: parseInt($form.find('select[name=enable]').val())
					};
					$form.find('input[data-lang], textarea[data-lang]').each(function(){
						infos[$(this).attr('name')]= $(this).val();
					});
					Acui.callCore('buildCategory', infos, 'categories', 'blogAdmin', {
						success: function(r){
							Acui.notice('Catégorie '+( infos.id_category ? 'mise-à-jour' : 'enregistrée'), 'success');
							Acui('blogAdmincategories').refresh(function(){
								if (!infos.id_category){
									Acui(id_node).data.id_category = r.id_category;
									Acui(id_node).refresh();
								} 
							});
							
						}
					});	
				}
			})
		} );
	}
	Acui(id_cat_admin).ready(function(){
		this.contextMenu(_categories_contextmenu, true);
		this.setEvents('click', {
			'[role=createCategory]': function(){
				var data = {
					controller: 'categories',
					module: 'blogAdmin',
					auwaController: Acui.$controllerSelector.val(),
					action: 'infos'
				};
				editorInfo('create_category_'+Acui.createId(), data);
			},
			'.item_line': function(e){
				e.stopPropagation();
				var data = {
					controller: 'categories',
					module: 'blogAdmin',
					id_category: $(this).attr('data-id'),
					action: 'edit'
				};
				var id_editor = 'edit_category_'+$(this).attr('data-id');		
				// events when the editor is ready
				var ready = function(){
					auwa.launchEditor("#"+id_editor+' .ajaxeditor', id_editor);
					Acui(id_editor).setEvents('click', {
						'[role=infoCategory]' : function(){
							id_category = $(this).attr('data-id');
							var data = {
								controller: 'categories',
								module: 'blogAdmin',
								id_category: id_category,
								auwaController: Acui.$controllerSelector.val(),
								action: 'infos'
							};
							editorInfo('info_category_'+id_category, data);
						},
						'[role=saveCategory]' : function(){
							item = {};
							$('.categoryEditor article').find('textarea').each(function(){
								var iso_lang = $(this).parent().attr('data-lang');
								item[iso_lang] = {
									html: tinyMCE.get( $(this).attr('id') ).getContent(),
									id_content: $(this).attr('data-content')
								};
							})
							var callback = {
								success: function(r){
									Acui.notice('Contenu enregistré','success');
								}
							}
							Acui.callCore('setHtmlContent', item, 'categories', 'blogAdmin', callback);
						}
					})
				};
				// open the article editor
				Acui.open(id_editor, data, 'categoryEditor mergeHeader maximized', ready);
			}
		});
	});
	Acui(id_cat_admin).init();
})()