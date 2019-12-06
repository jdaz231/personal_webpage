<?php
namespace Controllers;
require_once 'BaseController.php';
use Models\{Job,Project};

class IndexController extends BaseController{
    public function indexAction(){
        $jobs = Job::all();
        $project1 = new Project('Project 1','Description 1');
        $projects = [
            $project1
        ];
        $name = 'Hector Benitez';
        return $this->renderHTML('index.twig',[
            'name' => $name,
            'jobs' => $jobs
        ]);
    }
}
