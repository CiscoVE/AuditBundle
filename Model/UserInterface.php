<?php

namespace CiscoSystems\AuditBundle\Model;

interface UserInterface
{
    public function getId();
    
//    public function getUsername();
    
    public function __toString();
}

