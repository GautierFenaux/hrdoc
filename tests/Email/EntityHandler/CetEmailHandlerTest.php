<?php

namespace App\Tests\Email\EntityHandler;

use App\Email\EntityHandler\CetEmailHandler;
use App\Email\Dto\EmailData;
use App\Entity\Cet;
use App\Entity\User;
use App\Enum\StateEnum;
use PHPUnit\Framework\TestCase;

class CetEmailHandlerTest extends TestCase
{
    private CetEmailHandler $handler;
    private Cet $cet;
    private User $user;

    protected function setUp(): void
    {
        $this->handler = new CetEmailHandler();

        $this->user = new User();
        $this->user->setName('Gautier');
        $this->user->setSurname('FENAUX');
        $this->user->setEmail('gfenaux@hrdoc.com');

        $this->cet = new Cet();
        $this->cet->setUser($this->user);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->handler->supports($this->cet));
    }

    public function testGetManagerEmailData(): void
    {
        $url = 'http://example.com/manager';
        $emailData = $this->handler->getManagerEmailData($this->cet, $url);

        $this->assertInstanceOf(EmailData::class, $emailData);
        $this->assertEquals('gfenaux@hrdoc.com', $emailData->to);
        $this->assertEquals('Demande relative au compte épargne temps de Gautier FENAUX', $emailData->subject);
        $this->assertEquals('emails/cet/cet_email.html.twig', $emailData->template);
        $this->assertArrayHasKey('form', $emailData->context);
        $this->assertEquals($url, $emailData->context['target_url']);
    }

    public function testGetRhEmailDataWithRefusManager(): void
    {
        $this->cet->setState(StateEnum::REFUSED_MANAGER);

        $url = 'http://example.com/rh';
        $emailData = $this->handler->getRhEmailData($this->cet, $url);

        $this->assertEquals('Demande de CET de Gautier FENAUX refusée', $emailData->subject);
        $this->assertEquals('cet@hrdoc.fr', $emailData->to);
        $this->assertEquals('emails/cet/cet_email_to_rh.html.twig', $emailData->template);
    }

    public function testGetCollaboratorEmailDataDefault(): void
    {
        $this->cet->setState(StateEnum::VALITED);

        $emailData = $this->handler->getCollaboratorEmailData($this->cet, 'http://example.com', '/tmp/doc.pdf');

        $this->assertEquals('Demande de CET validée', $emailData->subject);
        $this->assertEquals('/tmp/doc.pdf', $emailData->attachment);
        $this->assertEquals('emails/cet/cet_email_from_rh.html.twig', $emailData->template);
    }

    public function testGetCollaboratorEmailDataReopen(): void
    {
        $this->cet->setState(StateEnum::REOPEN);

        $emailData = $this->handler->getCollaboratorEmailData($this->cet, 'http://example.com', null);

        $this->assertEquals('Réouverture de la demande CET de Gautier FENAUX', $emailData->subject);
    }

    public function testGetCollaboratorEmailDataRefusRh(): void
    {
        $this->cet->setState(StateEnum::REFUSED_HR);

        $emailData = $this->handler->getCollaboratorEmailData($this->cet, 'http://example.com', null);

        $this->assertEquals('Demande de CET de Gautier FENAUX refusée.', $emailData->subject);
        $this->assertEquals('gfenaux@hrdoc.com', $emailData->to);
    }
}
