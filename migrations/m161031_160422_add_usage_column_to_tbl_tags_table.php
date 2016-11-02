<?php

use yii\db\Migration;

/**
 * Handles adding usage to table `tbl_tags`.
 */
class m161031_160422_add_usage_column_to_tbl_tags_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('tbl_tags', 'usage', $this->integer()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('tbl_tags', 'usage');
    }
}
