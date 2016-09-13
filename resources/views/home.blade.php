@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                            <form action="{{route('show.store')}}" method="POST" class="form-inline" role="form">
                            {{ csrf_field() }}
                                <div class="form-group">
                                  
                                  <input type="text" class="form-control" id="url" name="url" placeholder="i.e. http://kissanime.to/Anime/Fullmetal-Alchemist-Brotherhood-Dub" style="width:350px">
                                </div>
                              <button type="submit" class="btn btn-primary">Add Show</button>
                            </form> 
                        </div>
                                             
                    </div>
                    
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Show ID</th>
                                <th>Name</th>
                                <th>URL</th>
                                <th>Episodes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( Show::orderBy('id', 'desc')->get() as $show)
                                <tr>
                                    <td lass="col-md-1">{{$show->id}}</td>
                                    <td class="col-md-6">{{$show->name}}</td>
                                    <td>{{$show->url}}</td>
                                    <td class="col-md-1">
                                    <span class="badge">{{$show->episodes()->whereDownloaded(true)->count()}}</span> /
                                    <span class="badge">{{$show->episodes()->count()}}</span>
                                    </td>
                                    <td class="col-md-2">                                        
                                        <form action="{{route('show.destroy', [$show->id])}}" method="POST">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                                            <a href="{{route('show.edit', [$show->id])}}" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                                        </form>
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
