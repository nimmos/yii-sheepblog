<?php

use yii\db\Migration;

/**
 * Handles adding userimage to table `user`.
 */
class m161010_184404_add_userimage_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('tbl_user', 'userimage', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('tbl_user', 'userimage');
    }
}
