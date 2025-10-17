<?php

namespace App\State\Main;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Main\ResetPasswordRequest;
use App\ApiResource\Main\ResetPasswordResponse;
use App\Repository\Main\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @implements ProcessorInterface<ResetPasswordRequest, ResetPasswordResponse>
 */
class ResetPasswordStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly MailerInterface $mailer,

    )
    {
    }

    /**
     * @param ResetPasswordRequest $data
     * @throws TransportExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ResetPasswordResponse
    {
        $user = $this->userRepository->findOneBy([
            'email' => $data->getEmail(),
        ]);

        // Do not reveal whether a user account was found or not.
        $errorMessage = "If an account matching your email exists, then an email was just sent that contains a link that you can use to reset your password.";

        if (!$user) {
            return new ResetPasswordResponse($errorMessage);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new ResetPasswordResponse($errorMessage);
        }

        $email = (new TemplatedEmail())
            ->from(new Address('mailer@your-domain.com', 'Acme Mail Bot'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $this->mailer->send($email);


        return new ResetPasswordResponse("An email has been sent to your registered email address.");
    }
}