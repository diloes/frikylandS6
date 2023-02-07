<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $em;
    public function __construct (EntityManagerInterface $em)
    {
        $this->em = $em; // $em equivale al EntityManagerInterface
    }

    #[Route('/registration', name: 'userRegistration')]
    public function userRegistration(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User(); // instanciamos la clase User
        $registration_form = $this->createForm(UserType::class, $user); // creamos el formulario a partir de UserType, los datos se almacenarán en $user
        $registration_form->handleRequest($request); // manejar la petición
        
        if($registration_form->isSubmitted() && $registration_form->isValid()) // cuando click en botón y si está todo ok
        {   
            $plaintextPassword = $registration_form->get('password')->getData(); // obtenemos el contendio del campo password
            $hashedPassword = $passwordHasher->hashPassword( // accedemos al metodo hashPassword de la clase UserPasswordHasherInterface
                $user, // ??
                $plaintextPassword // text a hashear
            );
            $user->setPassword($hashedPassword); // seteamos la constraseña hasheada

            $user->setRoles(['ROLE_USER']); // todos los user tendrán este rol
            $this->em->persist($user); // persisitimos el user en la bbdd
            $this->em->flush(); // esta línea se encarga de escribir en la base de datos
            return $this->redirectToRoute('userRegistration'); // una vez finalizado todo, recargamos la ruta
        }

        return $this->render('user/index.html.twig', [
            'registration_form' => $registration_form->createView() // crea la vista del formulario en twig
        ]);
    }
}
