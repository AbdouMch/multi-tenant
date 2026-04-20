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
        private readonly UserRepository               $userRepository,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly MailerInterface              $mailer,
        private readonly string                       $mailerFromAddress,
        private readonly string                       $mailerFromName,
    ) {
    }

    /**
     * @param ResetPasswordRequest $data
     * @throws TransportExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ResetPasswordResponse
    {
        // Generic message — never reveal whether an account exists.
        $genericMessage = 'If an account matching your email exists, a reset link has been sent.';

        $user = $this->userRepository->findOneBy(['email' => $data->email]);

        if (!$user) {
            return new ResetPasswordResponse($genericMessage);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface) {
            return new ResetPasswordResponse($genericMessage);
        }

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFromAddress, $this->mailerFromName))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context(['resetToken' => $resetToken])
        ;

        $this->mailer->send($email);

        return new ResetPasswordResponse($genericMessage);
    }
}
