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

    public function __construct($object, $type, $comments)
    {
        $this->object = $object;
        $this->type = $type;
        $this->comments = $comments;
    }

    public function render()
    {
        return view('components.comment-section');
    }
}
