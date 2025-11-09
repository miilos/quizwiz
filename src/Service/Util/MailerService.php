<?php

namespace App\Service\Util;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    public function __construct(
        private string $apiUrl,
        private MailerInterface $mailer,
    ) {}

    public function sendAccountActivationLink(string $to, string $subject, string $activationToken): void
    {
        $emailText = "Welcome to QuizWiz! We're so glad to have you on board :) <br />
                        This is your account activation link:
                        <br /><br />
                        <a href='{$this->apiUrl}/api/account/activate/{$activationToken}' style='display: inline-block; background-color: cornflowerblue; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; font-size: 16px; font-family: Arial, sans-serif;'>
                            Activate account
                        </a>";

        $emailContent = str_replace('{{ TEXT }}', $emailText, $this->emailTemplate());

        $this->send(
            $to,
            $subject,
            $emailContent,
            "Welcome to QuizWiz! We're so glad to have you on board :) This is your account activation link: {$this->apiUrl}/api/account/activate/{$activationToken}"
        );
    }

    public function sendPasswordResetToken(string $to, string $subject, string $resetToken): void
    {
        $emailText = "Your password reset token is: <br />
                        <h2>{$resetToken}</h2>
                        This token is valid for <b>15 minutes</b>.";

        $emailContent = str_replace('{{ TEXT }}', $emailText, $this->emailTemplate());

        $this->send(
            $to,
            $subject,
            $emailContent,
            "Your password reset token is: {$resetToken}. This token is valid for 15 minutes."
        );
    }

    public function send(string $to, string $subject, string $html, string $altText): void
    {
        $email = (new TemplatedEmail())
            ->to($to)
            ->subject($subject)
            ->html($html)
            ->text($altText);

        $this->mailer->send($email);
    }

    private function emailTemplate(): string
    {
        return <<<HTML
                    <!DOCTYPE html>
                    <html>
                      <head>
                        <meta charset='UTF-8' />
                        <title>QuizWiz account activation</title>
                      </head>
                      <body style='margin:0; padding:0; background-color:#f5f5f5;'>
                        <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                          <tr>
                            <td align='center' style='padding: 20px 0;'>
                              <table width='600' cellpadding='0' cellspacing='0' border='0' style='background-color: #ffffff; border-radius: 10px; overflow: hidden;'>
                                <tr>
                                  <td style='background-color: cornflowerblue; padding: 20px; text-align: center;'>
                                    <h1 style='margin: 0; color: white; font-family: Arial, sans-serif; font-size: 24px;'>
                                      Welcome to QuizWiz!
                                    </h1>
                                  </td>
                                </tr>
                                <tr>
                                  <td style='padding: 20px; font-family: Arial, sans-serif; color: black; font-size: 16px; line-height: 1.5;'>
                                    <p style='margin: 20px 0;'>
                                      {{ TEXT }}
                                    </p>
                                  </td>
                                </tr>
                                <tr>
                                  <td style='padding: 0 20px 20px 20px; font-family: Arial, sans-serif;'>
                                    <p style='margin: 0; color: #999999; font-size: 14px;'>
                                      Best regards,<br />
                                      The QuizWiz Team
                                    </p>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </body>
                    </html>
                HTML;
    }
}
