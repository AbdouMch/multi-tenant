<?php

namespace App;

use App\DBAL\Type\EncryptedStringType;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot()
    {
        parent::boot();

        $dbSecretKey = $this->getContainer()->getParameter('db_secret_key');
        EncryptedStringType::setDbSecretKey($dbSecretKey);
    }
}
