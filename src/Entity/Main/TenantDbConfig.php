<?php

namespace App\Entity\Main;

use App\Repository\Main\TenantDbConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Hakam\MultiTenancyBundle\Enum\DriverTypeEnum;
use Hakam\MultiTenancyBundle\Services\TenantDbConfigurationInterface;
use Hakam\MultiTenancyBundle\Traits\TenantDbConfigTrait;

#[ORM\Entity(repositoryClass: TenantDbConfigRepository::class)]
class TenantDbConfig implements TenantDbConfigurationInterface
{
    use TenantDbConfigTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'encrypted_string', length: 255)]
    protected string $dbName;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    protected ?string $dbUserName = null;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    protected ?string $dbPassword = null;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    protected ?string $dbHost = null;

    #[ORM\Column(type: 'encrypted_string', length: 255, nullable: true, options: ["default" => null])]
    protected ?string $dbPort = null;


    public function getId(): ?int
    {
        return $this->id;
    }
}
