<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;
   
    /** @test */
    public function a_user_can_create_a_project()
    {   
        $this->withoutExceptionHandling();
        $this->actingAs(factory('App\User')->create());

        $this->get('/projects/create')->assertStatus(200);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];

        $this->post('/projects', $attributes)->assertRedirect('/projects');
        $this->assertDatabaseHas('projects', $attributes);
        $this->get('/projects')->assertSee($attributes['title']);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $this->be(factory('App\User')->create());
        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
             ->assertSee($project->description);
    }

    public function an_authenticated_user_cannot_view_the_project_of_others()
    {
        $this->be(factory('App\User')->create());
        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->actingAs(factory('App\User')->create());
        $attributes = factory('App\Project')->raw(['title' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->actingAs(factory('App\User')->create());
        $attributes = factory('App\Project')->raw(['description' => '']);
        $this->post('/projects', [])->assertSessionHasErrors('description');
    }

     /** @test */
     public function guest_cannot_create_projects()
     {
         $attributes = factory('App\Project')->raw();
         $this->post('/projects', $attributes)->assertRedirect('login');
     }

     /** @test */
     public function guests_cannot_view_projects()
     {
         $this->get('/projects')->assertRedirect('login');
     }

     /** @test */
     public function guests_cannot_view_a_single_project()
     {
        $project = factory('App\Project')->create();
        $this->get($project->path())->assertRedirect('login');
     }

     /** @test */
     public function it_belongs_to_an_owner()
     {
        $project = factory('App\Project')->create();

        $this->assertInstanceOf('App\User', $project->owner);
     }

}
