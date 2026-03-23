<?php

namespace App\Service\Util;

use App\Entity\AccessLog;
use DateTimeImmutable;
use Detection\MobileDetect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

trait IPLoggerTrait
{
    public static function logAccess(Request $request, EntityManagerInterface $entityManager): void
    {
        $ip = $request->getClientIp();
        $detect = new MobileDetect();
        $deviceType = $detect->isMobile() ? 'mobile' : ($detect->isTablet() ? 'tablet' : 'desktop');

        $accessLog = (new AccessLog())
            ->setIp($ip)
            ->setDeviceType($deviceType)
            ->setCreatedAt(new DateTimeImmutable());

        $entityManager->persist($accessLog);
        $entityManager->flush();
    }
}
