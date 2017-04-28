<?php
namespace Auwa;
Auwa::loadPhp( '
	Tag,
	Article,
	Category',
	'class');
/**
 * Controller Article for Auwa Administration Module
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class ArticlesController extends CoreController{

	function loadCss(){ // from core/
		$this->addCss('articles');
		$this->addCss('jquery.Jcrop.min',	_CORE_CSS_DIR_);
	}
	function loadJs(){
		$this->addJs('jquery.Jcrop.min',	_CORE_JS_DIR_);
		$this->addJs('articleEditor');
	}

	public function action(){// fonction exécutées via POST
		$this->setTitle('Mes Articles');
		$this->setVar('baseLink', '?controller=articles&module='.$this->module);
		switch( $this->action ){
			case 'nothing';
				AuwaAdminController::$simpleCtrl = true;
				return;
			case 'infos':
				$article = new Article( (int)Tools::getValue('id_article') );
				$contents  = $article->getContents();
				$this->setVar('associations', ArticleAssociation::getFromArticle($article->id_article, $article->controller) );
				foreach ($this->getVar('languages') as $idl => $c) {
					if( !isset($contents[$idl]) ){
						$contents[$idl] = (array)new ArticleContent();
					}
					if ( empty($contents[$idl]['rewrite']) ) 
						$contents[$idl]['rewrite'] =  !empty($contents[$idl]['title']) 
								? Tools::url_transform($contents[$idl]['title'])
								: '';
					if (empty($article->id_article)) $contents[$idl]['controller'] = Tools::getValue('auwaController');
				}
				$articleTags = array();
				foreach(Tag::dispatchData( TagAssociation::search( $article->id_article )) as $tag){
					$articleTags[$tag->id_tag] = $tag;
				}
				$this->setVar(array(
					'articleTags'=> $articleTags,
					'availableTags'=> Tag::getCollection(),
					'categories' =>Category::getAllCategories(),
					'publish_date'=> isset($article->publish_date) 
									?  date(Tools::getDateFormat(), strtotime($article->publish_date)) 
									: date(Tools::getDateFormat()) 
				));

				$this->setVar(array(
					'article'			=> (array)$article,
					'contents'			=> $contents,
					'jquery_dateformat'	=> Tools::getJqueryDateFormat(),
				));
				$this->setTitle( 'Infos de l\'article '.(isset($contents[_CURRENT_LANG_]) ? $contents[_CURRENT_LANG_]['rewrite']:'') );
				$e = $this->displayContent('articles/infos');
				break;
			case 'edit' :
				$article = new Article( (int)Tools::getValue('id_article') );
				$contents  = $article->getContents();
				foreach ($this->getVar('languages') as $idl => $c) {
					if (empty($article->id_article)) $content[$idl]['controller'] = $this->getVar('currentSelectedController');
					$contents[$idl]['html'] = str_replace( "{url:data}", str_replace(_ROOT_DIR_,'../',_DATA_DIR_ ) , $contents[$idl]['html']);
				}
				$this->setTitle( 'Édition de l\'article '.(isset($contents[_CURRENT_LANG_]) ? $contents[_CURRENT_LANG_]['rewrite']:'') );
				$this->setVar(array(
					'article'			=> $article->toArray(),
					'contents'			=> $contents,
				));
				$contents  = $article->getContents();
				foreach ($contents as $idl => $c) {
					$contents[$idl]['html'] = str_replace( "{url:data}", str_replace(_ROOT_DIR_,'../',_DATA_DIR_ ) , $contents[$idl]['html']);
				}
				$this->setVar('contents',$contents );

				$e = $this->displayContent('articles/edit');
				break;
			case 'thumb':
				$this->setTitle( 'Miniature de l\'article '.(isset($contents[_CURRENT_LANG_]) ? $contents[_CURRENT_LANG_]['rewrite']:'') );
				$e = $this->displayContent('articles/thumbNav');
				break;

			case 'tag' :
				$this->setTitle( (Tools::getValue('id_tag')!=='false' ? 'Éditer':'Créer').' un tag');
				$this->setVar(array(
					'mwController'=> Tools::getValue('mwController'),
					'tag'=>new Tag( Tools::getValue('id_tag') ),
				));
				$e = $this->displayContent('articles/tag');
				break;
			default: 
				if( !ArticleContent::tableExists()){
					ArticleContent::createTable();
				}
				if( !ArticleContent::tableExists()){
					ArticleContent::createTable();
				}
				if( !Article::tableExists()){
					Article::createTable();
				}
				if( !TagContent::tableExists()){
					TagContent::createTable();
				}
				if( !TagAssociation::tableExists()){
					TagAssociation::createTable();
				}
				if( !Tag::tableExists()){
					Tag::createTable();
				}
				$list_item = Article::getAllArticles();
				//var_dump($list_item);die();
				$this->setVar('list_item',  $list_item);
				$this->setVar('categories', Category::getCollectionAsArray());
				$e = $this->displayContent('articles/list');
		}
	}

	public function query(){
		$errors = new Error();
		switch ($this->query) {
			case 'setHtmlContent':
				if (!$this->data) return;
				$r = array();
		    	foreach (Lang::getEnabledLanguages() as $iso_lang=>$lang) {
		    		$id_content = $this->data[$iso_lang]['id_content'];
		    		$c_obj = new ArticleContent( $id_content );
		    		$h = new Editor( $this->data[$iso_lang]['html'] );
	    			$c_obj -> html = Editor::replaceExpr( $h->getHtml(), true );
	    			$res = $c_obj->update();
		    		if (!$res || !is_array($res) || $res['id_content']==null) 
		    			$this->errors->addError('L\'enregistrement en "'+$lang['name']+ '" a échoué');
			    		$c_obj = new ArticleContent( $id_content );
			    		$r[$iso_lang] = $c_obj -> html;
		    	}
		    	if ( ! $this->errors->hasError() ){
		    		$this->setResponse(true, $r);
		    	} else {
		    		$this->setResponse(false, $this->errors->getErrorMsg() );
		    	}
				break;
			    case 'deleteArticle':
			    // delete a page and its contents
			    	if (!$this->data['id_article']){
			    		$this->setResponse(false, 'ID non renseigné');
			    		return;
			    	}
			    	$a = new Article($this->data['id_article']);
			    	$r = $a->remove();
			    	$this->setResponse($r, $r ? 'Article supprimé' : 'Erreur durant la suppresion');
			    	break;
			    case 'buildArticle':
					$id=(int)$this->data['id_article'];
					$article = new Article($id);
					if ($this->data['publish_date']){
						$date = \DateTime::createFromFormat( Tools::getDateFormat(), $this->data['publish_date']);
						if (!$date) {
							$errors->addError('Erreur avec la date de publication', 'danger');
							goto endArticleBuild;
						}
						$article->publish_date = $date->format('Y-m-d'); // convert to sql date format
					}
					if (empty($article->publish_date)) $article->publish_date = $article->insert_date;

					if (empty($article->id_user)) {
						$get = User::getMainConnection();
						$article->id_user = $get->id;
					}
					$article->controller = $this->data['controller'];
					$article->enable = (int)$this->data['enable'];
			    	$categories = isset($this->data['categories']) ? $this->data['categories'] : array();
			    	$tags = isset($this->data['tags']) ? $this->data['tags'] : array();

					$res = $article->update();
					if (!$res ) {
						$errors->addError('L\'enregistrement de l\'article a échoué', 'danger');
						goto endArticleBuild;
					}
					$id_article = $article->id_article;
		    		if ($article->id_article===null){
		    			$errors->addError('Erreur dans la récupération de l\'article');
		    			goto endArticleBuild;
		    		}
					foreach (Tag::getCollection() as $key => $tag) {
						TagAssociation::associate($article->id_article, $tag->id_tag, !in_array($tag->id_tag, $tags) );
					}
					foreach (Category::getAllCategories() as $key => $cat) {
						ArticleAssociation::associate($article->id_article, $cat->id_category, $article->publish_date, $this->data['controller'], !in_array($cat->id_category, $categories) );
					}
		    		$contents = $article->contents;
			    	foreach (Lang::getEnabledLanguages() as $iso_lang=>$lang) {
						//Tag::setTags($this->data['tags_'.$iso_lang], $id_article, $iso_lang, $this->data['controller']);
			    		$c_obj = new ArticleContent( isset($contents[ $iso_lang ]) ? $contents[ $iso_lang ]['id_content'] : false );
			    		$resC = false;
		    			$c_obj -> set( 
		    				$id_article, 
		    				$iso_lang, 
		    				$this->data['title_'.$iso_lang], 
		    				$this->data['rewrite_'.$iso_lang], 
		    				$this->data['controller'],
		    				$this->data['description_'.$iso_lang]
		    			);
		    			$resC = $c_obj->update();
			    		if (! $resC  ) {
			    			$errors->addError('Contenu "'.$iso_lang.'" non sauvegardé');
			    		}
			    	}

					endArticleBuild:
			    	if ( ! $errors->hasError() ){
			    		$this->setResponse(true, $article->toArray());
			    	} else {
			    		$this->setResponse(false, $errors->getErrorMsg() );
			    	}
			    	break;
			    case 'saveTag';
			    	$newTag = empty($this->data['id_tag']) || $this->data['id_tag'] =='false';
			    	$t = new Tag( !$newTag ? $this->data['id_tag'] : false );
			    	$t->controller = $this->data['controller'];
			    	$r = $t->update();
			    	if ($newTag) $t->contents = array();
			    	//$t = Tag::getLastInserted();
			    	foreach (Lang::getEnabledLanguages() as $iso_lang=>$lang) {
				    	$c = new TagContent( !$newTag ? $t->contents[$iso_lang]['id_tag_content'] : false );
				    	$c->id_tag = $t->id_tag;
				    	$c->controller = $this->data['controller'];
				    	$c->iso_lang = $iso_lang;
				    	$c->name = $this->data['name_'.$iso_lang];
				    	$c->rewrite = Tools::url_transform($this->data['name_'.$iso_lang]);
				    	$c->update();
				    	$t->contents[$iso_lang] = $c->toArray();
				    }
			    	$this->setResponse($r, $r ? $t->toArray() : 'Erreur durant la sauvegarde du tag');
			    	break;

			    case 'removeTag';
			    	$t = new Tag( $this->data['id_tag'] );
			    	$r = $t->remove();
			    	$this->setResponse($r, $r ? $t->toArray() : 'Erreur durant la création de tag');
			    	break;
		}

	}

}
?>