@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Summary</th>
                                <th>Episodes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( Show::all() as $show)
                                <tr>
                                    <td class="col-md-3">{{$show->name}}</td>
                                    <td>{{$show->summary}}</td>
                                    <td class="col-md-1">
                                    <span class="badge">{{$show->episodes()->whereDownloaded(true)->count()}}</span> /
                                    <span class="badge">{{$show->episodes()->count()}}</span>
                                    </td>
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
