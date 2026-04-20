<?php

namespace App\Entity\Loggable;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

#[ORM\Entity]
class LogEntry extends AbstractLogEntry
{
    #[ORM\Column(type: Types::JSON, nullable: true)]
    protected $data;
}