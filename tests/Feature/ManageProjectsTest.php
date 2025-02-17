<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\ProjectFactory;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;
   
    /** @test */
    public function a_user_can_create_a_project()
    {   
        $this->signIn();

        $this->get('/projects/create')->assertStatus(200);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => 'General notes here'
        ];

        $response = $this->post('/projects', $attributes);
        
        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

     /** @test */
    public function a_user_can_update_a_project()
    {
        $project = ProjectFactory::create();
        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = ['notes' => 'Changed'])
            ->assertRedirect($project->path());
 
        $this->patch($project->path(), [
            'notes' => 'Changed'
        ])->assertRedirect($project->path());
 
        $this->assertDatabaseHas('projects', $attributes);
     }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $project = ProjectFactory::create();
        $this->actingAs($project->owner)
            ->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /** @test */
    public function an_authenticated_user_cannot_view_the_project_of_others()
    {
        $this->signIn();
        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_the_projects_of_others()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->patch($project->path())->assertStatus(403);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->signIn();
        $attributes = factory('App\Project')->raw(['title' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->signIn();
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
