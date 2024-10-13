<?php

namespace Repository\Base\Interfaces;

interface AccessRecipientContextInterface
{
    public function getRecipient(): AccessRecipientInterface;
    public function hasAccess(string $scope, int $flag): bool;
    public function hasAccessByFagName(string $scope, string $flagName): bool;
}
