<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    
    /** @test */
    public function has_projects()
    {
        $user = \factory('App\User')->create();

        $this->assertInstanceOf(Collection::class, $user->projects);
    }
}
