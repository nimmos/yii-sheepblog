<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tbl_tag_assign`.
 * Has foreign keys to the tables:
 *
 * - `tbl_tags`
 * - `tbl_post`
 */
class m161024_143346_create_tbl_tag_assign_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'auto_increment=1 character set utf8 collate utf8_unicode_ci engine=InnoDB';
        }
        
        $this->createTable('tbl_tag_assign', [
            'tag_assing_id' => $this->primaryKey(),
            'tag_id' => $this->integer()->notNull(),
            'post_id' => $this->integer()->notNull(),
        ], $tableOptions);

        // creates index for column `tag_id`
        $this->createIndex(
            'idx-tbl_tag_assign-tag_id',
            'tbl_tag_assign',
            'tag_id'
        );

        // add foreign key for table `tbl_tags`
        $this->addForeignKey(
            'fk-tbl_tag_assign-tag_id',
            'tbl_tag_assign',
            'tag_id',
            'tbl_tags',
            'tag_id',
            'CASCADE'
        );

        // creates index for column `post_id`
        $this->createIndex(
            'idx-tbl_tag_assign-post_id',
            'tbl_tag_assign',
            'post_id'
        );

        // add foreign key for table `tbl_post`
        $this->addForeignKey(
            'fk-tbl_tag_assign-post_id',
            'tbl_tag_assign',
            'post_id',
            'tbl_post',
            'post_id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `tbl_tags`
        $this->dropForeignKey(
            'fk-tbl_tag_assign-tag_id',
            'tbl_tag_assign'
        );

        // drops index for column `tag_id`
        $this->dropIndex(
            'idx-tbl_tag_assign-tag_id',
            'tbl_tag_assign'
        );

        // drops foreign key for table `tbl_post`
        $this->dropForeignKey(
            'fk-tbl_tag_assign-post_id',
            'tbl_tag_assign'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-tbl_tag_assign-post_id',
            'tbl_tag_assign'
        );

        $this->dropTable('tbl_tag_assign');
    }
}
