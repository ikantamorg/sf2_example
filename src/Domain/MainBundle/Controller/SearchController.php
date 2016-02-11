<?php
/**
 * User: dev
 * Date: 09.10.13
 * Time: 10:41
 */

namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends CoreController
{

    /**
     * Experts per page
     *
     * @var int
     */
    private $experts_per_page = 5;

    /*
     * Tempaltes of blocks
     */
    private $templates = [
        'sidebar' => 'MainBundle:Search:blocks/sidebar.html.twig',
        'content' => 'MainBundle:Search:blocks/content.html.twig',
        'pagination' => 'MainBundle:Search:blocks/pagination.html.twig',
    ];


    public function expertsAction(Request $Request)
    {

        $page = $Request->query->get('page', 1);

        $searcher = $this->get('domain.expert.search');

        $searcher->setRequest($Request);

        $paginator  = $this->get('knp_paginator');

        $experts = $paginator->paginate(
            $searcher->getExpertsQuery(),
            $page/*page number*/,
            $this->experts_per_page/*limit per page*/,
            $Request->query->all()
        );


        $data_to_render = [
            'experts' => $experts,
            'count' => $experts->getTotalItemCount()
        ];

        // if ajax - render - ajax
        if ($Request->isXmlHttpRequest()) {
            $only_content = $page > 1 ? true : false;

            if (!$only_content) {
                $data_to_render['sidebar_data'] = $searcher->getFiltersData();
            }

            return $this->expertSearchAjaxRender($data_to_render, $only_content);
        }

        $data_to_render['sidebar_data'] = $searcher->getFiltersData();

        $data_to_render['industries'] = $this->getRepository('CoreBundle:Industry')->findAll();

        $data_to_render['actived_filters'] = $searcher->getActiveFilters();

        //templates list
        $data_to_render['include'] = $this->templates;

        return $this->expertSearchRender($data_to_render);
    }

    /**
     * Render template for usual view
     *
     * @param $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function expertSearchRender($data)
    {
        return $this->render('MainBundle:Search:experts.html.twig', $data);
    }

    /**
     * Render some tempaltes for ajax view (json output)
     *
     * @param $data
     * @param bool $only_content
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function expertSearchAjaxRender($data, $only_content = false)
    {
        $responce = [
            'content' => $this->renderView($this->templates['content'], $data),
            'pagination' => $this->renderView($this->templates['pagination'], $data),
            'count' => $data['count']
        ];

        if (!$only_content) {
            $responce['sidebar'] = $this->renderView($this->templates['sidebar'], $data);
        }

        return $this->jsonResponse($responce);

    }




}
