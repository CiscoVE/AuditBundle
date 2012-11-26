<?php

namespace CiscoSystems\AuditBundle\Model;

interface ReferenceInterface
{
    public function getId();
    
    public function __toString();
}

