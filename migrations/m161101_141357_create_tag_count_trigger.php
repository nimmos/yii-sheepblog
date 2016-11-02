<?php

use yii\db\Migration;

class m161101_141357_create_tag_count_trigger extends Migration
{
    public function up()
    {
        $this->execute('
            create trigger `tag_insert_trigger`
            after insert on `tbl_tag_assign`
            for each row
            begin
                update `tbl_tags` set `usage`=`usage`+1
                where tag_id=NEW.tag_id;
            end;
            /
            ');
        
        $this->execute('
            create trigger `tag_delete_trigger`
            after delete on `tbl_tag_assign`
            for each row
            begin
                update `tbl_tags` set `usage`=`usage`-1
                where tag_id=OLD.tag_id;
            end;
            /
            ');
    }

    public function down()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS `tag_insert_trigger`
            DROP TRIGGER IF EXISTS `tag_delete_trigger`
        ');
    }
}
