<?php

class Magestore_Magesitemap_Adminhtml_MagesitemapController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('magesitemap/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Tag Manager'), Mage::helper('adminhtml')->__('Tag Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('magesitemap/magesitemap')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('magesitemap_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('magesitemap/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('magesitemap/adminhtml_magesitemap_edit'))
				->_addLeft($this->getLayout()->createBlock('magesitemap/adminhtml_magesitemap_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magesitemap')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
	
			$model = Mage::getModel('magesitemap/magesitemap');	
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
            if($data['type_attribute'] !='attribute_product'){
				if($data['type_attribute'] == 'static_value'){
					$model->setStaticValue($data['static_value']);
					$model->setAttributeCode($data['type_attribute']);
				}else{
					$model->setAttributeCode($data['type_attribute']);
				}
			}			
			try {	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magesitemap')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magesitemap')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('magesitemap/magesitemap');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $magesitemapIds = $this->getRequest()->getParam('magesitemap');
        if(!is_array($magesitemapIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($magesitemapIds as $magesitemapId) {
                    $magesitemap = Mage::getModel('magesitemap/magesitemap')->load($magesitemapId);
                    $magesitemap->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($magesitemapIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $magesitemapIds = $this->getRequest()->getParam('magesitemap');
        if(!is_array($magesitemapIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($magesitemapIds as $magesitemapId) {
                    $magesitemap = Mage::getSingleton('magesitemap/magesitemap')
                        ->load($magesitemapId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($magesitemapIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'magesitemap.csv';
        $content    = $this->getLayout()->createBlock('magesitemap/adminhtml_magesitemap_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'magesitemap.xml';
        $content    = $this->getLayout()->createBlock('magesitemap/adminhtml_magesitemap_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	/***************start create Controller Sitemap**********************/
	protected function _initcreateAction() {
		$this->loadLayout()
			->_setActiveMenu('magesitemap/create')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Create Sitemap'), Mage::helper('adminhtml')->__('Create Sitemap'));
		
		return $this;
	}
	
	public function createAction(){
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('magesitemap/adminhtml_createsitemap'));
		$this->renderLayout();
			 
	}

	public function editsitemapAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('magesitemap/createsitemap')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('createsitemap_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('magesitemap/create');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Sitemap Manager'), Mage::helper('adminhtml')->__('Sitemap Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Sitemap News'), Mage::helper('adminhtml')->__('Sitemap News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('magesitemap/adminhtml_createsitemap_edit'))
				->_addLeft($this->getLayout()->createBlock('magesitemap/adminhtml_createsitemap_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magesitemap')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newsitemapAction() {
		$this->_forward('editsitemap');
	}	
}