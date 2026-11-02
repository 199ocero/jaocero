<?php

namespace App\View\Components;

use App\Models\About;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SiteFooter extends Component
{
    public About $about;

    /**
     * The "let's build it" call to action, hidden on the contact page itself.
     */
    public bool $showCallToAction;

    public function __construct()
    {
        $this->about = About::current();
        $this->showCallToAction = ! request()->routeIs('contact');
    }

    public function render(): View
    {
        return view('components.site-footer');
    }
}
