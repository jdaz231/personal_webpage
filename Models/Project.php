<?php
namespace Models;

class Project extends BaseElement implements Printable{
    public function getDescription(){
        return $this->description;
    }
}