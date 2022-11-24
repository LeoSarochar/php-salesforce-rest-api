<?php

namespace lsarochar\Salesforce\Authentication;

interface AuthenticationInterface
{
    public function getAccessToken();

    public function getInstanceUrl();
}

?>
