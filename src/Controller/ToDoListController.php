<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/", name="to_do_list")
     */
    public function index(TaskRepository $repo)
    {
        $tasks = $repo->findBy([], ['id' => 'DESC']);
        return $this->render('to_do_list/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $om)
    {
        $title = trim($request->request->get('title'));
        if (empty($title))
            return $this->redirectToRoute('to_do_list');
        $new = new Task();
        $new->setTitle($title);
        $om->persist($new);
        $om->flush();
        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/switch_status/{id}", name="switch_status")
     */
    public function switch_status($id, EntityManagerInterface $om)
    {
        $task = $om->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->getStatus());
        $om->flush();
        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Task $id, EntityManagerInterface $om)
    {
        $om->remove($id);
        $om->flush();
        return $this->redirectToRoute('to_do_list');
    }
}
