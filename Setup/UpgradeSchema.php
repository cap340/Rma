<?php

namespace Cap\Rma\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $tableName = $setup->getTable('cap_rma_request');
            $fullTextIndex = ['increment_id', 'customer_name', 'customer_email'];

            $setup->getConnection()->addIndex(
                $tableName,
                $setup->getIdxName($tableName, $fullTextIndex, AdapterInterface::INDEX_TYPE_FULLTEXT),
                $fullTextIndex,
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }

        $setup->endSetup();
    }
}
