<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CommentSection extends Component
{
    public $object;
    public string $type;
    public $comments;
    public $canCreateProject;

    public function __construct($object, $type, $comments, $canCreateProject)
    {
        $this->object = $object;
        $this->type = $type;
        $this->comments = $comments;
        $this->canCreateProject = $canCreateProject;
    }

    public function render()
    {
        return view('components.comment-section');
    }
}
