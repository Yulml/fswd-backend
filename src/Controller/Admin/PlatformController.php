<?php

namespace App\Controller\Admin;

use App\Entity\Platform;
use App\Form\PlatformType;
use App\Repository\PlatformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/platform')]
class PlatformController extends AbstractController
{
    #[Route('/', name: 'app_admin_platform_index', methods: ['GET'])]
    public function index(PlatformRepository $platformRepository): Response
    {
        return $this->render('admin/platform/index.html.twig', [
            'platforms' => $platformRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_platform_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PlatformRepository $platformRepository): Response
    {
        $platform = new Platform();
        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $platformRepository->add($platform, true);

            return $this->redirectToRoute('app_admin_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/platform/new.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_platform_show', methods: ['GET'])]
    public function show(Platform $platform): Response
    {
        return $this->render('admin/platform/show.html.twig', [
            'platform' => $platform,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_platform_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Platform $platform, PlatformRepository $platformRepository): Response
    {
        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $platformRepository->add($platform, true);

            return $this->redirectToRoute('app_admin_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/platform/edit.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_platform_delete', methods: ['POST'])]
    public function delete(Request $request, Platform $platform, PlatformRepository $platformRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$platform->getId(), $request->request->get('_token'))) {
            $platformRepository->remove($platform, true);
        }

        return $this->redirectToRoute('app_admin_platform_index', [], Response::HTTP_SEE_OTHER);
    }
}
