@extends('layouts.app')

@section('content')

<header class="flex items-center mb-3 py-4 mb-6">
    <div class="flex items-end justify-between w-full">
        <p class="text-gray-600 text-sm font-normal">
            <a href="/projects">My Projects</a> / {{ $project->title }}
        </p>
        <a href="/projects/create" class="button">New Project</a>
    </div>
</header>

<main>
    <div class="lg:flex -mx-3">
        <div class="lg:w-3/4 px-3">
            <div class="mb-8">
                <h2 class="text-lg text-gray-600 font-normal">Tasks</h2>
                
                @foreach ($project->tasks as $task)
                    <div class="card mb-3">{{ $task->body }}</div>
                @endforeach
                <div class="card mb-3">
                    <form action="{{ $project->path() . '/tasks' }}" method="POST">
                        @csrf
                        
                    </form>
                    <input type="text" placeholder="Begin adding tasks..." class="w-full" name="body">
                </div>
            </div>  
            
            <div>
                <h2 class="text-lg text-gray-600 font-normal">General Notes</h2>
                <textarea class="card w-full" style="min-height: 200px;">Lorem ipsum.</textarea>
            </div>
        </div>

        <div class="lg:w-1/4 px-3">
         @include('projects.card')
        </div>
    </div>
</main>

@endsection
