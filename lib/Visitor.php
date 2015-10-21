<?php
namespace Wntrmn\Auth;

abstract class Visitor
{
    abstract public function getAction();

    abstract public function getName();
}
