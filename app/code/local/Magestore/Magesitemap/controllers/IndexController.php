<?php
class Magestore_Magesitemap_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){

		$categoryId = $this->getRequest()->getParam('catID');
		
		if($categoryId){
			$sitemap = new Magestore_Magesitemap_Block_Magesitemap();
			$sitemap->setCategory($categoryId);
			$sitemap->exportXml();
		
		}else{
			$jsTree = new Magestore_Magesitemap_Block_Tree();
            $jsTree->buildTree();  
			
		}
		
	}
}