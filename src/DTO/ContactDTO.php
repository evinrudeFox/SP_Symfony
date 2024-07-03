<?php
namespace App\DTO; 
use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank]
    #[Assert\Lenght (min: 2, max: 200)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotBlank]
    #[Assert\Lenght (min: 2, max: 200)]
    public string $message = '';
}
