<?php

use yii\db\Migration;

/**
 * Handles adding headerimage to table `post`.
 */
class m161002_135641_add_headerimage_column_to_post_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('tbl_post', 'headerimage', $this->string(50));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('tbl_post', 'headerimage');
    }
}
