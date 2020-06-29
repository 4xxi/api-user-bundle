<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Sender;

use Fourxxi\ApiUserBundle\Model\EmailUserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PlainMailerSender implements MessageSenderInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $from;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        array $from,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->from = $from;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    public function sendConfirmationMessage(UserInterface $user, string $confirmationUrl): void
    {
        if (!$user instanceof EmailUserInterface) {
            return;
        }

        $subject = $this->translator->trans('messages.confirmation.subject', [], 'ApiUserBundle');
        $content = $this->translator->trans('messages.confirmation.content', [
            '%username%' => $user->getUsername(),
            '%confirmationUrl%' => $confirmationUrl,
        ], 'ApiUserBundle');

        $email = (new Email())
            ->from(new Address($this->from['email'], $this->from['name']))
            ->to(new Address($user->getEmail()))
            ->subject($subject)
            ->text($content)
        ;

        try {
            $this->mailer->send($email);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('[%s] Transport error: %s', __CLASS__, $exception->getMessage()));
        }
    }
}
