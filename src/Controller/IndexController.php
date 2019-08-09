<?php
namespace App\Controller;

use App\Entity\Project;
use App\Entity\ActivityHasMedia;
use App\Entity\Phase;
use App\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

/** @Route("/") */
class IndexController extends AbstractController
{
    //
    // Show projects
    //
    public function homePage(AuthorizationCheckerInterface $authChecker) {
      // Get the object of the currrent user
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $user = $this->getUser();
      // Get the project's id linked to the user
      $userId = $user->getId();
      $newUser = $user->getNewUser();

      $em = $this->getDoctrine()->getManager();

      if ( $authChecker->isGranted('ROLE_ADMIN') ) {
        $repository = $em->getRepository(Project::class);
        $projects = $repository->findAll();
      } else {
        // 2. Setup repository of some entity
        $projects = $user->getProjects();
        $repoActivity = $em->getRepository(Project::class);
        // 3. Query how many rows are there in the Articles table
        $projects = $repoActivity->createQueryBuilder('p')
            // Filter by some parameter if you want
            ->innerJoin('p.users', 'u')
            ->where('u = '.$userId)
            ->andWhere('p.isActivated = 1')
            ->getQuery()->getResult();
      }
      $countProjects = count($projects);

      return $this->render('index.html.twig', [
          'projects' => $projects,
          'newUser' => $newUser,
          'countProjects' => $countProjects
      ]);
    }

    //
    // Show one project
    //
    public function showProjet(Request $request, $slug_project)
    {
        // Get the object of the currrent user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        // Get the project's id linked to the user
        $userId = $user->getId();

        // Get the object of the project
        $project = $this->getDoctrine()
          ->getRepository(Project::class)
          ->findOneBy(array(
              'slug' => $slug_project
          ));

        // $userOwner = $project->getUser();
        // $userOwnerId = $userOwner->getId();

        // Check user has access to this project
        $this->denyAccessUnlessGranted('VIEW', $project);

        // Get the phases & slug
        // $phases = $project->getPhases();
        $hotTopic = $project->getHotTopic();
        $slug_project = $project->getSlug();

        $projectId = $project->getId();

        $em = $this->getDoctrine()->getManager();
        // 2. Setup repository of some entity
        $repoActivity = $em->getRepository(Phase::class);
        // 3. Query how many rows are there in the Articles table
        $phases = $repoActivity->createQueryBuilder('a')
            // Filter by some parameter if you want
            ->where('a.project = '.$projectId)
            ->orderBy('a.position', 'ASC')
            ->getQuery()->getResult();

        $foo = [];
        // Count the quantity of activities for each phase
        foreach($phases as $phase) {
            $act_id = $phase->getId();
            // 1. Obtain doctrine manager
            $em = $this->getDoctrine()->getManager();
            // 2. Setup repository of some entity
            $repoActivity = $em->getRepository(Activity::class);
            // 3. Query how many rows are there in the Articles table
            $totalActivity = $repoActivity->createQueryBuilder('a')
                // Filter by some parameter if you want
                ->where('a.phase = '.$act_id)
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();

            $acti = $phase->getActivities();

            $totalFile = 0;
            foreach($acti as $act) {
              $activity_id = $act->getId();
              $em = $this->getDoctrine()->getManager();
              // 2. Setup repository of some entity
              $repoActivity = $em->getRepository(ActivityHasMedia::class);
              $total = $repoActivity->createQueryBuilder('am')
                  // Filter by some parameter if you want
                  ->where('am.activity = '.$activity_id)
                  ->select('count(am.id)')
                  ->getQuery()
                  ->getSingleScalarResult();
              $totalFile = $totalFile + $total;
            }

            $x = ['nbActivity' => $totalActivity, 'nbFile' => $totalFile];
            array_push($foo, $x);
          }

        if(empty($hotTopic)) {
          return $this->render('project.html.twig', [
              'project' => $project,
              'phases' => $phases,
              'slug_project' => $slug_project,
              'hotTopic' => $hotTopic,
              'countInfo' => $foo
            ]);
        } else if (empty($phases)) {
          return $this->render('project.html.twig', [
              'project' => $project,
              'slug_project' => $slug_project,
              'countInfo' => $foo
            ]);
        } else {
            return $this->render('project.html.twig', [
                'project' => $project,
                'phases' => $phases,
                'slug_project' => $slug_project,
                'countInfo' => $foo
              ]);
            }
        }

    //
    // Show one project's phases
    //
    public function showActivity(Request $request, $slug_project, $slug_child)
    {
        // Get the object of the project
        $project = $this->getDoctrine()
          ->getRepository(Project::class)
          ->findOneBy(array(
              'slug' => $slug_project
        ));

        // Check user has access to this project
        $this->denyAccessUnlessGranted('VIEW', $project);

        $user = $this->getUser();
        $mediasD = $user->getActivityHasMedias();

        $phase = $this->getDoctrine()
          ->getRepository(Phase::class)
          ->findOneBy(array(
              'slug' => $slug_child
        ));

        if (strcmp($slug_project, $phase->getProject()->getSlug()) !== 0) {
          throw $this->createNotFoundException('This project does not exist.');
        }

        // Sort activities
        $activities = $this->getDoctrine()->getRepository(Activity::class)->findBy(
            array('phase' => $phase->getId()),
            array('position' => 'ASC')
        );

        // foreach($activities as $activity) {
        //   $actiFiles = $activity->getActivityHasMedias()->getValues();
        //   // $files = $test[0]->getThumbnailImage();
        //   foreach($actiFiles as $actiFile) {
        //     if ($actiFile->getThumbnailImage()) {
        //     }
        //   }
        // }

        if(empty($activities)) {
          return $this->render('activity.html.twig', [
              'phase' => $phase,
              'slug_project' => $slug_project
            ]);
        } else {
          return $this->render('activity.html.twig', [
              'phase' => $phase,
              'slug_project' => $slug_project,
              'activities' => $activities,
              'mediasD' => $mediasD
            ]);
        }
    }

    //
    // Ajax call
    //
    public function newUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        // Get the project's id linked to the user
        $user->setNewUser(true);
        $em->persist($user);
        $em->flush();

        return new Response('Its coming from here .', 200, array('Content-Type' => 'text/html'));
    }

    //
    // Ajax call
    //
    public function hasBeenDownloaded(Request $request)
    {
        if($request->request->get('relationId')){
          $id = $request->request->get('relationId');
          $em = $this->getDoctrine()->getManager();
          $user = $this->getUser();

          // Know if user already download
          $ms = $user->getActivityHasMedias();
          foreach($ms as $m) {
            if($m->getId() == $id) {
              $arrData = ['output' => 'File downloaded'];
              return new JsonResponse($arrData);
            }
          }

          $media = $this->getDoctrine()
            ->getRepository(ActivityHasMedia::class)
            ->findOneBy(array(
                'id' => $id
          ));

          $user->addActivityHasMedia($media);
          $media->addUser($user);

          $em->persist($media);
          $em->persist($user);
          $em->flush();

          $arrData = ['output' => 'File downloaded'];
          return new JsonResponse($arrData);
        }
    }
}
