<?php
class QueryTest_User extends Doctrine_Record 
{   

    public function setTableDefinition(): void

    {        
        $this->hasColumn('username as username', 'string', 50,
                array('notnull'));
        $this->hasColumn('visibleRankId', 'integer', 8);
        $this->hasColumn('subscriptionId', 'integer', 8);
    }

    /**
     * Runtime definition of the relationships to other entities.
     */
    public function setUp(): void
    {
        $this->hasOne('QueryTest_Rank as visibleRank', array(
            'local' => 'visibleRankId', 'foreign' => 'id'
        ));
        
        $this->hasOne('QueryTest_Subscription', array(
            'local' => 'subscriptionId', 'foreign' => 'id'
        ));

        $this->hasMany('QueryTest_Rank as ranks', array(
            'local' => 'userId', 'foreign' => 'rankId', 'refClass' => 'QueryTest_UserRank'
        ));
    }
}
