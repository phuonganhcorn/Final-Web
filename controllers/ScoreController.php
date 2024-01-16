<?php

namespace app\controllers;

use app\models\forms\SearchFormScore;
use app\models\Score;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ScoreController extends Controller
{
    public $classItemName;
    public $classSearchName;

    public function __construct($classItemName, $classSearchName)
    {
        // Initial restricted areas
        $this->registerMiddleware(new AuthMiddleware(['register', 'confirm', 'complete', 'search']));
        $this->classItemName = $classItemName;
        $this->classSearchName = $classSearchName;
        
        parent::__construct(Score::class, SearchFormScore::class);
        $this->setContentView('score');
    }

    public function search(Request $request, Response $response)
    {
        $itemForm = new $this->classSearchName();
        
        if ($request->isPost()) {
            $this->handlePostRequest($request, $itemForm);
        }

        return $this->renderSearchView($itemForm, []);
    }

    private function handlePostRequest(Request $request, $itemForm)
    {
        $deleteId = $request->getParsedBody()['delete_id'] ?? '';

        if (!empty($deleteId)) {
            $this->deleteItemById($deleteId);
        }

        $itemForm->loadData($request->getParsedBody());

        // Extend the search functionality to include related models (Student and Subject)
        $items = $itemForm->search(
            $itemForm->getNameSearchKey(),
            $itemForm->getSearchValue(),
            ['student', 'subject']
        );

        $this->renderSearchView($itemForm, $items);
    }

    private function deleteItemById($id)
    {
        $this->classItemName::delete(['id' => $id]);
    }

    private function renderSearchView($itemForm, $items)
    {
        return $this->render('search', ['model' => $itemForm, 'items' => $items]);
    }
}
