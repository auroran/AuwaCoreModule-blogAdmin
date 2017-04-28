<?php
namespace Auwa;
Auwa::loadPhp( 'Category', 'class');
/**
 * Controller LOGIN for MiddleWay Administration
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class CategoriesController extends CoreController{

	function loadCss(){ // from core/
		$this->addCss('categories');
	}
	function loadJs(){
		$this->addJs('categoryEditor');
	}

	public function action(){// fonction exécutées via POST

		// check if table exists
		if( DefaultModel::tableExists('Auwa\\Category') === false){
			DefaultModel::createTable('Auwa\\Category');
		}
		if( DefaultModel::tableExists('Auwa\\CategoryContent') === false){
			DefaultModel::createTable('Auwa\\CategoryContent');
		}
		
		$this->setTitle('Mes Categories');
		$id_category = (int)Tools::getValue('id_category') ;
		$category = new Category( $id_category);
		switch( $this->action){
			case 'infos':
				$contents  = $category->getContents();
				foreach ($this->getVar('languages') as $idl => $c) {
					if( !isset($contents[$idl]) ){
						$contents[$idl] = (new CategoryContent())->toArray();
					}
					if ( empty($contents[$idl]['rewrite']) ) 
						$contents[$idl]['rewrite'] =  !empty($contents[$idl]['title']) 
								? Tools::url_transform($contents[$idl]['title'])
								: '';
					if ( $category->id_category==null ) $contents[$idl]['controller'] = Tools::getValue('auwaController');
				}
				$this->setVar(array(
					'contents'	=> $contents,
					'category'	=> (array)$category,
				));

				$e = $this->displayContent('categories/infos');
				$this->setTitle( ($id_category!==0 ? 'Infos de la':'Création d\'une').' catégorie '.($id_category!==0 ? '#'.$id_category: '' ));
				break;
			case 'edit' :
				$this->setTitle( 'Édition de la catégorie '.($id_category!==0 ? '#'.$id_category : '' ));
				$contents  = $category->getContents();
				foreach ($contents as $idl => $c) {
					$contents[$idl]['html'] = str_replace( "{url:data}", str_replace(_ROOT_DIR_,'../',_DATA_DIR_ ) , $contents[$idl]['html']);
				}
				$this->setVar(array(
					'contents'	=> $contents,
					'category'	=> (array)$category,
				));
				$e = $this->displayContent('categories/edit');
				break;
			default:
				if( DefaultModel::tableExists('Auwa\\Category') === false){
					DefaultModel::createTable('Auwa\\Category');
				}
				$this->setVar('list_item',  Category::getCollectionAsArray());
				$e = $this->displayContent('categories/list');
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
		    		$c_obj = new CategoryContent( $id_content );
		    		$h = new Editor( $this->data[$iso_lang]['html'] );
	    			$c_obj -> html = Editor::replaceExpr( $h->getHtml() );
	    			$res = $c_obj->update();
		    		if (!$res || !is_array($res) || $res['id_content']==null) 
		    			$this->errors->addError('L\'enregistrement en "'+$lang['name']+ '" a échoué');
			    		$c_obj = new CategoryContent( $id_content );
			    		$r[$iso_lang] = $c_obj -> html;
		    	}
		    	if ( ! $this->errors->hasError() ){
		    		$this->setResponse(true, $r);
		    	} else {
		    		$this->setResponse(false, $this->errors->getErrorMsg() );
		    	}
				break;
			    case 'deleteCategory':
			    // delete a page and its contents
			    	if (!$this->data['id_category']){
			    		$this->setResponse(false, 'ID non renseigné');
			    		return;
			    	}
			    	$a = new Category($this->data['id_category']);
			    	$r = $a->remove();
			    	$this->setResponse($r, $r ? 'Categorie suppriméé' : 'Erreur durant la suppresion');
			    	break;
			    case 'buildCategory':
					$id=(int)$this->data['id_category'];
					$category = new Category($id);
					$category->enable = (int)$this->data['enable'];
					$category->controller = $this->data['controller'];
					$category->pagination = (int)$this->data['pagination'];
					$res = $category->update();
					if (!$res ) {
						$errors->addError('L\'enregistrement de la catégorie a échoué', 'danger');
						goto endCategoryBuild;
					}
					$id_category = $category->id_category;
		    		if ($category->id_category===null){
		    			$errors->addError('Erreur dans la récupération de la catégorie');
		    			goto endCategoryBuild;
		    		}
		    		$contents = $category->contents;
			    	foreach (Lang::getEnabledLanguages() as $iso_lang=>$lang) {
			    		$c_obj = new CategoryContent( isset($contents[ $iso_lang ]) ? $contents[ $iso_lang ]['id_content'] : false );
			    		$resC = false;
		    			$c_obj -> set( 
		    				$id_category, 
		    				$iso_lang, 
		    				$this->data['title_'.$iso_lang], 
		    				$this->data['name_'.$iso_lang], 
		    				$this->data['rewrite_'.$iso_lang], 
		    				$this->data['controller'],
		    				$this->data['description_'.$iso_lang]
		    			);
		    			$resC = $c_obj->update();
			    		if (! $resC  ) {
			    			$errors->addError('Contenu "'.$iso_lang.'" non sauvegardé');
			    		}
			    	}

					endCategoryBuild:
			    	if ( ! $errors->hasError() ){
			    		$this->setResponse(true, $category->toArray());
			    	} else {
			    		$this->setResponse(false, $errors->getErrorMsg() );
			    	}
			    	break;
		}

	}

}
?>