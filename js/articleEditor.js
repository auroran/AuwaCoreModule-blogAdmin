(function(){
	if (Acui.debugMode) console.log('Article Manager loaded');
	$('#thumb_nav').click(function(){
		Acui.imgUpload( 'pictures/thumbs/', 'article_'+auwa.id_article );
	});
	var _articles_contextmenu = {
		'.customlist li':{
			'infos':{
				class: 'fa fa-info',
				text: 'Éditer les infos',
				fn: function(o,e){
					id_article = o.attr('data-id');
					var data = {
						controller: 'articles',
						module: 'blogAdmin',
						id_article: id_article,
						action: 'infos'
					};
					editorInfo('info_article_'+id_article, data);
				}
			},
			'edit':{
				class: 'fa fa-pencil',
				text: 'Éditer l\'article',
				fn: function(o,e){
					o.click();
				}
			},
			'thumb':{
				class: 'fa fa-image',
				text: 'Définir la miniature',
				fn: function(o,e){
					id_article = o.attr('data-id');
					thumbEditor('thumb_article_'+id_article, id_article);
				}
			},
			'delete' : {
				class: 'fa fa-remove',
				text: 'supprimer',
				fn : function(o,e){
					id_article = o.attr('data-id');
					if (!confirm('Voulez-vous vraiment supprimer cet article ? ( id: '+id_article+')')) return;
					var callback={
						success: function(){
							Acui.notice('Article supprimé', 'success');
							Acui('blogAdminarticles').refresh();
						}
					};
					Acui.callCore('deleteArticle', {id_article: id_article}, 'articles', 'blogAdmin', callback );
				}
			}
		}
	};

	// launch editor thumb editor
	var thumbEditor = function(id_node, id_article){
		Acui.imgUpload( 'pictures/thumbs/', 'article_'+ id_article, 'jpg' );
		Acui('Upload').$e.on('uploadDone', function(e, file){
			Acui.cropTools( file, 2.5, '' );
			Acui('thumbCrop').$e.on('cropDone', function(e, file){
				Acui(id_node, 'img[name='+id_article+']').attr('src',file);
			})
		})
	}
	$(document).on('change', '.articleInfos fieldset select[name=controller]',function(){
		var id = $(this).parents('.uiNode').attr('id');
		Acui(id,'fieldset [data-controller]').hide();
		Acui(id,'fieldset [data-controller='+$(this).val()+']').show();
	});
	
	// launch editor infomations editor
	var editorInfo = function(id_node, data){
		Acui.open(id_node, data, {node:'articleInfos mergeHeader'} ,function(){
			// date picker
			$('input.dateUI').each(function(){
				$(this).datepicker( {'dateFormat': $(this).attr('date-format')} );
			});
			Acui(id_node,'fieldset select[name=controller]').trigger('change');

			var _articleInfos_contextmenu = {
				'li.btn-tag':{
					'edit':{
						class: 'fa fa-edit',
						text: 'Éditer ce tag',
						fn: function($o,e){
							tagEditor($o.attr('data-id'));
						}
					},
					'remove':{
						class: 'fa fa-remove',
						text: 'Supprimer ce tag',
						fn: function($o,e){
							if( confirm( 'Supprimer ce tag ?\n\nCela le retirera de chaque article avec lequel il a été associé') ){
								var data = {
									'id_tag': $o.attr('data-id')
								};
								Acui.callCore('removeTag', data, 'articles', 'blogAdmin', {
									success: function(r){
										Acui.notice('Tag supprimé', 'success');
										$(document).find('.tags li[data-id='+data.id_tag+']').remove();
									}
								});	
							}
						}
					}
				},
				'[role=thumbmail]':{
					'thumb':{
						class: 'fa fa-image',
						text: 'Définir la miniature',
						fn: function(o,e){
							id_article = o.attr('name');
							thumbEditor(id_node, id_article);
						}
					},
				}
			}
			Acui(id_node).contextMenu(_articleInfos_contextmenu, true);
			var tagEditor = function(id_tag){// code for adding tag
				var data = {
					controller: 'articles',
					module: 'blogAdmin',
					action: 'tag',
					id_tag: id_tag ? id_tag : false,
					mwController : Acui(id_node, 'select[name=controller]').val()
				};
				id_tagNode = Acui.createId();
				Acui.open(id_tagNode, data, {node:'articleTag mergeHeader'} ,function(){
					Acui(id_tagNode).setEvents('click',{
						'[role=saveTag]': function(){
							var data = {
								'controller': Acui(id_tagNode, '[name=controller]').val(),
								'id_tag': Acui(id_tagNode, '[name=id_tag]').val()
							};
							Acui(id_tagNode,'input[data-lang]').each(function(){
								data[$(this).attr('name')]= $(this).val();
							});
							Acui.callCore('saveTag', data, 'articles', 'blogAdmin', {
								success: function(r){
									Acui.notice('Tag '+ data['name_'+Acui.$languageSelector.val()] +' enregistré', 'success');
									if (id_tag){
										$(document).find('.uiNode.articleInfos .btn-tag[data-id='+r.id_tag+'] span').each(function(){
											$(this).text( r.contents[ $(this).attr('data-lang') ]['name'] );
										});
									} else {
										$(document).find('.uiNode.articleInfos').each(function(){
											$t = $('<li>',{
												'class': 'btn btn-tag', 
												'data-id': r.id_tag,
												'data-controller' : r.controller
											});
											Acui.$languageSelector.find('option').each(function(){
												var iso = $(this).attr('value');
												$t.append( $('<span>', {'data-lang': iso}).text(r.contents[iso]['name']) );
											});
											$(this).find('.availableTags').prepend($t);
											Acui(id_node).contextMenu(_articleInfos_contextmenu, true);
											Acui(id_node,'fieldset select[name=controller]').trigger('change');
											Acui.applyLanguage();
										});
									}
								}
							});	
						} // end saveTag envent
					}) // end setEvents
				}); // end open function
			}

			Acui(id_node).setEvents('click',{
				'ul.tags li' : function(){
					if ($(this).hasClass('addTag')) {
						tagEditor();
						return;
					}
					var appendTo = $(this).parents('.tags').hasClass('articleTags') ? 'availableTags' : 'articleTags';
					Acui(id_node,'ul.'+appendTo).prepend( $(this).detach() );
				},
				'[role=saveArticleInfos]': function(e){
					// save article informations editor
					e.stopPropagation();
					var $form = $(this).parents('section');
					var current_lang =  Acui.$languageSelector.val();
					var id_node = $(this).parents('.articleInfos.uiNode').attr('id');
					var id_article =  $form.find('input[name=id_article]').val();
					var infos = {
						id_article: id_article,
						title: {},
						description: {},
						enable: parseInt($form.find('select[name=enable]').val()),
						controller: $form.find('select[name=controller]').val(),
						publish_date: $form.find('input[name=publish_date]').val(),
						tags: [],
						categories: []
					};
					$form.find('input[data-lang], textarea[data-lang]').each(function(){
						infos[$(this).attr('name')]= $(this).val();
					});
					//cat list
					$form.find('[name=category]:checked').each(function(){
						infos.categories.push( $(this).val() );
					});
					//tag list
					$form.find('ul.articleTags li[data-controller='+$form.find('select[name=controller]').val()+']').each(function(){
						infos.tags.push( $(this).attr('data-id') );
					});
					Acui.callCore('buildArticle', infos, 'articles', 'blogAdmin', {
						success: function(r){
							Acui.notice('Article '+( infos.id_article ? 'mis-à-jour' : 'enregistré'), 'success');
							Acui('blogAdminarticles').refresh(function(){
								if (!infos.id_article){
									Acui(id_node).data.id_article = r.id_article;
									Acui(id_node).refresh();
								} 
							});
						}
					});	
				}
			});
		} );
	}
	Acui('blogAdminarticles').ready(
		function(){
			// apply the contextual menu
			Acui('blogAdminarticles').contextMenu(_articles_contextmenu, true);
			var selectForCtrld = function(){
				Acui('blogAdminarticles', '[name=articleCategories]').val('');
				Acui('blogAdminarticles', '[name=articleCategories] option[data-controller]').hide()
				Acui('blogAdminarticles', '[name=articleCategories] option[data-controller='+Acui.$controllerSelector.val()+']').show();
			}
			Acui.$controllerSelector.on('change', function(){
				selectForCtrld();
			});
			selectForCtrld();
			// set action when user click on a article "file"
			Acui('blogAdminarticles').setEvents('click',{
				'[role=createArticle]': function(){
					var data = {
						controller: 'articles',
						module: 'blogAdmin',
						auwaController: Acui.$controllerSelector.val(),
						action: 'infos'
					};
					editorInfo('create_article_'+Acui.createId(), data);
				},
				'.item_line': function(e){
					e.stopPropagation();
					var data = {
						controller: 'articles',
						module: 'blogAdmin',
						id_article: $(this).attr('data-id'),
						action: 'edit'
					};
					var id_editor = 'edit_article_'+$(this).attr('data-id');

					// events when the editor is ready
					var ready = function(){
						auwa.launchEditor("#"+id_editor+' .ajaxeditor', id_editor);
						Acui(id_editor).setEvents('click', {
							'[role=infoArticle]' : function(){
								id_article = $(this).attr('data-id');
								var data = {
									controller: 'articles',
									module: 'blogAdmin',
									id_article: id_article,
									auwaController: Acui.$controllerSelector.val(),
									action: 'infos'
								};
								editorInfo('info_article_'+id_article, data);
							},
							'[role=saveArticle]' : function(){
								item = {};
								$('.articleEditor article').find('textarea').each(function(){
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
								Acui.callCore('setHtmlContent', item, 'articles', 'blogAdmin', callback);
							}
						})
					
					};
					// open the article editor
					Acui.open(id_editor, data, 'articleEditor maximized', ready);
				}
			});
		}
	)
	Acui('blogAdminarticles').init();

})();