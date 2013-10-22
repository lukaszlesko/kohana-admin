<?php

interface Admin_Model_AccessLog
{
    public function save($module, $action, $recordId, $adminId);
}