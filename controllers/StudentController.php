<?php

namespace app\controllers;

use app\models\forms\SearchFormStudent;
use app\models\Student;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentController extends Controller
{
    public $classItemName;
    public $classSearchName;

    public function __construct($classItemName, $classSearchName)
    {
        // Initial restricted areas
        $this->registerMiddleware(new AuthMiddleware(['register', 'confirm', 'complete', 'search']));
        $this->classItemName = $classItemName;
        $this->classSearchName = $classSearchName;
        
        parent::__construct(Student::class, SearchFormStudent::class);
        $this->setContentView('student');
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
        $items = $itemForm->search($itemForm->getNameSearchKey(), $itemForm->getSearchValue());

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
