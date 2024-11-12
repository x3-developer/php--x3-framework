<?php

namespace App\Controller;

use App\Route;
use App\Service\TestService;
use App\TemplateRenderer;

/**
 * Class HomeController
 */
readonly class HomeController
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
        private TestService      $testService,
    )
    {
    }

    /**
     * @return void
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(): void
    {
        try {
            var_dump($this->testService->test());
            echo $this->templateRenderer->render('index', ['title' => 'Home page']);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * @return void
     */
    #[Route(path: '/about', methods: ['GET'])]
    public function about(): void
    {
        try {
            echo $this->templateRenderer->render('index', ['title' => 'About page']);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
