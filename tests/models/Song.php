<?php
class Song extends Doctrine_Record
{
    public function setUp(): void
    {
        $this->hasOne('Album', array('local' => 'album_id',
                                     'foreign' => 'id',
                                     'onDelete' => 'CASCADE'));
    }
    public function setTableDefinition(): void
    {
        $this->hasColumn('album_id', 'integer', 8);
        $this->hasColumn('genre', 'string',20);
        $this->hasColumn('title', 'string',30);
    }
}
