<?php

namespace System\Contracts;

interface IValidator
{
    function validateLogin(array $fields): bool;
    function validateRegistration(array $fields): bool;
    function confirmPassword(string $password, string $passwordToConfirm): static;
    function hasErrors(): bool;
    function getErrors(): array;
}