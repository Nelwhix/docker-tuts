<?php declare(strict_types = 1);

namespace Nelwhix\ContactForm\Handlers;

use Http\Request;
use Http\Response;
use PHPMailer\PHPMailer\PHPMailer;


class MailHandler
{
    public function __construct(private Request $request,  private Response $response)
    {
    }

    public function index() {
        $index = file_get_contents(__DIR__ . "/../../public/index.html");

        $this->response->setContent($index);
    }

    public function send() {
        $name = $this->request->getParameter("name");
        $email = $this->request->getParameter("email");

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->Port = 25;

        $mail->setFrom($email, $name);
        $mail->addAddress("nelsonisioma1@gmail.com");

        if ($mail->addReplyTo($email, $name)) {
            $mail->Subject = "Testing PHPMailer";
            $mail->isHTML(false);

            $mail->Body = "You are gonna be great young padawan";

            $mail->send();
        }

        $this->response->setContent("Email sent successfully");
    }
}