<?php

namespace App\Controller;

use App\Entity\CmsBlock;
use App\Form\CmsBlockType;
use App\Repository\CmsBlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cms/block")
 */
class CmsBlockController extends AbstractController
{
    /**
     * @Route("/", name="cms_block_index", methods={"GET"})
     */
    public function index(CmsBlockRepository $cmsBlockRepository): Response
    {
        return $this->render('cms_block/index.html.twig', [
            'cms_blocks' => $cmsBlockRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="cms_block_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cmsBlock = new CmsBlock();
        $form = $this->createForm(CmsBlockType::class, $cmsBlock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsBlock);
            $entityManager->flush();

            return $this->redirectToRoute('cms_block_index');
        }

        return $this->render('cms_block/new.html.twig', [
            'cms_block' => $cmsBlock,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cms_block_show", methods={"GET"})
     */
    public function show(CmsBlock $cmsBlock): Response
    {
        $response = new Response();
        $response->setContent(json_encode([
            'id' => $cmsBlock->getId(),
            'title' => $cmsBlock->getTitle(),
            'content' => $cmsBlock->getContent(),
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}/edit", name="cms_block_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CmsBlock $cmsBlock): Response
    {
        $form = $this->createForm(CmsBlockType::class, $cmsBlock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_block_index', [
                'id' => $cmsBlock->getId(),
            ]);
        }

        return $this->render('cms_block/edit.html.twig', [
            'cms_block' => $cmsBlock,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cms_block_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CmsBlock $cmsBlock): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cmsBlock->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsBlock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_block_index');
    }
}
