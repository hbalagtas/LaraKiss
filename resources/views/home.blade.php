@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Summary</th>
                                <th># of episodes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( Show::all() as $show)
                                <tr>
                                    <td>{{$show->name}}</td>
                                    <td><img src="{{$show->cover}}" alt="">{{$show->summary}}</td>
                                    <td><span class="badge">{{$show->episodes()->count()}}</span></td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
