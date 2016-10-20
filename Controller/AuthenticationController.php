<?php

namespace Yuido\ApiAuthBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Yuido\ApiAuthBundle\Form\EmailType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yuido\ApiAuthBundle\Form\PasswordType;
use Yuido\ApiAuthBundle\Form\ChangeUserPasswordType;

class AuthenticationController extends Controller {

    /**
     * @Route("/rpc/login", name="login")
     * @Method("POST")
     */
    public function loginAction(Request $request) {

        $username = $request->get('username');
        $password = $request->get('password');
        $loginManager = $this->get('yuido_api_auth.login_manager');
                

        return $loginManager->login($username, $password);
    }

    /**
     * @Route("/rpc/logout", name="logout")
     * @Method("POST")
     */
    public function logout(Request $request) {      
        $token = $request->get('token');
        $loginManager = $this->get('yuido_api_auth.login_manager');

        return $loginManager->logout($token);
    }

    /**
     * 
     * @Route("/rpc/get_forgot_password_token", name="get_forgot_password_token")
     * @Method("POST")
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getForgotPasswordTokenAction(Request $request) {

        $data = json_decode($request->getContent(), true);

        $email = new \Yuido\ApiAuthBundle\Entity\Email();

        $form = $this->createForm(new EmailType(), $email);

        $form->submit($data);


        if ($form->isValid()) {

            $email = $form->getData();
            $token = $this->generateToken($email->getEmail());
            $this->sendToken($email, $token);

            $response = new JsonResponse([
                'message' => 'Se le ha enviado un enlace para restablecer su dirección de correo'
            ]);

            return $response;
        }



        $response = new JsonResponse([
            'errors' => $this->getErrorMessages($form)
        ]);

        $response->setStatusCode(500);

        return $response;
    }

    /**
     * 
     * @param Request $request
     * @Route("/rpc/set_password/{token}", name="set_password")
     */
    public function setPasswordAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $userClass = $this->getParameter('yuido_api_auth.user_class');
        
        $user = $em
                ->getRepository($userClass)
                ->findOneBy(array(
            'forgotpasstoken' => $request->get('token')
        ));
                

        if (!$user instanceof $userClass || (new \DateTime()) > $user->getForgotpassTokenValidity()) {
            $response = new JsonResponse([
                'errors' => ['El token enviado no es válido']
            ]);
            $response->setStatusCode(500);

            return $response;
        }        
        
        $data = json_decode($request->getContent(), true);
        
        if($data['password']['first'] !== $data['password']['second']){
            $response = new JsonResponse([
                'errors' => ['Los passwords introducidos no son idénticos']
            ]);
            $response->setStatusCode(500);

            return $response;
        }

        $form = $this->createForm(PasswordType::class, $user);

        $form->submit($data);

        if ($form->isValid()) {
            $user = $form->getData();

            $userManager = $this->get('yuido_api_auth.user_manager');

            
            $user->setPlainPassword($user->getPassword());
            $user->setForgotpasstoken(null);
            $user->setForgotpassTokenValidity(null);

            $userManager->updateUser($user);
            $userManager->updatePassword($user);

            $response = new JsonResponse([
                'message' => 'password actualizado'
            ]);

            return $response;
        }

        $response = new JsonResponse([
            'errors' => $this->getErrorMessages($form)
        ]);
        $response->setStatusCode(500);

        return $response;
    }

    /**
     * Esta acción sirve para que un usuario cambie su propio password,
     * para lo cual tendrá que enviar en la request el password actual
     * y el nuevo password repetido
     * 
     * @Route("/rpc/set_user_password", name="set_user_password")
     * @Method("POST")
     */
    public function setUserPasswordAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();

        $userClass = $this->getParameter('yuido_api_auth.user_class');
        $user = $em
                ->getRepository($userClass)
                ->findOneBy(array(
            'token' => $request->get('token')
        ));
                

        if (!$user instanceof $userClass) {
            $response = new JsonResponse([
                'errors' => ['Ningún usuario asociado a ese token']
            ]);
            $response->setStatusCode(500);

            return $response;
        }        
        
        $data = json_decode($request->getContent(), true);
        
        if($data['password']['first'] !== $data['password']['second']){
            $response = new JsonResponse([
                'errors' => ['Los passwords introducidos no son idénticos']
            ]);
            $response->setStatusCode(500);

            return $response;
        }

        $encoder = $this->container->get('security.password_encoder');
        $encodedPass = $encoder->encodePassword($user, $request->get('oldPassword'));
        if($user->getPassword() != $encodedPass){
            $response = new JsonResponse([
                'errors' => ['Password erróneo']
            ]);
            $response->setStatusCode(500);

            return $response;
        }

        $form = $this->createForm(ChangeUserPasswordType::class, $user);

        $form->submit($data);

        if ($form->isValid()) {
            $user = $form->getData();

            $userManager = $this->get('yuido_api_auth.user_manager');

            
            $user->setPlainPassword($user->getPassword());

            $userManager->updateUser($user);
            $userManager->updatePassword($user);

            $response = new JsonResponse([
                'message' => 'password actualizado'
            ]);

            return $response;
        }

        $response = new JsonResponse([
            'errors' => $this->getErrorMessages($form)
        ]);
        $response->setStatusCode(500);

        return $response;
    }

    protected function generateToken($email) {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array(
            'email' => $email
        ));

        $token = bin2hex(random_bytes(48));

        $user->setForgotpasstoken($token);
        $user->setForgotpassTokenValidity((new \DateTime())->add(new \DateInterval('PT2H')));
        $em->persist($user);
        $em->flush();

        return $token;
    }

    protected function sendToken($email, $token) {

        $url = $this->getParameter('web_url') . '/#/dashboard/establecer_password?token=' . $token;

        $message = \Swift_Message::newInstance()
                ->setSubject('Reestablezca su contraseña: Servicio WiFi para negocios de Orange')
                ->setFrom('noreplay@fractalia.es')
                ->setTo($email->getEmail())
                ->setBody(
                $this->renderView(
                        'email_recupera_password.txt.twig', array(
                    'url' => $url,
                    'email' => $email
                        )
                ), 'text/plain'
                )

        ;

        $this->get('mailer')->send($message);
    }

    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

}
