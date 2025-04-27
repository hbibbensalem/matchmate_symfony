<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use App\DependencyInjection\Compiler\StripeServicePass;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}