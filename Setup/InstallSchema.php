<?php

namespace Cap\Rma\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @throws Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_cap_rma_request = $setup->getConnection()->newTable($setup->getTable('cap_rma_request'));

        $table_cap_rma_request->addColumn(
            'request_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        );
        $table_cap_rma_request->addColumn(
            'increment_id',
            Table::TYPE_TEXT,
            32,
            [],
            'Increment ID'
        );
        $table_cap_rma_request->addColumn(
            'order_id',
            Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'Order ID'
        );
        $table_cap_rma_request->addColumn(
            'customer_name',
            Table::TYPE_TEXT,
            128,
            [],
            'Customer Name'
        );
        $table_cap_rma_request->addColumn(
            'customer_email',
            Table::TYPE_TEXT,
            128,
            [],
            'Customer Email'
        );
        $table_cap_rma_request->addColumn(
            'type',
            Table::TYPE_INTEGER,
            '2M',
            [],
            'Request Type'
        );
        $table_cap_rma_request->addColumn(
            'status',
            Table::TYPE_INTEGER,
            '2M',
            [],
            'Request Status'
        );
        $table_cap_rma_request->addColumn(
            'description',
            Table::TYPE_TEXT,
            null,
            [],
            'Request Description'
        );
        $table_cap_rma_request->addColumn(
            'comment',
            Table::TYPE_TEXT,
            null,
            [],
            'Request Comment'
        );
        $table_cap_rma_request->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'created_at'
        );
        $table_cap_rma_request->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'updated_at'
        );

        $setup->getConnection()->createTable($table_cap_rma_request);
        $setup->endSetup();
    }
}
