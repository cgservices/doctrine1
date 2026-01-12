<?php
class TestMovieUserBookmark extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('user_id', 'integer', null, array('primary' => true));
        $this->hasColumn('movie_id', 'integer', null, array('primary' => true));
    }
}

