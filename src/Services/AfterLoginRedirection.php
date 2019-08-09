<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
/**
 * Class AfterLoginRedirection
 *
 * @package AppBundle\AppListener
 */
class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{
    private $router;

    /**
     * AfterLoginRedirection constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Request        $request
     *
     * @param TokenInterface $token
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // Get roles to know if user OR admin
        $roles = $token->getRoles();
        $user = $token->getUser();
        $rolesTab = array_map(function ($role) {
            return $role->getRole();
        }, $roles);

        // Get projects for user redirection
        $userProject = $user->getProjects();
        $totalProjects = count($userProject);

        if (in_array('ROLE_ADMIN', $rolesTab, true)) {
            // c'est un aministrateur : on le rediriger vers l'espace admin
            $redirection = new RedirectResponse($this->router->generate('adminSide'));
        } else if ($totalProjects > 1){
            // c'est un utilisaeur qui possède plus de 1 projet, redirection vers la page d'accueil
            $redirection = new RedirectResponse($this->router->generate('homePage'));
        } else {
          // Get slug
          $slug = $userProject[0]->getSlug();
          if ($userProject[0]->getIsActivated()) {
            // C'est un utilisateur qui possède seulement 1 projet, direction vers le projet en question
            $redirection = new RedirectResponse($this->router->generate(
                'showProjet',
                ['slug_project' => $slug]
            ));
          } else {
            $redirection = new RedirectResponse($this->router->generate('homePage'));
          }
        }
        return $redirection;
    }
}
