<?php

use Phalcon\Mvc\Model;

class Products extends Model
{
    public $id;
    public $name;
    public $description;
    public $tag;
    public $price;
    public $stock;
}