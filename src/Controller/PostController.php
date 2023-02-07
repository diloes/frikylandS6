<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Regex;

class PostController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'home')]    
    public function home(Request $request, SluggerInterface $slugger): Response
    {   
        /**
         * No hay necesidad de pasar los datos a la instancia de Post porque
         * iniciamos todos los datos con null. 
         */
        $post = new Post(); // Instanciamos la clase Post que es la entitty Post de la BBDD
        $posts = $this->em->getRepository(Post::class)->findAllPosts(); // obtenemos todos los post de la BBDD

        $form = $this->createForm(PostType::class, $post); // creamos el formulario a partir de Post
        $form->handleRequest($request); // Obtenemos la petición

        if($form->isSubmitted() && $form->isValid()) 
        {   
            $file = $form->get('file')->getData();
            if($file)
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension(); // cambia nombre archivo para seguridad
                try {
                    $file->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new Exception('Ups there is a problem with your file.');
                }
                $post->setFile($newFilename);
            }

            $url = str_replace(" ", "-", $form->get('title')->getData()); // reemplazamos los espacios por guiones del title
            $post->setUrl($url); // creamos la url automaticamente con lo de arriba
            $user = $this->em->getRepository(User::class)->find(1); // obtenemos al user con el id 1
            $post->setUser($user); // este post ira con ese user
            $this->em->persist($post); // persistimos el post
            $this->em->flush(); // escribimos en la bbdd
            return $this->redirectToRoute('home'); // volvemos a la ruta home
        }

        return $this->render('post/index.html.twig', [
            'form' =>  $form->createView(), // crea la vista del formulario
            'posts' => $posts
        ]);
    }

    #[Route('/post/details/{id}', name: 'postDetails')]
    public function postDetails(Post $post)
    {
        return $this->render('post/post-details.html.twig', ['post' => $post]);
    }

    /* Recibimos id en url. Para esta no necesitamos constructor ni $em
    Sólo el código entre la linea 22 y 33 */
    #[Route('/post/{id}', name: 'app_post')]
    public function index(Post $post): Response
    {
        dump($post);
        return $this->render('post/index.html.twig', [
            'controller_name' => [
                'saludo' => 'Hola mundo.', 
                'nombre' => 'Diloes', 
            ],
            'post' => $post 
        ]);
    }

    /* Otra manera - Métodos mágicos */
    #[Route('/postId/{id}', name: 'postId')]
    public function postId($id): Response
    {
        // Mostrar post por id
        $post = $this->em->getRepository(Post::class)->find($id);
        $custom_post = $this->em->getRepository(Post::class)->findPost($id);
        return $this->render('post/postId.html.twig', [
            'post' => $post,
            'custom_post' => $custom_post
        ]);
    }

    /* Todos los posts */
    #[Route('/postsAll', name: 'postsAll')]
    public function posts(): Response
    {
        // Mostrar post por id
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->render('post/allPosts.html.twig', [
            'posts' => $posts
        ]);
    }

    /* Filtrar por cualquier cosa */
    #[Route('/postsBy', name: 'posts')]
    public function postBy(): Response
    {   
        // Buscar los que tengan el id = 1 y el title ese
        $postsBy = $this->em->getRepository(Post::class)->findBy([
            'id' => 1, 
            'title' => 'Mi primer post'
        ]);
        return $this->render('post/postBy.html.twig', [
            'postsBy' => $postsBy
        ]);

        // También tenemos findByOne que es como el anterior pero sólo devuelve uno
    }

    // Método para insertar un post
    #[Route('/insert/post', name: 'insert_post')]
    public function insert()
    {
        $post = new Post(
            'Mi post insertado', 
            'opinion', 
            'Descripción del post insertado.',
            'My FIle',
            'my-url'
        );
        $user = $this->em->getRepository(User::class)->find(2);
        $post->setUser($user);
        
        $this->em->persist($post);
        $this->em->flush(); // Esta línea se encarga de escribir en la base de datos
        return new JsonResponse(['success' => true]);            
    }

    // Método para actualizar un post
    #[Route('/update/post', name: 'update_post')]
    public function update()
    {
        $post = $this->em->getRepository(Post::class)->find(3);
        $post->setTitle('Título actualizado');
        $this->em->flush(); // Esta línea se encarga de escribir en la base de datos
        return new JsonResponse(['success' => true]);            
    }

    // Método para eliminar un post
    #[Route('/remove/post', name: 'remove_post')]
    public function remove()
    {
        $post = $this->em->getRepository(Post::class)->find(3);
        $this->em->remove($post);
        $this->em->flush(); // Esta línea se encarga de escribir en la base de datos
        return new JsonResponse(['success' => true]);            
    }
}

