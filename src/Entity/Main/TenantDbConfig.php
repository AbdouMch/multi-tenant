<?php

namespace App\Entity\Main;

use App\Entity\Loggable\LogEntry;
use App\Repository\Main\TenantDbConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Hakam\MultiTenancyBundle\Services\TenantDbConfigurationInterface;
use Hakam\MultiTenancyBundle\Traits\TenantDbConfigTrait;

#[ORM\Entity(repositoryClass: TenantDbConfigRepository::class)]
#[Gedmo\Loggable(logEntryClass: LogEntry::class)]
class TenantDbConfig implements TenantDbConfigurationInterface
{
    use TenantDbConfigTrait;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'encrypted_string', length: 255)]
    #[Gedmo\Versioned]
    protected string $dbName;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    #[Gedmo\Versioned]
    protected ?string $dbUserName = null;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    #[Gedmo\Versioned]
    protected ?string $dbPassword = null;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    #[Gedmo\Versioned]
    protected ?string $dbHost = null;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    #[Gedmo\Versioned]
    protected ?string $dbPort = null;


    public function getId(): ?int
    {
        return $this->id;
    }
}
