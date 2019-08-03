<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * TestEmail controller.
 *
 */
class TestEmailController extends Controller
{
	public function createEmailAction()
    {
        $msgtest = \Swift_Message::newInstance()
                ->setSubject("Nouvelle demande de congé à valider")
                ->setFrom(array('demandes@ima.cnps.com'=>'IMA'))
                ->setTo('b.h.konan@gmail.com')
                ->setBody("test ok");

        $this->get('mailer')->send($msgValideur);
        $this->get('session')->getFlashBag()->add('Email-Success','Votre email de test envoyé avec succès');

        return $this->render('KbhGestionCongesBundle:Test:envoi-email.html.twig');
    } 
}