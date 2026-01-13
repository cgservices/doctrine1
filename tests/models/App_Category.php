<?php
class App_Category extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 32);
        $this->hasColumn('parent_id', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasMany('App', array(
            'local' => 'id',
            'foreign' => 'app_category_id'
        ));

        $this->hasMany('App_Category as Parent', array(
            'local' => 'parent_id',
            'foreign' => 'id'
        ));
    }
}
