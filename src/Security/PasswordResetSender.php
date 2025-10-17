<?php

namespace App\Security;

use App\Entity\Main\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class PasswordResetSender
{
    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly MailerInterface $mailer,
    )
    {
    }

    /**
     * @throws ResetPasswordExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sendFirstTimeCreation(User $user): void
    {
        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        $email = (new TemplatedEmail())
            ->from(new Address('mailer@your-domain.com', 'Acme Mail Bot'))
            ->to((string) $user->getEmail())
            ->subject('Please change your password')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $this->mailer->send($email);
    }
}