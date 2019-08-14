<?php

class Magestore_Magesitemap_Block_Adminhtml_Magesitemap_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('magesitemapGrid');
      $this->setDefaultSort('magesitemap_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('magesitemap/magesitemap')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('magesitemap_id', array(
          'header'    => Mage::helper('magesitemap')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'magesitemap_id',
      ));

      $this->addColumn('alias_name', array(
          'header'    => Mage::helper('magesitemap')->__('XmlTag Name'),
          'align'     =>'left',
          'index'     => 'alias_name',
      ));

      $this->addColumn('attribute_code', array(
          'header'    => Mage::helper('magesitemap')->__('Attribute Code'),
          'align'     =>'left',
          'index'     => 'attribute_code',
      ));


      $this->addColumn('status', array(
          'header'    => Mage::helper('magesitemap')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('magesitemap')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('magesitemap')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('magesitemap')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('magesitemap')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('magesitemap_id');
        $this->getMassactionBlock()->setFormFieldName('magesitemap');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('magesitemap')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('magesitemap')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('magesitemap/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('magesitemap')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('magesitemap')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}